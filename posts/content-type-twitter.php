<?php
$post_id = get_the_ID();
$twitter_id = get_post_meta( $post_id, 'twitter_id', true );
$json_tweet = get_post_meta( $post_id, 'raw_import_data', true );
$raw_tweet = json_decode( strip_tags( $json_tweet ), true );
// This is reliant on Jetpack's `tweet` shortcode.
$content = the_dammed_format_tweet( $raw_tweet );
$class = $raw_tweet['retweeted'] ? 'retweeted-status' : 'tweeted-status';
?>
<div class="dammed-card type-twitter" data-type="twitter">
	<div class="dammed-content">
       <article class="tweet">
           <header>
               <?php echo the_dammed_tweet_header( $raw_tweet ); ?>
           </header>
           <div class="<?php echo $class; ?>">
	           <?php echo $content; ?>
           </div>
           <footer>
               <?php echo the_dammed_tweet_footer( $raw_tweet ); ?>
           </footer>
       </article>
	</div>
</div>
