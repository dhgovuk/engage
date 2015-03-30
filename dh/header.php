<?php
/**
 * The Header for our theme
 */
?><!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) & !(IE 8)]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>">
  <meta name="viewport" content="width=device-width">
  <title><?php wp_title( '|', true, 'right' ); ?></title>
  <link rel="profile" href="http://gmpg.org/xfn/11">
  <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

  <!--[if lt IE 9]>
  <script src="<?php echo get_template_directory_uri(); ?>/js/html5.js"></script>
  <![endif]-->
  <?php wp_head(); ?>
  <?php
  $colour_schemes = dh_colour_schemes();
  $current_colour_scheme = $colour_schemes[get_theme_mod('dh_colour_scheme', 1)];
  $colour_primary = $current_colour_scheme['primary'];
  $colour_secondary = $current_colour_scheme['secondary'];
  ?>
  <style type="text/css">
    <?php include get_template_directory() . '/colour-css.php'; ?>
  </style>
  <script type="text/javascript">
    var ajaxUrl = '<?php echo admin_url('admin-ajax.php'); ?>';
  </script>
</head>

<body <?php body_class(); ?>>
<div class="header-wrap">
  <header>
    <span class="crest"><img src="<?php echo get_template_directory_uri(); ?>/images/crests/org_crest_white_27px.png" alt="crest"></span>
    <span class="dep-title"><a href="https://www.gov.uk/dh">Department of Health</a></span>

    <?php if ( get_theme_mod( 'dh_newsletter_header' ) ): ?>
      <?php
      $form_style = '';
      $input_style = '';
      $email = '';
      $message = '';

      if ( isset( $_POST['submitted'] ) &&  $_POST['submitted'] == 'newsletter-form-header' ) {
        $email = strtolower( trim( isset( $_POST['alert-email'] ) ? $_POST['alert-email'] : '') );
        if ( filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
          //SAVE
          global $wpdb;
          $wpdb->prefix . "dh_newsletter_subscriptions";
          $result = $wpdb->get_results( "SELECT * FROM " . $wpdb->prefix . "dh_newsletter_subscriptions WHERE email = '" . $email . "'" );
          if ( empty( $result ) ) {
            $result = $wpdb->insert(
                $wpdb->prefix . 'dh_newsletter_subscriptions',
                array( 'status' => 1, 'email' => $email, 'time' => current_time( 'mysql' ) ) );
          }
          $message = '<p>Thank you for subscribing!</p>';
          $email = '';
        }
        else {
          // error
          $message = '<p style="color:red;">Please enter a valid e-mail.</p>';
          $input_style = ' style="border:1px solid red;"';
          $form_style = ' style="display:block;"';
        }
      }
      ?>
      <a href="/newsletter" id="newsletter" class="email-signup">Sign up for newsletter</a>

      <div class="signupform" id="newsletterform" <?php echo $form_style; ?>>
        <span class="close">X</span>
        <p>To sign up for updates please enter your email address below.</p>
        <form id="newsletter-form-header" accept-charset="UTF-8" action="https://public.govdelivery.com/accounts/UKDH/subscribers/qualify" method="post">
          <div style="margin:0;padding:0;display:inline">
            <input name="utf8" type="hidden" value="✓" />
            <input name="authenticity_token" type="hidden" value="fpDUF0E54P0eIsD2Jd0BYQW8QFnOAs/39gdiuVAk8A4=" />
          </div>
          <input id="topic_id" name="topic_id" type="hidden" value="<?php echo esc_attr(get_theme_mod( 'dh_newsletter_header_topic_id' )); ?>" />
          <input class="long" id="email" name="email" type="text" placeholder="Your email address" />
          <input class="signup" name="commit" type="submit" value="Submit" />
        </form>
      </div>
    <?php endif; ?>

  </header>
</div>
<div class="container">
  <div id="page" class="page">
    <section class="section-row">
      <div class="section-wrap">
        <div class="grid-2-3">
          <?php if ( get_bloginfo( 'description', 'display' ) ): ?><div class="text-thin subheader"> <?php bloginfo( 'description' ); ?> </div><?php endif; ?>
          <div class="page-title"> <a href="<?php echo get_site_url(); ?>"><?php bloginfo( 'name' ); ?> </a></div>
        </div>
        <?php if ( get_theme_mod('dh_search_header') ): ?>
        <div class="grid-1-3">
          <?php //get_search_form(); ?>
          <form action="<?php echo get_site_url(); ?>" id="search" role="search" method="get" class="search-box search-form">
            <input type="text" placeholder="Search" name="s">
            <input type="submit" class="search-btn">
          </form>
        </div>
        <?php endif; ?>
      </div>
      <nav>
        <?php if ( has_nav_menu( 'primary' ) ) : ?>
          <?php wp_nav_menu( array( 'theme_location' => 'primary', 'menu_class' => 'main-menu', 'container' => FALSE, 'walker' => new DH_Walker_Nav_Menu() ) ); ?>
        <?php endif; ?>
      </nav>
    </section>
    <?php get_sidebar( 'top' ); ?>