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

class DH_Widget_Spotlight extends DH_Super_Widget {
  /**
   * Constructor.
   */
  public function __construct() {
    parent::__construct( 'widget_dh_spotlight', __( 'DH: Spotlight', 'dh' ), array(
        'description' => __( 'Use this widget to display Spotlights (This is for regular posts and pages. For news, look at DH: News Spotlight).', 'dh' ),
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

    if ( $instance['spotlight_post'] && $spotlight_post = get_post( $instance['spotlight_post'] ) ) {
      // Cool, we got our spotlight spot

      if ( ! $title ) {
        $title = esc_html( $spotlight_post->post_title );
      }
      $thumbnail_id = get_post_thumbnail_id($spotlight_post->ID);
      $sizes = array(1 => 'thumbnail', 2 => 'medium', 3 => 'large');

      $text  = strip_tags( $spotlight_post->post_content );
      if ( 150 < strlen( $text ) ) {
        $text = substr( $text, 0, 150 ) . '&hellip;' ;
      }

      echo $args['before_widget'];
      ?>
      <div class="grid-<?php echo $width . '-' . $row_width; ?>">
        <div class="block-spotlight block-spotlight-<?php echo $instance['spotlight_post']; ?>">
          <?php echo wp_get_attachment_image($thumbnail_id, $sizes[$width] ); ?>
          <h3><?php echo '<a href="'. get_permalink($spotlight_post) . '">' . $title . '</a>'; ?></h3>
          <p><?php echo $text; ?></p>
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
    $instance = parent::update( $new_instance, $instance );

    $instance['spotlight_post'] = $new_instance['spotlight_post'];

    return $instance;
  }

  /**
   * Display the form for this widget on the Widgets page of the Admin area.
   *
   * @param array $instance
   */
  function form( $instance, $fields = Array() ) {
    parent::form( $instance );

    $all_posts_defaults = array(
      'depth' => 0, 'child_of' => 0,
      'selected' => $instance['spotlight_post'], 'echo' => 1,
      'name' => 'page_id', 'id' => '',
      'show_option_none' => '', 'show_option_no_change' => '',
      'option_none_value' => ''
    );

    $all_posts = get_posts( array( 'post_type' => array( 'post', 'page' ), 'numberposts' => -1, 'orderby' => 'ID' ) );
    ?>
      <p>
        <label for="<?php echo esc_attr( $this->get_field_id( 'spotlight_post' ) ); ?>"><?php _e( 'Spotlight Post:', 'dh' ); ?></label>
        <select id="<?php echo esc_attr( $this->get_field_id( 'spotlight_post' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'spotlight_post' ) ); ?>">
          <option value="-1"> --- </option>
          <?php print walk_page_dropdown_tree( $all_posts, 0, $all_posts_defaults ); ?>
        </select>
      </p>

    <?php
  }
}