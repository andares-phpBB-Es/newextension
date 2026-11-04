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
	'QTE_ADD'			=> 'Crear atributo',
	'QTE_ADD_EXPLAIN'	=> 'Aquí puede definir propiedades del nuevo atributo.',
	'QTE_EDIT'			=> 'Editar atributo',
	'QTE_EDIT_EXPLAIN'	=> 'Aquí puede modificar las propiedades del atributo seleccionado.',

	'QTE_FIELDS'					=> 'Campos del atributo',
	'QTE_TYPE'						=> 'Tipo de atributo',
	'QTE_TYPE_TXT'					=> 'Texto',
	'QTE_TYPE_IMG'					=> 'Imagen',
	'QTE_NAME'						=> 'Nombre del atributo',
	'QTE_NAME_EXPLAIN'				=> 'Utilice la constante de idioma si el nombre se proporciona desde el archivo de idioma o ingrese directamente el nombre del atributo.<br />- Insertando <strong>%mod%</strong> mostrará el nombre de usuario que aplicó el atributo.<br />- Insertando <strong>%date%</strong> mostrará la fecha del día en que se aplicó el atributo.<br /><br />Por ejemplo: <strong>[Solucionado por %mod%]</strong> mostrará <strong>[Solucionado por %s]</strong>',
	'QTE_DESC'						=> 'Descripción del atributo',
	'QTE_DESC_EXPLAIN'				=> 'Puede añadir un comentario breve, que se utilizará para diferenciar sus atributos si algunos necesitan tener el mismo nombre.',
	'QTE_IMG'						=> 'Imagen del atributo',
	'QTE_IMG_EXPLAIN'				=> 'Puede utilizar el nombre de la imagen si se sirve en el conjunto de imágenes o aprovechar la ruta relativa de la imagen.',
	'QTE_DATE'						=> 'Formato de fecha del atributo',
	'QTE_DATE_EXPLAIN'				=> 'La sintaxis utilizada es idéntica a la función PHP <a href="http://www.php.net/date">date()</a>.',
	'QTE_COLOUR'					=> 'Color del atributo',
	'QTE_COLOUR_EXPLAIN'			=> 'Seleccione un valor del <strong>selector de color</strong> o ingréselo directamente.',
	'QTE_USER_COLOUR'				=> 'Colorea el nombre de usuario que aplicó el atributo',
	'QTE_USER_COLOUR_EXPLAIN'		=> 'Si utiliza el argumento <strong>%mod%</strong> y la opción está habilitada, se aplicará el color del grupo de usuarios.',
	'QTE_COPY_AUTHS'				=> 'Copiar permisos de',
	'QTE_COPY_AUTHS_EXPLAIN'		=> 'Si elige copiar permisos, el atributo tendrá los mismos permisos que el que seleccione aquí. Esto sobrescribirá cualquier permiso que haya establecido previamente para este atributo. Si selecciona la opción <strong>Personalizado</strong> se conservarán los permisos actuales.',
	'QTE_COPY_PERMISSIONS'			=> 'Copiar permisos de atributos de',
	'QTE_COPY_PERMISSIONS_EXPLAIN'	=> 'Al crear el foro, éste tendrá los mismos permisos de atributos que el foro seleccionado. Si no se selecciona ningún foro, no se mostrarán los atributos mientras no se hayan definido sus permisos.',

	'QTE_AUTH_ADD'				=> 'Añadir permiso',
	'QTE_AUTH_REMOVE'			=> 'Quitar permiso',
	'QTE_AUTH_NO_PERMISSIONS'	=> 'No copiar permisos',

	'QTE_ATTRIBUTE'		=> 'Atributo',
	'QTE_ATTRIBUTES'	=> 'Atributos',
	'QTE_USAGE'			=> 'Uso',

	'QTE_CSS'	=> 'Probablemente administrado por CSS',
	'QTE_NONE'	=> 'N/A',

	'QTE_MUST_SELECT'			=> 'Debe seleccionar un atributo.',
	'QTE_NAME_ERROR'			=> 'El campo de nombre del atributo no puede estar vacío.',
	'QTE_COLOUR_ERROR'			=> 'El campo de color del atributo contiene caracteres no válidos.',
	'QTE_DATE_ARGUMENT_ERROR'	=> 'Ha definido un formato de fecha sin definir el argumento <strong>%date%</strong> dentro de su atributo.',
	'QTE_DATE_FORMAT_ERROR'		=> 'Ha definido el argumento <strong>%date%</strong> dentro de su atributo sin definir el formato de fecha.',
	'QTE_USER_COLOUR_ERROR'		=> 'Ha habilitado la opción de colorear el nombre de usuario sin definir el argumento <strong>%mod%</strong> dentro de su atributo.',
	'QTE_FORUM_ERROR'			=> 'No puede especificar una categoría o un enlace de foro.',

	'QTE_ADDED'		=> 'Un nuevo atributo ha sido añadido.',
	'QTE_UPDATED'	=> 'El atributo seleccionado ha sido actualizado.',
	'QTE_REMOVED'	=> 'El atributo seleccionado ha sido borrado.',

	// Forums
	'QTE_TOPICS_ATTR_SETTINGS'	=> 'Ajustes de atributos del tema',

	'QTE_DEFAULT_ATTR'			=> 'Atributo por defecto del foro',
	'QTE_DEFAULT_ATTR_EXPLAIN'	=> 'El atributo seleccionado se aplicará cuando se cree un nuevo tema, independientemente de los permisos del usuario.',
	'QTE_FORCE_USERS'			=> 'Obligar a los usuarios a poner un atributo a su tema',
	'QTE_FORCE_USERS_EXPLAIN'	=> 'Si está habilitado, los usuarios tendrán que seleccionar un atributo para su tema en ese foro.',
]);
