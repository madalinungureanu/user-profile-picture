=== User Profile Picture ===
Contributors: ronalfy
Tags: users, user, user profile
Requires at least: 3.5
Tested up to: 4.5
Stable tag: 1.3.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Set a custom profile image for a user using the standard WordPress media upload tool.
== Description ==

Set or remove a custom profile image for a user using the standard WordPress media upload tool.  

A template tag is supplied for outputting to a theme and the option to override a user's default avatar is also available.

If you like this plugin, please leave a rating/review and mark the plugin as working.

== Installation ==

1. Upload `metronet-profile-picture` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Place `<?php mt_profile_img() ?>` in your templates (arguments and usage are below)
4. Use the "Override Avatar" function to change your default avatar.

Arguments: 

`/**
* mt_profile_img
* 
* Adds a profile image
*
@param $user_id INT - The user ID for the user to retrieve the image for
@ param $args mixed
	size - string || array (see get_the_post_thumbnail)
	attr - string || array (see get_the_post_thumbnail)
	echo - bool (true or false) - whether to echo the image or return it
*/
`

Example Usage:
`
<?php
//Assuming $post is in scope
if (function_exists ( 'mt_profile_img' ) ) {
	$author_id=$post->post_author;
	mt_profile_img( $author_id, array(
		'size' => 'thumbnail',
		'attr' => array( 'alt' => 'Alternative Text' ),
		'echo' => true )
	);
}
?>
`
View the code on <a href="http://pastebin.com/Xaf8dJqQ">Pastebin</a>.

The `mt_profile_img` function internally uses the <a href="http://codex.wordpress.org/Function_Reference/get_the_post_thumbnail">get_the_post_thumbnail</a> function to retrieve the profile image.

Optionally, if you choose the "Override Avatar" function, you can use <a href="http://codex.wordpress.org/Function_Reference/get_avatar">get_avatar</a> to retrieve the profile image.

If you want the "Override Avatar" checkbox to be checked by default, drop this into your theme's `functions.php` file or <a href="http://www.wpbeginner.com/beginners-guide/what-why-and-how-tos-of-creating-a-site-specific-wordpress-plugin/">Site-specific plugin</a>:

`add_filter( 'mpp_avatar_override', '__return_true' );`

If you want to hide the "Override Avatar" checkbox, use this filter (the override functionality will be enabled by default):
`add_filter( 'mpp_hide_avatar_override', '__return_true' );`

The REST API is currently supported for versions of WordPress 4.4 and up.

The endpoint is: `http://www.yourdomain.com/wp-json/mpp/v1/user/{user_id}`

If a profile picture is found, it'll return JSON as follows:

`
{"attachment_id":"3638","attachment_url":"http:\/\/localhost\/ronalfy\/wp-content\/uploads\/2015\/12\/Leaderboard-All-Time.png"}
`

== Frequently Asked Questions ==

= How do you set a user profile image? =

1.  Visit the profile page you would like to edit.
2.  Click on the profile picture to add, edit, or remove the profile picture.

To override an avatar, select the "Override Avatar?" checkbox and save the profile page.

= What role does a user have to be to set a profile image? =

Author or greater.

= How do I create specific thumbnail sizes? =

Since the plugin uses the native uploader, you'll have to make use of <a href='http://codex.wordpress.org/Function_Reference/add_image_size'>add_image_size</a> in your theme.  You can then call `mt_profile_img` and pass in the custom image size.

= The image is cropped wrong.  How do I fix this? = 

We highly recommend the <a href='http://wordpress.org/extend/plugins/post-thumbnail-editor/'>Post Thumbnail Editor</a> plugin for cropping thumbnails, as you can custom-crop various image sizes without affecting other images.

= Does the plugin work with Multisite? =

Yes, but you'll have to set a new profile image per site.  This is currently a limitation of the way the plugin stores its data.  Ideas to overcome this are welcome.

== Screenshots ==

1. Profile page options.
2. Media upload dialog.

== Changelog ==

