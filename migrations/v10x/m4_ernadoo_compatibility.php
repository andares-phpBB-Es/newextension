<?php
/**
 * Upgrade the shared QTE data left by ernadoo/qte 2.0.0-RC1.
 * @license GNU General Public License, version 2 (GPL-2.0)
 */

namespace kaileymsnay\qte\migrations\v10x;

class m4_ernadoo_compatibility extends \phpbb\db\migration\container_aware_migration
{
	public static function depends_on()
	{
		return ['\kaileymsnay\qte\migrations\v10x\m3_remove_version'];
	}

	public function update_data()
	{
		return [['custom', [[$this, 'migrate_ernadoo']]]];
	}

	public function migrate_ernadoo()
	{
		$result = $this->db->sql_query("SELECT ext_active FROM {$this->table_prefix}ext WHERE ext_name = 'ernadoo/qte'");
		$legacy = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);
		if (!$legacy)
		{
			return;
		}
		if ($legacy['ext_active'])
		{
			throw new \phpbb\db\migration\exception('Disable ernadoo/qte before enabling kaileymsnay/qte. Do not delete its data.');
		}
		if (isset($this->config['qte_version']) && strcasecmp($this->config['qte_version'], '2.0.0-RC1') !== 0)
		{
			throw new \phpbb\db\migration\exception('This migration requires ernadoo/qte 2.0.0-RC1. Complete the legacy upgrade first.');
		}

		// Validate before writing: older/partially upgraded installations need their own converter.
		$schema = new m1_initial_schema($this->config, $this->db, $this->db_tools, $this->phpbb_root_path, $this->php_ext, $this->table_prefix);
		$schema = $schema->update_schema();
		$columns = $schema['add_columns'];
		$columns[$this->table_prefix . 'topics_attr'] = $schema['add_tables'][$this->table_prefix . 'topics_attr']['COLUMNS'];
		foreach ($columns as $table => $definitions)
		{
			foreach ($definitions as $column => $definition)
			{
				if (!$this->db_tools->sql_column_exists($table, $column))
				{
					throw new \phpbb\db\migration\exception('Incomplete legacy QTE schema: ' . $table . '.' . $column);
				}
			}
		}
		if ($this->db_tools->sql_column_exists($this->table_prefix . 'topics_attr', 'attr_auths') ||
			$this->db_tools->sql_column_exists($this->table_prefix . 'forums', 'hide_attr'))
		{
			throw new \phpbb\db\migration\exception('Complete the ernadoo/qte 2.0.0-RC1 permission migrations before switching extensions.');
		}

		$old_permission = $this->permission_id('m_qte_attr_del');
		$new_permission = $this->permission_id('m_qte_attr_delete');
		if (!$this->permission_id('a_attr_manage') || !$this->permission_id('m_qte_attr_edit') || (!$old_permission && !$new_permission))
		{
			throw new \phpbb\db\migration\exception('Legacy QTE permissions are incomplete. Restore or complete the old installation before migrating.');
		}

		$result = $this->db->sql_query("SELECT module_id, module_basename FROM {$this->table_prefix}modules
			WHERE module_class = 'acp' AND module_mode = 'manage'
			AND " . $this->db->sql_in_set('module_basename', [
				'\\ernadoo\\qte\\acp\\main_module', 'ernadoo\\qte\\acp\\main_module',
				'\\kaileymsnay\\qte\\acp\\main_module', 'kaileymsnay\\qte\\acp\\main_module',
			]));
		$modules = $this->db->sql_fetchrowset($result);
		$this->db->sql_freeresult($result);
		if (!$modules)
		{
			throw new \phpbb\db\migration\exception('The QTE administration module is missing. Restore it before migrating.');
		}

		// RC1 limited descriptions to 60 characters. No attribute IDs or topic/forum rows change.
		$this->db_tools->sql_column_change($this->table_prefix . 'topics_attr', 'attr_desc', ['VCHAR:255', '']);
		$this->db->sql_transaction('begin');
		if ($old_permission && !$new_permission)
		{
			// Renaming preserves every group/user/role assignment, including NEVER values.
			$this->db->sql_query("UPDATE {$this->table_prefix}acl_options SET auth_option = 'm_qte_attr_delete'
				WHERE auth_option_id = " . $old_permission);
		}
		else if ($old_permission)
		{
			$this->merge_permissions($old_permission, $new_permission);
		}

		$has_new_module = false;
		foreach ($modules as $module)
		{
			$has_new_module = $has_new_module || strpos(ltrim($module['module_basename'], '\\'), 'kaileymsnay\\') === 0;
		}
		foreach ($modules as $module)
		{
			if (strpos(ltrim($module['module_basename'], '\\'), 'ernadoo\\') !== 0)
			{
				continue;
			}
			// Keep the module's ID and position. If a new module exists, hide the obsolete entry.
			$data = $has_new_module ? ['module_enabled' => 0] : [
				'module_basename' => '\\kaileymsnay\\qte\\acp\\main_module',
				'module_auth' => 'ext_kaileymsnay/qte && acl_a_attr_manage',
			];
			$this->db->sql_query("UPDATE {$this->table_prefix}modules SET " . $this->db->sql_build_array('UPDATE', $data) .
				' WHERE module_id = ' . (int) $module['module_id']);
		}
		$this->db->sql_transaction('commit');

		$cache = $this->container->get('cache');
		foreach (['_acl_options', '_modules_acp', '_attr'] as $key)
		{
			$cache->destroy($key);
		}
		$this->container->get('auth')->acl_clear_prefetch();
	}

	private function permission_id($name)
	{
		$result = $this->db->sql_query("SELECT auth_option_id FROM {$this->table_prefix}acl_options
			WHERE auth_option = '" . $this->db->sql_escape($name) . "'");
		$id = (int) $this->db->sql_fetchfield('auth_option_id');
		$this->db->sql_freeresult($result);
		return $id;
	}

	private function merge_permissions($old_id, $new_id)
	{
		// A board may already have both options. Merge without duplicate ACL rows.
		$tables = [
			'acl_groups' => ['group_id', 'forum_id', 'auth_role_id'],
			'acl_users' => ['user_id', 'forum_id', 'auth_role_id'],
			'acl_roles_data' => ['role_id'],
		];
		foreach ($tables as $name => $keys)
		{
			$table = $this->table_prefix . $name;
			$result = $this->db->sql_query('SELECT * FROM ' . $table . ' WHERE auth_option_id = ' . $old_id);
			$rows = $this->db->sql_fetchrowset($result);
			$this->db->sql_freeresult($result);
			foreach ($rows as $row)
			{
				$where = 'auth_option_id = ' . $new_id;
				foreach ($keys as $key)
				{
					$where .= ' AND ' . $key . ' = ' . (int) $row[$key];
				}
				$result = $this->db->sql_query('SELECT auth_setting FROM ' . $table . ' WHERE ' . $where);
				$existing = $this->db->sql_fetchrow($result);
				$this->db->sql_freeresult($result);
				if (!$existing)
				{
					$row['auth_option_id'] = $new_id;
					$this->db->sql_query('INSERT INTO ' . $table . ' ' . $this->db->sql_build_array('INSERT', $row));
				}
				else if ((int) $row['auth_setting'] < (int) $existing['auth_setting'])
				{
					// Prefer the restrictive value on collision: NEVER (-1), NO (0), YES (1).
					$this->db->sql_query('UPDATE ' . $table . ' SET auth_setting = ' . (int) $row['auth_setting'] . ' WHERE ' . $where);
				}
			}
		}
	}
}
