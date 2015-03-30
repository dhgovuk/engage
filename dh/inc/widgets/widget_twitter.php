<?php
/**
 * DH: Text Widget
 *
 * Inherits from DH_Super_Widget
 *
 * @see super_widget.php
 * @author Khaled.zaidan
 *
 */

class DH_Widget_Twitter extends DH_Super_Widget {
  /**
   * Constructor.
   */
  public function __construct() {
    parent::__construct( 'widget_dh_twitter_feed', __( 'DH: Twitter Feed', 'dh' ), array(
        'description' => __( 'Use this widget to display a feed from Twitter.', 'dh' ),
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
    $title = parent::get_title_display( $instance );
    $width = parent::get_width( $instance );
    $row_width = parent::get_row_width($instance);

    echo $args['before_widget'];

    require_once(get_template_directory() . '/inc/twitter/twitteroauth.php');
    $search_by            = $instance['search_by'];
    $twitter_user         = $instance['twitter_user'];
    $twitter_hashtag      = $instance['twitter_hashtag'];
    $consumer_key         = $instance['consumer_key'];
    $consumer_secret      = $instance['consumer_secret'];
    $access_token         = $instance['access_token'];
    $access_token_secret  = $instance['access_token_secret'];

    $connection = new TwitterOAuth($consumer_key, $consumer_secret, $access_token, $access_token_secret);
    if ( $search_by == 1 ) {
      $user_handles = array();
      foreach ( explode( ',', $twitter_user ) as $user_handle ) {
        $user_handle = urlencode( trim( $user_handle ) );
        if ( $user_handle ) {
          $user_handles[] = 'from:' . $user_handle;
        }
      }
      $Q = implode( '+OR+', $user_handles );
    }
    else {
      $Q = urlencode( '#' . $twitter_hashtag );
    }

    $Q_legal_offset = md5($Q);

    // Load the tweets cache, see if we have this result (not older than 1 minute)
    $dh_twitter = wp_cache_get( 'dh_twitter_widget_cache', 'dh' );

    if ( ! is_array( $dh_twitter ) ) {
      $dh_twitter = array();
    }

    if ( ! is_array( $dh_twitter ) ||
         ! isset( $dh_twitter[$Q_legal_offset] ) ||
         ! isset( $dh_twitter[$Q_legal_offset]['timestamp'] ) ||
         time() > 60 + $dh_twitter[$Q_legal_offset]['timestamp'] ) {
      //$tweets = $connection->get("https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=" . urlencode($twitter_user) . "&count=" . max( $width, 3 ) );
      // $limits = $connection->get('https://api.twitter.com/1.1/application/rate_limit_status.json?resources=help,users,search,statuses');
      $tweets = $connection->get('https://api.twitter.com/1.1/search/tweets.json?q=' . $Q . '&count=' . max( $width, 3 ) )->statuses;

      // Clean the cache
      foreach ( $dh_twitter as $index => $twitter_query_cache ) {
        if ( ! isset( $twitter_query_cache['timestamp'] ) || time() > 60 + $twitter_query_cache['timestamp'] ) {
          unset( $dh_twitter[$index] );
        }
      }

      // Now the new cached query
      $dh_twitter[$Q_legal_offset] = array(
        'tweets' => $tweets,
        'timestamp' => time(),
      );

      wp_cache_set( 'dh_twitter_widget_cache', $dh_twitter, 'dh', time() * 2 );
    }
    else {
      // Use the cached tweets
      $tweets = $dh_twitter[$Q_legal_offset]['tweets'];
    }

    wp_enqueue_script(
      'dh-widget-twitter-feed',
      get_template_directory_uri().'/inc/widgets/widget_twitter.js',
      array(),'',
      TRUE
    );
    ?>
    <script type="text/javascript">
      var dhTwitterFeeds = <?php echo json_encode( array_slice( $tweets, 0, $width ) ); ?>;
      var dhTwitterImagesURI = "<?php echo get_template_directory_uri() . '/images/'; ?>";
    </script>
    <div class="grid-<?php echo $width . '-' . $row_width; ?>">
      <div class="block-twitter">
        <div id="twitter-feed"></div>
      </div>
    </div>
    <?php

    echo $args['after_widget'];
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
  function update( $new_instance, $instance, $fields = Array() ) {
    $instance = parent::update( $new_instance, $instance, array('width') );

    $instance['search_by']           = $new_instance['search_by'];
    $instance['twitter_user']        = $new_instance['twitter_user'];
    $instance['twitter_hashtag']     = $new_instance['twitter_hashtag'];
    $instance['consumer_key']        = $new_instance['consumer_key'];
    $instance['consumer_secret']     = $new_instance['consumer_secret'];
    $instance['access_token']        = $new_instance['access_token'];
    $instance['access_token_secret'] = $new_instance['access_token_secret'];

    return $instance;
  }

  /**
   * Display the form for this widget on the Widgets page of the Admin area.
   *
   * @param array $instance
   */
  function form( $instance, $fields = Array() ) {
    parent::form( $instance, array('width') );

    $search_by            = empty( $instance['search_by'] ) ? '' : $instance['search_by'];
    $twitter_user         = empty( $instance['twitter_user'] ) ? '' : $instance['twitter_user'];
    $twitter_hashtag      = empty( $instance['twitter_hashtag'] ) ? '' : $instance['twitter_hashtag'];
    $consumer_key         = empty( $instance['consumer_key'] ) ? '' : $instance['consumer_key'];
    $consumer_secret      = empty( $instance['consumer_secret'] ) ? '' : $instance['consumer_secret'];
    $access_token         = empty( $instance['access_token'] ) ? '' : $instance['access_token'];
    $access_token_secret  = empty( $instance['access_token_secret'] ) ? '' : $instance['access_token_secret'];

    ?>
      <p><label for="<?php echo esc_attr( $this->get_field_id( 'search_by' ) ); ?>"><?php _e( 'Search by:', 'dh' ); ?></label>
      <select id="<?php echo esc_attr( $this->get_field_id( 'search_by' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'search_by' ) ); ?>">
        <option value="1" <?php if ( $search_by == 1 ) { echo 'selected'; } ?>> Twitter User(s) </option>
        <option value="2" <?php if ( $search_by == 2 ) { echo 'selected'; } ?>> Twitter Hashtag </option>
      </select></p>

      <p><label for="<?php echo esc_attr( $this->get_field_id( 'twitter_user' ) ); ?>"><?php _e( 'Twitter User(s):', 'dh' ); ?></label>
      <input id="<?php echo esc_attr( $this->get_field_id( 'twitter_user' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'twitter_user' ) ); ?>" type="text" value="<?php echo esc_attr( $twitter_user ); ?>"/>
      <span>(As a comma-separated list)</span></p>

      <p><label for="<?php echo esc_attr( $this->get_field_id( 'twitter_hashtag' ) ); ?>"><?php _e( 'Twitter Hashtag:', 'dh' ); ?></label>
      <input id="<?php echo esc_attr( $this->get_field_id( 'twitter_hashtag' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'twitter_hashtag' ) ); ?>" type="text" value="<?php echo esc_attr( $twitter_hashtag ); ?>"/>
      <span>(Do NOT include '#')</span></p>

      <p><label for="<?php echo esc_attr( $this->get_field_id( 'consumer_key' ) ); ?>"><?php _e( 'Consumer Key:', 'dh' ); ?></label>
      <input id="<?php echo esc_attr( $this->get_field_id( 'consumer_key' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'consumer_key' ) ); ?>" type="text" value="<?php echo esc_attr( $consumer_key ); ?>"/></p>

      <p><label for="<?php echo esc_attr( $this->get_field_id( 'consumer_secret' ) ); ?>"><?php _e( 'Consumer Secret:', 'dh' ); ?></label>
      <input id="<?php echo esc_attr( $this->get_field_id( 'consumer_secret' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'consumer_secret' ) ); ?>" type="text" value="<?php echo esc_attr( $consumer_secret ); ?>"/></p>

      <p><label for="<?php echo esc_attr( $this->get_field_id( 'access_token' ) ); ?>"><?php _e( 'Access Token:', 'dh' ); ?></label>
      <input id="<?php echo esc_attr( $this->get_field_id( 'access_token' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'access_token' ) ); ?>" type="text" value="<?php echo esc_attr( $access_token ); ?>"/></p>

      <p><label for="<?php echo esc_attr( $this->get_field_id( 'access_token_secret' ) ); ?>"><?php _e( 'Access Token Secret:', 'dh' ); ?></label>
      <input id="<?php echo esc_attr( $this->get_field_id( 'access_token_secret' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'access_token_secret' ) ); ?>" type="text" value="<?php echo esc_attr( $access_token_secret ); ?>"/></p>
    <?php
  }
}