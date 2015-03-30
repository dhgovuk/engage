<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages and that
 * other 'pages' on your WordPress site will use a different template.
 */
$perm_contact_forms =  array(
  'report-a-comment' => get_option( 'dh_page_report-a-comment', 0 ),
  'contact-us' => get_option( 'dh_page_contact-us', 0 ),
);
$front_page = get_option( 'page_on_front', 0 );
get_header(); ?>

<div id="main-content" class="main-content">

<?php
  if ( is_front_page() && dh_has_featured_posts() ) {
    // Include the featured content template.
    get_template_part( 'featured-content' );
  }
?>
  <section id="primary" class="section-row">
    <div id="content" class="grid-<?php echo ( $front_page == $page_id ) ? 3 : 2; ?>-3 grid-centered" role="main">

      <?php
        // Start the Loop.
        while ( have_posts() ) : the_post();
          $suffix = array_search( get_post()->ID, $perm_contact_forms);

          if ( $suffix ) {
            $suffix = '-' . $suffix;
          }
          else {
            $suffix = '';
          }
          // Include the page content template.
          get_template_part( 'content', get_post()->post_type . $suffix );

          // If comments are open or we have at least one comment, load up the comment template.
          if ( comments_open() || get_comments_number() ) {
            comments_template();
          }
        endwhile;
      ?>

    </div><!-- #content -->
  </section><!-- #primary -->
</div><!-- #main-content -->

<?php
get_footer();