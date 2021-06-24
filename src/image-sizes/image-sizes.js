let __ = wp.i18n.__;
let _n = wp.i18n._n;

import axios from 'axios';

jQuery(function ($) {
	const MPP_Image_Sizes = {
		init() {
			this.registerAddImageSize();
			this.registerEditImageSize();
			this.registerCancelImageSize();
			this.registerSaveImageSize();
			this.registerDeleteImageSize();
		},

		/**
		 * Send command via Ajax.
		 */
		sendCommand(action, data, callback, options = {}, blockUI = true) {
			let default_options = {
				json: true,
				alert_on_error: false,
				prefix: 'mpp_',
				nonce: $('#_mpp').val(),
				timeout: null,
				async: true,
				type: 'POST',
			};
			for (let opt in default_options) {
				if (!options.hasOwnProperty(opt)) {
					options[opt] = default_options[opt];
				}
			}
			// Axios and WordPress require data as form data.
			var formData = new FormData();
			for (let key in data) {
				formData.append(key, data[key]);
			}
			formData.append('action', options.prefix + action);

			axios({
				method: options.type,
				url: ajaxurl,
				data: formData,
			}).then(
				(response) => {
					$.unblockUI();
					if (!response.data.success && options.alert_on_error) {
						alert(response.data.data.message);
						return;
					}
					if ('function' === typeof callback) callback(response.data);
				},
				(response) => {
					$.unblockUI();
					alert(__('Could not complete request', 'metronet-profile-picture'));
				}
			);
		},

		/**
		 * Register the add image size button.
		 */
		registerAddImageSize() {
			$('body').on('click', '#mpp-image-size-save', function (e) {
				e.preventDefault();
				MPP_Image_Sizes.sendCommand(
					'add_image_size',
					{
						nonce: $('#_mpp').val(),
						width: $('#mpp-field-image-size-width-input').val(),
						height: $('#mpp-field-image-size-height-input').val(),
						name: $('#mpp-field-image-sizes-input').val(),
					},
					(response) => {
						if (response.success) {
							$('.image-size-status')
								.removeClass('mpp-success mpp-error')
								.addClass('mpp-success')
								.html(response.data.message)
								.css('display', 'block');
							$('#mpp-image-sizes-table').html(response.data.html);
						} else {
							$('.image-size-status')
								.removeClass('mpp-success mpp-error')
								.addClass('mpp-error')
								.html(response.data[0].message)
								.css('display', 'block');
						}
						setTimeout(function () {
							$('.image-size-status').fadeOut();
						}, 5000);
					}
				);
			});
		},
		registerEditImageSize() {
			$('body').on('click', '.mpp-image-size-edit', function (e) {
				e.preventDefault();
				const $target = $(e.target);
				let $parent = $target.parents('.mpp-image-size-row');
				$parent.find('span').hide();
				$parent.find('input').attr('type', 'text');
				$parent.find('input:first').trigger('focus');
				$parent.find('.mpp-image-size-edit, .mpp-image-size-delete').hide();
				$parent.find('.mpp-image-size-save, .mpp-image-size-cancel').show();
			});
		},
		registerCancelImageSize() {
			$('body').on('click', '.mpp-image-size-cancel', function (e) {
				e.preventDefault();
				const $target = $(e.target);
				let $parent = $target.parents('.mpp-image-size-row');
				$parent.find('span').show();
				$parent.find('input').attr('type', 'hidden');
				$parent.find('.mpp-image-size-edit, .mpp-image-size-delete').show();
				$parent.find('.mpp-image-size-save, .mpp-image-size-cancel').hide();
			});
		},
		registerSaveImageSize() {
			$('body').on('click', '.mpp-image-size-save', function (e) {
				e.preventDefault();
				const $target = $(e.target);
				let $parent = $target.parents('.mpp-image-size-row');
				MPP_Image_Sizes.sendCommand(
					'edit_image_size',
					{
						nonce: $('#_mpp').val(),
						slug: $parent.find('.mpp-image-size-table-name').data('slug'),
						width: $parent.find('.mpp-image-size-table-width').val(),
						height: $parent.find('.mpp-image-size-table-height').val(),
						name: $parent.find('.mpp-image-size-table-name').val(),
					},
					(response) => {
						if (response.success) {
							$('#mpp-image-sizes-table').html(response.data.html);
						} else {
							alert(response.data[0].message);
						}
					}
				);
			});
		},
		registerDeleteImageSize() {
			$('body').on('click', '.mpp-image-size-delete', function (e) {
				e.preventDefault();
				const $target = $(e.target);
				let $parent = $target.parents('.mpp-image-size-row');
				MPP_Image_Sizes.sendCommand(
					'delete_image_size',
					{
						nonce: $('#_mpp').val(),
						slug: $parent.find('.mpp-image-size-table-name').data('slug'),
					},
					(response) => {
						if (response.success) {
							$('#mpp-image-sizes-table').html(response.data.html);
						} else {
							alert(response.data[0].message);
						}
					}
				);
			});
		},
	};
	MPP_Image_Sizes.init();
});
