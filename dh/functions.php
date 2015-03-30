<?php
/**
 * Set up the theme and provides some helper functions, which are used in the
 * theme as custom template tags. Others are attached to action and filter
 * hooks in WordPress to change core functionality.
 *
 * When using a child theme you can override certain functions (those wrapped
 * in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before
 * the parent theme's file, so the child theme functions would be used.
 *
 * @link http://codex.wordpress.org/Theme_Development
 * @link http://codex.wordpress.org/Child_Themes
 *
 * Functions that are not pluggable (not wrapped in function_exists()) are
 * instead attached to a filter or action hook.
 *
 * For more information on hooks, actions, and filters,
 * @link http://codex.wordpress.org/Plugin_API
 *
 * @package WordPress
 */

define( 'DH_POSTS_PER_BLOG_PAGE', 3 );

if ( ! function_exists( 'dh_setup' ) ) :
/**
 * Set up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support post thumbnails.
 */
function dh_setup() {

  /*
   * Translations can be added to the /languages/ directory.
   * If you're building a theme based on Twenty Fourteen, use a find and
   * replace to change 'dh' to the name of your theme in all
   * template files.
   */
  load_theme_textdomain( 'dh', get_template_directory() . '/languages' );

  // This theme styles the visual editor to resemble the theme style.
  add_editor_style( array( 'css/editor-style.css', 'genericons/genericons.css' ) );

	// Add RSS feed links to <head> for posts and comments.
	add_theme_support( 'automatic-feed-links' );

	// Enable support for Post Thumbnails, and declare two sizes.
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 672, 372, true );
	add_image_size( 'dh-full-width', 1038, 576, true );

  // This theme uses wp_nav_menu() in two locations.
  register_nav_menus( array(
    'primary'   => __( 'Top primary menu', 'dh' ),
    'secondary' => __( 'Secondary menu in left sidebar', 'dh' ),
  ) );

  dh_install();
}
endif; // dh_setup
add_action( 'after_setup_theme', 'dh_setup' );

/**
 * Getter function for Featured Content Plugin.
 *
 * @return array An array of WP_Post objects.
 */
function dh_get_featured_posts() {
	/**
	 * Filter the featured posts to return in Twenty Fourteen.
	 *
	 * @param array|bool $posts Array of featured posts, otherwise false.
	 */
	return apply_filters( 'dh_get_featured_posts', array() );
}

/**
 * A helper conditional function that returns a boolean value.
 *
 * @return bool Whether there are featured posts.
 */
function dh_has_featured_posts() {
	return ! is_paged() && (bool) dh_get_featured_posts();
}

function dh_pre_get_posts( $query ) {

  // not an admin page and it is the main (paginated) query
  if ( ! is_admin() && $query->is_main_query() ) {

    global $wp;

    $bool_meta_order = FALSE;

    if ( in_array( $wp->matched_rule, array('post/page/([0-9]{1,})/?$', 'post/?$') ) ) {
      $query->set('orderby', 'date');
      $query->set('order', 'DESC');

      $query->set('posts_per_page', -1);
      $tax_query = array();

      if ( isset( $_GET['tags'] ) ) {
        $chosen_tags = $_GET['tags'];

        if ( is_array( $chosen_tags ) ) {
          foreach ( $chosen_tags as $index => $chosen_tag ) {
            if ( ! get_term( $chosen_tag, 'post_tag' ) ) {
              // Remove items that aren't really terms in the taxonomy
              unset( $chosen_tags[$index] );
            }
          }

          if ( count( $chosen_tags ) > 0 ) {
            $tax_query[] = array(
              'taxonomy' => 'post_tag',
              'terms' => $chosen_tags,
              'field' => 'id',
            );
          }
        }
      }

      $query->set( 'tax_query', $tax_query );
    }
    if ( in_array( $wp->matched_rule, array('recommendation/page/([0-9]{1,})/?$', 'recommendation/?$') ) ) {
      $query->set('posts_per_page', -1);
      $tax_query = array();

      if ( isset( $_GET['themes'] ) ) {
        $chosen_themes = $_GET['themes'];

        if ( is_array( $chosen_themes ) ) {
          foreach ( $chosen_themes as $index => $chosen_theme ) {
            if ( ! get_term( $chosen_theme, 'topic' ) ) {
              // Remove items that aren't really terms in the taxonomy
              unset( $chosen_themes[$index] );
            }
          }

          if ( count( $chosen_themes ) > 0 ) {
            $tax_query[] = array(
                'taxonomy' => 'topic',
                'terms' => $chosen_themes,
                'field' => 'id',
            );
          }
        }
      }
      if ( isset( $_GET['organisations'] ) ) {
        $chosen_organisations = $_GET['organisations'];

        if ( is_array( $chosen_organisations ) ) {
          foreach ( $chosen_organisations as $index => $chosen_organisation ) {
            if ( ! get_term( $chosen_organisation, 'organisation' ) ) {
              // Remove items that aren't really terms in the taxonomy
              unset( $chosen_organisations[$index] );
            }
          }

          if ( count( $chosen_organisations ) > 0 ) {
            $tax_query[] = array(
              'taxonomy' => 'organisation',
              'terms' => $chosen_organisations,
              'field' => 'id',
            );
          }
        }
      }

      $query->set( 'tax_query', $tax_query );
      $bool_meta_order = TRUE;
    }
    if ( in_array( $wp->matched_rule, array('question/page/([0-9]{1,})/?$', 'question/?$') ) ) {
      $query->set('posts_per_page', -1);
      $tax_query = array();

      if ( isset( $_GET['chapters'] ) ) {
        $chosen_chapters = $_GET['chapters'];

        if ( is_array( $chosen_chapters ) ) {
          foreach ( $chosen_chapters as $index => $chosen_chapter ) {
            if ( ! get_term( $chosen_chapter, 'chapter' ) ) {
              // Remove items that aren't really terms in the taxonomy
              unset( $chosen_chapter[$index] );
            }
          }

          if ( count( $chosen_chapters ) > 0 ) {
            $tax_query[] = array(
              'taxonomy' => 'chapter',
              'terms' => $chosen_chapters,
              'field' => 'id',
            );
          }
        }
      }

      $query->set( 'tax_query', $tax_query );
      $bool_meta_order = TRUE;
    }

    if ( $query->is_tag() ) {
      $query->set( 'posts_per_page', DH_POSTS_PER_BLOG_PAGE );
    }

    if ( in_array( $wp->matched_rule, array(        'topic/([^/]+)/page/?([0-9]{1,})/?$',        'topic/([^/]+)/?$',
                                             'organisation/([^/]+)/page/?([0-9]{1,})/?$', 'organisation/([^/]+)/?$',
                                                  'chapter/([^/]+)/page/?([0-9]{1,})/?$',      'chapter/([^/]+)/?$' ) ) ) {
      $bool_meta_order = TRUE;
    }

    if ( $bool_meta_order ) {
      $query->set( 'meta_key', 'dh_number' );
      $query->set( 'meta_type', 'NUMERIC' );
      $query->set( 'orderby', 'meta_value_num' );
      $query->set( 'order', 'ASC' );
    }
  }


  if( $query->get( 'orderby' ) == 'DH-Number' ) {
    $query->set( 'meta_key', 'dh_number' );
    $query->set( 'meta_type', 'NUMERIC' );
    $query->set( 'orderby', 'meta_value_num' );
  }
}
add_action( 'pre_get_posts', 'dh_pre_get_posts' );

/**
 * Register widgets and widget areas.
 */
function dh_widgets_init() {
  require get_template_directory() . '/inc/widgets/super_widget.php';
  $widgets = array( 'Widget_Category_Content',
                    'Widget_Category_Posts',
                    'Widget_Consultation_Summary',
                    'Widget_Event',
                    'Widget_Filters',
                    'Widget_Free_Image',
                    'Widget_Hidden_Text',
                    'Widget_Link_Events',
                    'Widget_Link_Recommendations',
                    'Widget_Link_Questions',
                    'Widget_News_Spotlight',
                    'Widget_Newsletter',
                    'Widget_Page_Summary',
                    'Widget_Parliament_RSS',
                    'Widget_Question',
                    'Widget_Quote',
                    'Widget_Recommendations',
                    'Widget_Recent_Comments',
                    'Widget_Related_Links',
                    'Widget_Space',
                    'Widget_Said_Elsewhere',
                    'Widget_Share_This_Page',
                    'Widget_Site_Content',
                    'Widget_Spotlight',
                    'Widget_Tabs',
                    'Widget_Text',
                    'Widget_Twitter' );
  foreach ($widgets as $widget) {
    require get_template_directory() . '/inc/widgets/' . strtolower($widget) . '.php';
    register_widget( 'DH_' . $widget );
  }

  // Removing all non-DH widgets, since adding them just breaks the layout.
  global $wp_widget_factory;
  foreach ($wp_widget_factory->widgets as $wp_widget_class => $wp_widget) {
    if (substr($wp_widget_class, 0, 3) != 'DH_') {
      unregister_widget($wp_widget_class);
    }
  }

  register_sidebar( array(
    'name'          => __( 'Top', 'dh' ),
    'id'            => 'sidebar-top',
    'description'   => __( 'Widgets section on the top.', 'dh' ),
    'before_widget' => '',
    'after_widget'  => '',
    'before_title'  => '',
    'after_title'   => '',
  ) );

  register_sidebar( array(
    'name'          => __( 'Bottom', 'dh' ),
    'id'            => 'sidebar-bottom',
    'description'   => __( 'Widgets section in the bottom.', 'dh' ),
    'before_widget' => '',
    'after_widget'  => '',
    'before_title'  => '',
    'after_title'   => '',
  ) );
}
add_action( 'widgets_init', 'dh_widgets_init' );

/**
 * Register custom post types.
 */
