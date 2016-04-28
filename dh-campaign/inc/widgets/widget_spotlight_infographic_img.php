<?php

/**
 * DH Campaign: Infographic image
 *
 * Inherits from DH_Campaign_Super_Widget
 *
 * @see super_widget.php
 * @author Shajidul.Alom
 *
 */
class DH_Campaign_Widget_Spotlight_Infographic_Img extends DH_Campaign_Super_Widget {

    /**
     * Constructor.
     */
    public function __construct( $id_base = false, $name = false, $widget_options = array(), $control_options = array() ) {
	parent::__construct( 'widget_dh_campaign_spotlight_infographic_img', __( 'DH Campaign: Infographic image', 'dh' ), array(
	    'description' => __( 'Use this widget to display an infograhic image of vaired sizes and widths.', 'dh' ),
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
	$width		 = parent::get_width( $instance );
	$width_col_no	 = ($width === 'full') ? 12 : 6;
	$height		 = parent::get_height( $instance );
	$size		 = 'medium';
	if ( $height === 'short' ) {
	    $size = 'dh-short';
	} elseif ( $height === 'tall' ) {
	    $size = 'large';
	}

	$image		 = get_post( $instance[ 'image_id' ] );
	$image_alt_text	 = get_post_meta( $instance[ 'image_id' ], '_wp_attachment_image_alt', true );
	$img_src	 = wp_get_attachment_image_src( $instance[ 'image_id' ], $size );
	?>

	<section id="<?php echo $instance[ 'section_id' ]; ?>">
	    <div class="col-sm-<?php echo $width_col_no; ?>  mobile-height height-<?php echo $height; ?>">
		<!--<div class="spotlight  spotlight--wide">-->
		<!--<div class='spotlight__inner'>-->
		<img class="img-responsive img-center" src="<?php echo $img_src[ 0 ]; ?>" alt="<?php echo $image_alt_text; ?>" title="<?php echo $image->post_title; ?>" >
		<!--</div>-->
		<!--</div>-->
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

	$instance = parent::update( $new_instance, $instance, array( 'height', 'section_id', 'width' ) );

	$instance[ 'image_id' ] = absint( $new_instance[ 'image_id' ] );
	if ( $new_instance[ 'delete-image' ] == 'delete-image' ) {
	    $instance[ 'image_id' ] = NULL;
	}

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
	    <legend>Widget : Infographic Image</legend>
	    <?php
	    parent::form( $instance, array( 'section_id', 'height', 'width' ) );

	    _dh_widget_image_control( $this, $instance );
	    ?>

	</fieldset>
	<?php
    }

}
