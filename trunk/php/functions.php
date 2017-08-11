<?php
/**
 * GENERAL FUNCTIONS
 * -----------------
 * Functions available anytime, frontend or backend
    * Register "gallery" taxonomy
    * Adds user styles and scripts to frontend <head>
    * Creates shortcodes for inserting galleries
    * Function for inserting gallery directly into template file
*/

#--- REGISTER TAXONOMY
add_action( 'init', 'fluff_custom_taxonomy' );

function fluff_custom_taxonomy() {
	// create a new taxonomy
	register_taxonomy(
		'gallery', // slug
		array( // relates to post types
            'attachment'
        ),
		array(
			'label' => __( 'Add Gallery' ),
            'hierarchical' => true, // parent/child
            'public' => true // visible in admin
		)
	);
}

#--- ADD STYLES AND SCRIPTS TO FRONT END
add_action('wp_head', 'fluff_add_styles_scripts');

function fluff_add_styles_scripts(){
    // get user scripts and styles
    $scripts = get_option('fluff_scripts');
    // if found, print in wp head
    if($scripts){
        echo $scripts;
    }
}

#--- ADD SHORTCODE
//create shortcode [photos] for inserting gallery into post (Note: can't use [gallery] as wordpress uses this already)
add_shortcode( 'photos', 'fluff_shortcode_function' );

function fluff_shortcode_function( $atts ) {
	extract( shortcode_atts( array(
            'gallery' => '',
            'size' => 'medium',
        ), $atts )
    );
    
    $args = array(
        'post_type' => 'attachment',
        'post_status' => 'inherit',
        'gallery' => $gallery,
        'orderby' => 'menu_order',
        'order' => 'ASC',
        'posts_per_page' => -1
    );
    
    $gallery = fluff_return_my_gallery($args, $size);
    
    return $gallery;
}


// call this function from a template file in place of a shortcode
function fluff_add_my_gallery($gallery = '', $size = 'medium'){
    // query args
    $args = array(
        'post_type' => 'attachment',
        'post_status' => 'inherit',
        'gallery' => $gallery,
        'orderby' => 'menu_order',
        'order' => 'ASC',
        'posts_per_page' => -1
    );
    // create the gallery
    $content = fluff_return_my_gallery($args, $size);
    
    echo $content;
}


// pull the output file from an option
function fluff_get_in_loop($post){
    
    // get user settings
    $in_loop = get_option('fluff_in_loop');
    
    // if no user settings are present exit
    if( !isset($in_loop) ) return false;
    
    /**
     * POSSIBLE VARIABLES TO REPLACE...
     * ID
     * post_name (slug)
     * post_title
     * post_content
     * post_excerpt
     * 
     * wp_get_attachment_image_src( $post->ID, $size )
     * wp_get_attachment_image( $post->ID, $size )
     * get_post_meta($post->ID, '_wp_attachment_image_alt')
     */
    
    // array for variables to replace
    $replace = array();
    
    $replace['ID'] = $post->ID;
    $replace['post_name'] = $post->post_name;
    $replace['post_title'] = $post->post_title;
    $replace['post_content'] = $post->post_content;
    $replace['post_excerpt'] = $post->post_excerpt;
    $replace['alt_text'] = get_post_meta($post->ID, '_wp_attachment_image_alt');
    
    foreach($replace as $key => $value){
        // look for each key between curly brackets and replace with value if found
        $in_loop = str_replace('{'.$key.'}', $value, $in_loop);
    }
    
    // Get the available image sizes in an array.
	$sizes = get_intermediate_image_sizes();
    // add the full size
    $sizes[] = 'full';
    
    
    foreach($sizes as $size){
        // replace each image found
        $in_loop = str_replace('{img-'.$size.'}', wp_get_attachment_image( $post->ID, $size ), $in_loop);
        // replace each src found
        $in_loop = str_replace('{src-'.$size.'}', fluff_get_image_src( $post->ID, $size ), $in_loop);
    }
    
    return $in_loop;
}


// work out the src
function fluff_get_image_src( $post_id, $size ){
    $src = wp_get_attachment_image_src( $post_id, $size );
    // src is first element of array
    return $src[0];
}

/**
 * GET GALLERY TERMS
 * -----------------
 * TERM OBJECT
 * term_id
 * name
 * slug
 * term_group
 * term_taxonomy_id
 * taxonomy
 * description
 * parent
 * count
 */
function fluff_get_galleries(){
    $args = array(
        'hide_empty' => 0
    );
    $galleries = get_terms( 'gallery', $args );
    
    return $galleries;
}


// get image posts for given gallery in current menu order
function fluff_get_my_posts($gallery){
    
    // query args
    $args = array(
        'post_type' => 'attachment',
        'post_status' => 'inherit', // attachments aren't "published"
        'gallery' => $gallery, // term slug
        'orderby' => 'menu_order',
        'order' => 'ASC',
        'posts_per_page' => -1
    );
    $posts = get_posts($args);
    
    return $posts;
}


?>