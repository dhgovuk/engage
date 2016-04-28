<?php

/**
 * DH Campaign: half/full quote text spotlight
 *
 * Inherits from DH_Campaign_Super_Widget
 *
 * @see super_widget.php
 * @author Shajidul.Alom
 *
 */
class DH_Campaign_Widget_Spotlight_Quote_Text extends DH_Campaign_Super_Widget {

    /**
     * Constructor.
     */
    public function __construct( $id_base = false, $name = false, $widget_options = array(), $control_options = array() ) {
	parent::__construct( 'widget_dh_campaign_spotlight_quote_text', __( 'DH Campaign: Quote text', 'dh' ), array(
	    'description' => __( 'Use this widget to display a spotlight with quote text in full/half widths.', 'dh' ),
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
	$text		 = parent::get_text_display( $instance );
	$width		 = parent::get_width( $instance );
	$width_col_no	 = ($width === 'full') ? 12 : 6;
	$quote_from	 = $instance[ 'quote_from' ];
	$their_role	 = $instance[ 'their_role' ];
	?>

	<section id="<?php echo $instance[ 'section_id' ]; ?>">

	    <div class="col-sm-<?php echo $width_col_no; ?>">
		<div class="spotlight spotlight--wide spotlight--blockquote">
		    <blockquote class="spotlight__inner">
			<div>
			    <?php if ( $text ) : ?>
	    		    <p><?php echo $text; ?></p>
			    <?php endif; ?>
			    <footer><cite title="<?php echo $quote_from; ?>"><?php echo $quote_from; ?></cite> - <?php echo $their_role; ?></footer>
			</div>
		    </blockquote>
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

	$instance		 = parent::update( $new_instance, $instance, array( 'section_id', 'width', 'text' ) );
	$instance[ 'quote_from' ]	 = strip_tags( $new_instance[ 'quote_from' ] );
	$instance[ 'their_role' ]	 = strip_tags( $new_instance[ 'their_role' ] );

	return $instance;
    }

    /**
     * Display the form for this widget on the Widgets page of the Admin area.
     *
     * @param array $instance
     */
    public function form( $instance, $fields = Array() ) {
	$quote_from	 = empty( $instance[ 'quote_from' ] ) ? '' : esc_attr( $instance[ 'quote_from' ] );
	$their_role	 = empty( $instance[ 'their_role' ] ) ? '' : esc_attr( $instance[ 'their_role' ] );
	?>        

	<fieldset>
	    <legend>Widget : Quote Text</legend>
	    <?php parent::form( $instance, array( 'width', 'section_id', 'text' ) ); ?>

	    <p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'quote_from' ) ); ?>"><?php _e( 'Quote from :', 'dh' ); ?></label>
		<input id="<?php echo esc_attr( $this->get_field_id( 'quote_from' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'quote_from' ) ); ?>" type="text" value="<?php echo esc_attr( $quote_from ); ?>" required>
	    </p>

	    <p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'their_role' ) ); ?>"><?php _e( 'Their role :', 'dh' ); ?></label>
		<input id="<?php echo esc_attr( $this->get_field_id( 'their_role' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'their_role' ) ); ?>" type="text" value="<?php echo esc_attr( $their_role ); ?>" required>
	    </p>
	</fieldset>

	<?php
    }

}
