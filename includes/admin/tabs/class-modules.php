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
	 *
	 * @param string $tab     Current tab.
	 * @param string $sub_tab Current Sub-tab.
	 */
	public function output_modules_content( $tab, $sub_tab = '' ) {
		if ( 'modules' === $tab ) {
			if ( empty( $sub_tab ) || 'modules' === $sub_tab ) {
				if ( isset( $_POST['submit'] ) && isset( $_POST['options'] ) ) {
					check_admin_referer( 'save_mpp_module_options' );
					$options = wp_unslash( $_POST['options'] ); // phpcs:ignore
					Options::update_options( $options );
					printf( '<div class="updated"><p><strong>%s</strong></p></div>', esc_html__( 'Your options have been saved.', 'metronet-profile-picture' ) );
				}
				// Get options and defaults.
				$options = Options::get_options();
				?>
				<form action="<?php echo esc_url( Functions::get_settings_url( 'modules' ) ); ?>" method="POST">
					<?php wp_nonce_field( 'save_mpp_module_options' ); ?>
					<table class="form-table">
						<tbody>
							<tr>
								<th scope="row"><?php esc_html_e( 'Hide WordPress Avatar Section', 'metronet-profile-picture' ); ?></th>
								<td>
									<input type="hidden" name="options[hide_wordpress_avatar_section]" value="off" />
									<input id="mpp-hide-wordpress-avatar-section" type="checkbox" value="on" name="options[hide_wordpress_avatar_section]" <?php checked( 'on', $options['hide_wordpress_avatar_section'] ); ?> /> <label for="mpp-hide-wordpress-avatar-section"><?php esc_html_e( 'Hide WordPress Avatar Section', 'metronet-profile-picture' ); ?></label>
									<p class="description"><?php esc_html_e( 'Select this option to hide the WordPress Avatar section on the user edit screen.' ); ?></p>
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
									<input id="mpp-custom-cropping" type="checkbox" value="on" name="options[custom_cropping]" <?php checked( 'on', $options['custom_cropping'] ); ?> /> <label for="mpp-custom-cropping"><?php esc_html_e( 'Custom Cropping.', 'metronet-profile-picture' ); ?></label>
									<p class="description"><?php esc_html_e( 'Select this option to enable the custom cropping module.' ); ?></p>
								</td>
							</tr>
							<tr>
								<th scope="row"><?php esc_html_e( 'Enable Upload on User List', 'metronet-profile-picture' ); ?></th>
								<td>
									<input type="hidden" name="options[change_profile_user_list]" value="off" />
									<input id="mpp-upload-user-list" type="checkbox" value="on" name="options[change_profile_user_list]" <?php checked( 'on', $options['change_profile_user_list'] ); ?> /> <label for="mpp-upload-user-list"><?php esc_html_e( 'Upload Profiles on the User List Screen', 'metronet-profile-picture' ); ?></label>
									<p class="description"><?php esc_html_e( 'Select this option to enable profile uploading from the user list.' ); ?></p>
								</td>
							</tr>
							<tr>
								<th scope="row"><?php esc_html_e( 'Multiple Profile Images', 'metronet-profile-picture' ); ?></th>
								<td>
									<input type="hidden" name="options[multiple_profile_images]" value="off" />
									<input id="mpp-multiple-profile-images" disabled="disabled" type="checkbox" value="off" name="options[multiple_profile_images]" <?php checked( 'on', $options['multiple_profile_images'] ); ?> /> <label for="mpp-multiple-profile-images"><?php esc_html_e( 'Enable Multiple Profile Images', 'metronet-profile-picture' ); ?></label>
									<p class="description"><?php esc_html_e( 'Allow your users to upload multiple profile images.', 'metronet-profile-picture' ); ?> | <a href="https://github.com/madalinungureanu/user-profile-picture/issues/24" target="_blank">Vote and comment on this feature.</a></p>
								</td>
							</tr>
							<tr>
								<th scope="row"><?php esc_html_e( 'Add Avatar for Post Type Columns', 'metronet-profile-picture' ); ?></th>
								<td>
									<input type="hidden" name="options[show_author_avatar_column_post_types]" value="off" />
									<input id="mpp-author-post-type-columns" disabled="disabled" type="checkbox" value="off" name="options[show_author_avatar_column_post_types]" <?php checked( 'on', $options['show_author_avatar_column_post_types'] ); ?> /> <label for="mpp-author-post-type-columns"><?php esc_html_e( 'Avatars in Post Type Columns', 'metronet-profile-picture' ); ?></label>
									<p class="description"><?php esc_html_e( 'Show and upload an avatar from Post Type Columns', 'metronet-profile-picture' ); ?> | <a href="https://github.com/madalinungureanu/user-profile-picture/issues/27" target="_blank">Vote and comment on this feature.</a></p>
								</td>
							</tr>
							<tr>
								<th scope="row"><?php esc_html_e( 'Front-end Profile Set', 'metronet-profile-picture' ); ?></th>
								<td>
									<input type="hidden" name="options[front_end_upload_module]" value="off" />
									<input id="mpp-frontend-upload-module" disabled="disabled" type="checkbox" value="off" name="options[front_end_upload_module]" <?php checked( 'on', $options['front_end_upload_module'] ); ?> /> <label for="mpp-frontend-upload-module"><?php esc_html_e( 'Front-end Upload Integration', 'metronet-profile-picture' ); ?></label>
									<p class="description"><?php esc_html_e( 'Integrate with popular profile edit plugins.', 'metronet-profile-picture' ); ?> | <a href="https://github.com/madalinungureanu/user-profile-picture/issues/23" target="_blank">Vote and comment on this feature.</a></p>
								</td>
							</tr>
							<tr>
								<th scope="row"><?php esc_html_e( 'Team Member Block', 'metronet-profile-picture' ); ?></th>
								<td>
									<input type="hidden" name="options[custom_team_member_module]" value="off" />
									<input id="mpp-custom-team-block" disabled="disabled" type="checkbox" value="off" name="options[custom_team_member_module]" <?php checked( 'on', $options['custom_team_member_module'] ); ?> /> <label for="mpp-custom-team-block"><?php esc_html_e( 'Team Member Block', 'metronet-profile-picture' ); ?></label>
									<p class="description"><?php esc_html_e( 'Add user profile fields and create a block to showcase a team member.', 'metronet-profile-picture' ); ?> | <a href="https://github.com/madalinungureanu/user-profile-picture/issues/22" target="_blank">Vote and comment on this feature.</a></p>
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
