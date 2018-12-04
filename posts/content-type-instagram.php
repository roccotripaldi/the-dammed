<?php
$post_id = get_the_ID();
$raw_import_data = get_post_meta( $post_id, 'raw_import_data', true );
$import_object = json_decode( $raw_import_data );
$media = the_dammed_get_attachment_url();

?>

<div class="dammed-card type-instagram" data-type="instagram">
    <div class="dammed-content">
        <div class="media-box">
            <img class="media-main" src="<?php echo $media; ?>" />
            <?php the_dammed_instagram_map( $import_object ); ?>
		    <?php if( $import_object->caption ): ?>
                <p class="media-caption"><?php echo $import_object->caption->text ?></p>
		    <?php endif; ?>
            <p>Something something</p>
        </div>
    </div>
</div>
