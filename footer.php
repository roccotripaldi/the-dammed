<?php wp_footer(); ?>

<?php if ( ! is_404() ) : ?>
 <?php get_template_part( 'pagination/main' ); ?>
<?php endif; ?>

<!-- end dammed-container --></div>

<a id="dammed-button" class="dark-text box-shadow">&triangledown;</a>
<!-- end dammed-app --></div>

<div id="dammed-loader">
    <p>My life is as gorgeous as<br />
        ...<em><?php echo the_dammed_get_gorgeous_thing(); ?></em>...<br />
       and its worth the wait.<br />
        Thank you for your patience.
    </p>
</div>

</body>
</html>
