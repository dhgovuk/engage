<?php
/**
 * Super widget for the DH theme
 * The width of each widget can be set to 1, 2 or 3 for a proper layout
 *
 * All DH widgets are inherited from this class
 */

class DH_Super_Widget extends WP_Widget {
  /**
   * Constructor.
   */
  public function __construct( $id_base, $name, $widget_options = array(), $control_options = array() ) {
    parent::__construct( $id_base, $name, $widget_options, $control_options );
  }

  protected function get_title_display( $instance ) {
    return apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
  }

  protected function get_width( $instance ) {
    $width = isset( $instance['width'] ) && in_array( $instance['width'], array(1, 2, 3) ) ? $instance['width'] : 1;

    if ( isset( $instance['dh_width_suggestion'] ) ) {
      $width = $instance['dh_width_suggestion'];
    }
    return $width;
  }

  protected function get_row_width( $instance ) {
    $row_width = 3;
    if ( isset( $instance['dh_row_width'] ) ) {
      $row_width = $instance['dh_row_width'];
    }
    return $row_width;
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
    $title = get_title_display( $instance );
    $width = get_width( $instance );
    $row_width = get_row_width($instance);

    echo $args['before_widget'];
    ?>
    <div class="grid-<?php echo $width . '-' . $row_width; ?>">
      <h2><?php echo $title; ?></h2>
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
  function update( $new_instance, $instance, $fields = array('title', 'width')) {
    if (in_array('title', $fields)) {
      if ( !empty( $new_instance['title'] ) ) {
        $instance['title']  = strip_tags( $new_instance['title'] );
      }
      else {
        $instance['title'] = NULL;
      }
    }

    if (in_array('width', $fields)) {
      if ( !empty( $new_instance['width'] ) ) {
        if ( in_array( $new_instance['width'], array(1, 2, 3) ) ) {
          $instance['width'] = $new_instance['width'];
        }
      }
      else {
        $instance['width'] = NULL;
      }
    }

    return $instance;
  }

  /**
   * Display the form for this widget on the Widgets page of the Admin area.
   *
   * @param array $instance
   */
  function form( $instance, $fields = array('title', 'width') ) {
    if (in_array('title', $fields)) {
      $title = empty( $instance['title'] ) ? '' : esc_attr( $instance['title'] );
      ?>
      <p><label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title:', 'dh' ); ?></label>
      <input id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
      <?php if ( isset( $fields['title help'] ) ) { echo '<br>(' . $fields['title help'] . ')'; } ?></p>
      <?php
    }

    if (in_array('width', $fields)) {
      $width = isset( $instance['width'] ) && in_array( $instance['width'], array(1, 2, 3) ) ? $instance['width'] : 1;
      ?>
      <p><label for="<?php echo esc_attr( $this->get_field_id( 'width' ) ); ?>"><?php _e( 'Width:', 'dh' ); ?></label>
      <select id="<?php echo esc_attr( $this->get_field_id( 'width' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'width' ) ); ?>">
        <?php foreach ( array(1, 2, 3) as $slug ) : ?>
        <option value="<?php echo $slug; ?>"<?php selected( $width, $slug ); ?>><?php echo $slug; ?></option>
        <?php endforeach; ?>
      </select>
      <?php
    }
  }
}