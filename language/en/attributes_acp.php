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
	'QTE_ADD'			=> 'Create attribute',
	'QTE_ADD_EXPLAIN'	=> 'Here you can define the new attribute properties.',
	'QTE_EDIT'			=> 'Edit attribute',
	'QTE_EDIT_EXPLAIN'	=> 'Here you can modify the properties of the selected attribute.',

	'QTE_FIELDS'					=> 'Attribute fields',
	'QTE_TYPE'						=> 'Attribute type',
	'QTE_TYPE_TXT'					=> 'Text',
	'QTE_TYPE_IMG'					=> 'Image',
	'QTE_NAME'						=> 'Attribute name',
	'QTE_NAME_EXPLAIN'				=> 'Use language constant if name is served from language file, or enter directly the attribute name.<br />- Inserting <strong>%mod%</strong> will display the username who applied the attribute.<br />- Inserting <strong>%date%</strong> will display the day date when the attribute was applied.<br /><br />Example: <strong>[Solved by %mod%]</strong> will display <strong>[Solved by %s]</strong>',
	'QTE_DESC'						=> 'Attribute description',
	'QTE_DESC_EXPLAIN'				=> 'You can enter a short comment, which will be used in order to differentiate your attributes if some need to have the same name.',
	'QTE_IMG'						=> 'Attribute image',
	'QTE_IMG_EXPLAIN'				=> 'You can use the image name if it is served in the imageset or seize the relative path of the image.',
	'QTE_DATE'						=> 'Attribute date format',
	'QTE_DATE_EXPLAIN'				=> 'The syntax used is identical to the PHP <a href="http://www.php.net/date">date()</a> function.',
	'QTE_COLOUR'					=> 'Attribute colour',
	'QTE_COLOUR_EXPLAIN'			=> 'Select a value from the <strong>colour picker</strong> or enter it directly.',
	'QTE_USER_COLOUR'				=> 'Colour the username who applied the attribute',
	'QTE_USER_COLOUR_EXPLAIN'		=> 'If you use the <strong>%mod%</strong> argument and the option is enabled, the user group colour will be applied.',
	'QTE_COPY_AUTHS'				=> 'Copy permissions from',
	'QTE_COPY_AUTHS_EXPLAIN'		=> 'If you choose to copy permissions, the attribute will have the same permissions as the one you select here. This will overwrite any permissions you have previously set for this attribute. If the <strong>Custom</strong> option is selected, the current permissions will be kept.',
	'QTE_COPY_PERMISSIONS'			=> 'Copy attributes permissions from',
	'QTE_COPY_PERMISSIONS_EXPLAIN'	=> 'When created, the forum will have the same attributes permissions as the one you selected. If no forum is selected, attributes will not be displayed while their permissions will not have been defined.',

	'QTE_AUTH_ADD'				=> 'Add permission',
	'QTE_AUTH_REMOVE'			=> 'Remove permission',
	'QTE_AUTH_NO_PERMISSIONS'	=> 'Do not copy permissions',

	'QTE_ATTRIBUTE'		=> 'Attribute',
	'QTE_ATTRIBUTES'	=> 'Attributes',
	'QTE_USAGE'			=> 'Usage',

	'QTE_CSS'	=> 'Probably CSS-managed',
	'QTE_NONE'	=> 'N/A',

	'QTE_MUST_SELECT'			=> 'You must select an attribute.',
	'QTE_NAME_ERROR'			=> 'The attribute name field cannot be empty.',
	'QTE_COLOUR_ERROR'			=> 'The attribute colour field contains invalid characters.',
	'QTE_DATE_ARGUMENT_ERROR'	=> 'You have defined a date format without defining the <strong>%date%</strong> argument inside your attribute.',
	'QTE_DATE_FORMAT_ERROR'		=> 'You have defined the <strong>%date%</strong> argument inside your attribute without defining the date format.',
	'QTE_USER_COLOUR_ERROR'		=> 'You have enabled the option to colour the username without defining the <strong>%mod%</strong> argument inside your attribute.',
	'QTE_FORUM_ERROR'			=> 'You cannot specify a category or a forum link.',

	'QTE_ADDED'		=> 'A new attribute has been added.',
	'QTE_UPDATED'	=> 'The selected attribute has been updated.',
	'QTE_REMOVED'	=> 'The selected attribute has been deleted.',

	// Forums
	'QTE_TOPICS_ATTR_SETTINGS'	=> 'Topic attributes settings',

	'QTE_DEFAULT_ATTR'			=> 'Default attribute of the forum',
	'QTE_DEFAULT_ATTR_EXPLAIN'	=> 'The selected attribute will be applied when a new topic is created, whatever the user permissions.',
	'QTE_FORCE_USERS'			=> 'Force users to apply an attribute to their topic',
	'QTE_FORCE_USERS_EXPLAIN'	=> 'If enabled, users will have to select an attribute for their topic in that forum.',
]);
