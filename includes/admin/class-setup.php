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

		new \MPP\Includes\Admin\Tabs\Settings();
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
	 * Registers and outputs placeholder for settings.
	 *
	 * @since 1.0.0
	 */
	public static function settings_page() {
		?>
		<div class="wrap upp-admin-wrap">
			<?php
			self::get_settings_header();
			self::get_settings_tabs();
			self::get_settings_footer();
			?>
		</div>
		<?php
	}

	/**
	 * Output the top-level admin tabs.
	 */
	public static function get_settings_tabs() {
		$settings_url_base = Functions::get_settings_url( 'setup' )
		?>
			<?php
			$tabs = array();
			/**
			 * Filer the output of the tab output.
			 *
			 * Potentially modify or add your own tabs.
			 *
			 * @since 1.0.0
			 *
			 * @param array $tabs Associative array of tabs.
			 */
			$tabs       = apply_filters( 'mpp_admin_tabs', $tabs );
			$tab_html   = '<nav class="nav-tab-wrapper">';
			$tabs_count = count( $tabs );
			if ( $tabs && ! empty( $tabs ) && is_array( $tabs ) ) {
				$active_tab = Functions::get_admin_tab();
				if ( null === $active_tab ) {
					$active_tab = 'setup';
				}
				$is_tab_match = false;
				if ( 'setup' === $active_tab ) {
					$active_tab = 'setup';
				} else {
					foreach ( $tabs as $tab ) {
						$tab_get = isset( $tab['get'] ) ? $tab['get'] : '';
						if ( $active_tab === $tab_get ) {
							$is_tab_match = true;
						}
					}
					if ( ! $is_tab_match ) {
						$active_tab = 'setup';
					}
				}
				$do_action = false;
				foreach ( $tabs as $tab ) {
					$classes = array( 'nav-tab' );
					$tab_get = isset( $tab['get'] ) ? $tab['get'] : '';
					if ( $active_tab === $tab_get ) {
						$classes[] = 'nav-tab-active';
						$do_action = isset( $tab['action'] ) ? $tab['action'] : false;
					} elseif ( ! $is_tab_match && 'setup' === $tab_get ) {
						$classes[] = 'nav-tab-active';
						$do_action = isset( $tab['action'] ) ? $tab['action'] : false;
					}
					$tab_url   = isset( $tab['url'] ) ? $tab['url'] : '';
					$tab_label = isset( $tab['label'] ) ? $tab['label'] : '';
					$tab_html .= sprintf(
						'<a href="%s" class="%s" id="eff-%s"><svg class="mpp-icon mpp-icon-tab">%s</svg><span>%s</span></a>',
						esc_url( $tab_url ),
						esc_attr( implode( ' ', $classes ) ),
						esc_attr( $tab_get ),
						sprintf( '<use xlink:href="#%s"></use>', esc_attr( $tab['icon'] ) ),
						esc_html( $tab['label'] )
					);
				}
				$tab_html .= '</nav>';
				if ( $tabs_count > 0 ) {
					echo wp_kses( $tab_html, Functions::get_kses_allowed_html() );
				}

				$current_tab     = Functions::get_admin_tab();
				$current_sub_tab = Functions::get_admin_sub_tab();

				/**
				 * Filer the output of the sub-tab output.
				 *
				 * Potentially modify or add your own sub-tabs.
				 *
				 * @since 3.0.0
				 *
				 * @param array Associative array of tabs.
				 * @param string Tab
				 * @param string Sub Tab
				 */
				$sub_tabs = apply_filters( 'mpp_admin_sub_tabs', array(), $current_tab, $current_sub_tab );

				// Check to see if no tabs are available for this view.
				if ( null === $current_tab && null === $current_sub_tab ) {
					$current_tab = 'setup';
				}
				if ( $sub_tabs && ! empty( $sub_tabs ) && is_array( $sub_tabs ) ) {
					if ( null === $current_sub_tab ) {
						$current_sub_tab = '';
					}
					$is_tab_match      = false;
					$first_sub_tab     = current( $sub_tabs );
					$first_sub_tab_get = $first_sub_tab['get'];
					if ( $first_sub_tab_get === $current_sub_tab ) {
						$active_tab = $current_sub_tab;
					} else {
						$active_tab = $current_sub_tab;
						foreach ( $sub_tabs as $tab ) {
							$tab_get = isset( $tab['get'] ) ? $tab['get'] : '';
							if ( $active_tab === $tab_get ) {
								$is_tab_match = true;
							}
						}
						if ( ! $is_tab_match ) {
							$active_tab = $first_sub_tab_get;
						}
					}
					$sub_tab_html_array = array();
					$do_subtab_action   = false;
					$maybe_sub_tab      = '';
					foreach ( $sub_tabs as $sub_tab ) {
						$classes = array( 'mpp-sub-tab' );
						$tab_get = isset( $sub_tab['get'] ) ? $sub_tab['get'] : '';
						if ( $active_tab === $tab_get ) {
							$classes[]        = 'mpp-sub-tab-active';
							$do_subtab_action = true;
							$current_sub_tab  = $tab_get;
						} elseif ( ! $is_tab_match && $first_sub_tab_get === $tab_get ) {
							$classes[]        = 'mpp-sub-tab-active';
							$do_subtab_action = true;
							$current_sub_tab  = $first_sub_tab_get;
						}
						$tab_url   = isset( $sub_tab['url'] ) ? $sub_tab['url'] : '';
						$tab_label = isset( $sub_tab['label'] ) ? $sub_tab['label'] : '';
						if ( $current_sub_tab === $tab_get ) {
							$sub_tab_html_array[] = sprintf( '<span class="%s" id="mpp-tab-%s">%s</span>', esc_attr( implode( ' ', $classes ) ), esc_attr( $tab_get ), esc_html( $sub_tab['label'] ) );
						} else {
							$sub_tab_html_array[] = sprintf( '<a href="%s" class="%s" id="mpp-tab-%s">%s</a>', esc_url( $tab_url ), esc_attr( implode( ' ', $classes ) ), esc_attr( $tab_get ), esc_html( $sub_tab['label'] ) );
						}
					}
					if ( ! empty( $sub_tab_html_array ) ) {
						echo '<nav class="mpp-sub-links">' . wp_kses_post( rtrim( implode( ' | ', $sub_tab_html_array ), ' | ' ) ) . '</nav>';
					}
					if ( $do_subtab_action ) {
						/**
						 * Perform a sub tab action.
						 *
						 * Perform a sub tab action. Useful for loading scripts or inline styles as necessary.
						 *
						 * @since 3.0.0
						 *
						 * mpp_admin_sub_tab_{current_tab}_{current_sub_tab}
						 * @param string Sub Tab
						 */
						do_action(
							sprintf( // phpcs:ignore
								'mpp_admin_sub_tab_%s_%s',
								sanitize_title( $current_tab ),
								sanitize_title( $current_sub_tab )
							)
						);
					}
				}
				if ( $do_action ) {

					/**
					 * Perform a tab action.
					 *
					 * Perform a tab action.
					 *
					 * @since 3.0.0
					 *
					 * @param string $action Can be any action.
					 * @param string Tab
					 * @param string Sub Tab
					 */
					do_action( $do_action, $current_tab, $current_sub_tab );
				}
			}
			?>
		<?php
	}

	/**
	 * Output Admin Page Header.
	 */
	public static function get_settings_header() {
		?>
		<div class="wrap upp-admin-wrap">
			<h1><strong>User Profile</strong> Picture</h1>
			<p class="upp-info-text"><?php esc_html_e( 'The easiest way to add a profile picture for your users.', 'metronet-profile-picture' ); ?></p>
		<?php
	}

	/**
	 * Run script and enqueue stylesheets and stuff like that.
	 */
	public static function get_settings_footer() {
		?>
		</div><!-- .wrap.upp-admin-wrap -->
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
				array( '\MPP\Includes\Admin\Setup', 'settings_page' ),
				'dashicons-groups',
				100
			);
		} else {
			$hook = add_users_page(
				__( 'Profile Picture', 'metronet-profile-picture' ),
				__( 'Profile Picture', 'metronet-profile-picture' ),
				'manage_options',
				'mpp',
				array( '\MPP\Includes\Admin\Setup', 'settings_page' )
			);
		}
	}
}
