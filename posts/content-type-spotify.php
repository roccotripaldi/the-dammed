<?php
    $post_id = get_the_ID();
    $cover_art = the_dammed_get_first_attachment_url();
    $roccos_spotify_profile_link = 'spotify:user:121311302';
    $album_info = get_post_meta( $post_id, 'album_info', true );
    if ( ! $album_info || empty( $album_info['artist'] ) || empty( $album_info['artist'] ) ) {
        $album_info = the_dammed_update_album_info( $post_id );
    }

    $spotify_info = get_post_meta( $post_id, 'spotify_info', true );
    if ( ! $spotify_info ) {
	    $spotify_info = the_dammed_update_spotify_info( $post_id, $album_info['artist'], $album_info['album'] );
    }
    $cover_alt_text = esc_attr( $album_info['album'] ) . ' by ' . esc_attr( $album_info['artist'] );
?>
<?php if ( empty( $spotify_info ) ) : ?>
    <div class="dammed-card type-spotify" data-type="spotify">
        <div class="dammed-content">
            <img src="<?php echo $cover_art; ?>" alt="<?php echo $cover_alt_text; ?>" />
            <p>
                On <?php the_date( 'M j, Y' ); ?>,<br />
                <a href="<?php echo $roccos_spotify_profile_link; ?>">Rocco</a> listened to:<br />
                <em><?php echo $album_info['album']; ?></em> by <?php echo $album_info['artist']; ?>
            </p>
        </div>
    </div>
<?php else : ?>
    <div class="dammed-card type-spotify" data-type="spotify">
        <div class="dammed-content">
            <a href="spotify:album:<?php echo $spotify_info['album_id']; ?>" class="img-link">
                <img src="<?php echo $cover_art; ?>" alt="<?php echo $cover_alt_text; ?>" />
            </a>
            <p>
                On <?php the_date( 'M j, Y' ); ?>,<br />
                <a href="<?php echo $roccos_spotify_profile_link; ?>">Rocco</a> listened to:<br />
                <em><?php echo $album_info['album']; ?></em> by
                <a href="spotify:artist:<?php echo $spotify_info['artist_id']; ?>">
                    <?php echo $album_info['artist']; ?>
                </a><br />
                <a href="spotify:album:<?php echo $spotify_info['album_id']; ?>" class="spotify-link">
                    Listen on Spotify
                </a>
            </p>
        </div>
    </div>
<?php endif; ?>

