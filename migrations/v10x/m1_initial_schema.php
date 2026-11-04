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

class m1_initial_schema extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return $this->db_tools->sql_column_exists($this->table_prefix . 'forums', 'default_attr');
	}

	public static function depends_on()
	{
		return ['\phpbb\db\migration\data\v33x\v338'];
	}

	/**
	 * Update database schema
	 */
	public function update_schema()
	{
		return [
			'add_tables'		=> [
				$this->table_prefix . 'topics_attr'				=> [
					'COLUMNS'		=> [
						'attr_id'			=> ['UINT', null, 'auto_increment'],
						'attr_type'			=> ['BOOL', 0],
						'attr_name'			=> ['VCHAR:255', ''],
						'left_id'			=> ['UINT', 0],
						'right_id'			=> ['UINT', 0],
						'attr_img'			=> ['VCHAR:255', ''],
						'attr_date'			=> ['VCHAR:30', ''],
						'attr_colour'		=> ['VCHAR:6', ''],
						'attr_user_colour'	=> ['BOOL', 0],
						'attr_desc'			=> ['VCHAR:255', ''],
					],
					'PRIMARY_KEY'	=> 'attr_id',
				],
			],
			'add_columns'		=> [
				$this->table_prefix . 'topics'					=> [
					'topic_attr_id'			=> ['UINT', 0],
					'topic_attr_user'		=> ['UINT', 0],
					'topic_attr_time'		=> ['TIMESTAMP', 0],
				],

				$this->table_prefix . 'forums'					=> [
					'force_attr'			=> ['BOOL', 0],
					'default_attr'			=> ['UINT', 0],
				],
			],
		];
	}

	/**
	 * Revert database schema
	 */
	public function revert_schema()
	{
		return [
			'drop_columns'		=> [
				$this->table_prefix . 'forums'					=> [
					'force_attr',
					'default_attr',
				],

				$this->table_prefix . 'topics'					=> [
					'topic_attr_id',
					'topic_attr_user',
					'topic_attr_time',
				],
			],
			'drop_tables'		=> [
				$this->table_prefix . 'topics_attr',
			],
		];
	}
}
