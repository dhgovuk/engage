<?php

/**
 * DH Campaign: Data capture
 *
 * Inherits from DH_Campaign_Super_Widget
 *
 * @see super_widget.php
 * @author Shajidul.Alom
 *
 */
class DH_Campaign_Widget_Spotlight_Data_Capture extends DH_Campaign_Super_Widget {

    /**
     * Constructor.
     */
    public function __construct( $id_base = false, $name = false, $widget_options = array(), $control_options = array() ) {
	parent::__construct( 'widget_dh_campaign_spotlight_data_capture', __( 'DH Campaign: Data capture', 'dh' ), array(
	    'description' => __( 'Use this widget to display full width data capture spotlight', 'dh' ),
	) );
    }

    /*
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
	$subtitle	 = parent::get_subtitle_display( $instance );
	$topic_id	 = $instance[ 'topic_id' ];
	?>

	<div class="section section--dark" id="sign-up">  
	    <section id="<?php echo $instance[ 'section_id' ]; ?>" class="container clearfix">
		<div class="col-sm-6">
		    <h1><?php echo $title; ?><small><?php echo $subtitle; ?></small></h1>
		    <div class="lead">
			<?php echo $text; ?>
		    </div>
		</div>
		<div class="col-sm-6">
		    <div class="form-group">
			<div class="form-group">
			    <h5>Fill in your details below</h5>
			</div>

			<form id="newsletter-form-header" accept-charset="UTF-8" action="https://public.govdelivery.com/accounts/UKDH/subscribers/qualify" method="post">
			    <div class="form-group">
				<input name="utf8" type="hidden" value="&#10003;" />
				<input name="authenticity_token" type="hidden" value="fpDUF0E54P0eIsD2Jd0BYQW8QFnOAs/39gdiuVAk8A4=" />
				<input id="topic_id" name="topic_id" type="hidden" value="<?php echo $topic_id; ?>" />
			    </div>
			    <div class="form-group">
				<label class="visuallyhidden" for="email">Email</label>
				<input class="form-control" id="email" name="email" type="text" placeholder="Email" />
			    </div>
			    <button class="btn btn--filled btn--block" type="submit">
				Submit
			    </button>
			</form>
		    </div>
		</div>
	    </section>
	</div>
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
	$instance		 = parent::update( $new_instance, $instance, array( 'section_id', 'title', 'subtitle', 'text' ) );
	$instance[ 'topic_id' ]	 = strip_tags( $new_instance[ 'topic_id' ] );
	return $instance;
    }

    /**
     * Display the form for this widget on the Widgets page of the Admin area.
     *
     * @param array $instance
     */
    public function form( $instance, $fields = Array() ) {
	$topic_id = empty( $instance[ 'topic_id' ] ) ? '' : esc_attr( $instance[ 'topic_id' ] );
	?>        
	<fieldset>
	    <legend>Widget : Data Capture</legend>
	    <?php parent::form( $instance, array( 'section_id', 'title', 'subtitle', 'text' ) ); ?>

	    <p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'topic_id' ) ); ?>"><?php _e( 'Gov delivery form topic ID :', 'dh' ); ?></label>
		<input id="<?php echo esc_attr( $this->get_field_id( 'topic_id' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'topic_id' ) ); ?>" type="text" value="<?php echo esc_attr( $topic_id ); ?>" required>
	    </p>

	</fieldset>
	<?php
    }

}
