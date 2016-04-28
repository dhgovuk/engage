<?php
/**
 * The template for displaying the footer
 *
 * Contains footer content and the closing of the #main and #page div elements.
 */
$twitter = get_theme_mod('dh_campaign_twitter_link');
$fb = get_theme_mod('dh_campaign_fb_link');
$yt = get_theme_mod('dh_campaign_yt_link');
?>

<footer class="footer" role="contentinfo">
    <div class="container">
        <a class="footer__brand  pull-left" href="/">
            <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/footer-logo.png" alt="Department of Health" width="161" height="105">
        </a>  
      
      <?php if ( has_nav_menu( 'footer' ) ) : ?>
        <?php wp_nav_menu( array( 'theme_location' => 'footer', 'depth' => 1, 'menu_class' => 'footer__links', 'container' => FALSE, 'walker' => new DH_Campaign_Walker_Nav_Menu() ) ); // Container set to false else starting of container shows as html ?>
      <?php endif; ?>

        <ul class="footer__social  pull-right">
            <?php if (!empty($twitter)) : ?>
              <li><a href="<?php echo $twitter; ?>"><i class = "fa fa-twitter-square"></i></a>
                <?php endif; ?>
                <?php if (!empty($fb)) : ?>
              <li><a href="<?php echo $fb; ?>"><i class = "fa fa-facebook-square"></i></a>
                <?php endif; ?>
                <?php if (!empty($yt)) : ?>
              <li><a href="<?php echo $yt; ?>"><i class = "fa fa-youtube-square"></i></a>
                    <?php endif; ?>
        </ul>
    </div>
</footer>

<?php wp_footer();
?>
</body>
</html>