<?php
/**
 * DH: Newsletter
 *
 * Inherits from DH_Super_Widget
 *
 * @see super_widget.php
 * @author Khaled.zaidan
 *
 */

class DH_Widget_Parliament_RSS extends DH_Super_Widget {
  /**
   * Constructor.
   */
  public function __construct() {
    parent::__construct( 'widget_dh_Parliament_RSS', __( 'DH: Parliament_RSS', 'dh' ), array(
        'description' => __( 'Use this widget to display an RSS feed from parliament.uk.', 'dh' ),
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

    // @TODO Get+parse RSS feed
    $url = $instance['url'];
    if (!empty($url)) {
      $feed = file_get_contents( $url );
    }
    else {
      $feed = '';
    }
    $parsed_feed = simplexml_load_string( $feed );

    $updates = array();

    $limit = 5;

    foreach ($parsed_feed->channel->item as $item) {
      $new_update = array();

      $new_update['title'] = $item->title;
      $new_update['date'] = $item->pubDate;
      $new_update['category'] = $item->category;
      $new_update['stage'] = $item->stage;
      $new_update['link'] = $item->link;

      $updates[] = $new_update;

      if ( count( $updates ) > $limit ) {
        break;
      }
    }

    echo $args['before_widget'];
    ?>
    <div class="grid-<?php echo $width . '-' . $row_width; ?>">
      <div class="block-spotlight-text">
        <h3><?php echo $title; ?></h3>
        <ul>
        <?php foreach ( $updates as $update): ?>
          <li>
            <a href="<?php echo esc_attr($update['link']); ?>">
              <?php echo esc_html($update['date'] . ' - ' . $update['title']) . '<br/>' . esc_html($update['stage']); ?>
            </a>
          </li>
        <?php endforeach; ?>
        </u>
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

    $instance['url'] = $new_instance['url'];

    return $instance;
  }

  /**
   * Display the form for this widget on the Widgets page of the Admin area.
   *
   * @param array $instance
   */
  function form( $instance, $fields = Array() ) {
    parent::form( $instance, array( 'title', 'width' ) );

    $url   = empty( $instance['url'] ) ? '' : $instance['url'];
    ?>
      <p><label for="<?php echo esc_attr( $this->get_field_id( 'url' ) ); ?>"><?php _e( 'URL:', 'dh' ); ?></label>
      <input id="<?php echo esc_attr( $this->get_field_id( 'url' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'url' ) ); ?>" value="<?php echo esc_attr( $url ); ?>" />
    <?php
  }
}
