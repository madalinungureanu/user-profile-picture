<?php
 // Prevent direct file access
 if (!defined('ABSPATH')) {
    die('No direct access');
}
/**
 * Gutenberg class for Metronet Tag Manager
 *
 * Gutenberg class for Metronet Tag Manager
 *
 * @category Metronet Tag Manager
 * @package  Metronet Tag Manager
 * @author   Ronald Huereca <ronald@mediaron.com>
 * @license  GPL-2.0+
 * @link     https://github.com/ronalfy/metronet-tag-manager
 *
 * @since 2.0.0
 */
class Metronet_Profile_Picture_Gutenberg {
	public function __construct() {
		if (!function_exists( 'register_block_type')) {
			return;
		}

		add_action('init', array($this, 'register_block'));
		add_action('enqueue_block_assets', array($this, 'add_gutenberg_styles'));
		add_action('enqueue_block_editor_assets', array($this,'add_gutenberg_scripts'));
	}

	public function register_block() {
		register_block_type( 'mpp/user-profile', array(
			'attributes' => array()
		) );
	}

	public function add_gutenberg_scripts() {

		// Ensure script debug allows non-minified scripts
		$min_or_not = (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) ? '' : '.min';

		wp_enqueue_script('mpp_gutenberg', Metronet_Profile_Picture::get_plugin_url('js/gutenberg'.$min_or_not.'.js'), array('wp-blocks', 'wp-element'), METRONET_PROFILE_PICTURE_VERSION, true);

		/* For the Gutenberg plugin */
		if ( function_exists( 'gutenberg_get_jed_locale_data' ) ) {
			$locale  = gutenberg_get_jed_locale_data( 'metronet-profile-picture' );
			$content = 'wp.i18n.setLocaleData( ' . json_encode( $locale ) . ', "metronet-profile-picture" );';
			wp_script_add_data( 'mpp_gutenberg', 'data', $content );
		} elseif (function_exists('wp_get_jed_locale_data')) {
			/* for 5.0 */
			$locale  = wp_get_jed_locale_data( 'metronet-profile-picture' );
			$content = 'wp.i18n.setLocaleData( ' . json_encode( $locale ) . ', "metronet-profile-picture" );';
			wp_script_add_data( 'mpp_gutenberg', 'data', $content );
		}

		// Pass in REST URL
		wp_localize_script(
			'mpp_gutenberg',
			'mpp_gutenberg',
			array(
				'rest_url' => esc_url( rest_url( 'mpp/v2' ) ),
				'nonce' => wp_create_nonce( 'wp_rest' ),
				'mystery_man' => Metronet_Profile_Picture::get_plugin_url( 'img/mystery.png' )
			)
		);

		wp_enqueue_style( 'mpp_gutenberg', Metronet_Profile_Picture::get_plugin_url( '/css/back-end-gutenberg.css' ), array(), METRONET_PROFILE_PICTURE_VERSION, 'all' );
	}

	public function add_gutenberg_styles() {
		// Ensure script debug allows non-minified scripts
		$min_or_not = (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) ? '' : '.min';
		wp_enqueue_style( 'mpp_gutenberg', Metronet_Profile_Picture::get_plugin_url( '/css/front-end-gutenberg.css' ), array(), METRONET_PROFILE_PICTURE_VERSION, 'all' );
		
	}
}