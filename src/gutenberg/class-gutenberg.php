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

		add_action( 'init', array($this, 'register_block' ) );
		add_action( 'enqueue_block_assets', array( $this, 'add_gutenberg_styles' ) );
		add_action( 'enqueue_block_editor_assets', array( $this,'add_gutenberg_scripts') );
		add_action( 'admin_footer', array( $this, 'load_gutenblock_svgs' ) );
	}

	public function register_block() {
		register_block_type( 'mpp/user-profile', array(
			'attributes' => array(
				'profileName' => array(
					'type' => 'string',
					'default' => '',
				),
				'theme' => array(
					'string',
					'default' => 'regular',
				),
				'profileTitle' => array(
					'type' => 'string',
					'default' => '',
				),
				'profileContent' => array(
					'type' => 'string',
					'default' => '',
				),
				'profileAlignment' => array(
					'type' => 'string',
					'default' => '',
				),
				'profileImgURL' => array(
					'type' => 'string',
					'default' => '',
				),
				'profileImgID' => array(
					'type' => 'number',
					'default' => ''
				),
				'profileURL' => array(
					'type' => 'string',
					'default' => '',
				),
				'padding' => array(
					'type' => 'number',
					'default' => 0,
				),
				'border' => array(
					'type' => 'number',
					'default' => 0,
				),
				'borderRounded' => array(
					'type' => 'number',
					'default' => 0
				),
				'borderColor' => array(
					'type' => 'string',
					'default' => '#f2f2f2',
				),
				'profileBackgroundColor' => array(
					'type' => 'string',
					'default' => '#f2f2f2'
				),
				'profileTextColor' => array(
					'type' => 'string',
					'default' => '#32373c'
				),
				'profileViewPostsBackgroundColor' => array(
					'type' => 'string',
					'default' => '#cf6d38',
				),
				'profileViewPostsTextColor' => array(
					'type' => 'string',
					'default' => '#FFFFFF',
				),
				'profileWebsiteBackgroundColor' => array(
					'type' => 'string',
					'default' => '#000000',
				),
				'profileWebsiteTextColor' => array(
					'type' => 'string',
					'default' => '#FFFFFF',
				),
				'profileLinkColor' => array(
					'type' => 'string',
					'default' => 'inherit'
				),
				'headerFontSize' => array(
					'type' => 'number',
					'default' => 24,
				),
				'buttonFontSize' => array(
					'type' => 'number',
					'default' => 16
				),
				'profileFontSize' => array(
					'type' => 'number',
					'default' => 18
				),
				'profileAvatarShape' => array(
					'type' => 'string',
					'default' => 'square'
				),
				'showName' => array(
					'type' => 'bool',
					'default' => true,
				),
				'showTitle' => array(
					'type' => 'bool',
					'default' => true
				),
				'showDescription' => array(
					'type' => 'bool',
					'default' => true,
				),
				'showViewPosts' => array(
					'type' => 'bool',
					'default' => true,
				),
				'showWebsite' => array(
					'type' => 'bool',
					'default' => true,
				),
				'user_id' => array(
					'type' => 'number',
					'default' => 0
				),
				'socialFacebook' => array(
					'type' => 'string',
					'default' => '',
				),
				'socialTwitter' => array(
					'type' => 'string',
					'default' => '',
				),
				'socialYouTube' => array(
					'type' => 'string',
					'default' => ''
				),
				'socialLinkedIn' => array(
					'type' => 'string',
					'default' => '',
				),
				'socialWordPress' => array(
					'type' => 'string',
					'default' => '',
				),
				'socialGitHub' => array(
					'type' => 'string',
					'default' => '',
				),
				'socialPinterest' => array(
					'type' => 'string',
					'default' => ''
				),
				'socialInstagram' => array(
					'type' => 'string',
					'default' => '',
				),
				'website' => array(
					'type' => 'string',
					'default' => '',
				),
				'socialMediaOptions' => array(
					'type' => 'string',
					'default' => 'colors',
				),
				'socialMediaColors' => array(
					'type' => 'string',
					'default' => '#000000'
				),
				'tabbedAuthorProfile' => array(
					'type' => 'string',
					'default' => __( 'Author Details', 'metronet-profile-picture' )
				),
				'tabbedAuthorProfileHeading' => array(
					'type' => 'string',
					'default' => __( 'Author Details', 'metronet-profile-picture' )
				),
				'tabbedAuthorLatestPosts' => array(
					'type' => 'string',
					'default' => __( 'Latest Posts', 'metronet-profile-picture' )
				),
				'tabbedAuthorSubHeading' => array(
					'type' => 'string',
					'default' => '',
				),
				'tabbedAuthorProfileTitle' => array(
					'type' => 'string',
					'default' => ''
				),
				'profileTabHeadlineColor' => array(
					'type' => 'string',
					'default' => '#42737b'
				),
				'profileTabColor' => array(
					'type' => 'string',
					'default' => '#42737b',
				),
				'profileTabPostsColor' => array(
					'type' => 'string',
					'default' => '#30424b',
				),
				'profileTabHeadlineTextColor' => array(
					'type' => 'string',
					'default' => '#FFFFFF'
				),
				'profileTabTextColor' => array(
					'type' => 'string',
					'default' => '#FFFFFF',
				),
				'profileTabPostsTextColor' => array(
					'type' => 'string',
					'default' => '#FFFFFF',
				),
				'profileLatestPostsOptionsValue' => array(
					'type' => 'string',
					'default' => 'light'
				),
				'profileCompactAlignment' => array(
					'type' => 'string',
					'default' => 'center'
				),
				'showPostsWidth' => array(
					'type' => 'string',
					'default' => '',
				),
				'showSocialMedia' => array(
					'type' => 'bool',
					'default' => true
				)

			),
			'render_callback' => array($this, 'display_frontend'),
            'editor_script'   => 'mpp_gutenberg'
		) );
	}

	public function display_frontend( $attributes ) {
		ob_start();
		?>
		<div class="mpp-profile-wrap mpp-block-profile <?php echo esc_attr( $attributes['profileAlignment'] ); ?> <?php echo esc_attr( $attributes['theme'] ); ?> <?php echo esc_attr( $attributes['profileAvatarShape'] ); ?>" style="<?php echo $attributes['padding'] > 0 ? 'padding: ' . esc_attr( $attributes['padding'] ) . 'px;' : ''?><?php echo $attributes['border'] > 0 ? 'border:' . esc_attr( $attributes['border'] ) . 'px solid ' . esc_attr( $attributes['borderColor'] ) . ';' : ''?><?php echo $attributes['borderRounded'] > 0 ? 'border-radius:' . esc_attr( $attributes['borderRounded'] ) . 'px;': ''?>background-color: <?php echo esc_attr( $attributes['profileBackgroundColor'] ) . ';'; ?> color: <?php echo esc_attr( $attributes['profileTextColor'] ) . ';' ?>">
			<div class="mpp-profile-gutenberg-wrap mt-font-size-<?php echo esc_attr( $attributes['profileFontSize'] ); ?>">
				<div class="mpp-profile-image-wrapper">
					<div class="mpp-profile-image-square">
						<img class="profile-avatar" alt="avatar" src="<?php echo esc_url( $attributes['profileImgURL'] ); ?>" />
					</div>
				</div>
				<div class="mpp-content-wrap">
					<?php if ( $attributes['showName']):?>
					<h2 style="color:<?php echo esc_attr( $attributes['profileTextColor'] ); ?>; font-size: <?php echo esc_attr( $attributes['headerFontSize'] ) . 'px;'?>"><?php echo wp_kses_post( $attributes['profileName'] ); ?></h2>
					<?php endif; ?>
					<?php if ( $attributes['showTitle']):?>
					<p style="color:<?php echo esc_attr( $attributes['profileTextColor'] ); ?>"><?php echo wp_kses_post( $attributes['profileTitle'] ); ?></p>
					<?php endif; ?>
					<?php if ( $attributes['showDescription']):?>
					<div><?php echo wp_kses_post( $attributes['profileContent'] ); ?></div>
					<?php endif; ?>
					<?php if ( isset( $attributes['profileURL'] ) && strlen( $attributes['profileURL' ] ) > 0 ) :?>
						<div class="mpp-gutenberg-view-posts">
							<?php if ( $attributes['showViewPosts'] ): ?>
								<div class="mpp-profile-view-posts" style="background-color: <?php echo esc_attr( $attributes['profileViewPostsBackgroundColor'] ); ?>; color: <?php echo esc_attr( $attributes['profileViewPostsTextColor'] ); ?>; width: <?php echo esc_attr( $attributes['showPostsWidth'] ); ?>; font-size: <?php echo esc_attr( $attributes['buttonFontSize'] ); ?>px;">
									<a href="<?php echo esc_url( $attributes['profileURL'] ); ?>" style="background: <?php echo esc_attr( $attributes['profileViewPostsBackgroundColor'] ); ?>; color: <?php echo esc_attr( $attributes['profileViewPostsTextColor'] ); ?>">
									<?php esc_html_e( 'View Posts', 'metronet-profile-picture' ); ?></a>
								</div><!-- .mpp-profile-view-posts -->
							<?php endif; ?>
							<?php if ( '' != $attributes['website'] && $attributes['showWebsite'] ): ?>
							<div class="mpp-profile-view-website" style="background: <?php echo esc_attr( $attributes['profileWebsiteBackgroundColor'] ); ?>;color: <?php echo esc_attr( $attributes['profileWebsiteTextColor'] ); ?>; font-size: <?php echo esc_attr( $attributes['buttonFontSize'] ) ;?>px;">
								<a href="<?php echo esc_attr( $attributes['website'] ) ?>" style="background: <?php echo esc_attr( $attributes['profileWebsiteBackgroundColor'] ); ?>; color: <?php echo esc_attr( $attributes['profileWebsiteTextColor'] ); ?>;"><?php esc_html_e( 'View Website', 'metronet-profile-picture' ); ?></a>
							</div><!-- .mpp-profile-view-website -->
							<?php endif; ?>
						</div><!-- .mpp-gutenberg-view-posts -->
					<?php endif; ?>
				</div><!-- .mpp-content-wrap -->
				<?php if ( true == $attributes['showSocialMedia'] && ( 'regular' === $attributes['theme'] || 'compact' === $attributes['theme'] || 'profile' === $attributes['theme'] ) ) : ?>
					<?php echo $this->get_social_icons( $attributes ); ?>
				<?php endif; ?>
			</div><!-- .mpp-profile-gutenberg-wrap -->
		</div><!-- .mpp-profile-wrap -->
		<?php
		$this->load_gutenblock_svgs();
		return ob_get_clean();
	}

	public function get_social_icons( $attributes ) {
		ob_start();
		?>
		<div class="mpp-social">
			<?php if( !empty( $attributes['socialFacebook'] ) ): ?>
				<a href="<?php echo esc_url( $attributes['socialFacebook'] ); ?>">
					<svg class="icon icon-facebook" role="img" style="<?php echo 'custom' === $attributes['socialMediaOptions'] ? 'fill:' . esc_attr( $attributes['socialMediaColors'] ) . ';': ''; ?>">
						<use href="#facebook"></use>
					</svg>
				</a>
			<?php endif; ?>
			<?php if( !empty( $attributes['socialTwitter'] ) ): ?>
				<a href="<?php echo esc_url( $attributes['socialTwitter'] ); ?>">
					<svg class="icon icon-twitter" role="img" style="<?php echo 'custom' === $attributes['socialMediaOptions'] ? 'fill:' . esc_attr( $attributes['socialMediaColors'] ) . ';': ''; ?>">
						<use href="#twitter"></use>
					</svg>
				</a>
			<?php endif; ?>
			<?php if( !empty( $attributes['socialInstagram'] ) ): ?>
				<a href="<?php echo esc_url( $attributes['socialInstagram'] ); ?>">
					<svg class="icon icon-instagram" role="img" style="<?php echo 'custom' === $attributes['socialMediaOptions'] ? 'fill:' . esc_attr( $attributes['socialMediaColors'] ) . ';': ''; ?>">
						<use href="#instagram"></use>
					</svg>
				</a>
			<?php endif; ?>
			<?php if( !empty( $attributes['socialPinterest'] ) ): ?>
				<a href="<?php echo esc_url( $attributes['socialPinterest'] ); ?>">
					<svg class="icon icon-pinterest" role="img" style="<?php echo 'custom' === $attributes['socialMediaOptions'] ? 'fill:' . esc_attr( $attributes['socialMediaColors'] ) . ';': ''; ?>">
						<use href="#pinterest"></use>
					</svg>
				</a>
			<?php endif; ?>
			<?php if( !empty( $attributes['socialLinkedIn'] ) ): ?>
				<a href="<?php echo esc_url( $attributes['socialLinkedIn'] ); ?>">
					<svg class="icon icon-linkedin" role="img" style="<?php echo 'custom' === $attributes['socialMediaOptions'] ? 'fill:' . esc_attr( $attributes['socialMediaColors'] ) . ';': ''; ?>">
						<use href="#linkedin"></use>
					</svg>
				</a>
			<?php endif; ?>
			<?php if( !empty( $attributes['socialYouTube'] ) ): ?>
				<a href="<?php echo esc_url( $attributes['socialYouTube'] ); ?>">
					<svg class="icon icon-youtube" role="img" style="<?php echo 'custom' === $attributes['socialMediaOptions'] ? 'fill:' . esc_attr( $attributes['socialMediaColors'] ) . ';': ''; ?>">
						<use href="#youtube"></use>
					</svg>
				</a>
			<?php endif; ?>
			<?php if( !empty( $attributes['socialGitHub'] ) ): ?>
				<a href="<?php echo esc_url( $attributes['socialGitHub'] ); ?>">
					<svg class="icon icon-github" role="img" style="<?php echo 'custom' === $attributes['socialMediaOptions'] ? 'fill:' . esc_attr( $attributes['socialMediaColors'] ) . ';': ''; ?>">
						<use href="#github"></use>
					</svg>
				</a>
			<?php endif; ?>
			<?php if( !empty( $attributes['socialWordPress'] ) ): ?>
				<a href="<?php echo esc_url( $attributes['socialWordPress'] ); ?>">
					<svg class="icon icon-wordpress" role="img" style="<?php echo 'custom' === $attributes['socialMediaOptions'] ? 'fill:' . esc_attr( $attributes['socialMediaColors'] ) . ';': ''; ?>">
						<use href="#wordpress"></use>
					</svg>
				</a>
			<?php endif; ?>
		</div><!-- .mpp-social -->
		<?php
		return ob_get_clean();
	}

	public function load_gutenblock_svgs() {
		if ( '' != get_post_type() ) {
			// Define SVG sprite file.
			$path = '/img/social-logos.svg';
			$svg_icons = rtrim( dirname( plugin_dir_path(__FILE__), 1 ), '/' );
			if ( ! empty( $path ) && is_string( $path) ) {
				$svg_icons .= '/' . ltrim( $path, '/' );
			}

			/**
			 * Filter Social Icons Sprite.
			 *
			 * @since 2.1.0
			 *
			 * @param string Absolute directory path to SVG sprite
			 */
			$svg_icons = apply_filters( 'mpp_icons_sprite', $svg_icons );
			// If it exists, include it.
			if ( file_exists( $svg_icons ) ) {
				echo '<div style="position: absolute; height: 0; width: 0; overflow: hidden;">';
				require( $svg_icons );
				echo '</div>';
			}
		}
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
		if( is_admin() ) return;
		// Ensure script debug allows non-minified scripts
		$min_or_not = (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) ? '' : '.min';
		wp_enqueue_style( 'mpp_gutenberg', Metronet_Profile_Picture::get_plugin_url( '/css/front-end-gutenberg.css' ), array(), METRONET_PROFILE_PICTURE_VERSION, 'all' );

	}
}