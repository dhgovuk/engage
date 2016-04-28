<?php

/**
 * DH Campaign: Text with background
 *
 * Inherits from DH_Campaign_Super_Widget
 *
 * @see super_widget.php
 * @author Shajidul.Alom
 *
 */
class DH_Campaign_Widget_Spotlight_Img_Text extends DH_Campaign_Super_Widget {

    /**
     * Constructor.
     */
    public function __construct( $id_base = false, $name = false, $widget_options = array(), $control_options = array() ) {
	parent::__construct( 'widget_dh_campaign_spotlight_text', __( 'DH Campaign: Text with background', 'dh' ), array(
	    'description' => __( 'Use this widget to display a spotlight with text & CTA over a background image/colour in full/half widths.', 'dh' ),
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

	$img		 = wp_get_attachment_image_src( $instance[ 'image_id' ], 'large' );
	$title		 = parent::get_title_display( $instance );
	$text		 = parent::get_text_display( $instance );
	$width		 = parent::get_width( $instance );
	$width_col_no	 = ($width === 'full') ? 12 : 6;
	$cta_text	 = parent::get_cta_text( $instance );
	$cta_link	 = parent::get_cta_link( $instance );
	$background	 = parent::get_background( $instance );

	$spotlight_class		 = '';
	$spotlight_background_img	 = '';
	if ( ! empty( $img ) ) {
	    $spotlight_class		 = 'image spotlight--overlay';
	    $spotlight_background_img	 = 'style="background-image: url(' . $img[ 0 ] . ');"';
	} elseif ( $background == 'coloured' ) {
	    $spotlight_class = 'primary';
	} elseif ( $background == 'white' ) {
	    $spotlight_class = 'white';
	} elseif ( $background == 'border' ) {
	    $spotlight_class = 'border';
	}
	?>

	<section id="<?php echo $instance[ 'section_id' ]; ?>">
	    <div class="col-sm-<?php echo $width_col_no; ?>">
		<div class="spotlight spotlight--wide spotlight--<?php echo $spotlight_class; ?>" <?php echo $spotlight_background_img; ?>>
		    <div class="spotlight__inner">
			<h4><?php echo $title; ?></h4>
			<p><?php echo $text; ?></p>

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

	$instance = parent::update( $new_instance, $instance, array( 'section_id', 'title', 'text', 'background', 'cta' ) );

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
	    <legend>Widget : Text over background image</legend>
	<?php
	parent::form( $instance, array( 'section_id', 'title', 'text', 'width', 'background', 'cta' ) );
	_dh_widget_image_control( $this, $instance );
	?>
	</fieldset>

	    <?php
	}

    }
    