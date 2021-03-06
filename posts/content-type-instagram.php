<?php
$post_id = get_the_ID();
$raw_import_data = get_post_meta( $post_id, 'raw_import_data', true );
$import_object = json_decode( $raw_import_data );
$media = the_dammed_get_first_attachment_url();

?>

<div class="dammed-card type-instagram" data-type="instagram">
    <div class="dammed-content">
        <div class="instagram-header">
            <?php the_dammed_instagram_header( $import_object ); ?>
        </div>
        <div class="media-box">
            <?php the_dammed_instagram_media(); ?>
        </div>
        <div class="instagram-footer">
            <div class="instagram-header alternate">
	            <?php the_dammed_instagram_header( $import_object ); ?>
            </div>
	        <?php if( $import_object->caption ): ?>
                <p class="instagram-caption"><?php echo $import_object->caption->text ?></p>
	        <?php endif; ?>
            <p class="instagram-date">Posted <?php the_time( 'M j, Y' ); ?>
                on <a href="<?php echo $import_object->link; ?>" target="_blank">Instagram</a></p>
        </div>
    </div>
</div>
