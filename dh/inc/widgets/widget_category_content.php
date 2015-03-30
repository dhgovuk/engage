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

class DH_Widget_Category_Content extends DH_Super_Widget {
  /**
   * Constructor.
   */
  public function __construct() {
    parent::__construct( 'widget_dh_category_content', __( 'DH: Category Content', 'dh' ), array(
        'description' => __( 'Use this widget to display the content of a category (mainly a list of its posts).', 'dh' ),
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
    $row_width = parent::get_row_width( $instance );

    // Load a potential CL for thie category
    $custom_layouts = get_posts(array(
      'post_type' => 'custom_layout',
      'tax_query' => array(array(
        'field' => 'id',
        'terms' => array(get_queried_object()->term_id),
        'taxonomy' => get_queried_object()->taxonomy)),
    ));

    $nested_output = '[CATEGORY-CONTENT]';
    if ( count( $custom_layouts ) > 0 ) {
      $the_custom_layout = $custom_layouts[0];

      // Note that we pass the row width for proper rendering of the panel
      $nested_output = _dh_siteorigin_panels_render( $the_custom_layout->ID, TRUE, FALSE, $row_width );
    }

    echo $args['before_widget'];
    ?>
    <div class="grid-<?php echo $row_width . '-' . $row_width; ?>">
      <div class="block-text"><?php print $nested_output; ?></div>
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
    return $instance;
  }

  /**
   * Display the form for this widget on the Widgets page of the Admin area.
   *
   * @param array $instance
   */
  function form( $instance, $fields = Array() ) {}
}