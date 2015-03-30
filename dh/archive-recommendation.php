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
 */

_dh_redirect_from_all_to_category();

get_header();

$all_recommendations = get_posts( array(
    'post_type' => 'recommendation',
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'meta_key' => 'dh_number',
    'meta_type' => 'NUMERIC',
    'orderby' => 'meta_value_num',
    'order' => 'ASC' ) );

$prepped_recommendations = array();
foreach ( $all_recommendations as $recommendation ) {
  $rec_object = new stdClass();
  $rec_object->title = $recommendation->post_title;
  $rec_object->titleInLower = strtolower($recommendation->post_title);
  $rec_object->URL = get_permalink($recommendation);
  foreach (get_post_taxonomies($recommendation->ID) as $taxonomy) {
    $terms = wp_get_post_terms($recommendation->ID, $taxonomy);
    $rec_object->{$taxonomy} = array();
    foreach ($terms as $term) {
      $rec_object->{$taxonomy}[] = $term->term_id;
    }
  }
  $rec_object->recommendationNumber = _dh_get_post_dh_num( $recommendation->ID, 'recommendation' );

  $prepped_recommendations[] = $rec_object;
}
?>
  <script type="text/javascript">
    var dhAllRecommendations = <?php echo json_encode( $prepped_recommendations ); ?>;
  </script>
	<section id="primary" class="content-area section-row">
		<div id="content" class="site-content" role="main">

			<header class="page-header">
				<h1 class="page-title">
					<?php
						if ( is_day() ) :
							printf( __( 'Daily Archives: %s', 'dh' ), get_the_date() );

						elseif ( is_month() ) :
							printf( __( 'Monthly Archives: %s', 'dh' ), get_the_date( _x( 'F Y', 'monthly archives date format', 'dh' ) ) );

						elseif ( is_year() ) :
							printf( __( 'Yearly Archives: %s', 'dh' ), get_the_date( _x( 'Y', 'yearly archives date format', 'dh' ) ) );

						else :
							_e( 'Recommendations', 'dh' );

						endif;
					?>
				</h1>
			</header><!-- .page-header -->
			<div class="grid-2-3">
        <div id="quick-text-filter" style="display: none;">
          <input type="text" id="recommendations-text-search" name="text-search" value="" placeholder="Quick Text filter">
        </div>
        <ul id="recommendations-main-list">
        <?php if ( have_posts() ) {
          // Start the Loop.
          $label = get_post_type_labels( get_post_type_object( get_post_type() ) );
          $label = $label->singular_name;
          while ( have_posts() ) : the_post();
            /*
             * Include the post format-specific template for the content. If you want to
             * use this in a child theme, then include a file called called content-___.php
             * (where ___ is the post format) and that will be used instead.
             */
          ?>
            <li>
              <div class="small-text"><?php echo $label . ' ' . _dh_get_post_dh_num( get_post()->ID, 'recommendation' ); ?></div>
              <a href="<?php echo get_permalink( get_post() ); ?>"><?php echo esc_html( get_post()->post_title ); ?></a>
            </li>
          <?php
					endwhile;
					// Previous/next page navigation.
					dh_paging_nav();
        }
        else {
					// If no content, include the "No posts found" template.
				  echo   '<li>No results match the chosen criteria!</li>';
				} ?>
        </ul>
			</div>
		  <?php
				the_widget('DH_Widget_Filters', array('content_type' => 'recommendation', 'force_render' => TRUE));
			?>
		</div><!-- #content -->
	</section><!-- #primary -->

<?php
get_footer();
