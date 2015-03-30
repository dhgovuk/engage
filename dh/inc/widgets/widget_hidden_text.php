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

class DH_Widget_Hidden_Text extends DH_Super_Widget {
  /**
   * Constructor.
   */
  public function __construct() {
    parent::__construct( 'widget_dh_hidden_text', __( 'DH: Hidden Text', 'dh' ), array(
        'description' => __( 'Use this widget to display hidden text (expandible with a click).', 'dh' ),
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
    $text  = empty( $instance['text'] ) ? '' : $instance['text'];

    echo $args['before_widget'];
    ?>
    <div class="grid-<?php echo $width . '-' . $row_width; ?>">
      <div class="block-text">
        <details>
          <summary aria-controls="hidden-text-<?php echo $this->number; ?>"><?php echo $title; ?></summary>
          <div class="indented" id="hidden-text-<?php echo $this->number; ?>"><?php echo strip_tags( nl2br( $text ), '<div><a><br><p><em><i><b><strong><ul><ol><li>' ); ?></div>
        </details>
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

    return $instance;
  }

  /**
   * Display the form for this widget on the Widgets page of the Admin area.
   *
   * @param array $instance
   */
  function form( $instance, $fields = Array() ) {
    parent::form( $instance );

    $text  = empty( $instance['text'] ) ? '' : $instance['text'];
    ?>
      <p><label for="<?php echo esc_attr( $this->get_field_id( 'text' ) ); ?>"><?php _e( 'Text:', 'dh' ); ?></label>
      <textarea id="<?php echo esc_attr( $this->get_field_id( 'text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'text' ) ); ?>" rows="8"><?php echo esc_textarea( $text ); ?></textarea></p>
    <?php
  }
}