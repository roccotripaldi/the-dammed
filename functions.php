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
	wp_enqueue_style( 'the-dammed-style', get_stylesheet_uri(), array( 'dashicons' ) );
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

function the_dammed_is_post_instagram_type() {
	return has_term( 'instagram', 'category' );
}

function the_dammed_is_post_pocket_type() {
	return has_term( 'pocket', 'category' );
}

function the_dammed_get_first_attachment_url( $type = 'image' ) {
	$media = get_attached_media( $type );
	if ( empty( $media ) ) {
		return false;
	}
	return array_values( $media )[0]->guid;
}

function the_dammed_attached_photo() {
	$photo = the_dammed_get_first_attachment_url();
	if ( ! $photo ) {
		return;
	}
	echo "<a class='dashicons dashicons-camera dammed-image-zoom' data-img='$photo'></a>";
}

function the_dammed_swarm_place() {
	// removes 'Checked in at '
	echo substr( get_the_title(), 14 );
}

function the_dammed_instagram_media() {
	$video = the_dammed_get_first_attachment_url( 'video' );
	$image = the_dammed_get_first_attachment_url();
	if ( $video ) {
		echo apply_filters( 'the_content', "[video src='$video' height='320']" );
		return;
	} else if ( $image ) {
		echo  "<img class='media-main' src='$image' />";
		echo "<a class='dashicons dashicons-search dammed-image-zoom' data-img='$image'></a>";
		return;
	}
}

function the_dammed_spotify_album_art( $spotify_info, $album_info ) {
	$cover_art = the_dammed_get_first_attachment_url();
	$cover_alt_text = esc_attr( $album_info['album'] ) . ' by ' . esc_attr( $album_info['artist'] );
	$img = "<img src='$cover_art' alt='$cover_alt_text' />";
	if ( empty( $spotify_info ) ) {
		echo $img;
		return;
	}
	$album_id = $spotify_info['album_id'];
	echo "<a href='spotify:album:$album_id' class='img-link'>$img</a>";
}

function the_dammed_spotify_album_text( $spotify_info, $album_info ) {
	$album = $album_info['album'];
	$artist = $album_info['artist'];

	if ( empty( $spotify_info ) ) {
		echo "<span><i>$album</i> by $artist</span>";
		return;
	}
	$artist_id = $spotify_info['artist_id'];
	$album_id = $spotify_info['album_id'];
	echo "<span><i>$album</i> by <a href='spotify:artist:$artist_id'>$artist</a></span>" .
	     "<span class='link'><a class='spotify-link' href='spotify:album:$album_id'>Listen on Spotify</a></span>";
}

function the_dammed_instagram_header( $import_object ) {
	$pic =  $import_object->user->profile_picture;
	$name = $import_object->user->username;
	$location = the_dammed_instagram_location( $import_object );
	echo "<img class='instagram-avatar' src='$pic' /><p class='instagram-info'>$name<br />$location</p>";
}

function the_dammed_pocket_title( $link ) {
	$url = the_dammed_pocket_url( $link );
	$title = $url;
	if ( $link['resolved_title'] ) {
		$title = $link['resolved_title'];
	} elseif ( $link['given_title'] ) {
		$title = $link['given_title'];
	}
	echo "<a href='$url' target='_blank'>$title</a>";
}

function the_dammed_pocket_footer( $link ) {
	$text = 'Visit';
	$icon = '';
	$url = the_dammed_pocket_url( $link );

	if ( $link['domain_metadata'] && $link['domain_metadata']['name'] ) {
		$text = $link['domain_metadata']['name'];
	}

	if ( $link['domain_metadata'] && $link['domain_metadata']['logo'] ) {
		$src = $link['domain_metadata']['logo'];
		$icon = "<a href='$url' target='_blank' class='icon-link'><img src='$src' /></a>";
	}

	echo "$icon<a href='$url' target='_blank' class='domain-link'>$text</a>";
}