function dh_init() {
  update_option('thumbnail_size_h', 210);
  update_option('thumbnail_size_w', 320);
  update_option('thumbnail_crop', 1);
  update_option('medium_size_h', 423);
  update_option('medium_size_w', 670);
  update_option('medium_crop', 1);
  update_option('large_size_h', 680);
  update_option('large_size_w', 1020);
  update_option('large_crop', 1);
  update_option( 'date_format', 'd/m/Y' );

  register_post_type('event', array(
    'labels' => array(
      'name' => __( 'Events' ),
      'singular_name' => __( 'Event' ),
    ),
    'public' => TRUE,
    'has_archive' => TRUE,
    'menu_icon' => 'dashicons-feedback',
    'description' => 'DH post type for events',
    'has_archive' => TRUE,
    'supports' => array('title', 'editor', 'author', 'excerpt', 'comments', 'revisions', 'thumbnail'),
  ));

  register_post_type('question', array(
    'labels' => array(
      'name' => __( 'Questions' ),
      'singular_name' => __( 'Question' ),
    ),
    'public' => TRUE,
    'has_archive' => TRUE,
    'menu_icon' => 'dashicons-clipboard',
    'description' => 'DH post type for questions',
    'has_archive' => TRUE,
    'supports' => array('title', 'editor', 'author', 'excerpt', 'comments', 'revisions', 'thumbnail'),
  ));

  register_post_type('custom_layout', array(
    'labels' => array(
      'name' => __( 'Custom Layouts' ),
      'singular_name' => __( 'Custom Layout' ),
    ),
    'public' => TRUE,
    'has_archive' => TRUE,
    'menu_icon' => 'dashicons-tagcloud',
    'description' => 'DH post type to build a custom layout of widgets',
    'show_in_nav_menus' => true,
  ));

  register_post_type('recommendation', array(
    'labels' => array(
      'name' => __( 'Recommendations' ),
      'singular_name' => __( 'Recommendation' ),
    ),
    'public' => TRUE,
    'has_archive' => TRUE,
    'menu_icon' => 'dashicons-awards',
    'description' => 'DH post type for recommendations',
    'has_archive' => TRUE,
    'supports' => array('title', 'editor', 'author', 'excerpt', 'comments', 'revisions', 'thumbnail'),
  ));

  register_post_type('news', array(
    'labels' => array(
      'name' => __( 'News' ),
      'singular_name' => __( 'News' ),
    ),
    'public' => TRUE,
    'has_archive' => TRUE,
    'menu_icon' => 'dashicons-testimonial',
    'description' => 'DH post type for news/spotlights',
    'has_archive' => TRUE,
    'supports' => array('title', 'editor', 'author', 'excerpt', 'comments', 'revisions', 'thumbnail'),
  ));

  $post_type = get_post_type_object( 'post' );
  $post_type->has_archive = TRUE;
  $post_type->rewrite['slug'] = 'post';
  $post_type->query_var = 'post';
  register_post_type( 'post', $post_type );

  register_taxonomy(
    'topic',
    'recommendation',
    array(
      'label' => __( 'Theme' ),
      'capabilities' => array(
        'manage_terms' => 'manage_categories',
        'edit_terms'   => 'manage_categories',
        'delete_terms' => 'manage_categories',
        'assign_terms' => 'edit_posts',
      )
    )
  );

  register_taxonomy(
    'organisation',
    'recommendation',
    array(
      'label' => __( 'Organisation' ),
      'capabilities' => array(
        'manage_terms' => 'manage_categories',
        'edit_terms'   => 'manage_categories',
        'delete_terms' => 'manage_categories',
        'assign_terms' => 'edit_posts',
      )
    )
  );

  register_taxonomy(
    'chapter',
    'question',
    array(
      'label' => __( 'Chapter' ),
      'capabilities' => array(
        'manage_terms' => 'manage_categories',
        'edit_terms'   => 'manage_categories',
        'delete_terms' => 'manage_categories',
        'assign_terms' => 'edit_posts',
      ),
      'hierarchical' => TRUE,
      'meta_box_cb' => '_dh_post_categories_meta_box_prevent_top_level',
    )
  );

  // Register these taxonomies for Custom Layouts
  register_taxonomy_for_object_type( 'topic', 'custom_layout' );
  register_taxonomy_for_object_type( 'organisation', 'custom_layout' );
  register_taxonomy_for_object_type( 'chapter', 'custom_layout' );

  $current_panels_settings = get_option( 'siteorigin_panels_settings', array() );
  if ( empty( $current_panels_settings['post-types'] ) || ! is_array( $current_panels_settings['post-types'] ) || ! in_array('custom_layout', $current_panels_settings['post-types'])) {
    $current_panels_settings['post-types'][] = 'custom_layout';
    update_option('siteorigin_panels_settings', $current_panels_settings);
  }

  if(function_exists("register_field_group"))
  {
    register_field_group(array (
      'id' => 'acf_event-meta',
      'title' => 'Event Meta',
      'fields' => array (
        array (
          'key' => 'field_541add257bed4',
          'label' => 'Start Date/Time',
          'name' => 'start_date',
          'type' => 'date_time_picker',
          'required' => 1,
          'show_date' => 'true',
          'date_format' => 'd/m/yy',
          'time_format' => 'h:mm tt',
          'show_week_number' => 'false',
          'picker' => 'select',
          'save_as_timestamp' => 'true',
          'get_as_timestamp' => 'true',
        ),
        array (
          'key' => 'field_541add8d7bed5',
          'label' => 'End Date/Time',
          'name' => 'end_date',
          'type' => 'date_time_picker',
          'required' => 1,
          'show_date' => 'true',
          'date_format' => 'd/m/yy',
          'time_format' => 'h:mm tt',
          'show_week_number' => 'false',
          'picker' => 'select',
          'save_as_timestamp' => 'true',
          'get_as_timestamp' => 'true',
        ),
        array (
          'key' => 'field_541addb97bed6',
          'label' => 'Location',
          'name' => 'location',
          'type' => 'text',
          'default_value' => '',
          'placeholder' => '',
          'prepend' => '',
          'append' => '',
          'formatting' => 'none',
          'maxlength' => '',
        ),
      ),
      'location' => array (
        array (
          array (
          'param' => 'post_type',
          'operator' => '==',
          'value' => 'event',
          'order_no' => 0,
          'group_no' => 0,
          ),
        ),
      ),
      'options' => array (
        'position' => 'normal',
        'layout' => 'no_box',
        'hide_on_screen' => array (),
      ),
      'menu_order' => 0,
    ));

    register_field_group(array (
      'id' => 'acf_recommendation-meta',
      'title' => 'Recommendation Meta',
      'fields' => array (
        array (
          'key' => 'field_541af070fd8a7',
          'label' => 'Decision',
          'name' => 'decision',
          'type' => 'select',
          'choices' => array (
            '0' => 'No decision yet',
            'Accepted' => 'Accepted',
            'Accepted in principle' => 'Accepted in principle',
            'Accepted in part' => 'Accepted in part',
            'Not accepted - agree with principle' => 'Not accepted, although we agee with the principle regarding changes to the regulatory system.',
            'Rejected' => 'Rejected',
          ),
          'default_value' => '',
          'allow_null' => 0,
          'multiple' => 0,
        ),
      ),
      'location' => array (
        array (
          array (
            'param' => 'post_type',
            'operator' => '==',
            'value' => 'recommendation',
            'order_no' => 0,
            'group_no' => 0,
          ),
        ),
      ),
      'options' => array (
        'position' => 'normal',
        'layout' => 'no_box',
        'hide_on_screen' => array (),
      ),
      'menu_order' => 0,
    ));

    register_field_group(array (
      'id' => 'acf_file-uploads',
      'title' => 'File Uploads',
      'fields' => array (
        array (
          'key' => 'field_5432a1fd01bac',
          'label' => 'File Upload',
          'name' => 'file_upload1',
          'type' => 'file',
          'save_format' => 'object',
          'library' => 'all',
        ),
        array (
          'key' => 'field_5432a21301bad',
          'label' => 'File Upload',
          'name' => 'file_upload2',
          'type' => 'file',
          'save_format' => 'object',
          'library' => 'all',
        ),
        array (
          'key' => 'field_5432a22101bae',
          'label' => 'File Upload',
          'name' => 'file_upload3',
          'type' => 'file',
          'save_format' => 'object',
          'library' => 'all',
        ),
      ),
      'location' => array (
        array (
          array (
            'param' => 'post_type',
            'operator' => '!=',
            'value' => 'custom_layout',
            'order_no' => 0,
            'group_no' => 0,
          ),
          array (
            'param' => 'post_type',
            'operator' => '!=',
            'value' => 'wpcf7_contact_form',
            'order_no' => 1,
            'group_no' => 0,
          ),
        ),
      ),
      'options' => array (
        'position' => 'normal',
        'layout' => 'no_box',
        'hide_on_screen' => array (),
      ),
      'menu_order' => 0,
    ));

    register_field_group(array (
      'id' => 'acf_dh-numbers',
      'title' => 'DH Numbers',
      'fields' => array (
        array (
          'key' => 'field_54c65c4e42521',
          'label' => 'DH Number',
          'name' => 'dh_number',
          'type' => 'number',
          'required' => ! get_theme_mod( 'dh_numbering_auto' ),
          'instructions' => 'Assign a DH number to this post. If the number is already in use, then all current posts with DH numbers equal to or greater than the number entered here will be shifted up by 1 (unless it is disabled from the "Miscellaneous" list in the customizer).',
          'default_value' => '',
          'placeholder' => '',
          'prepend' => '',
          'append' => '',
          'min' => '',
          'max' => '',
          'step' => '',
        ),
      ),
      'location' => array (
        array (
          array (
            'param' => 'post_type',
            'operator' => '==',
            'value' => 'question',
            'order_no' => 0,
            'group_no' => 0,
          ),
        ),
        array (
          array (
            'param' => 'post_type',
            'operator' => '==',
            'value' => 'recommendation',
            'order_no' => 0,
            'group_no' => 1,
          ),
        ),
      ),
      'options' => array (
        'position' => 'normal',
        'layout' => 'no_box',
        'hide_on_screen' => array (
        ),
      ),
      'menu_order' => 0,
    ));
  }

  if (get_option( 'permalink_structure', '' ) != '/%postname%/' ) {
    update_option( 'permalink_structure', '/%postname%/' );
    // The following code is inspired by the code in wp-admin/options-permalinks.php
    global $wp_rewrite;
    $prefix = $blog_prefix = '';
    if ( ! got_url_rewrite() )
      $prefix = '/index.php';
    if ( is_multisite() && !is_subdomain_install() && is_main_site() )
      $blog_prefix = '/blog';
    $permalink_structure = preg_replace( '#/+#', '/', '/' . str_replace( '#', '', '/%postname%/' ) );
    if ( $prefix && $blog_prefix )
      $permalink_structure = $prefix . preg_replace( '#^/?index\.php#', '', $permalink_structure );
    else
      $permalink_structure = $blog_prefix . $permalink_structure;
    $wp_rewrite->set_permalink_structure( $permalink_structure );

    $wp_rewrite->set_category_base( get_option( 'category_base' ) );
    $wp_rewrite->set_tag_base( get_option( 'tag_base' ) );

    flush_rewrite_rules();
  }
}
add_action( 'init', 'dh_init' );

function dh_editable_roles( $roles ) {
  unset( $roles['subscriber'] );
  unset( $roles['author'] );
  unset( $roles['editor'] );

  $roles['administrator']['name'] = 'Site Administrator';

  return $roles;
}
add_filter( 'editable_roles', 'dh_editable_roles', 20);

function dh_prepare_post_comments_options_html( $id ) {
  $comments = array();
  if ( $id ) {
    $comments = get_comments( array( 'post_id' => $id ) );
  }

  $output = '<option value="-1"> (Most recent) </option>';

  foreach ($comments as $comment) {
    $output .= '<option value="' . $comment->comment_ID . '" >' . esc_html($comment->comment_ID . ':[' . $comment->comment_author . ']:' . substr($comment->comment_content, 0)) . '</option>';
  }

  return $output;
}

//get_question_comments
function get_question_comments() {
  if ( isset( $_POST['question_id'] ) && is_numeric( $_POST['question_id'] ) ) {
    $question_id = $_POST['question_id'];
  }
  wp_enqueue_script( 'comment-reply' );
  echo dh_prepare_post_comments_options_html( $question_id );

  // die() here to avoid the "0" at the end of wp-admin/admin-ajax.php
  die();
}

add_action( 'wp_ajax_get_question_comments', 'get_question_comments' );
add_action( 'wp_ajax_nopriv_get_question_comments', 'get_question_comments' );

/**
 * Enqueue scripts and styles for the front end.
 */
function dh_scripts() {
	// Add Genericons font, used in the main stylesheet.
	wp_enqueue_style( 'genericons', get_template_directory_uri() . '/genericons/genericons.css', array(), '3.0.3' );

	// Load our main stylesheet.
	wp_enqueue_style( 'dh-style', get_stylesheet_uri(), array( 'genericons' ) );

	wp_enqueue_style( 'dh-colour-style', get_template_directory_uri() . '/style-colour.css', array() );

	// Load the Internet Explorer specific stylesheet.
	wp_enqueue_style( 'dh-ie', get_template_directory_uri() . '/css/ie.css', array( 'dh-style', 'genericons' ), '20131205' );
	wp_style_add_data( 'dh-ie', 'conditional', 'lt IE 9' );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	if ( is_singular() && wp_attachment_is_image() ) {
		wp_enqueue_script( 'dh-keyboard-image-navigation', get_template_directory_uri() . '/js/keyboard-image-navigation.js', array( 'jquery' ), '20130402' );
	}

	if ( is_active_sidebar( 'sidebar-3' ) ) {
		wp_enqueue_script( 'jquery-masonry' );
	}

	wp_enqueue_script( 'dh-script', get_template_directory_uri() . '/js/functions.js', array( 'jquery' ), '20140616', true );

	wp_enqueue_script( 'dh-vendor-polyfills-js', get_template_directory_uri() . '/js/vendor/polyfills/bind.js', array( 'jquery' ), '20140616', true );
	wp_enqueue_script( 'dh-govuk-selection-buttons-js', get_template_directory_uri() . '/js/govuk/selection-buttons.js', array( 'jquery' ), '20140616', true );
	wp_enqueue_script( 'dh-polyfills-js', get_template_directory_uri() . '/js/details.polyfills.js', array( 'jquery' ), '20140616', true );

	wp_enqueue_script( 'dh-tabs-js-p', get_template_directory_uri() . '/js/jquery.easytabs.js', array( 'jquery' ), '20140616', false );
	wp_enqueue_script( 'dh-main-js', get_template_directory_uri() . '/js/main.js?gamma', array( 'jquery', 'dh-tabs-js-p' ), '20140616', true );
}
add_action( 'wp_enqueue_scripts', 'dh_scripts' );

/**
 * Enqueue Google fonts style to admin screen for custom header display.
 */
function dh_admin_fonts() {
	wp_enqueue_style( 'dh-lato', array(), null );
}
add_action( 'admin_print_scripts-appearance_page_custom-header', 'dh_admin_fonts' );

if ( ! function_exists( 'dh_the_attached_image' ) ) :
/**
 * Print the attached image with a link to the next attached image.
 */
function dh_the_attached_image() {
	$post                = get_post();
	/**
	 * @param array $dimensions {
	 *     An array of height and width dimensions.
	 *
	 *     @type int $height Height of the image in pixels. Default 810.
	 *     @type int $width  Width of the image in pixels. Default 810.
	 * }
	 */
	$attachment_size     = apply_filters( 'dh_attachment_size', array( 810, 810 ) );
	$next_attachment_url = wp_get_attachment_url();

	/*
	 * Grab the IDs of all the image attachments in a gallery so we can get the URL
	 * of the next adjacent image in a gallery, or the first image (if we're
	 * looking at the last image in a gallery), or, in a gallery of one, just the
	 * link to that image file.
	 */
	$attachment_ids = get_posts( array(
		'post_parent'    => $post->post_parent,
		'fields'         => 'ids',
		'numberposts'    => -1,
		'post_status'    => 'inherit',
		'post_type'      => 'attachment',
		'post_mime_type' => 'image',
		'order'          => 'ASC',
		'orderby'        => 'menu_order ID',
	) );

	// If there is more than 1 attachment in a gallery...
	if ( count( $attachment_ids ) > 1 ) {
		foreach ( $attachment_ids as $attachment_id ) {
			if ( $attachment_id == $post->ID ) {
				$next_id = current( $attachment_ids );
				break;
			}
		}

		// get the URL of the next image attachment...
		if ( $next_id ) {
			$next_attachment_url = get_attachment_link( $next_id );
		}

		// or get the URL of the first image attachment.
		else {
			$next_attachment_url = get_attachment_link( array_shift( $attachment_ids ) );
		}
	}

	printf( '<a href="%1$s" rel="attachment">%2$s</a>',
		esc_url( $next_attachment_url ),
		wp_get_attachment_image( $post->ID, $attachment_size )
	);
}
endif;

