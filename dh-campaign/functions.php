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

/**
 * upadtes images styles craeted in parent theme with sizes relevant to this child theme also introduces a new style
 *
 */
function dh_campaign_init_image_styles() {
    // update the image style sizes
    update_option( 'medium_size_h', 700 );
    update_option( 'medium_size_w', 960 );
    update_option( 'medium_crop', 0 );
    update_option( 'large_size_h', 960 );
    update_option( 'large_size_w', 960 );
    update_option( 'large_crop', 0 );

    // add new image style not available in the parent theme
    add_image_size( 'dh-short', 450, 960, FALSE );
}

// The call back to update the image sizes needs to be called after everything else to make sure it's not overridden.
add_action( 'init', 'dh_campaign_init_image_styles', 100 );

/**
 * function will change the slugs of the parent post type 'custom_layout' and allow the theme to work correctly with the new permalinks
 *
 */
function dh_campaign_custom_pre_get_posts( $query ) {
    $args		 = get_post_type_object( "custom_layout" );
    $args->rewrite	 = array( 'slug' => '/', 'with_front' => false );
    register_post_type( $args->name, $args );
    // Only noop the main query
    if ( ! $query->is_main_query() )
	return;

    // Only noop our very specific rewrite rule match
    if ( 2 != count( $query->query ) || ! isset( $query->query[ 'page' ] ) ) {
	return;
    }

    // 'name' will be set if post permalinks are just post_name, otherwise the page rule will match
    if ( ! empty( $query->query[ 'name' ] ) ) {
	$query->set( 'post_type', array( 'custom_layout' ) );
    }
}

add_action( 'pre_get_posts', 'dh_campaign_custom_pre_get_posts' );

/**
 * function to run when this child theme is loaded
 *
 */
function dh_campaign_init() {

    // Removing all non-DH campaign post types added by the parent theme
    global $wp_post_types;

    $post_types_to_remove = array( 'event', 'question', 'recommendation', 'news', 'page', 'post', 'comments' );

    foreach ( $post_types_to_remove as $post_type_to_remove ) {
	//remove_post_type_support( $post_type_to_remove );
	unset( $wp_post_types[ $post_type_to_remove ] );
    }

    // Register a new post type to hold custom text (using the wysiwyg editor which is not available using sitebuilder)
    register_post_type( 'custom_text', array(
	'labels'		 => array(
	    'name'		 => __( 'Custom Text' ),
	    'singular_name'	 => __( 'Custom text' ),
	),
	'public'		 => TRUE,
	'has_archive'		 => TRUE,
	'menu_icon'		 => 'dashicons-tagcloud',
	'description'		 => 'DH Campaign post type to build text used in custom layout widgets',
	'show_in_nav_menus'	 => true,
	'supports'		 => array( 'title', 'editor' ),
    ) );


    // Check if the menu exists
  $menu_name	 = 'Main menu';
  $menu_exists	 = wp_get_nav_menu_object( $menu_name );

    // If it doesn't exist, let's create it.
  if ( ! $menu_exists ) {
    $menu_id = wp_create_nav_menu( $menu_name );

    // Set up default menu items
	wp_update_nav_menu_item( $menu_id, 0, array(
	    'menu-item-title'	 => __( 'Home' ),
	    'menu-item-classes'	 => 'home',
	    'menu-item-url'		 => home_url( '/' ),
	    'menu-item-status'	 => 'publish' ) );
    }
    
  // Check if the footer menu exists
  $menu_name	 = 'Footer menu';
  $menu_exists	 = wp_get_nav_menu_object( $menu_name );
  
  // If it doesn't exist, let's create it.
    if ( ! $menu_exists ) {
      wp_create_nav_menu( $menu_name );
    }
    
    //@TODO - in phase 2: add code to auto select the main menu for new micro sites
  if ( ! wp_get_nav_menu_object( 'primary' ) ) {
    wp_update_nav_menu_object( 0, array( 'primary' => $menu_name ) );
  }

    unregister_nav_menu( 'secondary' );
    
    //add the footer menu location
    register_nav_menu('footer',__( 'Footer menu' ));

    dh_campaign_set_custom_layout_homepage();
}

