<?php
$post_id = get_the_ID();
$raw_import_data = get_post_meta( $post_id, 'raw_import_data', true );
$import_object = json_decode( $raw_import_data );
$media = the_dammed_get_attachment_url();

?>

<?php if( $import_object->location ): ?>
    <?php
        $map_src = 'https://maps.googleapis.com/maps/api/staticmap?size=640x320&' .
                   'markers=' . $import_object->location->latitude . ',' .
                   $import_object->location->longitude .'&key=' . GOOGLE_MAPS_API_KEY;
    ?>
    <div class="dammed-card type-instagram" data-type="instagram">
        <div class="dammed-content">
            <div class="media-box">
                <img class="media-main" src="<?php echo $media; ?>" />
                <img class="media-secondary" src="<?php echo $map_src;?>" />
				<?php if( $import_object->caption ): ?>
                    <p class="media-caption"><?php echo $import_object->caption->text ?></p>
				<?php endif; ?>
            </div>
            <p>something something</p>
        </div>
    </div>
<?php else : ?>
    <div class="dammed-card type-instagram" data-type="instagram">
        <div class="dammed-content">
            <div class="media-box">
                <img class="media-main" src="<?php echo $media; ?>" />
	            <?php if( $import_object->caption ): ?>
                    <p class="media-caption"><?php echo $import_object->caption->text ?></p>
	            <?php endif; ?>
            </div>
            <p>Something something</p>
        </div>
    </div>
<?php endif; ?>