if ( ! function_exists( 'dh_list_authors' ) ) :
/**
 * Print a list of all site contributors who published at least one post.
 *
 * @since Twenty Fourteen 1.0
 */
function dh_list_authors() {
	$contributor_ids = get_users( array(
		'fields'  => 'ID',
		'orderby' => 'post_count',
		'order'   => 'DESC',
		'who'     => 'authors',
	) );

	foreach ( $contributor_ids as $contributor_id ) :
		$post_count = count_user_posts( $contributor_id );

		// Move on if user has not published a post (yet).
		if ( ! $post_count ) {
			continue;
		}
	?>

	<div class="contributor">
		<div class="contributor-info">
			<div class="contributor-avatar"><?php echo get_avatar( $contributor_id, 132 ); ?></div>
			<div class="contributor-summary">
				<h2 class="contributor-name"><?php echo get_the_author_meta( 'display_name', $contributor_id ); ?></h2>
				<p class="contributor-bio">
					<?php echo get_the_author_meta( 'description', $contributor_id ); ?>
				</p>
				<a class="button contributor-posts-link" href="<?php echo esc_url( get_author_posts_url( $contributor_id ) ); ?>">
					<?php printf( _n( '%d Article', '%d Articles', $post_count, 'dh' ), $post_count ); ?>
				</a>
			</div><!-- .contributor-summary -->
		</div><!-- .contributor-info -->
	</div><!-- .contributor -->

	<?php
	endforeach;
}
endif;

/**
 * Extend the default WordPress body classes.
 *
 * Adds body classes to denote:
 * 1. Single or multiple authors.
 * 2. Presence of header image except in Multisite signup and activate pages.
 * 3. Index views.
 * 4. Full-width content layout.
 * 5. Presence of footer widgets.
 * 6. Single views.
 * 7. Featured content layout.
 *
 * @param array $classes A list of existing body class values.
 * @return array The filtered body class list.
 */
function dh_body_classes( $classes ) {
	if ( is_multi_author() ) {
		$classes[] = 'group-blog';
	}

	if ( get_header_image() ) {
		$classes[] = 'header-image';
	} elseif ( ! in_array( $GLOBALS['pagenow'], array( 'wp-activate.php', 'wp-signup.php' ) ) ) {
		$classes[] = 'masthead-fixed';
	}

	if ( is_archive() || is_search() || is_home() ) {
		$classes[] = 'list-view';
	}

	if ( ( ! is_active_sidebar( 'sidebar-2' ) )
		|| is_page_template( 'page-templates/full-width.php' )
		|| is_page_template( 'page-templates/contributors.php' )
		|| is_attachment() ) {
		$classes[] = 'full-width';
	}

	if ( is_active_sidebar( 'sidebar-3' ) ) {
		$classes[] = 'footer-widgets';
	}

	if ( is_singular() && ! is_front_page() ) {
		$classes[] = 'singular';
	}

	if ( is_front_page() ) {
		$classes[] = 'grid';
	}

	return $classes;
}
add_filter( 'body_class', 'dh_body_classes' );

/**
 * Extend the default WordPress post classes.
 *
 * Adds a post class to denote:
 * Non-password protected page with a post thumbnail.
 *
 * @param array $classes A list of existing post class values.
 * @return array The filtered post class list.
 */
function dh_post_classes( $classes ) {
  global $post;
	if ( ! post_password_required() && ! is_attachment() && has_post_thumbnail() ) {
		$classes[] = 'has-post-thumbnail';
	}

  if ( $post->post_type == 'question' ) {
    $classes[] = 'question';
    $classes[] = 'intro';
  }

  return $classes;
}
add_filter( 'post_class', 'dh_post_classes' );

/**
 * Create a nicely formatted and more specific title element text for output
 * in head of document, based on current view.
 *
 * @global int $paged WordPress archive pagination page count.
 * @global int $page  WordPress paginated post page count.
 *
 * @param string $title Default title text for current view.
 * @param string $sep Optional separator.
 * @return string The filtered title.
 */
function dh_wp_title( $title, $sep ) {
	global $paged, $page;

	if ( is_feed() ) {
		return $title;
	}

	// Add the site name.
	$title .= get_bloginfo( 'name', 'display' );

	// Add the site description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) ) {
		$title = "$title $sep $site_description";
	}

	// Add a page number if necessary.
	if ( ( $paged >= 2 || $page >= 2 ) && ! is_404() ) {
		$title = "$title $sep " . sprintf( __( 'Page %s', 'dh' ), max( $paged, $page ) );
	}

	return $title;
}
add_filter( 'wp_title', 'dh_wp_title', 10, 2 );

// Custom template tags for this theme.
require get_template_directory() . '/inc/template-tags.php';

// Add Theme Customizer functionality.
require get_template_directory() . '/inc/customizer.php';


/**
 * (Inspired/copied-and-modified from siteorigin_panels plugin's siteorigin_panels_render())
 * Render the panels
 *
 * @param int|string|bool $post_id The Post ID or 'home'.
 * @param bool $enqueue_css Should we also enqueue the layout CSS.
 * @param array|bool $panels_data Existing panels data. By default load from settings or post meta.
 * @return string
 */
function _dh_siteorigin_panels_render( $post_id = false, $enqueue_css = true, $panels_data = false, $layout_width = 3 ) {
  if( empty($post_id) ) $post_id = get_the_ID();

  global $siteorigin_panels_current_post;
  $old_current_post = $siteorigin_panels_current_post;
  $siteorigin_panels_current_post = $post_id;

  // Try get the cached panel from in memory cache.
  global $siteorigin_panels_cache;
  /*if(!empty($siteorigin_panels_cache) && !empty($siteorigin_panels_cache[$post_id]))
    return $siteorigin_panels_cache[$post_id];*/

  if( empty($panels_data) ) {
    if( strpos($post_id, 'prebuilt:') === 0) {
      list($null, $prebuilt_id) = explode(':', $post_id, 2);
      $layouts = apply_filters('siteorigin_panels_prebuilt_layouts', array());
      $panels_data = !empty($layouts[$prebuilt_id]) ? $layouts[$prebuilt_id] : array();
    }
    else if($post_id == 'home'){
      $panels_data = get_post_meta( get_option('siteorigin_panels_home_page_id'), 'panels_data', true );

      if( is_null($panels_data) ){
        // Load the default layout
        $layouts = apply_filters('siteorigin_panels_prebuilt_layouts', array());
        $prebuilt_id = siteorigin_panels_setting('home-page-default') ? siteorigin_panels_setting('home-page-default') : 'home';

        $panels_data = !empty($layouts[$prebuilt_id]) ? $layouts[$prebuilt_id] : current($layouts);
      }
    }
    else{
      if ( post_password_required($post_id) ) return false;
      $panels_data = get_post_meta( $post_id, 'panels_data', true );
    }
  }

  $panels_data = apply_filters( 'siteorigin_panels_data', $panels_data, $post_id );
  if( empty( $panels_data ) || empty( $panels_data['grids'] ) ) return '';

  // Create the skeleton of the grids
  $grids = array();
  if( !empty( $panels_data['grids'] ) && !empty( $panels_data['grids'] ) ) {
    foreach ( $panels_data['grids'] as $gi => $grid ) {
      $gi = intval( $gi );
      $grids[$gi] = array();
      for ( $i = 0; $i < $grid['cells']; $i++ ) {
        $grids[$gi][$i] = array();
      }
    }
  }

  // We need this to migrate from the old $panels_data that put widget meta into the "info" key instead of "panels_info"
  if( !empty( $panels_data['widgets'] ) && is_array($panels_data['widgets']) ) {
    foreach ( $panels_data['widgets'] as $i => $widget ) {
      if( empty( $panels_data['widgets'][$i]['panels_info'] ) ) {
        $panels_data['widgets'][$i]['panels_info'] = $panels_data['widgets'][$i]['info'];
        unset($panels_data['widgets'][$i]['info']);
      }
    }
  }

  if( !empty( $panels_data['widgets'] ) && is_array($panels_data['widgets']) ){
    foreach ( $panels_data['widgets'] as $widget ) {
      $grids[intval( $widget['panels_info']['grid'] )][intval( $widget['panels_info']['cell'] )][] = $widget;
    }
  }

  ob_start();

  global $siteorigin_panels_inline_css;
  if(empty($siteorigin_panels_inline_css)) $siteorigin_panels_inline_css = '';

  echo '<div class="custom-layout-entry-content">';
  $cells_count = 0;
  foreach ( $grids as $gi => $cells ) {

    echo '<section class="section-row">';
    echo   '<div class="grid-wrapper">';

    foreach ( $cells as $ci => $widgets ) {
      switch (count($cells)) {
        case 1:
          $width_in_cols = $layout_width;
          break;
        case 2:
          // Assign the width in columns depending on which cell is set to be wider in teh layout editor
          $width_in_cols = 1;
          $me = $ci;
          $the_other = ($ci == 0) ? 1 : 0;

          $my_width        = $panels_data['grid_cells'][$cells_count + $me]['weight'];
          $the_other_width = $panels_data['grid_cells'][$cells_count + $the_other]['weight'];

          if ($my_width > $the_other_width || ($my_width == $the_other_width && $me == 0)) {
            $width_in_cols = 2;
          }

          // If the total row width is 2 or 1, then the width of this cell will always be 1
          if ( $layout_width < 3) {
            $width_in_cols = 1;
          }
          break;
        case 3:
        default:
          $width_in_cols = 1;
          break;
      }
      $cells_count += count($cells);

      echo '<div class="grid-' . $width_in_cols . '-' . $layout_width . '">';
      foreach ( $widgets as $pi => $widget_info ) {
        $data = $widget_info;
        $data['dh_width_suggestion'] = $width_in_cols;
        $data['dh_visual_width'] = $width_in_cols;
        $data['dh_row_width'] = $width_in_cols;
        $data['dh_inside_custom_layout'] = TRUE;
        unset( $data['panels_info'] );
        _dh_siteorigin_panels_the_widget( $widget_info['panels_info']['class'], $data, $gi, $ci, $pi, $pi == 0, $pi == count( $widgets ) - 1, $post_id, $widget_style_wrapper );
      }
      echo '</div>';
    }

    echo   '</div>';
    echo '</section>';
  }
  echo '</div>';

  $html = ob_get_clean();

  // Reset the current post
  $siteorigin_panels_current_post = $old_current_post;

  return apply_filters( 'siteorigin_panels_render', $html, $post_id, !empty($post) ? $post : null );
}

/**
 * (Inspired/copied-and-modified from siteorigin_panels plugin's siteorigin_panels_prepare_single_post_content())
 * Prepare the content of the page early on so widgets can enqueue their scripts and styles
 */
function _dh_panels_prepare_single_post_content(){
  if( is_singular() ) {
    global $siteorigin_panels_cache;
    if( empty($siteorigin_panels_cache[ get_the_ID() ] ) ) {
      $siteorigin_panels_cache[ get_the_ID() ] = _dh_siteorigin_panels_render( get_the_ID() );
    }
  }
}
remove_action('wp_enqueue_scripts', 'siteorigin_panels_prepare_single_post_content', 10);
add_action('wp_enqueue_scripts', '_dh_panels_prepare_single_post_content');

/**
 * (Inspired/copied-and-modified from siteorigin_panels plugin's siteorigin_panels_the_widget())
 * Render the widget.
 *
 * @param string $widget The widget class name.
 * @param array $instance The widget instance
 * @param int $grid The grid number.
 * @param int $cell The cell number.
 * @param int $panel the panel number.
 * @param bool $is_first Is this the first widget in the cell.
 * @param bool $is_last Is this the last widget in the cell.
 * @param bool $post_id
 */
function _dh_siteorigin_panels_the_widget( $widget, $instance, $grid, $cell, $panel, $is_first, $is_last, $post_id = false ) {

  global $wp_widget_factory;

  // Load the widget from the widget factory and give plugins a chance to provide their own
  $the_widget = !empty($wp_widget_factory->widgets[$widget]) ? $wp_widget_factory->widgets[$widget] : false;
  $the_widget = apply_filters( 'siteorigin_panels_widget_object', $the_widget, $widget );

  if( empty($post_id) ) $post_id = get_the_ID();

  $id = 'panel-' . $post_id . '-' . $grid . '-' . $cell . '-' . $panel;

  $args = array(
      'before_widget' => '',
      'after_widget' => '',
      'before_title' => '',
      'after_title' => '',
      'widget_id' => 'widget-' . $grid . '-' . $cell . '-' . $panel
  );

  if ( !empty($the_widget) && is_a($the_widget, 'WP_Widget')  ) {
    $the_widget->widget($args , $instance );
  }
  else {
    // This gives themes a chance to display some sort of placeholder for missing widgets
    echo apply_filters('siteorigin_panels_missing_widget', '', $widget, $args , $instance);
  }
}

