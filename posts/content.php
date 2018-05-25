<?php if ( the_dammed_is_post_spotify_type() ) : ?>
	<?php get_template_part( 'posts/content-type-spotify' ); ?>
<?php elseif ( the_dammed_is_post_twitter_type() ) : ?>
	<?php get_template_part( 'posts/content-type-twitter' ); ?>
<?php else: ?>
	<div class="dammed-card" data-type="rocco">
		<div class="dammed-content">
			<?php the_content(); ?>
		</div>
	</div>
<?php endif; ?>



