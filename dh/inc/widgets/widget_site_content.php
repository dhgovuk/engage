<?php
/**
 * DH: Site Content Widget
 *
 * Inherits from DH_Super_Widget
 *
 * @see super_widget.php
 * @author Khaled.zaidan
 *
 */

class DH_Widget_Site_Content extends DH_Super_Widget {
  /**
   * Constructor.
   */
  public function __construct() {
    parent::__construct( 'widget_dh_site_content', __( 'DH: Site Content', 'dh' ), array(
        'description' => __( 'Use this widget to display a listing of all categories on the site.', 'dh' ),
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
    $visual_width = $width;
    if ( isset( $instance['dh_visual_width'] ) ) {
      $visual_width = $instance['dh_visual_width'];
    }
    $row_width = parent::get_row_width($instance);

    $title = parent::get_title_display( $instance );

    $archive_pages = array(
      'chapter' => 'question',
      'topic' => 'recommendation',
      'organisation' => 'recommendation'
    );

    $raw_taxonomy_terms = get_terms( $instance['taxonomy'], array( 'get' => 'all' ) );

    $taxonomy_terms = _dh_prep_hier_taxonomy( $raw_taxonomy_terms );
    $taxonomy_name = 'chapter';

    $archive_url = $archive_pages[$taxonomy_name];

    $total_terms_titles_count = count( $raw_taxonomy_terms );

    echo $args['before_widget'];
    ?>
    <div class="grid-<?php echo $width . '-' . $row_width; ?>">
      <div class="block-site-content">
        <div class="grid-<?php print $row_width . '-' . $row_width; ?>"><h2><?php echo $title; ?></h2></div>
        <div class="grid-1-<?php echo $visual_width; ?>">
          <?php $terms_count_in_col = 0; ?>
          <?php foreach ($taxonomy_terms as $parent_term): ?>
          <div class="grid-1-1">
            <div class="area-inter">
              <h3><?php echo $parent_term->name; ?></h3>
              <ul>
                <?php foreach ( $parent_term->children as $child_term ): ?>
                  <!-- <li><a href="<?php echo get_site_url() . '/' . $archive_url . '/?' . $taxonomy_name . 's%5B%5D=' . $child_term->term_id; ?>"><?php echo $child_term->name; ?></a></li> -->
                  <li><a href="<?php echo get_category_link($child_term); ?>"><?php echo esc_html($child_term->name); ?></a></li>
                <?php endforeach; ?>

              </ul>
            </div>
          </div>
          <?php
          // Decide when to break to a new column
          $terms_count_in_col += 1 + count( $parent_term->children );
          if ( $terms_count_in_col >= ( $total_terms_titles_count / $visual_width ) ) {
            $terms_count_in_col = 0;
            echo '</div><div class="grid-1-' . $visual_width . '">';
          }
          ?>
          <?php endforeach; ?>
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
    $instance = parent::update( $new_instance, $instance, array( 'title', 'width' ) );

    if ( in_array( $new_instance['taxonomy'], array('', 'topic', 'organisation', 'chapter' ) ) ) {
      $instance['taxonomy'] = $new_instance['taxonomy'];
    }

    return $instance;
  }

  /**
   * Display the form for this widget on the Widgets page of the Admin area.
   *
   * @param array $instance
   */
  function form( $instance, $fields = Array() ) {
    parent::form( $instance, array( 'title', 'width' ) );

    $taxonomy  = empty( $instance['taxonomy'] ) ? '' : $instance['taxonomy'];
    ?>
      <p><label for="<?php echo esc_attr( $this->get_field_id( 'taxonomy' ) ); ?>"><?php _e( 'Category:', 'dh' ); ?></label>
      <select id="<?php echo esc_attr( $this->get_field_id( 'taxonomy' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'taxonomy' ) ); ?>">
        <option value="topic" <?php echo ( $taxonomy == 'topic' ) ? 'selected' : ''; ?>>Themes</option>
        <option value="organisation" <?php echo ( $taxonomy == 'organisation' ) ? 'selected' : ''; ?>>Organisations</option>
        <option value="chapter" <?php echo ( $taxonomy == 'chapter' ) ? 'selected' : ''; ?>>Chapters</option>
      </select></p>
    <?php
  }
}