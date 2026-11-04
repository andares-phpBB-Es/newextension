/**
 *
 * Quick Title Edition extension for the phpBB Forum Software package
 *
 * @copyright (c) 2023, Kailey Snay, https://www.snayhomelab.com/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */
(function ($) { // Avoid conflicts with other libraries

	phpbb.addAjaxCallback('qte.attr_apply', function(data) {

		var new_attribute = data.NEW_ATTRIBUTE;
		var parent = $('h2.topic-title .qte-attr');

		if (new_attribute) {

			if (parent.length) {
				parent.replaceWith(new_attribute);
			}
			else {
				$('h2.topic-title').prepend(new_attribute + '&nbsp;');
			}
		}
		else {
			$('h2.topic-title').html($('h2.topic-title a'));
		}

		phpbb.closeDarkenWrapper(3000);
	});

})(jQuery); // Avoid conflicts with other libraries
