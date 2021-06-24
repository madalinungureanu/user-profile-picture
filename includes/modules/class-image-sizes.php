<?php
/**
 * Customize WP Image Sizes.
 *
 * @package user-profile-picture
 */

namespace MPP\Includes\Modules;

use MPP\Includes\Functions as Functions;

/**
 * Hide the WP avatar section on the profile page.
 */
class Image_Sizes {
	/**
	 * Constructor.
	 */
	public function __construct() {
		add_filter( 'mpp_admin_tabs', array( $this, 'add_image_sizes_tab' ), 1, 1 );
		add_filter( 'mpp_admin_sub_tabs', array( $this, 'add_image_sizes_main_sub_tab' ), 1, 3 );
		add_filter( 'mpp_output_image_sizes', array( $this, 'output_image_sizes_content' ), 1, 3 );
	}

	/**
	 * Add the image sizes tab and callback actions.
	 *
	 * @param array $tabs Array of tabs.
	 *
	 * @return array of tabs.
	 */
	public function add_image_sizes_tab( $tabs ) {
		$tabs[] = array(
			'get'    => 'image_sizes',
			'action' => 'mpp_output_image_sizes',
			'url'    => Functions::get_settings_url( 'image_sizes' ),
			'label'  => _x( 'Image Sizes', 'Tab label as image sizes', 'metronet-profile-picture' ),
			'icon'   => 'home-heart',
		);
		return $tabs;
	}

	/**
	 * Add the image sizes tab.
	 *
	 * @param array  $tabs        Array of tabs.
	 * @param string $current_tab The current tab selected.
	 * @param string $sub_tab     The current sub-tab selected.
	 *
	 * @return array of tabs.
	 */
	public function add_image_sizes_main_sub_tab( $tabs, $current_tab, $sub_tab ) {
		if ( ( ! empty( $current_tab ) || ! empty( $sub_tab ) ) && 'image_sizes' !== $current_tab ) {
			return $tabs;
		}
		return $tabs;
	}

	/**
	 * Get options for the image sizes.
	 */
	private function get_saved_image_sizes() {
		$saved_image_sizes = get_option( 'mpp_image_sizes', array() );
		if ( empty( $saved_image_sizes ) || ! is_array( $saved_image_sizes ) ) {
			$saved_image_sizes = $this->get_image_sizes();
			if ( is_array( $saved_image_sizes ) && ! empty( $saved_image_sizes ) ) {
				update_option( 'mpp_image_sizes', $saved_image_sizes );
			} else {
				$saved_image_sizes = array();
			}
		}
		return $saved_image_sizes;
	}

	/**
	 * Get built-in extra image sizes.
	 */
	private function get_image_sizes() {
		$image_sizes = wp_get_additional_image_sizes();
		return $image_sizes;
	}

