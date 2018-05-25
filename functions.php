<?php

require_once ( get_template_directory() . '/config.php' );

function the_dammed_scripts() {
	wp_enqueue_style( 'the-dammed-style', get_stylesheet_uri(), array(), null );
	wp_enqueue_script( 'the-dammed-js', get_template_directory_uri() . '/js/the-dammed.js', array( 'jquery') );
}
add_action( 'wp_enqueue_scripts', 'the_dammed_scripts' );

register_meta( 'post', 'album_info', array() );

// Functions within THE LOOP
function the_dammed_is_post_twitter_type() {
	return has_term( 'tweets', 'category' ) &&
	       ! has_term( 'todaystunes', 'post_tag' );
}

function the_dammed_is_post_spotify_type() {
	return has_term( 'tweets', 'category' ) &&
	       has_term( 'todaystunes', 'post_tag' );
}

function the_dammed_get_cover_art() {
	$cover_art = get_attached_media( 'image' );
	if ( empty( $cover_art ) ) {
		return false;
	}
	return array_values( $cover_art )[0]->guid;
}

function the_dammed_update_album_info( $post_id ) {
	l( 'ALERT: updating album info' );
	$content = trim( strip_tags( get_the_content() ) );
	$album_info_array = explode( ':', $content );
	$artist = trim( $album_info_array[0] );
	$album_array = explode( '"', html_entity_decode( $album_info_array[1] ) );
	$album = trim( $album_array[1] );
	$album_info = array( 'album' => $album, 'artist' => $artist );
	update_post_meta( $post_id, 'album_info', $album_info );
	return $album_info;
}

function the_dammed_set_spotify_access_token() {
	l( 'ALERT: we are updating the spotify access token!' );
	$args = array(
		'headers' => array(
			'Authorization' => 'Basic ' . base64_encode( SPOTIFY_API_CLIENT_ID . ':' . SPOTIFY_API_CLIENT_SECRET ),
		),
		'body' => array( 'grant_type' => 'client_credentials', ),
		'method' => 'POST'
	);

	$response = wp_remote_post( 'https://accounts.spotify.com/api/token', $args );

	if ( is_wp_error( $response ) || 200 != wp_remote_retrieve_response_code( $response ) ) {
		return;
	}

	$body = wp_remote_retrieve_body( $response );
	$response_array = json_decode( $body );
	set_transient( 'spotify_access_token', $response_array->access_token,3540 );
}

function the_dammed_update_spotify_info( $post_id, $artist, $album ) {
	$args = array(
		'headers' => array(
			'Authorization' => 'Bearer ' . get_transient( 'spotify_access_token' )
		)
	);

	$response = wp_remote_get( 'https://api.spotify.com/v1/search?query=' . urlencode( $album . ' ' . $artist ) . '&type=album&limit=1', $args );

	if ( is_wp_error( $response ) || 200 != wp_remote_retrieve_response_code( $response ) ) {
		return;
	}

	$repsonse_object = json_decode( wp_remote_retrieve_body( $response ) );

	$spotify_info = array(
		'artist_id' => $repsonse_object->albums->items[0]->artists[0]->id,
		'artist_url' => $repsonse_object->albums->items[0]->artists[0]->external_urls->spotify,
		'album_id' => $repsonse_object->albums->items[0]->id,
		'album_url' => $repsonse_object->albums->items[0]->external_urls->spotify,
	);

	update_post_meta( $post_id, 'spotify_info', $spotify_info );
	return $spotify_info;
}

if ( ! get_transient( 'spotify_access_token' ) ) {
	the_dammed_set_spotify_access_token();
}

if ( isset( $_GET['spotify'] ) ) {
	$args = array(
		'headers' => array(
			'Authorization' => 'Bearer ' . get_transient( 'spotify_access_token' )
		)
	);
	$response = wp_remote_get( 'https://api.spotify.com/v1/search?query=Life%20and%20Livin%27%20It%20Sinkane&type=album&limit=1', $args );
	$array = json_decode( wp_remote_retrieve_body( $response ) );
	echo '<pre>';
	print_r( $array );
	exit;
}
