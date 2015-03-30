<?php
/**
 * DH: Newsletter
 *
 * Inherits from DH_Super_Widget
 *
 * @see super_widget.php
 * @author Khaled.zaidan
 *
 */

class DH_Widget_Newsletter extends DH_Super_Widget {
  /**
   * Constructor.
   */
  public function __construct() {
    parent::__construct( 'widget_dh_newsletter', __( 'DH: Newsletter', 'dh' ), array(
        'description' => __( 'Use this widget to display a newsletter subscription form.', 'dh' ),
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
    $row_width = parent::get_row_width( $instance );
    if ( ! $title ) {
      $title = 'Email updates';
    }

    $text = empty( $instance['text'] ) ? "To sign up for updates please enter your email address below." : $instance['text'];

    $style = empty($instance['style']) ? 1 : $instance['style'];
    $topic_id = empty($instance['topic_id']) ? '' : $instance['topic_id'];

    echo $args['before_widget'];
    ?>
    <div class="grid-1-<?php echo $row_width; ?>">
      <div class="block-spotlight-text">
        <h3><?php echo esc_html($title); ?></h3>
        <p><?php echo esc_html($text); ?></p>
        <?php if ( $style == 1 ): ?>
          <a href='https://public.govdelivery.com/accounts/UKDH/subscriber/new?topic_id=<?php echo esc_url($topic_id); ?>'>Click to subscribe</a>
        <?php else: ?>
        <form id="newsletter-form-<?php echo $this->number; ?>" accept-charset="UTF-8" action="https://public.govdelivery.com/accounts/UKDH/subscribers/qualify" method="post">
          <div style="margin:0;padding:0;display:inline">
            <input name="utf8" type="hidden" value="✓" />
            <input name="authenticity_token" type="hidden" value="fpDUF0E54P0eIsD2Jd0BYQW8QFnOAs/39gdiuVAk8A4=" />
          </div>
          <input id="topic_id" name="topic_id" type="hidden" value="<?php echo esc_attr($topic_id); ?>" />
          <input class="long" id="email" name="email" type="text" placeholder="Your email address" />
          <input class="signup" name="commit" type="submit" value="Submit" />
        </form>
        <?php endif; ?>
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
    $instance = parent::update( $new_instance, $instance, array( 'title' ) );

    $instance['text'] = $new_instance['text'];
    $instance['topic_id'] = $new_instance['topic_id'];
    $instance['style'] = $new_instance['style'];

    return $instance;
  }

  /**
   * Display the form for this widget on the Widgets page of the Admin area.
   *
   * @param array $instance
   */
  function form( $instance, $fields = Array() ) {
    parent::form( $instance, array() );

    $title = empty( $instance['title'] ) ? '' : $instance['title'];
    ?>
      <p><label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title:', 'dh' ); ?></label>
      <input id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
      <br/>(Defaults to "Email updates" if left empty)</p>
    <?php
    $topic_id = empty( $instance['topic_id'] ) ? '' : $instance['topic_id'];
    ?>
      <p><label for="<?php echo esc_attr( $this->get_field_id( 'topic_id' ) ); ?>"><?php _e( 'Topic ID:', 'dh' ); ?></label>
      <input id="<?php echo esc_attr( $this->get_field_id( 'topic_id' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'topic_id' ) ); ?>" type="text" value="<?php echo esc_attr( $topic_id ); ?>">
    <?php

    $style   = empty( $instance['style'] ) ? 1 : $instance['style'];
    ?>
      <p><label for="<?php echo esc_attr( $this->get_field_id( 'style' ) ); ?>"><?php _e( 'Style:', 'dh' ); ?></label>
      <select id="<?php echo esc_attr( $this->get_field_id( 'style' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'style' ) ); ?>">
        <option value="1" <?php if ($style == 1) { echo 'selected="selected"'; } ?>>Link</option>
        <option value="2" <?php if ($style == 2) { echo 'selected="selected"'; } ?>>Form</option>
      </select>
    <?php

    $text   = empty( $instance['text'] ) ? '' : $instance['text'];
    ?>
      <p><label for="<?php echo esc_attr( $this->get_field_id( 'text' ) ); ?>"><?php _e( 'Text:', 'dh' ); ?></label>
      <textarea id="<?php echo esc_attr( $this->get_field_id( 'text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'text' ) ); ?>" rows="8"><?php echo esc_attr( $text ); ?></textarea>
      <br/>(Defaults to "To sign up for updates or to access your subscriber preferences, please enter your contact information below.")</p>
    <?php
  }
}
