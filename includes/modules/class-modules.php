<?php
/**
 * Module loader.
 *
 * @package user-profile-picture
 */

namespace MPP\Includes\Modules;

use MPP\Includes\Functions as Functions;
use MPP\Includes\Options as Options;

/**
 * Initialize modules.
 */
class Modules {
	/**
	 * Class constructor and module loader.
	 */
	public function __construct() {
		$options = Options::get_options();
		if ( 'on' === $options['hide_wordpress_avatar_section'] ) {
			new \MPP\Includes\Modules\Hide_WP_Avatar();
		}
	}
}