function the_dammed_pocket_content( $link ) {
	$url = the_dammed_pocket_url( $link );

	if ( the_dammed_pocket_is_youtube( $link ) ) {
		$video = the_dammed_pocket_first_video( $link );
		echo apply_filters( 'the_content', $video['src'] );
		return;
	}

	if ( $link['has_image'] && $link['image'] ) {
		$src = $link['image']['src'];
		echo "<a href='$url' class='media-box' target='_blank'><img src='$src' class='media-main' /></a>";
		return;
	}

	if ( $link['excerpt'] ) {
		echo "<div class='pocket-content-excerpt'>";
		echo "<p>" . $link['excerpt'] . "</p>";
		echo "<p class='pocket-content-read-more'><a href='$url' target='_blank'>Read more...</a></p>";
		echo "</div>";
		return;
	}

	echo "<a href='$url' target='_blank' class='pocket-empty-content dashicons dashicons-admin-links'></a>";
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

function the_dammed_unicode_mappings( $content ) {
	$search  = array( '’',  '‘',  '“', '”', '—' );
	$replace = array( '\'', '\'', '"', '"', '-' );
	return str_replace( $search, $replace, $content );
}

function the_dammed_format_tweet( $raw_tweet ) {
	$entities = array();

	$tweet = $raw_tweet['retweeted'] ?
		$raw_tweet['retweeted_status'] :
		$raw_tweet;

	$content = the_dammed_unicode_mappings( $tweet['text'] );

	foreach( $tweet['entities']['user_mentions'] as $mention ) {
		$entities[] = array(
			'starts' => $mention['indices'][0],
			'ends' => $mention['indices'][1],
			'type' => 'mention',
			'screen_name' => $mention['screen_name'],
			'id' => $mention['id'],
		);
	}

	foreach( $tweet['entities']['hashtags'] as $hashtag ) {
		$entities[] = array(
			'starts' => $hashtag['indices'][0],
			'ends' => $hashtag['indices'][1],
			'type' => 'hashtag',
			'text' => $hashtag['text'],
		);
	}

	foreach ( $tweet['entities']['urls'] as $url ) {
		$entities[] = array(
			'starts' => $url['indices'][0],
			'ends' => $url['indices'][1],
			'expanded' => $url['expanded_url'],
			'type' => 'url'
		);
	}

	if ( isset( $tweet['entities']['media'] ) ) {
		foreach ( $tweet['entities']['media'] as $media ) {
			$entities[] = array(
				'starts' => $media['indices'][0],
				'ends' => $media['indices'][1],
				'type' => 'media'
			);
		}
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
				$content = substr_replace( $content, '<a href="https://twitter.com/' . $entity['screen_name'] .'" target="_blank">', $entity['starts'], 0 );
				break;
			case 'hashtag':
				$content = substr_replace( $content, '</a>', $entity['ends'], 0 );
				$content = substr_replace( $content, '<a href="https://twitter.com/hashtag/' . $entity['text'] .'" target="_blank">', $entity['starts'], 0 );
				break;
			case 'url':
				if ( $tweet['truncated'] ) {
					$content = substr_replace( $content, '', $entity['starts'] + 1, $entity['ends'] - $entity['starts'] + 1 );
				} else if ( false != strpos( $entity['expanded'], 'twitter.com' ) ) {
					$content = substr_replace( $content, '', $entity['starts'], $entity['ends'] - $entity['starts'] );
				} else {
					$content = substr_replace( $content, '</a>', $entity['ends'], 0 );
					$content = substr_replace( $content, '<a href="' . $entity['expanded'] . '" target="_blank">', $entity['starts'], 0 );
				}
				break;
			case 'media':
				$content = substr_replace( $content, '', $entity['starts'], $entity['ends'] - $entity['starts'] );
				break;
		}
	}

	$content = str_replace( "\n", "<br />", $content );

	if ( isset( $raw_tweet['quoted_status'] ) ) {
		$quoted = the_dammed_format_tweet( $raw_tweet['quoted_status'] );
		$header = the_dammed_tweet_header( $raw_tweet['quoted_status'] );
		$content .= "<article class='retweeted-status'><header>$header</header><div class='tweeted-status'>$quoted</div></article>";
	}

	return $content;
}

function the_dammed_tweet_header( $raw_tweet ) {
	$action = $raw_tweet['retweeted'] ?
		'retweetd <a href="https://twitter.com/' . $raw_tweet['retweeted_status']['user']['screen_name']  . '" target="_blank">' . $raw_tweet['retweeted_status']['user']['screen_name'] . '</a>' :
		'tweeted';
	$user = '<a href="https://twitter.com/' . $raw_tweet['user']['screen_name'] . '" target="_blank">@' . $raw_tweet['user']['screen_name'] . '</a>';
	return $user . ' ' . $action . ':';
}

function the_dammed_tweet_footer( $raw_tweet ) {
	$time = strtotime( $raw_tweet['created_at'] );
	$date = date( 'g:i A - M n, Y', $time );
	$tweet_date = '<a class="tweet-date" href="https://twitter.com/' . $raw_tweet['user']['screen_name'] . '/status/' . $raw_tweet['id'] . '" target="_blank">' . $date . '</a>';
	$tweet_link = '<a href="https://twitter.com/' . $raw_tweet['user']['screen_name'] . '/status/' . $raw_tweet['id'] . '" target="_blank">View on Twitter</a>';
	return $tweet_date . ' - ' . $tweet_link;
}

function the_dammed_instagram_location( $import_object ) {
	if ( ! $import_object->location ) {
		return;
	}
	$id = $import_object->location->id;
	$name = $import_object->location->name;
	$url_name = sanitize_title( $name );
	return "<a href='https://www.instagram.com/explore/locations/$id/$url_name' target='_blank'>$name</a>";
}

function the_dammed_pocket_url( $link ) {
	if ( $link['resolved_url'] ) {
		return $link['resolved_url'];
	}
	return $link['given_url'];
}

function the_dammed_pocket_first_video( $link ) {
	return array_shift(array_values( $link['videos'] ) );
}

function the_dammed_pocket_has_video( $link ) {
	return $link['has_video'] && ! empty( $link['videos'] );
}

function the_dammed_pocket_is_youtube( $link ) {
	if ( ! the_dammed_pocket_has_video( $link ) ) {
		return false;
	}
	return ( $link['domain_metadata'] && $link['domain_metadata']['name'] && 'YouTube' === $link['domain_metadata']['name'] );
}
