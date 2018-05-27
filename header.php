<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div id="dammed-app">
    <div id="dammed-date" class="dark-text box-shadow">
        <span id="date"><?php echo date( 'Y-m-d H:i:s' ); ?></span>
    </div>
    <div class="dammed-container">
        <header class="dammed-card" data-type="rocco">
            <div class="dammed-content">
                <img src="<?php echo get_template_directory_uri(); ?>/images/rocco-tripaldi-logo@2x.png" alt="Rocco Tripaldi" />
                <div class="site-info">
                    <p class="blogname"><?php echo get_bloginfo('blogname'); ?></p>
                    <p class="description"><?php echo get_bloginfo('description'); ?></p>
                </div>
            </div>
        </header>

