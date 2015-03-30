<?php
/**
 * The default template for displaying content
 *
 * Used for both single and index/archive/search.
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php dh_post_thumbnail(); ?>

	<header class="entry-header">
		<?php if ( in_array( 'category', get_object_taxonomies( get_post_type() ) ) && dh_categorized_blog() ) : ?>
		<div class="entry-meta">
			<span class="cat-links"><?php echo get_the_category_list( _x( ', ', 'Used between list items, there is a space after the comma.', 'dh' ) ); ?></span>
		</div>
		<?php
			endif;
      edit_post_link( __( 'Edit', 'dh' ), '<span class="edit-link" style="float:right;">', '</span>' );
      if ( in_array( get_post()->post_type, array('recommendation', 'question') ) ) {
        $label = get_post_type_labels( get_post_type_object( get_post()->post_type ) );
        $label = $label->singular_name;
        echo '<div class="small-text">' . $label . ' ' . _dh_get_post_dh_num( get_post()->ID, get_post()->post_type ) . '</div>';
      }
      if ( ! is_front_page() || get_post()->ID != get_option( 'page_on_front', '' ) ) {
        if ( is_single() || is_page() ) :
  				echo '<h1 class="entry-title">' . esc_html((get_the_title())) . '</h1>';
  			else :
  				echo '<h1 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . esc_html((get_the_title())) . '</a></h1>';
  			endif;
			}
		?>

	</header><!-- .entry-header -->

	<?php if ( is_search() ) : ?>
	<div class="entry-summary">
		<?php the_excerpt(); ?>
	</div><!-- .entry-summary -->
	<?php else : ?>
	<div class="entry-content">
		<?php
		  if ( $decision = get_field('decision', get_post() ) ) {
        if (stripos($decision, 'Rejected') !== FALSE || stripos($decision, 'Not Accepted') !== FALSE) {
          echo '<div class="rejected">' . $decision . '</div>';
        }
        elseif (stripos($decision, 'Accepted') !== FALSE) {
          echo '<div class="accepted">' . $decision . '</div>';
        }
      }
			the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'dh' ) );

      _dh_print_file_links();
			wp_link_pages( array(
				'before'      => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'dh' ) . '</span>',
				'after'       => '</div>',
				'link_before' => '<span>',
				'link_after'  => '</span>',
			) );
		?>
	</div><!-- .entry-content -->
	<?php endif; ?>

	<?php the_tags( '<footer class="entry-meta"><span class="tag-links">', '', '</span></footer>' ); ?>
  <?php if ( !is_search() && in_array( get_post()->post_type, array( 'recommendation', 'question' ) ) ): ?>
    <?php
      $dh_id = _dh_get_post_dh_num( get_post()->ID, get_post()->post_type );
      $previous = '';
      $next = '';
      $next_n_prev = _dh_get_pnn_by_dh_num( $dh_id, get_post()->post_type );
      if ( $next_n_prev['prev'] ) {
        $previous = '<a href="' . esc_attr( get_permalink( $next_n_prev['prev'] ) ) . '" class="previous">Previous</a>';
      }
      if ( $next_n_prev['next'] ) {
        $next = '<a href="' . esc_attr( get_permalink( $next_n_prev['next'] ) ) . '" class="next">Next</a>';
      }
    ?>
    <div class="direction">
      <?php echo $previous; ?>
      <?php echo $next; ?>
    </div>
    <div style="clear:both;"></div>
  <?php endif; ?>
</article><!-- #post-## -->