<?php
/**
 * DH: Quote Widget
 *
 * Inherits from DH_Super_Widget
 *
 * @see super_widget.php
 * @author Khaled.zaidan
 *
 */

class DH_Widget_Event extends DH_Super_Widget {
  /**
   * Constructor.
   */
  public function __construct() {
    parent::__construct( 'widget_dh_event', __( 'DH: Event', 'dh' ), array(
        'description' => __( 'Use this widget to display an event.', 'dh' ),
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
    $row_width = parent::get_row_width( $instance );

    if ( !empty($instance['event']) ) {
      // Load the event
      $event = get_page($instance['event']);

      // Prepare the URL
      if ( $instance['custom_url'] ) {
        $url = $instance['custom_url'];
      }
      else {
        $url = get_permalink($event->ID);
      }

      // Load the data of hte event
      $start_date = get_field( 'start_date', $event->ID, TRUE);
      $end_date   = get_field( 'end_date', $event->ID, TRUE);
      $location   = get_field( 'location', $event->ID, TRUE);

      $time_text = date('j M Y, H:i - ', $start_date);
      if (date('j M Y', $start_date) == date('j M Y', $end_date)) {
        $time_text .= date('H:i', $end_date);
      }
      else {
        $time_text .= date('j M Y, H:i', $end_date);
      }

      echo $args['before_widget'];
      ?>
      <div class="grid-<?php echo $width . '-' . $row_width; ?>">
        <div class="block-spotlight-text">
          <h3><a href="<?php echo $url; ?>"><?php echo esc_html($event->post_title); ?></a></h3>
          <div class="small-text">
            <div class="date"><span class="icon-date"></span><?php echo $time_text; ?></div>
            <?php if ( $location ): ?>
            <div class="location"><span class="icon-location"></span><?php echo esc_html($location); ?></div>
            <?php endif; ?>
          </div>
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
    $instance['event'] = is_numeric($new_instance['event']) ? $new_instance['event'] : -1;

    $instance['custom_url'] = $new_instance['custom_url'];

    return $instance;
  }

  /**
   * Display the form for this widget on the Widgets page of the Admin area.
   *
   * @param array $instance
   */
  function form( $instance, $fields = Array() ) {
    // This widget doesn't need a title nor a width
    // (title comes from chosen page/post and width is always 3)
    //parent::form( $instance );

    $events_defaults = array(
      'depth' => 0, 'child_of' => 0,
      'selected' => !empty($instance['event']) ? $instance['event'] : NULL, 'echo' => 1,
      'name' => 'page_id', 'id' => '',
      'show_option_none' => '', 'show_option_no_change' => '',
      'option_none_value' => ''
    );

    $events = get_posts( array('post_type' => 'event', 'numberposts' => -1) );

    $custom_url = !empty($instance['custom_url']) ? $instance['custom_url'] : '';
    ?>
      <p><label for="<?php echo esc_attr( $this->get_field_id( 'custom_url' ) ); ?>"><?php _e( 'Custom URL', 'dh' ); echo '<br/> (Make sure to include http:// or https://):'; ?></label>
      <input id="<?php echo esc_attr( $this->get_field_id( 'custom_url' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'custom_url' ) ); ?>" type="text" value="<?php echo esc_attr( $custom_url ); ?>"></p>
      <p><?php _e( 'Choose a page OR a post', 'dh' ); ?></p>
      <p>
        <label for="<?php echo esc_attr( $this->get_field_id( 'event' ) ); ?>"><?php _e( 'Event:', 'dh' ); ?></label>
        <select id="<?php echo esc_attr( $this->get_field_id( 'event' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'event' ) ); ?>">
          <option value="-1"> --- </option>
          <?php print walk_page_dropdown_tree( $events, 0, $events_defaults ); ?>
        </select>
      </p>
    <?php
  }
}
