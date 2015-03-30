<?php
/**
 * The template for displaying 404 pages (Not Found)
 */

get_header(); ?>


	<section id="primary" class="content-area section-row">
		<div id="content" class="site-content" role="main">

			<header class="page-header">
				<h1 class="page-title"><?php _e( 'Not Found', 'dh' ); ?></h1>
			</header>

			<div class="page-content">
				<p><?php _e( 'It looks like nothing was found at this location. Maybe try a search?', 'dh' ); ?></p>

				<?php get_search_form(); ?>
			</div><!-- .page-content -->

		</div><!-- #content -->
	</section><!-- #primary -->

<?php
get_footer();
