<?php // phpcs:ignore
// Prevent direct file access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct access' );
}
/**
 * Gutenberg class for Metronet Profile Picture
 *
 * Gutenberg class for Metronet Profile Picture
 *
 * @category Metronet Metronet Profile Picture
 * @package  Metronet Metronet Profile Picture
 * @author   Ronald Huereca <ronald@mediaron.com>
 * @license  GPL-2.0+
 * @link     https://github.com/ronalfy/user-profile-picture
 *
 * @since 2.0.0
 */
class Metronet_Profile_Picture_Gutenberg {
	/**
	 * Class constructor.
	 */
	public function __construct() {
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}

		add_action( 'init', array( $this, 'register_block' ) );
		add_action( 'enqueue_block_assets', array( $this, 'add_gutenberg_styles' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'add_gutenberg_scripts' ) );
		add_action( 'admin_footer', array( $this, 'load_gutenblock_svgs' ) );
	}

	/**
	 * Register the main User Profile Picture block.
	 */
	public function register_block() {
		register_block_type(
			'mpp/user-profile',
			array(
				'attributes' => array(),
			)
		);
		register_block_type(
			'mpp/user-profile-enhanced',
			array(
				'attributes'      => array(
					'profileName'                     => array(
						'type'    => 'string',
						'default' => '',
					),
					'theme'                           => array(
						'type'    => 'string',
						'default' => 'regular',
					),
					'profileTitle'                    => array(
						'type'    => 'string',
						'default' => '',
					),
					'profileContent'                  => array(
						'type'    => 'string',
						'default' => '',
					),
					'profileViewPosts'                => array(
						'type'    => 'string',
						'default' => __( 'View Posts', 'metronet-profile-picture' ),
					),
					'profileViewWebsite'              => array(
						'type'    => 'string',
						'default' => __( 'View Website', 'metronet-profile-picture' ),
					),
					'profileAlignment'                => array(
						'type'    => 'string',
						'default' => '',
					),
					'profileImgURL'                   => array(
						'type'    => 'string',
						'default' => '',
					),
					'profileImgID'                    => array(
						'type'    => 'number',
						'default' => '',
					),
					'profileURL'                      => array(
						'type'    => 'string',
						'default' => '',
					),
					'padding'                         => array(
						'type'    => 'number',
						'default' => 0,
					),
					'border'                          => array(
						'type'    => 'number',
						'default' => 0,
					),
					'borderRounded'                   => array(
						'type'    => 'number',
						'default' => 0,
					),
					'borderColor'                     => array(
						'type'    => 'string',
						'default' => '#f2f2f2',
					),
					'profileBackgroundColor'          => array(
						'type'    => 'string',
						'default' => '#f2f2f2',
					),
					'profileTextColor'                => array(
						'type'    => 'string',
						'default' => '#32373c',
					),
					'profileViewPostsBackgroundColor' => array(
						'type'    => 'string',
						'default' => '#cf6d38',
					),
					'profileViewPostsTextColor'       => array(
						'type'    => 'string',
						'default' => '#FFFFFF',
					),
					'profileWebsiteBackgroundColor'   => array(
						'type'    => 'string',
						'default' => '#000000',
					),
					'profileWebsiteTextColor'         => array(
						'type'    => 'string',
						'default' => '#FFFFFF',
					),
					'profileLinkColor'                => array(
						'type'    => 'string',
						'default' => 'inherit',
					),
					'headerFontSize'                  => array(
						'type'    => 'number',
						'default' => 24,
					),
					'buttonFontSize'                  => array(
						'type'    => 'number',
						'default' => 16,
					),
					'profileFontSize'                 => array(
						'type'    => 'number',
						'default' => 18,
					),
					'profileAvatarShape'              => array(
						'type'    => 'string',
						'default' => 'square',
					),
					'showName'                        => array(
						'type'    => 'boolean',
						'default' => true,
					),
					'showTitle'                       => array(
						'type'    => 'boolean',
						'default' => true,
					),
					'showDescription'                 => array(
						'type'    => 'boolean',
						'default' => true,
					),
					'showViewPosts'                   => array(
						'type'    => 'boolean',
						'default' => true,
					),
					'showWebsite'                     => array(
						'type'    => 'boolean',
						'default' => true,
					),
					'user_id'                         => array(
						'type'    => 'number',
						'default' => 0,
					),
					'socialFacebook'                  => array(
						'type'    => 'string',
						'default' => '',
					),
					'socialTwitter'                   => array(
						'type'    => 'string',
						'default' => '',
					),
					'socialYouTube'                   => array(
						'type'    => 'string',
						'default' => '',
					),
					'socialLinkedIn'                  => array(
						'type'    => 'string',
						'default' => '',
					),
					'socialWordPress'                 => array(
						'type'    => 'string',
						'default' => '',
					),
					'socialGitHub'                    => array(
						'type'    => 'string',
						'default' => '',
					),
					'socialPinterest'                 => array(
						'type'    => 'string',
						'default' => '',
					),
					'socialInstagram'                 => array(
						'type'    => 'string',
						'default' => '',
					),
					'website'                         => array(
						'type'    => 'string',
						'default' => '',
					),
					'socialMediaOptions'              => array(
						'type'    => 'string',
						'default' => 'colors',
					),
					'socialMediaColors'               => array(
						'type'    => 'string',
						'default' => '#000000',
					),
					'tabbedAuthorProfile'             => array(
						'type'    => 'string',
						'default' => __( 'Author Details', 'metronet-profile-picture' ),
					),
					'tabbedAuthorProfileHeading'      => array(
						'type'    => 'string',
						'default' => __( 'Author Details', 'metronet-profile-picture' ),
					),
					'tabbedAuthorLatestPosts'         => array(
						'type'    => 'string',
						'default' => __( 'Latest Posts', 'metronet-profile-picture' ),
					),
					'tabbedAuthorSubHeading'          => array(
						'type'    => 'string',
						'default' => '',
					),
					'tabbedAuthorProfileTitle'        => array(
						'type'    => 'string',
						'default' => '',
					),
					'profileTabHeadlineColor'         => array(
						'type'    => 'string',
						'default' => '#42737b',
					),
					'profileTabColor'                 => array(
						'type'    => 'string',
						'default' => '#42737b',
					),
					'profileTabPostsColor'            => array(
						'type'    => 'string',
						'default' => '#30424b',
					),
					'profileTabHeadlineTextColor'     => array(
						'type'    => 'string',
						'default' => '#FFFFFF',
					),
					'profileTabTextColor'             => array(
						'type'    => 'string',
						'default' => '#FFFFFF',
					),
					'profileTabPostsTextColor'        => array(
						'type'    => 'string',
						'default' => '#FFFFFF',
					),
					'profileLatestPostsOptionsValue'  => array(
						'type'    => 'string',
						'default' => 'light',
					),
					'profileCompactAlignment'         => array(
						'type'    => 'string',
						'default' => 'center',
					),
					'showPostsWidth'                  => array(
						'type'    => 'string',
						'default' => '',
					),
					'showSocialMedia'                 => array(
						'type'    => 'boolean',
						'default' => true,
					),
					'profileName'                     => array(
						'type'    => 'string',
						'default' => '',
					),

				),
				'render_callback' => array( $this, 'display_frontend' ),
				'editor_script'   => 'mpp_gutenberg',
			)
		);
	}

	/**
	 * Display the front-end for the Gutenberg block.
	 *
	 * @param array $attributes Array of block attributes.
	 */
	public function display_frontend( $attributes ) {
		if ( is_admin() ) {
			return;
		}
		ob_start();
		if ( empty( $attributes['profileImgURL'] ) ) {
			$attributes['profileImgURL'] = wp_get_attachment_image_url( $attributes['profileImgID'], 'thumbnail', false, '' );
		}
		?>
		<?php if ( 'regular' === $attributes['theme'] || 'compact' === $attributes['theme'] || 'profile' === $attributes['theme'] ) : ?>
			<div class="mpp-enhanced-profile-wrap mpp-block-profile <?php echo 'compact' === $attributes['theme'] ? esc_attr( $attributes['profileCompactAlignment'] ) : ''; ?> <?php echo esc_attr( $attributes['theme'] ); ?> <?php echo esc_attr( $attributes['profileAvatarShape'] ); ?>" style="<?php echo $attributes['padding'] > 0 ? 'padding: ' . esc_attr( $attributes['padding'] ) . 'px;' : ''; ?><?php echo $attributes['border'] > 0 ? 'border:' . esc_attr( $attributes['border'] ) . 'px solid ' . esc_attr( $attributes['borderColor'] ) . ';' : ''; ?><?php echo $attributes['borderRounded'] > 0 ? 'border-radius:' . esc_attr( $attributes['borderRounded'] ) . 'px;' : ''; ?>background-color: <?php echo esc_attr( $attributes['profileBackgroundColor'] ) . ';'; ?> color: <?php echo esc_attr( $attributes['profileTextColor'] ) . ';'; ?>">
				<div class="mpp-profile-gutenberg-wrap mt-font-size-<?php echo esc_attr( $attributes['profileFontSize'] ); ?>">
					<?php if ( 'regular' === $attributes['theme'] ) : ?>
						<div class="mpp-profile-image-wrapper">
							<div class="mpp-profile-image-square">
								<img class="profile-avatar" alt="avatar" src="<?php echo esc_url( $attributes['profileImgURL'] ); ?>" />
							</div>
						</div>
						<div class="mpp-content-wrap">
							<?php if ( $attributes['showName'] ) : ?>
							<h2 style="color:<?php echo esc_attr( $attributes['profileTextColor'] ); ?>; font-size: <?php echo esc_attr( $attributes['headerFontSize'] ) . 'px;'; ?>"><?php echo wp_kses_post( $attributes['profileName'] ); ?></h2>
							<?php endif; ?>
							<?php if ( $attributes['showTitle'] ) : ?>
							<p style="color:<?php echo esc_attr( $attributes['profileTextColor'] ); ?>"><?php echo wp_kses_post( $attributes['profileTitle'] ); ?></p>
							<?php endif; ?>
							<?php if ( $attributes['showDescription'] ) : ?>
							<div><?php echo wp_kses_post( $attributes['profileContent'] ); ?></div>
							<?php endif; ?>
							<?php if ( isset( $attributes['profileURL'] ) && strlen( $attributes['profileURL'] ) > 0 ) : ?>
								<div class="mpp-gutenberg-view-posts">
									<?php if ( $attributes['showViewPosts'] ) : ?>
										<div class="mpp-profile-view-posts" style="background-color: <?php echo esc_attr( $attributes['profileViewPostsBackgroundColor'] ); ?>; color: <?php echo esc_attr( $attributes['profileViewPostsTextColor'] ); ?>; width: <?php echo esc_attr( $attributes['showPostsWidth'] ); ?>; font-size: <?php echo esc_attr( $attributes['buttonFontSize'] ); ?>px;">
											<a href="<?php echo esc_url( $attributes['profileURL'] ); ?>" style="background: <?php echo esc_attr( $attributes['profileViewPostsBackgroundColor'] ); ?>; color: <?php echo esc_attr( $attributes['profileViewPostsTextColor'] ); ?>">
											<?php
											if ( isset( $attributes['profileViewPosts'] ) ) {
												echo esc_html( $attributes['profileViewPosts'] );
											} else {
												esc_html_e( 'View Posts', 'metronet-profile-picture' );
											}
											?>
											</a>
										</div><!-- .mpp-profile-view-posts -->
									<?php endif; ?>
									<?php if ( '' !== $attributes['website'] && $attributes['showWebsite'] ) : ?>
									<div class="mpp-profile-view-website" style="background: <?php echo esc_attr( $attributes['profileWebsiteBackgroundColor'] ); ?>;color: <?php echo esc_attr( $attributes['profileWebsiteTextColor'] ); ?>; font-size: <?php echo esc_attr( $attributes['buttonFontSize'] ); ?>px;">
										<a href="<?php echo esc_url( $attributes['website'] ); ?>" style="background: <?php echo esc_attr( $attributes['profileWebsiteBackgroundColor'] ); ?>; color: <?php echo esc_attr( $attributes['profileWebsiteTextColor'] ); ?>;">
										<?php
										if ( isset( $attributes['profileViewWebsite'] ) ) {
											echo esc_html( $attributes['profileViewWebsite'] );
										} else {
											esc_html_e( 'View Website', 'metronet-profile-picture' );
										}
										?>
										</a>
									</div><!-- .mpp-profile-view-website -->
									<?php endif; ?>
								</div><!-- .mpp-gutenberg-view-posts -->
							<?php endif; ?>
						</div><!-- .mpp-content-wrap -->
					<?php endif; /* End Regular Theme */ ?>
					<?php if ( 'profile' === $attributes['theme'] ) : ?>
						<?php if ( $attributes['showName'] ) : ?>
						<h2 style="color: <?php echo esc_attr( $attributes['profileTextColor'] ); ?>; font-size: <?php echo esc_attr( $attributes['headerFontSize'] ) . 'px'; ?>"><?php echo wp_kses_post( $attributes['profileName'] ); ?></h2>
						<?php endif; ?>
						<div class="mpp-profile-image-wrapper">
							<div class="mpp-profile-image-square">
								<img src="<?php echo esc_url( $attributes['profileImgURL'] ); ?>" alt="avatar" class="profile-avatar" />
							</div>
						</div><!-- .mpp-profile-image-wrapper -->
						<?php if ( $attributes['showDescription'] ) : ?>
						<div class="mpp-profile-text">
							<?php echo wp_kses_post( $attributes['profileContent'] ); ?>
						</div><!-- .mpp-profile-text -->
						<?php endif; ?>
						<?php
						$font_size       = isset( $attributes['fontSize'] ) ? $attributes['fontSize'] : false;
						$font_size_style = '';
						if ( $font_size ) {
							$font_size_style = 'font-size: ' . $font_size . 'px';
						}
						?>
						<div class="mpp-profile-meta" style="<?php echo esc_attr( $font_size_style ); ?>">
							<?php if ( $attributes['showViewPosts'] ) : ?>
								<div class="mpp-profile-link alignleft">
									<a href="<?php echo esc_url( $attributes['profileURL'] ); ?>" style="color: <?php echo esc_attr( $attributes['profileLinkColor'] ); ?>;"><?php esc_html_e( 'View all posts by', 'metronet-profile-picture' ); ?> <?php echo esc_html( $attributes['profileName'] ); ?></a>
								</div><!-- .mpp-profile-link -->
								<div class="mpp-profile-link alignright">
									<a href="<?php echo esc_url( $attributes['website'] ); ?>" style="color: <?php echo esc_attr( $attributes['profileLinkColor'] ); ?>">
									<?php esc_html_e( 'Website', 'metronet-profile-picture' ); ?>
									</a>
								</div><!-- .mpp-profile-link -->
							<?php endif; ?>
						</div><!-- .mpp-profile-meta -->
					<?php endif; /* End of profile theme */ ?>
					<?php if ( 'compact' === $attributes['theme'] ) : ?>
						<?php if ( $attributes['showName'] ) : ?>
						<h2 style="color: <?php echo esc_attr( $attributes['profileTextColor'] ); ?>; font-size: <?php echo esc_attr( $attributes['headerFontSize'] ) . 'px'; ?>"><?php echo wp_kses_post( $attributes['profileName'] ); ?></h2>
						<?php endif; ?>
						<div class="mpp-profile-image-wrapper">
							<div class="mpp-profile-image-square">
								<img src="<?php echo esc_url( $attributes['profileImgURL'] ); ?>" alt="avatar" class="profile-avatar" />
							</div>
						</div><!-- .mpp-profile-image-wrapper -->
						<?php if ( $attributes['showDescription'] ) : ?>
						<div class="mpp-profile-text">
							<?php echo wp_kses_post( $attributes['profileContent'] ); ?>
						</div><!-- .mpp-profile-text -->
						<?php endif; ?>
						<div class="mpp-compact-meta">
							<?php if ( $attributes['showViewPosts'] ) : ?>
								<div class="mpp-profile-view-posts" style="background: <?php echo esc_attr( $attributes['profileViewPostsBackgroundColor'] ); ?>; color: <?php echo esc_attr( $attributes['profileViewPostsTextColor'] ); ?>; width: 90%; margin: 0 auto 10px auto; font-size: <?php echo esc_attr( $attributes['buttonFontSize'] ); ?>px;">
									<a href="<?php echo esc_url( $attributes['profileURL'] ); ?>" style="color: <?php echo esc_attr( $attributes['profileViewPostsTextColor'] ); ?>; background: <?php echo esc_attr( $attributes['profileViewPostsBackgroundColor'] ); ?>;"><?php esc_html_e( 'View Posts', 'metronet-profile-picture' ); ?></a>
								</div><!-- .mpp-profile-view-posts -->
							<?php endif; ?>
							<?php if ( '' !== $attributes['website'] && $attributes['showWebsite'] ) : ?>
								<div class="mpp-profile-view-website" style="background: <?php echo esc_attr( $attributes['profileWebsiteBackgroundColor'] ); ?>; color: <?php echo esc_attr( $attributes['profileWebsiteTextColor'] ); ?>; width: 90%; margin: 0 auto 0 auto; font-size: <?php echo esc_attr( $attributes['buttonFontSize'] ); ?>px;">
									<a href="<?php echo esc_url( $attributes['website'] ); ?>" style="color: <?php echo esc_attr( $attributes['profileWebsiteTextColor'] ); ?>; background: <?php echo esc_attr( $attributes['profileWebsiteBackgroundColor'] ); ?>;"><?php esc_html_e( 'View Website', 'metronet-profile-picture' ); ?></a>
								</div><!-- .mpp-profile-view-posts -->
							<?php endif; ?>

						</div>
					<?php endif; /* Compact theme end */ ?>
					<?php if ( true === $attributes['showSocialMedia'] && ( 'regular' === $attributes['theme'] || 'compact' === $attributes['theme'] || 'profile' === $attributes['theme'] ) ) : ?>
						<?php echo $this->get_social_icons( $attributes ); // phpcs:ignore ?>
					<?php endif; ?>
				</div><!-- .mpp-profile-gutenberg-wrap -->
			</div><!-- .mpp-profile-wrap -->
		<?php endif; ?>
		<?php if ( 'tabbed' === $attributes['theme'] ) : ?>
		<style>
			.mpp-author-tabbed ul.mpp-author-tabs li.active:after {
				border-top: 10px solid <?php echo esc_attr( $attributes['profileTabColor'] ); ?>;
				border-top-color: <?php echo esc_attr( $attributes['profileTabColor'] ); ?>;
			}
			.mpp-author-tabbed ul.mpp-author-tabs li.mpp-tab-posts.active:after {
				border-top: 10px solid <?php echo esc_attr( $attributes['profileTabPostsColor'] ); ?>;
				border-top-color: <?php echo esc_attr( $attributes['profileTabPostsColor'] ); ?>;
			}
		</style>
		<div class="mpp-author-tabbed tabbed <?php echo esc_attr( $attributes['profileAvatarShape'] ); ?> mpp-block-profile">
			<ul class="mpp-author-tabs">
				<li class="mpp-tab-profile active mpp-gutenberg-tab" data-tab="mpp-profile-tab" style="background: <?php echo esc_attr( $attributes['profileTabColor'] ); ?>; color: <?php echo esc_attr( $attributes['profileTabTextColor'] ); ?>;">
				<?php echo wp_kses_post( $attributes['tabbedAuthorProfile'] ); ?>
				</li>
				<li class="mpp-tab-posts mpp-gutenberg-tab" data-tab="mpp-latestposts-tab" style="background: <?php echo esc_attr( $attributes['profileTabPostsColor'] ); ?>; color: <?php echo esc_attr( $attributes['profileTabPostsTextColor'] ); ?>;">
				<?php echo wp_kses_post( $attributes['tabbedAuthorLatestPosts'] ); ?>
				</li>
			</ul>
			<div class="mpp-tab-wrapper" style="<?php echo $attributes['padding'] > 0 ? 'padding: ' . esc_attr( $attributes['padding'] ) . 'px;' : ''; ?><?php echo $attributes['border'] > 0 ? 'border:' . esc_attr( $attributes['border'] ) . 'px solid ' . esc_attr( $attributes['borderColor'] ) . ';' : ''; ?><?php echo $attributes['borderRounded'] > 0 ? 'border-radius:' . esc_attr( $attributes['borderRounded'] ) . 'px;' : ''; ?>background-color: <?php echo esc_attr( $attributes['profileBackgroundColor'] ) . ';'; ?> color: <?php echo esc_attr( $attributes['profileTextColor'] ) . ';'; ?>">
				<div class="mpp-tab-active mpp-profile-tab mpp-tab">
				<div class="mpp-author-social-wrapper">
					<div class="mpp-author-heading">
						<div class="mpp-author-profile-heading" style="background: <?php echo esc_attr( $attributes['profileTabHeadlineColor'] ); ?>; color: <?php echo esc_attr( $attributes['profileTabHeadlineTextColor'] ); ?>;">
							<?php echo wp_kses_post( $attributes['tabbedAuthorProfileHeading'] ); ?>
						</div><!-- .mpp-author-heading -->
					</div><!-- .mpp-author-social-wrapper -->
					<?php if ( $attributes['showSocialMedia'] ) : ?>
						<div class="mpp-author-social">
						<?php echo $this->get_social_icons( $attributes ); // phpcs:ignore ?>
						</div>
					<?php endif; ?>
				</div><!-- .mpp-author-social-wrapper -->
				<div class="mpp-profile-image-wrapper">
					<div class="mpp-profile-image-square">
						<img class="profile-avatar" alt="avatar" src="<?php echo esc_url( $attributes['profileImgURL'] ); ?>">
						<div class="mpp-author-profile-sub-heading">
							<?php echo wp_kses_post( $attributes['tabbedAuthorSubHeading'] ); ?>
						</div>
					</div><!-- .mpp-profile-image-square -->
				</div><!-- .mpp-profile-image-wrapper -->
				<div class="mpp-tabbed-profile-information">
					<?php if ( $attributes['showTitle'] || '' !== $attributes['tabbedAuthorProfileTitle'] ) : ?>
						<?php echo '<div>' . wp_kses_post( $attributes['tabbedAuthorProfileTitle'] ) . '</div>'; ?>
					<?php endif; ?>
					<?php if ( $attributes['showName'] ) : ?>
					<h2 style="color: <?php echo esc_attr( $attributes['profileTextColor'] ); ?>; font-size: <?php echo esc_attr( $attributes['headerFontSize'] ) . 'px;'; ?>"><?php echo wp_kses_post( $attributes['profileName'] ); ?></h2>
					<?php endif; ?>
					<?php if ( $attributes['showDescription'] ) : ?>
					<div class="mpp-profile-text mt-font-size-<?php echo esc_attr( $attributes['profileFontSize'] ); ?>">
						<?php echo wp_kses_post( $attributes['profileContent'] ); ?>
					</div>
					<?php endif; ?>
				</div><!-- .mpp-tabbed-profile-information -->
				</div><!-- first profile tab -->
				<div class="mpp-tabbed-latest-posts mpp-latestposts-tab mpp-tab">
					<?php
					$user_id = $attributes['user_id'];
					$args    = array(
						'post_type'      => 'post',
						'post_status'    => 'publish',
						'author'         => $user_id,
						'orderby'        => 'date',
						'order'          => 'DESC',
						'posts_per_page' => 5,
					);
					$posts   = get_posts( $args );
					?>
					<ul class="mpp-author-tab-content <?php echo esc_attr( $attributes['profileLatestPostsOptionsValue'] ); ?>">
					<?php
					foreach ( $posts as $post ) {
						printf( "<li><a href='%s'>%s</a></li>", esc_url( get_permalink( $post->ID ) ), esc_html( $post->post_title ) );
					}
					?>
					</ul>
				</div><!-- .mpp-tabbed-latest-posts -->
			</div><!-- mpp-tab-wrapper -->
		</div><!-- .mpp-author-tabbed -->

		<?php endif; ?>
		<?php
		$this->load_gutenblock_svgs();
		return ob_get_clean();
	}

	/**
	 * Get the social icons for the block.
	 *
	 * @param array $attributes Gutenberg attributes.
	 */
	public function get_social_icons( $attributes ) {
		ob_start();
		?>
		<div class="mpp-social">
			<?php if ( ! empty( $attributes['socialFacebook'] ) ) : ?>
				<a href="<?php echo esc_url( $attributes['socialFacebook'] ); ?>">
					<svg class="icon icon-facebook" role="img" style="<?php echo 'custom' === $attributes['socialMediaOptions'] ? 'fill:' . esc_attr( $attributes['socialMediaColors'] ) . ';' : ''; ?>">
						<use href="#facebook"></use>
					</svg>
				</a>
			<?php endif; ?>
			<?php if ( ! empty( $attributes['socialTwitter'] ) ) : ?>
				<a href="<?php echo esc_url( $attributes['socialTwitter'] ); ?>">
					<svg class="icon icon-twitter" role="img" style="<?php echo 'custom' === $attributes['socialMediaOptions'] ? 'fill:' . esc_attr( $attributes['socialMediaColors'] ) . ';' : ''; ?>">
						<use href="#twitter"></use>
					</svg>
				</a>
			<?php endif; ?>
			<?php if ( ! empty( $attributes['socialInstagram'] ) ) : ?>
				<a href="<?php echo esc_url( $attributes['socialInstagram'] ); ?>">
					<svg class="icon icon-instagram" role="img" style="<?php echo 'custom' === $attributes['socialMediaOptions'] ? 'fill:' . esc_attr( $attributes['socialMediaColors'] ) . ';' : ''; ?>">
						<use href="#instagram"></use>
					</svg>
				</a>
			<?php endif; ?>
			<?php if ( ! empty( $attributes['socialPinterest'] ) ) : ?>
				<a href="<?php echo esc_url( $attributes['socialPinterest'] ); ?>">
					<svg class="icon icon-pinterest" role="img" style="<?php echo 'custom' === $attributes['socialMediaOptions'] ? 'fill:' . esc_attr( $attributes['socialMediaColors'] ) . ';' : ''; ?>">
						<use href="#pinterest"></use>
					</svg>
				</a>
			<?php endif; ?>
			<?php if ( ! empty( $attributes['socialLinkedIn'] ) ) : ?>
				<a href="<?php echo esc_url( $attributes['socialLinkedIn'] ); ?>">
					<svg class="icon icon-linkedin" role="img" style="<?php echo 'custom' === $attributes['socialMediaOptions'] ? 'fill:' . esc_attr( $attributes['socialMediaColors'] ) . ';' : ''; ?>">
						<use href="#linkedin"></use>
					</svg>
				</a>
			<?php endif; ?>
			<?php if ( ! empty( $attributes['socialYouTube'] ) ) : ?>
				<a href="<?php echo esc_url( $attributes['socialYouTube'] ); ?>">
					<svg class="icon icon-youtube" role="img" style="<?php echo 'custom' === $attributes['socialMediaOptions'] ? 'fill:' . esc_attr( $attributes['socialMediaColors'] ) . ';' : ''; ?>">
						<use href="#youtube"></use>
					</svg>
				</a>
			<?php endif; ?>
			<?php if ( ! empty( $attributes['socialGitHub'] ) ) : ?>
				<a href="<?php echo esc_url( $attributes['socialGitHub'] ); ?>">
					<svg class="icon icon-github" role="img" style="<?php echo 'custom' === $attributes['socialMediaOptions'] ? 'fill:' . esc_attr( $attributes['socialMediaColors'] ) . ';' : ''; ?>">
						<use href="#github"></use>
					</svg>
				</a>
			<?php endif; ?>
			<?php if ( ! empty( $attributes['socialWordPress'] ) ) : ?>
				<a href="<?php echo esc_url( $attributes['socialWordPress'] ); ?>">
					<svg class="icon icon-wordpress" role="img" style="<?php echo 'custom' === $attributes['socialMediaOptions'] ? 'fill:' . esc_attr( $attributes['socialMediaColors'] ) . ';' : ''; ?>">
						<use href="#wordpress"></use><?php // phpcs:ignore ?>
					</svg>
				</a>
			<?php endif; ?>
		</div><!-- .mpp-social -->
		<?php
		return ob_get_clean();
	}

	/**
	 * Add SVGs for the social icons in the block.
	 */
	public function load_gutenblock_svgs() {
		if ( '' !== get_post_type() ) {
			// Define SVG sprite file.
			$path      = '/img/social-logos.svg';
			$svg_icons = rtrim( dirname( plugin_dir_path( __FILE__ ) ), '/' );
			if ( ! empty( $path ) && is_string( $path ) ) {
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
				require $svg_icons;
				echo '</div>';
			}
		}
	}

	/**
	 * Add Gutenberg scripts.
	 */
	public function add_gutenberg_scripts() {

		wp_enqueue_script( 'mpp_gutenberg', Metronet_Profile_Picture::get_plugin_url( 'dist/blocks.build.js' ), array( 'wp-blocks', 'wp-element' ), METRONET_PROFILE_PICTURE_VERSION, true );

		/* For the Gutenberg plugin */
		if ( function_exists( 'wp_set_script_translations' ) ) {
			wp_set_script_translations( 'mpp_gutenberg', 'metronet-profile-picture' );
		} elseif ( function_exists( 'gutenberg_get_jed_locale_data' ) ) {
			$locale  = gutenberg_get_jed_locale_data( 'metronet-profile-picture' );
			$content = 'wp.i18n.setLocaleData( ' . wp_json_encode( $locale ) . ', "post-type-archive-mapping" );';
			wp_script_add_data( 'mpp_gutenberg', 'data', $content );
		} elseif ( function_exists( 'wp_get_jed_locale_data' ) ) {
			/* for 5.0 */
			$locale  = wp_get_jed_locale_data( 'metronet-profile-picture' );
			$content = 'wp.i18n.setLocaleData( ' . $locale . ', "metronet-profile-picture" );';
			wp_script_add_data( 'mpp_gutenberg', 'data', $content );
		}

		// Pass in REST URL.
		wp_localize_script(
			'mpp_gutenberg',
			'mpp_gutenberg',
			array(
				'rest_url'    => esc_url( rest_url( 'mpp/v2' ) ),
				'nonce'       => wp_create_nonce( 'wp_rest' ),
				'mystery_man' => Metronet_Profile_Picture::get_plugin_url( 'img/mystery.png' ),
			)
		);

		wp_enqueue_style( 'mpp_gutenberg', Metronet_Profile_Picture::get_plugin_url( '/dist/blocks.editor.build.css' ), array(), METRONET_PROFILE_PICTURE_VERSION, 'all' );
	}

	/**
	 * Add Gutenberg styles.
	 */
	public function add_gutenberg_styles() {
		if ( is_admin() ) {
			return;
		}
		// Ensure script debug allows non-minified scripts.
		wp_enqueue_style( 'mpp_gutenberg', Metronet_Profile_Picture::get_plugin_url( '/dist/blocks.style.build.css' ), array(), METRONET_PROFILE_PICTURE_VERSION, 'all' );
		wp_enqueue_script( 'mpp_gutenberg_tabs', Metronet_Profile_Picture::get_plugin_url( 'js/mpp-frontend.js' ), array( 'jquery' ), METRONET_PROFILE_PICTURE_VERSION, true );

	}
}
