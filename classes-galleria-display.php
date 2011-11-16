<?php

class Galleria_in_WordPress_Display {

	var $instances;

	function __construct() {
		$this->instances = 1;
        add_action( 'wp_enqueue_scripts', array(&$this, 'register_galleria') );
    	add_shortcode( 'galleria', array(&$this, 'handle_shortcode') );
    	add_action( 'wp_footer', array(&$this, 'intialize_galleria') );
    	wp_enqueue_script('jquery');
    }

    function register_galleria() {

    	$pluginDirectoryLocalPath = dirname(__FILE__);
    	wp_register_script( 'galleria', WP_PLUGIN_URL . "/galleria-in-wordpress/galleria/galleria-1.2.5.min.js");
    	wp_enqueue_script( 'galleria' );

    }

    function handle_shortcode($atts) {
    	global $wpdb;


    	$defaults = array(
	    	'width' => '100%',
	    	'height' => '500px'
	    );

	    $defaults = wp_parse_args($atts, $defaults);

	    $atts['name'] = htmlentities($atts['name']);
	    $post_id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_title = %s AND post_type='gallery'", $atts['name'] ));

        // if ( $post )
        //     return get_post($post, $output);

    	$children = get_post_meta($post_id, '_galleria_for_wp_image_id');
		if ( $children ) {
			$args = array( 'post_type' => 'attachment', 'post__in' => $children, 'posts_per_page' => '-1' );
			$attachments = get_posts($args);
		}
		else
			return;
		$content = '<div id="galleria-' . $this->instances . '" style="width:'.$defaults['width'] .'; height: '.$defaults['height'] .'">';
		foreach( $attachments as $attachment ) :
			$large_url = wp_get_attachment_image_src($attachment->ID, 'large' );
			$large_url = $large_url[0];
			
	    	$content .= '<img title="' . $attachment->post_title .'"
            	     alt="' . $attachment->post_excerpt .  '"
            	     src="' . $large_url .'">';
        endforeach;
        $content .= '</div>';
        $this->instances++;
    	return $content;
    }

    function intialize_galleria() {
    	if($this->instances == 1 )
    		return;
    	?><script>
	    (function($) {
	    	$(document).ready( function() { 
	    		Galleria.loadTheme('<?php echo WP_PLUGIN_URL . "/galleria-in-wordpress/galleria/themes/classic/"; ?>galleria.classic.min.js'); <?php
		    	for($i=1 ; $i < $this->instances ; $i++) :
					?>$('#galleria-<?php echo $i; ?>').galleria();
					<?php
		    	endfor; ?>
	    	});
	    	
    	})(jQuery);
    	</script><?php
    }

}