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
  		if ( isset( $_GET['comment'] ) && $reported_comment = get_comment( $_GET['comment'] ) ) {
  		  $query_char = '?';
        if ( strpos( get_permalink( $reported_comment->comment_post_ID ), '?' ) !== FALSE ) {
          $query_char = '&';
        }
  		  echo '<div> Comment:<br/>"<em>' . esc_html( $reported_comment->comment_content ) . '</em>"</div>';
  		  echo '<div><a href="' . get_permalink( $reported_comment->comment_post_ID ) . $query_char . 'comment_id=' . $reported_comment->comment_ID . '#comment-' . $reported_comment->comment_ID . '">Go to comment</a></div>';
  		}

		  the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'dh' ) );
			wp_link_pages( array(
				'before'      => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'dh' ) . '</span>',
				'after'       => '</div>',
				'link_before' => '<span>',
				'link_after'  => '</span>',
			) );
		?>
	</div><!-- .entry-content -->
	<script>
	  ( function( $ ) { $('#comment-url').val('<?php echo get_permalink( $reported_comment->comment_post_ID ) . $query_char . 'comment_id=' . $reported_comment->comment_ID . '#comment-' . $reported_comment->comment_ID; ?>').attr('disabled', 'disabled'); } )( jQuery );
	</script>
	<?php endif; ?>

	<?php the_tags( '<footer class="entry-meta"><span class="tag-links">', '', '</span></footer>' ); ?>
</article><!-- #post-## -->