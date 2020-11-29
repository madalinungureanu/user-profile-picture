<?php
/**
 * Register the Settings tab and any sub-tabs.
 *
 * @package user-profile-picture
 */

namespace MPP\Includes\Admin\Tabs;

use MPP\Includes\Functions as Functions;
use MPP\Includes\Options as Options;

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
				if ( isset( $_POST['submit'] ) && isset( $_POST['options'] ) ) {
					check_admin_referer( 'save_mpp_options' );
					$options = wp_unslash( $_POST['options'] ); // phpcs:ignore
					Options::update_options( $options );
					printf( '<div class="updated"><p><strong>%s</strong></p></div>', esc_html__( 'Your options have been saved.', 'metronet-profile-picture' ) );
				}
				// Get options and defaults.
				$options = Options::get_options();
				?>
				<form action="<?php echo esc_url( Functions::get_settings_url( 'settings' ) ); ?>" method="POST">
					<?php wp_nonce_field( 'save_mpp_options' ); ?>
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
				<?php
			}
		}
	}
}
