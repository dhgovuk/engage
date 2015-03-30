<?php
/**
 * Custom template tags for Twenty Fourteen
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 * @since Twenty Fourteen 1.0
 */

if ( ! function_exists( 'dh_paging_nav' ) ) :
/**
 * Display navigation to next/previous set of posts when applicable.
 *
 * @global WP_Query   $wp_query   WordPress Query object.
 * @global WP_Rewrite $wp_rewrite WordPress Rewrite object.
 */
function dh_paging_nav() {
  global $wp_query, $wp_rewrite;

  // Don't print empty markup if there's only one page.
  if ( $wp_query->max_num_pages < 2 ) {
    return;
  }

  $paged        = get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1;
  $pagenum_link = html_entity_decode( get_pagenum_link() );
  $query_args   = array();
  $url_parts    = explode( '?', $pagenum_link );

  if ( isset( $url_parts[1] ) ) {
    wp_parse_str( $url_parts[1], $query_args );
  }

  $pagenum_link = remove_query_arg( array_keys( $query_args ), $pagenum_link );
  $pagenum_link = trailingslashit( $pagenum_link ) . '%_%';

  $format  = $wp_rewrite->using_index_permalinks() && ! strpos( $pagenum_link, 'index.php' ) ? 'index.php/' : '';
  $format .= $wp_rewrite->using_permalinks() ? user_trailingslashit( $wp_rewrite->pagination_base . '/%#%', 'paged' ) : '?paged=%#%';

  $new_query_args = array();
  foreach ( $query_args as &$query_arg) {
    if ( ! is_array( $query_arg ) ) {
      $query_arg = urlencode($query_arg);
    } else {
      foreach ( $query_arg as $index => $query_arg_sub ) {
        $query_arg[$index] = urlencode($query_arg_sub);
      }
    }
  }

  // Set up paginated links.
  $links = paginate_links( array(
    'base'     => $pagenum_link,
    'format'   => $format,
    'total'    => $wp_query->max_num_pages,
    'current'  => $paged,
    'mid_size' => 1,
    'add_args' => $query_args,
    'prev_text' => __( '&larr; Previous', 'dh' ),
    'next_text' => __( 'Next &rarr;', 'dh' ),
  ) );

  if ( $links ) :

  ?>
  <nav class="navigation paging-navigation" role="navigation">
    <div class="pagination loop-pagination">
      <?php echo $links; ?>
    </div><!-- .pagination -->
  </nav><!-- .navigation -->
  <?php
  endif;
}
endif;

/**
 * Find out if blog has more than one category.
 *
 * @since Twenty Fourteen 1.0
 *
 * @return boolean true if blog has more than 1 category
 */
function dh_categorized_blog() {
	if ( false === ( $all_the_cool_cats = get_transient( 'dh_category_count' ) ) ) {
		// Create an array of all the categories that are attached to posts
		$all_the_cool_cats = get_categories( array(
			'hide_empty' => 1,
		) );

		// Count the number of categories that are attached to the posts
		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( 'dh_category_count', $all_the_cool_cats );
	}

	if ( 1 !== (int) $all_the_cool_cats ) {
		// This blog has more than 1 category so dh_categorized_blog should return true
		return true;
	} else {
		// This blog has only 1 category so dh_categorized_blog should return false
		return false;
	}
}

/**
 * Display an optional post thumbnail.
 *
 * Wraps the post thumbnail in an anchor element on index
 * views, or a div element when on single views.
 *
 * @since Twenty Fourteen 1.0
 */
function dh_post_thumbnail() {return;
	if ( post_password_required() || is_attachment() || ! has_post_thumbnail() ) {
		return;
	}

	if ( is_singular() ) :
	?>

	<div class="post-thumbnail">
	<?php
		if ( ( ! is_active_sidebar( 'sidebar-2' ) || is_page_template( 'page-templates/full-width.php' ) ) ) {
			the_post_thumbnail( 'dh-full-width' );
		} else {
			the_post_thumbnail();
		}
	?>
	</div>

	<?php else : ?>

	<a class="post-thumbnail" href="<?php the_permalink(); ?>">
	<?php
		if ( ( ! is_active_sidebar( 'sidebar-2' ) || is_page_template( 'page-templates/full-width.php' ) ) ) {
			the_post_thumbnail( 'dh-full-width' );
		} else {
			the_post_thumbnail();
		}
	?>
	</a>

	<?php endif; // End is_singular()
}