function dh_indent_shotcode( $atts, $content="" ) {
  return "<div class='indented'>$content</div>";
}
add_shortcode( 'dh-indent', 'dh_indent_shotcode' );

if ( current_user_can( 'edit_posts' ) && current_user_can( 'edit_pages' ) ) {
  add_filter( 'mce_buttons', 'dh_filter_mce_button' );
  add_filter( 'mce_external_plugins', 'dh_filter_mce_plugin' );
}

function dh_filter_mce_button( $buttons ) {
  // add a separation before our button, here our button's id is &quot;mygallery_button&quot;
  array_push( $buttons, 'dh_indent_button' );
  return $buttons;
}

function dh_filter_mce_plugin( $plugins ) {
  // this plugin file will work the magic of our button
  $plugins['dh_indent_button'] = get_template_directory_uri() . '/js/dh_indent_plugin.js';
  return $plugins;
}

/**
 * Output a single comment.
 *
 * @access protected
 * @since 3.6.0
 *
 * @see wp_list_comments()
 *
 * @param object $comment Comment to display.
 * @param int    $depth   Depth of comment.
 * @param array  $args    An array of arguments.
 */
function dh_comment( $comment, $args, $depth ) {
  if ( 'div' == $args['style'] ) {
    $tag = 'div';
    $add_below = 'comment';
  } else {
    $tag = 'li';
    $add_below = 'div-comment';
  }

  $query_char = '?';
  if ( strpos( get_permalink( $comment->comment_post_ID ), '?' ) !== FALSE ) {
    $query_char = '&';
  }
  ?>
  <<?php echo $tag; ?> <?php comment_class( $comment->has_children ? 'parent' : '' ); ?> id="comment-<?php comment_ID(); ?>">
    <div class="comments-meta">
      <div class="comments-date"> <?php printf( __( '%1$s | %2$s' ), get_comment_date(),  get_comment_time() ); ?> </div>
      <div class="comments-author"> <?php echo get_comment_author(); ?> </div>
      <div class="report">
        <?php edit_comment_link( __( '(Edit)' ), '&nbsp;&nbsp;', '' ); ?>
        <a href="<?php echo get_permalink( get_option( 'dh_page_report-a-comment', 0 ) ) . $query_char . 'comment=' . get_comment_ID(); ?>">Report</a>
      </div>
    </div>

    <?php if ( '0' == $comment->comment_approved ) : ?>
      <em class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.' ) ?></em><br />
    <?php endif; ?>

    <div class="comment-body">
      <?php comment_text( get_comment_id(), array_merge( $args, array( 'add_below' => $add_below, 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
    </div>

    <div class="comments-meta">
      <?php
      $reply_link = get_comment_reply_link( array_merge( $args, array( 'add_below' => $add_below, 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ), $comment, $comment->comment_post_ID );
      $reply_link = str_replace( esc_url( add_query_arg( 'replytocom', $comment->comment_ID ) ), esc_url( get_permalink( $comment->comment_post_ID ) . $query_char . 'replytocom=' . $comment->comment_ID ) , $reply_link );
      echo $reply_link;
      ?>
    </div>
  <?php
}

function dh_get_comment_fields_options() {
  $fields = array();

  $theme_mods = get_theme_mods();

  if ( $theme_mods['dh_comment_field_author_enabled'] ) {
    $fields['author'] = array('label' => __('Your name'), 'type' => 'text', 'required' => $theme_mods['dh_comment_field_author_required']);
  }

  if ( $theme_mods['dh_comment_field_email_enabled'] ) {
    $fields['email'] = array('label' => __('E-mail'), 'type' => 'text', 'required' => $theme_mods['dh_comment_field_email_required']);
  }

  if ( $theme_mods['dh_comment_field_custom_text_1_enabled'] ) {
    $fields['dh_comment_field_custom_text_1'] = array('label' => $theme_mods['dh_comment_field_custom_text_1_label'], 'type' => 'text', 'required' => $theme_mods['dh_comment_field_custom_text_1_required']);
  }

  if ( $theme_mods['dh_comment_field_custom_text_2_enabled'] ) {
    $fields['dh_comment_field_custom_text_2'] = array('label' => $theme_mods['dh_comment_field_custom_text_2_label'], 'type' => 'text', 'required' => $theme_mods['dh_comment_field_custom_text_2_required']);
  }

  for ( $i = 1; $i <= 3; $i++ ) {
    $prefix = 'dh_comment_field_custom_dd_' . $i;

    if ( $theme_mods[$prefix . '_enabled'] ) {
      $fields['organisation'] = array(
        'label' => $theme_mods[$prefix . '_label'],
        'type' => 'select',
        'required' => $theme_mods[$prefix . '_required'],
        'choices' => explode("\n", str_replace("\r", '', $theme_mods[$prefix . '_items'])),
      );
    }
  }

  return $fields;
}

function _dh_custom_main_comment_field() {
  return '<label for="comment">' . _x( 'Your comment', 'noun' ) . '</label> <textarea id="comment" name="comment" cols="30" rows="10" aria-required="true" placeholder="Enter your comment here..."></textarea>';
}

function dh_comment_fields($fields) {
  $fields = array();

  $field_options = dh_get_comment_fields_options();

  $fields['dh_main_comment'] = _dh_custom_main_comment_field();

  foreach ($field_options as $machine_name => $info) {
    $fields[$machine_name] = '';
    $fields[$machine_name] .= '<label for="' . $machine_name . '">';
    $fields[$machine_name] .=   $info['label'];
    $fields[$machine_name] .=   '<span class="comment-meta"> ';
    if ($info['required']) {
      $fields[$machine_name] .= '(required)';
    }
    else {
      $fields[$machine_name] .= '(optional)';
    }
    $fields[$machine_name] .=   '</span>';
    $fields[$machine_name] .= '</label>';
    switch ($info['type']) {
      case 'select':
        $fields[$machine_name] .= '<select id="' . $machine_name . '" name="' . $machine_name . '">';
        foreach ( $info['choices'] as $choice ) {
          $fields[$machine_name] .= '<option value="' . $choice . '">' . esc_html($choice) . '</option>';
        }
        $fields[$machine_name] .= '</select>';
        break;
      case 'text':
      default:
        $fields[$machine_name] .= '<input id="' . $machine_name . '" name="' . $machine_name . '" type="text"/>';
        break;
    }
  }

  return $fields;
}
add_filter('comment_form_default_fields','dh_comment_fields');

function dh_comment_post($comment_id) {
  $field_options = dh_get_comment_fields_options();

  foreach ($field_options as $machine_name => $info) {
    if(isset($_POST[$machine_name]) && !in_array($machine_name, array('author', 'email'))) {
      $value = wp_filter_nohtml_kses($_POST[$machine_name]);
      add_comment_meta($comment_id, $machine_name, $value, false);
    }
  }
}
add_action('comment_post', 'dh_comment_post');

/**
 * HTML comment list class.
 *
 * @uses Walker_Comment
 */
class DH_Walker_Comment extends Walker_Comment {
  /**
   * @see Walker_Comment::start_lvl()
   */
  public function start_lvl( &$output, $depth = 0, $args = array() ) {
    $GLOBALS['comment_depth'] = $depth + 1;

    $output .= '<details>';
    $output .=   '<summary class="comments-meta in-reply" aria-controls="details-content-2" tabindex="0">';
    $output .=     '<span class="summary"> See all replies </span>';
    $output .=   '</summary>';
    $output .=   '<ul>';
  }

  /**
   * @see Walker_Comment::end_lvl()
   */
  public function end_lvl( &$output, $depth = 0, $args = array() ) {
    $GLOBALS['comment_depth'] = $depth + 1;

    $output .=   '</ul>';
    $output .= '</details>';
  }
}

function dh_colour_schemes() {
  return array(
    1 => array( 'primary' => '#39836E', 'secondary' => '#00AD93', 'label' => 'Green' ),
    2 => array( 'primary' => '#1B71A3', 'secondary' => '#2B8CC4', 'label' => 'Blue' ),
    3 => array( 'primary' => '#7C2374', 'secondary' => '#912B88', 'label' => 'Purple' ),
    4 => array( 'primary' => '#C55228', 'secondary' => '#F47738', 'label' => 'Orange' ),
    5 => array( 'primary' => '#CA272B', 'secondary' => '#DF3034', 'label' => 'Red' ),
  );
}

function _dh_prep_breads( $args = array() ) {
  static $menu_id_slugs = array();

  $defaults = array( 'menu' => '', 'container' => 'div', 'container_class' => '', 'container_id' => '', 'menu_class' => 'menu', 'menu_id' => '',
      'echo' => true, 'fallback_cb' => 'wp_page_menu', 'before' => '', 'after' => '', 'link_before' => '', 'link_after' => '', 'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
      'depth' => 0, 'walker' => '', 'theme_location' => '' );

  $args = wp_parse_args( $args, $defaults );
  /**
   * Filter the arguments used to display a navigation menu.
   *
   * @since 3.0.0
   *
   * @see wp_nav_menu()
   *
   * @param array $args Array of wp_nav_menu() arguments.
  */
  $args = apply_filters( 'wp_nav_menu_args', $args );
  $args = (object) $args;

  /**
   * Filter whether to short-circuit the wp_nav_menu() output.
   *
   * Returning a non-null value to the filter will short-circuit
   * wp_nav_menu(), echoing that value if $args->echo is true,
   * returning that value otherwise.
   *
   * @since 3.9.0
   *
   * @see wp_nav_menu()
   *
   * @param string|null $output Nav menu output to short-circuit with. Default null.
   * @param object      $args   An object containing wp_nav_menu() arguments.
   */
  $nav_menu = apply_filters( 'pre_wp_nav_menu', null, $args );

  if ( !empty( $nav_menu )) {
    if ( $args->echo ) {
      echo $nav_menu;
      return;
    }

    return $nav_menu;
  }

  // Get the nav menu based on the requested menu
  $menu = wp_get_nav_menu_object( $args->menu );

  // Get the nav menu based on the theme_location
  if ( ! $menu && $args->theme_location && ( $locations = get_nav_menu_locations() ) && isset( $locations[ $args->theme_location ] ) )
    $menu = wp_get_nav_menu_object( $locations[ $args->theme_location ] );

  // get the first menu that has items if we still can't find a menu
  if ( ! $menu && !$args->theme_location ) {
    $menus = wp_get_nav_menus( array( 'orderby' => 'name' ) );
    foreach ( $menus as $menu_maybe ) {
      if ( $menu_items = wp_get_nav_menu_items( $menu_maybe->term_id, array( 'update_post_term_cache' => false ) ) ) {
        $menu = $menu_maybe;
        break;
      }
    }
  }

  // If the menu exists, get its items.
  if ( $menu && ! is_wp_error($menu) && !isset($menu_items) )
    $menu_items = wp_get_nav_menu_items( $menu->term_id, array( 'update_post_term_cache' => false ) );

  /*
   * If no menu was found:
   *  - Fall back (if one was specified), or bail.
   *
   * If no menu items were found:
   *  - Fall back, but only if no theme location was specified.
   *  - Otherwise, bail.
   */
  if ( ( !$menu || is_wp_error($menu) || ( isset($menu_items) && empty($menu_items) && !$args->theme_location ) )
    && $args->fallback_cb && is_callable( $args->fallback_cb ) )
      return call_user_func( $args->fallback_cb, (array) $args );

  if ( ! $menu || is_wp_error( $menu ) )
    return false;

  $nav_menu = $items = '';

  $show_container = false;
  if ( $args->container ) {
    /**
     * Filter the list of HTML tags that are valid for use as menu containers.
     *
     * @since 3.0.0
     *
     * @param array $tags The acceptable HTML tags for use as menu containers.
     *                    Default is array containing 'div' and 'nav'.
     */
    $allowed_tags = apply_filters( 'wp_nav_menu_container_allowedtags', array( 'div', 'nav' ) );
    if ( in_array( $args->container, $allowed_tags ) ) {
      $show_container = true;
      $class = $args->container_class ? ' class="' . esc_attr( $args->container_class ) . '"' : ' class="menu-'. $menu->slug .'-container"';
      $id = $args->container_id ? ' id="' . esc_attr( $args->container_id ) . '"' : '';
      $nav_menu .= '<'. $args->container . $id . $class . '>';
    }
  }

  // Set up the $menu_item variables
  _wp_menu_item_classes_by_context( $menu_items );

  $sorted_menu_items = $menu_items_with_children = array();
  foreach ( (array) $menu_items as $menu_item ) {
    $sorted_menu_items[ $menu_item->menu_order ] = $menu_item;
    if ( $menu_item->menu_item_parent )
      $menu_items_with_children[ $menu_item->menu_item_parent ] = true;
  }

  // Add the menu-item-has-children class where applicable
  if ( $menu_items_with_children ) {
    foreach ( $sorted_menu_items as &$menu_item ) {
      if ( isset( $menu_items_with_children[ $menu_item->ID ] ) )
        $menu_item->classes[] = 'menu-item-has-children';
    }
  }

  unset( $menu_items, $menu_item );

  /**
   * Filter the sorted list of menu item objects before generating the menu's HTML.
   *
   * @since 3.1.0
   *
   * @param array  $sorted_menu_items The menu items, sorted by each menu item's menu order.
   * @param object $args              An object containing wp_nav_menu() arguments.
  */
  $sorted_menu_items = apply_filters( 'wp_nav_menu_objects', $sorted_menu_items, $args );

  return $sorted_menu_items;
}

/**
 * Build breadcrmbs
 */
function dh_breadcrumbs() {
  if ( ! is_front_page() ) {
    $crumbs = array();

    $current_page = get_queried_object();
    if ( empty( $current_page ) ) {
      return;
    }
    /*) == 'WP_Post' ) {
      foreach ( wp_get_post_tags( get_post()->ID ) as $post_tag ) {
        $current_post_tags[] = $post_tag->term_id;
      }
    }*/
    if ( in_array( $current_page->post_type, array( 'recommendation', 'question', 'event' ) ) /*check if recommendation or question or event*/) {
      $labels = array('recommendation' => 'Recommendations', 'question' => 'Questions', 'event' => 'Events');
      $crumbs[] = '<a href="' . get_site_url() . '/' . $current_page->post_type . '">' . $labels[$current_page->post_type] . '</a>';
      $crumbs[] = esc_html( $current_page->post_title );
    }
    else {
      if ( $current_page->post_title ) {
        $crumbs[] = esc_html( $current_page->post_title );
        $page_title_blank = FALSE;
      }
      else {
        $page_title_blank = TRUE;
      }

      $menu_items = _dh_prep_breads( array( 'theme_location' => 'primary', 'menu_class' => 'main-menu', 'container' => FALSE, 'walker' => new DH_Walker_Nav_Menu(), 'echo' => FALSE ) );

      // Get the current element
      if (is_array($menu_items)) {
        foreach ( $menu_items as $menu_item ) {
          if ( $menu_item->current ) {
            if ( _dh_object_id( $current_page ) == $menu_item->object_id ) {
              if ( $page_title_blank ) {
                $crumbs[] = esc_html( $menu_item->title );
              }
            }
            else {
              array_unshift($crumbs, '<a href="' . $menu_item->url . '">' . esc_html( $menu_item->title ) . '</a>');
            }
            $menu_item_parent = $menu_item->menu_item_parent;
            while ( $menu_item_parent ) {
              // Find the parent item in the items we already loaded
              foreach ( $menu_items as $menu_item_again ) {
                if ( $menu_item_again->ID == $menu_item_parent ) {
                  // Add this to the beginning of the crumbs
                  array_unshift($crumbs, '<a href="' . $menu_item_again->url . '">' . esc_html( $menu_item_again->title ) . '</a>');
                  $menu_item_parent = $menu_item_again->menu_item_parent;
                  break; // The inner foreach
                }
              }
            }

            break;// We've got the crumbs, break the outer foreach!
          }
        }
      }
    }

    // Now add 'Home' to the crumbs
    if ( count($crumbs) > 0 ) {
      array_unshift($crumbs, '<a href="' . get_site_url() . '">Home</a>');
    }
  }

  if ( count($crumbs) > 0 ) {
    echo '<div class="breadcrumbs">';
    echo   '<ul>';
    foreach ( $crumbs as $crumb ) {
      echo '<li>' . $crumb . '</li>';
    }
    echo   '</ul>';
    echo '</div>';
  }
}

function _dh_object_id( $object ) {
  if ( ! empty( $object->ID ) ) {
    return $object->ID;
  }
  elseif ( ! empty( $object->term_id ) ) {
    return $object->term_id;
  }

  return FALSE;
}

/**
 * Make sure the newletter table is created
 * Make sure the contact-us and report-comment pages are created
 */
function dh_install() {
  global $wpdb;

  $table_name = $wpdb->prefix . "dh_newsletter_subscriptions";

  /*
   * We'll set the default character set and collation for this table.
  * If we don't do this, some characters could end up being converted
  * to just ?'s when saved in our table.
  */
  $charset_collate = '';

  if ( ! empty( $wpdb->charset ) ) {
    $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
  }

  if ( ! empty( $wpdb->collate ) ) {
    $charset_collate .= " COLLATE {$wpdb->collate}";
  }

  $sql = "CREATE TABLE IF NOT EXISTS $table_name (
  id mediumint(9) NOT NULL AUTO_INCREMENT,
  time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
  email varchar(500) NOT NULL,
  status integer DEFAULT 1 NOT NULL,
  UNIQUE KEY id (id)
  ) $charset_collate;";

  require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
  dbDelta( $sql );

  global $user_ID;

  // Create constant forms that should always be there
  // (If they are ever deleted, they will be re-created)
  $form_titles = array( 'Contact us', 'Report a comment' );

  if ( is_plugin_active( 'contact-form-7/wp-contact-form-7.php' ) ) {
    foreach ($form_titles as $form_title) {
      $machine_name = strtolower( str_replace( ' ', '-', $form_title ) );

      $form_id = get_option( 'dh_form_' . $machine_name, 0 );

      if ( ! $form_id || ! get_post( $form_id ) ) {
        $form = array();

        $form['post_type']    = 'wpcf7_contact_form';
        $form['post_parent']  = 0;
        $form['post_author']  = $user_ID;
        $form['post_status']  = 'publish';
        $form['post_title']   = $form_title;
        $form['comment_status'] = 'closed';
        $formid = wp_insert_post ($form);
        $form_meta = WPCF7_ContactForm::get_template();
          $properties = $form_meta->get_properties();
        if ( $machine_name == 'report-a-comment' ) {
          $properties['form'] = "<p>URL to comment<br />\n[text* comment-url id:comment-url] </p>\n\n" . $properties['form'];
        }
        add_post_meta($formid, 'form', $properties['form'] );
        add_post_meta($formid, 'mail', $properties['mail'] );
        add_post_meta($formid, 'mail_2', $properties['mail_2'] );
        add_post_meta($formid, 'messages', $properties['messages'] );

        update_option( 'dh_form_' . $machine_name, $formid );
      }
    }
    $page_titles = array( 'Contact us', 'Report a comment' );

    foreach ($page_titles as $page_title) {
      $machine_name = strtolower( str_replace( ' ', '-', $page_title ) );

      $page_id = get_option( 'dh_page_' . $machine_name, 0 );

      if ( ! $page_id || ! get_post( $page_id ) ) {
        $page = array();

        $page['post_type']    = 'page';
        $page['post_content'] = '[contact-form-7 id="' . get_option( 'dh_page_' . $machine_name, 0 ) . '" title="' . $page_title . '"]';
        $page['post_parent']  = 0;
        $page['post_author']  = $user_ID;
        $page['post_status']  = 'publish';
        $page['post_title']   = $page_title;
        $page['comment_status'] = 'closed';
        $pageid = wp_insert_post ($page);

        update_option( 'dh_page_' . $machine_name, $pageid );
      }
    }
  }

  // Now create that custom layout for categories
  $cl_id = get_option( 'dh_custom_layout', 0 );
  if ( ! $cl_id || ! get_post( $cl_id ) ) {
    $cl = array();

    $cl['post_type']    = 'custom_layout';
    $cl['post_parent']  = 0;
    $cl['post_author']  = $user_ID;
    $cl['post_status']  = 'publish';
    $cl['post_title']   = 'Categories: Frame Custom Layout';
    $cl['comment_status'] = 'closed';
    $clid = wp_insert_post ($cl);
    // @TODO Do we really need this? If so, change the widget name to DH: Category Content when implemented
    add_post_meta($clid, 'panels_data', unserialize('a:3:{s:7:"widgets";a:1:{i:0;a:1:{s:4:"info";a:4:{s:4:"grid";s:1:"0";s:4:"cell";s:1:"0";s:2:"id";s:1:"1";s:5:"class";s:26:"DH_Widget_Category_Content";}}}s:5:"grids";a:1:{i:0;a:1:{s:5:"cells";s:1:"1";}}s:10:"grid_cells";a:1:{i:0;a:2:{s:6:"weight";s:1:"1";s:4:"grid";s:1:"0";}}}') );

    update_option( 'dh_custom_layout', $clid );
  }
}

class DH_Newsletter {
  /**
   * Constructor
   */
  public function __construct() {
    if(isset($_GET['export']) && $_GET['export'] == 'yes') {
      header("Pragma: public");
      header("Expires: 0");
      header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
      header("Cache-Control: private", false);
      header( 'Content-Type: text/xml; charset=' . get_option( 'blog_charset' ), true );
      header( 'Content-Disposition: attachment; filename=newsletter-emails_' . date( 'Y-m-d-H-i-s' ) . '.csv' );
      header( 'Content-Description: File Transfer' );

      $this->generate_csv();
      exit;
    }

    // Add extra menu items for admins
    add_action('admin_menu', array($this, 'admin_menu'));

    // Create end-points
    add_filter('query_vars', array($this, 'query_vars'));
    add_action('parse_request', array($this, 'parse_request'));
  }

  /**
   * Add extra menu items for admins
   */
  public function admin_menu()
  {
    add_menu_page('DH Newsletter', 'DH Newsletter', 'manage_options', 'dh-newsletter-settings', array($this, 'download_report'));
  }

  /**
   * Allow for custom query variables
   */
  public function query_vars($query_vars)
  {
    $query_vars[] = 'download_report';
    return $query_vars;
  }

  /**
   * Parse the request
   */
  public function parse_request(&$wp)
  {
    if(array_key_exists('download_report', $wp->query_vars))
    {
      $this->download_report();
      exit;
    }
  }

  /**
   * Download report
   */
  public function download_report() {
    echo '<div class="wrap">';
    echo '<div id="icon-tools" class="icon32"></div>';
    echo '<h2>Download Report</h2>';
    //$url = site_url();

    echo '<p><a href="' . site_url() . '/wp-admin/admin.php?page=download_report&export=yes">Export the Subscribers</a>';
  }

  /**
   * Converting data to CSV
   */
  public function generate_csv() {
    global $wpdb;
    $table = $wpdb->prefix . 'dh_newsletter_subscriptions';

    $subs = $wpdb->get_results( "SELECT * FROM " . $table, ARRAY_A );

    $out = fopen('php://output', 'w');
    foreach ($subs as $sub) {
      fputcsv($out, $sub);
    }
    fclose($out);
  }
}

// Instantiate a singleton of this plugin
$dh_newsletter = new DH_Newsletter();



class DH_Comments_Exporter {
  /**
   * Constructor
   */
  public function __construct() {
    if(isset($_GET['export_comments']) && $_GET['export_comments'] == 'yes') {
      header("Pragma: public");
      header("Expires: 0");
      header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
      header("Cache-Control: private", false);
      header( 'Content-Type: text/xml; charset=' . get_option( 'blog_charset' ), true );
      header( 'Content-Disposition: attachment; filename=comments_' . date( 'Y-m-d-H-i-s' ) . '.csv' );
      header( 'Content-Description: File Transfer' );

      $this->generate_csv();
      exit;
    }

    // Add extra menu items for admins
    add_action('admin_menu', array($this, 'admin_menu'));

    // Create end-points
    add_filter('query_vars', array($this, 'query_vars'));
    add_action('parse_request', array($this, 'parse_request'));
  }

  /**
   * Add extra menu items for admins
   */
  public function admin_menu()
  {
    add_menu_page('DH Comments Export', 'DH Comments Export', 'manage_options', 'dh-comments-exporter-settings', array($this, 'download_report'));
  }

  /**
   * Allow for custom query variables
   */
  public function query_vars($query_vars)
  {
    $query_vars[] = 'download_report';
    return $query_vars;
  }

  /**
   * Parse the request
   */
  public function parse_request(&$wp)
  {
    if(array_key_exists('download_report', $wp->query_vars))
    {
      $this->download_report();
      exit;
    }
  }

  /**
   * Download report
   */
  public function download_report() {
    echo '<div class="wrap">';
    echo '<div id="icon-tools" class="icon32"></div>';
    echo '<h2>Download Report</h2>';
    //$url = site_url();

    echo '<p><a href="' . site_url() . '/wp-admin/admin.php?page=download_report&export_comments=yes">Export the comments</a>';
  }

  /**
   * Converting data to CSV
   */
  public function generate_csv() {
    global $wpdb;
    $table = $wpdb->prefix . 'comments';
    $meta_table = $wpdb->prefix . 'commentmeta';

    $comments = $wpdb->get_results( "SELECT * FROM " . $table . " ORDER BY comment_post_ID, comment_ID", ARRAY_A );

    $out = fopen('php://output', 'w');
    fputcsv($out, array('comment_id', 'comment_post_id', 'author', 'e-mail', 'date', 'content', 'status'));
    foreach ($comments as $comment) {
      $export_record = array();

      $export_record['comment_id']      = $comment['comment_ID'];
      $export_record['comment_post_id'] = $comment['comment_post_ID'];
      $export_record['author']          = $comment['comment_author'];
      $export_record['e_mail']          = $comment['comment_author_email'];
      $export_record['date']            = $comment['comment_date'];
      $export_record['content']         = $comment['comment_content'];
      $export_record['status']          = $comment['comment_approved'];

      $comment_meta = $wpdb->get_results( "SELECT * FROM " . $meta_table . " WHERE meta_key NOT LIKE '_wp_%' AND comment_id=" . $comment['comment_ID'], ARRAY_A );
      foreach ( $comment_meta as $comment_metum ) {
        $export_record[$comment_metum['meta_key']] = $comment_metum['meta_key'] . ':' . $comment_metum['meta_value'];
      }

      fputcsv($out, $export_record);
    }
    fclose($out);
  }
}

// Instantiate a singleton of this plugin
$dh_comments_exporter = new DH_Comments_Exporter();

function dh_save_post( $post_id ) {

  $the_post = get_post( $post_id );

  // Numbers saving + possible shifting
  if ( in_array( $the_post->post_type, array( 'recommendation', 'question' ) ) && _dh_get_post_status( $post_id ) == 'publish' ) {
    // Check if a DH number has been specified in the DH number field
    $post_meta = get_post_meta( $post_id );

    // Prepare the potential current and specified dh numbers for this post
    $user_specified_num = $post_meta['dh_number'][0];
    $current_dh_number = _dh_get_post_dh_num( $post_id, $the_post->post_type );

    if ( ! empty( $user_specified_num ) ) {
      // Check if the specified number is different from the number that may already be assigned to this post
      if ( $user_specified_num != $current_dh_number ) {
//         // If enabled
//         if ( get_theme_mod( 'dh_numbering_auto' ) ) {
//           // Shift all others >= $user_specified_num (We'll do so until we reach a gap, then no more shifting is needed)
//           $posts_to_shift = get_posts(array(
//             'post_type' => $the_post->post_type,
//             'meta_key' => 'dh_' . $the_post->post_type . '_num',
//             'meta_value' => $user_specified_num,
//             'meta_compare' => '>=',
//             'meta_type' => 'NUMERIC',
//             'orderby' => 'meta_value_num',
//             'order' => 'ASC',
//             'numberposts' => '-1',
//             'exclude' => array($post_id), // Skip the post being saved, so that it doesn't itself also get pushed
//           ));

//           $number_being_replaced = $user_specified_num;
//           foreach ($posts_to_shift as $post_to_shift) {
//             $number_of_shiftee = _dh_get_post_dh_num($post_to_shift->ID, $post_to_shift->post_type);

//             // If the number we're shifting to doesn't match the number of he post being shifted, then we've reached a gap, and there's no need for any further shifting
//             if ( $number_being_replaced != $number_of_shiftee ) {
//               break;
//             }

//             delete_post_meta($post_to_shift->ID, 'dh_' . $the_post->post_type . '_num');
//             delete_post_meta($post_to_shift->ID, 'dh_number');

//             add_post_meta( $post_to_shift->ID, 'dh_' . $the_post->post_type . '_num', $number_of_shiftee + 1 );
//             add_post_meta( $post_to_shift->ID, 'dh_number', $number_of_shiftee + 1 );

//             $number_being_replaced = $number_of_shiftee + 1;
//           }
//         }

        // Assign $user_specified_num to post
        delete_post_meta($the_post->ID, 'dh_' . $the_post->post_type . '_num');
        delete_post_meta($the_post->ID, 'dh_number');
        add_post_meta( $the_post->ID, 'dh_' . $the_post->post_type . '_num', $user_specified_num );
        add_post_meta( $the_post->ID, 'dh_number', $user_specified_num );
      }
    }
    else {
      if ( get_theme_mod( 'dh_numbering_auto' ) ) {
        // Get the new DH number
        $new_dh_number = dh_get_next_dh_number( $the_post->post_type );

        // Assign $user_specified_num to post
        delete_post_meta($the_post->ID, 'dh_' . $the_post->post_type . '_num');
        delete_post_meta($the_post->ID, 'dh_number');
        add_post_meta( $the_post->ID, 'dh_' . $the_post->post_type . '_num', $new_dh_number );
        add_post_meta( $the_post->ID, 'dh_number', $new_dh_number );
      }
    }
  }
}
add_action( 'save_post', 'dh_save_post' );

function dh_before_delete_post( $post_id ) {
  $the_post = get_post( $post_id );

  // Numbers saving + possible shifting
  if ( in_array( $the_post->post_type, array( 'recommendation', 'question' ) ) ) {
    // Get the post DH Number
    $delete_dh_number = _dh_get_post_dh_num( $post_id, $the_post->post_type );

    if ( get_theme_mod( 'dh_numbering_auto' ) && $delete_dh_number ) {
      // Load posts to shift down
      $posts_to_shift = get_posts(array(
        'post_type' => $the_post->post_type,
        'meta_key' => 'dh_' . $the_post->post_type . '_num',
        'meta_value' => $delete_dh_number,
        'meta_compare' => '>=',
        'meta_type' => 'NUMERIC',
        'orderby' => 'meta_value_num',
        'order' => 'ASC',
        'numberposts' => '-1',
        'exclude' => array($post_id), // Skip the post being saved, so that it doesn't itself also get pushed
      ));

      foreach ($posts_to_shift as $post_to_shift) {
        $number_of_shiftee = _dh_get_post_dh_num($post_to_shift->ID, $post_to_shift->post_type);

        delete_post_meta($post_to_shift->ID, 'dh_' . $the_post->post_type . '_num');
        delete_post_meta($post_to_shift->ID, 'dh_number');

        add_post_meta( $post_to_shift->ID, 'dh_' . $the_post->post_type . '_num', $number_of_shiftee - 1 );
        add_post_meta( $post_to_shift->ID, 'dh_number', $number_of_shiftee - 1 );
      }
    }
  }
}
add_action( 'before_delete_post', 'dh_before_delete_post' );

function _dh_get_post_status( $post_id ) {
  $the_post = get_post ( $post_id );
  if ( $the_post->post_status == 'inherit' ) {
    return _dh_get_post_status( $the_post->post_parent );
  }
  else {
    return $the_post->post_status;
  }
}

function _dh_get_post_dh_num( $post_id, $post_type ) {
  // "TRUE" is NOT the default value to fetch, it just means fetch the first value as a single value
  return get_post_meta( $post_id, 'dh_' . $post_type . '_num', TRUE );
}

function _dh_get_post_by_dh_num( $post_dh_num, $post_type ) {
  $posts_query = new WP_Query(
    array(
      'post_type' => $post_type,
      'meta_key' => 'dh_' . $post_type . '_num', 'meta_value' => $post_dh_num,
    )
  );
  $posts = $posts_query->get_posts();

  if ( ! empty( $posts[0] ) ) {
    return $posts[0];
  }
  else {
    return FALSE;
  }
}

/**
 * Get Previous 'n Next (PNN) by DH number
 *
 * @param $post_dh_num
 * @param $post_type
 * @return multitype:Ambigous <> Ambigous <number>
 */
function _dh_get_pnn_by_dh_num( $post_dh_num, $post_type ) {
  $return = array();

  $n_posts_query = new WP_Query(
    array(
      'post_type' => $post_type,
      'meta_key' => 'dh_' . $post_type . '_num', 'meta_value' => (int)$post_dh_num, 'meta_compare' => '>', 'meta_type' => 'UNSIGNED',
      'order' => 'ASC',
      'orderby' => 'meta_value',
    )
  );
  $n_posts = $n_posts_query->get_posts();

  if ( ! empty( $n_posts[0] ) ) {
    $return['next'] = $n_posts[0];
  }

  $p_posts_query = new WP_Query(
    array(
      'post_type' => $post_type,
      'meta_key' => 'dh_' . $post_type . '_num', 'meta_value' => $post_dh_num, 'meta_compare' => '<', 'meta_type' => 'UNSIGNED',
      'order' => 'DESC',
      'orderby' => 'meta_value',
    )
  );
  $p_posts = $p_posts_query->get_posts();

  if ( ! empty( $p_posts[0] ) ) {
    $return['prev'] = $p_posts[0];
  }

  return $return;
}

function _dh_comments_max_page() {
  global $wp_query;
  $max_page = NULL;

  if ( !is_singular() || !get_option('page_comments') )
    return;

  $page = get_query_var('cpage');

  $nextpage = intval($page) + 1;

  if ( empty($max_page) )
    $max_page = $wp_query->max_num_comment_pages;

  if ( empty($max_page) )
    $max_page = get_comment_pages_count();

  return $max_page;
}

//get_question_comments
function get_comments_page() {
  if ( isset( $_POST['post_id'] ) && $post = get_post( $_POST['post_id'] ) ) {
    // Get all the comments for the mentioned post
    $fetched_comments = get_comments( array( 'post_id' => $_POST['post_id'], 'status' => 'approve', 'order' => 'DESC', 'orderby' => 'ID' ) );

    // If a particular comment needs to be seen, there is some special preparation to be done
    if ( isset( $_POST['comment_id'] ) && get_comment( $_POST['comment_id'] ) && get_comment( $_POST['comment_id'] )->comment_post_ID == $_POST['post_id'] ) {
      // Find the top-level comment that needs to be displayed
      $top_level_comment_index = _dh_fetch_comment_top_level( $fetched_comments, $_POST['comment_id'] );

      // Modify the target page number
      if ( $top_level_comment_index ) {
        $_POST['page_num'] = floor( $top_level_comment_index / get_option( 'comments_per_page' ) ) + 1;
      }

      // Let's assume only the first page has been displayed so far
      $start_page = 2;
      // Get a client-defined value for start page
      if ( isset( $_POST['start_page'] ) && is_numeric( $_POST['start_page'] ) ) {
        $start_page = $_POST['start_page'];
      }
    }

    if ( isset( $_POST['page_num'] ) && is_numeric( $_POST['page_num'] ) ) {
      if ( ! isset( $start_page ) ) {
        // If there is no specific comment to be displayed, set the start page same as the target page
        $start_page = $_POST['page_num'];
      }

      $output = '';

      for ( $i = $start_page; $i <= $_POST['page_num']; $i++ ) {
        $output .= wp_list_comments( array(
          'walker'     => new DH_Walker_Comment,
          'style'      => 'ul',
          'callback'   => 'dh_comment',
          'short_ping' => true,
          'page'       => $i,
          'per_page'   => get_option( 'comments_per_page' ),
          'echo'       => FALSE,
        ), $fetched_comments );
      }
      echo json_encode( array( 'html' => $output, 'newPage' => $_POST['page_num'] ) );
    }

  }
  else {
    echo 'Invalid data sent';
  }
  // Use die() here to avoid the "0" at the end of wp-admin/admin-ajax.php
  die();
}

add_action( 'wp_ajax_get_comments_page', 'get_comments_page' );
add_action( 'wp_ajax_nopriv_get_comments_page', 'get_comments_page' );

//get_tag_posts
function get_tag_posts_page() {
  if ( isset( $_POST['tag_slug'] ) && $matching_tag = get_tags( array( 'slug' => $_POST['tag_slug'] ) ) ) {
    // Get all the comments for the mentioned post
    $fetched_posts = get_posts( array( 'tag' => $matching_tag[0]->slug, 'status' => 'approve', 'order' => 'DESC', 'orderby' => 'date', 'posts_per_page' => -1 ) );

    if ( isset( $_POST['page_num'] ) && is_numeric( $_POST['page_num'] ) ) {
      $target_page = $_POST['page_num'];

      ob_start();
      $count = ( $target_page - 1 ) * DH_POSTS_PER_BLOG_PAGE;
      while ( $count < $target_page * DH_POSTS_PER_BLOG_PAGE && ! empty( $fetched_posts[$count] ) ) {
        the_widget( 'DH_Widget_News_Spotlight', array( 'spotlight_post' => $fetched_posts[$count]->ID, 'width' => 1, 'force_render' => TRUE ) );

        $count++;
        if ( $count % 3 == 0 ) {
          echo '<div style="clear:both;"></div>';
        }
      }
      $output = ob_get_clean();
      echo json_encode( array( 'html' => $output, 'newPage' => $target_page ) );
    }

  }
  else {
    echo 'Invalid data sent';
  }
  // Use die() here to avoid the "0" at the end of wp-admin/admin-ajax.php
  die();
}

add_action( 'wp_ajax_get_tag_posts_page', 'get_tag_posts_page' );
add_action( 'wp_ajax_nopriv_get_tag_posts_page', 'get_tag_posts_page' );

function _dh_fetch_comment_top_level( $comments, $comment_id ) {
  foreach ( $comments as $index => $comment ) {
    if ( $comment->comment_ID == $comment_id ) {
      if ( ! $comment->comment_parent ) {
        return $index;
      }
      else {
        return _dh_fetch_comment_top_level( $comments, $comment->comment_parent );
      }
    }
  }

  return FALSE;
}

/**
 * Adds CPTs to the list of available pages for a static front page.
 *
 * @param  string $select Existing select list.
 * @return string
*/
function dh_add_custom_layout_to_front_page_dropdown( $select )
{
  if ( FALSE === strpos( $select, 'page_on_front' ) )
  {
    return $select;
  }

  $cpt_posts = get_posts(
      array(
          'post_type'      => 'custom_layout'
          ,   'nopaging'       => TRUE
          ,   'numberposts'    => -1
          ,   'order'          => 'ASC'
          ,   'orderby'        => 'title'
          ,   'posts_per_page' => -1
      )
  );

  if ( ! $cpt_posts ) // no posts found.
  {
    return $select;
  }

  $current = get_option( 'page_on_front', 0 );

  $options = walk_page_dropdown_tree(
      $cpt_posts
      ,   0
      ,    array(
          'depth'                 => 0
          ,  'child_of'              => 0
          ,  'selected'              => $current
          ,  'echo'                  => 0
          ,  'name'                  => 'page_on_front'
          ,  'id'                    => ''
          ,  'show_option_none'      => ''
          ,  'show_option_no_change' => ''
          ,  'option_none_value'     => ''
      )
  );

  return str_replace( '</select>', $options . '</select>', $select );
}
add_filter( 'wp_dropdown_pages', 'dh_add_custom_layout_to_front_page_dropdown', 10, 1 );



function enable_front_page_custom_layout( $query ) {
  if(empty($query->query_vars['post_type']) && 0 != $query->query_vars['page_id'])
    $query->query_vars['post_type'] = array( 'page', 'custom_layout' );
}
add_action( 'pre_get_posts', 'enable_front_page_custom_layout' );

function _dh_print_file_links() {
  $attached_files = array();
  $attached_files[] = get_field('file_upload1', get_post() );
  $attached_files[] = get_field('file_upload2', get_post() );
  $attached_files[] = get_field('file_upload3', get_post() );
  echo '<div class="section-row">';
  foreach ( $attached_files as $attached_file) {
    if ( ! $attached_file ) { continue; }
    if ( preg_match('/\/[^\/]+$/', $attached_file['url'], $matches) ) {
      preg_match('/[^\.]+$/', $matches[0], $file_type);
    }

    $file_size = filesize(get_attached_file($attached_file['id']));

    if ( $file_size / 1024 < 1024) {
      $size_text = round($file_size / 1024, 1) . 'KB';
    }
    else {
      $size_text = round($file_size / (1024 * 1024), 2) . 'MB';
    }

    $name_to_display = $attached_file['title'];
    if ( strlen($name_to_display) > 20 ) {
      $name_to_display = substr($name_to_display, 0, 20) . '...';
    }

    echo '<a href="' . $attached_file['url'] . '" class="download" title="' . esc_attr($attached_file['title']) . '">';
    echo   $name_to_display;
    echo '</a>';
    echo '<p class="download-size">' . strtoupper($file_type[0]) . ', ' . $size_text . '</p>';
  }
  echo '</div>';
}

function _dh_widget_image_control( $local_this, $instance ) {
  $instance = wp_parse_args(
    (array) $instance,
    array(
      'alt'          => '', // Legacy.
      'image'        => '', // Legacy URL field.
      'image_id'     => '',
      'image_size'   => 'full',
      'link'         => '',
      'link_classes' => '',
      'link_text'    => '',
      'new_window'   => '',
      'title'        => '',
      'text'         => '',
    )
  );

  $instance['image_id'] = absint( $instance['image_id'] );
  wp_enqueue_media();
  wp_enqueue_script( 'dh-customizer-ajax', get_template_directory_uri() . '/js/dh.customize.js', array( 'jquery', 'customize-controls', 'media-upload', 'thickbox' ), false, true );

  $button_class = array( 'button', 'button-hero', 'simple-image-widget-control-choose' );
  $image_id     = $instance['image_id'];

  ?>
  <style type="text/css">
    .widget .widget-inside .simple-image-widget-form .simple-image-widget-field.is-hidden {display: none;}
    .widget .widget-inside .simple-image-widget-form .simple-image-widget-control {border: 1px dashed #aaa;padding: 20px 0;text-align: center;}
    .widget .widget-inside .simple-image-widget-form .simple-image-widget-control.has-image {border: 1px dashed #aaa;padding: 10px;text-align: left;}
    .widget .widget-inside .simple-image-widget-form .simple-image-widget-control img {display: block;height: auto;margin-bottom: 10px;max-width: 100%;}
    .simple-image-widget-legacy-fields {margin-bottom: 1em;padding: 10px;background-color: #e0e0e0;border-radius: 3px;}
    .simple-image-widget-legacy-fields p:last-child {margin-bottom: 0;}
  </style>
  <div class="simple-image-widget-form">

    <?php
    do_action( 'simple_image_widget_form_before', $instance, $local_this->id_base );
    ?>

    <p class="simple-image-widget-control<?php echo ( $image_id ) ? ' has-image' : ''; ?>"
      data-title="<?php esc_attr_e( 'Choose an Image', 'simple-image-widget' ); ?>"
      data-update-text="<?php esc_attr_e( 'Update Image', 'simple-image-widget' ); ?>"
      data-target=".image-id">
      <?php
      if ( $image_id ) {
        echo wp_get_attachment_image( $image_id, 'medium', false );
        unset( $button_class[ array_search( 'button-hero', $button_class ) ] );
      }
      ?>
      <input type="hidden" name="<?php echo esc_attr( $local_this->get_field_name( 'image_id' ) ); ?>" id="<?php echo esc_attr( $local_this->get_field_id( 'image_id' ) ); ?>" value="<?php echo absint( $image_id ); ?>" class="image-id simple-image-widget-control-target">
      <a href="#" class="<?php echo esc_attr( join( ' ', $button_class ) ); ?>"><?php _e( 'Choose an Image', 'simple-image-widget' ); ?></a>
    </p>

    <?php
    do_action( 'simple_image_widget_form_after', $instance, $local_this->id_base );
    ?>

  </div><!-- /.simple-image-widget-form -->
  <?php
}

/**
 * Enabling ACF-field-date-time-picker messes up the CSS from the buttons from siteorigin-panels plugin
 * This CSS to fix it
 */
function dh_admin_theme_style() {
  wp_enqueue_style( 'dh-admin-theme', get_template_directory_uri() . '/css/fix-acf-date-n-panels.css', array() );
}
add_action('admin_enqueue_scripts', 'dh_admin_theme_style');

function _dh_prep_hier_taxonomy( $terms ) {
  $prepped = array();

  $pre_prepped = array();

  foreach ( $terms as $term ) {
    $pre_prepped[$term->term_id] = $term;
  }

  $children_struct = array();

  foreach ( $terms as $term ) {
    if ( $term->parent == 0 ) {
      $prepped[$term->term_id] = $term;
      $prepped[$term->term_id]->children = array();
    }
    else {
      $parent_id = $term->parent;
      while ( $pre_prepped[$parent_id]->parent != 0 ) {
        $parent_id = $pre_prepped[$parent_id]->parent;
      }
      $children_struct[$parent_id][$term->term_id] = $term;
    }
  }

  foreach ( $children_struct as $parent_id => $children ) {
    foreach ( $children as $child ) {
      $prepped[$parent_id]->children[$child->term_id] = $child;
    }
  }

  return $prepped;
}

/**
 * Inspired by post_categories_meta_box( $post, $box ) for disallow assigning posts to top level chapters
 */
function _dh_post_categories_meta_box_prevent_top_level( $post, $box ) {
  $defaults = array( 'taxonomy' => 'category' );
  if ( ! isset( $box['args'] ) || ! is_array( $box['args'] ) ) {
    $args = array();
  } else {
    $args = $box['args'];
  }
  $r = wp_parse_args( $args, $defaults );
  $tax_name = esc_attr( $r['taxonomy'] );
  $taxonomy = get_taxonomy( $r['taxonomy'] );
  ?>
	<div id="taxonomy-<?php echo $tax_name; ?>" class="categorydiv">
		<ul id="<?php echo $tax_name; ?>-tabs" class="category-tabs">
			<li class="tabs"><a href="#<?php echo $tax_name; ?>-all"><?php echo $taxonomy->labels->all_items; ?></a></li>
			<li class="hide-if-no-js"><a href="#<?php echo $tax_name; ?>-pop"><?php _e( 'Most Used' ); ?></a></li>
		</ul>

		<div id="<?php echo $tax_name; ?>-pop" class="tabs-panel" style="display: none;">
			<ul id="<?php echo $tax_name; ?>checklist-pop" class="categorychecklist form-no-clear" >
				<?php $popular_ids = wp_popular_terms_checklist( $tax_name ); ?>
			</ul>
		</div>

		<div id="<?php echo $tax_name; ?>-all" class="tabs-panel">
			<?php
            $name = ( $tax_name == 'category' ) ? 'post_category' : 'tax_input[' . $tax_name . ']';
            echo "<input type='hidden' name='{$name}[]' value='0' />"; // Allows for an empty term set to be sent. 0 is an invalid Term ID and will be ignored by empty() checks.
            ?>
			<ul id="<?php echo $tax_name; ?>checklist" data-wp-lists="list:<?php echo $tax_name; ?>" class="categorychecklist form-no-clear">
				<?php wp_terms_checklist( $post->ID, array( 'taxonomy' => $tax_name, 'popular_cats' => $popular_ids, 'walker' => new DH_Hier_Tax_Walker_Category_Checklist() ) ); ?>
			</ul>
		</div>
	<?php if ( current_user_can( $taxonomy->cap->edit_terms ) ) : ?>
			<div id="<?php echo $tax_name; ?>-adder" class="wp-hidden-children">
				<h4>
					<a id="<?php echo $tax_name; ?>-add-toggle" href="#<?php echo $tax_name; ?>-add" class="hide-if-no-js">
						<?php
							/* translators: %s: add new taxonomy label */
							printf( __( '+ %s' ), $taxonomy->labels->add_new_item );
						?>
					</a>
				</h4>
				<p id="<?php echo $tax_name; ?>-add" class="category-add wp-hidden-child">
					<label class="screen-reader-text" for="new<?php echo $tax_name; ?>"><?php echo $taxonomy->labels->add_new_item; ?></label>
					<input type="text" name="new<?php echo $tax_name; ?>" id="new<?php echo $tax_name; ?>" class="form-required form-input-tip" value="<?php echo esc_attr( $taxonomy->labels->new_item_name ); ?>" aria-required="true"/>
					<label class="screen-reader-text" for="new<?php echo $tax_name; ?>_parent">
						<?php echo $taxonomy->labels->parent_item_colon; ?>
					</label>
					<?php wp_dropdown_categories( array( 'taxonomy' => $tax_name, 'hide_empty' => 0, 'name' => 'new' . $tax_name . '_parent', 'orderby' => 'name', 'hierarchical' => 1, 'show_option_none' => '&mdash; ' . $taxonomy->labels->parent_item . ' &mdash;' ) ); ?>
					<input type="button" id="<?php echo $tax_name; ?>-add-submit" data-wp-lists="add:<?php echo $tax_name; ?>checklist:<?php echo $tax_name; ?>-add" class="button category-add-submit" value="<?php echo esc_attr( $taxonomy->labels->add_new_item ); ?>" />
					<?php wp_nonce_field( 'add-' . $tax_name, '_ajax_nonce-add-' . $tax_name, false ); ?>
					<span id="<?php echo $tax_name; ?>-ajax-response"></span>
				</p>
			</div>
		<?php endif; ?>
	</div>
	<?php
}

require_once(ABSPATH . 'wp-admin/includes/template.php');
/**
 * Walker to output an unordered list of category checkbox <input> elements.
 */
class DH_Hier_Tax_Walker_Category_Checklist extends Walker_Category_Checklist {
  /**
   * Start the element output.
   *
   * @see Walker::start_el()
   *
   * @since 2.5.1
   *
   * @param string $output   Passed by reference. Used to append additional content.
   * @param object $category The current term object.
   * @param int    $depth    Depth of the term in reference to parents. Default 0.
   * @param array  $args     An array of arguments. @see wp_terms_checklist()
   * @param int    $id       ID of the current term.
   */
  public function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {
    if ( empty( $args['taxonomy'] ) ) {
      $taxonomy = 'category';
    } else {
      $taxonomy = $args['taxonomy'];
    }

    if ( $taxonomy == 'category' ) {
      $name = 'post_category';
    } else {
      $name = 'tax_input[' . $taxonomy . ']';
    }
    $args['popular_cats'] = empty( $args['popular_cats'] ) ? array() : $args['popular_cats'];
    $class = in_array( $category->term_id, $args['popular_cats'] ) ? ' class="popular-category"' : '';

    $args['selected_cats'] = empty( $args['selected_cats'] ) ? array() : $args['selected_cats'];

    /** This filter is documented in wp-includes/category-template.php */
    $output .= "\n<li id='{$taxonomy}-{$category->term_id}'$class>" .
    '<label class="selectit">';
    if ( $category->parent != 0 ) {
      $output .= '<input value="' . $category->term_id . '" type="checkbox" name="'.$name.'[]" id="in-'.$taxonomy.'-' . $category->term_id . '"' .
                  checked( in_array( $category->term_id, $args['selected_cats'] ), true, false ) .
                  disabled( empty( $args['disabled'] ), false, false ) . ' /> ';
    }
    $output .= esc_html( apply_filters( 'the_category', $category->name ) ) . '</label>';
  }

  /**
   * Starts the list before the elements are added.
   *
   * @see Walker:start_lvl()
   *
   * @since 2.5.1
   *
   * @param string $output Passed by reference. Used to append additional content.
   * @param int    $depth  Depth of category. Used for tab indentation.
   * @param array  $args   An array of arguments. @see wp_terms_checklist()
   */
  public function start_lvl( &$output, $depth = 0, $args = array() ) {
    if ( $depth < 1 ) {
      $indent = str_repeat("\t", $depth);
      $output .= "$indent<ul class='11children'>\n";
    }
  }

  /**
   * Ends the list of after the elements are added.
   *
   * @see Walker::end_lvl()
   *
   * @since 2.5.1
   *
   * @param string $output Passed by reference. Used to append additional content.
   * @param int    $depth  Depth of category. Used for tab indentation.
   * @param array  $args   An array of arguments. @see wp_terms_checklist()
   */
  public function end_lvl( &$output, $depth = 0, $args = array() ) {
    if ( $depth < 1 ) {
      $indent = str_repeat("\t", $depth);
      $output .= "$indent</ul>\n";
    }
  }
}

function set_the_terms_in_order( $terms, $taxonomies, $args ) {
  if (empty($args['orderby']) || in_array($args['orderby'], array('term_id', 'id'))) {
    usort( $terms, '_dh_compare_term_id' );
  }
  return $terms;
}
add_filter( 'get_terms', 'set_the_terms_in_order' , 10, 4 );

function _dh_compare_term_id( $term_1, $term_2) {
  if ( $term_1->term_id == $term_2->term_id ) {
    return 0;
  }
  if ( $term_1->term_id > $term_2->term_id ) {
    return 1;
  }
  if ( $term_1->term_id < $term_2->term_id ) {
    return -1;
  }
}

function _dh_html_truncate($html_string, $length) {
  $length_physical = 0;
  $length_html = 0;

  $in_tag = FALSE;

  while ($length_html < $length && $length_physical <= strlen($html_string)) {
    $length_physical++;

    $char = $html_string[$length_physical - 1];
    if ($in_tag) {
      // we're inside a tag, check when it closes
      if ($char == '>') {
        $in_tag = FALSE;
      }
    }
    elseif ($char == '<') {
      // a tag has just started
      $in_tag = TRUE;
    }
    else {
      // we're in a tag, nor is one starting... so add 1 to the html length
      $length_html++;
    }
  }

  return substr($html_string, 0, $length_physical);
}

/**
 * Create HTML list of nav menu items.
 *
 * @since 3.0.0
 * @uses Walker
 */
class DH_Walker_Nav_Menu extends Walker_Nav_Menu {

  public function start_lvl( &$output, $depth = 0, $args = array() ) {
    $indent = str_repeat("\t", $depth);
    $output .= "\n$indent<ul class=\"main-menu sub-menu\">\n";
  }

  public function display_element( $element, &$children_elements, $max_depth, $depth, $args, &$output ) {

    if ( !$element )
      return;

    $id_field = $this->db_fields['id'];
    $id       = $element->$id_field;

    //display this element
    $this->has_children = ! empty( $children_elements[ $id ] );
    if ( isset( $args[0] ) && is_array( $args[0] ) ) {
      $args[0]['has_children'] = $this->has_children; // Backwards compatibility.
    }

    $cb_args = array_merge( array(&$output, $element, $depth), $args);
    call_user_func_array(array($this, 'start_el'), $cb_args);

    // descend only when the depth is right and there are childrens for this element
    if ( ($max_depth == 0 || $max_depth > $depth+1 ) && isset( $children_elements[$id]) && ( $element->current || $element->current_item_ancestor || $element->current_item_parent )) {

      foreach( $children_elements[ $id ] as $child ){

        if ( !isset($newlevel) ) {
          $newlevel = true;
          //start the child delimiter
          $cb_args = array_merge( array(&$output, $depth), $args);
          call_user_func_array(array($this, 'start_lvl'), $cb_args);
        }
        $this->display_element( $child, $children_elements, $max_depth, $depth + 1, $args, $output );
      }
    }
    if ( ! empty( $children_elements[ $id ] ) ) {
      unset( $children_elements[ $id ] );
    }

    if ( isset($newlevel) && $newlevel ){
      //end the child delimiter
      $cb_args = array_merge( array(&$output, $depth), $args);
      call_user_func_array(array($this, 'end_lvl'), $cb_args);
    }

    //end this element
    $cb_args = array_merge( array(&$output, $element, $depth), $args);
    call_user_func_array(array($this, 'end_el'), $cb_args);
  }

  public function walk( $elements, $max_depth) {

    $args = array_slice(func_get_args(), 2);
    $output = '';

    if ($max_depth < -1) //invalid parameter
      return $output;

    if (empty($elements)) //nothing to walk
      return $output;

    $parent_field = $this->db_fields['parent'];

    // flat display
    if ( -1 == $max_depth ) {
      $empty_array = array();
      foreach ( $elements as $e )
        $this->display_element( $e, $empty_array, 1, 0, $args, $output );
      return $output;
    }

    /*
     * Need to display in hierarchical order.
     * Separate elements into two buckets: top level and children elements.
     * Children_elements is two dimensional array, eg.
     * Children_elements[10][] contains all sub-elements whose parent is 10.
     */
    $top_level_elements = array();
    $children_elements  = array();
    foreach ( $elements as $e) {
      if ( 0 == $e->$parent_field )
        $top_level_elements[] = $e;
      else
        $children_elements[ $e->$parent_field ][] = $e;
    }

    /*
     * When none of the elements is top level.
     * Assume the first one must be root of the sub elements.
     */
    if ( empty($top_level_elements) ) {

      $first = array_slice( $elements, 0, 1 );
      $root = $first[0];

      $top_level_elements = array();
      $children_elements  = array();
      foreach ( $elements as $e) {
        if ( $root->$parent_field == $e->$parent_field )
          $top_level_elements[] = $e;
        else
          $children_elements[ $e->$parent_field ][] = $e;
      }
    }

    $children_elements_again = $children_elements;

    foreach ( $top_level_elements as $e )
      $this->display_element( $e, $children_elements, 1, 0, $args, $output );

    foreach ( $top_level_elements as $e ) {
      if ( ( $e->current || $e->current_item_ancestor || $e->current_item_parent ) && !empty( $children_elements_again[ $e->ID ] )  && count( $children_elements_again[ $e->ID ] ) > 0 ) {
        $cb_args = array_merge( array(&$output, 1), $args);
        call_user_func_array(array($this, 'end_lvl'), $cb_args);
        call_user_func_array(array($this, 'start_lvl'), $cb_args);
        foreach( $children_elements_again[ $e->ID ] as $child ) {
          $this->display_element( $child, $children_elements_again, $max_depth, 1, $args, $output );
          if ( ! empty( $args[0]->menu_class ) ) {
            $args[0]->menu_class .= ' main-with-sub-menu';
          }
        }
      }
    }

    /*
     * If we are displaying all levels, and remaining children_elements is not empty,
     * then we got orphans, which should be displayed regardless.
     */
    if ( ( $max_depth == 0 ) && count( $children_elements ) > 0 ) {
      $empty_array = array();
      foreach ( $children_elements as $orphans )
        foreach( $orphans as $op )
          $this->display_element( $op, $empty_array, 1, 0, $args, $output );
    }

    return $output;
  }

} // Walker_Nav_Menu

add_filter( 'wp_nav_menu_objects', 'dh_wp_nav_menu_objects', 10, 2 );
function dh_wp_nav_menu_objects($items, $args) {
  // First of all, make sure no other menu item is active
  foreach ( $items as $item ) {
    if ( $item->current ) {
      return $items;
    }
  }

  // Now create an array of the tags on the current post
  $current_post_tags = array();
  if ( is_object( get_queried_object() ) && get_class( get_queried_object() ) == 'WP_Post' ) {
    foreach ( wp_get_post_tags( get_post()->ID ) as $post_tag ) {
      $current_post_tags[] = $post_tag->term_id;
    }
  }

  $activate = FALSE;

  // Check if there's a menu item to be made active
  foreach ( $items as $index => $item ) {
    if ( count( $current_post_tags ) > 0 && $item->type == 'taxonomy' && in_array( $item->object_id, $current_post_tags ) ) {
      $activate = TRUE;
    }

    if ( $activate ) {
      // Make this item active
      $items[$index]->current = TRUE;
      $items[$index]->classes[] = 'current-menu-item';

      // Make this item's ancestors active
      $parent_item = $item->menu_item_parent;
      $met_the_parent = FALSE;
      while ( $parent_item > 0 ) {
        foreach ( $items as $sub_index => $sub_item ) {
          if ( $sub_item->ID == $parent_item ) {
            $items[$sub_index]->current_item_ancestor = TRUE;
            $items[$sub_index]->classes[] = 'current-menu-ancestor';
            if ( ! $met_the_parent ) {
              $items[$sub_index]->current_item_parent = TRUE;
              $items[$sub_index]->classes[] = 'current-menu-parent';
              $met_the_parent = TRUE;
            }

            $parent_item = $sub_item->menu_item_parent;
            continue;
          }
        }
      }

      // And then break (to avoid having multiple active items in the menu)
      break;
    }
  }

  return $items;
}

/**
 * WordPress doesn't have any way of fetching comments filtered by a category.
 *
 * This code is inspired from http://wordpress.stackexchange.com/questions/83577/get-recent-comments-of-a-particular-category
 */
function _dh_get_comments_by_category($term, $comment_count) {
  global $wpdb;

  // fetch posts in all those categories
  $posts = get_objects_in_term( array($term->term_id), $term->taxonomy );

  $sql = "SELECT comment_ID, comment_date, comment_content, comment_post_ID
  FROM {$wpdb->comments} WHERE
  comment_post_ID in (".implode(',', $posts).") AND comment_approved = 1
  ORDER by comment_date DESC LIMIT $comment_count";

  $comments_list = $wpdb->get_results( $sql );

  return $comments_list;
}

function _dh_redirect_from_all_to_category() {
  $count = 0;

  $potential_target_term = FALSE;

  foreach (array('topic' => 'themes', 'organisation' => 'organisations', 'chapter' => 'chapters') as $taxonomy => $taxonomy_label) {
    if (is_array($_GET[$taxonomy_label])) {
      if (count($_GET[$taxonomy_label]) == 1) {
        $potential_target_term =get_term($_GET[$taxonomy_label][0], $taxonomy);
      }
      $count += count($_GET[$taxonomy_label]);
    }
  }

  if ( $count == 1 && $potential_target_term ) {
    header('Location:' . get_term_link($potential_target_term));
    exit;
  }
}

/**
 * @TODO @FIXME !
 * @param unknown $query
 * @return unknown
 */
function dh_searchfilter($query) {

  if ($query->is_search && !is_admin() ) {
    // We do this to prevent custom layouts from showing up in the search, as they are still not made to render properly
    // @TODO Make them render properly (or filter them out more accurately?!)
    $query->set('post_type',array('post','page', 'event', 'question', 'recommendation'));
  }

  return $query;
}
add_filter('pre_get_posts','dh_searchfilter');

function dh_add_dh_number_column_filter($columns) {
  $columns['dh_dh_number'] = "DH-Number";
  return $columns;
}
function dh_add_dh_number_sortable_column_filter($columns) {
  $columns['dh_dh_number'] = "DH-Number";
  return $columns;
}
function dh_add_dh_number_column_action($column_name, $post_id) {
  if($column_name == 'dh_dh_number') {
    $post = get_post($post_id);
    echo _dh_get_post_dh_num($post_id, $post->post_type);
  }
}
add_filter( 'manage_edit-recommendation_columns'         , 'dh_add_dh_number_column_filter' );
add_filter( 'manage_edit-recommendation_sortable_columns', 'dh_add_dh_number_sortable_column_filter' );
add_action( 'manage_recommendation_posts_custom_column'  , 'dh_add_dh_number_column_action', 20, 2 );
add_filter( 'manage_edit-question_columns'               , 'dh_add_dh_number_column_filter' );
add_filter( 'manage_edit-question_sortable_columns'      , 'dh_add_dh_number_sortable_column_filter' );
add_action( 'manage_question_posts_custom_column'        , 'dh_add_dh_number_column_action', 20, 2 );

function dh_acf_create_field($field, $post_id) {
  if ( $field['key'] == 'field_54c65c4e42521' ) {
    if ( get_theme_mod( 'dh_numbering_auto' ) ) {
      $post = get_post($post_id);
      echo '<script>jQuery("#acf-field-dh_number").prop("disabled", true);</script>';
      echo '<p>Auto-numbering is enabled.</p>';

      if ( empty( $field['value'] ) ) {
        $next_dh_number = dh_get_next_dh_number($post->post_type);
        echo '<p>A number will automatically be assigned to this post when saved (tentatively, ' . $next_dh_number . ' will be used).</p>';
      }
    }
  }
}
add_filter('acf/create_field', 'dh_acf_create_field', 11, 2);

function dh_get_next_dh_number($type) {
  $last_dh_number_b4_gap = 0;

  $posts = get_posts(array(
    'post_type' => $type,
    'meta_key' => 'dh_' . $type . '_num',
    'meta_value' => $last_dh_number_b4_gap,
    'meta_compare' => '>=',
    'meta_type' => 'NUMERIC',
    'orderby' => 'meta_value_num',
    'order' => 'ASC',
    'numberposts' => '-1',
  ));

  foreach ($posts as $post) {
    $number_of_post = _dh_get_post_dh_num($post->ID, $post->post_type);

    if ( $number_of_post - $last_dh_number_b4_gap <= 1) {
      // No gap (yet)
      $last_dh_number_b4_gap = $number_of_post;
    }
    else {
      // We came across a gap
      break;
    }
  }

  return $last_dh_number_b4_gap + 1;
}