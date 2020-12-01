<?php
/**
 * Restrict media on select roles.
 *
 * @package user-profile-picture
 */

namespace MPP\Includes;

use MPP\Includes\Functions as Functions;
use MPP\Includes\Options as Options;

/**
 * Class Media_Restrict
 */
class Media_Restrict {

	/**
	 * The user's post ID.
	 *
	 * @var int $post_id
	 */
	private $post_id = 0;

	/**
	 * Register Media Action.
	 */
	public function __construct() {

		// Restrict items for subscribers/authors/contributors.
		add_filter( 'ajax_query_attachments_args', array( $this, 'restrict_media_view' ) );

		// Only allow certain mime types for subscribers and contributors.
		add_filter( 'upload_mimes', array( $this, 'maybe_change_mime_types' ), 10, 2 );
	}

	/**
	 * Restrict media view for <= role author.
	 *
	 * @param array $query Query arguments.
	 */
	public function restrict_media_view( $query ) {
		$options = Options::get_options();
		if ( 'on' === $options['media_files_restrict'] ) {
			$user_id = get_current_user_id();
			if ( $user_id && ! current_user_can( 'activate_plugins' ) && ! current_user_can( 'edit_others_posts' ) ) {
				$query['author'] = $user_id;
			}
		}
		return $query;
	}

	/**
	 * Change mime types for subscribers and contributors.
	 *
	 * @param array   $types Mime types.
	 * @param WP_User $user The logged-in user.
	 */
	public function maybe_change_mime_types( $types, $user ) {
		$image_mime_types = array(
			'jpg|jpeg|jpe' => 'image/jpeg',
			'gif'          => 'image/gif',
			'png'          => 'image/png',
		);
		$options          = Options::get_options();
		if ( current_user_can( 'subscriber' ) && 'on' === $options['subscribers_only_upload_images'] ) {
			return $image_mime_types;
		}
		if ( current_user_can( 'contributor' ) && 'on' === $options['contributors_only_upload_images'] ) {
			return $image_mime_types;
		}
		return $types;
	}

	/**
	 * Check that a subscriber has access to upload an image to a post.
	 */
	public function allow_subscriber_to_attach_to_post() {
		global $current_user;
		if ( ! isset( $current_user->ID ) ) {
			return;
		}
		if ( isset( $_REQUEST['action'] ) && 'upload-attachment' === $_REQUEST['action'] ) {
			check_ajax_referer( 'media-form' );
		}
		if ( 0 === $this->post_id ) {
			$this->post_id = get_user_option( 'metronet_post_id', $current_user->ID );
		}
		map_meta_cap( 'edit_post', 5, 10388 );
	}
}
