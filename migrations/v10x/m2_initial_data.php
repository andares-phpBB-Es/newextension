<?php
/**
 *
 * Quick Title Edition extension for the phpBB Forum Software package
 *
 * @copyright (c) 2023, Kailey Snay, https://www.snayhomelab.com/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace kaileymsnay\qte\migrations\v10x;

class m2_initial_data extends \phpbb\db\migration\container_aware_migration
{
	public function effectively_installed()
	{
		return $this->config->offsetExists('qte_version');
	}

	public static function depends_on()
	{
		return ['\kaileymsnay\qte\migrations\v10x\m1_initial_schema'];
	}

	/**
	 * Add, update or delete data
	 */
	public function update_data()
	{
		return [
			// Add permissions
			['permission.add', ['a_attr_manage']],
			['permission.add', ['m_qte_attr_edit', false]],
			['permission.add', ['m_qte_attr_delete', false]],

			// Set permissions
			['permission.permission_set', ['ROLE_ADMIN_FULL', 'a_attr_manage']],
			['permission.permission_set', ['ROLE_MOD_FULL', 'm_qte_attr_edit']],
			['permission.permission_set', ['ROLE_MOD_FULL', 'm_qte_attr_delete']],

			// Add module
			['module.add', [
				'acp',
				'ACP_MESSAGES',
				[
					'module_basename'	=> '\kaileymsnay\qte\acp\main_module',
					'modes'				=> ['manage'],
				],
			]],

			// Add config_text table setting
			['config_text.add', ['qte_version', '1.0.0-dev']],
		];
	}

	/**
	 * Add, update or delete data
	 */
	public function revert_data()
	{
		return [
			['custom', [[$this, 'remove_permissions']]],
		];
	}

	/**
	 * A custom function for making more complex database changes
	 * during extension un-installation. Must be declared as public.
	 */
	public function remove_permissions()
	{
		$migrator_tool_permission = $this->container->get('migrator.tool.permission');

		$sql = 'SELECT auth_option FROM ' . ACL_OPTIONS_TABLE . '
			WHERE auth_option ' . $this->db->sql_like_expression('f_qte_attr_' . $this->db->get_any_char());
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$migrator_tool_permission->remove($row['auth_option'], false);
		}
		$this->db->sql_freeresult($result);
	}
}
