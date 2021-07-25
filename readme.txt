=== User Profile Picture ===
Contributors: cozmoslabs, ronalfy, Alaadiaa
Tags: users, user profile, gravatar, avatar, blocks, block
Requires at least: 3.5
Tested up to: 5.8
Stable tag: 2.6.0
Requires PHP: 5.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Donate link: https://cozmoslabs.com/

Set a custom profile image (avatar) for a user using the standard WordPress media upload tool.
== Description ==

Set or remove a custom profile image for a user using the standard WordPress media upload tool.

<a href="https://www.cozmoslabs.com/user-profile-picture/">View Documentation and Examples</a>

https://www.youtube.com/watch?v=9icnOWWZUpA&rel=0

Users must have the ability to upload images (typically author role or greater). You can use the plugin <a href="https://wordpress.org/plugins/profile-builder/">Profile Builder</a> to allow other roles (e.g. subscribers) the ability to upload images.

A template tag is supplied for outputting to a theme and the option to override a user's default avatar is also available.

== Documentation and Feedback ==

See the documentation on <a href="https://github.com/madalinungureanu/user-profile-picture">GitHub</a>.

> Please <a href="https://wordpress.org/support/plugin/metronet-profile-picture/reviews/#new-post">Rate the Plugin</a>.

== Installation ==

1. Upload `metronet-profile-picture` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Place `<?php mt_profile_img() ?>` in your templates (arguments and usage are below)

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

Since 2.2.0, you can add a profile author box using function `mt_author_box`.

`
<?php
mt_author_box( $post->post_author, array(
	'theme'              => 'tabbed',
	'profileAvatarShape' => 'round',
	'showWebsite'        => true,
	'website'            => 'https://www.ronalfy.com',
	'showSocialMedia'    => true,
	'socialMediaOptions' => 'brand',
	'socialWordPress'    => 'https://profiles.wordpress.org/ronalfy',
	'socialFacebook'     => 'https://facebook.com/mindefusement',
) );
`

View the code on <a href="https://github.com/madalinungureanu/user-profile-picture">GitHub</a>.

The REST API is currently supported for versions of WordPress 4.4 and up.

== Frequently Asked Questions ==

= How do you set a user profile image? =

1.  Visit the profile page you would like to edit.
2.  Click on the profile picture to add, edit, or remove the profile picture.

= What role does a user have to be to set a profile image? =

Author or greater.

= How do I create specific thumbnail sizes? =

As of 1.5, three image sizes are created: profile_24, profile_48, and profile_96. You are not limited to these sizes, however.

= Does the plugin work with Multisite? =

Yes, but you'll have to set a new profile image per site.  This is currently a limitation of the way the plugin stores its data.  Ideas to overcome this are welcome.

== Screenshots ==

1. Profile page options.
2. Media upload dialog.
3. Gutenberg settings back-end
4. Gutenberg profile front-end

== Changelog ==

= 2.6.0 =
* Released 2021-06-25
* Fixed a security flaw where a user can change others profile picture.

= 2.5.0 =
* Released 2021-02-18
* Medium level (6.5) security fix. Please update as soon as possible.
* Cleaned up REST user data so only pertitent information is returned to prevent user data leakage to roles with the upload_files capability.

= 2.4.0 =
* Released 2020-11-17
* Fixing REST issues.
* Cleaning up dist scripts.
* General code cleanup.

= 2.3.11 =
* Released 2020-02-29
* Added option to disable image sizes.

= 2.3.10 =
* Released 2019-12-29
* Added new hook for add-on capability.

= 2.3.9 =
* Released 2019-12-06
* Removing adverts for User Profile Picture Enhanced.

= 2.3.8 =
* Released 2019-10-30
* Removing top-level navigation.

= 2.3.7 =
* Released 2019-10-23
* Fixing options not being saved properly.

= 2.3.6 =
* Released 2019-10-09
* Updating compatibility with WordPress 5.3
* Fixing JavaScript error in WordPress 5.3

= 2.3.5 =
* Released 2019-09-17
* Added option in Gutenberg block to customize the View Posts and View Website text.

= 2.3.2 =
* Released 2019-07-14
* Adding more filters for third-party plugin integration.

= 2.3.0 =
* Released 2019-07-06
* Gutenberg blocks are now in the User Profile Picture category.
* New filters for add-on extensibility
* Added Options page so you can disable the Gutenberg blocks if you so desire.
* Bug fix: Adding user display name to post title when User Profile Picture creates the user page.

= 2.2.8 =
* Released 2019-06-11
* New REST API endpoint for changing profile pictures.

= 2.2.7 =
* Released 2019-06-11
* Fixing permissions in REST API

= 2.2.6 =
* Released 2019-06-10
* Fixing permissions in REST API

= 2.2.5 =
* Released 2019-06-02
* Code cleanup.
* Leaner Gutenberg JavaScript.
* Gutenberg improvements.
* Security improvements.

= 2.2.0 =
* Released 2019-05-12
* Added template tags for displaying an author box

= 2.1.3 =
* Released 2019-02-16
* Added filter to get users in Gutenberg besides author

= 2.1.2 =
* Released 2019-01-26
* Resolving PHP notice for dirname

= 2.1.1 =
* Released 2018-12-20
* Adding white posts theme to the tabbed view block
* Fixing clearing for the tabbed view block

= 2.1.0 =
* Released 2018-12-19
* Old block deprecated, but still supported
* New block added with more control over appearance and includes themes.

= 2.0.2 =
* Released 2018-11-20
* Gutenberg fixes with alignment (center, right) on the front end.
* Gutenberg fixes with the toggle boxes defaulting back to nothing.

= 2.0.1 =
* Released 2018-11-20
* Fixing PHP 5.2 incompatibility
* Fixing Gutenberg block when there is no profile picture present on the front-end
* Updating translations file

= 2.0.0 =
* Released 2018-11-19
* Added Gutenberg block for easy outputting to posts
* Tested with WordPress 5.0

= 1.5.5 =
* Released 2018-08-19
* Enhancement: Loading image now shows between states for better UX
* Enhancement: Plugin attempts to override the default WordPress avatar in the User Profile page
* Enhancement: Plugin attempts to override the admin bar avatars if the users match
* Enhancement: Added Click to Edit bar to make it more obvious what to do with the profile picture
* Refactor: Plugin now uses wp_send_json instead of json_encode for more compatibility

= 1.5.1 =
* Released 2018-07-12
* Fixed a condition where a featured image was shown for the author instead of a blank gravatar

= 1.5.0 =
* Released 2018-07-11
* Support for AMP avatar has been added
* Two REST API endpoints have been added to facilitate better programatic avatar selection

= 1.4.3 =
* Released 2016-09-24
* Bug fix: Post featured image is being shown as user's profile picture when no avatar is selected.

= 1.4.1 =
* Released 2016-08-30 (Props @Monter)
* Fix select states in image modal

= 1.4.0 =
* Released 2016-08-29 (props kelderic)
* Bug fix: CSS Fixes to the trashcan icon and image placeholders
* Bug fix: Modal window was set to the wrong settings
* Enhancement: Avatar override is now the default option

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

= 2.6.0 =
Please update. Fixed a security flaw where a user can change others profile picture.

= 2.5.0 =
Medium level (6.5) security fix. Please update as soon as possible. Cleaned up REST user data so only pertitent information is returned to prevent user data leakage to roles with the upload_files capability.

= 2.4.0 =
Fixing REST issues. Cleaning up dist scripts. General code cleanup.

= 2.3.11 =
Added option to disable image sizes.

= 2.3.10 =
Added new hook for add-on capability.