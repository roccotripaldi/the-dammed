<?php
$post_id = get_the_ID();
$foursquare_id = get_post_meta( $post_id, 'foursquare_id', true );
$geo_latitude = get_post_meta( $post_id, 'geo_latitude', true );
$geo_longitude = get_post_meta( $post_id, 'geo_longitude', true );
$map_src = 'https://maps.googleapis.com/maps/api/staticmap?size=640x320&' .
           'markers=' . $geo_latitude . ',' . $geo_longitude .'&key=' . GOOGLE_MAPS_API_KEY;
$foursquare_link = 'https://www.swarmapp.com/roccotripaldi/checkin/' . $foursquare_id;
$raw_import_data = get_post_meta( $post_id, 'raw_import_data', true );
$import_object = json_decode( $raw_import_data );
$shout = the_dammed_get_foursquare_shout( $post_id, $import_object );
?>

<div class="dammed-card type-swarm" data-type="swarm">
    <div class="dammed-content">
        <p class="card-meta">
            <span class="intro">On <?php the_time( 'Y-m-d' ); ?> Rocco was at</span>
            <span><b><?php the_dammed_swarm_place(); ?></b></span>
            <span><i><?php echo the_dammed_swarm_location( $import_object ); ?></i></span>
            <span class="link"><a href="<?php echo $foursquare_link; ?>">View on Swarm</a></span>
        </p>
        <div class="media-box">
            <img class="media-main" src="<?php echo $map_src; ?>" />
            <?php the_dammed_attached_photo(); ?>
		    <?php if( $shout ): ?>
                <p class="media-caption"><?php echo $shout ?></p>
		    <?php endif; ?>
        </div>
    </div>
</div>
