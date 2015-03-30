<?php
/**
 * The Bottom Sidebar
 */

if ( ! is_active_sidebar( 'sidebar-bottom' ) ) {
  return;
}
?>

<section class="section-row">
  <div class="grid-wrapper">
    <?php dynamic_sidebar( 'sidebar-bottom' ); ?>
  </div>
</section>
