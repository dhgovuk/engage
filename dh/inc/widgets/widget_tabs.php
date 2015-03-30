<?php
/**
 * DH: Text Tabs
 *
 * Inherits from DH_Super_Widget
 *
 * @see super_widget.php
 * @author Khaled.zaidan
 *
 */

class DH_Widget_Tabs extends DH_Super_Widget {
  /**
   * Constructor.
   */
  public function __construct() {
    parent::__construct( 'widget_dh_tabs', __( 'DH: Tabs', 'dh' ), array(
        'description' => __( 'Use this widget to display a tabs.', 'dh' ),
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

    echo $args['before_widget'];

    ?>
    <div class="grid-<?php echo $width . '-' . $row_width; ?>">
      <div class="block-tabs">
        <?php if ( $title ): ?>
          <h2><?php echo $title; ?></h2>
        <?php endif; ?>
        <div id="tabs-<?php echo $this->number; ?>" class="tabify ui-tabs">
          <ul class="ui-tabs-nav">
            <?php
            if ( is_array( $instance['tabs'] ) ) {
              foreach ( $instance['tabs'] as $index => $tab ) {
                $tab_title = esc_html( $tab['tab_title'] );
                $active_tab_class = '';
                if ( $index == 0 ) {
                  $active_tab_class = 'ui-tabs-active ui-state-active';
                }
                echo '<li><a href="#tabs-' . $this->number . '-' . $index . '" class="ui-tabs-anchor">' . $tab_title . '</a></li>';
              }
            }
            ?>
          </ul>
          <?php
          if ( is_array( $instance['tabs'] ) ) {
            foreach ( $instance['tabs'] as $index => $tab ) {
              $tab_content = strip_tags( nl2br( $tab['tab_content'] ), '<div><a><br><p><em><i><b><strong><iframe><ul><ol><li>' );
              echo '<div id="tabs-' . $this->number . '-' . $index . '" class="ui-tabs-panel">';
              echo   $tab_content;
              echo '</div>';
            }
          }
          ?>
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

    $instance['tabs'] = array();

    //This is because siteorigin-panels plugin doesn't handle multi-valued $_POST/$_GET arrays (usinf [] in the name attribute in HTML form elements)
    $index = 0;
    while ( isset( $new_instance['tab_title-' . $index] ) || isset($new_instance['tab_content-' . $index]) ) {
      $tab_title = $new_instance['tab_title-' . $index];
      $tab_content = $new_instance['tab_content-' . $index];
      if ( empty( $tab_title ) && empty( $tab_content ) ) {
        $index++;
        continue;
      }
      $instance['tabs'][] = array(
        'tab_title'   => $tab_title,
        'tab_content' => $tab_content,
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
    parent::form( $instance, array( 'title', 'width' ) );

    if (!is_array($instance['tabs'])) $instance['tabs'] = array();

    foreach ( $instance['tabs'] as $index => $tabs ) {
      if ( empty( $tabs['tab_title'] ) && empty( $tabs['tab_content'] ) ) {
        unset( $instance['tabs'][$index] );
      }
    }
    $instance['tabs'][] = array( 'tab_title' => '', 'tab_content' => '' );
    ?>
      <hr/>
      <?php foreach ( $instance['tabs'] as $index => $tab ): ?>
        <div class="dh-tabs--item-container">
          <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'tab_title' ) ); ?>"><?php _e( 'Tab Title:', 'dh' ); ?></label>
            <input type="text" id="<?php echo esc_attr( $this->get_field_id( 'tab_title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'tab_title-' . $index ) ); ?>" value="<?php echo esc_attr( $tab['tab_title'] ); ?>"/>
          </p>
          <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'tab_content' ) ); ?>"><?php _e( 'Tab Content:', 'dh' ); ?></label>
            <textarea type="text" id="<?php echo esc_attr( $this->get_field_id( 'tab_content' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'tab_content-' . $index ) ); ?>" rows="6"><?php echo esc_textarea( $tab['tab_content'] ); ?></textarea>
          </p>
          <hr/>
        </div>
      <?php endforeach; ?>
    <?php
  }

  public function is_preview() {
    global $wp_customize;

    if ( method_exists( get_parent_class( $this ), 'is_preview' ) ) {
      return parent::is_preview();
    }

    return ( isset( $wp_customize ) && $wp_customize->is_preview() ) ;
  }
}