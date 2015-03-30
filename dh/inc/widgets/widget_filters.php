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

class DH_Widget_Filters extends DH_Super_Widget {
  /**
   * Constructor.
   */
  public function __construct() {
    parent::__construct( 'widget_dh_filters', __( 'DH: Filters (View by)', 'dh' ), array(
        'description' => __( 'Use this widget to display the options to filter recommendations or questions.', 'dh' ),
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

    global $wp;

    $content_type = 'recommendation';
    if ( $instance['content_type'] ) {
      $content_type = $instance['content_type'];
    }

    if ( in_array( $wp->matched_rule, array( $content_type . '/page/([0-9]{1,})/?$', $content_type . '/?$') ) ) {
      // If we're on the recommendations page, then we don't wanna render this widget
      if ( ! isset( $instance['force_render'] ) || ! $instance['force_render'])
      return;
    }
    $organisations = get_terms('organisation', array('get' => 'all', 'orderby' => 'name' ));
    $themes = get_terms('topic', array('get' => 'all', 'orderby' => 'name' ));
    $chapters = _dh_prep_hier_taxonomy( get_terms( 'chapter', array( 'get' => 'all' ) ) );
    $tags = get_terms( 'post_tag', array( 'get' => 'all' ) );

    $chosen_themes = array();
    if ( isset( $_GET['themes'] ) ) {
      $chosen_themes = $_GET['themes'];

      foreach ( $chosen_themes as $index => $chosen_theme ) {
        if ( ! get_term( $chosen_theme, 'topic' ) ) {
          // Remove items that aren't really terms in the taxonomy
          unset( $chosen_themes[$index] );
        }
      }
    }
    $chosen_organisations = array();
    if ( isset( $_GET['organisations'] ) ) {
      $chosen_organisations = $_GET['organisations'];

      foreach ( $chosen_organisations as $index => $chosen_organisation ) {
        if ( ! get_term( $chosen_organisation, 'organisation' ) ) {
          // Remove items that aren't really terms in the taxonomy
          unset( $chosen_organisations[$index] );
        }
      }
    }
    $chosen_chapters = array();
    if ( isset( $_GET['chapters'] ) ) {
      $chosen_chapters = $_GET['chapters'];

      foreach ( $chosen_chapters as $index => $chosen_chapter ) {
        if ( ! get_term( $chosen_organisation, 'chapter' ) ) {
          // Remove items that aren't really terms in the taxonomy
          unset( $chosen_chapters[$index] );
        }
      }
    }
    $chosen_tags = array();
    if ( isset( $_GET['tags'] ) ) {
      $chosen_tags = $_GET['tags'];

      foreach ( $chosen_tags as $index => $chosen_tag ) {
        if ( ! get_term( $chosen_tag, 'post_tag' ) ) {
          // Remove items that aren't really terms in the taxonomy
          unset( $chosen_tags[$index] );
        }
      }
    }

    $hide_vocabs = empty( $instance['hide_vocabs'] ) ? array() : $instance['hide_vocabs'];

    echo $args['before_widget'];
    ?>
    <div class="grid-1-<?php echo $row_width; ?>">
      <div class="block-filters">
        <h2>View by</h2>
        <form id="<?php echo $content_type; ?>-filters-form" class="dh-filters-form" method="get" action="<?php echo get_site_url() . '/' . $content_type; ?>">
          <?php if ( $content_type == 'recommendation' ): ?>
            <?php if ( ! in_array( 'topic', $hide_vocabs ) ): ?>
              <details <?php echo (count($chosen_themes) > 0) ? 'open="open"':''; ?>>
                <summary><span class="summary">Theme</span></summary>
                <div class="form-group">

                  <?php foreach ($themes as $theme): ?>
                    <label for="checkbox-theme-<?php echo $theme->term_id; ?>" class="label-block <?php if (in_array($theme->term_id, $chosen_themes)) { echo 'selected'; }?>">
                      <input alt-url="<?php print get_term_link($theme); ?>" id="checkbox-theme-<?php echo $theme->term_id; ?>" type="checkbox" name="themes[]" value="<?php echo $theme->term_id; ?>" <?php if (in_array($theme->term_id, $chosen_themes)) { echo 'checked'; }?>/>
                      <span><?php echo $theme->name; ?></span>
                    </label>
                  <?php endforeach; ?>
                </div>
              </details>
            <?php endif; ?>

            <?php if ( ! in_array( 'organisation', $hide_vocabs ) ): ?>
              <details <?php echo (count($chosen_organisations) > 0) ? 'open="open"':''; ?>>
                <summary><span class="summary">Organisation</span></summary>
                <div class="form-group">
                  <?php foreach ($organisations as $organisation): ?>
                    <label for="checkbox-organisation-<?php echo $organisation->term_id; ?>" class="label-block <?php if (in_array($organisation->term_id, $chosen_organisations)) { echo 'selected'; }?>">
                      <input alt-url="<?php print get_term_link($organisation); ?>" id="checkbox-organisation-<?php echo $organisation->term_id; ?>" type="checkbox" name="organisations[]" value="<?php echo $organisation->term_id; ?>" <?php if (in_array($organisation->term_id, $chosen_organisations)) { echo 'checked'; }?>/>
                      <span><?php echo $organisation->name; ?></span>
                    </label>
                  <?php endforeach; ?>
                </div>
              </details>
            <?php endif; ?>
          <?php elseif ( $content_type == 'question' ): ?>
          <?php foreach ($chapters as $parent_chapter): ?>
            <details <?php echo ( count(array_intersect($chosen_chapters, array_keys($parent_chapter->children))) > 0 ) ? 'open="open"':''; ?>>
              <summary><span class="summary"><?php echo $parent_chapter->name; ?></span></summary>
              <div class="form-group">

                <?php foreach ($parent_chapter->children as $chapter): ?>
                  <label for="checkbox-chapter-<?php echo $chapter->term_id; ?>" class="label-block <?php if (in_array($chapter->term_id, $chosen_chapters)) { echo 'selected'; }?>">
                    <input alt-url="<?php print get_term_link($chapter); ?>" id="checkbox-chapter-<?php echo $chapter->term_id; ?>" type="checkbox" name="chapters[]" value="<?php echo $chapter->term_id; ?>" <?php if (in_array($chapter->term_id, $chosen_chapters)) { echo 'checked'; }?>/>
                    <span><?php echo $chapter->name; ?></span>
                  </label>
                <?php endforeach; ?>
              </div>
            </details>
          <?php endforeach; ?>
          <?php elseif ( $content_type == 'post' ): ?>
          <details <?php echo (count($chosen_tags) > 0) ? 'open="open"':''; ?>>
            <summary><span class="summary">Tags</span></summary>
            <div class="form-group">
              <?php foreach ($tags as $tag): ?>
                <label for="checkbox-tag-<?php echo $tag->term_id; ?>" class="label-block <?php if (in_array($tag->term_id, $chosen_tags)) { echo 'selected'; }?>">
                  <input alt-url="<?php print get_term_link($tag); ?>" id="checkbox-tag-<?php echo $tag->term_id; ?>" type="checkbox" name="tags[]" value="<?php echo $tag->term_id; ?>" <?php if (in_array($tag->term_id, $chosen_tags)) { echo 'checked'; }?>/>
                  <span><?php echo $tag->name; ?></span>
                </label>
              <?php endforeach; ?>
            </div>
          </details>
          <?php endif; ?>
          <br/>
          <input type="submit" value="Apply" class="button-primary button-get-started"/>
        </form>
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

    $instance['content_type'] = 'recommendation';
    if ( in_array( $new_instance['content_type'], array( 'recommendation', 'question', 'post' ) ) ) {
      $instance['content_type'] = $new_instance['content_type'];
    }

    $instance['hide_vocabs'] = empty( $new_instance['hide_vocabs'] ) ? array() :  $new_instance['hide_vocabs'];

    return $instance;
  }

  /**
   * Display the form for this widget on the Widgets page of the Admin area.
   *
   * @param array $instance
   */
  function form( $instance, $fields = Array() ) {
    parent::form( $instance, array() );

    $content_type  = empty( $instance['content_type'] ) ? 'recommendation' : $instance['content_type'];
    ?>
      <p><label for="<?php echo esc_attr( $this->get_field_id( 'content_type' ) ); ?>"><?php _e( 'Content Type:', 'dh' ); ?></label>
      <select id="<?php echo esc_attr( $this->get_field_id( 'content_type' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'content_type' ) ); ?>">
        <option value="recommendation" <?php echo ( $content_type == 'recommendation' ) ? 'selected' : ''; ?>>Recommendations</option>
        <option value="question" <?php echo ( $content_type == 'question' ) ? 'selected' : ''; ?>>Questions</option>
        <option value="post" <?php echo ( $content_type == 'post' ) ? 'selected' : ''; ?>>Posts</option>
        <?php echo esc_attr( $text ); ?>
      </select></p>
    <?php

    $hide_vocabs  = empty( $instance['hide_vocabs'] ) ? array() : $instance['hide_vocabs'];
    $all_vocabs = array(
      'topic' => 'Themes (for recommendations)',
      'organisation' => 'Organisations (for recommendations)',
//       'chapter' => 'Chapters (for questions)',
//       'post_tag' => 'Tags (for posts)',
    );
    ?>
      <p>
        <div>Hide these:</div>
        <?php foreach ( $all_vocabs as $vocab => $label ): ?>
          <input type="checkbox" class="checkbox" value="<?php echo $vocab; ?>" id="<?php echo $this->get_field_id( 'hide_vocabs-' . $vocab ); ?>" name="<?php echo $this->get_field_name( 'hide_vocabs' ) . '[]'; ?>"<?php if ( in_array($vocab, $hide_vocabs) ) { echo 'checked="checked"';} ?> />
          <label for="<?php echo $this->get_field_id( 'hide_vocabs-' . $vocab ); ?>"><?php echo $label; ?></label><br/>
        <?php endforeach;?>
      </p>
    <?php
  }
}