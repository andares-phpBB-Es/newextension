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
	'QTE_ATTRIBUTES'	=> 'Atributos del Tema',
	'QTE_ATTRIBUTE'		=> 'Atributo del Tema',

	'QTE_ATTRIBUTE_ADD' 	=> 'Seleccione un atributo de tema',
	'QTE_ATTRIBUTE_DELETE'	=> 'Eliminar atributo del tema',
	'QTE_ATTRIBUTE_DESIRED'	=> 'Seleccione el atributo deseado',
	'QTE_ATTRIBUTE_KEEP'	=> 'Mantener el atributo actual',

	// Notifications
	'QTE_ATTRIBUTE_ADDED'	=> 'Se ha aplicado un atributo al título del tema',
	'QTE_ATTRIBUTE_UPDATED' => 'El atributo de ese tema ha sido actualizado',
	'QTE_ATTRIBUTE_DELETED' => 'El atributo de tema ha sido eliminado',

	'QTE_TOPIC_ATTRIBUTE_ADDED'		=> 'Se ha aplicado un atributo al tema seleccionado',
	'QTE_TOPICS_ATTRIBUTE_ADDED'	=> 'Se ha aplicado un atributo a los temas seleccionados',
	'QTE_TOPIC_ATTRIBUTE_UPDATED'	=> 'El atributo del tema seleccionado ha sido actualizado',
	'QTE_TOPICS_ATTRIBUTE_UPDATED'	=> 'El atributo de los temas seleccionados han sido actualizados',
	'QTE_TOPIC_ATTRIBUTE_DELETED'	=> 'El atributo del tema seleccionado ha sido eliminado',
	'QTE_TOPICS_ATTRIBUTE_DELETED'	=> 'El atributo de los temas seleccionados han sido eliminados',

	// Search
	'QTE_ATTRIBUTE_SELECT'	=> 'Seleccione un atributo de tema',

	'QTE_ATTRIBUTE_SEARCH'			=> 'Buscar por atributos',
	'QTE_ATTRIBUTE_SEARCH_EXPLAIN'	=> 'Seleccione el atributo que desea buscar',

	// Sort
	'QTE_SORT'	=> 'Como atributo',
	'QTE_ALL'	=> 'Todo',

	// Errors
	'QTE_ATTRIBUTE_UNSELECTED' => '¡Debe seleccionar un atributo!',

	// Placeholders
	'QTE_KEY_USERNAME'	=> '¦user¦',
	'QTE_KEY_DATE'		=> '¦date¦',

	// Topic attributes as keys
	'QTE_SOLVED'	=> '[Solucionado por %mod% :: %date%]',
	'QTE_CANCELLED'	=> 'Cancelado',
	// Claves de atributos personalizadas de la versión antigua de ernadoo/qte.
	'QTE_REUP'	=> 'RESUBIDO',
	'QTE_ROTO'	=> 'ENLACE ROTO',
	'QTE_LAUT'	=> 'LISTADO AUTOMATICO',
	'QTE_LVPINT'	=> 'LISTADO ZONA VIP & INT.',
]);
