<?php
/**
 *
 * Quick Title Edition extension for the phpBB Forum Software package
 *
 * @copyright (c) 2023, Kailey Snay, https://www.snayhomelab.com/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace kaileymsnay\qte\controller;

/**
 * Quick Title Edition ACP controller
 */
class acp_controller
{
	/** @var \phpbb\cache\driver\driver_interface */
	protected $cache;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\log\log */
	protected $log;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var string */
	protected $root_path;

	/** @var string */
	protected $php_ext;

	/** @var string */
	protected $table_prefix;

	/** @var \kaileymsnay\qte\qte */
	protected $qte;

	/** @var \phpbb\db\migration\tool\permission */
	protected $migrator_tool_permission;

	/** @var string */
	protected $u_action;

	/**
	 * Constructor
	 *
	 * @param \phpbb\cache\driver\driver_interface  $cache
	 * @param \phpbb\db\driver\driver_interface     $db
	 * @param \phpbb\language\language              $language
	 * @param \phpbb\log\log                        $log
	 * @param \phpbb\request\request                $request
	 * @param \phpbb\template\template              $template
	 * @param \phpbb\user                           $user
	 * @param string                                $root_path
	 * @param string                                $php_ext
	 * @param string                                $table_prefix
	 * @param \kaileymsnay\qte\qte                  $qte
	 * @param \phpbb\db\migration\tool\permission   $migrator_tool_permission
	 */
	public function __construct(\phpbb\cache\driver\driver_interface $cache, \phpbb\db\driver\driver_interface $db, \phpbb\language\language $language, \phpbb\log\log $log, \phpbb\request\request $request, \phpbb\template\template $template, \phpbb\user $user, $root_path, $php_ext, $table_prefix, \kaileymsnay\qte\qte $qte, \phpbb\db\migration\tool\permission $migrator_tool_permission)
	{
		$this->cache = $cache;
		$this->db = $db;
		$this->language = $language;
		$this->log = $log;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;
		$this->table_prefix = $table_prefix;
		$this->qte = $qte;
		$this->migrator_tool_permission = $migrator_tool_permission;
	}

