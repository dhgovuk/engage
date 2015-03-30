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

class DH_Widget_Question extends DH_Super_Widget {
  /**
   * Constructor.
   */
  public function __construct() {
    parent::__construct( 'widget_dh_question', __( 'DH: Question', 'dh' ), array(
        'description' => __( 'Use this widget to display a question and one of its answers (comments).', 'dh' ),
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
    $width = parent::get_width( $instance );
    $row_width = parent::get_row_width($instance);

    if ( $instance['question'] ) {
      $question = get_page($instance['question']);

      $text = $question->post_content;

      if ($cut_point = strpos($text, '<!--more-->')) {
        $text = substr($text, 0, $cut_point);
      }
      else {
        if ( 150 < strlen( strip_tags( $text ) ) ) {
          $text = force_balance_tags( _dh_html_truncate( $text, 150 ) . '&hellip;' );
        }
      }

      if ( $comment = get_comment($instance['comment']) ) {
        // Cool, we got the comment
      }
      else {
        $comments = get_comments( array( 'post_id' => $instance['question'], 'parent' => 0 ) );
        $comment = $comments[0];
      }
      $comment_date = date('j F Y  |  H:iA', strtotime($comment->comment_date));
      $comment_author = ($comment->comment_author) ? $comment->comment_author : 'Anonymous';
      $comment_text = esc_html(strip_tags($comment->comment_content));
      if ( strlen( strip_tags( $comment_text ) ) > 70) {
        $comment_text = _dh_html_truncate($comment_text, 70) . '&hellip;';
      }

      echo $args['before_widget'];
      ?>
      <div class="grid-<?php echo $width . '-' . $row_width; ?>">
        <div class="block-spotlight-text">
          <h3><a href="<?php echo get_permalink( $question->ID ); ?>"><?php echo esc_html( $question->post_title ); ?></a></h3>
          <p><?php echo $text; ?></p>
          <?php if ( $comment ): ?>
          ---
          <div class="comments-meta">
            <div class="comments-date"><?php echo $comment_date; ?></div>
            <div class="comments-author"><?php echo $comment_author; ?></div>
          </div>
          <p><?php echo $comment_text; ?></p>
          <?php endif; ?>
        </div>
      </div>
      <?php

      echo $args['after_widget'];
    }
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
    $instance = parent::update( $new_instance, $instance, array( 'width' ) );

    $instance['question'] = is_numeric($new_instance['question']) ? $new_instance['question'] : -1;
    $instance['comment'] = is_numeric($new_instance['comment']) ? $new_instance['comment'] : -1;

    return $instance;
  }

  /**
   * Display the form for this widget on the Widgets page of the Admin area.
   *
   * @param array $instance
   */
  function form( $instance, $fields = Array() ) {
    echo '<span class="widget-form-elements-container">';

    parent::form( $instance, array( 'width' ) );

    $questions_defaults = array(
      'depth' => 0, 'child_of' => 0,
      'selected' => $instance['question'], 'echo' => 1,
      'name' => 'page_id', 'id' => '',
      'show_option_none' => '', 'show_option_no_change' => '',
      'option_none_value' => ''
    );

    $questions = get_posts( array( 'post_type' => 'question', 'numberposts' => -1 ) );

    ?>
      <p>
        <label for="<?php echo esc_attr( $this->get_field_id( 'question' ) ); ?>"><?php _e( 'Question:', 'dh' ); ?></label>
        <select id="<?php echo esc_attr( $this->get_field_id( 'question' ) ); ?>" class="dh-question--question" name="<?php echo esc_attr( $this->get_field_name( 'question' ) ); ?>">
          <option value="-1"> --- </option>
          <?php print walk_page_dropdown_tree( $questions, 0, $questions_defaults ); ?>
        </select>
      </p>
      <p>
        <label for="<?php echo esc_attr( $this->get_field_id( 'comment' ) ); ?>"><?php _e( 'Comment:', 'dh' ); ?></label>
        <select id="<?php echo esc_attr( $this->get_field_id( 'comment' ) ); ?>" class="dh-question--comment" name="<?php echo esc_attr( $this->get_field_name( 'comment' ) ); ?>">
          <?php echo dh_prepare_post_comments_options_html( $instance['question'] ); ?>
        </select>
      </p>
    <?php
    echo '</span>';
  }
}