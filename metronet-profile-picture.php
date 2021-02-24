<?php // phpcs:ignore
/*
Plugin Name: User Profile Picture
Plugin URI: http://wordpress.org/plugins/metronet-profile-picture/
Description: Use the native WP uploader on your user profile page.
Author: Cozmoslabs
Version: 3.0.0
Requires at least: 4.6
Author URI: https://www.cozmoslabs.com
Contributors: ronalfy
Text Domain: metronet-profile-picture
Domain Path: /languages
*/

define( 'METRONET_PROFILE_PICTURE_VERSION', '3.0.0' );
define( 'METRONET_PROFILE_PICTURE_PLUGIN_NAME', 'User Profile Picture' );
define( 'METRONET_PROFILE_PICTURE_DIR', plugin_dir_path( __FILE__ ) );
define( 'METRONET_PROFILE_PICTURE_URL', plugins_url( '/', __FILE__ ) );
define( 'METRONET_PROFILE_PICTURE_SLUG', plugin_basename( __FILE__ ) );
define( 'METRONET_PROFILE_PICTURE_FILE', __FILE__ );

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Setup the plugin auto loader.
require_once 'autoloader.php';

use MPP\Includes\Functions as Functions;
use MPP\Includes\Options as Options;

/**
 * Main Class for User Profile Picture
 *
 * Main class for user profile picture.
 *
 * @category User Profile Picture
 * @package  User Profile Picture
 * @author   Ronald Huereca <ronald@mediaron.com>
 * @license  GPL-2.0+
 * @link     https://github.com/madalinungureanu/user-profile-picture
 *
 * @since 1.0.0
 */
class Metronet_Profile_Picture {
	/**
	 * __construct()
	 *
	 * Class constructor
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Determine if to load Gutenberg or not.
		$options = Options::get_options();
		if ( 'on' === $options['load_gutenberg'] ) {
			// Include Gutenberg.
			add_filter( 'block_categories', array( $this, 'add_block_category' ), 10, 2 );
			new MPP\Includes\Blocks\Legacy\Blocks();
		}

		new \MPP\Includes\Ajax();
		new \MPP\Includes\Media_Restrict();
		new \MPP\Includes\Rest();
		new \MPP\Includes\Avatar_Overrides();
		new \MPP\Includes\Admin\Setup();
		new \MPP\Includes\Admin\Profile();
		new \MPP\Includes\Admin\User_List();
		new \MPP\Includes\Modules\Modules();

	} //end constructor

	/**
	 * Load plugin text domain.
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'metronet-profile-picture', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Add a User Profile Picture category for block creation.
	 *
	 * @since 2.3.0
	 *
	 * @param array  $categories Array of available categories.
	 * @param object $post Post to attach it to.
	 *
	 * @return array New Categories
	 */
	public function add_block_category( $categories, $post ) {
		return array_merge(
			$categories,
			array(
				array(
					'slug'  => 'mpp',
					'title' => __( 'Profiles', 'metronet-profile-picture' ),
					'icon'  => 'groups',
				),
			)
		);
	}
}

// instantiate the class.
global $mt_pp;
if ( class_exists( 'Metronet_Profile_Picture' ) ) {
	if ( get_bloginfo( 'version' ) >= '3.5' ) {
		add_action( 'plugins_loaded', 'mt_mpp_instantiate', 10 );
	}
}

/**
 * Instantiate User Profile Picture.
 */
function mt_mpp_instantiate() {
	global $mt_pp;
	$mt_pp = new Metronet_Profile_Picture();
	do_action( 'user_profile_picture_loaded' );
}
/**
 * Template tag for outputting a profile image.
 *
 * @param int   $user_id The user ID for the user to retrieve the image for.
 * @param mixed $args    Arguments for custom output.
 *   size - string || array (see get_the_post_thumbnail).
 *   attr - string || array (see get_the_post_thumbnail).
 *   echo - bool (true or false) - whether to echo the image or return it.
 */