	/**
	 * Begin Image sizes output.
	 *
	 * @param string $tab     Current tab.
	 * @param string $sub_tab Current Sub-tab.
	 */
	public function output_image_sizes_content( $tab, $sub_tab = '' ) {
		if ( 'image_sizes' === $tab ) {
			if ( empty( $sub_tab ) || 'image_sizes' === $sub_tab ) {
				if ( isset( $_POST['submit'] ) && isset( $_POST['options'] ) ) {
					check_admin_referer( 'save_mpp_module_options' );
					$options = wp_unslash( $_POST['options'] ); // phpcs:ignore
					printf( '<div class="updated"><p><strong>%s</strong></p></div>', esc_html__( 'Your options have been saved.', 'metronet-profile-picture' ) );
				}
				wp_register_style(
					'mpp-image-sizes',
					Functions::get_plugin_url( '/dist/image-sizes.css' ),
					array(),
					Functions::get_plugin_version(),
					'all'
				);
				wp_print_styles( array( 'mpp-image-sizes' ) );
				wp_enqueue_script(
					'mpp-image-sizes',
					Functions::get_plugin_url( '/dist/image-sizes-js.js' ),
					array( 'jquery' ),
					Functions::get_plugin_version(),
					true
				);
				Functions::output_svg_sprite();
				// Get options and defaults.
				$image_sizes = $this->get_saved_image_sizes();
				?>
				<div class="mpp-section-header">
					<h2 class="mpp-heading icon icon-image-sizes"><?php esc_html_e( 'Image Sizes', 'metronet-profile-picture' ); ?></h2>
					<p class="description"><?php printf( __( 'We recommend %s if you make any changes to the image sizes.', 'metronet-profile-picture' ), '<a href="https://wordpress.org/plugins/regenerate-thumbnails/">Regenerate Thumbnails</a>' ); // phpcs:ignore ?></p>
				</div>
				<div class="mpp-option-body">
					<div id="mpp-image-sizes-table">
						<?php
						$count = 0;
						?>
						<table class="widefat mpp-table" id="mpp-image-sizes-table">
							<thead>
								<tr>
									<th><?php esc_html_e( 'Image Size Name', 'metronet-profile-picture' ); ?></th>
									<th><?php esc_html_e( 'Image Width', 'metronet-profile-picture' ); ?></th>
									<th><?php esc_html_e( 'Image Height', 'metronet-profile-picture' ); ?></th>
									<th><?php esc_html_e( 'Action', 'metronet-profile-picture' ); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php
								foreach ( $image_sizes as $name => $size_data ) {
									?>
									<tr class="<?php echo esc_attr( ( 0 === ( $count % 2 ) ) ? 'even' : 'odd' ); ?> mpp-image-size-row">
										<td>
											<span><?php echo esc_attr( $name ); ?></span>
											<input data-slug="<?php echo esc_attr( sanitize_title( $name ) ); ?>" class="mpp-image-size-table-name" type="hidden" value="<?php echo esc_attr( sanitize_title( $name ) ); ?>" />
										</td>
										<td>
											<span><?php echo absint( $size_data['width'] ); ?></span>
											<input class="mpp-image-size-table-width" type="hidden" value="<?php echo absint( $size_data['width'] ); ?>" />
										</td>
										<td>
											<span><?php echo absint( $size_data['height'] ); ?></span>
											<input class="mpp-image-size-table-height" type="hidden" value="<?php echo esc_attr( $size_data['height'] ); ?>" />
										</td>
										<td>
											<a href="#" data-nonce="<?php echo esc_attr( wp_create_nonce( 'mpp_edit_size-' . sanitize_title( $name ) ) ); ?>" class="mpp-button mpp-button-info mpp-image-size-edit" data-name="<?php echo esc_attr( $name ); ?>" data-width="<?php echo absint( $size_data['width'] ); ?>" data-height="<?php echo absint( $size_data['height'] ); ?>"><svg viewBox="0 0 100 100" class="mpp-icon" aria-hidden="true"><use xlink:href="#mpp-pencil-duotone"></use></svg> <?php esc_html_e( 'Edit', 'metronet-profile-picture' ); ?></a>
											<a href="#" data-nonce="<?php echo esc_attr( wp_create_nonce( 'mpp_delete_size-' . sanitize_title( $name ) ) ); ?>" class="mpp-button mpp-button-delete mpp-image-size-delete" data-name="<?php echo esc_attr( $name ); ?>" data-width="<?php echo absint( $size_data['width'] ); ?>" data-height="<?php echo absint( $size_data['height'] ); ?>"><svg viewBox="0 0 100 100" class="mpp-icon" aria-hidden="true"><use xlink:href="#mpp-trash-alt-duotone"></use></svg> <?php esc_html_e( 'Delete', 'metronet-profile-picture' ); ?></a>
											<a href="#" data-nonce="<?php echo esc_attr( wp_create_nonce( 'mpp_save_size-' . sanitize_title( $name ) ) ); ?>" class="mpp-button mpp-button-save mpp-image-size-save" style="display: none;"><svg viewBox="0 0 100 100" class="mpp-icon" aria-hidden="true"><use xlink:href="#mpp-save-duotone"></use></svg> <?php esc_html_e( 'Save', 'metronet-profile-picture' ); ?></a>
											<a href="#" class="mpp-button mpp-button-secondary mpp-button-cancel mpp-image-size-cancel" style="display: none;"><svg viewBox="0 0 100 100" class="mpp-icon" aria-hidden="true"><use xlink:href="#mpp-undo-duotone"></use></svg> <?php esc_html_e( 'Cancel', 'metronet-profile-picture' ); ?></a>
										</td>
									</tr>
									<?php
									$count++;
								}
								?>
							</tbody>
							<tfoot>
								<tr>
									<th><?php esc_html_e( 'Image Size Name', 'metronet-profile-picture' ); ?></th>
									<th><?php esc_html_e( 'Image Width', 'metronet-profile-picture' ); ?></th>
									<th><?php esc_html_e( 'Image Height', 'metronet-profile-picture' ); ?></th>
									<th><?php esc_html_e( 'Action', 'metronet-profile-picture' ); ?></th>
								</tr>
							</tfoot>
						</table>
					</div>
					<fieldset class="mpp-option-fieldset" id="mpp-image-sizes-wrapper">
						<div class="mpp-field mpp-field--input">
							<label for="mpp-field-license-input">
								<?php
								esc_html_e( 'Image Size Name', 'metronet-profile-picture' );
								?>
							</label>
							<input id="mpp-field-image-sizes-input" class="mpp-field-input" type="text" name="mpp[image-sizes]" value="" placeholder="<?php echo esc_attr_e( 'Enter an image size name', 'metronet-profile-picture' ); ?>" />
						</div>
						<div class="mpp-field mpp-field--input">
							<label for="mpp-field-image-size-width-input">
								<?php
								esc_html_e( 'Image Width', 'metronet-profile-picture' );
								?>
							</label>
							<input id="mpp-field-image-size-width-input" class="mpp-field-input" type="number" name="mpp[image-sizes-width]" value="" placeholder="<?php echo absint( 640 ); ?>" />
						</div>
						<div class="mpp-field mpp-field--input">
							<label for="mpp-field-image-size-height-input">
								<?php
								esc_html_e( 'Image Height', 'metronet-profile-picture' );
								?>
							</label>
							<input id="mpp-field-image-size-height-input" class="mpp-field-input" type="number" name="mpp[image-sizes-height]" value="" placeholder="<?php echo absint( 800 ); ?>" />
						</div>
						<div class="mpp-field mpp-field--buttons">
							<button id="mpp-image-size-save" class="mpp-button mpp-button-save">
								<?php
								esc_html_e( 'Add Image Size', 'metronet-profile-picture' );
								?>
							</button>
						</div>
						<div class="mpp-field mpp-field--status mpp-status mpp-success image-size-status" style="display: none;">
						</div>
					</fieldset>
				</div>
				<?php
			}
		}
	}
}
