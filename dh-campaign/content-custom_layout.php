<?php
/**
 * The default template for displaying custom layout content
 *
 */
$heading		 = (get_field( 'dh_campaign_intro_heading' ) ? get_field( 'dh_campaign_intro_heading' ) : get_field( 'dh_campaign_hero_heading' ));
$subheading		 = (get_field( 'dh_campaign_intro_subheading' )) ? get_field( 'dh_campaign_intro_subheading' ) : get_field( 'dh_campaign_hero_subheading' );
$intro_text		 = get_field( 'dh_campaign_intro_text' );
$intro_image		 = (get_field( 'dh_campaign_intro_image' ) ? get_field( 'dh_campaign_intro_image' ) : get_field( 'dh_campaign_hero_image' ));
$video			 = get_field( 'dh_campaign_youtube_video' );
$intro_text_col_width	 = empty( $intro_image ) && empty( $video ) ? 12 : 7;
$url			 = get_permalink();
?>


<?php if ( is_search() ) : ?>
    <div class="entry-summary">
	<?php the_excerpt(); ?>
    </div><!-- .entry-summary -->
<?php else : ?>

    <div class="section" id="start">
        <div class="container">
    	<div class="row">
    	    <div class="col-md-<?php echo $intro_text_col_width; ?>">
    		<h1><?php print $heading; ?><small><?php echo $subheading; ?></small></h1>

		    <?php if ( isset( $intro_text ) ) : ?>
			<p class="lead"><?php echo $intro_text; ?></p>
		    <?php endif; ?>

		    <?php if ( get_field( 'dh_campaign_cta_text' ) ) : ?>
			<p>
			    <a class="btn" href="#<?php the_field( 'dh_campaign_cta_inpage_section_link' ); ?>" data-scroll><?php the_field( 'dh_campaign_cta_text' ); ?></a>
			</p>
		    <?php endif; ?>

    	    </div>
		<?php if ( $video ) : ?>
		    <div class="col-md-5">
			<div class="videoContainer">
			    <?php echo wp_oembed_get( $video ); ?>
			</div>
		    </div>
		<?php elseif ( $intro_image ) : ?>
		    <div class="col-md-5">
			<p><img class="img-responsive" src="<?php echo $intro_image[ 'url' ]; ?>" alt="<?php echo $intro_image[ 'alt' ]; ?>" title="<?php echo $intro_image[ 'title' ]; ?>" width="483" height="363"></p>
		    </div>
		<?php endif; ?>

    	</div>
	    <?php if ( get_field( 'dh_campaign_display_share_buttons' ) ) : ?>
		<div class="row">
		    <div class="col-md-12">
			<h5>Like/share</h5>
			<ul class="social">
			    <li><a class="social__link social__link--twitter" href="//twitter.com/intent/tweet?status=<?php echo $heading . ':' . $subheading; ?>+<?php echo $url; ?>"><i class="fa fa-twitter"></i></a>
			    <li><a class="social__link social__link--google" href="//plus.google.com/share?url=<?php echo $url; ?>"><i class="fa fa-google-plus"></i></a>
			    <li><a class="social__link social__link--facebook" href="//www.facebook.com/sharer/sharer.php?u=<?php echo $url; ?>/&title=<?php echo $heading . ':' . $subheading; ?>"><i class="fa fa-facebook"></i>				    </a>
			    <li><a class="social__link social__link--pinterest" href="//pinterest.com/pin/create/bookmarklet/?media=<?php echo $intro_image[ 'url' ]; ?>&url=<?php echo $url; ?>/&is_video=false&description=<?php				    echo $heading . ':' . $subheading; ?>"><i class="fa fa-pinterest"></i></a>
			    <li><a class="social__link social__link--email" href="mailto:?subject=<?php echo $heading . ':' . $subheading; ?>&body=<?php echo $url; ?>"><i class="fa fa-envelope"></i></a>
			</ul>
		    </div>
		</div>
	    <?php endif; ?>

	    <?php if ( get_field( 'dh_campaign_section_id' ) ) : ?>
		<div class="text-center">
		    <a class="scroll-btn scroll-btn--dark scroll-btn--relative" href="#<?php the_field( 'dh_campaign_section_id' ); ?>" data-scroll>
			<?php the_field( 'dh_campaign_scroll_text' ); ?>
			<i class="fa fa-chevron-down"></i>
		    </a>
		</div>
	    <?php endif; ?>

        </div>
    </div>

    <?php
    echo _dh_campaign_siteorigin_panels_render();
    ?>
<?php endif; ?>