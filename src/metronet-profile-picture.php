<?php
/*
Plugin Name: User Profile Picture
Plugin URI: http://wordpress.org/extend/plugins/metronet-profile-picture/
Description: Use the native WP uploader on your user profile page.
Author: Ronald Huereca
Version: 2.1.3
Requires at least: 3.5
Author URI: https://www.mediaron.com
Contributors: ronalfy
Text Domain: metronet-profile-picture
Domain Path: /languages
*/
define( 'METRONET_PROFILE_PICTURE_VERSION', '2.1.3' );
class Metronet_Profile_Picture	{

	//private
	private $plugin_url = '';
	private $plugin_dir = '';
	private $plugin_path = '';

	/**
	* __construct()
	*
	* Class constructor
	*
	*/
	function __construct(){

		//* Localization Code */
		load_plugin_textdomain( 'metronet-profile-picture', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

		$this->plugin_path = plugin_basename( __FILE__ );
		$this->plugin_url = rtrim( plugin_dir_url(__FILE__), '/' );
		$this->plugin_dir = rtrim( plugin_dir_path(__FILE__), '/' );

		add_action( 'init', array( &$this, 'init' ) );
		add_action( 'personal_options', array( &$this, 'insert_upload_form' ) );

		//Scripts
		add_action( 'admin_print_scripts-user-edit.php', array( &$this, 'print_media_scripts' ) );
		add_action( 'admin_print_scripts-profile.php', array( &$this, 'print_media_scripts' ) );

		add_action( 'wp_enqueue_scripts', array( &$this, 'profile_print_media_scripts' ), 9 );
		add_action( 'acf/input/admin_enqueue_scripts', array( &$this, 'profile_print_media_scripts' ), 9 ); //Advanced Custom Field compatibility

		//Styles
		add_action( 'admin_print_styles-user-edit.php', array( &$this, 'print_media_styles' ) );
		add_action( 'admin_print_styles-profile.php', array( &$this, 'print_media_styles' ) );

		//Ajax
		add_action( 'wp_ajax_metronet_add_thumbnail', array( &$this, 'ajax_add_thumbnail' ) );
		add_action( 'wp_ajax_metronet_get_thumbnail', array( &$this, 'ajax_get_thumbnail' ) );
		add_action( 'wp_ajax_metronet_remove_thumbnail', array( &$this, 'ajax_remove_thumbnail' ) );

		//User update action
		add_action( 'edit_user_profile_update', array( &$this, 'save_user_profile' ) );
		add_action( 'personal_options_update', array( &$this, 'save_user_profile' ) );


		//User Avatar override
		add_filter( 'get_avatar', array( &$this, 'avatar_override' ), 10, 6 );
		add_filter( 'pre_get_avatar_data', array( $this, 'pre_avatar_override' ), 10, 2 );

		//Rest API
		add_action( 'rest_api_init', array( $this, 'rest_api_register' ) );

		//Avatar check overridden - Can be overridden using a higher priority
		add_filter( 'mpp_hide_avatar_override', '__return_true', 5 );

		// Include Gutenberg
		include_once self::get_plugin_dir('/gutenberg/class-gutenberg.php');
		new Metronet_Profile_Picture_Gutenberg();
	} //end constructor

	/**
	* ajax_add_thumbnail()
	*
	* Adds a thumbnail to user meta and returns thumbnail html
	*
	*/
	public function ajax_add_thumbnail() {
		if ( !current_user_can( 'upload_files' ) ) die( '' );
		$post_id = isset( $_POST[ 'post_id' ] ) ? absint( $_POST[ 'post_id' ] ) : 0;
		$user_id = isset( $_POST[ 'user_id' ] ) ? absint( $_POST[ 'user_id' ] ) : 0;
		$thumbnail_id = isset( $_POST[ 'thumbnail_id' ] ) ? absint( $_POST[ 'thumbnail_id' ] ) : 0;
		if ( $post_id == 0 || $user_id == 0 || $thumbnail_id == 0 || 'mt_pp' !== get_post_type( $post_id ) ) die( '' );
		check_ajax_referer( "mt-update-post_$post_id" );

		//Save user meta
		update_user_option( $user_id, 'metronet_post_id', $post_id );
		update_user_option( $user_id, 'metronet_image_id', $thumbnail_id ); //Added via this thread (Props Solinx) - https://wordpress.org/support/topic/storing-image-id-directly-as-user-meta-data
		set_post_thumbnail( $post_id, $thumbnail_id );

		if ( has_post_thumbnail( $post_id ) ) {
			$thumb_src = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'thumbnail' , false, '' );
			$post_thumbnail = sprintf( '<img src="%s" width="150" height="150" title="%s" />', esc_url( $thumb_src[0] ), esc_attr__( "Upload or Change Profile Picture", 'metronet-profile-picture' ) );
			$crop_html = $this->get_post_thumbnail_editor_link( $post_id );
			$thumb_html = sprintf( '<a href="#" class="mpp_add_media">%s%s</a>', $post_thumbnail, sprintf( '<div id="metronet-click-edit">%s</div>', esc_html__( 'Click to Edit', 'metronet-profile-picture' ) ) );
			$thumb_html .= sprintf( '<a id="metronet-remove" class="dashicons dashicons-trash" href="#" title="%s">%s</a>', esc_attr__( 'Remove profile image', 'metronet-profile-picture' ), esc_html__( "Remove profile image", "metronet-profile-picture" ) );
			wp_send_json( array(
				'thumb_html'          => $thumb_html,
				'crop_html'           => $crop_html,
				'has_thumb'           => true,
				'avatar'              => get_avatar( $user_id, 96 ),
				'avatar_admin_small'  => get_avatar( $user_id, 26 ),
				'avatar_admin_medium' => get_avatar( $user_id, 64 ),
				'user_id'             => $user_id,
				'logged_in_user_id'   => get_current_user_id(),
			) );
		}
		wp_send_json( array(
				'thumb_html'          => '',
				'crop_html'           => '',
				'has_thumb'           => false,
				'avatar'              => get_avatar( $user_id, 96 ),
				'avatar_admin_small'  => get_avatar( $user_id, 26 ),
				'avatar_admin_medium' => get_avatar( $user_id, 64 ),
				'user_id'             => $user_id,
				'logged_in_user_id'   => get_current_user_id(),
		) );
	} //end ajax_add_thumbnail

