<?php

/**
 * Super widget for the DH Campaign theme
 * The width of each widget can be set to 1, 2 or 3 for a proper layout
 *
 * All DH Campaign widgets are inherited from this class
 */
class DH_Campaign_Super_Widget extends WP_Widget {

    /**
     * Constructor.
     */
    public function __construct( $id_base, $name, $widget_options = array(), $control_options = array() ) {
	parent::__construct( $id_base, $name, $widget_options, $control_options );
    }

    protected function get_title_display( $instance ) {
	return apply_filters( 'widget_title', empty( $instance[ 'title' ] ) ? '' : $instance[ 'title' ], $instance, $this->id_base );
    }

    protected function get_subtitle_display( $instance ) {
	return apply_filters( 'widget_subtitle', empty( $instance[ 'subtitle' ] ) ? '' : $instance[ 'subtitle' ], $instance, $this->id_base );
    }

    protected function get_text_display( $instance ) {
	return apply_filters( 'widget_text', empty( $instance[ 'text' ] ) ? '' : $instance[ 'text' ], $instance, $this->id_base );
    }

    protected function get_section_id( $instance ) {
	return apply_filters( 'widget_section_id', empty( $instance[ 'section_id' ] ) ? '' : $instance[ 'section_id' ], $instance, $this->id_base );
    }

    protected function get_height( $instance ) {
	$height = isset( $instance[ 'height' ] ) && in_array( $instance[ 'height' ], array( 'short', 'medium', 'tall' ) ) ? $instance[ 'height' ] : 'short';
	return $height;
    }

    protected function get_width( $instance ) {
	$width = isset( $instance[ 'width' ] ) && in_array( $instance[ 'width' ], array( 'half', 'full' ) ) ? $instance[ 'width' ] : 'half';
	return $width;
    }

    protected function get_row_width( $instance ) {
	$row_width = 3;
	if ( isset( $instance[ 'dh_row_width' ] ) ) {
	    $row_width = $instance[ 'dh_row_width' ];
	}
	return $row_width;
    }

    protected function get_background( $instance ) {
	$background = isset( $instance[ 'background' ] ) && in_array( $instance[ 'background' ], array( 'coloured', 'white', 'border' ) ) ? $instance[ 'background' ] : 'coloured';
	return $background;
    }

    protected function get_cta_text( $instance ) {
	if ( isset( $instance[ 'cta_text' ] ) ) {
	    $cta_text = $instance[ 'cta_text' ];
	}
	return $cta_text;
    }

