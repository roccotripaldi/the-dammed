<?php
$post_id = get_the_ID();
$foursquare_id = get_post_meta( $post_id, 'foursquare_id', true );
$geo_latitude = get_post_meta( $post_id, 'geo_latitude', true );
$geo_longitude = get_post_meta( $post_id, 'geo_longitude', true );
$img_src = 'https://maps.googleapis.com/maps/api/staticmap?size=640x320&' .
           'markers=' . $geo_latitude . ',' . $geo_longitude .'&key=' . GOOGLE_MAPS_API_KEY;
$foursquare_link = 'https://www.swarmapp.com/roccotripaldi/checkin/' . $foursquare_id;
$raw_import_data = get_post_meta( $post_id, 'raw_import_data', true );
$import_object = json_decode( $raw_import_data );
?>
<div class="dammed-card type-swarm" data-type="swarm">
	<div class="dammed-content">
        <img src="<?php echo $img_src; ?>" />
        <p>On <?php the_time( 'M j, Y' ); ?> Rocco<br /><?php the_title(); ?><br />
            <?php echo the_dammed_swarm_location( $import_object ); ?>:
            <a href="<?php echo $foursquare_link; ?>">View on Swarm</a>
        </p>
	</div>
</div>
