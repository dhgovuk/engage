<?php
/**
 * The template for displaying the footer
 *
 * Contains footer content and the closing of the #main and #page div elements.
 */
?>
      <?php get_sidebar( 'bottom' ); ?>
    </div><!-- #page -->
  </div><!-- .container -->
  <?php if ( has_nav_menu( 'secondary' ) ) : ?>
    <div class="container">
      <footer>
        <?php wp_nav_menu( array( 'theme_location' => 'secondary', 'menu_class' => 'footer-links', 'container' => FALSE, 'depth' => 1 ) ); ?>
      </footer>
    </div>
  <?php endif; ?>

  <?php wp_footer(); ?>
</body>
</html>