<?php
/**
 *
 * Quick Title Edition extension for the phpBB Forum Software package
 *
 * @copyright (c) 2023, Kailey Snay, https://www.snayhomelab.com/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = [];
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine
//
// Some characters you may want to copy&paste:
// ’ » “ ” …
//

$lang = array_merge($lang, [
	// Logs
	'LOG_ATTRIBUTE_ADDED'		=> '<strong>Added a new attribute</strong><br />» %s',
	'LOG_ATTRIBUTE_UPDATED'		=> '<strong>Updated an attribute</strong><br />» %s',
	'LOG_ATTRIBUTE_DELETED'		=> '<strong>Deleted an attribute</strong><br />» %s',
	'LOG_ATTRIBUTE_MOVE_DOWN'	=> '<strong>Moved an attribute</strong> %1$s <strong>below</strong> %2$s',
	'LOG_ATTRIBUTE_MOVE_UP'		=> '<strong>Moved an attribute</strong> %1$s <strong>above</strong> %2$s',

	'MCP_ATTRIBUTE_ADDED'	=> '<strong>Added a new attribute to the topic</strong><br />» %s',
	'MCP_ATTRIBUTE_UPDATED'	=> '<strong>Updated the attribute for the topic</strong><br />» %s',
	'MCP_ATTRIBUTE_DELETED'	=> '<strong>Deleted the attribute of the topic</strong><br />» %s',
]);
