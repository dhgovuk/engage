<?php
/**
 * The template for displaying Comments
 *
 * The area of the page that contains comments and the comment form.
 */

/*
 * If the current post is protected by a password and the visitor has not yet
 * entered the password we will return early without loading the comments.
 */
if ( post_password_required() ) {
  return;
}
?>

<div id="comments" class="comments-area">
  <?php if ( !comments_open() ): ?>
    <div class="info grid-centered grid-3-3">
      <h3>Comments are now closed</h3>
      <p><?php echo esc_html(get_theme_mod( 'dh_comment_closed_text' )); ?></p>
    </div>
  <?php endif; ?>

  <?php if ( have_comments() ) : ?>

    <h3>Comments <span class="commnets-number"><?php echo get_comments_number(); ?></span></h3>

    <div class="comments">
      <ul id="main-comments-list">
        <?php
          $current_comments_page = 0;
          $fetched_comments = get_comments( array( 'post_id' => get_post()->ID, 'status' => 'approve', 'order' => 'DESC', 'orderby' => 'ID' ) );

          // If a particular comment needs to be seen, there is some special preparation to be done
          $target_page = get_query_var('cpage');
          if ( ! $target_page ) { $target_page = 1; }
          if ( isset( $_GET['comment_id'] ) && get_comment( $_GET['comment_id'] ) && get_comment( $_GET['comment_id'] )->comment_post_ID == get_post()->ID ) {
            // Find the top-level comment that needs to be displayed
            $top_level_comment_index = _dh_fetch_comment_top_level( $fetched_comments, $_GET['comment_id'] );

            // Modify the target page number
            if ( $top_level_comment_index ) {
              $target_page = floor( $top_level_comment_index / get_option( 'comments_per_page' ) ) + 1;
            }
          }

          while ( $current_comments_page < max( $target_page, get_query_var('cpage') ) ) {
            $current_comments_page++;

            wp_list_comments( array(
            'walker'     => new DH_Walker_Comment,
            'style'      => 'ul',
            'callback'   => 'dh_comment',
            'short_ping' => true,
            'page'       => $current_comments_page,
            ), $fetched_comments );
          }
        ?>
      </ul>
      <?php if ( $current_comments_page < (int)(_dh_comments_max_page()) ): ?>
        <a href="<?php echo esc_url( get_comments_pagenum_link( $current_comments_page + 1, (int)(_dh_comments_max_page()) ) ); ?>" id="comments-load-more-button" class="button-primary">Load more</a>
      <?php endif; ?>
      <script>
        var currentCommentsMaxPage = <?php echo (int)(_dh_comments_max_page()); ?>;
        var currentCommentsPage = <?php echo $current_comments_page; ?>;
        var currentPostId = <?php echo get_post()->ID; ?>;
      </script>
    </div>
  <?php endif; // have_comments() ?>

  <?php
    if ( comments_open() ) {
      comment_form(array(
        'title_reply'          => __( 'Post a comment' ),
        'title_reply_to'       => __( 'Post a reply to %s' ),
        'label_submit'         => __( 'Post' ),
        'comment_notes_before' => '',
        'comment_notes_after'  => '',
        'comment_field'        => ( is_user_logged_in() ) ? implode(' ', dh_comment_fields(array())) : '',
      ));
    }
  ?>

</div><!-- #comments -->
