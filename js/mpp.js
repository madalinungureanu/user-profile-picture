jQuery( document ).ready( function( $ ) {
	//Refresh the profile image thumbnail
	function mt_ajax_thumbnail_refresh() {
		var post_id = jQuery( "#metronet_post_id" ).val();
		$.post( metronet_profile_image.ajax_url, { 
				action: 'metronet_get_thumbnail', 
				post_id: post_id, 
			}, 
			function( response ) {
				jQuery( "#metronet-profile-image" ).html( response.thumb_html );
			},
			'json'
		);
	};
	//Remove the profile image
	function mt_remove_profile_image() {
		$.post( metronet_profile_image.ajax_url, { 
				action: 'metronet_remove_thumbnail', 
				post_id: metronet_profile_image.user_post_id, 
				user_id: jQuery( "#metronet_profile_id" ).val(), 
				_wpnonce: metronet_profile_image.nonce
			}, 
			function( response ) {
				jQuery( "#metronet-profile-image" ).html( response.thumb_html );
			},
			'json'
		);	
	}
	
	$('#mpp').on( "click", '.mpp_add_media', function(e) {
		//Assign the default view for the media uploader
		var uploader = wp.media({
			state: 'featured-image',
			states: [ new wp.media.controller.FeaturedImage() ],
		});
		uploader.state('featured-image').set( 'title', metronet_profile_image.set_profile_text );
		
		uploader.on( 'toolbar:create:featured-image', function( toolbar ) {
			var options = {
			};
			options.items = {};
			options.items.select = {
				text: metronet_profile_image.set_profile_text,
				style:    'primary',
				click: wp.media.view.Toolbar.Select.prototype.clickSelect,
				requires: { selection: true },
				event: 'select',
				reset: true,
				close: true,
				state: false
			};
			if ( $( '#metronet-profile-image img' ).length > 0 ) {
				options.items.remove = {
					text: metronet_profile_image.remove_profile_text,
					style:    'secondary',
					requires: { selection: false },
					click: wp.media.view.Toolbar.Select.prototype.clickSelect,
					event: 'remove',
					reset: true,
					close: true,
					state: false
				};
			}
			this.createSelectToolbar( toolbar, options );
		}, uploader );
			
		
		//For when the featured thumbnail is set
		uploader.mt_featured_set = function( id ) {
			$.post( metronet_profile_image.ajax_url, { 
					action: 'metronet_add_thumbnail', 
					post_id: metronet_profile_image.user_post_id, 
					user_id: jQuery( "#metronet_profile_id" ).val(), 
					thumbnail_id: id,
					_wpnonce: metronet_profile_image.nonce 
				}, 
				function( response ) {
					jQuery( "#metronet-profile-image" ).html( response.thumb_html );
				},
				'json'
			);
		};
		
		//For when the Add Profile Image is clicked
		uploader.state('featured-image').on( 'select', function() {
			var featured_id = this.get('selection').single().id;

			if ( ! featured_id )
				return;
			
			uploader.mt_featured_set( featured_id );
		} );
		
		//When the remove buttons is clicked
		uploader.state( 'featured-image' ).on( 'remove', function() {
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
		mt_remove_profile_image();
	} );
	
} );