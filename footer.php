<?php wp_footer(); ?>

<?php if ( ! is_404() ) : ?>
 <?php get_template_part( 'pagination/main' ); ?>
<?php endif; ?>

<!-- end dammed-container --></div>

<a id="dammed-button" class="dark-text box-shadow">&triangledown;</a>
<!-- end dammed-app --></div>

<div id="dammed-loader">
    <p>My life is as gorgeous as <?php echo get_gorgeous_thing(); ?>
        and it may take a few seconds to compile.<br />
        Thank you for your patience.
    </p>
</div>

</body>
</html>
