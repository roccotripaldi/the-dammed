<?php
$post_id = get_the_ID();
$json_link = get_post_meta( $post_id, 'raw_import_data', true );
$raw_link = json_decode( strip_tags( $json_link ), true );
?>
<div class="dammed-card type-pocket" data-type="pocket">
	<div class="dammed-content">
        <p class="card-meta">
            <span class="intro">
                On <?php the_time( 'Y-m-d' ); ?> Rocco saved a link to
                <a href="https://getpocket.com" target="_blank" class="pocket-link">Pocket</a>
            </span>
        </p>
		<article>
            <header><?php the_dammed_pocket_title( $raw_link ); ?></header>
            <?php the_dammed_pocket_content( $raw_link ); ?>
            <footer><?php the_dammed_pocket_footer( $raw_link ); ?></footer>
        </article>
	</div>
</div>

