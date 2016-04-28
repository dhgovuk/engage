<?php
/**
 * The Header for our theme
 */
$bloginfo			 = get_bloginfo( 'name', 'show' );
$twitter_publisher_handle	 = get_theme_mod( 'dh_campaign_twitter_id' );
$facebook_id			 = get_theme_mod( 'dh_campaign_facebook_id' );
$url				 = get_permalink();
$hero_image			 = get_field( 'dh_campaign_hero_image' );
$title				 = wp_strip_all_tags( get_the_title() );
$excerpt			 = wp_strip_all_tags( get_the_excerpt() );
$intro_text			 = wp_strip_all_tags( get_field( 'dh_campaign_intro_text' ) );
$intro_image			 = (get_field( 'dh_campaign_intro_image' ) ? get_field( 'dh_campaign_intro_image' ) : get_field( 'dh_campaign_hero_image' ));
?>
<!DOCTYPE html>
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
        <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

        <link rel="apple-touch-icon" href="apple-touch-icon.png">
        <!-- Place favicon.ico in the root directory -->

        <!-- Twitter Card data -->
        <meta name="twitter:site" content="<?php echo $twitter_publisher_handle; ?>">
        <meta name="twitter:card" content="<?php echo $intro_text; ?>">
        <meta name="twitter:title" content="<?php echo $title; ?>">
        <meta name="twitter:description" content="<?php echo $excerpt; ?>">
        <!-- Twitter Summary card images must be at least 120x120px -->
        <meta name="twitter:image" content="<?php echo $intro_image[ 'url' ]; ?>">

        <!-- Open Graph data -->
        <meta property="og:title" content="<?php echo $title; ?>" />
        <meta property="og:type" content="website" />
        <meta property="og:url" content="<?php echo $url; ?>" />
        <meta property="og:image" content="<?php echo $intro_image[ 'url' ]; ?>" />
        <meta property="og:description" content="<?php echo $intro_text; ?>" />
        <meta property="og:site_name" content="<?php echo $bloginfo; ?>" />
        <meta property="fb:admins" content="<?php echo $facebook_id; ?>" />

        <!--[if lt IE 9]>
        <script src="<?php echo get_stylesheet_directory_uri(); ?>/js/html5.js"></script>
        <![endif]-->
        
          <?php
          $ga_code			 = get_theme_mod( 'dh_ga_code' ); //@TODO get campaign analytics code
          if ( ! empty( $ga_code ) ) {
              ?>
              <script>
                  (function (i, s, o, g, r, a, m) {
                i['GoogleAnalyticsObject'] = r;
                i[r] = i[r] || function () {
                    (i[r].q = i[r].q || []).push(arguments)
                }, i[r].l = 1 * new Date();
                a = s.createElement(o),
                  m = s.getElementsByTagName(o)[0];
                a.async = 1;
                a.src = g;
                m.parentNode.insertBefore(a, m)
                  })(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');

                  ga('create', '<?php echo $ga_code; ?>', 'auto');
                  ga('send', 'pageview');
              </script>
              <?php
          }
          ?>
      
	<?php wp_head(); ?>

        <script type="text/javascript">
	    var ajaxUrl = '<?php echo admin_url( 'admin-ajax.php' ); ?>';
        </script>

        <!-- Begin Cookie Consent plugin by Silktide - http://silktide.com/cookieconsent -->
        <script type="text/javascript">
            window.cookieconsent_options = {"message":"This website uses cookies to ensure you get the best experience on our website.","dismiss":"Got it","learnMore":"Find out more about cookies","link":"https://engage.dh.gov.uk/dhpolicies/cookie-policy/","theme":"dark-top"};
        </script>

        <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/cookieconsent2/1.0.9/cookieconsent.min.js"></script>
        <!-- End Cookie Consent plugin -->
      
    </head>
    <body <?php body_class(); ?>>

	<?php if ( ! empty( $hero_image[ 'url' ] ) ) : ?>
    	<div class="hero" style="background-image: url(<?php echo $hero_image[ 'url' ]; ?>);">
    	    <a href="/">
    		<div class="hero__logo"><h1 class="visuallyhidden">Department of Health</h1></div>
    	    </a>
    	    <div class="hero__inner">
    		<div class="container">
    		    <h1 class="hero__header"><?php the_field( 'dh_campaign_hero_heading' ); ?><span><?php the_field( 'dh_campaign_hero_subheading' ); ?></span></h1>

    		    <a class="scroll-btn" data-scroll href="#start">
    			Scroll
    			<i class="fa fa-chevron-down"></i>
    		    </a>
    		</div>
    	    </div>
    	</div>
	<?php endif; ?>

        <div class="navbar navbar--static-top" role="banner">
            <div class="container"> 
                <div class="navbar__header">
                    <button class="navbar__toggle  collapsed" data-toggle="collapse" data-target="#navbar" aria-controls="navbar" aria-expanded="false" type="button">
                        <span class="toggle-label">Menu</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar__brand" href="/">
                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/logo.png" alt="Department of Health" width="88" height="58.5">
                    </a>
                </div>
                <nav class="navbar__collapse  collapse" id="navbar">

		    <?php if ( has_nav_menu( 'primary' ) ) : ?>
			<?php wp_nav_menu( array( 'theme_location' => 'primary', 'depth' => 1, 'menu_class' => 'nav nav--justified  navbar__nav', 'container' => FALSE, 'walker' => new DH_Campaign_Walker_Nav_Menu() ) ); //				container set to false else starting of container shows as html ?>
		    <?php endif; ?>

                </nav>
            </div>
        </div>
