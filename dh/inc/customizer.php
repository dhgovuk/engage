<?php
/**
 * DH Theme Customizer support
 */

/**
 * Implement Theme Customizer additions and adjustments.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function dh_customize_register( $wp_customize ) {
  $wp_customize->remove_section( 'colors' );
  $wp_customize->add_section( 'dh_custom_colours', array( 'title' => __( 'Custom Colours', 'dh' ) ) );
  $wp_customize->add_setting( 'dh_colour_scheme', array( 'default' => 1 ) );

  $choices = array();
  foreach ( dh_colour_schemes() as $index => $choice ) {
    $choices[$index] = $choice['label'];
  }
  $wp_customize->add_control( 'dh_colour_scheme', array(
    'label'   => __( 'Choose colour scheme', 'dh' ),
    'section' => 'dh_custom_colours',
    'type'    => 'radio',
    'choices' => $choices,
  ));

  $wp_customize->remove_control( 'display_header_text' );

  // Cleaning up the frontpage section
  $wp_customize->get_section( 'static_front_page' )->title = __( 'Front Page', 'dh' );
  $wp_customize->remove_control('page_for_posts');
  $wp_customize->remove_control('show_on_front');
  if ( get_option('show_on_front', FALSE) != 'page' ) {
    delete_option('show_on_front');
    add_option('show_on_front', 'page');
  }
  if ( get_option('page_for_posts', '') != 0 ) {
    delete_option('page_for_posts');
    add_option('page_for_posts', 0);
  }

  $wp_customize->add_section( 'dh_comment_form', array(
    'title' => __( 'Comment Form', 'dh' ),
  ));

  $wp_customize->add_setting( 'dh_comment_field_author_enabled' );
  $wp_customize->add_control( 'dh_comment_field_author_enabled', array(
    'label'    => __( 'Enable "Name" field', 'dh' ),
    'section'  => 'dh_comment_form',
    'type'     => 'checkbox',
    'priority' => 10,
  ));

  $wp_customize->add_setting( 'dh_comment_field_author_required' );
  $wp_customize->add_control( 'dh_comment_field_author_required', array(
    'label'       => __( 'Require "Name" field', 'dh' ),
    'section'     => 'dh_comment_form',
    'type'        => 'checkbox',
    'priority'    => 11,
    'description' => '<hr/>',
  ));

  $wp_customize->add_setting( 'dh_comment_field_email_enabled' );
  $wp_customize->add_control( 'dh_comment_field_email_enabled', array(
    'label'      => __( 'Enable "E-mail" field', 'dh' ),
    'section'     => 'dh_comment_form',
    'type'        => 'checkbox',
    'priority'    => 12,
  ));

  $wp_customize->add_setting( 'dh_comment_field_email_required' );
  $wp_customize->add_control( 'dh_comment_field_email_required', array(
    'label'       => __( 'Require "E-mail" field', 'dh' ),
    'section'     => 'dh_comment_form',
    'type'        => 'checkbox',
    'priority'    => 13,
    'description' => '<hr/>',
  ));

  $wp_customize->add_setting( 'dh_comment_field_custom_text_1_enabled' );
  $wp_customize->add_control( 'dh_comment_field_custom_text_1_enabled', array(
    'label'      => __( 'Enable custom text field 1', 'dh' ),
    'section'     => 'dh_comment_form',
    'type'        => 'checkbox',
    'priority'    => 14,
  ));

  $wp_customize->add_setting( 'dh_comment_field_custom_text_1_label' );
  $wp_customize->add_control( 'dh_comment_field_custom_text_1_label', array(
    'label'      => __( 'Label of custom text field 1', 'dh' ),
    'section'     => 'dh_comment_form',
    'type'        => 'text',
    'priority'    => 14.5,
  ));

  $wp_customize->add_setting( 'dh_comment_field_custom_text_1_required' );
  $wp_customize->add_control( 'dh_comment_field_custom_text_1_required', array(
    'label'       => __( 'Require custom text field 1', 'dh' ),
    'section'     => 'dh_comment_form',
    'type'        => 'checkbox',
    'priority'    => 15,
    'description' => '<hr/>',
  ));

  $wp_customize->add_setting( 'dh_comment_field_custom_text_2_enabled' );
  $wp_customize->add_control( 'dh_comment_field_custom_text_2_enabled', array(
    'label'       => __( 'Enable custom text field 2', 'dh' ),
    'section'     => 'dh_comment_form',
    'type'        => 'checkbox',
    'priority'    => 16,
  ));

  $wp_customize->add_setting( 'dh_comment_field_custom_text_2_label' );
  $wp_customize->add_control( 'dh_comment_field_custom_text_2_label', array(
    'label'      => __( 'Label of custom text field 2', 'dh' ),
    'section'     => 'dh_comment_form',
    'type'        => 'text',
    'priority'    => 16.5,
  ));

  $wp_customize->add_setting( 'dh_comment_field_custom_text_2_required' );
  $wp_customize->add_control( 'dh_comment_field_custom_text_2_required', array(
    'label'       => __( 'Require custom text field 2', 'dh' ),
    'section'     => 'dh_comment_form',
    'type'        => 'checkbox',
    'priority'    => 17,
    'description' => '<hr/>',
  ));

  for ( $i = 1; $i <= 3; $i++ ) {
    $prefix = 'dh_comment_field_custom_dd_' . $i;

    $wp_customize->add_setting( $prefix . '_enabled' );
    $wp_customize->add_control( $prefix . '_enabled', array(
      'label'    => __( 'Enable custom drop-down list ' . $i . ' field', 'dh' ),
      'section'  => 'dh_comment_form',
      'type'     => 'checkbox',
      'priority' => $i * 20 + 1,
    ));

    $wp_customize->add_setting( $prefix . '_label' );
    $wp_customize->add_control( $prefix . '_label', array(
      'label'    => __( 'Label for custom drop-down list ' . $i . ' field', 'dh' ),
      'section'  => 'dh_comment_form',
      'type'     => 'text',
      'priority' => $i * 20 + 2,
    ));

    $wp_customize->add_setting( $prefix . '_items' );
    $wp_customize->add_control( $prefix . '_items', array(
      'label'    => __( 'Options for custom drop-down list ' . $i . ' field', 'dh' ),
      'section'  => 'dh_comment_form',
      'type'     => 'textarea',
      'priority' => $i * 20 + 3,
    ));

    $wp_customize->add_setting( $prefix . '_required' );
    $wp_customize->add_control( $prefix . '_required', array(
      'label'       => __( 'Require custom drop-down list ' . $i . ' field', 'dh' ),
      'section'     => 'dh_comment_form',
      'type'        => 'checkbox',
      'priority'    => $i * 20 + 4,
      'description' => '<hr/>',
    ));
  }

  $wp_customize->add_section( 'dh_comment_form_closed', array(
    'title' => __( 'Comments Closed', 'dh' ),
  ));

  $wp_customize->add_setting( 'dh_comment_closed_text');
  $wp_customize->add_control( 'dh_comment_closed_text', array(
    'label' => __( 'Text when comments are closed', 'dh' ),
    'section' => 'dh_comment_form_closed',
    'type'    => 'textarea',
  ));

  $wp_customize->add_section( 'dh_misc', array(
    'title' => __( 'Miscellaneous', 'dh' ),
  ));

  $wp_customize->add_setting( 'dh_breadcrumbs');
  $wp_customize->add_control( 'dh_breadcrumbs', array(
    'label' => __( 'Show breadcrumbs', 'dh' ),
    'section' => 'dh_misc',
    'type'    => 'checkbox',
  ));

  $wp_customize->add_setting( 'dh_search_header');
  $wp_customize->add_control( 'dh_search_header', array(
    'label' => __( 'Show Search Box in header', 'dh' ),
    'section' => 'dh_misc',
    'type'    => 'checkbox',
  ));

  $wp_customize->add_setting( 'dh_newsletter_header');
  $wp_customize->add_control( 'dh_newsletter_header', array(
    'label' => __( 'Show Newsletter Link in header', 'dh' ),
    'section' => 'dh_misc',
    'type'    => 'checkbox',
  ));

  $wp_customize->add_setting( 'dh_newsletter_header_topic_id');
  $wp_customize->add_control( 'dh_newsletter_header_topic_id', array(
    'label' => __( 'Topic ID for Newsletter Link in header', 'dh' ),
    'section' => 'dh_misc',
    'type'    => 'text',
  ));

  $wp_customize->add_setting( 'dh_numbering_auto');
  $wp_customize->add_control( 'dh_numbering_auto', array(
    'label' => __( 'Enable auto-renumbering to avoid duplicate DH Numbers for recommendations and questions', 'dh' ),
    'section' => 'dh_misc',
    'type'    => 'checkbox',
  ));
}
add_action( 'customize_register', 'dh_customize_register' );

/**
 * Bind JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function dh_customize_preview_js() {
	wp_enqueue_script( 'dh_customizer', get_template_directory_uri() . '/js/customizer.js', array( 'customize-preview' ), '20131205', true );
}
add_action( 'customize_preview_init', 'dh_customize_preview_js' );

function dh_customize_enqueue() {
  wp_enqueue_media();
  wp_enqueue_script( 'dh-customizer-ajax', get_template_directory_uri() . '/js/dh.customize.js', array( 'jquery', 'customize-controls', 'media-upload', 'thickbox' ), false, true );
}
add_action( 'customize_controls_enqueue_scripts', 'dh_customize_enqueue' );