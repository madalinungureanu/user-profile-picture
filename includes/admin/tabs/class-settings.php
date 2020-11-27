<?php
/**
 * Register the Settings tab and any sub-tabs.
 *
 * @package user-profile-picture
 */

namespace MPP\Includes\Admin\Tabs;

use MPP\Includes\Functions as Functions;

/**
 * Output the settings tab and content.
 */
class Settings {
	/**
	 * Constructor.
	 */
	public function __construct() {
		add_filter( 'mpp_admin_tabs', array( $this, 'add_settings_tab' ), 1, 1 );
		add_filter( 'mpp_admin_sub_tabs', array( $this, 'add_settings_main_sub_tab' ), 1, 3 );
		add_filter( 'mpp_output_settings', array( $this, 'output_settings_content' ), 1, 3 );
	}

	/**
	 * Add the settings tab and callback actions.
	 *
	 * @param array $tabs Array of tabs.
	 *
	 * @return array of tabs.
	 */
	public function add_settings_tab( $tabs ) {
		$tabs[] = array(
			'get'    => 'settings',
			'action' => 'mpp_output_settings',
			'url'    => Functions::get_settings_url( 'settings' ),
			'label'  => _x( 'Settings', 'Tab label as settings', 'metronet-profile-picture' ),
			'icon'   => 'home-heart',
		);
		return $tabs;
	}

	/**
	 * Add the settings main tab and callback actions.
	 *
	 * @param array  $tabs        Array of tabs.
	 * @param string $current_tab The current tab selected.
	 * @param string $sub_tab     The current sub-tab selected.
	 *
	 * @return array of tabs.
	 */
	public function add_settings_main_sub_tab( $tabs, $current_tab, $sub_tab ) {
		if ( ( ! empty( $current_tab ) || ! empty( $sub_tab ) ) && 'settings' !== $current_tab ) {
			return $tabs;
		}
		return $tabs;
	}

	/**
	 * Begin settings routing for the various outputs.
	 */
	public function output_settings_content( $tab, $sub_tab = '' ) {
		if ( 'settings' === $tab ) {
			if ( empty( $sub_tab ) || 'settings' === $sub_tab ) {
				?>
				<div id="mpp-settings-options"></div>
				<?php
			}
		}
	}
}
