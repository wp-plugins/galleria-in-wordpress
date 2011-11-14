<?php
/*
Plugin Name: Galleria in WordPress
Description: Incorporate Galleria into WordPress easily! Simply create Galleries and add images. 
Author: Eric Lewis
Version: 1.0.1
Author URI: http://www.ericandrewlewis.com/

*/

class Galleria_in_WordPress {

	var $pluginDirectoryLocalPath;
	var $Galleria_for_WordPress_Admin;
	var $Galleria_for_WordPress_Display;

    function __construct() {
    	add_action('init', array(&$this, 'register_post_types') );
        $this->pluginDirectoryLocalPath = dirname(__FILE__);

        require_once( $this->pluginDirectoryLocalPath . "/classes-galleria-admin.php" );
        require_once( $this->pluginDirectoryLocalPath . "/classes-galleria-display.php" );
        
        $this->Galleria_for_WordPress_Admin = new Galleria_for_WordPress_Admin;
        $this->Galleria_for_WordPress_Display = new Galleria_for_WordPress_Display;
    }


	function register_post_types() {

	    $labels = array(
	        'name' => _x('Galleries', 'post type general name'),
	        'singular_name' => _x('Gallery', 'post type singular name'),
	        'add_new' => _x('Add New', 'gallery'),
	        'add_new_item' => __('Add New Gallery'),
	        'edit_item' => __('Edit Gallery'),
	        'new_item' => __('New Gallery'),
	        'all_items' => __('All Galleries'),
	        'view_item' => __('View Gallery'),
	        'search_items' => __('Search Galleries'),
	        'not_found' =>  __('No galleries found'),
	        'not_found_in_trash' => __('No galleries found in Trash'), 
	        'parent_item_colon' => '',
	        'menu_name' => 'Galleries'
	    );

	    $args = array(
	        'labels' => $labels,
	        'public' => true,
	        'publicly_queryable' => true,
	        'show_ui' => true, 
	        'show_in_menu' => true, 
	        'query_var' => true,
	        'rewrite' => false,
	        'capability_type' => 'post',
	        'has_archive' => false, 
	        'hierarchical' => false,
	        'menu_position' => null,
	        'supports' => array('title', 'thumbnail')
	    ); 
	    register_post_type('gallery', $args);
	}
}

$Galleria_in_WordPress = new Galleria_in_WordPress;

?>