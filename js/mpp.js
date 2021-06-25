jQuery( document ).ready( function( $ ) {
	//Refresh the profile image thumbnail
	function mt_ajax_thumbnail_refresh() {
		var post_id = jQuery( "#metronet_post_id" ).val();
		jQuery( '#metronet-profile-image' ).html( '<img class="mpp-loading" alt="Loading" width="150" height="150" src="' + metronet_profile_image.loading_gif + '" />' );
		$.post( metronet_profile_image.ajax_url, {
				action: 'metronet_get_thumbnail',
				post_id: post_id,
				_wpnonce: metronet_profile_image.nonce,
				user_id: jQuery( "#metronet_profile_id" ).val(),
			},
			function( response ) {
				jQuery( "#metronet-profile-image" ).html( mt_display_block( response.thumb_html ) );
				jQuery( '.user-profile-picture img ').replaceWith( response.avatar );
				if ( response.user_id === response.logged_in_user_id ) {
					jQuery( '#wp-admin-bar-my-account img.avatar-26' ).replaceWith( response.avatar_admin_small );
					jQuery( '#wp-admin-bar-my-account img.avatar-64' ).replaceWith( response.avatar_admin_medium );
				}
			},
			'json'
		);
	};
	//Remove the profile image
	function mt_remove_profile_image() {
		jQuery( '#metronet-profile-image' ).html( '<img class="mpp-loading" alt="Loading" width="150" height="150" src="' + metronet_profile_image.loading_gif + '" />' );
		$.post( metronet_profile_image.ajax_url, {
				action: 'metronet_remove_thumbnail',
				post_id: metronet_profile_image.user_post_id,
				user_id: jQuery( "#metronet_profile_id" ).val(),
				_wpnonce: metronet_profile_image.nonce
			},
			function( response ) {
				jQuery( "#metronet-profile-image" ).html( mt_display_block( response.thumb_html ) );
				jQuery( '.user-profile-picture img ').replaceWith( response.avatar );
				if ( response.user_id === response.logged_in_user_id ) {
					jQuery( '#wp-admin-bar-my-account img.avatar-26' ).replaceWith( response.avatar_admin_small );
					jQuery( '#wp-admin-bar-my-account img.avatar-64' ).replaceWith( response.avatar_admin_medium );
				}
			},
			'json'
		);
	}
	// Set thumbnail img and wrapping a to display:block to fix visual bug
	function mt_display_block(htmlString) {
		var temp = document.createElement('div');
		temp.innerHTML = htmlString;
		temp.firstElementChild.style.display = 'block';
		temp.firstElementChild.firstElementChild.style.display = 'block';
		return temp.innerHTML;
	}

	$('#mpp').on( "click", '.mpp_add_media', function(e) {

		//Assign the default view for the media uploader

		var uploader = wp.media({
			state: 'featured-image',
			states: [ new wp.media.controller.FeaturedImage() ],
			title: metronet_profile_image.set_profile_text,
			button: {
				text: metronet_profile_image.remove_profile_text
			},
			multiple: false,
		});

		// CUSTOM TOOLBAR ON BOTTOM OF MEDIA MANAGER. SETS UP THE TWO ACTION BUTTONS

		uploader.on( 'toolbar:create', function( toolbar ) {
			var options = {};
			options.items = {};
			options.items.select = {
				text: metronet_profile_image.set_profile_text,
				style: 'primary',
				click: wp.media.view.Toolbar.Select.prototype.clickSelect,
				requires: { selection: true },
				event: 'select',
				reset: false,
				close: true,
				state: false,
				syncSelection: true
			};
			if ( ! $( '#metronet-profile-image a' ).hasClass('default-image') ) {
				options.items.remove = {
					text: metronet_profile_image.remove_profile_text,
					style:    'secondary',
					requires: { selection: false },
					click: wp.media.view.Toolbar.Select.prototype.clickSelect,
					event: 'remove',
					reset: true,
					close: true,
					state: false,
					syncSelection: true
				};
			}
			this.createSelectToolbar( toolbar, options );
		}, uploader );


		//For when the featured thumbnail is set
		uploader.mt_featured_set = function( id ) {
			jQuery( '#metronet-profile-image' ).html( '<img class="mpp-loading" alt="Loading" width="150" height="150" src="' + metronet_profile_image.loading_gif + '" />' );
			$.post( metronet_profile_image.ajax_url, {
					action: 'metronet_add_thumbnail',
					post_id: metronet_profile_image.user_post_id,
					user_id: jQuery( "#metronet_profile_id" ).val(),
					thumbnail_id: id,
					_wpnonce: metronet_profile_image.nonce
				},
				function( response ) {
					jQuery( "#metronet-profile-image" ).html( mt_display_block( response.thumb_html ) );
					jQuery( '.user-profile-picture img ').replaceWith( response.avatar );
					if ( response.user_id === response.logged_in_user_id ) {
						jQuery( '#wp-admin-bar-my-account img.avatar-26' ).replaceWith( response.avatar_admin_small );
						jQuery( '#wp-admin-bar-my-account img.avatar-64' ).replaceWith( response.avatar_admin_medium );
					}
				},
				'json'
			);
		};

		//For when the Add Profile Image is clicked
		uploader.on( 'select', function() {

			var featured = uploader.state().get('selection').single();
			wp.media.featuredImage.set( featured ? featured.id : -1 );
			if ( ! featured.id ) {
				return;
			}

			uploader.mt_featured_set( featured.id );


		} );

		//When the remove buttons is clicked
		uploader.on( 'remove', function() {
			wp.media.featuredImage.set( -1 );
			mt_remove_profile_image();
		} );


		//For when the window is closed (update the thumbnail)
		uploader.on('escape', function(){
			mt_ajax_thumbnail_refresh();
		});

		//Open the media uploader
		uploader.open();
		return false;
	});
	$( "#mpp" ).on( 'click', 'a#metronet-remove', function( e ) {
		e.preventDefault();
		wp.media.featuredImage.set( -1 );
		mt_remove_profile_image();
	} );

} );