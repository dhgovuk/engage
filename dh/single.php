<?php
/**
 * The Template for displaying all single posts
 */

get_header(); ?>

  <section id="primary" class="section-row">
    <div id="content" class="grid-<?php echo (get_post_type() == 'custom_layout') ? '3' : '2'; ?>-3 grid-centered" role="main">
      <?php
        // Start the Loop.
        while ( have_posts() ) : the_post();

          /*
           * Include the post format-specific template for the content. If you want to
           * use this in a child theme, then include a file called called content-___.php
           * (where ___ is the post format) and that will be used instead.
           */
          get_template_part( 'content', get_post_type() );

          // If comments are open or we have at least one comment, load up the comment template.
          if ( comments_open() || get_comments_number() ) {
            comments_template();
          }
        endwhile;
      ?>
    </div><!-- #content -->
  </section><!-- #primary -->

<?php
get_footer();
