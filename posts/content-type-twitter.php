<?php
$post_id = get_the_ID();
$twitter_id = get_post_meta( $post_id, 'twitter_id', true );
$json_tweet = get_post_meta( $post_id, 'raw_import_data', true );
$raw_tweet = json_decode( strip_tags( $json_tweet ), true );
// This is reliant on Jetpack's `tweet` shortcode.
$content = the_dammed_format_tweet( $raw_tweet );
?>
<div class="dammed-card type-twitter" data-type="twitter">
	<div class="dammed-content">
       <p><?php echo $content; ?></p>
	</div>
</div>
