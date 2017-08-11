<?php
/**
 * ADMIN FUNCTIONS
 * -----------------
 * Functions for the dashboard, called if( is-admin() ) from plugin setup file
 * Media Library
    * Adds Gallery metabox to edit image screen
    * Adds Gallery column and terms to Library
 * Images to Gallery
    * Creates Images to Gallery library
    * Assigns selected images to gallery on Update
 * Manage Galleries
    * Creates manage gallery table
    * Updates image meta in bulk
    * Removes images from gallery
 * Settings
    * Saves gallery settings
*/

#--- MEDIA LIBRARY FUNCTIONS

// adds metabox to media editor
add_action('init', 'fluff_reg_tax');

function fluff_reg_tax() {
   register_taxonomy_for_object_type('gallery', 'attachment');
}


// adds gallery column to library
add_filter('manage_media_columns', 'fluff_image_columns');

function fluff_image_columns($defaults) {
    $defaults['galleries'] = __('Galleries');
    return $defaults;
}


// adds gallery terms to library rows
add_action('manage_media_custom_column', 'my_custom_column', 10, 2);

function my_custom_column($column_name, $post_id) {
    global $wpdb;
    if( $column_name == 'galleries' ) {
        
        $terms = get_the_terms( $post_id, 'gallery' );
        if($terms) {
            foreach ( $terms as $term ) {
                $names[] = $term->name;
            }
            $join = join( ", ", $names );
            
            echo $join;
        }
    }
}


#--- IMAGES TO GALLERY FUNCTIONS

/**
 * MEDIA LIBRARY FOR IMAGES TO GALLERY
 * --------------------------------------
 * Called by images to gallery screen (menu.php)
 * Generates a list of all images
 * Checkbox allows images to be selected
 */
function fluff_media_library(){
    
    $args = array(
        'post_type' => 'attachment',
        'post_status' => 'inherit',
        'post_mime_type' => 'image',
        'order' => 'DESC',
        'posts_per_page' => -1
    );
    $query = new WP_Query($args);
    
    if( $query->have_posts() ): while( $query->have_posts() ): $query->the_post();
        global $post;
        setup_postdata($post);
        ?>
        <div class="image-item">
            <div class="image-item-wrap">
                <div class="image-thumbnail-wrap">
            <?php
            //get the thumbnail src with post id
            $src = wp_get_attachment_image_src( $post->ID, 'thumbnail' );
            
            // get current gallery terms
            $terms = get_the_terms( $post->ID, 'gallery' );
            
            // no gallery by default
            $galleries = 'None';
            
            // if there are gallery terms
            if ( $terms && !is_wp_error( $terms ) ) : 
                
                // put them in an array
                $links = array();
                
                foreach ( $terms as $term ) {
                    $links[] = $term->name;
                }
                // comma separated string of galleries
                $galleries = join( ", ", $links );
            endif;
            ?>
                <img src="<?php echo $src[0]; ?>" class="image-thumbnail" />
                <br /><span class="edit"><a title="Edit this image" href="post.php?post=<?php echo $post->ID; ?>&action=edit">Edit</a></span>
                </div>
                <div class="image-description-wrap">
                    <p>
                        <input type="checkbox" name="ids[<?php echo $post->ID; ?>]" value="<?php echo $post->ID; ?>" class="image-select" /> 
                        Add this?
                    </p>
                    <p class="image-title"><?php the_title(); ?></p>
                    <p class="image-gallery-terms">Galleries: <?php echo $galleries; ?></p>
                </div>
            </div>
            <div class="clear"></div>
        </div>
        <?php
    endwhile;
    endif;
}

/**
 * ASSIGN IMAGES TO GALLERY IN BULK
 * --------------------------------
 * Called from bulk actions screen
 * Gets checked images and assigns to selected gallery
 */
function fluff_do_bulk_assign($bulk){
    // gallery slug to assign
    $gallery = $bulk['fluff-bulk-assign'];
    
    // error - no gallery selected
    if($gallery == -1){
        $message = '<div id="message" class="error fluff below-h2"><p>You forgot to choose a gallery.</p></div>';
        return $message;
    }
    
    // list of ids to assign
    $ids = $bulk['ids'];
    
    // error - no images selected
    if( !isset($ids) ){
        $message = '<div id="message" class="error fluff below-h2"><p>You need to select some images first.</p></div>';
        return $message;
    }
    
    foreach($ids as $post_id){
        // add term to each post
        // post id, term, tax, append terms don't replace
        wp_set_object_terms( $post_id, $gallery, 'gallery', true );
    }
    
    $term = get_term_by( 'slug', $gallery, 'gallery' );
    
    $term_tax_id = array();
    $term_tax_id[] = $term->term_taxonomy_id;
    
    // need to update the term count in term_taxonomy table
    $output = wp_update_term_count_now( $term_tax_id, 'gallery' );
    
    $message = '<div id="message" class="updated fluff below-h2"><p>Your gallery settings have been updated.</p></div>';
    
    // send back the message
    return $message.$plus;
}


#--- MANAGE GALLERY FUNCTIONS

