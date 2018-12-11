<?php if ( the_dammed_is_post_spotify_type() ) : ?>
	<?php get_template_part( 'posts/content-type-spotify' ); ?>
<?php elseif ( the_dammed_is_post_twitter_type() ) : ?>
	<?php get_template_part( 'posts/content-type-twitter' ); ?>
<?php elseif ( the_dammed_is_post_swarm_type() ) : ?>
	<?php get_template_part( 'posts/content-type-swarm' ); ?>
<?php elseif( the_dammed_is_post_instagram_type() ) : ?>
    <?php get_template_part( 'posts/content-type-instagram' ); ?>
<?php elseif( the_dammed_is_post_pocket_type() ) : ?>
	<?php get_template_part( 'posts/content-type-pocket' ); ?>
<?php else: ?>
	<div class="dammed-card" data-type="rocco">
		<div class="dammed-content">
			<?php the_content(); ?>
		</div>
	</div>
<?php endif; ?>

