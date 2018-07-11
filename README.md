# WordPress User Profile Picture
WordPress User Profile Picture

This is the development area for <a href="https://wordpress.org/plugins/metronet-profile-picture/">User Profile Picture</a>.

Please do pull requests on the dev branch.

### Helper Theme Function

```php
//Assuming $post is in scope
if (function_exists ( 'mt_profile_img' ) ) {
	$author_id = $post->post_author;
	mt_profile_img( $author_id, array(
		'size' => 'thumbnail',
		'attr' => array( 'alt' => 'Alternative Text' ),
		'echo' => true )
	);
}
```

### Rest API

The plugin has three routes for you.

This one will change your profile image. You must be the author of the image you are trying to change to, and you must also have upload privileges. 

```php
$request = new WP_REST_Request( 'POST', '/mpp/v2/profile-image/me' );
$request->set_param( 'media_id', 3754 );	
$request->set_header( 'X-WP-Nonce', wp_create_nonce( 'wp_rest' ) );
$response = rest_do_request( $request );
```

This one will get the avatar data for a specific user: 

```php
$request = new WP_REST_Request( 'GET', '/wp/v2/users/15' );
$response = rest_do_request( $request );
$avatars = $response->data[ 'mpp_avatar' ];
```

An old an deprecated way is to use version 1 of the API.

```php
$request = new WP_REST_Request( 'GET', '/mpp/v1/user/15' );
$response = rest_do_request( $request );
$avatar = $response->data;
```