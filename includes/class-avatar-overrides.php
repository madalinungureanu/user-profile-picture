<?php
/**
 * Ajax actions for the plugin.
 *
 * @package user-profile-picture
 */

namespace MPP\Includes;

/**
 * Class Ajax
 */
class Avatar_Overrides {

	/**
	 * Register Ajax actions.
	 */
	public function __construct() {
		// User Avatar override.
		add_filter( 'get_avatar', array( $this, 'avatar_override' ), 10, 6 );
		add_filter( 'pre_get_avatar_data', array( $this, 'pre_avatar_override' ), 10, 2 );
	}

	/**
	 * Override an Avatar with a User Profile Picture.
	 *
	 * Overrides an avatar with a profile image
	 *
	 * @param string $avatar SRC to the avatar.
	 * @param mixed  $id_or_email The ID or email address.
	 * @param int    $size Size of the image.
	 * @param string $default URL to the default image.
	 * @param string $alt Alternative text.
	 * @param array  $args Misc. args for the avatar.
	 *
	 * @return string Avatar.
	 */
	public function avatar_override( $avatar, $id_or_email, $size, $default, $alt, $args = array() ) {
		global $pagenow;
		if ( 'options-discussion.php' === $pagenow ) {
			return $avatar; // Stop overriding gravatars on options-discussion page.
		}

		// Get user data.
		if ( is_numeric( $id_or_email ) ) {
			$user = get_user_by( 'id', (int) $id_or_email );
		} elseif ( is_object( $id_or_email ) ) {
			$comment = $id_or_email;
			if ( empty( $comment->user_id ) ) {
				$user = get_user_by( 'id', $comment->user_id );
			} else {
				$user = get_user_by( 'email', $comment->comment_author_email );
			}
			if ( ! $user ) {
				return $avatar;
			}
		} elseif ( is_string( $id_or_email ) ) {
			$user = get_user_by( 'email', $id_or_email );
		} else {
			return $avatar;
		}
		if ( ! $user ) {
			return $avatar;
		}
		$user_id = $user->ID;

		// Determine if user has an avatar override.
		$avatar_override = get_user_option( 'metronet_avatar_override', $user_id );
		if ( ! $avatar_override || 'on' !== $avatar_override ) {
			return $avatar;
		}

		// Build classes array based on passed in args, else set defaults - see get_avatar in /wp-includes/pluggable.php.
		$classes = array(
			'avatar',
			sprintf( 'avatar-%s', esc_attr( $size ) ),
			'photo',
		);
		if ( isset( $args['class'] ) ) {
			if ( is_array( $args['class'] ) ) {
				$classes = array_merge( $classes, $args['class'] );
			} else {
				$args['class'] = explode( ' ', $args['class'] );
				$classes       = array_merge( $classes, $args['class'] );
			}
		}

		// Get custom filter classes.
		$classes = (array) apply_filters( 'mpp_avatar_classes', $classes );

		// Determine if the user has a profile image.
		$custom_avatar = mt_profile_img(
			$user_id,
			array(
				'size' => array( $size, $size ),
				'attr' => array(
					'alt'   => $alt,
					'class' => implode( ' ', $classes ),
				),
				'echo' => false,
			)
		);

		if ( ! $custom_avatar ) {
			return $avatar;
		}
		return $custom_avatar;
	}

	/**
	 * Overrides an avatar with a profile image
	 *
	 * @param array $args Arguments to determine the avatar dimensions.
	 * @param mixed $id_or_email The ID or email address.
	 *
	 * @return array $args Overridden URL or default if none can be found
	 **/
	public function pre_avatar_override( $args, $id_or_email ) {

		// Get user data.
		if ( is_numeric( $id_or_email ) ) {
			$user = get_user_by( 'id', (int) $id_or_email );
		} elseif ( is_object( $id_or_email ) ) {
			$comment = $id_or_email;
			if ( empty( $comment->user_id ) ) {
				$user = get_user_by( 'id', $comment->user_id );
			} else {
				$user = get_user_by( 'email', $comment->comment_author_email );
			}
			if ( ! $user ) {
				return $args;
			}
		} elseif ( is_string( $id_or_email ) ) {
			$user = get_user_by( 'email', $id_or_email );
		} else {
			return $args;
		}
		if ( ! $user ) {
			return $args;
		}
		$user_id = $user->ID;

		// Get the post the user is attached to.
		$size = $args['size'];

		$profile_post_id = absint( get_user_option( 'metronet_post_id', $user_id ) );
		if ( 0 === $profile_post_id ) {
			return $args;
		}
		$post_thumbnail_id = get_post_thumbnail_id( $profile_post_id );

		// Attempt to get the image in the right size.
		$avatar_image = get_the_post_thumbnail_url( $profile_post_id, array( $size, $size ) );
		if ( empty( $avatar_image ) ) {
			return $args;
		}
		$args['url'] = $avatar_image;
		return $args;
	}
}