add_action( 'init', 'dh_campaign_init', 100 );

/**
 * function used as a workaround to setting a custom_layout post as the campaign sites 'homepage' which looks at an acf field on each custom layout post
 *
 */
function dh_campaign_set_custom_layout_homepage() {

    //the homepage is stored as an option and is not always computed for better performance
    $default		 = array( 'value' => NULL, 'expire' => 0 );
    $dh_campaign_homepage	 = get_option( 'dh_campaign_homepage', array( 'value' => NULL, 'expire' => 0 ) );

    // Ignore fetched value if it's expired.
    if ( $dh_campaign_homepage[ 'expire' ] < time() ) {
	$dh_campaign_homepage = $default;
    }

    // args to find 
    if ( $dh_campaign_homepage[ 'value' ] != get_option( 'page_on_front' ) ) {
	// args
	$args = array(
	    'numberposts'	 => -1,
	    'post_type'	 => 'custom_layout',
	    'meta_key'	 => 'dh_campaign_set_this_as_front_page',
	    'meta_value'	 => 'Yes'
	);

	// query
	$the_query = new WP_Query( $args );

	if ( $the_query->have_posts() ) {
	    while ( $the_query->have_posts() ) {
		$current_post			 = $the_query->the_post();
		$dh_campaign_homepage[ 'value' ] = get_the_ID();
	    }
	}

	if ( $dh_campaign_homepage[ 'value' ] ) {
	    update_option( 'page_on_front', $dh_campaign_homepage[ 'value' ] );
	    update_option( 'show_on_front', 'page' );
	    $dh_campaign_homepage[ 'expire' ] = time() + 3600;
	    update_option( 'dh_campaign_homepage', $dh_campaign_homepage );
	}
	wp_reset_query();  // Restore global post data stomped by the_post().
    }
}

/**
 * Register widgets and widget areas.
 */
function dh_campaign_widgets_init() {
    require get_stylesheet_directory() . '/inc/widgets/super_widget.php';
    $widgets = array(
	'Widget_Spotlight',
	'Widget_Spotlight_spacer',
	'Widget_Spotlight_img_vid',
	'Widget_Spotlight_img_text',
	'Widget_Spotlight_data_capture',
	'Widget_Spotlight_custom_text',
	'Widget_Spotlight_quote_text',
	'Widget_Spotlight_infographic_img',
    );

    foreach ( $widgets as $widget ) {
	require get_stylesheet_directory() . '/inc/widgets/' . strtolower( $widget ) . '.php';
	register_widget( 'DH_Campaign_' . $widget );
    }
}

add_action( 'widgets_init', 'dh_campaign_widgets_init', 100 );

/**
 * Correctly enqueuing or dequeue java scripts / style sheets
 *
 */
function dh_campaign_scripts() {
    wp_enqueue_script( 'vendor', get_stylesheet_directory_uri() . '/scripts/vendor.js' );
    wp_enqueue_script( 'main', get_stylesheet_directory_uri() . '/scripts/main.js' );
    
    // Remove colour style sheet from parent theme using the handle
    wp_dequeue_style( 'dh-colour-style' );
    wp_deregister_style( 'dh-colour-style' );
}

add_action( 'wp_enqueue_scripts', 'dh_campaign_scripts', 100 );


/**
 * Create HTML list of nav menu items.
 *
 * @since 3.0.0
 * @uses Walker
 */
class DH_Campaign_Walker_Nav_Menu extends Walker_Nav_Menu {

    public function start_lvl( &$output, $depth = 0, $args = array() ) {
	$indent = str_repeat( "\t", $depth );
	$output .= "\n$indent<ul class=\"main-menu sub-menu\">\n";
    }

