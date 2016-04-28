<?php

/**
 * DH Campaign: Image/video
 *
 * Inherits from DH_Campaign_Super_Widget
 *
 * @see super_widget.php
 * @author Shajidul.Alom
 *
 */
class DH_Campaign_Widget_Spotlight_Img_Vid extends DH_Campaign_Super_Widget {

    /**
     * Constructor.
     */
    public function __construct( $id_base = false, $name = false, $widget_options = array(), $control_options = array() ) {
	parent::__construct( 'widget_dh_campaign_spotlight_img_vid', __( 'DH Campaign: Image/video', 'dh' ), array(
	    'description' => __( 'Use this widget to display a spotlight image or video with half/full width.', 'dh' ),
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
	$width				 = parent::get_width( $instance );
	$width_col_no			 = ($width === 'full') ? 12 : 6;
	$img				 = wp_get_attachment_image_src( $instance[ 'image_id' ], 'large' );
	$youtube			 = $instance[ 'youtube' ];
	$spotlight_class		 = '';
	$spotlight_background_img	 = '';

	if ( ! empty( $youtube ) ) {
	    $spotlight_class = 'spotlight--video';
	} elseif ( ! empty( $img ) ) {
	    $spotlight_class		 = 'spotlight--image';
	    $spotlight_background_img	 = 'style="background-image: url(' . $img[ 0 ] . ');"';
	}
	?>

	<section id="<?php echo $instance[ 'section_id' ]; ?>">
	    <div class="col-sm-<?php echo $width_col_no; ?>">
		<div class="spotlight spotlight--wide <?php echo $spotlight_class; ?>" <?php echo $spotlight_background_img; ?>>
		    <?php if ( $youtube ) : ?>
			<?php echo wp_oembed_get( $youtube ); ?>
		    <?php endif; ?>
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

	$instance = parent::update( $new_instance, $instance, array( 'section_id', 'width' ) );

	$instance[ 'image_id' ] = absint( $new_instance[ 'image_id' ] );
	if ( $new_instance[ 'delete-image' ] == 'delete-image' ) {
	    $instance[ 'image_id' ] = NULL;
	}

	$instance[ 'use_colour_bg' ]	 = $new_instance[ 'use_colour_bg' ];
	$instance[ 'youtube' ]		 = esc_url( $new_instance[ 'youtube' ] );


	return $instance;
    }

    /**
     * Display the form for this widget on the Widgets page of the Admin area.
     *
     * @param array $instance
     */
    public function form( $instance, $fields = Array() ) {
	?>
	<fieldset>
	    <legend>Widget : Image or video spotlight</legend>
	    <?php
	    parent::form( $instance, array( 'section_id', 'width' ) );

	    $youtube = empty( $instance[ 'youtube' ] ) ? '' : esc_attr( $instance[ 'youtube' ] );
	    _dh_widget_image_control( $this, $instance );
	    ?>

	    <p><label for="<?php echo esc_attr( $this->get_field_id( 'youtube' ) ); ?>"><?php _e( 'Youtube:', 'dh' ); ?></label>
		<input id="<?php echo esc_attr( $this->get_field_id( 'youtube' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'youtube' ) ); ?>" type="text" value="<?php echo esc_attr( $youtube ); ?>"><br>To add a Youtube video simply paste the videos link above, note that adding a video here will override any image selected previously.</p>

	    <p><?php if ( $youtube ) : ?>
	        <div class="videoContainer">
		    <?php echo wp_oembed_get( $youtube ); ?>
	        </div>
	    <?php endif; ?>
	    <?php
	    if ( isset( $fields[ 'youtube help' ] ) ) {
		echo '<br>(' . $fields[ 'youtube help' ] . ')';
	    }
	    ?></p>
	</fieldset>
	<?php
    }

}