// get table rows for gallery screens, called in menu.php
function fluff_get_thumbnail_order($gallery){
    
    // get posts for this gallery
    $posts = fluff_get_my_posts($gallery);
    
    // counter for <tr id="">
    $count = 0;
    
    // loop through each attachment post
    foreach($posts as $post){
        
        // start the row with unique id, class for our drag-drop script
        echo '<tr id="list_items_' . $count . '" class="list_item">';
    
        // show the thumbnail
        echo '<td>';
            //get the thumbnail src with post id
            $src = wp_get_attachment_image_src( $post->ID, 'thumbnail' );
            
            // print the thumbnail
            echo '<img src="'.$src[0].'" class="image-thumbnail" />';
            
            // link to edit this attachment
            echo '<br /><span class="edit"><a title="Edit this image" href="post.php?post='.$post->ID.'&action=edit">Edit</a></span>';
            echo ' | ID: ' . $post->ID;
        echo '</td>';
        ?>
        
        <td>
            <input type="text" name="image_meta[<?php echo $post->ID; ?>][title]" value="<?php echo $post->post_title; ?>" class="image-meta image-title" />
        </td>
        <td>
            <textarea name="image_meta[<?php echo $post->ID; ?>][caption]" class="image-meta image-caption"><?php echo $post->post_excerpt; ?></textarea>
        </td>
        <td>
            <input type="text" name="image_meta[<?php echo $post->ID; ?>][alt_text]" value="<?php echo get_post_meta($post->ID, '_wp_attachment_image_alt', true); ?>" class="image-meta image-caption" />
        </td>
        <td>
            <p class="image-menu-order"><?php echo $post->menu_order; ?></p>
        </td>
        <td>
            <input type="checkbox" name="image_meta[<?php echo $post->ID; ?>][remove]" value="remove" class="image-meta image-remove" />
        </td>
        </tr>
        <?php
        
        $count++;
    }
}

/**
 * BULK UPDATE IMAGE META
 * ----------------------
 * Receives $_POST and separates out the array for image meta into $updates
 * $updates is a multidimensional array, with array keys of each post id to be updated
 * Loops through these arrays, using the key to reference the post id, and updates the image meta
 * Returns a message on success
 */
function fluff_do_bulk_update($bulk){
    // get out if not right
    if( !isset($bulk) || !is_array($bulk) ) return;
    
    // create an array for updates
    $updates = $bulk['image_meta'];
    
    $order_option = get_option($bulk['term'].'_gallery_order');
    $order_option = maybe_unserialize($order_option);
    $order = array();
    foreach($order_option as $value){
        $order[] = $value;
    }
    
    // $update is an array with meta for each single post and a key of post ID
    foreach($updates as $post_id => $update){
        
        // put ID, title and caption into an array for the posts table
        $image_meta = array();
        $image_meta['ID'] = $post_id;
        $image_meta['post_title'] = $update['title'];
        $image_meta['post_excerpt'] = $update['caption'];
        
        // update the menu order
        // loop through the order we got from the option
        foreach($order as $key => $value){
            // look for matching ID
            if($value == $post_id){
                // the array key is the menu order
                $image_meta['menu_order'] = $key;
            }
        }
        
        // Update the post table
        $success = wp_update_post( $image_meta );
        
        // Update alt text in postmeta table
        update_post_meta($post_id, '_wp_attachment_image_alt', $update['alt_text']);
        
        // if remove is set, remove the gallery term
        if($update['remove']){
            // Update the terms
            fluff_remove_from_gallery($post_id, $bulk['term']);
        }
    }
    // if it updated something
    if($success && $success!== 0){
        // success message
        $message = '<div id="message" class="updated fluff below-h2"><p>Your images have been updated.</p></div>';
    }
    else{
        $message = '';
    }
    
    return $message;
}


/**
 * REMOVE IMAGE FROM GALLERY
 * -------------------------
 * Removes a given image id from a gallery
 * Called in loop from bulk gallery update
 * Receives post ID and slug of current gallery
 */
function fluff_remove_from_gallery($post_id, $gallery){
    
    // get last saved terms
    $terms = wp_get_object_terms($post_id, 'gallery');
    
    // array for new terms
    $new_terms = array();
    
    // loop through and assign all but the removed term to our new array
    foreach($terms as $term){
        
        if($term->slug !== $gallery){
            $new_terms[] = $term->slug;
        }
    }
    
    // replace old terms with new list
    // post id, term, tax, replace terms
    wp_set_object_terms( $post_id, $new_terms, 'gallery', false );
}


#--- GALLERY SETTINGS FUNCTIONS

/**
 * SAVE OUTPUT SETTINGS
 * -------------------
 * 
 */
function fluff_save_output_settings($settings){
    
    $before_loop = $settings['before-loop'];
    $in_loop = $settings['in-loop'];
    $after_loop = $settings['after-loop'];
    
    // if reset is clicked, replace user options with default and return reset message
    if( isset($settings['reset']) && $settings['reset'] == 'Reset' ){
        fluff_update_default_options();
        
        $message = '<div id="message" class="updated fluff below-h2"><p>The default settings have been reset.</p></div>';
        // send back the message
        return $message;
    }
    
    // otherwise update options with new values
    update_option( 'fluff_before_loop', stripslashes(wp_filter_post_kses(addslashes($before_loop))) );
    update_option( 'fluff_in_loop', stripslashes(wp_filter_post_kses(addslashes($in_loop))) );
    update_option( 'fluff_after_loop', stripslashes(wp_filter_post_kses(addslashes($after_loop))) );
    
    $message = '<div id="message" class="updated fluff below-h2"><p>Your settings have been updated.</p></div>';
    
    // send back the message
    return $message;
}

?>