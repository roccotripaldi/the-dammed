<?php
    $post_id = get_the_ID();
    $roccos_spotify_profile_link = 'spotify:user:121311302';
    $album_info = get_post_meta( $post_id, 'album_info', true );
    if ( ! $album_info || empty( $album_info['artist'] ) || empty( $album_info['artist'] ) ) {
        $album_info = the_dammed_update_album_info( $post_id );
    }
    $spotify_info = get_post_meta( $post_id, 'spotify_info', true );
    if ( ! $spotify_info ) {
	    $spotify_info = the_dammed_update_spotify_info( $post_id, $album_info['artist'], $album_info['album'] );
    }
?>
<div class="dammed-card type-spotify" data-type="spotify">
    <div class="dammed-content">
        <p class="card-meta">
            <span class="intro">On <?php the_time( 'Y-m-d' ); ?>, <a href="<?php echo $roccos_spotify_profile_link; ?>">Rocco</a> played:</span>
            <?php the_dammed_spotify_album_text( $spotify_info, $album_info ); ?>
        </p>
        <?php the_dammed_spotify_album_art( $spotify_info, $album_info ); ?>
    </div>
</div>

