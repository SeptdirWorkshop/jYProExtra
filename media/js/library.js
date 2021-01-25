/*
 * @package    jYProExtra System Plugin
 * @version    __DEPLOY_VERSION__
 * @author     Septdir Workshop - www.septdir.com
 * @copyright  Copyright (c) 2018 - 2021 Septdir Workshop. All rights reserved.
 * @license    GNU/GPL license: https://www.gnu.org/copyleft/gpl.html
 * @link       https://www.septdir.com/
 */

document.addEventListener('DOMContentLoaded', function () {
	// Import
	document.querySelectorAll('[library-import="container"]').forEach(function (container) {
		let url = container.getAttribute('data-url'),
			button = container.querySelector('[library-import="button"]'),
			field = container.querySelector('input[type="file"]');

		if (button) {
			button.addEventListener('click', function () {
				let request = new XMLHttpRequest(),
					requestUrl = url,
					formData = new FormData();
				Array.from(field.files).forEach(function (file) {
					formData.append('files[]', file);
				});

				request.open('POST', requestUrl);
				request.send(formData);
				request.onreadystatechange = function () {
					if (this.readyState === 4 && this.status === 200) {
						let response = false;
						try {
							response = JSON.parse(this.response);
						} catch (e) {
							response = false;
							Joomla.renderMessages({"error": [e.message]});
							return;
						}
						if (response.success) {
							window.location.reload();
						} else {
							Joomla.renderMessages({"error": [response.message]});
							console.error(response.message);
						}
					} else if (this.readyState === 4 && this.status !== 200) {
						Joomla.renderMessages({"error": [request.message]});
					}
				};
			});
		}
	});

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