function mt_profile_img( $user_id, $args = array() ) {
	$profile_post_id = absint( get_user_option( 'metronet_post_id', $user_id ) );

	if ( 0 === $profile_post_id || 'mt_pp' !== get_post_type( $profile_post_id ) ) {
		return false;
	}

	$defaults = array(
		'size' => 'thumbnail',
		'attr' => '',
		'echo' => true,
	);
	$args     = wp_parse_args( $args, $defaults );
	extract( $args ); // phpcs:ignore

	$post_thumbnail_id = get_post_thumbnail_id( $profile_post_id );

	// Return false or echo nothing if there is no post thumbnail.
	if ( ! $post_thumbnail_id ) {
		if ( $echo ) {
			echo '';
		} else {
			return false;
		}
		return;
	}

	// Implode Classes if set and array - dev note: edge case.
	if ( is_array( $attr ) && isset( $attr['class'] ) ) {
		if ( is_array( $attr['class'] ) ) {
			$attr['class'] = implode( ' ', $attr['class'] );
		}
	}

	$post_thumbnail = wp_get_attachment_image( $post_thumbnail_id, $size, false, $attr );

	/**
	 * Filter outputted HTML.
	 *
	 * Filter outputted HTML.
	 *
	 * @param string $post_thumbnail       img tag with formed HTML.
	 * @param int    $profile_post_id      The profile in which the image is attached.
	 * @param int    $profile_thumbnail_id The thumbnail ID for the attached image.
	 * @param int    $user_id              The user id for which the image is attached.
	 */
	$post_thumbnail = apply_filters( 'mpp_thumbnail_html', $post_thumbnail, $profile_post_id, $post_thumbnail_id, $user_id );
	if ( $echo ) {
		echo wp_kses_post( $post_thumbnail );
	} else {
		return $post_thumbnail;
	}
} //end mt_profile_img

/**
 * Adds a profile author box
 *
 * @since 2.2.0
 *
 * @param int   $user_id    The user ID for the user to retrieve the profile for.
 * @param array $attributes See defaults in function for all attributes.
 *
 * @return string User profile box if user exists
 */
