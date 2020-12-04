<?php
/**
 * Register the Modules tab and any sub-tabs.
 *
 * @package user-profile-picture
 */

namespace MPP\Includes\Admin\Tabs;

use MPP\Includes\Functions as Functions;
use MPP\Includes\Options as Options;

/**
 * Output the modules tab and content.
 */
class Modules {
	/**
	 * Constructor.
	 */
	public function __construct() {
		add_filter( 'mpp_admin_tabs', array( $this, 'add_modules_tab' ), 1, 1 );
		add_filter( 'mpp_admin_sub_tabs', array( $this, 'add_modules_main_sub_tab' ), 1, 3 );
		add_filter( 'mpp_output_modules', array( $this, 'output_modules_content' ), 1, 3 );
	}

	/**
	 * Add the modules tab and callback actions.
	 *
	 * @param array $tabs Array of tabs.
	 *
	 * @return array of tabs.
	 */
	public function add_modules_tab( $tabs ) {
		$tabs[] = array(
			'get'    => 'modules',
			'action' => 'mpp_output_modules',
			'url'    => Functions::get_settings_url( 'modules' ),
			'label'  => _x( 'Modules', 'Tab label as modules', 'metronet-profile-picture' ),
			'icon'   => 'home-heart',
		);
		return $tabs;
	}

	/**
	 * Add the modules main tab and callback actions.
	 *
	 * @param array  $tabs        Array of tabs.
	 * @param string $current_tab The current tab selected.
	 * @param string $sub_tab     The current sub-tab selected.
	 *
	 * @return array of tabs.
	 */
	public function add_modules_main_sub_tab( $tabs, $current_tab, $sub_tab ) {
		if ( ( ! empty( $current_tab ) || ! empty( $sub_tab ) ) && 'modules' !== $current_tab ) {
			return $tabs;
		}
		return $tabs;
	}

	/**
	 * Begin modules routing for the various outputs.
	 */
	public function output_modules_content( $tab, $sub_tab = '' ) {
		if ( 'modules' === $tab ) {
			if ( empty( $sub_tab ) || 'modules' === $sub_tab ) {
				// Get options and defaults.
				$options = Options::get_module_options();
				?>
				<form action="<?php echo esc_url( Functions::get_settings_url( 'modules' ) ); ?>" method="POST">
					<?php wp_nonce_field( 'save_mpp_module_options' ); ?>
					<table class="form-table">
						<tbody>
							<tr>
								<th scope="row"><?php esc_html_e( 'Social Networks', 'metronet-profile-picture' ); ?></th>
								<td>
									<input type="hidden" name="options[enable_social_networks]" value="off" />
									<input id="mpp-load-social-networks" type="checkbox" value="off" name="options[enable_social_networks]" <?php checked( 'off', $options['enable_social_networks'] ); ?> /> <label for="mpp-load-social-networks"><?php esc_html_e( 'Enable Social Networks', 'metronet-profile-picture' ); ?></label>
									<p class="description"><?php esc_html_e( 'Allow your users to set their social networks in their user profile', 'metronet-profile-picture' ); ?></p>
								</td>
							</tr>
							<tr>
								<th scope="row"><?php esc_html_e( 'Custom Image Sizes', 'metronet-profile-picture' ); ?></th>
								<td>
									<input type="hidden" name="options[custom_image_sizes]" value="off" />
									<input id="mpp-enable-image-sizes" type="checkbox" value="on" name="options[custom_image_sizes]" <?php checked( 'on', $options['custom_image_sizes'] ); ?> /> <label for="mpp-enable-image-sizes"><?php esc_html_e( 'Enable Custom Image Sizes', 'metronet-profile-picture' ); ?></label>
									<p class="description"><?php esc_html_e( 'Select this option to enable the custom image sizes module.' ); ?></p>
								</td>
							</tr>
							<tr>
								<th scope="row"><?php esc_html_e( 'Enable Custom Cropping', 'metronet-profile-picture' ); ?></th>
								<td>
									<input type="hidden" name="options[custom_cropping]" value="off" />
									<input id="mpp-custom-cropping" type="checkbox" value="on" name="options[custom_cropping" <?php checked( 'on', $options['custom_cropping'] ); ?> /> <label for="mpp-custom-cropping"><?php esc_html_e( 'Custom Cropping.', 'metronet-profile-picture' ); ?></label>
									<p class="description"><?php esc_html_e( 'Select this option to enable the custom cropping module.' ); ?></p>
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
					<?php submit_button( __( 'Save Module Options', 'metronet-profile-picture' ) ); ?>
				</form>
				<?php
			}
		}
	}
}
