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
	// Select
	'QTE_ATTRIBUTES'	=> 'Topic attributes',
	'QTE_ATTRIBUTE'		=> 'Topic attribute',

	'QTE_ATTRIBUTE_ADD'		=> 'Select a topic attribute',
	'QTE_ATTRIBUTE_DELETE'	=> 'Delete the topic attribute',
	'QTE_ATTRIBUTE_DESIRED'	=> 'Select desired attribute',
	'QTE_ATTRIBUTE_KEEP'	=> 'Keep the actual attribute',

	// Notifications
	'QTE_ATTRIBUTE_ADDED'	=> 'An attribute has been applied to the topic title',
	'QTE_ATTRIBUTE_UPDATED'	=> 'The attribute of the topic has been updated',
	'QTE_ATTRIBUTE_DELETED'	=> 'The topic attribute has been deleted',

	'QTE_TOPIC_ATTRIBUTE_ADDED'		=> 'An attribute has been applied to the selected topic',
	'QTE_TOPICS_ATTRIBUTE_ADDED'	=> 'An attribute has been applied to the selected topics',
	'QTE_TOPIC_ATTRIBUTE_UPDATED'	=> 'The attribute of the selected topic has been updated',
	'QTE_TOPICS_ATTRIBUTE_UPDATED'	=> 'The attribute of the selected topics has been updated',
	'QTE_TOPIC_ATTRIBUTE_DELETED'	=> 'The attribute of the selected topic has been deleted',
	'QTE_TOPICS_ATTRIBUTE_DELETED'	=> 'The attribute of the selected topics has been deleted',

	// Search
	'QTE_ATTRIBUTE_SELECT'	=> 'Select a topic attribute',

	'QTE_ATTRIBUTE_SEARCH'			=> 'Search for attributes',
	'QTE_ATTRIBUTE_SEARCH_EXPLAIN'	=> 'Select the attribute you wish to search',

	// Sort
	'QTE_SORT'	=> 'As attribute',
	'QTE_ALL'	=> 'All',

	// Errors
	'QTE_ATTRIBUTE_UNSELECTED'	=> 'You must select an attribute!',

	// Placeholders
	'QTE_KEY_USERNAME'	=> '¦user¦',
	'QTE_KEY_DATE'		=> '¦date¦',

	// Topic attributes as keys
	'QTE_SOLVED'	=> '[Solved by %mod% :: %date%]',
	'QTE_CANCELLED'	=> 'Cancelled',
]);
