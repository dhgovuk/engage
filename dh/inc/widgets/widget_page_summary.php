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

class DH_Widget_Page_Summary extends DH_Super_Widget {
  /**
   * Constructor.
   */
  public function __construct() {
    parent::__construct( 'widget_dh_page_summary', __( 'DH: Page Summary', 'dh' ), array(
        'description' => __( 'Use this widget to display the summary of a page or post.', 'dh' ),
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
    $row_width = parent::get_row_width( $instance );

    if ( !empty($instance['page']) ) {
      $post = get_page($instance['page']);

      $text = $post->post_content;

      if ( $cut_point = strpos( $text, '<!--more-->' ) ) {
        $text = substr( $text, 0, $cut_point) . '...';
      }
      if ( is_numeric( $instance['max_length'] ) && $instance['max_length'] > 0 && $instance['max_length'] < strlen( strip_tags( $text ) ) ) {
        $text = force_balance_tags( _dh_html_truncate( $text, $instance['max_length'] ) . '...' );
      }

      echo $args['before_widget'];
      ?>
      <div class="grid-<?php print $row_width . '-' . $row_width; ?>"></div>
      <div class="<?php if ( $row_width == 3 ) { echo 'grid-1-2 grid-centered'; } ?>">
        <div class="block-page-summary keyline">
          <?php if ( ! $instance['hide_title'] ): ?>
            <h2><?php echo esc_html($post->post_title); ?></h2>
          <?php endif; ?>
          <div>
            <p><?php echo $text; ?></p>
          </div>
          <?php if ($instance['more_link']): ?>
            <a href="<?php echo get_permalink($post->ID); ?>"><?php echo esc_html($instance['more_link_text']); ?></a>
          <?php endif; ?>
        </div>
      </div>
      <div class="grid-<?php print $row_width . '-' . $row_width; ?>"></div>
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

    $instance['hide_title']     = !empty($new_instance['hide_title']) ? 1 : 0;
    $instance['page']           = is_numeric($new_instance['page']) ? $new_instance['page'] : -1;
    $instance['max_length']     = is_numeric($new_instance['max_length']) ? $new_instance['max_length'] : '';
    $instance['more_link']      = !empty($new_instance['more_link']) ? 1 : 0;
    $instance['more_link_text'] = strip_tags( $new_instance['more_link_text'] );

    return $instance;
  }

  /**
   * Display the form for this widget on the Widgets page of the Admin area.
   *
   * @param array $instance
   */
  function form( $instance, $fields = Array() ) {
    parent::form( $instance, array() );

    $hide_title = isset( $instance['hide_title'] ) ? (bool) $instance['hide_title'] : FALSE;
    ?>
      <p>
        <input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'hide_title' ); ?>" name="<?php echo $this->get_field_name( 'hide_title' ); ?>"<?php checked( $hide_title ); ?> />
        <label for="<?php echo $this->get_field_id( 'hide_title' ); ?>"><?php _e( 'Hide the title' ); ?></label><br/>
      </p>
    <?php

    $pages_defaults = array(
      'depth' => 0, 'child_of' => 0,
      'selected' => !empty($instance['page']) ? $instance['page'] : NULL, 'echo' => 1,
      'name' => 'page_id', 'id' => '',
      'show_option_none' => '', 'show_option_no_change' => '',
      'option_none_value' => ''
    );

    $post_types = get_post_types();
    foreach ($post_types as $index => $post_type) {
      if (!in_array($post_type, array('page', 'post')) && substr($post_type, 0, 3) != 'dh_') {
        unset($post_types[$index]);
      }
    }
    $pages = get_posts( array( 'numberposts' => -1, 'post_type' => array('post', 'page', 'event', 'question', 'custom_layout') ) );

    $max_length = isset( $instance['max_length'] ) ? (int)($instance['max_length']) : '';

    $more_link = isset( $instance['more_link'] ) ? (bool) $instance['more_link'] : FALSE;
    $more_link_text = empty( $instance['more_link_text'] ) ? '' : $instance['more_link_text'];
    ?>
      <p>
        <label for="<?php echo esc_attr( $this->get_field_id( 'page' ) ); ?>"><?php _e( 'Page:', 'dh' ); ?></label>
        <select id="<?php echo esc_attr( $this->get_field_id( 'page' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'page' ) ); ?>">
          <?php print walk_page_dropdown_tree( $pages, 0, $pages_defaults ); ?>
        </select>
      </p>
      <p>
        <label for="<?php echo esc_attr( $this->get_field_id( 'max_length' ) ); ?>"><?php _e( 'Character limit:', 'dh' ); ?></label>
        <input id="<?php echo esc_attr( $this->get_field_id( 'max_length' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'max_length' ) ); ?>" type="text" value="<?php echo $max_length; ?>">
      </p>
      <p>
        <input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'more_link' ); ?>" name="<?php echo $this->get_field_name( 'more_link' ); ?>"<?php checked( $more_link ); ?> />
        <label for="<?php echo $this->get_field_id( 'more_link' ); ?>"><?php _e( 'Display a "more" link' ); ?></label><br/>
        <label for="<?php echo esc_attr( $this->get_field_id( 'more_link_text' ) ); ?>"><?php _e( 'More link text:', 'dh' ); ?></label>
        <input id="<?php echo esc_attr( $this->get_field_id( 'more_link_text' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'more_link_text' ) ); ?>" type="text" value="<?php echo esc_attr( $more_link_text ); ?>">
      </p>
    <?php
  }
}