function mt_author_box( $user_id = 0, $attributes = array() ) {
	$user = get_user_by( 'id', $user_id );
	if ( false === $user ) {
		return '';
	}
	$defaults = array(
		'theme'                           => 'regular', /* Can be regular, compact, profile, or tabbed */
		'profileAvatarShape'              => 'square', /* Can be 'square' or 'rounded' */
		'padding'                         => 10,
		'border'                          => 1,
		'borderRounded'                   => 5,
		'borderColor'                     => '#000000',
		'profileBackgroundColor'          => '#FFFFFF',
		'profileTextColor'                => '#000000',
		'showName'                        => true,
		'showTitle'                       => false,
		'fontSize'                        => 18,
		'profileName'                     => $user->data->display_name,
		'profileTitle'                    => '',
		'avatarSize'                      => 150,
		'profileImgURL'                   => get_avatar_url( $user_id, isset( $attributes['avatarSize'] ) ? $attributes['avatarSize'] : 150 ),
		'headerFontSize'                  => 24,
		'showDescription'                 => true,
		'showSocialMedia'                 => true,
		'profileContent'                  => get_user_meta( $user_id, 'description', true ),
		'profileFontSize'                 => 18,
		'showViewPosts'                   => true,
		'profileURL'                      => get_author_posts_url( $user_id ),
		'website'                         => '', /* Needs to be a URl */
		'showWebsite'                     => false,
		'showPostsWidth'                  => '100%', /* ignored if website is not empty and true */
		'profileViewPostsBackgroundColor' => '#cf6d38',
		'profileViewPostsTextColor'       => '#FFFFFF',
		'buttonFontSize'                  => 16,
		'profileWebsiteBackgroundColor'   => '#333333',
		'profileWebsiteTextColor'         => '#FFFFFF',
		'profileLinkColor'                => '#000000',
		'showSocialMedia'                 => false,
		'socialWordPress'                 => '',
		'socialFacebook'                  => '',
		'socialTwitter'                   => '',
		'socialInstagram'                 => '',
		'socialPinterest'                 => '',
		'socialLinkedIn'                  => '',
		'socialYouTube'                   => '',
		'socialGitHub'                    => '',
		'socialMediaOptions'              => 'brand', /* can be brand or custom */
		'socialMediaColors'               => '#000000', /* Only applicable if socialMediaOptions is custom */
		'profileCompactAlignment'         => 'center', // Can be left, center, or right.
		/* Tabbed Attributes */
		'tabbedAuthorProfileTitle'        => '',
		'tabbedAuthorSubHeading'          => '',
		'tabbedAuthorProfile'             => __( 'Author', 'metronet-profile-picture' ),
		'tabbedAuthorLatestPosts'         => __( 'Latest Posts', 'metronet-profile-picture' ),
		'tabbedAuthorProfileHeading'      => __( 'Author Information', 'metronet-profile-picture' ),
		'profileLatestPostsOptionsValue'  => 'white', /* can be none, white, light, black, magenta, blue, green */
		'profileTabColor'                 => '#333333',
		'profileTabPostsColor'            => '#333333',
		'profileTabHeadlineColor'         => '#333333',
		'profileTabHeadlineTextColor'     => '#FFFFFF',
		'profileTabTextColor'             => '#FFFFFF',
		'profileTabPostsTextColor'        => '#FFFFFF',

	);
	$attributes = wp_parse_args( $attributes, $defaults );
	$min_or_not = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
	if ( 'regular' === $attributes['theme'] || 'compact' === $attributes['theme'] || 'profile' === $attributes['theme'] ) :
		?>
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
										<div class="mpp-profile-view-posts" style="background-color: <?php echo esc_attr( $attributes['profileViewPostsBackgroundColor'] ); ?>; color: <?php echo esc_attr( $attributes['profileViewPostsTextColor'] ); ?>; <?php ( '' !== $attributes['website'] && $attributes['showWebsite'] ) ? '' : 'width:' . esc_attr( $attributes['showPostsWidth'] ) . ';'; ?> font-size: <?php echo esc_attr( $attributes['buttonFontSize'] ); ?>px;">
											<a href="<?php echo esc_url( $attributes['profileURL'] ); ?>" style="background: <?php echo esc_attr( $attributes['profileViewPostsBackgroundColor'] ); ?>; color: <?php echo esc_attr( $attributes['profileViewPostsTextColor'] ); ?>">
											<?php esc_html_e( 'View Posts', 'metronet-profile-picture' ); ?></a>
										</div><!-- .mpp-profile-view-posts -->
									<?php endif; ?>
									<?php if ( '' !== $attributes['website'] && $attributes['showWebsite'] ) : ?>
									<div class="mpp-profile-view-website" style="background: <?php echo esc_attr( $attributes['profileWebsiteBackgroundColor'] ); ?>;color: <?php echo esc_attr( $attributes['profileWebsiteTextColor'] ); ?>; font-size: <?php echo esc_attr( $attributes['buttonFontSize'] ); ?>px;">
										<a href="<?php echo esc_url( $attributes['website'] ); ?>" style="background: <?php echo esc_attr( $attributes['profileWebsiteBackgroundColor'] ); ?>; color: <?php echo esc_attr( $attributes['profileWebsiteTextColor'] ); ?>;"><?php esc_html_e( 'View Website', 'metronet-profile-picture' ); ?></a>
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
						<div class="mpp-profile-meta" style="font-size: <?php echo esc_attr( $attributes['fontSize'] ); ?>px;">
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
						<?php echo mpp_get_social_icons( $attributes ); // phpcs:ignore ?>
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
						<?php echo mpp_get_social_icons( $attributes ); // phpcs:ignore ?>
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
					$args  = array(
						'post_type'      => 'post',
						'post_status'    => 'publish',
						'author'         => $user_id,
						'orderby'        => 'date',
						'order'          => 'DESC',
						'posts_per_page' => 5,
					);
					$posts = get_posts( $args );
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
		wp_enqueue_style( 'mpp_gutenberg', Metronet_Profile_Picture::get_plugin_url( '/css/front-end-gutenberg.css' ), array(), METRONET_PROFILE_PICTURE_VERSION, 'all' );
		wp_enqueue_script( 'mpp_gutenberg_tabs', Metronet_Profile_Picture::get_plugin_url( 'js/mpp-frontend' . $min_or_not . '.js' ), array( 'jquery' ), METRONET_PROFILE_PICTURE_VERSION, true );
		add_action( 'wp_footer', 'mpp_load_gutenblock_svgs' );
		echo ob_get_clean(); // phpcs:ignore
}
/**
 * Get social icons based on passed attributes
 *
 * @see mt_author_box for attribute valies
 *
 * @since 2.2.0
 *
 * @param array $attributes See defaults in function mt_author_box for all attributes.
 *
 * @return string User social icons
 */
function mpp_get_social_icons( $attributes ) {
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
 * Load social icons in footer of theme
 *
 * @since 2.2.0
 */
function mpp_load_gutenblock_svgs() {
	/**
	 * Allow other plugins to run code from inside this SVG block.
	 *
	 * @since 2.3.0
	 */
	do_action( 'mpp_svg_start' );
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
	/**
	 * Allow other plugins to run code from inside this SVG block at the end.
	 *
	 * @since 2.3.0
	 */
	do_action( 'mpp_svg_end' );
}
