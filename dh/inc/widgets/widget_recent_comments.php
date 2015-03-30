<?php
/**
 * DH: Text Widget
 *
 * Inherits from DH_Super_Widget
 *
 * @see super_widget.php
 * @author Khaled.zaidan
 *
 */

class DH_Widget_Recent_Comments extends DH_Super_Widget {
  /**
   * Constructor.
   */
  public function __construct() {
    parent::__construct( 'widget_dh_recent_comments', __( 'DH: Recent Comments', 'dh' ), array(
        'description' => __( 'Use this widget to display the the most recent comments. The comments are automatically narrowed down when on a category page.', 'dh' ),
    ) );
  }

  /**
   * Output the HTML for this widget.
   *
   * @access public
   *
   * @param array $args     An array of standard parameters for widgets in this theme.
   * @param array $instance An array of settings for this widget instance.
   */
  public function widget( $args, $instance ) {
    $title = parent::get_title_display( $instance );
    if (empty($title)) {
      $title = 'Recent Comments';
    }
    $width = parent::get_width( $instance );
    $row_width = parent::get_row_width( $instance );

    $text = $instance['text'];

    echo $args['before_widget'];


    /**
     * Load recent comments
     */
    // Try to load a current category
    $category = get_queried_object();
    if ( !empty($category->term_id) && !empty($category->taxonomy) ) {
      $comments = _dh_get_comments_by_category($category, $instance['num_of_comments']);
      foreach ($comments as &$comment) {
        $comment->comment_author = get_comment_author($comment->comment_ID);
      }
    }
    else {
      $comments_args = array(
        'status' => 'approve',
        'number' => $instance['num_of_comments'],
      );
      $comments = get_comments( $comments_args );
    }


    ?>
    <div class="grid-<?php echo $width . '-' . $row_width; ?>">
      <div class="block-spotlight-text">
        <h3><?php echo $title; ?></h3>
        <?php if ( $text ): ?>
          <p><?php echo strip_tags( do_shortcode( nl2br( $text ) ), '<div><a><br><p><em><i><b><strong><iframe><ul><ol><li>' ); ?></p>
        <?php endif; ?>
        <?php foreach ($comments as $comment): ?>
          <?php
          $comment_date = date('j F Y  |  H:iA', strtotime($comment->comment_date));
          $comment_author = ($comment->comment_author) ? $comment->comment_author : 'Anonymous';
          $comment_text = esc_html(strip_tags($comment->comment_content));
          if ( strlen( strip_tags( $comment_text ) ) > 70 * $width) {
            $comment_text = _dh_html_truncate($comment_text, 70 * $width) . '&hellip;';
          }
          $comment_post = get_post( $comment->comment_post_ID );
          ?>
          <h3><a href="<?php echo get_permalink( $comment_post->ID ); ?>"><?php print esc_html( $comment_post->post_title ); ?></a></h3>
          ---
          <div class="comments-meta">
            <div class="comments-date"><?php echo $comment_date; ?></div>
            <div class="comments-author"><?php echo $comment_author; ?></div>
          </div>
          <p><?php echo $comment_text; ?></p>
        <?php endforeach; ?>
      </div>
    </div>
    <?php

    echo $args['after_widget'];
  }

  /**
   * Deal with the settings when they are saved by the admin.
   *
   * Here is where any validation should happen.
   *
   * @param array $new_instance New widget instance.
   * @param array $instance     Original widget instance.
   * @return array Updated widget instance.
   */
  function update( $new_instance, $instance, $fields = Array() ) {
    $instance = parent::update( $new_instance, $instance );

    $instance['text'] = $new_instance['text'];

    $instance['num_of_comments'] = ( is_numeric($new_instance['num_of_comments']) && $new_instance['num_of_comments'] > 0 ) ? $new_instance['num_of_comments'] : 5;

    return $instance;
  }

  /**
   * Display the form for this widget on the Widgets page of the Admin area.
   *
   * @param array $instance
   */
  function form( $instance, $fields = Array() ) {
    parent::form( $instance, array( 'title', 'width', 'title help' => 'Default to "Recent Comments"' ) );

    $text  = empty( $instance['text'] ) ? '' : $instance['text'];
    $num_of_comments = isset( $instance['num_of_comments'] ) ? (int)($instance['num_of_comments']) : '';
    ?>
      <p>
        <label for="<?php echo esc_attr( $this->get_field_id( 'text' ) ); ?>"><?php _e( 'Text:', 'dh' ); ?></label>
        <textarea id="<?php echo esc_attr( $this->get_field_id( 'text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'text' ) ); ?>" rows="8"><?php echo esc_textarea( $text ); ?></textarea>
      </p>
      <p>
        <label for="<?php echo esc_attr( $this->get_field_id( 'num_of_comments' ) ); ?>"><?php _e( 'Number of comments:', 'dh' ); ?></label>
        <input id="<?php echo esc_attr( $this->get_field_id( 'num_of_comments' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'num_of_comments' ) ); ?>" type="text" value="<?php echo $num_of_comments; ?>">
      </p>
    <?php
  }
}