= 1.3.1 =
* Released 2016-04-02
* Fixing thumbnail calls that could potential be inadvertently filtered and cause the profile picture to provide an erroneous callback. Props @Monter.

= 1.3.0 =
* Released 2016-03-28
* Adding REST API endpoint

= 1.2.7 =
* Updated 2015-08-20 for WP 4.3 compatibility
* Released 2015-06-10
* Bug fix: warning message saying missing argument for avatar_override

= 1.2.5 =
* Released 2015-06-06
* Bug fix: get_avatar override now accepts custom classes.
* Added `mpp_avatar_classes` filter to get_avatar override to allow global class overrides/additions.

= 1.2.3 =
* Released 2015-05-20
* Revised post type initialization to make sure post type is completely hidden.
* Refactored function mt_profile_img to use a different API call so that output isn't inadvertently filtered.
* Added new filter, mpp_thumbnail_html, to filter output.

= 1.2.2 =
* Released 2015-04-16
* Added compatibility to Advanced Custom Fields.
* Added increased capabilities check to Ajax calls.
* Ensuring WordPress 4.2 compatibility.

= 1.2.1 =
* Released 2015-03-03
* Fixed internationalization errors.
* Added Spanish translation.

= 1.2.0 =
* Released 2014-12-07
* Reducing clutter in the interface.  Removed text option to upload.  Added default image if no profile image is available.  Added option to remove the profile image.
* Fixed internationalization bug in the JavaScript.

= 1.1.0 = 
* Released 2014-11-11
* Added the ability to remove profile images (aside from deleting the image).
* Added better internationalization capabilities.
* Added compatibility with <a href="https://wordpress.org/plugins/theme-my-login/">Theme My Login</a>.

= 1.0.23 =
* Released 2014-10-20
* Added a new filter to allow the "Override Avatar" interface to be hidden (and turned on my default).

= 1.0.22 =
* Released 2014-09-02
* Added minor update to additional user meta for easier querying (props Solinx)

= 1.0.21 =
* Released 2013-09-09
* Fixed avatar override on options discussion page.

= 1.0.20 = 
* Released 2013-05-13
* Added a filter for turning on "Override Avatar" by default.

= 1.0.19 = 
* Added support for 2.0.x version of <a href='http://wordpress.org/extend/plugins/post-thumbnail-editor/'>Post Thumbnail Editor</a>

= 1.0.18 = 
* Added basic multisite support

= 1.0.16 =
* Fixed a bug where only the profile image interface was showing for only authors and not editors and administrators. 

= 1.0.15 =
* Built-in support for <a href="http://wordpress.org/extend/plugins/post-thumbnail-editor/">Post Thumbnail Editor</a>
* Better integration with the new WP 3.5 media uploader
* Various bug fixes.

= 1.0.10 = 
* Usability enhancements.
* Stripping out useless code.
* Updating documentation

= 1.0.9 = 
* Adding support for the new 3.5 media uploader.

= 1.0.3 = 
* Bug fix:  Avatar classes in the comment section

= 1.0.2 =
* Bug fix:  Error being shown in comment section

= 1.0.1 =
* Bug fix:  Not able to "uncheck" Override Avatar.
* Bug fix:  Deleting profile image and not reverting to normal avatar.

= 1.0.0 =
* Initial release.

== Upgrade Notice ==

= 1.3.1 =
Fixing thumbnail calls that could potential be inadvertently filtered and cause the profile picture to provide an erroneous callback.

= 1.3.0 =
Adding REST API endpoint.

= 1.2.7 =
Bug fix for warning message saying missing argument for avatar_override.

= 1.2.5 =
Bug fix for avatar override to accept custom CSS classes.

= 1.2.3 =
Made MPP post type completely hidden. mt_profile_img refactored to avoid filtered output.

= 1.2.2 =
Added compatibility to Advanced Custom Fields.  Ensuring WordPress 4.2 compatibility.

= 1.2.1 =
Fixed internationalization errors. Added Spanish translation.

= 1.2.0 =
Reducing clutter in the interface.  Removed text option to upload.  Added default image if no profile image is available.  Added option to remove the profile image.

