<?php
/**
 *
 * Quick Title Edition extension for the phpBB Forum Software package
 *
 * @copyright (c) 2023, Kailey Snay, https://www.snayhomelab.com/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace kaileymsnay\qte\acp;

/**
 * Quick Title Edition ACP module info
 */
class main_info
{
	public function module()
	{
		return [
			'filename'	=> '\kaileymsnay\qte\acp\main_module',
			'title'		=> 'QTE_MANAGE_TITLE',
			'modes'		=> [
				'manage'	=> [
					'title'	=> 'QTE_MANAGE_TITLE',
					'auth'	=> 'ext_kaileymsnay/qte && acl_a_attr_manage',
					'cat'	=> ['ACP_MESSAGES'],
				],
			],
		];
	}
}
