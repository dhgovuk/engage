<?php
/**
 * DH: Free Image
 *
 * Inherits from DH_Super_Widget
 *
 * @see super_widget.php
 * @author Khaled.zaidan
 *
 */

class DH_Widget_Free_Image extends DH_Super_Widget {
  /**
   * Constructor.
   */
  public function __construct( $id_base = false, $name = false, $widget_options = array(), $control_options = array() ) {
    parent::__construct( 'widget_dh_free_image', __( 'DH: Free Image', 'dh' ), array(
        'description' => __( 'Use this widget to just display an image.', 'dh' ),
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
    $sizes = array(1 => 'thumbnail', 2 => 'medium', 3 => 'large');

    // Make sure the image ID is a valid attachment.
    if ( ! empty( $instance['image_id'] ) ) {
      $image = get_post( $instance['image_id'] );
      if ( ! $image || 'attachment' != get_post_type( $image ) ) {
        $output = '<!-- Image Widget Error: Invalid Attachment ID -->';
      }
      else {
        ?>
        <div class="grid-<?php echo $width . '-' . $row_width; ?>">
          <div class="block-free-image">
          <?php echo wp_get_attachment_image( $instance['image_id'], $sizes[$width] ); ?>
          </div>
        </div>
        <?php
      }
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
  public function update( $new_instance, $old_instance, $fields = Array() ) {
    $instance = parent::update( $new_instance, $instance, array( 'width' ) );

    $instance['image_id']   = absint( $new_instance['image_id'] );

    return $instance;
  }

  /**
   * Display the form for this widget on the Widgets page of the Admin area.
   *
   * @param array $instance
   */
  public function form( $instance, $fields = Array() ) {
    parent::form( $instance, array( 'width' ) );

    _dh_widget_image_control( $this, $instance );
  }
}