    public function display_element( $element, &$children_elements, $max_depth, $depth, $args, &$output ) {

	if ( ! $element )
	    return;

	$id_field	 = $this->db_fields[ 'id' ];
	$id		 = $element->$id_field;

	//display this element
	$this->has_children = ! empty( $children_elements[ $id ] );
	if ( isset( $args[ 0 ] ) && is_array( $args[ 0 ] ) ) {
	    $args[ 0 ][ 'has_children' ] = $this->has_children; // Backwards compatibility.
	}

	$cb_args = array_merge( array( &$output, $element, $depth ), $args );
	call_user_func_array( array( $this, 'start_el' ), $cb_args );

	// descend only when the depth is right and there are childrens for this element
	if ( ($max_depth == 0 || $max_depth > $depth + 1 ) && isset( $children_elements[ $id ] ) && ( $element->current || $element->current_item_ancestor || $element->current_item_parent ) ) {

	    foreach ( $children_elements[ $id ] as $child ) {

		if ( ! isset( $newlevel ) ) {
		    $newlevel	 = true;
		    //start the child delimiter
		    $cb_args	 = array_merge( array( &$output, $depth ), $args );
		    call_user_func_array( array( $this, 'start_lvl' ), $cb_args );
		}
		$this->display_element( $child, $children_elements, $max_depth, $depth + 1, $args, $output );
	    }
	}
	if ( ! empty( $children_elements[ $id ] ) ) {
	    unset( $children_elements[ $id ] );
	}

	if ( isset( $newlevel ) && $newlevel ) {
	    //end the child delimiter
	    $cb_args = array_merge( array( &$output, $depth ), $args );
	    call_user_func_array( array( $this, 'end_lvl' ), $cb_args );
	}

	//end this element
	$cb_args = array_merge( array( &$output, $element, $depth ), $args );
	call_user_func_array( array( $this, 'end_el' ), $cb_args );
    }

