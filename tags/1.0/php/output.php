<?php
/**
 * OUTPUT THE GALLERY
 * ------------------
 * Function to return the gallery.
 * It is returned not echoed because it is called by a shortcode. So we need to save it into a $content variable and return this.
 * Looks for database user markup to output
 * If no user settings it returns the default gallery
*/

// This function takes arguments for WP_Query and the size of the image, medium by default
function fluff_return_my_gallery($args, $size){
    // run the query
    $query = new WP_Query($args);
    
    // if there are images to show
    if( $query->have_posts() ){
        
        // get user markup before the loop
        $before_loop = get_option('fluff_before_loop');
        
        // get user markup in the loop
        $in_loop = get_option('fluff_in_loop');
        
        // get user markup after the loop
        $after_loop .= get_option('fluff_after_loop');
        
        // if user options are missing, return default gallery
        if( !$before_loop || !$in_loop || !$after_loop ){
            
            $default = fluff_return_default_gallery($args, $size);
            return $default;
        }
        
        // start the custom output
        $output = $before_loop;
        
        // loop through photos
        while( $query->have_posts() ): $query->the_post();
            global $post;
            
            // add user markup
            $in_loop = fluff_get_in_loop($post);
            
            $output .= $in_loop;
            
        endwhile;
        
        // add the markup after the loop
        $output .= $after_loop;
        
        return $output;
    }
}
?>