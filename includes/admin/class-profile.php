<?php
/**
 * Profile admin markup and functions for the plugin.
 *
 * @package user-profile-picture
 */

namespace MPP\Includes\Admin;

use MPP\Includes\Functions as Functions;
use MPP\Includes\Options as Options;

/**
 * Class Setup
 */
class Profile {

	/**
	 * Class Constructor.
	 */
	public function __construct() {
		add_action( 'personal_options', array( $this, 'insert_upload_form' ) );

		// Scripts.
		add_action( 'admin_enqueue_scripts', array( $this, 'print_admin_scripts' ) );
		add_action( 'admin_print_scripts-user-edit.php', array( $this, 'print_media_scripts' ) );
		add_action( 'admin_print_scripts-profile.php', array( $this, 'print_media_scripts' ) );

		add_action( 'wp_enqueue_scripts', array( $this, 'profile_print_media_scripts' ), 9 );
		add_action( 'acf/input/admin_enqueue_scripts', array( $this, 'profile_print_media_scripts' ), 9 );
	}

	/**
	 * Adds an upload form to the user profile page and outputs profile image if there is one
	 */
	public function insert_upload_form() {
		if ( ! current_user_can( 'upload_files' ) ) {
			return; // Users must be author or greater.
		}

		$user_id = Functions::get_user_id();
		$post_id = Functions::get_post_id( $user_id );

		?>
		<tr valign="top">
			<th scope="row"><?php esc_html_e( 'Profile Image', 'metronet-profile-picture' ); ?></th>
			<td id="mpp">
				<input type="hidden" name="metronet_profile_id" id="metronet_profile_id" value="<?php echo esc_attr( $user_id ); ?>" />
				<input type="hidden" name="metronet_post_id" id="metronet_post_id" value="<?php echo esc_attr( $post_id ); ?>" />
				<div id="metronet-profile-image">
				<?php
				$has_profile_image = false;
				if ( has_post_thumbnail( $post_id ) ) {
					$has_profile_image = true;
					echo '<a style="display:block" href="#" class="mpp_add_media">';
					$thumb_src      = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'thumbnail', false, '' );
					$post_thumbnail = sprintf( '<img style="display:block" src="%s" width="150" height="150" title="%s" />', esc_url( $thumb_src[0] ), esc_attr__( 'Upload or Change Profile Picture', 'metronet-profile-picture' ) );
					echo wp_kses_post( $post_thumbnail );
					echo sprintf( '<div id="metronet-click-edit">%s</div>', esc_html__( 'Click to Edit', 'metronet-profile-picture' ) );
					echo '</a>';
				} else {
					echo '<a style="display:block" href="#" class="mpp_add_media default-image">';
					$post_thumbnail = sprintf( '<img style="display:block" src="%s" width="150" height="150" title="%s" />', Functions::get_plugin_url( 'img/mystery.png' ), esc_attr__( 'Upload or Change Profile Picture', 'metronet-profile-picture' ) );
					echo wp_kses_post( $post_thumbnail );
					echo sprintf( '<div id="metronet-click-edit">%s</div>', esc_html__( 'Click to Edit', 'metronet-profile-picture' ) );
					echo '</a>';
				}
				$remove_classes = array( 'dashicons', 'dashicons-trash' );
				if ( ! $has_profile_image ) {
					$remove_classes[] = 'mpp-no-profile-image';
				}
				?>
					<a id="metronet-remove" class="<?php echo implode( ' ', $remove_classes ); // phpcs:ignore ?>" href="#" title="<?php esc_attr_e( 'Remove profile image', 'metronet-profile-picture' ); ?>"><?php esc_html_e( 'Remove profile image', 'metronet-profile-picture' ); ?></a>
					<div style="display: none">
						<?php printf( '<img class="mpp-loading" width="150" height="150" alt="Loading" src="%s" />', esc_url( Functions::get_plugin_url( '/img/loading.gif' ) ) ); ?>
					</div>
				</div><!-- #metronet-profile-image -->
				<div id="metronet-override-avatar">
					<input type="hidden" name="metronet-user-avatar" value="off" />
					<?php
					// Get the user avatar override option - If not set, see if there's a filter override.
					$user_avatar_override = get_user_option( 'metronet_avatar_override', $user_id );
					$checked              = '';
					if ( $user_avatar_override ) {
						$checked = checked( 'on', $user_avatar_override, false );
					} else {
						$checked = checked( true, apply_filters( 'mpp_avatar_override', false ), false );
					}

