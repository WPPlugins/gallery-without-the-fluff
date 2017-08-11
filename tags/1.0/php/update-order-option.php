<?php
/* PROCESS AJAX */

// wp_ajax_(ajax action in drag-and-drop.js)
add_action('wp_ajax_fluff_update_order', 'fluff_save_order');

/**
 * SAVE NEW MENU ORDER
 * -----------------
 * Gets post ids for this gallery in the last saved menu order (admin-functions.php)
 * Gets the new ajax menu order from post in drag-and-drop.js
 * Uses the values of list_items to match the images with the new menu order
 * Loops through ids and updates the menu_order in the attachment posts
 */
function fluff_save_order(){
    
    check_ajax_referer( 'deep-fried-wallaby', 'security' );
    
    // get the last saved order
    $old_option = fluff_get_image_order($_POST['term']);
    
    // array of the new order... key = new order 0,1,2,3 etc / value = old keys 1,2,0,3 etc
    $ajax_order = $_POST['list_items'];
    
    $updated_order = array();
    
    // Loop through new order
    foreach($ajax_order as $value){
        
        if( isset($old_option[$value]) ){
            
            //Making an array of post ids in the right order
            //match new value to the old keys to get post id
            
            $updated_order[$value] = $old_option[$value];
        }
    }
    // serialise array for database
    $update = maybe_serialize($updated_order);
    
    // update the order
    update_option($_POST['term'].'_gallery_order', $update);
    
    // must die any ajax function or response is 0
    die();
}


// get option for gallery order or insert one if it doesn't exist
function fluff_get_image_order($gallery){
    // get last saved order
    $order = get_option($gallery.'_gallery_order');
    
    // if no saved order
    if(!$order){
        $order = fluff_refresh_image_order($_POST['term']);
    }
    
    $order = maybe_unserialize($order);
    return $order;
}


// refresh option list based on menu order of gallery
function fluff_refresh_image_order($gallery){
    
    // get posts in current menu order
    $posts = fluff_get_my_posts($gallery);
    
    // new array for post ids
    $create_order = array();
    
    // add post ids in order
    foreach($posts as $post){
        $create_order[] = $post->ID;
    }
    
    $order = maybe_serialize($create_order);
    
    // refresh or create the option
    update_option($gallery.'_gallery_order', $order);
    
    return $order;
}
?>