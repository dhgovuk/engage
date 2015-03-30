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

class DH_Widget_Quote extends DH_Super_Widget {
  /**
   * Constructor.
   */
  public function __construct() {
    parent::__construct( 'widget_dh_quote', __( 'DH: Quote', 'dh' ), array(
        'description' => __( 'Use this widget to display a quote.', 'dh' ),
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

    $read_more_link_title = apply_filters( 'widget_title', empty( $instance['read_more_link_title'] ) ? '' : $instance['read_more_link_title'], $instance, $this->id_base );
    $internal_link = $instance['internal_link'];
    $external_link = $instance['external_link'];

    echo $args['before_widget'];
    ?>
    <div class="grid-<?php echo $width . '-' . $row_width; ?>">
      <div class="block-spotlight block-spotlight-text">
        <h3><?php echo $title; ?></h3>
        <blockquote><p><?php echo strip_tags( nl2br( $text ), '<div><a><br><p><em><i><b><strong><ul><ol><li>' ); ?></p></blockquote>
        <div class="quote-author"><?php echo $source; ?></div>
        <?php
        $is_internal_link = (bool)( $internal_link && $post = get_post( $internal_link ) );
        $is_external_link = (bool)( $external_link );
        if ( $read_more_link_title && ( $is_internal_link || $is_external_link ) ) {
          echo '<div class="quote-author">';
          if ( $is_internal_link ) {
            echo '<a href="' . esc_attr( get_permalink( $internal_link ) ) . '">' . $read_more_link_title . '</a>';
          }
          else {
            echo '<a href="' . esc_attr( $external_link ) . '">' . $read_more_link_title . '</a>';
          }
          echo '</div>';
        }
        ?>
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
    $instance['source']  = strip_tags( $new_instance['source'] );

    $instance['read_more_link_title'] = $new_instance['read_more_link_title'];
    $instance['internal_link'] = $new_instance['internal_link'];
    $instance['external_link'] = $new_instance['external_link'];

    return $instance;
  }

  /**
   * Display the form for this widget on the Widgets page of the Admin area.
   *
   * @param array $instance
   */
  function form( $instance, $fields = Array() ) {
    parent::form( $instance );

    $text   = empty( $instance['text'] ) ? '' : $instance['text'];
    $source = empty( $instance['source'] ) ? '' : $instance['source'];
    ?>
      <p><label for="<?php echo esc_attr( $this->get_field_id( 'text' ) ); ?>"><?php _e( 'Text:', 'dh' ); ?></label>
      <textarea id="<?php echo esc_attr( $this->get_field_id( 'text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'text' ) ); ?>" rows="8"><?php echo esc_attr( $text ); ?></textarea></p>

      <p><label for="<?php echo esc_attr( $this->get_field_id( 'source' ) ); ?>"><?php _e( 'Source:', 'dh' ); ?></label>
      <input id="<?php echo esc_attr( $this->get_field_id( 'source' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'source' ) ); ?>" type="text" value="<?php echo esc_attr( $source ); ?>"></p>
      <?php

      $all_posts_defaults = array(
          'depth' => 0, 'child_of' => 0, 'echo' => 1,
          'name' => 'page_id', 'id' => '',
          'show_option_none' => '', 'show_option_no_change' => '',
          'option_none_value' => ''
      );

      $all_posts = get_posts( array( 'post_type' => array('post', 'page', 'question', 'recommendation', 'event'), 'numberposts' => -1, 'orderby' => 'ID' ) );

      $read_more_link_title = $instance['read_more_link_title'];
      $internal_link = $instance['internal_link'];
      $external_link = $instance['external_link'];
      ?>
      <p>
        <label for="<?php echo esc_attr( $this->get_field_id( 'read_more_link_title' ) ); ?>"><?php _e( 'Read More Link Title:', 'dh' ); ?></label>
        <input type="text" id="<?php echo esc_attr( $this->get_field_id( 'read_more_link_title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'read_more_link_title' ) ) ; ?>" value="<?php echo esc_attr( $read_more_link_title ); ?>"/>
      </p>
      <p>
        <label for="<?php echo esc_attr( $this->get_field_id( 'internal_link' ) ); ?>"><?php _e( 'Internal Link:', 'dh' ); ?></label>
        <select id="<?php echo esc_attr( $this->get_field_id( 'internal_link' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'internal_link' ) ) ; ?>">
          <option value="-1"> --- </option>
          <?php print walk_page_dropdown_tree( $all_posts, 0,  array_merge( $all_posts_defaults, array( 'selected' => $internal_link ) ) ); ?>
        </select>
      </p>
      <p>
        <label for="<?php echo esc_attr( $this->get_field_id( 'external_link' ) ); ?>"><?php _e( 'External Link:', 'dh' ); ?></label>
        <input type="text" id="<?php echo esc_attr( $this->get_field_id( 'external_link' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'external_link' ) ) ; ?>" value="<?php echo esc_attr( $external_link ); ?>"/>
      </p>
    <?php
  }
}
