<?php
/*
Plugin Name: Gallery Without the Fluff
Plugin URI: http://www.reviveweb.com.au
Description: Adds Gallery management functionality to WordPress, still using the built in Media Uploader & Library. For people who want to use their own jQuery script to create the frontend gallery or slider.
Version: 1.0
Author: Justyn Walker @ revive web
Author URI: http://www.reviveweb.com.au
License: GPL2.
*/
/**
 * PLUGIN BASE SETUP FILE
 * It all starts here!
 * It defines constants for file paths and urls to the plugin files.
 * It adds the necessary <head> scripts and stylesheets for the front end
 * It includes all the major plugin files
 * Using "fluff" as a prefix for everything
 *
 * To understand how the drag and drop works try this tutorial... http://pippinsplugins.com/drag-and-drop-order-for-plugin-options/
*/

#--- GET PLUGIN PATHS
// gal plugin folder by file path (for php includes) and url
define('FLUFF_PLUGIN_DIR', plugin_dir_path(__FILE__) );
define('FLUFF_PLUGIN_URL', plugin_dir_url(__FILE__) );
// php files
define('FLUFF_PHP_DIR', FLUFF_PLUGIN_DIR.'php/');
// javascript
define('FLUFF_JS', FLUFF_PLUGIN_URL.'js/');
// css
define('FLUFF_CSS', FLUFF_PLUGIN_URL.'css/');
// images
define('FLUFF_IMAGES', FLUFF_PLUGIN_URL.'images/');
// site url
define('FLUFF_URL', get_bloginfo('url') );

// define for the menu screen
global $fluff_menu_screen;


#--- ADD FRONTEND CSS
add_action( 'wp_enqueue_scripts', 'fluff_register_stylesheet' );
function fluff_register_stylesheet() {
    wp_register_style( 'gal-style', FLUFF_CSS.'frontend.css' );
    wp_enqueue_style( 'gal-style' );
}

#--- ON PLUGIN ACTIVATION
register_activation_hook( __FILE__, 'fluff_update_default_options' );

// SETUP DEFAULT OUTPUT OPTIONS
function fluff_update_default_options(){
    // get options
    $options = fluff_get_default_options();
    // add/update options with default values
    update_option( 'fluff_before_loop', $options['before_loop'] );
    update_option( 'fluff_in_loop', $options['in_loop'] );
    update_option( 'fluff_after_loop', $options['after_loop'] );
}


#--- ADMIN ONLY
if( is_admin ){ // only load if in dashboard
    
    #--- HELP TABS
    include_once( FLUFF_PHP_DIR.'help.php');
    
    #--- ADD GALLERY MENU & SCREEN
    include_once( FLUFF_PHP_DIR.'menu.php');
    
    #--- LOAD SCRIPTS
    include_once( FLUFF_PHP_DIR.'scripts.php');
    
    #--- GET AJAX POST & UPDATE DATABASE
    include_once( FLUFF_PHP_DIR.'update-order-option.php');
    
    #--- GENERAL & ADMIN FUNCTIONS
    include_once( FLUFF_PHP_DIR.'admin-functions.php');
}


#--- GENERAL & ADMIN FUNCTIONS
include_once( FLUFF_PHP_DIR.'functions.php');

#--- OUTPUT GALLERY FUNCTION
include_once( FLUFF_PHP_DIR.'output.php');

#--- DEFAULT GALLERY
include_once( FLUFF_PHP_DIR.'output-default.php');

?>