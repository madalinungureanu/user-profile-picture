<?php
/**
 * Hide the WordPress Avatar section.
 *
 * @package user-profile-picture
 */

namespace MPP\Includes\Modules;

use MPP\Includes\Functions as Functions;

/**
 * Hide the WP avatar section on the profile page.
 */
class Hide_WP_Avatar {
	/**
	 * Class constructor.
	 */
	public function __construct() {
		add_action( 'admin_body_class', array( $this, 'add_profile_admin_class' ) );
	}

	/**
	 * Add a profile page admin class to hide the avatar section.
	 *
	 * @param string $classes String of admin classes.
	 *
	 * @return string admin classes.
	 */
	public function add_profile_admin_class( $classes ) {
		if ( Functions::is_user_profile_page() ) {
			$classes .= ' mpp_hide_wp_avatar';
		}
		return $classes;
	}
}
