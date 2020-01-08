/*
 * @package    jYProExtra System Plugin
 * @version    __DEPLOY_VERSION__
 * @author     Septdir Workshop - www.septdir.com
 * @copyright  Copyright (c) 2018 - 2020 Septdir Workshop. All rights reserved.
 * @license    GNU/GPL license: https://www.gnu.org/copyleft/gpl.html
 * @link       https://www.septdir.com/
 */

// Add modal plugin settings
function jYProExtraModal(requestUrl) {
	document.addEventListener('DOMContentLoaded', function () {
		let request = new XMLHttpRequest(),
			formData = new FormData();
		request.open('POST', requestUrl);
		request.send(formData);
		request.onreadystatechange = function () {
			if (this.readyState === 4 && this.status === 200) {
				let response = false;
				try {
					response = JSON.parse(this.response);
				} catch (e) {
					response = false;
					console.error(e.message);
					return;
				}
				if (response.success) {

					// Add modal
					let content = document.createElement('div');
					content.innerHTML = response.data[0].content;
					document.querySelector('body .uk-noconflict').appendChild(content.firstChild);

					// Add button
					let button = document.createElement('div');
					button.innerHTML = response.data[0].button;
					document.querySelector('body .uk-noconflict .yo-sidebar').appendChild(button.firstChild);

					// Add style
					let style = document.createElement('div');
					style.innerHTML = response.data[0].style;
					document.querySelector('head').appendChild(style.firstChild);

					// Modal actions
					let modal = document.querySelector('#jYProExtraModal'),
						iframe = modal.querySelector('iframe');
					UIkit.util.on('#jYProExtraModal', 'show', function () {
						iframe.setAttribute('src', iframe.getAttribute('data-src'));
					});
					UIkit.util.on('#jYProExtraModal', 'hide', function () {
						iframe.removeAttribute('src');
					});

					// Iframe actions
					let iframeSave = false,
						iframeLibraryImport = false;
					iframe.addEventListener('load', function () {
						let iframeBody = iframe.contentWindow.document.body;

						// Save params
						let saveButton = iframeBody.querySelector('#applyBtn');
						if (saveButton) {
							modal.querySelector('button[type="button"]').addEventListener('click', function (event) {
								event.preventDefault();
								saveButton.dispatchEvent(new Event('click'));
								iframeSave = true;
							});
						}
						if (iframeSave) {
							let preview = document.querySelector('iframe[name="preview-1"]');
							preview.contentWindow.location = preview.contentWindow.location;
							iframeSave = false;
						}

						// Import layouts
						let libraryImportButton = iframeBody.querySelector('[library-import="button"]');
						if (libraryImportButton) {
							libraryImportButton.addEventListener('click', function (event) {
								iframeLibraryImport = true;
							});
						}
						if (iframeLibraryImport && iframeBody.querySelector('.alert.alert-success')) {
							UIkit.notification(iframe.getAttribute('data-import-message'), {status: 'success'});
							iframeLibraryImport = false;
						}
					});

				} else {
					console.error(response.message);
				}
			} else if (this.readyState === 4 && this.status !== 200) {
				console.error(request.status + ' ' + request.message);
			}
		};
	});
}

// Remove toolbar from preview
function jYProExtraRemoveToolbar() {
	document.addEventListener('DOMContentLoaded', function () {
		let preview = document.querySelector('iframe[name="preview-1"]');
		preview.addEventListener('load', function () {
			let toolbar = preview.contentWindow.document.body.querySelector('#jYProExtraToolbar');
			if (toolbar) {
				toolbar.remove();
			}
		});
	});
}