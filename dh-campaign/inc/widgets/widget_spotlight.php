<?php

/**
 * DH Campaign: 1/3 Spotlight
 *
 * Inherits from DH_Campaign_Super_Widget
 *
 * @see super_widget.php
 * @author Shajidul.Alom
 *
 */
class DH_Campaign_Widget_Spotlight extends DH_Campaign_Super_Widget {

    /**
     * Constructor.
     */
    public function __construct( $id_base = false, $name = false, $widget_options = array(), $control_options = array() ) {
	parent::__construct( 'widget_dh_campaign_spotlight', __( 'DH Campaign: 1/3 Spotlight', 'dh' ), array(
	    'description' => __( 'Use this widget to display a campaign spotlight with a 1/3 width which can contain text with a background.', 'dh' ),
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

	$title		 = parent::get_title_display( $instance );
	$text		 = parent::get_text_display( $instance );
	$background	 = parent::get_background( $instance );
	$img		 = wp_get_attachment_image_src( $instance[ 'image_id' ], 'large' );
	$cta_text	 = parent::get_cta_text( $instance );
	$cta_link	 = parent::get_cta_link( $instance );


	$spotlight_class		 = '';
	$spotlight_background_img	 = '';
	if ( $img ) {
	    $spotlight_class		 = 'image spotlight--overlay';
	    $spotlight_background_img	 = 'style="background-image: url(' . $img[ 0 ] . ');"';
	} else {
	    if ( $background == 'coloured' ) {
		$spotlight_class = 'primary';
	    } elseif ( $background == 'white' ) {
		$spotlight_class = 'white';
	    } elseif ( $background == 'border' ) {
		$spotlight_class = 'border';
	    }
	}
	?>
	<section id="<?php echo $instance[ 'section_id' ]; ?>">
	    <div class="col-sm-4">
		<div class="spotlight spotlight--<?php echo $spotlight_class; ?>" <?php echo $spotlight_background_img; ?>>
		    <div class="spotlight__inner">  
			<h4><?php echo $title; ?></h4>
			<p>
			    <?php echo $text; ?>
			</p>
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

	$instance = parent::update( $new_instance, $instance, array( 'text', 'title', 'section_id', 'background', 'cta' ) );

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
	    <legend>Widget: Spotlight</legend>
	    <?php
	    parent::form( $instance, array( 'text', 'title', 'section_id', 'background', 'cta' ) );
	    ?> 
	    <p>Or choose an image as the background:</p>
	    <?php
	    _dh_widget_image_control( $this, $instance );
	    ?>
	</fieldset>
	<?php
    }

}
