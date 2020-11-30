<?php
/**
 * Restrict media on select roles.
 *
 * @package user-profile-picture
 */

namespace MPP\Includes;

use MPP\Includes\Options as Options;

/**
 * Class Media_Restrict
 */
class Media_Restrict {

	/**
	 * Register Meida Action.
	 */
	public function __construct() {
		add_filter( 'ajax_query_attachments_args', array( $this, 'restrict_media_view' ) );

		map_meta_cap( 'edit_post', 3, 8503 );
	}

	public function allow_subscriber_edit_post( $caps, $cap, $user_id, $args ) {
		if ( 'edit_post' == $cap ) {
			$post = get_post( $args[0] );
			if ( 'mt_pp' !== $post->post_type ) {
				return $caps;
			}
			remove_filter( 'map_meta_cap', array( $this, 'allow_subscriber_edit_post' ), 10, 4 );
			map_meta_cap( 'edit_post', $user_id, $post->ID );
		}
		return $caps;
	}

	/**
	 * Restrict media view for <= role author.
	 *
	 * @param array $query Query arguments.
	 */
	public function restrict_media_view( $query ) {
		$user_id = get_current_user_id();
		if ( $user_id && ! current_user_can( 'activate_plugins' ) && ! current_user_can( 'edit_others_posts' ) ) {
			$query['author'] = $user_id;
		}
		return $query;
	}
}