    public function walk( $elements, $max_depth ) {

	$args	 = array_slice( func_get_args(), 2 );
	$output	 = '';

	if ( $max_depth < -1 ) //invalid parameter
	    return $output;

	if ( empty( $elements ) ) //nothing to walk
	    return $output;

	$parent_field = $this->db_fields[ 'parent' ];

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
	$top_level_elements	 = array();
	$children_elements	 = array();
	foreach ( $elements as $e ) {
	    if ( 0 == $e->$parent_field )
		$top_level_elements[]				 = $e;
	    else
		$children_elements[ $e->$parent_field ][]	 = $e;
	}

	/*
	 * When none of the elements is top level.
	 * Assume the first one must be root of the sub elements.
	 */
	if ( empty( $top_level_elements ) ) {

	    $first	 = array_slice( $elements, 0, 1 );
	    $root	 = $first[ 0 ];

	    $top_level_elements	 = array();
	    $children_elements	 = array();
	    foreach ( $elements as $e ) {
		if ( $root->$parent_field == $e->$parent_field )
		    $top_level_elements[]				 = $e;
		else
		    $children_elements[ $e->$parent_field ][]	 = $e;
	    }
	}

	$children_elements_again = $children_elements;

	foreach ( $top_level_elements as $e )
	    $this->display_element( $e, $children_elements, 1, 0, $args, $output );

	foreach ( $top_level_elements as $e ) {
	    if ( ( $e->current || $e->current_item_ancestor || $e->current_item_parent ) && ! empty( $children_elements_again[ $e->ID ] ) && count( $children_elements_again[ $e->ID ] ) > 0 ) {
		$cb_args = array_merge( array( &$output, 1 ), $args );
		call_user_func_array( array( $this, 'end_lvl' ), $cb_args );
		call_user_func_array( array( $this, 'start_lvl' ), $cb_args );
		foreach ( $children_elements_again[ $e->ID ] as $child ) {
		    $this->display_element( $child, $children_elements_again, $max_depth, 1, $args, $output );
		    if ( ! empty( $args[ 0 ]->menu_class ) ) {
			$args[ 0 ]->menu_class .= ' main-with-sub-menu';
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
		foreach ( $orphans as $op )
		    $this->display_element( $op, $empty_array, 1, 0, $args, $output );
	}

	return $output;
    }

}

/**
 * (Inspired/copied-and-modified from siteorigin_panels plugin's siteorigin_panels_render())
 * Render the panels
 *
 * @param int|string|bool $post_id The Post ID or 'home'.
 * @param bool $enqueue_css Should we also enqueue the layout CSS.
 * @param array|bool $panels_data Existing panels data. By default load from settings or post meta.
 * @return string
 */
function _dh_campaign_siteorigin_panels_render( $post_id = false, $enqueue_css = true, $panels_data = false, $layout_width = 3 ) {
    if ( empty( $post_id ) )
	$post_id = get_the_ID();

    global $siteorigin_panels_current_post;
    $old_current_post		 = $siteorigin_panels_current_post;
    $siteorigin_panels_current_post	 = $post_id;

    // Try get the cached panel from in memory cache.
    global $siteorigin_panels_cache;
    /* if(!empty($siteorigin_panels_cache) && !empty($siteorigin_panels_cache[$post_id]))
      return $siteorigin_panels_cache[$post_id]; */

    if ( empty( $panels_data ) ) {
	if ( strpos( $post_id, 'prebuilt:' ) === 0 ) {
	    list($null, $prebuilt_id) = explode( ':', $post_id, 2 );
	    $layouts	 = apply_filters( 'siteorigin_panels_prebuilt_layouts', array() );
	    $panels_data	 = ! empty( $layouts[ $prebuilt_id ] ) ? $layouts[ $prebuilt_id ] : array();
	} else if ( $post_id == 'home' ) {
	    $panels_data = get_post_meta( get_option( 'siteorigin_panels_home_page_id' ), 'panels_data', true );

	    if ( is_null( $panels_data ) ) {
		// Load the default layout
		$layouts	 = apply_filters( 'siteorigin_panels_prebuilt_layouts', array() );
		$prebuilt_id	 = siteorigin_panels_setting( 'home-page-default' ) ? siteorigin_panels_setting( 'home-page-default' ) : 'home';

		$panels_data = ! empty( $layouts[ $prebuilt_id ] ) ? $layouts[ $prebuilt_id ] : current( $layouts );
	    }
	} else {
	    if ( post_password_required( $post_id ) )
		return false;
	    $panels_data = get_post_meta( $post_id, 'panels_data', true );
	}
    }

    $panels_data = apply_filters( 'siteorigin_panels_data', $panels_data, $post_id );
    if ( empty( $panels_data ) || empty( $panels_data[ 'grids' ] ) )
	return '';

    // Create the skeleton of the grids
    $grids = array();
    if ( ! empty( $panels_data[ 'grids' ] ) && ! empty( $panels_data[ 'grids' ] ) ) {
	foreach ( $panels_data[ 'grids' ] as $gi => $grid ) {
	    $gi		 = intval( $gi );
	    $grids[ $gi ]	 = array();
	    for ( $i = 0; $i < $grid[ 'cells' ]; $i ++ ) {
		$grids[ $gi ][ $i ] = array();
	    }
	}
    }

    // We need this to migrate from the old $panels_data that put widget meta into the "info" key instead of "panels_info"
    if ( ! empty( $panels_data[ 'widgets' ] ) && is_array( $panels_data[ 'widgets' ] ) ) {
	foreach ( $panels_data[ 'widgets' ] as $i => $widget ) {
	    if ( empty( $panels_data[ 'widgets' ][ $i ][ 'panels_info' ] ) ) {
		$panels_data[ 'widgets' ][ $i ][ 'panels_info' ] = $panels_data[ 'widgets' ][ $i ][ 'info' ];
		unset( $panels_data[ 'widgets' ][ $i ][ 'info' ] );
	    }
	}
    }

    if ( ! empty( $panels_data[ 'widgets' ] ) && is_array( $panels_data[ 'widgets' ] ) ) {
	foreach ( $panels_data[ 'widgets' ] as $widget ) {
	    $grids[ intval( $widget[ 'panels_info' ][ 'grid' ] ) ][ intval( $widget[ 'panels_info' ][ 'cell' ] ) ][] = $widget;
	}
    }

    ob_start();

    global $siteorigin_panels_inline_css;
    if ( empty( $siteorigin_panels_inline_css ) )
	$siteorigin_panels_inline_css = '';

    //Commenting below for correction to campaign markup
    //echo '<div class="custom-layout-entry-content">';
    $cells_count = 0;
    foreach ( $grids as $gi => $cells ) {

	//commenting below for correction to campaign markup
	//echo '<div class="section">';
	echo '<div class="container container--wide grid">';
	echo '<div class="row spotlights">';

	foreach ( $cells as $ci => $widgets ) {
	    switch ( count( $cells ) ) {
		case 1:
		    $width_in_cols	 = $layout_width;
		    break;
		case 2:
		    // Assign the width in columns depending on which cell is set to be wider in teh layout editor
		    $width_in_cols	 = 2;
		    $me		 = $ci;
		    $the_other	 = ($ci == 0) ? 1 : 0;

		    $my_width	 = $panels_data[ 'grid_cells' ][ $cells_count + $me ][ 'weight' ];
		    $the_other_width = $panels_data[ 'grid_cells' ][ $cells_count + $the_other ][ 'weight' ];

		    if ( $my_width > $the_other_width || ($my_width == $the_other_width && $me == 0) ) {
			$width_in_cols = 2;
		    }

		    // If the total row width is 2 or 1, then the width of this cell will always be 1
		    if ( $layout_width < 3 ) {
			$width_in_cols = 1;
		    }
		    break;
		case 3:
		default:
		    $width_in_cols = 1;
		    break;
	    }
	    $cells_count += count( $cells );

	    //Commenting below for correction to campaign markup
	    //echo '<div class="grid-' . $width_in_cols . '-' . $layout_width . '">';
	    foreach ( $widgets as $pi => $widget_info ) {
		$data					 = $widget_info;
		$data[ 'dh_width_suggestion' ]		 = $width_in_cols;
		$data[ 'dh_visual_width' ]		 = $width_in_cols;
		$data[ 'dh_row_width' ]			 = $width_in_cols;
		$data[ 'dh_inside_custom_layout' ]	 = TRUE;
		unset( $data[ 'panels_info' ] );
		_dh_siteorigin_panels_the_widget( $widget_info[ 'panels_info' ][ 'class' ], $data, $gi, $ci, $pi, $pi == 0, $pi == count( $widgets ) - 1, $post_id, $widget_style_wrapper );
	    }
	    //echo '</div>';
	}

	echo '</div>';
	echo '</div>';
	//echo '</div>';
    }
    //echo '</div>';

    $html = ob_get_clean();

    // Reset the current post
    $siteorigin_panels_current_post = $old_current_post;

    return apply_filters( 'siteorigin_panels_render', $html, $post_id,  ! empty( $post ) ? $post : null  );
}

/**
 * Adding 'active' class to the the current menu item
 *
 */
function special_nav_class( $classes, $item ) {
    if ( in_array( 'current-menu-item', $classes ) ) {
	$classes[] = 'active ';
    }
    return $classes;
}

add_filter( 'nav_menu_css_class', 'special_nav_class', 10, 2 );

/* * ***************************************************************************
 * Removing features from parent theme                                         *
 * ************************************************************************** */

/**
 * workaround required to remove the admin menu items to add pages, posts & comments which are not used in this theme
 *
 */
function dh_campaign_remove_menus() {
    remove_menu_page( 'edit.php' );      //Posts
    remove_menu_page( 'edit.php?post_type=page' );     //Pages
    remove_menu_page( 'edit-comments.php' );    //Comments  
}

add_action( 'admin_menu', 'dh_campaign_remove_menus' );

/**
 * removing unwanted fields from the custom_layout post created in the parent theme
 *
 */
function dh_campaign_rem_content_from_post_type() {
    remove_post_type_support( 'custom_layout', 'editor' );
}

add_action( 'init', 'dh_campaign_rem_content_from_post_type', 1000 );

/**
 * Unregistering taxonomies from parent theme.
 */
function dh_campaign_unregister_taxonomy() {
    global $wp_taxonomies;
    $dh_taxonomy_to_remove = array( 'organisation', 'chapter', 'topic' );
    foreach ( $dh_taxonomy_to_remove as $taxonomy ) {
	if ( taxonomy_exists( $taxonomy ) )
	    unset( $wp_taxonomies[ $taxonomy ] );
    }
}

add_action( 'init', 'dh_campaign_unregister_taxonomy', 20 );

/**
 * Removing all non-DH campaign widgets, since adding them just breaks the layout.
 *
 */
function dh_campaign_unregister_widgets() {
    global $wp_widget_factory;
    foreach ( $wp_widget_factory->widgets as $wp_widget_class => $wp_widget ) {

	if ( substr( $wp_widget_class, 0, 11 ) != 'DH_Campaign' ) {
	    unregister_widget( $wp_widget_class );
	}
    }
}

add_action( 'widgets_init', 'dh_campaign_unregister_widgets', 99 );

/**
 * Removing all SiteOrigin widgets, unable to remove them using dh_campaign_unregister_widgets
 *
 */
function dh_campaign_remove_siteorigin_widgets( $widgets ) {

    foreach ( $widgets as $widget ) {

	if ( substr( $widget[ 'class' ], 0, 10 ) == 'SiteOrigin' ) {
	    unset( $widgets[ $widget[ 'class' ] ] );
	}
    }

    return $widgets;
}

add_filter( 'siteorigin_panels_widgets', 'dh_campaign_remove_siteorigin_widgets' );


/* * **************************************************************************
 * Export of  custom fields                                                   *
 * *******************************************************************8****** */
if ( function_exists( "register_field_group" ) ) {
    register_field_group( array(
	'id'		 => 'acf_dh-campaign-hero-intro',
	'title'		 => 'DH campaign hero intro',
	'fields'	 => array(
	    array(
		'key'		 => 'field_56656e9e66af9',
		'label'		 => 'Hero image',
		'name'		 => 'dh_campaign_hero_image',
		'type'		 => 'image',
		'instructions'	 => 'Add an image here to be used as the hero image.  If you do not add a hero image the "hero heading" and "hero subheading" will not be visible thus a simple content page can be constructed by utilising the intro section.',
		'save_format'	 => 'object',
		'preview_size'	 => 'thumbnail',
		'library'	 => 'all',
	    ),
	    array(
		'key'		 => 'field_56656ee766afa',
		'label'		 => 'Hero heading',
		'name'		 => 'dh_campaign_hero_heading',
		'type'		 => 'text',
		'default_value'	 => '',
		'placeholder'	 => '',
		'prepend'	 => '',
		'append'	 => '',
		'formatting'	 => 'html',
		'maxlength'	 => 125,
	    ),
	    array(
		'key'		 => 'field_56656f1566afb',
		'label'		 => 'Hero subheading',
		'name'		 => 'dh_campaign_hero_subheading',
		'type'		 => 'text',
		'default_value'	 => '',
		'placeholder'	 => '',
		'prepend'	 => '',
		'append'	 => '',
		'formatting'	 => 'html',
		'maxlength'	 => 125,
	    ),
	    array(
		'key'		 => 'field_56656f4766afc',
		'label'		 => 'Intro image',
		'name'		 => 'dh_campaign_intro_image',
		'type'		 => 'file',
		'instructions'	 => 'If an image is not added here a small version of the hero image will be used.',
		'save_format'	 => 'object',
		'library'	 => 'all',
	    ),
	    array(
		'key'		 => 'field_5669609972791',
		'label'		 => 'Youtube video',
		'name'		 => 'dh_campaign_youtube_video',
		'type'		 => 'text',
		'instructions'	 => 'To show a Youtube video instead of an intro image just paste the videos link here.',
		'default_value'	 => '',
		'placeholder'	 => '',
		'prepend'	 => '',
		'append'	 => '',
		'formatting'	 => 'html',
		'maxlength'	 => '',
	    ),
	    array(
		'key'		 => 'field_56656f8b66afd',
		'label'		 => 'Intro heading',
		'name'		 => 'dh_campaign_intro_heading',
		'type'		 => 'text',
		'instructions'	 => 'If a heading is not added here the hero heading will be used.',
		'default_value'	 => '',
		'placeholder'	 => '',
		'prepend'	 => '',
		'append'	 => '',
		'formatting'	 => 'html',
		'maxlength'	 => '',
	    ),
	    array(
		'key'		 => 'field_56656fef66afe',
		'label'		 => 'Intro subheading',
		'name'		 => 'dh_campaign_intro_subheading',
		'type'		 => 'text',
		'instructions'	 => 'If a sub-heading is not added here the hero sub-heading will be used.',
		'default_value'	 => '',
		'placeholder'	 => '',
		'prepend'	 => '',
		'append'	 => '',
		'formatting'	 => 'html',
		'maxlength'	 => '',
	    ),
	    array(
		'key'		 => 'field_5665702166aff',
		'label'		 => 'Intro text',
		'name'		 => 'dh_campaign_intro_text',
		'type'		 => 'wysiwyg',
		'default_value'	 => '',
		'toolbar'	 => 'full',
		'media_upload'	 => 'no',
	    ),
	    array(
		'key'		 => 'field_5665706866b00',
		'label'		 => 'Display share buttons?',
		'name'		 => 'dh_campaign_display_share_buttons',
		'type'		 => 'true_false',
		'message'	 => '',
		'default_value'	 => 0,
	    ),
	),
	'location'	 => array(
	    array(
		array(
		    'param'		 => 'post_type',
		    'operator'	 => '==',
		    'value'		 => 'custom_layout',
		    'order_no'	 => 0,
		    'group_no'	 => 0,
		),
	    ),
	),
	'options'	 => array(
	    'position'	 => 'normal',
	    'layout'	 => 'no_box',
	    'hide_on_screen' => array(
	    ),
	),
	'menu_order'	 => -3,
    ) );
    register_field_group( array(
	'id'		 => 'acf_dh-campaign-editable-cta',
	'title'		 => 'DH campaign editable CTA',
	'fields'	 => array(
	    array(
		'key'		 => 'field_566591179304a',
		'label'		 => 'CTA text',
		'name'		 => 'dh_campaign_cta_text',
		'type'		 => 'text',
		'instructions'	 => 'Add text that will appear in the Call To Action button.',
		'default_value'	 => '',
		'placeholder'	 => '',
		'prepend'	 => '',
		'append'	 => '',
		'formatting'	 => 'html',
		'maxlength'	 => '',
	    ),
	    array(
		'key'		 => 'field_566591769304c',
		'label'		 => 'CTA in-page section link',
		'name'		 => 'dh_campaign_cta_inpage_section_link',
		'type'		 => 'text',
		'default_value'	 => '',
		'placeholder'	 => '',
		'prepend'	 => '',
		'append'	 => '',
		'formatting'	 => 'html',
		'maxlength'	 => '',
	    ),
	),
	'location'	 => array(
	    array(
		array(
		    'param'		 => 'post_type',
		    'operator'	 => '==',
		    'value'		 => 'custom_layout',
		    'order_no'	 => 0,
		    'group_no'	 => 0,
		),
	    ),
	),
	'options'	 => array(
	    'position'	 => 'normal',
	    'layout'	 => 'no_box',
	    'hide_on_screen' => array(
	    ),
	),
	'menu_order'	 => -1,
    ) );
    register_field_group( array(
	'id'		 => 'acf_dh-campaign-in-page-scroll',
	'title'		 => 'DH campaign in page scroll',
	'fields'	 => array(
	    array(
		'key'		 => 'field_56659f28d475c',
		'label'		 => 'Scroll text',
		'name'		 => 'dh_campaign_scroll_text',
		'type'		 => 'text',
		'default_value'	 => '',
		'placeholder'	 => '',
		'prepend'	 => '',
		'append'	 => '',
		'formatting'	 => 'html',
		'maxlength'	 => '',
	    ),
	    array(
		'key'		 => 'field_56659f3ad475d',
		'label'		 => 'Scroll in-page section link',
		'name'		 => 'dh_campaign_section_id',
		'type'		 => 'text',
		'default_value'	 => '',
		'placeholder'	 => '',
		'prepend'	 => '',
		'append'	 => '',
		'formatting'	 => 'html',
		'maxlength'	 => '',
	    ),
	),
	'location'	 => array(
	    array(
		array(
		    'param'		 => 'post_type',
		    'operator'	 => '==',
		    'value'		 => 'custom_layout',
		    'order_no'	 => 0,
		    'group_no'	 => 0,
		),
	    ),
	),
	'options'	 => array(
	    'position'	 => 'normal',
	    'layout'	 => 'no_box',
	    'hide_on_screen' => array(
	    ),
	),
	'menu_order'	 => 0,
    ) );
    register_field_group( array(
	'id'		 => 'acf_set-this-custom-layout-as-the-home-page',
	'title'		 => 'Set this custom layout as the home page',
	'fields'	 => array(
	    array(
		'key'			 => 'field_568cfdf80090d',
		'label'			 => 'Set this as front page',
		'name'			 => 'dh_campaign_set_this_as_front_page',
		'type'			 => 'radio',
		'instructions'		 => 'Check this box if you want this custom layout page to be set as the front / home page for the campaign. ',
		'required'		 => 1,
		'choices'		 => array(
		    'Yes'	 => 'Yes',
		    'No'	 => 'No',
		),
		'other_choice'		 => 0,
		'save_other_choice'	 => 0,
		'default_value'		 => 'No',
		'layout'		 => 'vertical',
	    ),
	),
	'location'	 => array(
	    array(
		array(
		    'param'		 => 'post_type',
		    'operator'	 => '==',
		    'value'		 => 'custom_layout',
		    'order_no'	 => 0,
		    'group_no'	 => 0,
		),
	    ),
	),
	'options'	 => array(
	    'position'	 => 'acf_after_title',
	    'layout'	 => 'default',
	    'hide_on_screen' => array(
	    ),
	),
	'menu_order'	 => 0,
    ) );
}


/* * **************************************************************************
 * Theme customizer                                                           *
 * ************************************************************************** */

function dh_campaign_customize_register( $wp_customize ) {

    $wp_customize->remove_section( 'dh_comment_form' );
    $wp_customize->remove_section( 'dh_comment_form_closed' );
    $wp_customize->remove_section( 'static_front_page' );


    $wp_customize->add_setting( 'dh_campaign_twitter_link', array(
	'sanitize_callback' => 'esc_url',
    ) );
    $wp_customize->add_control( 'dh_campaign_twitter_link', array(
	'label'		 => __( 'This sites twitter link', 'dh' ),
	'section'	 => 'dh_misc',
	'type'		 => 'url',
    ) );

    $wp_customize->add_setting( 'dh_campaign_fb_link', array(
	'sanitize_callback' => 'esc_url',
    ) );
    $wp_customize->add_control( 'dh_campaign_fb_link', array(
	'label'		 => __( 'This sites Facebook link', 'dh' ),
	'section'	 => 'dh_misc',
	'type'		 => 'url',
    ) );

    $wp_customize->add_setting( 'dh_campaign_yt_link', array(
	'sanitize_callback' => 'esc_url',
    ) );
    $wp_customize->add_control( 'dh_campaign_yt_link', array(
	'label'		 => __( 'This sites Youtube link', 'dh' ),
	'section'	 => 'dh_misc',
	'type'		 => 'url',
    ) );

    $wp_customize->add_setting( 'dh_campaign_twitter_id' );
    $wp_customize->add_control( 'dh_campaign_twitter_id', array(
	'label'		 => __( 'Twitter account @username', 'dh' ),
	'section'	 => 'dh_misc',
	'type'		 => 'text',
    ) );

    $wp_customize->add_setting( 'dh_campaign_facebook_id' );
    $wp_customize->add_control( 'dh_campaign_facebook_id', array(
	'label'		 => __( 'facebook ID', 'dh' ),
	'section'	 => 'dh_misc',
	'type'		 => 'text',
    ) );

    $wp_customize->remove_control( 'dh_breadcrumbs' );
    $wp_customize->remove_control( 'dh_search_header' );
    $wp_customize->remove_control( 'dh_newsletter_header' );
    $wp_customize->remove_control( 'dh_newsletter_header_topic_id' );
    $wp_customize->remove_control( 'dh_numbering_auto' );
}

add_action( 'customize_register', 'dh_campaign_customize_register', 20 );
