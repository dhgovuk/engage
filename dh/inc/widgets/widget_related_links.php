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

class DH_Widget_Related_Links extends DH_Super_Widget {
  /**
   * Constructor.
   */
  public function __construct() {
    parent::__construct( 'widget_dh_related_links', __( 'DH: Related Links', 'dh' ), array(
        'description' => __( 'Use this widget to display a list of related links.', 'dh' ),
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
    $title = parent::get_title_display( $instance );
    if ( empty( $title ) ) {
      $title = 'Related links';
    }

    echo $args['before_widget'];

    $classes = ($width == 1) ? 'block-spotlight-text' : 'block-related related';
    ?>
    <div class="grid-<?php echo $width . '-' . $row_width; ?>">
      <div class="<?php echo $classes; ?>">
        <h2><?php echo $title; ?></h2>
        <ul>
          <?php
          if ( is_array( $instance['links'] ) ) {
            foreach ( $instance['links'] as $link ) {
              $link_title = esc_html( $link['title'] );
              $url = esc_attr( $link['external_target'] );
              if ( $target_post = get_post( $link['internal_target'] ) ) {
                $url = get_permalink($target_post);
                if ( empty( $link_title ) ) {
                  $link_title = esc_html( $target_post->post_title );
                }
              }
              echo '<li><a href="' . $url . '">' . $link_title . '</a></li>';
            }
          }
          ?>
        </ul>
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
    $instance = parent::update( $new_instance, $instance, array( 'width', 'title' ) );

    $instance['links'] = array();

    //This is because siteorigin-panels plugin doesn't handle multi-valued $_POST/$_GET arrays (usinf [] in the name attribute in HTML form elements)
    $index = 0;
    while ( isset( $new_instance['link_title-' . $index] ) || isset( $new_instance['external_link-' . $index] ) || isset( $new_instance['internal_link-' . $index] ) ) {
      $link_title = $new_instance['link_title-' . $index];
      $external_link = $new_instance['external_link-' . $index];
      $internal_link = $new_instance['internal_link-' . $index];
      if ( empty( $link_title ) && empty( $external_link ) && ! get_post( $internal_link ) ) {
        $index++;
        continue;
      }
      $instance['links'][] = array(
        'title' => $link_title,
        'internal_target' => (int)( $internal_link ),
        'external_target' => $external_link,
      );
      $index++;
    }

    return $instance;
  }

  /**
   * Display the form for this widget on the Widgets page of the Admin area.
   *
   * @param array $instance
   */
  function form( $instance, $fields = Array() ) {
    parent::form( $instance, array( 'width', 'title', 'title help' => 'Defaults to "Related links"' ) );

    $all_posts_defaults = array(
      'depth' => 0, 'child_of' => 0, 'echo' => 1,
      'name' => 'page_id', 'id' => '',
      'show_option_none' => '', 'show_option_no_change' => '',
      'option_none_value' => ''
    );

    $all_posts = get_posts( array( 'post_type' => array('post', 'page', 'question', 'recommendation', 'event'), 'numberposts' => -1, 'orderby' => 'ID' ) );
    if (!is_array($instance['links'])) $instance['links'] = array();

    foreach ( $instance['links'] as $index => $link ) {
      if ( empty( $link['title'] ) && empty( $link['external_target'] ) && ! get_post( $link['internal_target'] ) ) {
        unset( $instance['links'][$index] );
      }
    }

    $instance['links'][] = array( 'title' => '', 'internal_target' => -1, 'external_target' => '' );
    ?>
      <hr/>
      <?php foreach ( $instance['links'] as $index => $link ): ?>
        <div class="dh-related-links--item-container">
          <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'link_title' ) ); ?>"><?php _e( 'Link Title:', 'dh' ); ?></label>
            <input type="text" id="<?php echo esc_attr( $this->get_field_id( 'link_title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'link_title-' . $index ) ) ; ?>" value="<?php echo esc_attr( $link['title'] ); ?>"/>
          </p>
          <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'internal_link' ) ); ?>"><?php _e( 'Internal Link:', 'dh' ); ?></label>
            <select id="<?php echo esc_attr( $this->get_field_id( 'internal_link' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'internal_link-' . $index ) ) ; ?>">
              <option value="-1"> --- </option>
              <?php print walk_page_dropdown_tree( $all_posts, 0,  array_merge( $all_posts_defaults, array( 'selected' => $link['internal_target'] ) ) ); ?>
            </select>
          </p>
          <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'external_link' ) ); ?>"><?php _e( 'External Link:', 'dh' ); ?></label>
            <input type="text" id="<?php echo esc_attr( $this->get_field_id( 'external_link' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'external_link-' . $index ) ) ; ?>" value="<?php echo esc_attr( $link['external_target'] ); ?>"/>
          </p>
          <hr/>
        </div>
      <?php endforeach; ?>
    <?php
  }
}