<?php
/**
 * The template for displaying Archive pages
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * If you'd like to further customize these archive views, you may create a
 * new template file for each specific one. For example, Twenty Fourteen
 * already has tag.php for Tag archives, category.php for Category archives,
 * and author.php for Author archives.
 *
 * @link http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 * @since Twenty Fourteen 1.0
 */

get_header(); ?>

  <?php ob_start(); ?>
	<section id="primary" class="content-area section-row">
		<div id="content" class="site-content" role="main">

			<header class="page-header">
				<h1 class="page-title"><?php print get_queried_object()->name; ?></h1>
			</header><!-- .page-header -->

			<?php
			  $empty = TRUE;
				if ( have_posts() ) :
					// Start the Loop.
					echo '<ul>';
					while ( have_posts() ) : the_post();
  					$label = get_post_type_labels( get_post_type_object( get_post_type() ) );
  					$label = $label->singular_name;
  					// Skip Custom layouts
  					if ( get_post_type() == 'custom_layout' ) { continue; }
            $empty = FALSE;
						/*
						 * Include the post format-specific template for the content. If you want to
						 * use this in a child theme, then include a file called called content-___.php
						 * (where ___ is the post format) and that will be used instead.
						 */
					?>
					  <li>
					    <div class="small-text"><?php echo $label . ' ' . _dh_get_post_dh_num( get_post()->ID, get_post_type() ); ?></div>
					    <a href="<?php echo get_permalink( get_post() ); ?>"><?php echo esc_html( get_post()->post_title ); ?></a>
					  </li>
          <?php
					endwhile;
					echo '</ul>';
					// Previous/next page navigation.
					dh_paging_nav();

				endif;

				if ($empty) :
					// If no content, include the "No posts found" template.
					get_template_part( 'content', 'none' );

				endif;
			?>
		</div><!-- #content -->
	</section><!-- #primary -->
	<?php
	  $archive_output = ob_get_contents();
	  ob_end_clean();

	  $total_output = _dh_siteorigin_panels_render( get_option( 'dh_custom_layout', 0 ) );
	  echo str_replace('[CATEGORY-CONTENT]', $archive_output, $total_output);
	?>

<?php
get_footer();
