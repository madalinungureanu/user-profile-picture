<?php
/**
 * Helper functions for the plugin.
 *
 * @package user-profile-picture
 */

namespace MPP\Includes;

/**
 * Class Functions
 */
class Functions {

	/**
	 * Gets a post id for the user - Creates a post if a post doesn't exist
	 *
	 * @param int $user_id User ID of the user.
	 * @return int post_id
	 */
	public static function get_post_id( $user_id = 0 ) {

		$user = get_user_by( 'id', $user_id );

		// Get/Create Profile Picture Post.
		$post_args = array(
			'post_type'   => 'mt_pp',
			'author'      => $user_id,
			'post_status' => 'publish',
		);
		$posts     = get_posts( $post_args );
		if ( ! $posts ) {
			$post_id = wp_insert_post(
				array(
					'post_author' => $user_id,
					'post_type'   => 'mt_pp',
					'post_status' => 'publish',
					'post_title'  => $user->data->display_name,
				)
			);
		} else {
			$post    = end( $posts );
			$post_id = $post->ID;
		}
		return $post_id;
	}

	/**
	 * Checks if the plugin is on a multisite install.
	 *
	 * @since 3.0.0
	 *
	 * @param bool $network_admin Check if in network admin.
	 *
	 * @return true if multisite, false if not.
	 */
	public static function is_multisite( $network_admin = false ) {
		if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
			require_once ABSPATH . '/wp-admin/includes/plugin.php';
		}
		$is_network_admin = false;
		if ( $network_admin ) {
			if ( is_network_admin() ) {
				if ( is_multisite() && is_plugin_active_for_network( self::get_plugin_slug() ) ) {
					return true;
				}
			} else {
				return false;
			}
		}
		if ( is_multisite() && is_plugin_active_for_network( self::get_plugin_slug() ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Output SVG Sprite for MPP.
	 */
	public static function output_svg_sprite() {
		// From Fontawesome.
		?>
		<svg width="0" height="0" class="hidden">
			<symbol aria-hidden="true" data-prefix="fad" data-icon="save" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" id="mpp-save-duotone">
				<g class="fa-group"><path class="fa-secondary" fill="currentColor" d="M288 352a64 64 0 1 1-64-64 64 64 0 0 1 64 64z" opacity="0.4"></path><path class="fa-primary" fill="currentColor" d="M433.94 129.94l-83.88-83.88A48 48 0 0 0 316.12 32H48A48 48 0 0 0 0 80v352a48 48 0 0 0 48 48h352a48 48 0 0 0 48-48V163.88a48 48 0 0 0-14.06-33.94zM224 416a64 64 0 1 1 64-64 64 64 0 0 1-64 64zm96-204a12 12 0 0 1-12 12H76a12 12 0 0 1-12-12V108a12 12 0 0 1 12-12h228.52a12 12 0 0 1 8.48 3.52l3.48 3.48a12 12 0 0 1 3.52 8.48z"></path></g>
			</symbol>
			<symbol aria-hidden="true" data-prefix="fad" data-icon="pencil" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" id="mpp-pencil-duotone">
				<g class="fa-group"><path class="fa-secondary" fill="currentColor" d="M96 352H32l-16 64 80 80 64-16v-64H96zM498 74.26l-.11-.11L437.77 14a48.09 48.09 0 0 0-67.9 0l-46.1 46.1a12 12 0 0 0 0 17l111 111a12 12 0 0 0 17 0l46.1-46.1a47.93 47.93 0 0 0 .13-67.74z" opacity="0.4"></path><path class="fa-primary" fill="currentColor" d="M.37 483.85a24 24 0 0 0 19.47 27.8 24.27 24.27 0 0 0 8.33 0l67.32-16.16-79-79zM412.3 210.78l-111-111a12.13 12.13 0 0 0-17.1 0L32 352h64v64h64v64l252.27-252.25a12 12 0 0 0 .03-16.97z"></path></g>
			</symbol>
			<symbol aria-hidden="true" data-prefix="fad" data-icon="trash-alt" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" id="mpp-trash-alt-duotone">
				<g class="fa-group"><path class="fa-secondary" fill="currentColor" d="M32 464a48 48 0 0 0 48 48h288a48 48 0 0 0 48-48V96H32zm272-288a16 16 0 0 1 32 0v224a16 16 0 0 1-32 0zm-96 0a16 16 0 0 1 32 0v224a16 16 0 0 1-32 0zm-96 0a16 16 0 0 1 32 0v224a16 16 0 0 1-32 0z" opacity="0.4"></path><path class="fa-primary" fill="currentColor" d="M432 32H312l-9.4-18.7A24 24 0 0 0 281.1 0H166.8a23.72 23.72 0 0 0-21.4 13.3L136 32H16A16 16 0 0 0 0 48v32a16 16 0 0 0 16 16h416a16 16 0 0 0 16-16V48a16 16 0 0 0-16-16zM128 160a16 16 0 0 0-16 16v224a16 16 0 0 0 32 0V176a16 16 0 0 0-16-16zm96 0a16 16 0 0 0-16 16v224a16 16 0 0 0 32 0V176a16 16 0 0 0-16-16zm96 0a16 16 0 0 0-16 16v224a16 16 0 0 0 32 0V176a16 16 0 0 0-16-16z"></path></g>
			</symbol>
			<symbol aria-hidden="true" data-prefix="fad" data-icon="undo" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" id="mpp-undo-duotone">
				<g class="fa-group"><path class="fa-secondary" fill="currentColor" d="M123.31 388.69a12 12 0 0 1 16.38-.54 176 176 0 1 0-29.61-230.61l-46.5 2.22 3.52-64.43A247.45 247.45 0 0 1 256 8c136.66 0 248.1 111.53 248 248.19C503.9 393.07 392.9 504 256 504a247.1 247.1 0 0 1-166.21-63.88l-.49-.46a12 12 0 0 1 0-17z" opacity="0.4"></path><path class="fa-primary" fill="currentColor" d="M11.65 0h48A12 12 0 0 1 71 12.55l-7.42 147.21 147.54-7.06h.58a12 12 0 0 1 12 12V212a12 12 0 0 1-12 12h-200a12 12 0 0 1-12-12V12A12 12 0 0 1 11.65 0z"></path></g>
			</symbol>
		</svg>
		<?php
	}

	/**
	 * Returns true if on user profile page, false if not.
	 */
	public static function is_user_profile_page() {
		if ( defined( 'IS_PROFILE_PAGE' ) && IS_PROFILE_PAGE === true ) {
			return true;
		}
		return false;
	}

	/**
	 * Gets a user ID for the user.
	 *
	 * @return int user_id
	 */
	public static function get_user_id() {
		// Get user ID.
		$user_id = isset( $_GET['user_id'] ) ? absint( $_GET['user_id'] ) : 0; // phpcs:ignore
		if ( 0 === $user_id && IS_PROFILE_PAGE ) {
			$current_user = wp_get_current_user();
			$user_id      = $current_user->ID;
		}
		return $user_id;
	}

	/**
	 * Gets an array of plugins active on either the current site, or site-wide
	 *
	 * @since 3.0.0
	 *
	 * @return array A list of plugin paths (relative to the plugin directory)
	 */
	public static function get_active_plugins() {

		// Gets all active plugins on the current site.
		$active_plugins = get_option( 'active_plugins' );

		if ( self::is_multisite() ) {
			$network_active_plugins = get_site_option( 'active_sitewide_plugins' );
			if ( ! empty( $network_active_plugins ) ) {
				$network_active_plugins = array_keys( $network_active_plugins );
				$active_plugins         = array_merge( $active_plugins, $network_active_plugins );
			}
		}

		return $active_plugins;
	}

	/**
	 * Checks to see if an asset is activated or not.
	 *
	 * @since 3.0.0
	 *
	 * @param string $path Path to the asset.
	 * @param string $type Type to check if it is activated or not.
	 *
	 * @return bool true if activated, false if not.
	 */
	public static function is_activated( $path, $type = 'plugin' ) {

		// Gets all active plugins on the current site.
		$active_plugins = self::is_multisite() ? get_site_option( 'active_sitewide_plugins' ) : get_option( 'active_plugins', array() );
		if ( in_array( $path, $active_plugins, true ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Return the URL to the admin screen
	 *
	 * @param string $tab     Tab path to load.
	 * @param string $sub_tab Subtab path to load.
	 *
	 * @return string URL to admin screen. Output is not escaped.
	 */
	public static function get_settings_url( $tab = '', $sub_tab = '' ) {
		$options_url = admin_url( 'users.php?page=mpp' );
		if ( self::is_multisite() ) {
			$options_url = network_admin_url( 'settings.php?page=mpp' );
		}
		if ( ! empty( $tab ) ) {
			$options_url = add_query_arg( array( 'tab' => sanitize_title( $tab ) ), $options_url );
			if ( ! empty( $sub_tab ) ) {
				$options_url = add_query_arg( array( 'subtab' => sanitize_title( $sub_tab ) ), $options_url );
			}
		}
		return $options_url;
	}

	/**
	 * Get the current admin tab.
	 *
	 * @return null|string Current admin tab.
	 */
	public static function get_admin_tab() {
		$tab = filter_input( INPUT_GET, 'tab', FILTER_DEFAULT );
		if ( $tab && is_string( $tab ) ) {
			return sanitize_text_field( sanitize_title( $tab ) );
		}
		return null;
	}

	/**
	 * Get the current admin sub-tab.
	 *
	 * @return null|string Current admin sub-tab.
	 */
	public static function get_admin_sub_tab() {
		$tab = filter_input( INPUT_GET, 'tab', FILTER_DEFAULT );
		if ( $tab && is_string( $tab ) ) {
			$subtab = filter_input( INPUT_GET, 'subtab', FILTER_DEFAULT );
			if ( $subtab && is_string( $subtab ) ) {
				return sanitize_text_field( sanitize_title( $subtab ) );
			}
		}
		return null;
	}

	/**
	 * Return the plugin slug.
	 *
	 * @return string plugin slug.
	 */
	public static function get_plugin_slug() {
		return dirname( plugin_basename( METRONET_PROFILE_PICTURE_FILE ) );
	}

	/**
	 * Return the basefile for the plugin.
	 *
	 * @return string base file for the plugin.
	 */
	public static function get_plugin_file() {
		return plugin_basename( METRONET_PROFILE_PICTURE_FILE );
	}

	/**
	 * Return the version for the plugin.
	 *
	 * @return float version for the plugin.
	 */
	public static function get_plugin_version() {
		return METRONET_PROFILE_PICTURE_VERSION;
	}

	/**
	 * Get the Plugin Logo.
	 */
	public static function get_plugin_logo() {
		/**
		 * Filer the output of the plugin logo.
		 *
		 * Potentially change branding of the plugin.
		 *
		 * @since 3.0.0
		 *
		 * @param string URL to the plugin logo.
		 */
		return apply_filters( 'mpp_plugin_logo_full', self::get_plugin_url( '/img/logo/plugin-logo.png' ) );
	}

	/**
	 * Get the plugin author name.
	 */
	public static function get_plugin_author() {
		/**
		 * Filer the output of the plugin Author.
		 *
		 * Potentially change branding of the plugin.
		 *
		 * @since 3.0.0
		 *
		 * @param string Plugin Author name.
		 */
		$plugin_author = apply_filters( 'mpp_plugin_author', 'Cozmoslabs' );
		return $plugin_author;
	}

	/**
	 * Return the Plugin author URI.
	 */
	public static function get_plugin_author_uri() {
		/**
		 * Filer the output of the plugin Author URI.
		 *
		 * Potentially change branding of the plugin.
		 *
		 * @since 3.0.0
		 *
		 * @param string Plugin Author URI.
		 */
		$plugin_author = apply_filters( 'mpp_plugin_author_uri', 'https://cozmoslabs.com' );
		return $plugin_author;
	}

	/**
	 * Get the Plugin Icon.
	 */
	public static function get_plugin_icon() {
		/**
		 * Filer the output of the plugin icon.
		 *
		 * Potentially change branding of the plugin.
		 *
		 * @since 3.0.0
		 *
		 * @param string URL to the plugin icon.
		 */
		return apply_filters( 'mpp_plugin_icon', self::get_plugin_url( '/images/logo/ultimate-auto-updates-white-bg.png' ) );
	}

	/**
	 * Return the plugin name for the plugin.
	 *
	 * @return string Plugin name.
	 */
	public static function get_plugin_name() {
		/**
		 * Filer the output of the plugin name.
		 *
		 * Potentially change branding of the plugin.
		 *
		 * @since 3.0.0
		 *
		 * @param string Plugin name.
		 */
		return apply_filters( 'mpp_plugin_name', __( 'User Profile Picture', 'metronet-profile-picture' ) );
	}

	/**
	 * Get the Plugin Documentation URL.
	 *
	 * @since 3.0.0
	 */
	public static function get_plugin_docs_url() {
		/**
		 * Filer the output of the plugin documentation page.
		 *
		 * Potentially change branding of the plugin.
		 *
		 * @since 3.0.0
		 *
		 * @param string Plugin documentation URL.
		 */
		return apply_filters( 'mpp_documentation_link', 'https://www.cozmoslabs.com/user-profile-picture/' );
	}

	/**
	 * Return the plugin description for the plugin.
	 *
	 * @return string plugin description.
	 */
	public static function get_plugin_description() {
		/**
		 * Filer the output of the plugin name.
		 *
		 * Potentially change branding of the plugin.
		 *
		 * @since 3.0.0
		 *
		 * @param string Plugin description.
		 */
		return apply_filters( 'mpp_plugin_description', __( 'Use the native WP uploader on your user profile page.', 'metronet-profile-picture' ) );
	}

	/**
	 * Retrieve the plugin URI.
	 */
	public static function get_plugin_uri() {
		/**
		 * Filer the output of the plugin URI.
		 *
		 * Potentially change branding of the plugin.
		 *
		 * @since 3.0.0
		 *
		 * @param string Plugin URI.
		 */
		return apply_filters( 'uau_plugin_uri', 'https://www.cozmoslabs.com/user-profile-picture/' );
	}

	/**
	 * Retrieve the plugin Menu Name.
	 */
	public static function get_plugin_menu_name() {
		/**
		 * Filer the output of the plugin menu name.
		 *
		 * Potentially change branding of the plugin.
		 *
		 * @since 3.0.0
		 *
		 * @param string Plugin Menu Name.
		 */
		return apply_filters( 'mpp_plugin_menu_name', __( 'Profile Picture', 'metronet-profile-picture' ) );
	}

	/**
	 * Retrieve the plugin title.
	 */
	public static function get_plugin_title() {
		/**
		 * Filer the output of the plugin title.
		 *
		 * Potentially change branding of the plugin.
		 *
		 * @since 3.0.0
		 *
		 * @param string Plugin Menu Name.
		 */
		return apply_filters( 'mpp_plugin_menu_title', self::get_plugin_name() );
	}

	/**
	 * Returns appropriate html for KSES.
	 *
	 * @param bool $svg Whether to add SVG data to KSES.
	 */
	public static function get_kses_allowed_html( $svg = true ) {
		$allowed_tags = wp_kses_allowed_html();

		$allowed_tags['nav']        = array(
			'class' => array(),
		);
		$allowed_tags['a']['class'] = array();

		if ( ! $svg ) {
			return $allowed_tags;
		}
		$allowed_tags['svg'] = array(
			'xmlns'       => array(),
			'fill'        => array(),
			'viewbox'     => array(),
			'role'        => array(),
			'aria-hidden' => array(),
			'focusable'   => array(),
			'class'       => array(),
		);

		$allowed_tags['path'] = array(
			'd'       => array(),
			'fill'    => array(),
			'opacity' => array(),
		);

		$allowed_tags['g'] = array();

		$allowed_tags['use'] = array(
			'xlink:href' => array(),
		);

		$allowed_tags['symbol'] = array(
			'aria-hidden' => array(),
			'viewBox'     => array(),
			'id'          => array(),
			'xmls'        => array(),
		);

		return $allowed_tags;
	}

	/**
	 * Get the plugin directory for a path.
	 *
	 * @param string $path The path to the file.
	 *
	 * @return string The new path.
	 */
	public static function get_plugin_dir( $path = '' ) {
		$dir = rtrim( plugin_dir_path( METRONET_PROFILE_PICTURE_FILE ), '/' );
		if ( ! empty( $path ) && is_string( $path ) ) {
			$dir .= '/' . ltrim( $path, '/' );
		}
		return $dir;
	}

	/**
	 * Return a plugin URL path.
	 *
	 * @param string $path Path to the file.
	 *
	 * @return string URL to to the file.
	 */
	public static function get_plugin_url( $path = '' ) {
		$dir = rtrim( plugin_dir_url( METRONET_PROFILE_PICTURE_FILE ), '/' );
		if ( ! empty( $path ) && is_string( $path ) ) {
			$dir .= '/' . ltrim( $path, '/' );
		}
		return $dir;
	}

	/**
	 * Gets the highest priority for a filter.
	 *
	 * @param int $subtract The amount to subtract from the high priority.
	 *
	 * @return int priority.
	 */
	public static function get_highest_priority( $subtract = 0 ) {
		$highest_priority = PHP_INT_MAX;
		$subtract         = absint( $subtract );
		if ( 0 === $subtract ) {
			--$highest_priority;
		} else {
			$highest_priority = absint( $highest_priority - $subtract );
		}
		return $highest_priority;
	}
}

