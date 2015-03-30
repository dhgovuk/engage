<?php
/**
 * DH: Consultation Summary Widget
 *
 * Inherits from DH_Super_Widget
 *
 * @see super_widget.php
 * @author Khaled.zaidan
 *
 */

class DH_Widget_Consultation_Summary extends DH_Super_Widget {
  /**
   * Constructor.
   */
  public function __construct() {
    parent::__construct( 'widget_dh_consultation_summary', __( 'DH: Consultation Summary', 'dh' ), array(
        'description' => __( 'Use this widget to display a summary of the consultation.', 'dh' ),
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
    $row_width = parent::get_row_width($instance);

    $close_date = empty( $instance['close_date'] ) ? '' : $instance['close_date'];
    $summary    = empty( $instance['summary'] ) ? '' : $instance['summary'];

    echo $args['before_widget'];
    ?>
    <div class="block-inner grid-<?php print $row_width . '-' . $row_width; ?>">
      <div class="summary-widget">
        <div class="grid-1-3">
          <p>This consultation closes at</p>
          <h2 class="summary-date"><?php echo esc_html( $close_date ); ?></h2>
        </div>
        <div class="grid-2-3">
          <h2>Summary</h2>
          <p><?php echo esc_html( $summary ); ?></p>
        </div>
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
    $instance = parent::update( $new_instance, $instance, array() );

    $instance['close_date'] = $new_instance['close_date'];
    $instance['summary']    = $new_instance['summary'];

    return $instance;
  }

  /**
   * Display the form for this widget on the Widgets page of the Admin area.
   *
   * @param array $instance
   */
  function form( $instance, $fields = Array() ) {
    parent::form( $instance, array() );

    $close_date = empty( $instance['close_date'] ) ? '' : $instance['close_date'];
    $summary    = empty( $instance['summary'] ) ? '' : $instance['summary'];
    ?>
      <p><label for="<?php echo esc_attr( $this->get_field_id( 'close_date' ) ); ?>"><?php _e( 'Close Date:', 'dh' ); ?></label>
      <input id="<?php echo esc_attr( $this->get_field_id( 'close_date' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'close_date' ) ); ?>" type="text" value="<?php echo esc_attr( $close_date ); ?>">
      <br/>(Ex: 23 October 2014 11.45pm)</p>

      <p><label for="<?php echo esc_attr( $this->get_field_id( 'summary' ) ); ?>"><?php _e( 'Summary:', 'dh' ); ?></label>
      <textarea id="<?php echo esc_attr( $this->get_field_id( 'summary' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'summary' ) ); ?>" rows="8"><?php echo esc_textarea( $summary ); ?></textarea></p>
    <?php
  }
}
