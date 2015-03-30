<?php
/**
 * DH: Said Elsewhere Widget
 *
 * Inherits from DH_Super_Widget
 *
 * @see super_widget.php
 * @author Khaled.zaidan
 *
 */

class DH_Widget_Said_Elsewhere extends DH_Super_Widget {
  /**
   * Constructor.
   */
  public function __construct() {
    parent::__construct( 'widget_dh_said_elsewhere', __( 'DH: Said Elsewhere', 'dh' ), array(
        'description' => __( 'Use this widget to display content from another website.', 'dh' ),
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
    $width = parent::get_width( $instance );
    $row_width = parent::get_row_width($instance);

    $text   = empty( $instance['text'] ) ? '' : $instance['text'];
    $source = apply_filters( 'widget_title', empty( $instance['source'] ) ? '' : $instance['source'], $instance, $this->id_base );
    $url    = trim($instance['url']);

    echo $args['before_widget'];
    ?>
    <div class="grid-<?php echo $width . '-' . $row_width; ?>">
      <div class="block-spotlight block-spotlight-text">
        <h3><?php echo $title; ?></h3>
        <?php
        // Make sure the image ID is a valid attachment.
        if ( ! empty( $instance['image_id'] ) ) {
          $sizes = array(1 => 'thumbnail', 2 => 'medium', 3 => 'large');
          $image = get_post( $instance['image_id'] );
          if ( ! $image || 'attachment' != get_post_type( $image ) ) {
            $output = '<!-- Image Widget Error: Invalid Attachment ID -->';
          }
          else {
            echo wp_get_attachment_image( $instance['image_id'], $sizes[$width] );
          }
        }
        ?>
        <p><?php echo strip_tags( nl2br( $text ), '<div><a><br><p><em><i><b><strong><ul><ol><li>' ); ?></p>
        <div class="quote-author"><?php echo $source; ?></div>
        <?php if ($url): ?>
          <div class="quote-author"><a href="<?php echo esc_attr($url); ?>">Read more</a></div>
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
    $instance = parent::update( $new_instance, $instance, array( 'title', 'width' ) );

    $instance['image_id']   = absint( $new_instance['image_id'] );

    $instance['text'] = $new_instance['text'];
    $instance['source']  = strip_tags( $new_instance['source'] );
    $instance['url']  = $new_instance['url'];

    return $instance;
  }

  /**
   * Display the form for this widget on the Widgets page of the Admin area.
   *
   * @param array $instance
   */
  function form( $instance, $fields = Array() ) {
    parent::form( $instance, array( 'title', 'width' ) );

    _dh_widget_image_control( $this, $instance );

    $text   = empty( $instance['text'] ) ? '' : $instance['text'];
    $source = empty( $instance['source'] ) ? '' : $instance['source'];
    $url    = empty( $instance['url'] ) ? '' : $instance['url'];
    ?>
      <p><label for="<?php echo esc_attr( $this->get_field_id( 'text' ) ); ?>"><?php _e( 'Text:', 'dh' ); ?></label>
      <textarea id="<?php echo esc_attr( $this->get_field_id( 'text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'text' ) ); ?>" rows="8"><?php echo esc_textarea( $text ); ?></textarea></p>

      <p><label for="<?php echo esc_attr( $this->get_field_id( 'source' ) ); ?>"><?php _e( 'Source:', 'dh' ); ?></label>
      <input id="<?php echo esc_attr( $this->get_field_id( 'source' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'source' ) ); ?>" type="text" value="<?php echo esc_attr( $source ); ?>"></p>

      <p><label for="<?php echo esc_attr( $this->get_field_id( 'url' ) ); ?>"><?php _e( 'URL:', 'dh' ); ?></label>
      <input id="<?php echo esc_attr( $this->get_field_id( 'url' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'url' ) ); ?>" type="text" value="<?php echo esc_attr( $url ); ?>"></p>
    <?php
  }
}
