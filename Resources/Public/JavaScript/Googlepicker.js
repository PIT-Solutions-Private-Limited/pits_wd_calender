/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

/**
 * Module: TYPO3/CMS/PitsWdCalender/Googlepicker
 * Googlepicker JavaScript
 */
define(['jquery'], function ($) {
	'use strict';

	/**
	 *
	 * @type {{options: {}}}
	 * @exports TYPO3/CMS/PitsWdCalender/Googlepicker
	 */
	var Googlepicker = {
		options: {}
	};

	/**
	 *
	 * @param {Object} options
	 */
	Googlepicker.setFieldChangeFunctions = function(options) {
		Googlepicker.options = options;
	};

	
	/**
	 *
	 */
	Googlepicker.initializeEvents = function() {

		// Handle the transfer of the color value and closing of popup
		$('#savedata').on('click', function(e) {
			e.preventDefault();
			
			var theField = parent.opener.TYPO3.jQuery('[data-formengine-input-name="' + $('[name="fieldName"]').val() + '"]').get(0);
			
			if (theField) {
				theField.value = $('#newlgeodata').val();
                if (typeof Googlepicker.options.fieldChangeFunctions === 'function') {
					Googlepicker.options.fieldChangeFunctions();
				}
			}
			parent.close();
			return false;
		});
	};

	$(Googlepicker.initializeEvents);

	return Googlepicker;
});

