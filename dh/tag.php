<?php
/**
 * The template for displaying Tag pages
 *
 * Used to display archive-type pages for posts in a tag.
 *
 * @link http://codex.wordpress.org/Template_Hierarchy
 */

get_header(); ?>

	<section id="primary" class="content-area section-row">
		<div id="content" class="site-content" role="main">

			<?php if ( have_posts() ) : ?>

			<header class="archive-header">
				<h1 class="archive-title"><?php single_tag_title( '' ); ?></h1>

				<?php
					// Show an optional term description.
					$term_description = term_description();
					if ( ! empty( $term_description ) ) :
						printf( '<div class="taxonomy-description">%s</div>', $term_description );
					endif;
				?>
			</header><!-- .archive-header -->

			<div id="content-tag-main-content">
        <?php
          global $wp_query;
          $current_posts_page = 0;
          $target_tag = get_queried_object();
          $fetched_posts = get_posts( array( 'tag' => get_queried_object()->slug, 'status' => 'approve', 'order' => 'DESC', 'orderby' => 'date', 'posts_per_page' => -1 ) );

          $target_page = get_query_var('paged');
          if ( ! $target_page ) { $target_page = 1; }

          $count = 0;
          // Displaying all the posts from the previous pages (this is the fallback with JS/AJAX is not available)
          while ( $count < ( $target_page - 1 ) * DH_POSTS_PER_BLOG_PAGE ) {
            $current_posts_page++;

            the_widget('DH_Widget_News_Spotlight', array('spotlight_post' => $fetched_posts[$count]->ID, 'width' => 1, 'force_render' => TRUE));

						$count++;
						if ( $count % 3 == 0 ) {
              echo '<div style="clear:both;"></div>';
            }
          }
        ?>
        <script>
          var currentPostsPage = <?php echo $target_page; ?>;
          var maxPostsPage = <?php echo (int)( $wp_query->max_num_pages ); ?>;
          var currentTagSlug = '<?php echo get_queried_object()->slug; ?>';
        </script>
  			<?php
					// Start the Loop.

					while ( have_posts() ) : the_post();

						/*
						 * Include the post format-specific template for the content. If you want to
						 * use this in a child theme, then include a file called called content-___.php
						 * (where ___ is the post format) and that will be used instead.
						 */
						//get_template_part( 'content', get_post_format() );

						the_widget('DH_Widget_News_Spotlight', array('spotlight_post' => get_post()->ID, 'width' => 1, 'force_render' => TRUE));

						$count++;
						if ( $count % 3 == 0 ) {
              echo '<div style="clear:both;"></div>';
            }

					endwhile;
					// Previous/next page navigation.
          ?></div><?php
					if ( $target_page < (int)($wp_query->max_num_pages) ) {
            echo '<div style="clear:both;"></div>';
					  echo '<a href="' . html_entity_decode( get_pagenum_link() ) . 'page/' . ( $target_page + 1 ) . '/" id="tag_posts-load-more-button" class="button-primary">Load more</a>';
					}
					//dh_paging_nav();
        ?>
				<?php else :
					// If no content, include the "No posts found" template.
					get_template_part( 'content', 'none' );
				endif; ?>
		</div><!-- #content -->
	</section><!-- #primary -->

<?php
get_footer();
