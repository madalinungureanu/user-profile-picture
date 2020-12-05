<?php
/**
 * Ajax actions for the plugin.
 *
 * @package user-profile-picture
 */

namespace MPP\Includes;

use MPP\Includes\Functions as Functions;
/**
 * Class Ajax
 */
class Ajax {

	/**
	 * Register Ajax actions.
	 */
	public function __construct() {
		// Ajax.
		add_action( 'wp_ajax_metronet_add_thumbnail', array( $this, 'ajax_add_thumbnail' ) );
		add_action( 'wp_ajax_metronet_get_thumbnail', array( $this, 'ajax_get_thumbnail' ) );
		add_action( 'wp_ajax_metronet_remove_thumbnail', array( $this, 'ajax_remove_thumbnail' ) );
		add_action( 'wp_ajax_metronet_user_list_add_thumbnail', array( $this, 'add_user_list_thumbnail' ) );
	}

	/**
	 * Add a thumbnail via Ajax.
	 *
	 * Adds a thumbnail to user meta and returns thumbnail html.
	 */
	public function ajax_add_thumbnail() {
		if ( ! current_user_can( 'upload_files' ) ) {
			die( '' );
		}
		$post_id      = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
		$user_id      = isset( $_POST['user_id'] ) ? absint( $_POST['user_id'] ) : 0;
		$thumbnail_id = isset( $_POST['thumbnail_id'] ) ? absint( $_POST['thumbnail_id'] ) : 0;
		if ( 0 === $post_id || 0 === $user_id || 0 === $thumbnail_id || 'mt_pp' !== get_post_type( $post_id ) ) {
			die( '' );
		}
		check_ajax_referer( "mt-update-post_$post_id" );

		// Save user meta.
		update_user_option( $user_id, 'metronet_post_id', $post_id );
		update_user_option( $user_id, 'metronet_image_id', $thumbnail_id ); // Added via this thread (Props Solinx) - https://wordpress.org/support/topic/storing-image-id-directly-as-user-meta-data.
		set_post_thumbnail( $post_id, $thumbnail_id );

		if ( has_post_thumbnail( $post_id ) ) {
			$thumb_src      = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'thumbnail', false, '' );
			$post_thumbnail = sprintf( '<img src="%s" width="150" height="150" title="%s" />', esc_url( $thumb_src[0] ), esc_attr__( 'Upload or Change Profile Picture', 'metronet-profile-picture' ) );
			$crop_html      = '';
			$thumb_html     = sprintf( '<a href="#" class="mpp_add_media">%s%s</a>', $post_thumbnail, sprintf( '<div id="metronet-click-edit">%s</div>', esc_html__( 'Click to Edit', 'metronet-profile-picture' ) ) );
			$thumb_html    .= sprintf( '<a id="metronet-remove" class="dashicons dashicons-trash" href="#" title="%s">%s</a>', esc_attr__( 'Remove profile image', 'metronet-profile-picture' ), esc_html__( 'Remove profile image', 'metronet-profile-picture' ) );
			wp_send_json(
				array(
					'thumb_html'          => $thumb_html,
					'crop_html'           => $crop_html,
					'has_thumb'           => true,
					'avatar'              => get_avatar( $user_id, 96 ),
					'avatar_admin_small'  => get_avatar( $user_id, 26 ),
					'avatar_admin_medium' => get_avatar( $user_id, 64 ),
					'user_id'             => $user_id,
					'logged_in_user_id'   => get_current_user_id(),
				)
			);
		}
		wp_send_json(
			array(
				'thumb_html'          => '',
				'crop_html'           => '',
				'has_thumb'           => false,
				'avatar'              => get_avatar( $user_id, 96 ),
				'avatar_admin_small'  => get_avatar( $user_id, 26 ),
				'avatar_admin_medium' => get_avatar( $user_id, 64 ),
				'user_id'             => $user_id,
				'logged_in_user_id'   => get_current_user_id(),
			)
		);
	} //end ajax_add_thumbnail

	/**
	 * Retrieve a thumbnail via Ajax.
	 *
	 * Retrieves a thumbnail based on a passed post id ($_POST)
	 */
	public function ajax_get_thumbnail() {
		if ( ! current_user_can( 'upload_files' ) ) {
			die( '' );
		}
		$post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
		check_ajax_referer( "mt-update-post_$post_id" );
		$post    = get_post( $post_id );
		$user_id = 0;
		if ( $post ) {
			$user_id = $post->post_author;
		}

		if ( has_post_thumbnail( $post_id ) ) {
			$thumb_src      = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'thumbnail', false, '' );
			$post_thumbnail = sprintf( '<img style="display:block" src="%s" width="150" height="150" title="%s" />', esc_url( $thumb_src[0] ), esc_attr__( 'Upload or Change Profile Picture', 'metronet-profile-picture' ) );
			$crop_html      = '';
			$thumb_html     = sprintf( '<a href="#" class="mpp_add_media">%s%s</a>', $post_thumbnail, sprintf( '<div id="metronet-click-edit">%s</div>', esc_html__( 'Click to Edit', 'metronet-profile-picture' ) ) );
			$thumb_html    .= sprintf( '<a id="metronet-remove" class="dashicons dashicons-trash" href="#" title="%s">%s</a>', esc_attr__( 'Remove profile image', 'metronet-profile-picture' ), esc_html__( 'Remove profile image', 'metronet-profile-picture' ) );
			wp_send_json(
				array(
					'thumb_html'          => $thumb_html,
					'crop_html'           => $crop_html,
					'has_thumb'           => true,
					'avatar'              => get_avatar( $user_id, 96 ),
					'avatar_admin_small'  => get_avatar( $user_id, 26 ),
					'avatar_admin_medium' => get_avatar( $user_id, 64 ),
					'user_id'             => $user_id,
					'logged_in_user_id'   => get_current_user_id(),
				)
			);
		} else {
			$thumb_html  = '<a style="display:block" href="#" class="mpp_add_media default-image">';
			$thumb_html .= sprintf( '<img style="display:block" src="%s" width="150" height="150" title="%s" />', Functions::get_plugin_url( 'img/mystery.png' ), esc_attr__( 'Upload or Change Profile Picture', 'metronet-profile-picture' ) );
			$thumb_html .= sprintf( '<div id="metronet-click-edit">%s</div>', esc_html__( 'Click to Edit', 'metronet-profile-picture' ) );
			$thumb_html .= '</a>';
		}
		wp_send_json(
			array(
				'thumb_html'          => $thumb_html,
				'crop_html'           => '',
				'has_thumb'           => false,
				'avatar'              => get_avatar( $user_id, 96 ),
				'avatar_admin_small'  => get_avatar( $user_id, 26 ),
				'avatar_admin_medium' => get_avatar( $user_id, 64 ),
				'user_id'             => $user_id,
				'logged_in_user_id'   => get_current_user_id(),
			)
		);
	}

	/**
	 * Remove a thumbnail via Ajax.
	 *
	 * Removes a featured thumbnail.
	 */
	public function ajax_remove_thumbnail() {
		if ( ! current_user_can( 'upload_files' ) ) {
			die( '' );
		}
		$post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
		$user_id = isset( $_POST['user_id'] ) ? absint( $_POST['user_id'] ) : 0;
		if ( 0 === $post_id || 0 === $user_id ) {
			die( '' );
		}
		check_ajax_referer( "mt-update-post_$post_id" );

		$thumb_html  = '<a style="display:block" href="#" class="mpp_add_media default-image">';
		$thumb_html .= sprintf( '<img style="display:block" src="%s" width="150" height="150" title="%s" />', Functions::get_plugin_url( 'img/mystery.png' ), esc_attr__( 'Upload or Change Profile Picture', 'metronet-profile-picture' ) );
		$thumb_html .= sprintf( '<div id="metronet-click-edit">%s</div>', esc_html__( 'Click to Edit', 'metronet-profile-picture' ) );
		$thumb_html .= '</a>';

		// Save user meta and update thumbnail.
		update_user_option( $user_id, 'metronet_image_id', 0 );
		delete_post_meta( $post_id, '_thumbnail_id' );
		wp_send_json(
			array(
				'thumb_html'          => $thumb_html,
				'crop_html'           => '',
				'has_thumb'           => false,
				'avatar'              => get_avatar( $user_id, 96 ),
				'avatar_admin_small'  => get_avatar( $user_id, 26 ),
				'avatar_admin_medium' => get_avatar( $user_id, 64 ),
				'user_id'             => $user_id,
				'logged_in_user_id'   => get_current_user_id(),
			)
		);
	}

	/**
	 * Add a thumbnail via Ajax.
	 *
	 * Adds a thumbnail to user meta and returns thumbnail html.
	 */
	public function add_user_list_thumbnail() {
		if ( ! current_user_can( 'edit_others_posts' ) ) {
			die( '' );
		}
		$user_id      = isset( $_POST['user_id'] ) ? absint( $_POST['user_id'] ) : 0;
		$post_id      = Functions::get_post_id( $user_id );
		$thumbnail_id = isset( $_POST['thumbnail_id'] ) ? absint( $_POST['thumbnail_id'] ) : 0;
		if ( 0 === $post_id || 0 === $user_id || 0 === $thumbnail_id || 'mt_pp' !== get_post_type( $post_id ) ) {
			die( '' );
		}
		check_ajax_referer( 'mt-update-user-list-avatar' );

		// Save user meta.
		update_user_option( $user_id, 'metronet_post_id', $post_id );
		update_user_option( $user_id, 'metronet_image_id', $thumbnail_id ); // Added via this thread (Props Solinx) - https://wordpress.org/support/topic/storing-image-id-directly-as-user-meta-data.
		set_post_thumbnail( $post_id, $thumbnail_id );

		if ( has_post_thumbnail( $post_id ) ) {
			$thumb_src      = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'thumbnail', false, '' );
			$post_thumbnail = sprintf( '<img src="%s" width="32" height="32" title="%s" />', esc_url( $thumb_src[0] ), esc_attr__( 'Upload or Change Profile Picture', 'metronet-profile-picture' ) );
			wp_send_json(
				array(
					'thumb_html' => $post_thumbnail,
					'user_id'    => $user_id,
				)
			);
		}
	} //end ajax_add_thumbnail
}
