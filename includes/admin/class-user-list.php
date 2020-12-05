<?php
/**
 * Add profile changes from the user list screen.
 *
 * @package user-profile-picture
 */

namespace MPP\Includes\Admin;

use MPP\Includes\Functions as Functions;
use MPP\Includes\Options as Options;

/**
 * Class Setup
 */
class User_List {

	/**
	 * Class Constructor.
	 */
	public function __construct() {

		// Scripts.
		add_action( 'admin_print_scripts-users.php', array( $this, 'print_media_scripts' ) );
	}

	/**
	 * Output media scripts for thickbox and media uploader
	 **/
	public function print_media_scripts() {
		$options = Options::get_options();
		if ( 'on' !== $options['change_profile_user_list'] ) {
			return;
		}
		if ( ! current_user_can( 'edit_others_pages' ) ) { // Editor and above.
			return;
		}
		wp_enqueue_media();
		$script_deps = array( 'media-editor' );
		wp_enqueue_script( 'mt-pp-user-list', Functions::get_plugin_url( '/js/mpp-user-list.js' ), $script_deps, Functions::get_plugin_version(), true );
		wp_localize_script(
			'mt-pp-user-list',
			'metronet_profile_image_user_list',
			array(
				'set_profile_text'    => __( 'Set Profile Image', 'metronet-profile-picture' ),
				'remove_profile_text' => __( 'Remove Profile Image', 'metronet-profile-picture' ),
				'crop'                => __( 'Crop Thumbnail', 'metronet-profile-picture' ),
				'ajax_url'            => esc_url( admin_url( 'admin-ajax.php' ) ),
				'nonce'               => wp_create_nonce( 'mt-update-user-list-avatar' ),
				'loading_gif'         => esc_url( Functions::get_plugin_url( '/img/loading.gif' ) ),
			)
		);
		wp_enqueue_style(
			'mpp-profile-picture',
			Functions::get_plugin_url( '/dist/profile-picture.css' ),
			array( 'dashicons' ),
			Functions::get_plugin_version(),
			'all'
		);
	}
}