	/**
	 * Display
	 */
	public function display()
	{
		// Add our language file
		$this->language->add_lang(['attributes', 'attributes_acp', 'logs_attributes'], 'kaileymsnay/qte');

		// Create a form key for preventing CSRF attacks
		add_form_key('acp_attributes');

		// Create an array to collect errors that will be output to the user
		$errors = [];

		$action = $this->request->variable('action', '');
		$attr_id = $this->request->variable('id', 0);
		$attr_auth_id = $this->request->variable('attr_auth_id', 0);
		$clear_dest_perms = false;

		switch ($action)
		{
			case 'edit':
			case 'add':
				$attr_type = $this->request->variable('attr_type', 0);
				$attr_name = $this->request->variable('attr_name', '', true);
				$attr_img = $this->request->variable('attr_img', '');
				$attr_desc = $this->request->variable('attr_desc', '', true);
				$attr_date = $this->request->variable('attr_date', '');
				$attr_colour = $this->request->variable('attr_colour', '');
				$attr_user_colour = $this->request->variable('attr_user_colour', 0);

				// Is the form being submitted to us?
				if ($this->request->is_set_post('submit'))
				{
					// Test if the submitted form is valid
					if (!check_form_key('acp_attributes'))
					{
						$errors[] = $this->language->lang('FORM_INVALID');
					}

					if (empty($attr_name))
					{
						$errors[] = $this->language->lang('QTE_NAME_ERROR');
					}

					// Full xhtml compatibility, no capital letters
					if (!empty($attr_colour))
					{
						$attr_colour = strtolower($attr_colour);

						if (!preg_match('#^([a-f0-9]){6}#i', $attr_colour))
						{
							$errors[] = $this->language->lang('QTE_COLOUR_ERROR');
						}
					}

					// Don't use user colour when an image is used
					if ($attr_type && $attr_user_colour)
					{
						$attr_user_colour = false;
					}

					$attr_name_tmp = $this->language->lang($attr_name);

					if ($attr_user_colour)
					{
						if (strpos($attr_name_tmp, '%mod%') === false)
						{
							$errors[] = $this->language->lang('QTE_USER_COLOUR_ERROR');
						}
					}

					if (!empty($attr_date))
					{
						if (strpos($attr_name_tmp, '%date%') === false)
						{
							$errors[] = $this->language->lang('QTE_DATE_ARGUMENT_ERROR');
						}
					}
					else
					{
						if (strpos($attr_name_tmp, '%date%') !== false)
						{
							$errors[] = $this->language->lang('QTE_DATE_FORMAT_ERROR');
						}
					}

					unset($attr_name_tmp);

					// If no errors, process the form data
					if (empty($errors))
					{
						$sql_ary = [
							'attr_type'			=> $attr_type,
							'attr_name'			=> $attr_name,
							'attr_img'			=> $attr_img,
							'attr_desc'			=> $attr_desc,
							'attr_date'			=> $attr_date,
							'attr_colour'		=> $attr_colour,
							'attr_user_colour'	=> $attr_user_colour,
						];

						if ($attr_id)
						{
							$sql = 'UPDATE ' . $this->table_prefix . 'topics_attr
								SET ' . $this->db->sql_build_array('UPDATE', $sql_ary) . '
								WHERE attr_id = ' . (int) $attr_id;
							$this->db->sql_query($sql);

							$clear_dest_perms = true;
							$message = 'UPDATED';
						}
						else
						{
							$sql = 'SELECT MAX(right_id) AS right_id
								FROM ' . $this->table_prefix . 'topics_attr';
							$result = $this->db->sql_query($sql);
							$right_id = (int) $this->db->sql_fetchfield('right_id');
							$this->db->sql_freeresult($result);

							$sql_ary['left_id'] = ($right_id + 1);
							$sql_ary['right_id'] = ($right_id + 2);

							$sql = 'INSERT INTO ' . $this->table_prefix . 'topics_attr ' . $this->db->sql_build_array('INSERT', $sql_ary);
							$this->db->sql_query($sql);
							$attr_id = $this->db->sql_nextid();

							$this->migrator_tool_permission->add('f_qte_attr_' . $attr_id, false);

							$message = 'ADDED';
						}

						if ($attr_auth_id)
						{
							$this->_copy_permission('f_qte_attr_' . $attr_id, 'f_qte_attr_' . $attr_auth_id, $clear_dest_perms);
						}

						$this->cache->destroy('_attr');

						$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_ATTRIBUTE_' . $message, time(), [$attr_name]);

						trigger_error($this->language->lang('QTE_' . $message) . adm_back_link($this->u_action));
					}
				}
				else if ($attr_id)
				{
					$attr = $this->_get_attr_info($attr_id);
				}

				if ($action == 'edit')
				{
					$this->template->assign_vars([
						'QTE_ADD_EDIT'			=> $this->language->lang('QTE_EDIT'),
						'QTE_ADD_EDIT_EXPLAIN'	=> $this->language->lang('QTE_EDIT_EXPLAIN'),
					]);
				}
				else
				{
					$this->template->assign_vars([
						'QTE_ADD_EDIT'			=> $this->language->lang('QTE_ADD'),
						'QTE_ADD_EDIT_EXPLAIN'	=> $this->language->lang('QTE_ADD_EXPLAIN'),
					]);
				}

				$this->qte_attr_select($attr_id);

				$s_errors = !empty($errors);

				$attr_type_state = ((isset($attr['attr_type']) && $attr['attr_type']) || (isset($attr_type) && $attr_type));
				$attr_user_colour_state = ((isset($attr['attr_user_colour']) && $attr['attr_user_colour']) || (isset($attr_user_colour) && $attr_user_colour));

				// Set output variables for display in the template
				$this->template->assign_vars([
					'S_EDIT'	=> true,

					'S_ERROR'	=> $s_errors,
					'ERROR_MSG'	=> $s_errors ? implode('<br />', $errors) : '',

					'U_AJAX'	=> str_replace('&amp;', '&', $this->u_action),

					'ATTR_ID'		=> $attr['attr_id'] ?? $attr_id,
					'ATTR_NAME'		=> $attr['attr_name'] ?? $attr_name,
					'ATTR_IMG'		=> $attr['attr_img'] ?? $attr_img,
					'ATTR_DESC'		=> $attr['attr_desc'] ?? $attr_desc,
					'ATTR_DATE'		=> $attr['attr_date'] ?? $attr_date,
					'ATTR_COLOUR'	=> $attr['attr_colour'] ?? $attr_colour,

					'S_TEXT'		=> $attr_type_state ? true : false,
					'S_USER_COLOUR'	=> $attr_user_colour_state ? true : false,
				]);
			break;

			case 'delete':
				if (!$attr_id)
				{
					trigger_error($this->language->lang('QTE_MUST_SELECT') . adm_back_link($this->u_action));
				}

				if (confirm_box(true))
				{
					$sql = 'SELECT topic_id, topic_attr_id
						FROM ' . $this->table_prefix . 'topics
						WHERE topic_attr_id = ' . (int) $attr_id;
					$result = $this->db->sql_query($sql);
					$topic_id_ary = [];
					while ($row = $this->db->sql_fetchrow($result))
					{
						$topic_id_ary[] = (int) $row['topic_id'];
					}
					$this->db->sql_freeresult($result);

					if (count($topic_id_ary))
					{
						$fields = [
							'topic_attr_id'		=> 0,
							'topic_attr_user'	=> 0,
							'topic_attr_time'	=> 0,
						];

						$sql = 'UPDATE ' . $this->table_prefix . 'topics
							SET ' . $this->db->sql_build_array('UPDATE', $fields) . '
							WHERE ' . $this->db->sql_in_set('topic_id', array_map('intval', $topic_id_ary));
						$this->db->sql_query($sql);
					}

					$sql = 'SELECT attr_name
						FROM ' . $this->table_prefix . 'topics_attr
						WHERE attr_id = ' . (int) $attr_id;
					$result = $this->db->sql_query($sql);
					$attr_name = (string) $this->db->sql_fetchfield('attr_name');
					$this->db->sql_freeresult($result);

					$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_ATTRIBUTE_DELETED', time(), [$attr_name]);

					$this->migrator_tool_permission->remove('f_qte_attr_' . $attr_id, false);

					$sql = 'DELETE FROM ' . $this->table_prefix . 'topics_attr
						WHERE attr_id = ' . (int) $attr_id;
					$this->db->sql_query($sql);

					$this->cache->destroy('_attr');

					if ($this->request->is_ajax())
					{
						$json_response = new \phpbb\json_response;
						$json_response->send([
							'success'		=> 'true',
							'MESSAGE_TITLE'	=> $this->language->lang('INFORMATION'),
							'MESSAGE_TEXT'	=> $this->language->lang('QTE_REMOVED'),
							'REFRESH_DATA'	=> [
								'time'	=> 3,
							]
						]);
					}
					else
					{
						trigger_error($this->language->lang('QTE_REMOVED') . adm_back_link($this->u_action));
					}
				}
				else
				{
					confirm_box(false, $this->language->lang('CONFIRM_OPERATION'), build_hidden_fields([
						'attr_id'	=> $attr_id,
						'action'	=> 'delete',
					]));
				}
			break;

			case 'move_up':
			case 'move_down':
				if (!$attr_id)
				{
					trigger_error($this->language->lang('QTE_MUST_SELECT') . adm_back_link($this->u_action));
				}

				$sql = 'SELECT *
					FROM ' . $this->table_prefix . 'topics_attr
					WHERE attr_id = ' . (int) $attr_id;
				$result = $this->db->sql_query($sql);
				$row = $this->db->sql_fetchrow($result);
				$this->db->sql_freeresult($result);

				if (!$row)
				{
					trigger_error($this->language->lang('QTE_MUST_SELECT') . adm_back_link($this->u_action));
				}

				$move_attr_name = $this->qte_move($row, $action, 1);

				if ($move_attr_name !== false)
				{
					$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_ATTRIBUTE_' . strtoupper($action), time(), [$row['attr_name'], $move_attr_name]);
				}

				if ($this->request->is_ajax())
				{
					$json_response = new \phpbb\json_response;
					$json_response->send(['success' => true]);
				}
			break;
		}

		$this->template->assign_vars([
			'U_ACTION'	=> $this->u_action . '&amp;action=' . (($action == 'add') ? 'add' : 'edit&amp;id=' . (int) $attr_id),
		]);

		$sql = 'SELECT topic_attr_id, COUNT(topic_id) AS total_topics
			FROM ' . $this->table_prefix . 'topics
			GROUP BY topic_attr_id';
		$result = $this->db->sql_query($sql);
		$stats = [];
		$total_topics = 0;
		while ($row = $this->db->sql_fetchrow($result))
		{
			$stats[$row['topic_attr_id']] = $row['total_topics'];
			$total_topics += $row['total_topics'];
		}
		$this->db->sql_freeresult($result);

		$sql = 'SELECT *
			FROM ' . $this->table_prefix . 'topics_attr
			ORDER BY left_id';
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$attribute_name = str_replace(['%mod%', '%date%'], [$this->language->lang('QTE_KEY_USERNAME'), $this->language->lang('QTE_KEY_DATE')], $this->language->lang($row['attr_name']));
			$attribute_count = $stats[$row['attr_id']] ?? 0;

			$this->template->assign_block_vars('row', [
				'S_IMAGE'		=> $row['attr_type'] ? true : false,
				'S_COLOUR'		=> $row['attr_colour'] ? true : false,
				'S_DESC'		=> $row['attr_desc'] ? true : false,
				'S_DATE'		=> $row['attr_date'] ? true : false,
				'S_USER_COLOUR'	=> $row['attr_user_colour'] ? true : false,
				'S_CSS'			=> (!$row['attr_type'] && isset($row['attr_name']) && empty($row['attr_colour'])) ? true : false,

				'QTE_TXT'		=> $attribute_name,
				'QTE_DESC'		=> $this->language->lang($row['attr_desc']),
				'QTE_IMG'		=> $this->qte->attr_img_key($row['attr_img'], $attribute_name),
				'QTE_COLOUR'	=> $row['attr_colour'],
				'QTE_DATE'		=> $row['attr_date'],
				'QTE_COUNT'		=> (int) $attribute_count,
				'QTE_PER_CENT'	=> empty($total_topics) ? 0 : round(intval($attribute_count) * 100 / $total_topics),

				'U_EDIT'		=> $this->u_action . '&amp;action=edit&amp;id=' . $row['attr_id'],
				'U_MOVE_UP'		=> $this->u_action . '&amp;action=move_up&amp;id=' . $row['attr_id'],
				'U_MOVE_DOWN'	=> $this->u_action . '&amp;action=move_down&amp;id=' . $row['attr_id'],
				'U_DELETE'		=> $this->u_action . '&amp;action=delete&amp;id=' . $row['attr_id'],
			]);
		}
		$this->db->sql_freeresult($result);
	}

	protected function _get_attr_info($attr_id)
	{
		$sql = 'SELECT * FROM ' . $this->table_prefix . 'topics_attr
			WHERE attr_id = ' . (int) $attr_id;
		$result = $this->db->sql_query($sql);
		$attr = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		return $attr;
	}

	protected function qte_move($attr_row, $action = 'move_up', $steps = 1)
	{
		$sql = 'SELECT attr_id, attr_name, left_id, right_id
			FROM ' . $this->table_prefix . "topics_attr
			WHERE " . (($action == 'move_up') ? "right_id < {$attr_row['right_id']} ORDER BY right_id DESC" : "left_id > {$attr_row['left_id']} ORDER BY left_id ASC");
		$result = $this->db->sql_query_limit($sql, $steps);
		$target = [];
		while ($row = $this->db->sql_fetchrow($result))
		{
			$target = $row;
		}
		$this->db->sql_freeresult($result);

		if (!count($target))
		{
			return false;
		}

		if ($action == 'move_up')
		{
			$left_id = $target['left_id'];
			$right_id = $attr_row['right_id'];

			$diff_up = $attr_row['left_id'] - $target['left_id'];
			$diff_down = $attr_row['right_id'] + 1 - $attr_row['left_id'];

			$move_up_left = $attr_row['left_id'];
			$move_up_right = $attr_row['right_id'];
		}
		else
		{
			$left_id = $attr_row['left_id'];
			$right_id = $target['right_id'];

			$diff_up = $attr_row['right_id'] + 1 - $attr_row['left_id'];
			$diff_down = $target['right_id'] - $attr_row['right_id'];

			$move_up_left = $attr_row['right_id'] + 1;
			$move_up_right = $target['right_id'];
		}

		$sql = 'UPDATE ' . $this->table_prefix . "topics_attr
			SET left_id = left_id + CASE
				WHEN left_id BETWEEN {$move_up_left} AND {$move_up_right} THEN -{$diff_up}
				ELSE {$diff_down}
			END,
			right_id = right_id + CASE
				WHEN right_id BETWEEN {$move_up_left} AND {$move_up_right} THEN -{$diff_up}
				ELSE {$diff_down}
			END
			WHERE left_id BETWEEN {$left_id} AND {$right_id}
				AND right_id BETWEEN {$left_id} AND {$right_id}";
		$this->db->sql_query($sql);

		return $target['attr_name'];
	}

	protected function qte_attr_select($attr_id)
	{
		$current_time = time();

		foreach ($this->qte->get_attr() as $attr)
		{
			if ($attr['attr_id'] != $attr_id)
			{
				$attribute_name = str_replace(['%mod%', '%date%'], [$this->user->data['username'], $this->user->format_date($current_time, $attr['attr_date'])], $this->language->lang($attr['attr_name']));

				$this->template->assign_block_vars('select_row', [
					'QTE_ID'		=> $attr['attr_id'],
					'QTE_TYPE'		=> $attr['attr_type'],
					'QTE_NAME'		=> $attribute_name,
					'QTE_DESC'		=> $this->language->lang($attr['attr_desc']),
					'QTE_COLOUR'	=> $this->qte->attr_colour($attr['attr_name'], $attr['attr_colour']),
				]);
			}
		}
	}

	/**
	 * Permission Copy
	 *
	 * Copy a permission (auth) option
	 */
	private function _copy_permission($auth_option, $copy_from, $clear_dest_perms = true)
	{
		if (!class_exists('auth_admin'))
		{
			include($this->root_path . 'includes/acp/auth.' . $this->php_ext);
		}

		$auth_admin = new \auth_admin();

		$old_id = $auth_admin->acl_options['id'][$copy_from];
		$new_id = $auth_admin->acl_options['id'][$auth_option];

		$tables = [$this->table_prefix . 'acl_groups', $this->table_prefix . 'acl_roles_data', $this->table_prefix . 'acl_users'];

		foreach ($tables as $table)
		{
			// Clear current permissions of destination attributes
			if ($clear_dest_perms)
			{
				$sql = 'DELETE FROM ' . $table . '
					WHERE auth_option_id = ' . (int) $new_id;
				$this->db->sql_query($sql);
			}

			$sql = 'SELECT *
				FROM ' . $table . '
				WHERE auth_option_id = ' . (int) $old_id;
			$result = $this->db->sql_query($sql);
			$sql_ary = [];
			while ($row = $this->db->sql_fetchrow($result))
			{
				$row['auth_option_id'] = (int) $new_id;
				$sql_ary[] = $row;
			}
			$this->db->sql_freeresult($result);

			if (!empty($sql_ary))
			{
				$this->db->sql_multi_insert($table, $sql_ary);
			}
		}

		$auth_admin->acl_clear_prefetch();
	}

	/**
	 * Set custom form action
	 *
	 * @param string  $u_action
	 */
	public function set_page_url($u_action)
	{
		$this->u_action = $u_action;
	}
}
