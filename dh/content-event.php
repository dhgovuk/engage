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
			if ( is_single() ) :
				echo '<h1 class="entry-title">' . esc_html(get_the_title()) . '</h1>';
			else :
				echo '<h1 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . esc_html(get_the_title()) . '</a></h1>';
			endif;
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
        switch ( $decision ) {
          case 'Rejected':
            echo '<div class="rejected">Rejected</div>';
            break;
          case 'Accepted':
            echo '<div class="accepted">Accepted</div>';
            break;
          default:
            // Nothing!
        }
      }
      if ( get_post()->post_type == 'event' ) {
        $start_date = get_field( 'start_date', get_post()->ID, TRUE);
        $end_date   = get_field( 'end_date', get_post()->ID, TRUE);
        $location   = get_field( 'location', get_post()->ID, TRUE);

        $time_text = date('j M Y, H:i - ', $start_date);
        if (date('j M Y', $start_date) == date('j M Y', $end_date)) {
          $time_text .= date('H:i', $end_date);
        }
        else {
          $time_text .= date('j M Y, H:i', $end_date);
        }

        echo '<div class="small-text">';
        echo   '<div class="date"><span class="icon-date"></span>' . $time_text . '</div>';
        if ( $location ) {
          echo   '<div class="location"><span class="icon-location"></span>' . esc_html($location) . '</div>';
        }
        echo '</div>';
      }
    ?>
    <?php
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
</article><!-- #post-## -->