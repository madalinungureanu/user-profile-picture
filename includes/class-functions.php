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

