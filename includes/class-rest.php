<?php
/**
 * Rest actions for the plugin.
 *
 * @package user-profile-picture
 */

namespace MPP\Includes;

/**
 * Class Rest
 */
class Rest {

	/**
	 * Register Ajax actions.
	 */
	public function __construct() {
		// Rest API.
		add_action( 'rest_api_init', array( $this, 'rest_api_register' ) );
	}

	/**
	 * Gets permissions for the get users rest api endpoint.
	 *
	 * @return bool true if the user has permission, false if not
	 **/
	public function rest_get_users_permissions_callback() {
		return current_user_can( 'upload_files' );
	}

	/**
	 * Registers REST API endpoints
	 */
	public function rest_api_register() {
		register_rest_field(
			'user',
			'mpp_avatar',
			array(
				'get_callback' => array( $this, 'rest_api_get_profile_for_user' ),
			)
		);
		register_rest_route(
			'mpp/v2',
			'/profile-image/me',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'rest_api_put_profile' ),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			'mpp/v2',
			'/profile-image/change',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'rest_api_change_profile_image' ),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			'mpp/v2',
			'/get_users',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'rest_api_get_users' ),
				'permission_callback' => array( $this, 'rest_get_users_permissions_callback' ),
			)
		);
		register_rest_route(
			'mpp/v2',
			'/get_posts',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'rest_api_get_posts_for_user' ),
				'permission_callback' => array( $this, 'rest_get_users_permissions_callback' ),
			)
		);
		// keep it for backward compatibility.
		register_rest_route(
			'mpp/v1',
			'/user/(?P<id>\d+)',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'rest_api_get_profile' ),
				'permission_callback' => '__return_true',
				'args'                => array(
					'id' => array(
						'validate_callback' => array( $this, 'rest_api_validate' ),
						'sanitize_callback' => array( $this, 'rest_api_sanitize' ),
					),
				),
			)
		);
	}

	/**
	 * Gets users for the Gutenberg block
	 *
	 * @param array $request WP REST API array.
	 *
	 * @return array A list of users.
	 **/
	public function rest_api_get_users( $request ) {

		/**
		 * Filter the capability types of users.
		 *
		 * @since 2.1.3
		 *
		 * @param string User role for users
		 */
		$capabilities = apply_filters( 'mpp_gutenberg_user_role', 'authors' );
		$user_query   = new WP_User_Query(
			array(
				'who'     => $capabilities,
				'orderby' => 'display_name',
			)
		);
		$user_results = $user_query->get_results();
		$return       = array();
		foreach ( $user_results as $result ) {
			// Get attachment ID.
			$profile_post_id   = absint( get_user_option( 'metronet_post_id', $result->data->ID ) );
			$post_thumbnail_id = get_post_thumbnail_id( $profile_post_id );
			if ( ! $post_thumbnail_id ) {
				$result->data->has_profile_picture = false;
				$result->data->profile_picture_id  = 0;
				$result->data->default_image       = self::get_plugin_url( 'img/mystery.png' );
				$result->data->profile_pictures    = array(
					'avatar' => get_avatar( $result->data->ID ),
				);
				$result->data->is_user_logged_in   = ( get_current_user_id() == $result->data->ID ) ? true : false; // phpcs:ignore
				$return[ $result->data->ID ]       = $result->data;
				continue;
			}
			$result->data->description         = get_user_meta( $result->data->ID, 'description', true );
			$result->data->display_name        = $result->data->display_name;
			$result->data->has_profile_picture = true;
			$result->data->is_user_logged_in   = ( get_current_user_id() == $result->data->ID ) ? true : false; // phpcs:ignore
			$result->data->description         = get_user_meta( $result->data->ID, 'description', true );

			// Get attachment URL.
			$attachment_url = wp_get_attachment_url( $post_thumbnail_id );

			$result->data->profile_picture_id = $post_thumbnail_id;
			$result->data->default_image      = self::get_plugin_url( 'img/mystery.png' );
			$result->data->profile_pictures   = array(
				'24'        => wp_get_attachment_image_url( $post_thumbnail_id, 'profile_24', false, '' ),
				'48'        => wp_get_attachment_image_url( $post_thumbnail_id, 'profile_48', false, '' ),
				'96'        => wp_get_attachment_image_url( $post_thumbnail_id, 'profile_96', false, '' ),
				'150'       => wp_get_attachment_image_url( $post_thumbnail_id, 'profile_150', false, '' ),
				'300'       => wp_get_attachment_image_url( $post_thumbnail_id, 'profile_300', false, '' ),
				'thumbnail' => wp_get_attachment_image_url( $post_thumbnail_id, 'thumbnail', false, '' ),
				'avatar'    => get_avatar( $result->data->ID ),
				'full'      => $attachment_url,
			);
			$result->data->permalink          = get_author_posts_url( $result->data->ID );
			$return[ $result->data->ID ]      = $result->data;
		}
		return $return;
	}

	/**
	 * Changes a profile image for a user.
	 *
	 * @param array $request WP REST API array.
	 *
	 * @return array image URLs matched to sizes
	 **/
	public function rest_api_change_profile_image( $request ) {

		$user_id  = (int) $request['user_id'];
		$media_id = (int) $request['media_id'];

		if ( ! $user_id ) {
			return new WP_Error( 'mpp_no_user', __( 'User not found.', 'metronet-profile-picture' ), array( 'status' => 403 ) );
		}

		if ( ! current_user_can( 'upload_files', $user_id ) ) {
			return new WP_Error( 'mpp_insufficient_privs', __( 'You must be able to upload files.', 'metronet-profile-picture' ), array( 'status' => 403 ) );
		}

		$post_id = $this->get_post_id( $user_id );

		// Save user meta.
		update_user_option( $user_id, 'metronet_post_id', $post_id );
		update_user_option( $user_id, 'metronet_image_id', $media_id ); // Added via this thread (Props Solinx) - https://wordpress.org/support/topic/storing-image-id-directly-as-user-meta-data.

		set_post_thumbnail( $post_id, $media_id );

		$attachment_url = wp_get_attachment_url( $media_id );

		return array(
			'24'        => wp_get_attachment_image_url( $media_id, 'profile_24', false, '' ),
			'48'        => wp_get_attachment_image_url( $media_id, 'profile_48', false, '' ),
			'96'        => wp_get_attachment_image_url( $media_id, 'profile_96', false, '' ),
			'150'       => wp_get_attachment_image_url( $media_id, 'profile_150', false, '' ),
			'300'       => wp_get_attachment_image_url( $media_id, 'profile_300', false, '' ),
			'thumbnail' => wp_get_attachment_image_url( $media_id, 'thumbnail', false, '' ),
			'full'      => $attachment_url,
		);
	}

	/**
	 * Adds a profile picture to a user
	 *
	 * @param array $request WP REST API array.
	 *
	 * @return array image URLs matched to sizes
	 **/
	public function rest_api_put_profile( $request ) {

		$user_id  = get_current_user_id();
		$media_id = (int) $request['media_id'];
		if ( ! current_user_can( 'upload_files' ) ) {
			return new WP_Error( 'mpp_insufficient_privs', __( 'You must be able to upload files.', 'metronet-profile-picture' ), array( 'status' => 403 ) );
		}

		if ( ! $user_id ) {
			return new WP_Error( 'mpp_no_user', __( 'User not found.', 'metronet-profile-picture' ), array( 'status' => 403 ) );
		}
		if ( ! current_user_can( 'edit_others_posts', $user_id ) ) {
			return new WP_Error( 'mpp_not_privs', __( 'You must have a role of editor or above to set a new profile image.', 'metronet-profile-picture' ), array( 'status' => 403 ) );
		}
		$is_post_owner = ( get_post( $media_id )->post_author === $user_id ) ? true : false;
		if ( ! $is_post_owner && ! current_user_can( 'edit_others_posts', $user_id ) ) {
			return new WP_Error( 'mpp_not_owner', __( 'User not owner.', 'metronet-profile-picture' ), array( 'status' => 403 ) );
		}

		$post_id = $this->get_post_id( $user_id );
		// Save user meta.
		update_user_option( $user_id, 'metronet_post_id', $post_id );
		update_user_option( $user_id, 'metronet_image_id', $media_id ); // Added via this thread (Props Solinx) - https://wordpress.org/support/topic/storing-image-id-directly-as-user-meta-data.

		set_post_thumbnail( $post_id, $media_id );

		$attachment_url = wp_get_attachment_url( $media_id );

		return array(
			'24'        => wp_get_attachment_image_url( $media_id, 'profile_24', false, '' ),
			'48'        => wp_get_attachment_image_url( $media_id, 'profile_48', false, '' ),
			'96'        => wp_get_attachment_image_url( $media_id, 'profile_96', false, '' ),
			'150'       => wp_get_attachment_image_url( $media_id, 'profile_150', false, '' ),
			'300'       => wp_get_attachment_image_url( $media_id, 'profile_300', false, '' ),
			'thumbnail' => wp_get_attachment_image_url( $media_id, 'thumbnail', false, '' ),
			'full'      => $attachment_url,
		);
	}

	/**
	 * Returns the 5 most recent posts for the user
	 *
	 * @param array $request The REST Request data.
	 **/
	public function rest_api_get_posts_for_user( $request ) {
		$user_id = absint( $request['user_id'] );
		$user    = get_user_by( 'id', $user_id );
		if ( ! $user ) {
			return new WP_Error( 'mpp_no_user', __( 'User not found.', 'metronet-profile-picture' ), array( 'status' => 404 ) );
		}

		$args = array(
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'author'         => $user_id,
			'orderby'        => 'date',
			'order'          => 'DESC',
			'posts_per_page' => 5,
		);

		$posts = get_posts( $args );
		foreach ( $posts as &$post ) {
			$post->permalink = get_permalink( $post->ID );
		}
		wp_send_json( $posts );
	}
	/**
	 * Returns an attachment image ID and profile image if available
	 *
	 * @param array  $object REST object.
	 * @param string $field_name The field to update.
	 * @param array  $request The request made.
	 **/
	public function rest_api_get_profile_for_user( $object, $field_name, $request ) {
		$user_id = $object['id'];
		$user    = get_user_by( 'id', $user_id );
		if ( ! $user ) {
			return new WP_Error( 'mpp_no_user', __( 'User not found.', 'metronet-profile-picture' ), array( 'status' => 404 ) );
		}

		// No capability check here because we're just returning user profile data.

		// Get attachment ID.
		$profile_post_id   = absint( get_user_option( 'metronet_post_id', $user_id ) );
		$post_thumbnail_id = get_post_thumbnail_id( $profile_post_id );
		if ( ! $post_thumbnail_id ) {
			return new WP_Error( 'mpp_no_profile_picture', __( 'Profile picture not found.', 'metronet-profile-picture' ), array( 'status' => 404 ) );
		}

		// Get attachment URL.
		$attachment_url = wp_get_attachment_url( $post_thumbnail_id );

		return array(
			'24'   => wp_get_attachment_image_url( $post_thumbnail_id, 'profile_24', false, '' ),
			'48'   => wp_get_attachment_image_url( $post_thumbnail_id, 'profile_48', false, '' ),
			'96'   => wp_get_attachment_image_url( $post_thumbnail_id, 'profile_96', false, '' ),
			'150'  => wp_get_attachment_image_url( $post_thumbnail_id, 'profile_150', false, '' ),
			'300'  => wp_get_attachment_image_url( $post_thumbnail_id, 'profile_300', false, '' ),
			'full' => $attachment_url,
		);
	}

	/**
	 * Returns a profile for the user
	 *
	 * @param array $data WP REST API array.
	 *
	 * @return json image URLs matched to sizes
	 **/
	public function rest_api_get_profile( $data ) {
		$user_id = $data['id'];
		$user    = get_user_by( 'id', $user_id );
		if ( ! $user ) {
			return new WP_Error( 'mpp_no_user', __( 'User not found.', 'metronet-profile-picture' ), array( 'status' => 404 ) );
		}

		// Get attachment ID.
		$profile_post_id   = absint( get_user_option( 'metronet_post_id', $user_id ) );
		$post_thumbnail_id = get_post_thumbnail_id( $profile_post_id );
		if ( ! $post_thumbnail_id ) {
			return new WP_Error( 'mpp_no_profile_picture', __( 'Profile picture not found.', 'metronet-profile-picture' ), array( 'status' => 404 ) );
		}

		// Get attachment URL.
		$attachment_url = wp_get_attachment_url( $post_thumbnail_id );

		return array(
			'attachment_id'  => $post_thumbnail_id,
			'attachment_url' => $attachment_url,
		);
	}

	/**
	 * Makes sure the ID we are passed is numeric
	 *
	 * @param mixed $param   The paramater to validate.
	 * @param array $request The REST request.
	 * @param mixed $key     The key to check.
	 *
	 * @return bool Whether to the parameter is numeric or not.
	 **/
	public function rest_api_validate( $param, $request, $key ) {
		return is_numeric( $param );
	}

	/**
	 * Sanitizes user ID
	 *
	 * @param mixed $param   The paramater to validate.
	 * @param array $request The REST request.
	 * @param mixed $key     The key to check.
	 *
	 * @return int Sanitized user ID.
	 **/
	public function rest_api_sanitize( $param, $request, $key ) {
		return absint( $param );
	}
}
