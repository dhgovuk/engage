<?php

/**
 * DH Campaign: Custom text
 *
 * Inherits from DH_Campaign_Super_Widget
 *
 * @see super_widget.php
 * @author Shajidul.Alom
 *
 */
class DH_Campaign_Widget_Spotlight_Custom_Text extends DH_Campaign_Super_Widget {

    /**
     * Constructor.
     */
    public function __construct( $id_base = false, $name = false, $widget_options = array(), $control_options = array() ) {
	parent::__construct( 'widget_dh_campaign_custom_text', __( 'DH Campaign: Custom text', 'dh' ), array(
	    'description' => __( 'Use this widget to display the text saved in the custom text post (used for text related to infograhics.', 'dh' ),
	) );
    }

    /**
     * Output the HTML for this widget.
     *
     * @access public
     *
     * @param array $args     An array of standard parameters for widgets in this the me.
     * @param array $instance An array of settings for this widget instance.
     */
    public function widget( $args, $instance ) {
	$width		 = parent::get_width( $instance );
	$background	 = parent::get_background( $instance );
	$width_col_no	 = ($width === 'full') ? 12 : 6;
	$cta_text	 = parent::get_cta_text( $instance );
	$cta_link	 = parent::get_cta_link( $instance );

	$custom_text	 = '';
	$spotlight_class = '';

	if ( $background == 'coloured' ) {
	    $spotlight_class = 'primary';
	} elseif ( $background == 'white' ) {
	    $spotlight_class = 'white';
	} elseif ( $background == 'border' ) {
	    $spotlight_class = 'border';
	}

	if ( get_post_status( $instance[ 'custom_text' ] ) == 'publish' ) {
	    $custom_text = get_post_field( 'post_content', $instance[ 'custom_text' ] );
	} else {
	    $custom_text = 'Please select a valid custom text post in the page builder.';
	}
	?>

	<section id="<?php echo $instance[ 'section_id' ]; ?>">
	    <div class="col-sm-<?php echo $width_col_no; ?>">
		<div class="spotlight spotlight--custom spotlight--<?php echo $spotlight_class; ?>" >
		    <div class="spotlight__inner">
	<?php
	// The filter from siteorigin results in an infinite nested loop. So we remove it, run the content filters, then add back again.
	remove_filter( 'the_content', 'siteorigin_panels_filter_content' );
	echo apply_filters( 'the_content', $custom_text );
	add_filter( 'the_content', 'siteorigin_panels_filter_content' );
	?>
			<?php if ( ! empty( $cta_link ) && ! empty( $cta_text ) ) : ?>
	    		<p class="text-right"><a class="btn btn--small" href="<?php echo $cta_link; ?>"><?php echo $cta_text; ?></a></p>
			<?php endif; ?>

		    </div>
		</div>
	    </div>
	</section>

	<?php
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
    public function update( $new_instance, $old_instance, $fields = Array() ) {

	$instance		 = parent::update( $new_instance, $instance, array( 'section_id', 'width', 'background', 'cta' ) );
	$instance[ 'custom_text' ] = $new_instance[ 'custom_text' ];

	return $instance;
    }

    /**
     * Display the form for this widget on the Widgets page of the Admin area.
     *
     * @param array $instance
     */
    public function form( $instance, $fields = Array() ) {

	$custom_text_defaults = array(
	    'depth'			 => 0, 'child_of'		 => 0,
	    'selected'		 => ! empty( $instance[ 'custom_text' ] ) ? $instance[ 'custom_text' ] : NULL, 'echo'			 => 1,
	    'name'			 => 'page_id', 'id'			 => '',
	    'show_option_none'	 => '', 'show_option_no_change'	 => '',
	    'option_none_value'	 => '',
	);

	$custom_text = get_posts( array( 'post_type' => 'custom_text', 'numberposts' => -1 ) );
	?>        

	<fieldset>
	    <legend>Widget : Custom Text</legend>
	<?php
	parent::form( $instance, array( 'section_id', 'width', 'background', 'cta' ) );
	?>
	    <p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'custom_text' ) ); ?>"><?php _e( 'Custom text:', 'dh' ); ?></label>
		<select id="<?php echo esc_attr( $this->get_field_id( 'custom_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'custom_text' ) ); ?>">
		    <option value="-1"> --- </option>
	<?php print walk_page_dropdown_tree( $custom_text, 0, $custom_text_defaults ); ?>
		</select>
		Select the relevant custom text post that holds the text element you wish to display within this widget.
	    </p>

	</fieldset>

	<?php
    }

}
