<?php
/**
 * Admin actions for the plugin.
 *
 * @package user-profile-picture
 */

namespace MPP\Includes\Admin;

use MPP\Includes\Functions as Functions;
use MPP\Includes\Options as Options;

/**
 * Class Setup
 */
class Setup {

	/**
	 * Class constructor.
	 */
	public function __construct() {
		// For the admin interface.
		add_action( 'admin_menu', array( $this, 'register_settings_menu' ) );
		add_action( 'plugin_action_links_' . Functions::get_plugin_slug(), array( $this, 'plugin_settings_link' ) );

		add_action( 'init', array( $this, 'add_post_type' ) );
	}

	/**
	 * Adds the user profile picture post type.
	 */
	public function add_post_type() {
		$post_type_args = array(
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => false,
			'show_in_menu'       => false,
			'query_var'          => true,
			'rewrite'            => false,
			'has_archive'        => false,
			'hierarchical'       => false,
			'supports'           => array( 'thumbnail' ),
		);

		/**
		 * Allow other plugins to modify the post type creation arguments.
		 *
		 * @since 2.3.0
		 *
		 * @param array Post type arguments prior to registering the post type.
		 */
		$post_type_args = apply_filters( 'mpp_post_type_args', $post_type_args );
		register_post_type( 'mt_pp', $post_type_args );

		// Determine if to load image sizes or not.
		$options             = Options::get_options();
		$display_image_sizes = true;
		if ( 'on' === $options['disable_image_sizes'] ) {
			$display_image_sizes = false;
		}
		/**
		 * Filter the the creation of image sizes.
		 *
		 * @since 2.2.5
		 *
		 * @param bool Whether to allow image size creation or not
		 */
		if ( apply_filters( 'mpp_add_image_sizes', $display_image_sizes ) ) {
			add_image_size( 'profile_24', 24, 24, true );
			add_image_size( 'profile_48', 48, 48, true );
			add_image_size( 'profile_96', 96, 96, true );
			add_image_size( 'profile_150', 150, 150, true );
			add_image_size( 'profile_300', 300, 300, true );
		}
	}

	/**
	 * Adds a plugin settings link.
	 *
	 * Adds a plugin settings link.
	 *
	 * @param array $settings The settings array for the plugin.
	 *
	 * @return array Settings array.
	 */
	public function plugin_settings_link( $settings ) {
		$admin_settings_links = array();
		if ( defined( 'USER_PROFILE_PICTURE_ENHANCED' ) ) {
			$admin_settings_links[] = sprintf(
				'<a href="%s">%s</a>',
				esc_url( admin_url( 'admin.php?page=mpp' ) ),
				esc_html__( 'Settings', 'metronet-profile-picture' )
			);
		} else {
			$admin_settings_links[] = sprintf(
				'<a href="%s">%s</a>',
				esc_url( admin_url( 'users.php?page=mpp' ) ),
				esc_html__( 'Options', 'metronet-profile-picture' )
			);
			$admin_settings_links[] = sprintf(
				'<a href="%s" target="_blank">%s</a>',
				esc_url( $this->get_plugin_docs_url() ),
				esc_html__( 'Documentation', 'metronet-profile-picture' )
			);
		}
		if ( ! is_array( $settings ) ) {
			return $admin_settings_links;
		} else {
			return array_merge( $settings, $admin_settings_links );
		}
	}

	/**
	 * Admin page for User Profile Picture
	 *
	 * @since 2.3.0
	 */
	public function admin_page() {
		if ( isset( $_POST['submit'] ) && isset( $_POST['options'] ) ) {
			check_admin_referer( 'save_mpp_options' );
			$options = wp_unslash( $_POST['options'] ); // phpcs:ignore
			Options::update_options( $options );
			printf( '<div class="updated"><p><strong>%s</strong></p></div>', esc_html__( 'Your options have been saved.', 'metronet-profile-picture' ) );
		}
		// Get options and defaults.
		$options = Options::get_options();
		?>
		<div class="wrap upp-admin-wrap">
			<form action="" method="POST">
				<?php wp_nonce_field( 'save_mpp_options' ); ?>
				<h1><strong>User Profile</strong> Picture</h1>
				<p class="upp-info-text">The easiest way to add a profile picture for your users.</p>
				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row"><?php esc_html_e( 'Gutenberg Blocks', 'metronet-profile-picture' ); ?></th>
							<td>
								<input type="hidden" name="options['load_gutenberg']" value="on" />
								<input id="mpp-load-gutenberg" type="checkbox" value="off" name="options[load_gutenberg]" <?php checked( 'off', $options['load_gutenberg'] ); ?> /> <label for="mpp-load-gutenberg"><?php esc_html_e( 'Disable Gutenberg Blocks', 'metronet-profile-picture' ); ?></label>
								<p class="description"><?php esc_html_e( 'Select this option if you do not want User Profile Picture to show up in Gutenberg or do not plan on using the blocks.', 'metronet-profile-picture' ); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Disable Image Sizes?', 'metronet-profile-picture' ); ?></th>
							<td>
								<input type="hidden" name="options['disable_image_sizes']" value="off" />
								<input id="mpp-display-image-sizes" type="checkbox" value="on" name="options[disable_image_sizes]" <?php checked( 'on', $options['disable_image_sizes'] ); ?> /> <label for="mpp-display-image-sizes"><?php esc_html_e( 'Disable Image Sizes', 'metronet-profile-picture' ); ?></label>
								<p class="description"><?php esc_html_e( 'Select this option to disable the four image sizes User Profile Picture Creates.' ); ?></p>
							</td>
						</tr>
						<?php
						/**
						 * Allow other plugins to run code after the user profile admin Table Row.
						 *
						 * @since 2.3.0
						 *
						 * @param array $options Array of options.
						 */
						do_action( 'mpp_user_profile_admin_settings_after_row', $options );
						?>
					</tbody>
				</table>
				<?php
				/**
				 * Allow other plugins to run code after the user profile admin Table.
				 *
				 * @since 2.3.0
				 *
				 * @param array $options Array of options.
				 */
				do_action( 'mpp_user_profile_admin_settings_after_table', $options );
				?>
				<?php submit_button( __( 'Save Options', 'metronet-profile-picture' ) ); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Register the settings menu for User Profile Picture
	 *
	 * @since 2.3.0
	 */
	public function register_settings_menu() {
		if ( defined( 'USER_PROFILE_PICTURE_ENHANCED' ) ) {
			$hook = add_menu_page(
				__( 'User Profile Picture', 'metronet-profile-picture' ),
				__( 'User Profile Picture', 'metronet-profile-picture' ),
				'manage_options',
				'mpp',
				array( $this, 'admin_page' ),
				'dashicons-groups',
				100
			);
		} else {
			$hook = add_users_page(
				__( 'Profile Picture', 'metronet-profile-picture' ),
				__( 'Profile Picture', 'metronet-profile-picture' ),
				'manage_options',
				'mpp',
				array( $this, 'admin_page' )
			);
		}
	}
}
