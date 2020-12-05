jQuery(document).ready(function ($) {
	var MPP = {
		user_id: -1,
		init: function() {
			this.profile_image_click();
		},
		remove_thumbnail_image: function() {
			$.post(
				metronet_profile_image_user_list.ajax_url,
				{
					action: 'metronet_user_list_remove_thumbnail',
					post_id: metronet_profile_image_user_list.user_post_id,
					user_id: MPP.user_id,
					_wpnonce: metronet_profile_image_user_list.nonce,
				},
				function (response) {
					$( '#user-' + MPP.user_id + ' img' ).replaceWith( response.thumb_html );
				},
				'json'
			);
		},
		profile_image_click: function() {
			$('.column-username').on('click', 'img', function (e) {
				//Assign the default view for the media uploader
				$parent = jQuery( this ).closest( 'tr' ); // Find parent table row.
				MPP.user_id = $parent.find( '.check-column' ).find( 'input[type=checkbox]' ).val();
				var uploader = wp.media({
					states: [
						new wp.media.controller.Library({
							title: metronet_profile_image_user_list.set_profile_text,
							library: wp.media.query({type: 'image'}),
							multiple: false,
							date: false,
							priority: 20,
						}),
					],
					title: metronet_profile_image_user_list.set_profile_text,
					button: {
						text: metronet_profile_image_user_list.remove_profile_text,
					},
					multiple: false,
				});
		
				// CUSTOM TOOLBAR ON BOTTOM OF MEDIA MANAGER. SETS UP THE TWO ACTION BUTTONS
		
				uploader.on(
					'toolbar:create',
					function (toolbar) {
						var options = {};
						options.items = {};
						options.items.select = {
							text: metronet_profile_image_user_list.set_profile_text,
							style: 'primary',
							click: wp.media.view.Toolbar.Select.prototype.clickSelect,
							requires: {selection: true},
							event: 'select',
							reset: false,
							close: true,
							state: false,
							syncSelection: true,
						};
						options.items.remove = {
							text: metronet_profile_image_user_list.remove_profile_text,
							style: 'secondary',
							requires: {selection: false},
							click: wp.media.view.Toolbar.Select.prototype.clickSelect,
							event: 'remove',
							reset: true,
							close: true,
							state: false,
							syncSelection: true,
						};
						this.createSelectToolbar(toolbar, options);
					},
					uploader
				);
		
				//For when the featured thumbnail is set
				uploader.mt_featured_set = function (id) {
					$.post(
						metronet_profile_image_user_list.ajax_url,
						{
							action: 'metronet_user_list_add_thumbnail',
							thumbnail_id: id,
							user_id: MPP.user_id,
							_wpnonce: metronet_profile_image_user_list.nonce,
						},
						function (response) {
							$( '#user-' + MPP.user_id + ' img' ).replaceWith( response.thumb_html )
						},
						'json'
					);
				};
		
				//For when the Add Profile Image is clicked
				uploader.on('select', function () {
					var featured = uploader.state().get('selection').single();
					if (!featured.id) {
						return;
					}
					uploader.mt_featured_set(featured.id);
				});
		
				//When the remove buttons is clicked
				uploader.on('remove', function () {
					MPP.remove_thumbnail_image();
				});
		
				//Open the media uploader
				uploader.open();
				return false;
			});
		}
	}
	MPP.init();
});
