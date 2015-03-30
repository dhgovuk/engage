<?php
/**
 * DH: Recommendations
 *
 * Inherits from DH_Super_Widget
 *
 * @see super_widget.php
 * @author Khaled.zaidan
 *
 */

// Called "Recommendations" only because it used to be only for recommendations. But now it can also do questions as well.
class DH_Widget_Recommendations extends DH_Super_Widget {
  /**
   * Constructor.
   */
  public function __construct() {
    parent::__construct( 'widget_dh_recommendations', __( 'DH: Content Index', 'dh' ), array(
        'description' => __( 'Use this widget to display curated recommendations or questions.', 'dh' ),
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
    global $wp;
    $content_type  = empty( $instance['content_type'] ) ? 'recommendation' : $instance['content_type'];

    if ( in_array( $wp->matched_rule, array($content_type . '/page/([0-9]{1,})/?$', $content_type . '/?$') ) ) {
      // If we're on the [post-type]'s archive page, then we don't wanna render this widget
      if ( ! isset( $instance['force_render'] ) || ! $instance['force_render'])
      return;
    }
    $title = parent::get_title_display( $instance );
    $width = parent::get_width( $instance );
    $text  = empty( $instance['text'] ) ? '' : $instance['text'];
    $row_width = parent::get_row_width( $instance );

    $labels = get_post_type_labels( get_post_type_object( $content_type ) );

    echo $args['before_widget'];
    ?>
    <div class="grid-2-<?php echo $row_width; ?>">
      <div class="block-text">
        <h2>Content Index</h2>
        <p><?php echo strip_tags( nl2br( $text ), '<div><a><br><p><em><i><b><strong><ul><ol><li>' ); ?></p>
        <ul class="widget-<?php echo $content_type; ?>s-list">
        <?php if ( is_array( $instance['curated_recs'] ) ): ?>
          <?php foreach ( $instance['curated_recs'] as $index => $curated_rec ): ?>
            <li>
              <?php if ( $content_type == 'post' ): ?>
                <div class="small-text"></div>
                <a href="<?php echo post_permalink( $curated_rec ); ?>"><?php echo esc_html( get_post( $curated_rec )->post_title ); ?></a>
                <div class="text-thin download-size"><?php echo 'Date: ' . $curated_rec->post_date; ?></div>
              <?php else: ?>
                <div class="small-text"><?php echo $labels->singular_name . ' ' . _dh_get_post_dh_num( $curated_rec, $content_type ); ?></div>
                <a href="<?php echo post_permalink( $curated_rec ); ?>"><?php echo esc_html( get_post( $curated_rec )->post_title ); ?></a>
              <?php endif; ?>
            </li>
          <?php endforeach; ?>
        <?php endif; ?>
        </ul>
        <a href="<?php echo get_post_type_archive_link( $content_type ); ?>" class="button-primary button-get-started">All <?php echo strtolower( $labels->name ); ?></a>
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

    $instance['text'] = $new_instance['text'];

    $instance['content_type'] = 'recommendation';
    if ( in_array( $new_instance['content_type'], array( 'recommendation', 'question', 'post' ) ) ) {
      $instance['content_type'] = $new_instance['content_type'];
    }

    $instance['curated_recs'] = array();

    //This is because siteorigin-panels plugin doesn't handle multi-valued $_POST/$_GET arrays (usinf [] in the name attribute in HTML form elements)
    $index = 0;
    while ( isset( $new_instance['curated_' . $instance['content_type'] . 's-' . $index] ) ) {
    //foreach ( $new_instance['curated_recs'] as $index => $curated_rec ) {
      $curated_rec = $new_instance['curated_' . $instance['content_type'] . 's-' . $index];
      if ( empty( $curated_rec ) || ! get_post( $curated_rec ) ) {
        $index++;
        continue;
      }
      $instance['curated_recs'][] = $curated_rec;

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
    parent::form( $instance, array() );

    $text  = empty( $instance['text'] ) ? '' : $instance['text'];
    $content_type  = empty( $instance['content_type'] ) ? 'recommendation' : $instance['content_type'];
    ?>
      <p><label for="<?php echo esc_attr( $this->get_field_id( 'content_type' ) ); ?>"><?php _e( 'Content Type:', 'dh' ); ?></label>
      <select id="<?php echo esc_attr( $this->get_field_id( 'content_type' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'content_type' ) ); ?>">
        <option value="recommendation" <?php echo ( $content_type == 'recommendation' ) ? 'selected' : ''; ?>>Recommendations</option>
        <option value="question" <?php echo ( $content_type == 'question' ) ? 'selected' : ''; ?>>Questions</option>
        <option value="post" <?php echo ( $content_type == 'post' ) ? 'selected' : ''; ?>>Posts</option>
        <?php echo esc_attr( $text ); ?>
      </select></p>

      <p><label for="<?php echo esc_attr( $this->get_field_id( 'text' ) ); ?>"><?php _e( 'Text:', 'dh' ); ?></label>
      <textarea id="<?php echo esc_attr( $this->get_field_id( 'text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'text' ) ); ?>" rows="8"><?php echo esc_attr( $text ); ?></textarea></p>
      <?php

    $all_recs_defaults = array(
      'depth' => 0, 'child_of' => 0, 'echo' => 1,
      'name' => 'page_id', 'id' => '',
      'show_option_none' => '', 'show_option_no_change' => '',
      'option_none_value' => ''
    );

    $all_recs = get_posts( array( 'post_type' => $content_type, 'numberposts' => -1, 'orderby' => 'ID' ) );
    if ( ! is_array( $instance['curated_recs'] ) ) {
      $instance['curated_recs'] = array();
    }

    foreach ( $instance['curated_recs'] as $index => $curated_rec ) {
      if ( empty( $curated_rec ) || ! get_post( $curated_rec ) ) {
        unset( $instance['curated_recs'][$index] );
      }
    }

    $instance['curated_recs'][] = 0;

    foreach ( $instance['curated_recs'] as $index => $curated_rec ) {
      ?>
        <hr/>
        <div class="dh-curated-recs--item-container">
          <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'curated_' . $content_type . 's' ) ); ?>"><?php _e( 'Curated ' . $content_type . ':', 'dh' ); ?></label>
            <select id="<?php echo esc_attr( $this->get_field_id( 'curated_' . $content_type . 's' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'curated_' . $content_type . 's-' . $index ) ); ?>">
              <option value="-1"> --- </option>
              <?php print walk_page_dropdown_tree( $all_recs, 0,  array_merge( $all_recs_defaults, array( 'selected' => $curated_rec ) ) ); ?>
            </select>
          </p>
        </div>
      <?php
    }
  }
}