    protected function get_cta_link( $instance ) {
	if ( isset( $instance[ 'cta_section_link' ] ) ) {
	    $link = '#' . $instance[ 'cta_section_link' ];
	} elseif ( isset( $instance[ 'cta_link' ] ) ) {
	    $link = $instance[ 'cta_link' ];
	}
	return $link;
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
	$title		 = get_title_display( $instance );
	$width		 = get_width( $instance );
	$row_width	 = get_row_width( $instance );
	$section_id	 = get_section_id( $instance );

	echo $args[ 'before_widget' ];
	?>
	<div section id="<?php echo $section_id; ?>">
	    <div class="grid-<?php echo $width . '-' . $row_width; ?>">
		<h2><?php echo $title; ?></h2>
	    </div>
	</div>  
	<?php
	echo $args[ 'after_widget' ];
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
    function update( $new_instance, $instance, $fields = array( 'title', 'width', 'text', 'section_id', 'subtitle', 'height', 'background', 'cta' ) ) {

	if ( in_array( 'title', $fields ) ) {
	    if ( ! empty( $new_instance[ 'title' ] ) ) {
		$instance[ 'title' ] = strip_tags( $new_instance[ 'title' ] );
	    } else {
		$instance[ 'title' ] = NULL;
	    }
	}

	if ( in_array( 'subtitle', $fields ) ) {
	    if ( ! empty( $new_instance[ 'subtitle' ] ) ) {
		$instance[ 'subtitle' ] = strip_tags( $new_instance[ 'subtitle' ] );
	    } else {
		$instance[ 'subtitle' ] = NULL;
	    }
	}

	if ( in_array( 'text', $fields ) ) {
	    if ( ! empty( $new_instance[ 'text' ] ) ) {
		$instance[ 'text' ] = strip_tags( $new_instance[ 'text' ] );
	    } else {
		$instance[ 'text' ] = NULL;
	    }
	}

	if ( in_array( 'section_id', $fields ) ) {
	    if ( ! empty( $new_instance[ 'section_id' ] ) ) {
		$instance[ 'section_id' ] = strip_tags( $new_instance[ 'section_id' ] );
	    } else {
		$instance[ 'section_id' ] = NULL;
	    }
	}

	if ( in_array( 'height', $fields ) ) {
	    if ( ! empty( $new_instance[ 'height' ] ) ) {
		if ( in_array( $new_instance[ 'height' ], array( 'short', 'medium', 'tall' ) ) ) {
		    $instance[ 'height' ] = $new_instance[ 'height' ];
		}
	    } else {
		$instance[ 'height' ] = NULL;
	    }
	}

	if ( in_array( 'width', $fields ) ) {
	    if ( ! empty( $new_instance[ 'width' ] ) ) {
		if ( in_array( $new_instance[ 'width' ], array( 'half', 'full' ) ) ) {
		    $instance[ 'width' ] = $new_instance[ 'width' ];
		}
	    } else {
		$instance[ 'width' ] = NULL;
	    }
	}

	if ( in_array( 'background', $fields ) ) {
	    if ( ! empty( $new_instance[ 'background' ] ) ) {
		if ( in_array( $new_instance[ 'background' ], array( 'coloured', 'white', 'border' ) ) ) {
		    $instance[ 'background' ] = $new_instance[ 'background' ];
		}
	    } else {
		$instance[ 'background' ] = NULL;
	    }
	}

	if ( in_array( 'cta', $fields ) ) {
	    if ( ! empty( $new_instance[ 'cta_text' ] ) ) {
		$instance[ 'cta_text' ] = strip_tags( $new_instance[ 'cta_text' ] );
	    } else {
		$instance[ 'cta_text' ] = NULL;
	    }

	    if ( ! empty( $new_instance[ 'cta_link' ] ) ) {
		$instance[ 'cta_link' ] = esc_url( $new_instance[ 'cta_link' ] );
	    } else {
		$instance[ 'cta_link' ] = NULL;
	    }

	    if ( ! empty( $new_instance[ 'cta_section_link' ] ) ) {
		$instance[ 'cta_section_link' ] = strip_tags( $new_instance[ 'cta_section_link' ] );
	    } else {
		$instance[ 'cta_section_link' ] = NULL;
	    }
	}

	return $instance;
    }

    /**
     * Display the form for this widget on the Widgets page of the Admin area.
     *
     * @param array $instance
     */
    function form( $instance, $fields = array( 'title', 'width' ) ) {

	if ( in_array( 'width', $fields ) ) {
	    $width = isset( $instance[ 'width' ] ) && in_array( $instance[ 'width' ], array( 'half', 'full' ) ) ? $instance[ 'width' ] : 'half';
	    ?>
	    <p><label for="<?php echo esc_attr( $this->get_field_id( 'width' ) ); ?>"><?php _e( 'Width:', 'dh' ); ?></label>
	        <select id="<?php echo esc_attr( $this->get_field_id( 'width' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'width' ) ); ?>">
		    <?php foreach ( array( 'half', 'full' ) as $slug ) : ?>
			<option value="<?php echo $slug; ?>"<?php selected( $width, $slug ); ?>><?php echo $slug; ?></option>
		    <?php endforeach; ?>
	        </select>
	        </br>Select the width of this widget, by default it is half width.
	    </p>
	    <?php
	}

	if ( in_array( 'height', $fields ) ) {
	    $height = isset( $instance[ 'height' ] ) && in_array( $instance[ 'height' ], array( 'short', 'medium', 'tall' ) ) ? $instance[ 'height' ] : 'short';
	    ?>
	    <p><label for="<?php echo esc_attr( $this->get_field_id( 'height' ) ); ?>"><?php _e( 'Height:', 'dh' ); ?></label>
	        <select id="<?php echo esc_attr( $this->get_field_id( 'height' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'height' ) ); ?>">
		    <?php foreach ( array( 'short', 'medium', 'tall' ) as $slug ) : ?>
			<option value="<?php echo $slug; ?>"<?php selected( $height, $slug ); ?>><?php echo $slug; ?></option>
		    <?php endforeach; ?>
	        </select>
	        </br>Selecting the height will determine what size of image is used for this widget.
	    </p>
	    <?php
	}

	if ( in_array( 'section_id', $fields ) ) {
	    $section_id = empty( $instance[ 'section_id' ] ) ? '' : esc_attr( $instance[ 'section_id' ] );
	    ?>
	    <p><label for="<?php echo esc_attr( $this->get_field_id( 'section_id' ) ); ?>"><?php _e( 'Section ID:', 'dh' ); ?></label>
	        <input id="<?php echo esc_attr( $this->get_field_id( 'section_id' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'section_id' ) ); ?>" type="text" value="<?php echo esc_attr( $section_id ); ?>">
		<?php
		if ( isset( $fields[ 'section_id help' ] ) ) {
		    echo '<br>(' . $fields[ 'section_id help' ] . ')';
		}
		?>
	        </br>Enter an ID of your choice to identify this widget's position/section on the page (this is used for in-page section links).
	    </p>
	    <?php
	}

	if ( in_array( 'title', $fields ) ) {
	    $title = empty( $instance[ 'title' ] ) ? '' : esc_attr( $instance[ 'title' ] );
	    ?>
	    <p><label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title:', 'dh' ); ?></label>
	        <input id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		<?php
		if ( isset( $fields[ 'title help' ] ) ) {
		    echo '<br>(' . $fields[ 'title help' ] . ')';
		}
		?>
	    </p>
	    <?php
	}

	if ( in_array( 'subtitle', $fields ) ) {
	    $subtitle = empty( $instance[ 'subtitle' ] ) ? '' : esc_attr( $instance[ 'subtitle' ] );
	    ?>
	    <p><label for="<?php echo esc_attr( $this->get_field_id( 'subtitle' ) ); ?>"><?php _e( 'Subtitle:', 'dh' ); ?></label>
	        <input id="<?php echo esc_attr( $this->get_field_id( 'subtitle' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'subtitle' ) ); ?>" type="text" value="<?php echo esc_attr( $subtitle ); ?>">
		<?php
		if ( isset( $fields[ 'subtitle help' ] ) ) {
		    echo '<br>(' . $fields[ 'subtitle help' ] . ')';
		}
		?></p>
	    <?php
	}

	if ( in_array( 'text', $fields ) ) {
	    $text = empty( $instance[ 'text' ] ) ? '' : $instance[ 'text' ];
	    ?>
	    <p><label for="<?php echo esc_attr( $this->get_field_id( 'text' ) ); ?>"><?php _e( 'Text:', 'dh' ); ?></label>
	        <textarea id="<?php echo esc_attr( $this->get_field_id( 'text' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'text' ) ); ?>" rows="8"><?php echo esc_textarea( $text ); ?></textarea>
	    </p>
	    <?php
	}

	if ( in_array( 'cta', $fields ) ) {

	    $cta_text		 = empty( $instance[ 'cta_text' ] ) ? '' : $instance[ 'cta_text' ];
	    $cta_link		 = empty( $instance[ 'cta_link' ] ) ? '' : $instance[ 'cta_link' ];
	    $cta_section_link	 = empty( $instance[ 'cta_section_link' ] ) ? '' : $instance[ 'cta_section_link' ];
	    ?>
	    <fieldset>
	        <legend><h2>CTA</h2></legend>
	        <p>
	    	<label for="<?php echo esc_attr( $this->get_field_id( 'cta_text' ) ); ?>"><?php _e( 'CTA text:', 'dh' ); ?></label>
	    	<input id="<?php echo esc_attr( $this->get_field_id( 'cta_text' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'cta_text' ) ); ?>" type="text" value="<?php echo esc_attr( $cta_text ); ?>"><br>This text will appear inside the CTA button.
	        </p>

	        <p>
	    	<label for="<?php echo esc_attr( $this->get_field_id( 'cta_link' ) ); ?>"><?php _e( 'CTA link:', 'dh' ); ?></label>
	    	<input id="<?php echo esc_attr( $this->get_field_id( 'cta_link' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'cta_link' ) ); ?>" type="text" value="<?php echo esc_attr( $cta_link ); ?>"><br>Enter the full URL you wish this CTA to link to.
	        </p>
	        <p>
	    	<label for="<?php echo esc_attr( $this->get_field_id( 'cta_section_link' ) ); ?>"><?php _e( 'CTA in-page section link:', 'dh' ); ?></label>
	    	<input id="<?php echo esc_attr( $this->get_field_id( 'cta_section_link' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'cta_section_link' ) ); ?>" type="text" value="<?php echo esc_attr( $cta_section_link ); ?>"><br>Or Enter the section ID you wish this CTA to link to on this page (in-page section link has priority over a normal link).
	        </p>
	        <p>For any CTA's to display you must add the CTA text and either a link or in-page section link.</p>
	    </fieldset> <?php
	}

	if ( in_array( 'background', $fields ) ) {
	    $background = isset( $instance[ 'background' ] ) && in_array( $instance[ 'background' ], array( 'coloured', 'white', 'border' ) ) ? $instance[ 'background' ] : 'coloured';
	    ?>
	    <p><label for="<?php echo esc_attr( $this->get_field_id( 'background' ) ); ?>"><?php _e( 'Background:', 'dh' ); ?></label>
	        <select id="<?php echo esc_attr( $this->get_field_id( 'background' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'background' ) ); ?>">
		    <?php foreach ( array( 'coloured', 'white', 'border' ) as $slug ) : ?>
			<option value="<?php echo $slug; ?>"<?php selected( $background, $slug ); ?>><?php echo $slug; ?></option>
		    <?php endforeach; ?>
	        </select></br>The 'coloured' background is the default option, if the option to add an image is used then the image will be given priority as the background.</p>
	    <?php
	}
    }

}
