/*
 * @package    jYProExtra System Plugin
 * @version    __DEPLOY_VERSION__
 * @author     Septdir Workshop - www.septdir.com
 * @copyright  Copyright (c) 2018 - 2019 Septdir Workshop. All rights reserved.
 * @license    GNU/GPL license: https://www.gnu.org/copyleft/gpl.html
 * @link       https://www.septdir.com/
 */

document.addEventListener('DOMContentLoaded', function () {
	// Export
	document.querySelectorAll('[library-export="container"]').forEach(function (container) {
		let url = container.getAttribute('data-url'),
			button = container.querySelector('[library-export="button"]');

		if (button) {
			button.addEventListener('click', function () {
				let checkboxes = container.querySelectorAll('input[type="checkbox"]'),
					keys = [];
				checkboxes.forEach(function (input) {
					if (input.checked) {
						keys.push(input.value);
					}
				});

				let winUrl = url;
				if (keys.length > 0) {
					winUrl = winUrl + keys.join(',');
				}
				let win = window.open(winUrl, '_blank');
				win.focus();
			});
		}
	});
});
