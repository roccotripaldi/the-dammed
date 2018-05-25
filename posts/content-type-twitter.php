<?php
$post_id = get_the_ID();
$twitter_id = get_post_meta( $post_id, 'twitter_id', true );
$json_tweet = get_post_meta( $post_id, 'raw_import_data', true );
$tweet = json_decode( strip_tags( $json_tweet ), true );
// This is reliant on Jetpack's `tweet` shortcode.
$content = "[tweet $twitter_id hide_media='true']";
?>
<div class="dammed-card type-twitter" data-type="twitter">
	<div class="dammed-content">
        <?php if( $tweet['retweeted'] ) : ?>
            <p>Rocco retweeted:</p>
        <?php endif; ?>
		<?php echo apply_filters( 'the_content', $content ); ?>
	</div>
</div>