					// Filter for hiding the override interface.  If this option is set to true, the mpp_avatar_override filter is ignored and override is enabled by default.
					$hide_override = apply_filters( 'mpp_hide_avatar_override', true );
					if ( $hide_override ) :
						?>
						<input type="hidden" name="metronet-user-avatar" id="metronet-user-avatar" value="on"  />
						<?php
						else :
							?>
							<br /><input type="checkbox" name="metronet-user-avatar" id="metronet-user-avatar" value="on" <?php echo $checked; // phpcs:ignore ?> /> <label for="metronet-user-avatar"><?php esc_html_e( 'Override Avatar?', 'metronet-profile-picture' ); ?></label>
						<?php endif; ?>
				</div><!-- #metronet-override-avatar -->
			</td>
		</tr>
		<?php
		/**
		 * Allow other plugins to run code after the user profile picture UI.
		 *
		 * @since 2.3.0
		 */
		do_action( 'mpp_user_profile_form', $user_id );
	} //end insert_upload_form

	/**
	 * Output media scripts for thickbox and media uploader
	 **/
	public function profile_print_media_scripts() {
		if ( defined( 'IS_PROFILE_PAGE' ) && IS_PROFILE_PAGE === true ) {
			$this->print_media_scripts();
		}
	}

	/**
	 * Print admin scripts and styles.
	 *
	 * @since 2.5.0
	 */
	public function print_admin_scripts() {
		$screen = get_current_screen();
		if ( 'users_page_mpp' === $screen->id ) {
			wp_enqueue_script(
				'mpp-admin-script',
				Functions::get_plugin_url( '/dist/admin-panel.js' ),
				array( 'jquery' ),
				Functions::get_plugin_version(),
				true
			);
			wp_enqueue_style(
				'mpp-admin-styles',
				Functions::get_plugin_url( '/dist/admin.css' ),
				array(),
				Functions::get_plugin_version(),
				'all'
			);
		}
	}

	/**
	 * Output media scripts for thickbox and media uploader
	 **/
	public function print_media_scripts() {
		$post_id = Functions::get_post_id( Functions::get_user_id() );
		wp_enqueue_media();
		$script_deps = array( 'media-editor' );
		wp_enqueue_script( 'mt-pp', Functions::get_plugin_url( '/js/mpp.js' ), $script_deps, Functions::get_plugin_version(), true );
		wp_localize_script(
			'mt-pp',
			'metronet_profile_image',
			array(
				'set_profile_text'    => __( 'Set Profile Image', 'metronet-profile-picture' ),
				'remove_profile_text' => __( 'Remove Profile Image', 'metronet-profile-picture' ),
				'crop'                => __( 'Crop Thumbnail', 'metronet-profile-picture' ),
				'ajax_url'            => esc_url( admin_url( 'admin-ajax.php' ) ),
				'user_post_id'        => absint( $post_id ),
				'nonce'               => wp_create_nonce( 'mt-update-post_' . absint( $post_id ) ),
				'loading_gif'         => esc_url( Functions::get_plugin_url( '/img/loading.gif' ) ),
			)
		);
		wp_enqueue_style(
			'mpp-profile-picture',
			Functions::get_plugin_url( '/dist/profile-picture.css' ),
			array( 'dashicons' ),
			Functions::get_plugin_version(),
			'all'
		);
	}
}
