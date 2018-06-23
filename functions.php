<?php

/*--------------------------------------------------------------
>>> TABLE OF CONTENTS:
----------------------------------------------------------------
1.0 Initializations
2.0 Hooks & Filters
3.0 Loop Functions
4.0 Misc
--------------------------------------------------------------*/




/*--------------------------------------------------------------
1.0 Initializations
--------------------------------------------------------------*/

require_once ( get_template_directory() . '/config.php' );
register_meta( 'post', 'album_info', array() );
register_meta( 'post', 'late_foursquare_shout', array() );

function the_dammed_set_spotify_access_token() {
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

if ( ! get_transient( 'spotify_access_token' ) ) {
	the_dammed_set_spotify_access_token();
}

/*--------------------------------------------------------------
2.0 Hooks & Filters
--------------------------------------------------------------*/

function the_dammed_scripts() {
	wp_enqueue_style( 'the-dammed-style', get_stylesheet_uri(), array() );
	wp_enqueue_script( 'the-dammed-js', get_template_directory_uri() . '/js/the-dammed.js', array( 'jquery') );
}
add_action( 'wp_enqueue_scripts', 'the_dammed_scripts' );

/*--------------------------------------------------------------
3.0 Loop Functions
--------------------------------------------------------------*/

function the_dammed_is_post_twitter_type() {
	return has_term( 'tweets', 'category' ) &&
	       ! has_term( 'todaystunes', 'post_tag' );
}

function the_dammed_is_post_spotify_type() {
	return has_term( 'todaystunes', 'post_tag' );
}

function the_dammed_is_post_swarm_type() {
	return has_term( 'check-ins', 'category' );
}

function the_dammed_get_attachment_url() {
	$cover_art = get_attached_media( 'image' );
	if ( empty( $cover_art ) ) {
		return false;
	}
	return array_values( $cover_art )[0]->guid;
}


/*--------------------------------------------------------------
4.0 Misc
--------------------------------------------------------------*/

function the_dammed_update_album_info( $post_id ) {
	$content = trim( strip_tags( get_the_content() ) );
	$album_info_array = explode( ':', $content );
	$artist = trim( $album_info_array[0] );
	$album_array = explode( '"', html_entity_decode( $album_info_array[1], ENT_QUOTES ) );
	$album = trim( $album_array[1] );
	$album_info = array( 'album' => $album, 'artist' => $artist );
	update_post_meta( $post_id, 'album_info', $album_info );
	return $album_info;
}

function the_dammed_update_spotify_info( $post_id, $artist, $album ) {
	$args = array(
		'headers' => array(
			'Authorization' => 'Bearer ' . get_transient( 'spotify_access_token' )
		)
	);

	$query = $album . ' ' . $artist;
	$query = htmlspecialchars_decode( $query, ENT_QUOTES );
	$query = urlencode( $query );

	$response = wp_remote_get( 'https://api.spotify.com/v1/search?query=' . $query . '&type=album&limit=1', $args );

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

function the_dammed_get_gorgeous_thing() {
	$gorgeous_things = array(
		'a sunset in Sintra',
		'a bag of Fritos Twists &trade;',
		'a ladybird on a window sill',
		'a cellar door',
		'your first Okonomiyaki',
		'a ski trip to Tuckerman Ravine',
		'a cabin in Barra de Valizas',
		'a pancake in Amsterdam',
		'a seal in La Jolla',
		'a cicada at the Lincoln Memorial',
		'a striped bass at Old Orchard Beach',
		'a glade at Sugarloaf',
		'a plate of lomo saltado in Lima',
		'a riverside picnic in Paris',
		'a warm bag of chestnuts in Lisbon',
		'a plate of patatas bravas in Madrid',
		'a ferry ride to Bowen Island',
		'a Peaks Island pub crawl',
		'a scooter ride with a friend',
		'a motorcycle\'s friction zone',
		'birthday beers with family at the Old Port Festival',
		'freshly grown basil on a microwave dinner',
	);
	$gorgeous_things = apply_filters( 'dammed_gorgeous_things', $gorgeous_things );
	return $gorgeous_things[ mt_rand( 0, count( $gorgeous_things ) - 1 ) ];
}

function the_dammed_swarm_location( $swarm_data ) {
	$location = '';
	if ( ! empty( $swarm_data->venue->location->city ) ) {
		$location = $swarm_data->venue->location->city . ', ';
	}
	if(
		$swarm_data->venue->location->city !== $swarm_data->venue->location->state &&
		! empty( $swarm_data->venue->location->state )
	) {
		$location .= $swarm_data->venue->location->state . ', ';
	}
	$location .= $swarm_data->venue->location->country;
	return $location;
}

function the_dammed_get_foursquare_shout( $post_id, $swarm_data ) {
	$late_shout = get_post_meta( $post_id, 'late_foursquare_shout', true );
	if ( ! empty( $late_shout ) ) {
		return $late_shout;
	}
	if ( ! empty( $swarm_data->shout ) ) {
		return  $swarm_data->shout;
	}
	return null;
}

function the_dammed_format_tweet( $raw_tweet ) {
	$entities = array();
	$content = $raw_tweet['text'];
	foreach( $raw_tweet['entities']['user_mentions'] as $mention ) {
		$entities[] = array(
			'starts' => $mention['indices'][0],
			'ends' => $mention['indices'][1],
			'type' => 'mention',
			'screen_name' => $mention['screen_name'],
			'id' => $mention['id'],
		);
	}
	foreach( $raw_tweet['entities']['hashtags'] as $hashtag ) {
		$entities[] = array(
			'starts' => $hashtag['indices'][0],
			'ends' => $hashtag['indices'][1],
			'type' => 'hashtag',
			'text' => $hashtag['text'],
		);
	}

	usort( $entities, function( $a, $b ) {
		if ( $a['starts'] > $b['starts'] ) {
			return -1;
		}
		return 1;
	} );

	foreach( $entities as $entity ) {
		switch ( $entity['type'] ) {
			case 'mention':
				$content = substr_replace( $content, '</a>', $entity['ends'], 0 );
				$content = substr_replace( $content, '<a href="https://twitter.com/' . $entity['screen_name'] .'">', $entity['starts'], 0 );
				break;
			case 'hashtag':
				$content = substr_replace( $content, '</a>', $entity['ends'], 0 );
				$content = substr_replace( $content, '<a href="https://twitter.com/hashtag/' . $entity['text'] .'">', $entity['starts'], 0 );
				break;
		}
	}

	return $content;
}
