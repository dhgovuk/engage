<?php
/**
 * The Content "Sidebars"
 */

if ( ! is_active_sidebar( 'sidebar-top' ) && ! get_theme_mod('dh_breadcrumbs') ) {
  return;
}

?>
<section class="section-row">
  <?php if ( get_theme_mod('dh_breadcrumbs') ) { dh_breadcrumbs(); } ?>

  <div class="grid-wrapper">
    <?php if ( is_active_sidebar( 'sidebar-top' ) ) { dynamic_sidebar( 'sidebar-top' ); } ?>
  </div>
</section>