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
        $style           = $instance[ 'style' ];
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
                        <?php if ( $style == 1 ): ?>
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
                      <?php elseif ( $style == 2 ) : ?>
                        <div class="subscribe-link  lead">
                          <a href = 'http://r1.surveysandforms.com/<?php echo $topic_id; ?>'>Click here to subscribe</a>
                        </div>
                      <?php elseif ( $style == 3 ) :
                        global $wp;
                        $current_url = home_url(add_query_arg(array(),$wp->request));
                        ?>
                        <!-- Start of signup -->
                          <script language="javascript">
                          <!--
                          function validate_signup(frm) {
                                  var emailAddress = frm.Email.value;
                                  var errorString = '';
                                  if (emailAddress == '' || emailAddress.indexOf('@') == -1) {
                                          errorString = 'Please enter your email address';
                                  }


                          var els = frm.getElementsByTagName('input');
                          for (var i = 0; i < els.length; i++)
                          {
                              if (els[i].className == 'text' || els[i].className == 'date' || els[i].className == 'number')
                              {
                                  if (els[i].value == '')
                                      errorString = 'Please complete all required fields.';
                              }
                              else if (els[i].className == 'radio')
                              {
                                  var toCheck = document.getElementsByName(els[i].name);
                                  var radioChecked = false;
                                  for (var j = 0; j < toCheck.length; j++)
                                  {
                                      if (toCheck[j].name == els[i].name && toCheck[j].checked)
                                          radioChecked = true;
                                  }
                                  if (!radioChecked)
                                      errorString = 'Please complete all required fields.';
                              }
                          }



                                  var isError = false;
                              if (errorString.length > 0)
                                  isError = true;

                              if (isError)
                                  alert(errorString);
                                  return !isError;
                          }


                          //-->
                          </script>
                          <?php $return_code = $_GET['result'];
                          if ($return_code == 'success') :
                            echo '<div class="signup-thank-you  lead">Thank you for signing up to emails from the Department of Health. You should receive a welcome email shortly.</div>';
                          else : ?>
                            <form name="signup" id="signup" action="http://r1.dmtrk.net/signup.ashx" method="post" onsubmit="return validate_signup(this)">
<!--                            <p>Signup form description goes here</p>-->
                            <input type="hidden" name="addressbookid" value="<?php echo $topic_id; ?>">
                            <!-- UserID - required field, do not remove -->
                            <input type="hidden" name="userid" value="186288">
                            <!-- ReturnURL - when the user hits submit, they'll get sent here -->
                            <input type="hidden" name="ReturnURL" value="<?php echo $current_url;?>">
                            <!-- Email - the user's email address -->

                            <div class="form-group">
                              <label class="visuallyhidden" for="Email">Email</label>
                              <input class="form-control" type="text" name="Email" id="Email" placeholder="Email">
                            </div>
                            <input class="btn btn--filled btn--block" type="Submit" name="Submit" value="Subscribe">
                            </form>
                      <?php endif; ?>
                    <?php endif; ?>

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
        $instance['style'] = $new_instance['style'];
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
		<label for="<?php echo esc_attr( $this->get_field_id( 'topic_id' ) ); ?>"><?php _e( 'GovDelivery or Wired Marketing address book: ', 'dh' ); ?></label>
		<input id="<?php echo esc_attr( $this->get_field_id( 'topic_id' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'topic_id' ) ); ?>" type="text" value="<?php echo esc_attr( $topic_id ); ?>" required>
	    </p>
        <?php

        $style   = empty( $instance['style'] ) ? 1 : $instance['style'];
        ?>
          <p><label for="<?php echo esc_attr( $this->get_field_id( 'style' ) ); ?>"><?php _e( 'Style:', 'dh' ); ?></label>
          <select id="<?php echo esc_attr( $this->get_field_id( 'style' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'style' ) ); ?>">
            <option value="1" <?php if ($style == 1) { echo 'selected="selected"'; } ?>>GovDelivery Topic ID</option>
            <option value="2" <?php if ($style == 2) { echo 'selected="selected"'; } ?>>Wired Marketing Survey Link</option>
            <option value="3" <?php if ($style == 3) { echo 'selected="selected"'; } ?>>Wired Marketing Survey Form</option>
          </select>

	</fieldset>
	<?php
    }

}