	/**
	* ajax_get_thumbnail()
	*
	* Retrieves a thumbnail based on a passed post id ($_POST)
	*
	*/
	public function ajax_get_thumbnail() {
		if ( !current_user_can( 'upload_files' ) ) die( '' );
		$post_id = isset( $_POST[ 'post_id' ] ) ? absint( $_POST[ 'post_id' ] ) : 0;
		$post = get_post( $post_id );
		$user_id = 0;
		if( $post ) {
			$user_id = $post->post_author;
		}


		if ( has_post_thumbnail( $post_id ) ) {
			$thumb_src = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'thumbnail' , false, '' );
			$post_thumbnail = sprintf( '<img style="display:block" src="%s" width="150" height="150" title="%s" />', esc_url( $thumb_src[0] ), esc_attr__( "Upload or Change Profile Picture", 'metronet-profile-picture' ) );
			$crop_html = $this->get_post_thumbnail_editor_link( $post_id );
			$thumb_html = sprintf( '<a href="#" class="mpp_add_media">%s%s</a>', $post_thumbnail, sprintf( '<div id="metronet-click-edit">%s</div>', esc_html__( 'Click to Edit', 'metronet-profile-picture' ) ) );
			$thumb_html .= sprintf( '<a id="metronet-remove" class="dashicons dashicons-trash" href="#" title="%s">%s</a>', esc_attr__( 'Remove profile image', 'metronet-profile-picture' ), esc_html__( "Remove profile image", "metronet-profile-picture" ) );
			wp_send_json( array(
				'thumb_html'          => $thumb_html,
				'crop_html'           => $crop_html,
				'has_thumb'           => true,
				'avatar'              => get_avatar( $user_id, 96 ),
				'avatar_admin_small'  => get_avatar( $user_id, 26 ),
				'avatar_admin_medium' => get_avatar( $user_id, 64 ),
				'user_id'             => $user_id,
				'logged_in_user_id'   => get_current_user_id(),
			) );
		} else {
			$thumb_html = '<a style="display:block" href="#" class="mpp_add_media default-image">';
			$thumb_html.= sprintf( '<img style="display:block" src="%s" width="150" height="150" title="%s" />', self::get_plugin_url( 'img/mystery.png' ), esc_attr__( "Upload or Change Profile Picture", 'metronet-profile-picture' ) );
			$thumb_html .= sprintf( '<div id="metronet-click-edit">%s</div>', esc_html__( 'Click to Edit', 'metronet-profile-picture' ) );
			$thumb_html .= '</a>';
		}
		wp_send_json( array(
			'thumb_html'          => $thumb_html,
			'crop_html'           => '',
			'has_thumb'           => false,
			'avatar'              => get_avatar( $user_id, 96 ),
			'avatar_admin_small'  => get_avatar( $user_id, 26 ),
			'avatar_admin_medium' => get_avatar( $user_id, 64 ),
			'user_id'             => $user_id,
			'logged_in_user_id'   => get_current_user_id(),
		) );
	} //end ajax_get_thumbnail

	/**
	* ajax_remove_thumbnail()
	*
	* Removes a featured thumbnail
	*
	*/
	public function ajax_remove_thumbnail() {
		if ( !current_user_can( 'upload_files' ) ) die( '' );
		$post_id = isset( $_POST[ 'post_id' ] ) ? absint( $_POST[ 'post_id' ] ) : 0;
		$user_id = isset( $_POST[ 'user_id' ] ) ? absint( $_POST[ 'user_id' ] ) : 0;
		if ( $post_id == 0 || $user_id == 0 ) die( '' );
		check_ajax_referer( "mt-update-post_$post_id" );

		$thumb_html = '<a style="display:block" href="#" class="mpp_add_media default-image">';
		$thumb_html.= sprintf( '<img style="display:block" src="%s" width="150" height="150" title="%s" />', self::get_plugin_url( 'img/mystery.png' ), esc_attr__( "Upload or Change Profile Picture", 'metronet-profile-picture' ) );
		$thumb_html .= sprintf( '<div id="metronet-click-edit">%s</div>', esc_html__( 'Click to Edit', 'metronet-profile-picture' ) );
		$thumb_html .= '</a>';

		//Save user meta and update thumbnail
		update_user_option( $user_id, 'metronet_image_id', 0 );
		delete_post_meta( $post_id, '_thumbnail_id' );
		wp_send_json( array(
			'thumb_html'          => $thumb_html,
			'crop_html'           => '',
			'has_thumb'           => false,
			'avatar'              => get_avatar( $user_id, 96 ),
			'avatar_admin_small'  => get_avatar( $user_id, 26 ),
			'avatar_admin_medium' => get_avatar( $user_id, 64 ),
			'user_id'             => $user_id,
			'logged_in_user_id'   => get_current_user_id(),
		) );
	}

	/**
	* avatar_override()
	*
	* Overrides an avatar with a profile image
	*
	* @param string $avatar SRC to the avatar
	* @param mixed $id_or_email
	* @param int $size Size of the image
	* @param string $default URL to the default image
	* @param string $alt Alternative text
	**/
	public function avatar_override( $avatar, $id_or_email, $size, $default, $alt, $args = array() ) {
		global $pagenow;
		if ( 'options-discussion.php' == $pagenow ) return $avatar; //Stop overriding gravatars on options-discussion page

		//Get user data
		if ( is_numeric( $id_or_email ) ) {
			$user = get_user_by( 'id', ( int )$id_or_email );
		} elseif( is_object( $id_or_email ) )  {
			$comment = $id_or_email;
			if ( empty( $comment->user_id ) ) {
				$user = get_user_by( 'id', $comment->user_id );
			} else {
				$user = get_user_by( 'email', $comment->comment_author_email );
			}
			if ( !$user ) return $avatar;
		} elseif( is_string( $id_or_email ) ) {
			$user = get_user_by( 'email', $id_or_email );
		} else {
			return $avatar;
		}
		if ( !$user ) return $avatar;
		$user_id = $user->ID;

		//Determine if user has an avatar override
		$avatar_override = get_user_option( 'metronet_avatar_override', $user_id );
		if ( !$avatar_override || $avatar_override != 'on' ) return $avatar;

		//Build classes array based on passed in args, else set defaults - see get_avatar in /wp-includes/pluggable.php
		$classes = array(
			'avatar',
			sprintf( 'avatar-%s', esc_attr( $size ) ),
			'photo'
		);
		if ( isset( $args[ 'class' ] ) ) {
			if ( is_array( $args['class'] ) ) {
				$classes = array_merge( $classes, $args['class'] );
			} else {
				$args[ 'class' ] = explode( ' ', $args[ 'class' ] );
				$classes = array_merge( $classes, $args[ 'class' ] );
			}
		}

		//Get custom filter classes
		$classes = (array)apply_filters( 'mpp_avatar_classes', $classes );

		//Determine if the user has a profile image
		$custom_avatar = mt_profile_img( $user_id, array(
			'size' => array( $size, $size ),
			'attr' => array( 'alt' => $alt, 'class' => implode( ' ', $classes ) ),
			'echo' => false )
		);

		if ( ! $custom_avatar ) return $avatar;
		return $custom_avatar;
	} //end avatar_override

	/**
	 * pre_avatar_override()
	 *
	 * Overrides an avatar with a profile image
	 *
	 * @param array $args Arguments to determine the avatar dimensions
	 * @param mixed $id_or_email
	 * @return array $args Overridden URL or default if none can be found
	 **/
	public function pre_avatar_override( $args, $id_or_email ) {

		//Get user data
		if ( is_numeric( $id_or_email ) ) {
			$user = get_user_by( 'id', ( int )$id_or_email );
		} elseif( is_object( $id_or_email ) )  {
			$comment = $id_or_email;
			if ( empty( $comment->user_id ) ) {
				$user = get_user_by( 'id', $comment->user_id );
			} else {
				$user = get_user_by( 'email', $comment->comment_author_email );
			}
			if ( !$user ) return $args;
		} elseif( is_string( $id_or_email ) ) {
			$user = get_user_by( 'email', $id_or_email );
		} else {
			return $args;
		}
		if ( ! $user ) return $args;
		$user_id = $user->ID;

		// Get the post the user is attached to
		$size = $args[ 'size' ];

		$profile_post_id = absint( get_user_option( 'metronet_post_id', $user_id ) );
		if ( $profile_post_id == 0 ) {
			return $args;
		}
		$post_thumbnail_id = get_post_thumbnail_id( $profile_post_id );

		// Attempt to get the image in the right size
		$avatar_image = get_the_post_thumbnail_url( $profile_post_id, array( $size, $size ) );
		if ( empty( $avatar_image ) ) {
			return $args;
		}
		$args[ 'url' ] = $avatar_image;
		return $args;
	}

	/**
	* get_plugin_dir()
	*
	* Returns an absolute path to a plugin item
	*
	* @param		string    $path	Relative path to make absolute (e.g., /css/image.png)
	* @return		string               An absolute path (e.g., /htdocs/ithemes/wp-content/.../css/image.png)
	*/
	public static function get_plugin_dir( $path = '' ) {
		$dir = rtrim( plugin_dir_path(__FILE__), '/' );
		if ( !empty( $path ) && is_string( $path) )
			$dir .= '/' . ltrim( $path, '/' );
		return $dir;
	}


	/**
	* get_plugin_url()
	*
	* Returns an absolute url to a plugin item
	*
	* @param		string    $path	Relative path to plugin (e.g., /css/image.png)
	* @return		string               An absolute url (e.g., http://www.domain.com/plugin_url/.../css/image.png)
	*/
	public static function get_plugin_url( $path = '' ) {
		$dir = rtrim( plugin_dir_url(__FILE__), '/' );
		if ( !empty( $path ) && is_string( $path) )
			$dir .= '/' . ltrim( $path, '/' );
		return $dir;
	}

	/**
	* get_post_id
	*
	* Gets a post id for the user - Creates a post if a post doesn't exist
	*
	@param int user_id User ID of the user
	@return int post_id
	*/
	private function get_post_id( $user_id = 0 ) {
		//Get/Create Profile Picture Post
		$post_args = array(
			'post_type' => 'mt_pp',
			'author' => $user_id,
			'post_status' => 'publish'
		);
		$posts = get_posts( $post_args );
		if ( !$posts ) {
			$post_id = wp_insert_post( array(
				'post_author' => $user_id,
				'post_type' => 'mt_pp',
				'post_status' => 'publish',
			) );
		} else {
			$post = end( $posts );
			$post_id = $post->ID;
		}
		return $post_id;
	} //end get_post_id

	/**
	* get_post_thumbnail_editor_link
	*
	* Retrieve a crop-image link (HTML) based on the passed post_id
	*
	@param int post_id Post ID to find the featured image for
	@return string html
	*/
	private function get_post_thumbnail_editor_link( $post_id ) {
		ob_start();
		if ( has_post_thumbnail( $post_id ) && defined( 'PTE_VERSION' ) ) {
			//Post Thumbnail Editor compatibility - http://wordpress.org/extend/plugins/post-thumbnail-editor/
			$post_thumbnail_id = get_post_meta( $post_id, '_thumbnail_id', true );
			$pte_url = add_query_arg( array(
				'page' => 'pte-edit',
				'pte-id' => $post_thumbnail_id
			), admin_url('upload.php') );
			printf( ' - <a href="%s">%s</a>', $pte_url, __( 'Crop Thumbnail', 'metronet-profile-picture' ) );
		} //end post thumbnail editor
		return ob_get_clean();
	} //end get_post_thumbnail_editor_link

	/**
	* get_user_id
	*
	* Gets a user ID for the user
	*
	*@return int user_id
	*
	@return int post_id
	*/
	private function get_user_id() {
		//Get user ID
		$user_id = isset( $_GET[ 'user_id' ] ) ? absint( $_GET[ 'user_id' ] ) : 0;
		if ( $user_id == 0 && IS_PROFILE_PAGE ) {
			$current_user = wp_get_current_user();
			$user_id = $current_user->ID;
		}
		return $user_id;
	} //end get_user_id

	/**
	* init()
	*
	* Initializes plugin localization, post types, updaters, plugin info, and adds actions/filters
	*
	*/
	public function init() {

		add_theme_support( 'post-thumbnails' ); //This should be part of the theme, but the plugin registers it just in case.
		//Register post types
		$post_type_args = array(
			'public' => false,
			'publicly_queryable' => false,
			'show_ui' => false,
			'show_in_menu' => false,
			'query_var' => true,
			'rewrite' => false,
			'has_archive' => false,
			'hierarchical' => false,
			'supports' => array( 'thumbnail' )
		);
		register_post_type( 'mt_pp', $post_type_args );
		add_image_size( 'profile_24', 24, 24, true );
		add_image_size( 'profile_48', 48, 48, true );
		add_image_size( 'profile_96', 96, 96, true );

	}//end function init



	/**
	* insert_upload_form
	*
	* Adds an upload form to the user profile page and outputs profile image if there is one
	*/
	public function insert_upload_form() {
		if ( !current_user_can( 'upload_files' ) ) return; //Users must be author or greater

		$user_id = $this->get_user_id();
		$post_id = $this->get_post_id( $user_id );

		?>
		<tr valign="top">
			<th scope="row"><?php esc_html_e( "Profile Image", "metronet-profile-picture" ); ?></th>
			<td id="mpp">
				<input type="hidden" name="metronet_profile_id" id="metronet_profile_id" value="<?php echo esc_attr( $user_id ); ?>" />
				<input type="hidden" name="metronet_post_id" id="metronet_post_id" value="<?php echo esc_attr( $post_id ); ?>" />
				<div id="metronet-profile-image">
				<?php
					$has_profile_image = false;
					if ( has_post_thumbnail( $post_id ) ) {
						$has_profile_image = true;
						echo '<a style="display:block" href="#" class="mpp_add_media">';
						$thumb_src = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'thumbnail' , false, '' );
						$post_thumbnail = sprintf( '<img style="display:block" src="%s" width="150" height="150" title="%s" />', esc_url( $thumb_src[0] ), esc_attr__( "Upload or Change Profile Picture", 'metronet-profile-picture' ) );
						echo $post_thumbnail;
						echo sprintf( '<div id="metronet-click-edit">%s</div>', esc_html__( 'Click to Edit', 'metronet-profile-picture' ) );
						echo '</a>';
					} else {
						echo '<a style="display:block" href="#" class="mpp_add_media default-image">';
						$post_thumbnail = sprintf( '<img style="display:block" src="%s" width="150" height="150" title="%s" />', self::get_plugin_url( 'img/mystery.png' ), esc_attr__( "Upload or Change Profile Picture", 'metronet-profile-picture' ) );
						echo $post_thumbnail;
						echo sprintf( '<div id="metronet-click-edit">%s</div>', esc_html__( 'Click to Edit', 'metronet-profile-picture' ) );
						echo '</a>';
					}
					$remove_classes = array( 'dashicons', 'dashicons-trash' );
					if ( !$has_profile_image ) {
						$remove_classes[] = 'mpp-no-profile-image';
					}
				?>
					<a id="metronet-remove" class="<?php echo implode( ' ', $remove_classes ); ?>" href="#" title="<?php esc_attr_e( 'Remove profile image', 'metronet-profile-picture' ); ?>"><?php esc_html_e( "Remove profile image", "metronet-profile-picture" );?></a>
					<div style="display: none">
						<?php printf( '<img class="mpp-loading" width="150" height="150" alt="Loading" src="%s" />', esc_url( self::get_plugin_url( '/img/loading.gif' ) ) ); ?>
					</div>
				</div><!-- #metronet-profile-image -->
				<div id="metronet-override-avatar">
					<input type="hidden" name="metronet-user-avatar" value="off" />
					<?php
					//Get the user avatar override option - If not set, see if there's a filter override.
					$user_avatar_override = get_user_option( 'metronet_avatar_override', $user_id );
					$checked = '';
					if ( $user_avatar_override ) {
						$checked = checked( 'on', $user_avatar_override, false );
					} else {
						$checked = checked( true, apply_filters( 'mpp_avatar_override', false ), false );
					}

					//Filter for hiding the override interface.  If this option is set to true, the mpp_avatar_override filter is ignored and override is enabled by default
					$hide_override = apply_filters( 'mpp_hide_avatar_override', false );
					if ( $hide_override ):
					?>
					<input type="hidden" name="metronet-user-avatar" id="metronet-user-avatar" value="on"  />
					<?php
					else:
					?>
					<br /><input type="checkbox" name="metronet-user-avatar" id="metronet-user-avatar" value="on" <?php echo $checked; ?> /><label for="metronet-user-avatar"> <?php esc_html_e( "Override Avatar?", "metronet-profile-picture" ); ?></label>
					<?php endif; ?>
				</div><!-- #metronet-override-avatar -->
			</td>
		</tr>
		<?php
	} //end insert_upload_form

	function profile_print_media_scripts() {
		if ( defined( 'IS_PROFILE_PAGE' ) && IS_PROFILE_PAGE == true ) {
			$this->print_media_scripts();
		}
	}
	/**
	* print_media_scripts
	*
	* Output media scripts for thickbox and media uploader
	**/
	public function print_media_scripts() {
		$post_id = $this->get_post_id( $this->get_user_id() );
		wp_enqueue_media( array( 'post' => $post_id ) );
		$script_deps = array( 'media-editor' );
		wp_enqueue_script( 'mt-pp', self::get_plugin_url( '/js/mpp.js' ), $script_deps, METRONET_PROFILE_PICTURE_VERSION, true );
		wp_localize_script( 'mt-pp', 'metronet_profile_image',
			array(
				'set_profile_text'    => __( 'Set Profile Image', 'metronet-profile-picture' ),
				'remove_profile_text' => __( 'Remove Profile Image', 'metronet-profile-picture' ),
				'crop'                => __( 'Crop Thumbnail', 'metronet-profile-picture' ),
				'ajax_url'            => esc_url( admin_url( 'admin-ajax.php' ) ),
				'user_post_id'        => absint( $post_id ),
				'nonce'               => wp_create_nonce( 'mt-update-post_' . absint( $post_id ) ),
				'loading_gif'         => esc_url( self::get_plugin_url( '/img/loading.gif' ) )
			)
		);
		?>
		<style>
		/* Metronet Profile Picture */
		#metronet-profile-image {
			position: relative;
			float: left;
		}
		#metronet-profile-image a.mpp_add_media #metronet-click-edit,
		#metronet-profile-image a.mpp_add_media:hover #metronet-click-edit,
		#metronet-profile-image a.mpp_add_media:visited #metronet-click-edit {
			color: #FFF;
		}
		#metronet-profile-image a.mpp_add_media:hover #metronet-click-edit {
			background: #000;
			background: rgba(51,51,51,1);
			font-weight: normal;
		}
		#metronet-click-edit {
			position: absolute;
			bottom: 0;
			left: 0;
			width: 100%;
			background: #333;
			background: rgba(51,51,51,0.5);
			font-size: 14px;
			line-height: 14px;
			text-align: center;
			padding: 8px 0;
		}
		#metronet-remove {
			position: absolute;
			background: #424242;
			top: 0;
			right: 0;
			display: block;
			padding: 3px;
			width: 20px;
			height: 20px;
			overflow: hidden;
		}
		#metronet-remove:before {
			content: "\f182";
			color: #fd6a6a;
			font-size: 20px;
			margin-right:20px;
		}
		#metronet-remove:hover:before {
			color: #ff3e3e;
		}
		#metronet-remove.mpp-no-profile-image {
			display: none;
		}
		#metronet-override-avatar {
			clear: left;
		}
		</style>
		<?php
	} //end print_media_scripts

	public function print_media_styles() {
	} //end print_media_styles

	/**
	 * rest_get_users_permissions_callback()
	 *
	 * Gets permissions for the get users rest api endpoint.
	 *
	 * @return bool true if the user has permission, false if not
	 **/
	public function rest_get_users_permissions_callback() {
		return current_user_can( 'upload_files' );
	}

	/**
	* rest_api_register()
	*
	* Registers REST API endpoint
	**/
	public function rest_api_register() {
		register_rest_field(
			'user',
			'mpp_avatar',
			array(
				'get_callback' =>  array( $this, 'rest_api_get_profile_for_user' )
			)
		);
		register_rest_route(
			'mpp/v2',
			'/profile-image/me',
			array(
				'methods' => 'POST',
				'callback' =>  array( $this, 'rest_api_put_profile' )
			)
		);
		register_rest_route(
			'mpp/v2',
			'/get_users',
			array(
				'methods' => 'POST',
				'callback' =>  array( $this, 'rest_api_get_users' ),
				'permission_callback' => array( $this, 'rest_get_users_permissions_callback' )
			)
		);
		register_rest_route(
			'mpp/v2',
			'/get_posts',
			array(
				'methods' => 'POST',
				'callback' =>  array( $this, 'rest_api_get_posts_for_user' ),
				'permission_callback' => array( $this, 'rest_get_users_permissions_callback' )
			)
		);
		// keep it for backward compatibility
		register_rest_route(
			'mpp/v1',
			'/user/(?P<id>\d+)',
			array(
				'methods' => 'GET',
				'callback' =>  array( $this, 'rest_api_get_profile' ),
				'args'       =>  array(
					'id' => array(
						'validate_callback' => array( $this, 'rest_api_validate' ),
						'sanitize_callback' => array( $this, 'rest_api_sanitize' ),
					)
				)
			)
		);
	}

	/**
	 * rest_api_get_users()
	 *
	 * Gets users for the Gutenberg block
	 *
	 * @param array $request WP REST API array
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
		$user_query = new WP_User_Query( array( 'who' => $capabilities, 'orderby' => 'display_name' ) );
		$user_results = $user_query->get_results();
		$return = array();
		foreach( $user_results as $result ) {
			//Get attachment ID
			$profile_post_id = absint( get_user_option( 'metronet_post_id', $result->data->ID ) );
			$post_thumbnail_id = get_post_thumbnail_id( $profile_post_id );
			if ( ! $post_thumbnail_id ) {
				$result->data->has_profile_picture = false;
				$result->data->profile_picture_id = 0;
				$result->data->default_image = self::get_plugin_url( 'img/mystery.png' );
				$result->data->profile_pictures = array(
					'avatar' => get_avatar( $result->data->ID ),
				);
				$result->data->is_user_logged_in = ( $result->data->ID == get_current_user_id() ) ? true : false;
				$return[$result->data->ID] = $result->data;
				continue;
			}
			$result->data->description = get_user_meta( $result->data->ID, 'description', true );
			$result->data->display_name = $result->data->display_name;
			$result->data->has_profile_picture = true;
			$result->data->is_user_logged_in = ( $result->data->ID == get_current_user_id() ) ? true : false;
			$result->data->description = get_user_meta( $result->data->ID, 'description', true );

			//Get attachment URL
			$attachment_url = wp_get_attachment_url( $post_thumbnail_id );

			$result->data->profile_picture_id = $post_thumbnail_id;
			$result->data->default_image = self::get_plugin_url( 'img/mystery.png' );
			$result->data->profile_pictures = array(
				'24'        => wp_get_attachment_image_url( $post_thumbnail_id, 'profile_24', false, '' ),
				'48'        => wp_get_attachment_image_url( $post_thumbnail_id, 'profile_48', false, '' ),
				'96'        => wp_get_attachment_image_url( $post_thumbnail_id, 'profile_96', false, '' ),
				'thumbnail' => wp_get_attachment_image_url( $post_thumbnail_id, 'thumbnail', false, '' ),
				'avatar'    => get_avatar( $result->data->ID ),
				'full'      => $attachment_url,
			);
			$result->data->permalink = get_author_posts_url( $result->data->ID );
			$return[$result->data->ID] = $result->data;
		}
		return $return;
	}

	/**
	 * rest_api_put_profile()
	 *
	 * Adds a profile picture to a user
	 *
	 * @param array $request WP REST API array
	 *
	 * @return json image URLs matched to sizes
	 **/
	public function rest_api_put_profile( $request ) {

		$user_id = get_current_user_id();
		$media_id = (int) $request['media_id'];
		if ( ! current_user_can( 'upload_files' ) ) {
			return new WP_Error( 'mpp_insufficient_privs', __( 'You must be able to upload files.', 'metronet-profile-picture' ), array( 'status' => 403 ) );
		}

		if ( ! $user_id ) {
			return new WP_Error( 'mpp_no_user', __( 'User not found.', 'metronet-profile-picture' ), array( 'status' => 403 ) );
		}
		$is_post_owner = ( $user_id == get_post($media_id) ->post_author ) ? true  : false ;
		if ( ! $is_post_owner ) {
			return new WP_Error( 'mpp_not_owner', __( 'User not owner.', 'metronet-profile-picture' ), array( 'status' => 403 ) );
		}

		$post_id = $this->get_post_id( $user_id );
		//Save user meta
		update_user_option( $user_id, 'metronet_post_id', $post_id );
		update_user_option( $user_id, 'metronet_image_id', $media_id ); //Added via this thread (Props Solinx) - https://wordpress.org/support/topic/storing-image-id-directly-as-user-meta-data

		set_post_thumbnail( $post_id, $media_id );

		$attachment_url = wp_get_attachment_url( $media_id );

		return array(
			'24'  => wp_get_attachment_image_url( $media_id, 'profile_24', false, '' ),
			'48'  => wp_get_attachment_image_url( $media_id, 'profile_48', false, '' ),
			'96'  => wp_get_attachment_image_url( $media_id, 'profile_96', false, '' ),
			'full'=> $attachment_url
		);
	}

	/**
	* rest_api_get_posts_for_user()
	*
	* Returns the 5 most recent posts for the user
	**/
	public function rest_api_get_posts_for_user( $request ) {
		$user_id = absint( $request[ 'user_id' ] );
		$user = get_user_by( 'id', $user_id );
		if ( ! $user ) {
			return new WP_Error( 'mpp_no_user', __( 'User not found.', 'metronet-profile-picture' ), array( 'status' => 404 ) );
		}

		$args = array(
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'author'         => $user_id,
			'orderby'        => 'date',
			'order'          => 'DESC',
			'posts_per_page' => 5
		);
		$posts = get_posts( $args );
		foreach( $posts as &$post ) {
			$post->permalink = get_permalink( $post->ID );
		}
		wp_send_json( $posts );
	}
	/**
	* rest_api_get_profile()
	*
	* Returns an attachment image ID and profile image if available
	**/
	public function rest_api_get_profile_for_user( $object, $field_name, $request ) {
		$user_id = $object[ 'id' ];
		$user = get_user_by( 'id', $user_id );
		if ( ! $user ) {
			return new WP_Error( 'mpp_no_user', __( 'User not found.', 'metronet-profile-picture' ), array( 'status' => 404 ) );
		}

		// No capability check here because we're just returning user profile data

		//Get attachment ID
		$profile_post_id = absint( get_user_option( 'metronet_post_id', $user_id ) );
		$post_thumbnail_id = get_post_thumbnail_id( $profile_post_id );
		if ( ! $post_thumbnail_id ) {
			return new WP_Error( 'mpp_no_profile_picture', __( 'Profile picture not found.', 'metronet-profile-picture' ), array( 'status' => 404 ) );
		}

		//Get attachment URL
		$attachment_url = wp_get_attachment_url( $post_thumbnail_id );

		return array(
			'24'  => wp_get_attachment_image_url( $post_thumbnail_id, 'profile_24', false, '' ),
			'48'  => wp_get_attachment_image_url( $post_thumbnail_id, 'profile_48', false, '' ),
			'96'  => wp_get_attachment_image_url( $post_thumbnail_id, 'profile_96', false, '' ),
			'full'=> $attachment_url
		);
	}

	/**
	 * rest_api_get_profile()
	 *
	 * Returns a profile for the user
	 *
	 * @param array $data WP REST API array
	 *
	 * @return json image URLs matched to sizes
	 **/
	public function rest_api_get_profile( $data ) {
		$user_id = $data[ 'id' ];
		$user = get_user_by( 'id', $user_id );
		if ( ! $user ) {
			return new WP_Error( 'mpp_no_user', __( 'User not found.', 'metronet-profile-picture' ), array( 'status' => 404 ) );
		}

		//Get attachment ID
		$profile_post_id = absint( get_user_option( 'metronet_post_id', $user_id ) );
		$post_thumbnail_id = get_post_thumbnail_id( $profile_post_id );
		if ( ! $post_thumbnail_id ) {
			return new WP_Error( 'mpp_no_profile_picture', __( 'Profile picture not found.', 'metronet-profile-picture' ), array( 'status' => 404 ) );
		}

		//Get attachment URL
		$attachment_url = wp_get_attachment_url( $post_thumbnail_id );

		return array(
			'attachment_id'  => $post_thumbnail_id,
			'attachment_url' => $attachment_url
		);
	}

	/**
	* rest_api_validate()
	*
	* Makes sure the ID we are passed is numeric
	**/
	public function rest_api_validate( $param, $request, $key ) {
		return is_numeric( $param );
	}

	/**
	* rest_api_validate()
	*
	* Sanitizes user ID
	**/
	public function rest_api_sanitize( $param, $request, $key ) {
		return absint( $param );
	}

	/**
	* save_user_profile()
	*
	* Saves user profile fields
	* @param int $user_id
	**/
	public function save_user_profile( $user_id ) {
		if ( !isset( $_POST[ 'metronet-user-avatar' ] ) ) return;
		check_admin_referer( 'update-user_' . $user_id );

		$user_avatar = $_POST[ 'metronet-user-avatar' ];
		if ( $user_avatar == 'on' ) {
			update_user_option( $user_id, 'metronet_avatar_override', 'on' );
		} else {
			update_user_option( $user_id, 'metronet_avatar_override', 'off' );
		}
	} //end save_user_profile

} //end class
//instantiate the class
global $mt_pp;
if (class_exists('Metronet_Profile_Picture')) {
	if (get_bloginfo('version') >= "3.5") {
		add_action( 'plugins_loaded', 'mt_mpp_instantiate' );
	}
}
function mt_mpp_instantiate() {
	global $mt_pp;
	$mt_pp = new Metronet_Profile_Picture();
}
/**
 * mt_profile_img
 *
 * Adds a profile image
 *
 * @param $user_id INT - The user ID for the user to retrieve the image for
 * @param $args mixed
 *	size - string || array (see get_the_post_thumbnail)
 *	attr - string || array (see get_the_post_thumbnail)
 *	echo - bool (true or false) - whether to echo the image or return it
*/
function mt_profile_img( $user_id, $args = array() ) {
	$profile_post_id = absint( get_user_option( 'metronet_post_id', $user_id ) );

	if ( 0 === $profile_post_id || 'mt_pp' !== get_post_type( $profile_post_id ) ) {
		return false;
	}

	$defaults = array(
		'size' => 'thumbnail',
		'attr' => '',
		'echo' => true
	);
	$args = wp_parse_args( $args, $defaults );
	extract( $args ); //todo - get rid of evil extract

	$post_thumbnail_id = get_post_thumbnail_id( $profile_post_id );

	//Return false or echo nothing if there is no post thumbnail
	if( !$post_thumbnail_id ) {
		if ( $echo ) echo '';
		else return false;
		return;
	}

	//Implode Classes if set and array - dev note: edge case
	if ( is_array( $attr ) && isset( $attr[ 'class' ] ) ) {
		if ( is_array( $attr[ 'class' ] ) ) {
			$attr[ 'class' ] = implode( ' ', $attr[ 'class' ] );
		}
	}

	$post_thumbnail =  wp_get_attachment_image( $post_thumbnail_id, $size, false, $attr );

	/**
	 * Filter outputted HTML.
	 *
	 * Filter outputted HTML.
	 *
	 * @param string $post_thumbnail       img tag with formed HTML
	 * @param int	 $profile_post_id      The profile in which the image is attached
	 * @param int    $profile_thumbnail_id The thumbnail ID for the attached image
	 * @param int    $user_id              The user id for which the image is attached
	 *
	 */
	$post_thumbnail = apply_filters( 'mpp_thumbnail_html', $post_thumbnail, $profile_post_id, $post_thumbnail_id, $user_id );
	if ( $echo ) {
		echo $post_thumbnail;
	} else {
		return $post_thumbnail;
	}
} //end mt_profile_img
