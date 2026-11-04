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

class m3_remove_version extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return !isset($this->config['qte_version']);
	}

	public static function depends_on()
	{
		return ['\kaileymsnay\qte\migrations\v10x\m2_initial_data'];
	}

	/**
	 * Add, update or delete data
	 */
	public function update_data()
	{
		return [
			// Remove config_text table setting
			['config_text.remove', ['qte_version']],
		];
	}
}
