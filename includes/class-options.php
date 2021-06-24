<?php
/**
 * Options for the plugin.
 *
 * @package user-profile-picture
 */

namespace MPP\Includes;

/**
 * Class Options
 */
class Options {
	/**
	 * Get the options for User Profile Picture
	 *
	 * @since 2.3.0
	 *
	 * @return array $options Array of admin options.
	 */
	public static function get_options() {
		$options = get_option( 'mpp_options', false );
		if ( false === $options ) {
			$options = self::get_defaults();
		} elseif ( is_array( $options ) ) {
			$options = wp_parse_args( $options, self::get_defaults() );
		} else {
			$options = self::get_defaults();
		}
		return $options;
	}

	/**
	 * Update options via sanitization
	 *
	 * @since 2.3.0
	 * @access public
	 * @param array $options array of options to save.
	 * @return void
	 */
	public static function update_options( $options ) {
		foreach ( $options as $key => &$option ) {
			switch ( $key ) {
				default:
					$option = sanitize_text_field( $options[ $key ] );
					break;
			}
		}
		/**
		 * Allow other plugins to perform their own sanitization functions.
		 *
		 * @since 2.3.0
		 *
		 * @param array $options An array of sanitized POST options
		 */
		$options = apply_filters( 'mpp_options_sanitized', $options );
		update_option( 'mpp_options', $options );
	}

	/**
	 * Get the default options for User Profile Picture
	 *
	 * @since 2.3.0
	 */
	public static function get_defaults() {
		$defaults = array(
			'load_gutenberg'                       => 'on',
			'generate_image_sizes'                 => 'off',
			'disable_image_sizes'                  => 'off',
			'subscribers_upload'                   => 'off',
			'contributors_upload'                  => 'off',
			'media_files_restrict'                 => 'off',
			'subscribers_only_upload_images'       => 'on',
			'contributors_only_upload_images'      => 'off',
			'change_profile_user_list'             => 'off',
			'multiple_profile_images'              => 'off',
			'custom_image_sizes_module'            => 'off',
			'custom_cropping_module'               => 'off',
			'custom_team_member_module'            => 'off',
			'front_end_upload_module'              => 'off',
			'buddypress_bbpress_module'            => 'off',
			'hide_wordpress_avatar_section'        => 'off',
			'show_author_avatar_column_post_types' => 'off',
		);

		/**
		 * Allow other plugins to add to the defaults.
		 *
		 * @since 2.3.1
		 *
		 * @param array $defaults An array of option defaults.
		 */
		$defaults = apply_filters( 'mpp_options_defaults', $defaults );
		return $defaults;
	}
}