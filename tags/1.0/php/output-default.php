<?php
/**
 * OUTPUT THE GALLERY
 * ------------------
 * Function to return the gallery.
 * It is returned not echoed because it is called by a shortcode. So we need to save it into a $content variable and return this.
 * This gallery output is marked up for using jQuery fancybox but you should change it to suit your needs.
*/

// This function takes arguments for WP_Query and the size of the image, medium by default
function fluff_return_default_gallery($args, $size){
    // run the query
    $query = new WP_Query($args);
    
    // create a variable to output the gallery content
    $content = '';
    
    // if there are images to show
    if( $query->have_posts() ){
        // start a gallery container
        $content .= '<!-- START GALLERY -->';
        $content .= '<div id="gallery">';
        
        // loop through photos
        while( $query->have_posts() ): $query->the_post();
            // get the post object (access any info in the posts table)
            global $post;
            setup_postdata( $post );
            // get the path to the large format image for fancybox, returns an array with src at $large[0]
            $large = wp_get_attachment_image_src( $post->ID, 'large' );
            
            // markup for each image we loop through
            $content .= '<div class="photo-wrap">';
                $content .= '<div class="photo">';
                    // link to large image
                    $content .= '<a class="fancybox" rel="group" href="' . $large[0] . '">';
                        // the small image
                        $content .= wp_get_attachment_image( $post->ID, $size );
                    $content .= '</a>';
                    // image caption
                    $content .= '<div class="caption">' . $post->post_excerpt . '</div>';
                $content .= '</div>';
                $content .= '<div class="clear"></div>';
            $content .= '</div>';
            
        endwhile;
        wp_reset_postdata();
        
        // end gallery container
        $content .= '</div>';
        $content .= '<div class="clear"></div>';
        $content .= '<!-- end gallery -->';
        
        return $content;
    }
}


/**
 * DEFAULT GALLERY SETTINGS
 * --------------------------
 * On plugin activation or reset button
 * Return values of the default gallery.
*/
function fluff_get_default_options(){
    // array for 3 options
    $options = array();
    
    // markup before the image loop
    $options['before_loop'] =
'
<!-- START GALLERY -->
<div id="gallery">
    <ul>
    <!-- start loop -->
';
    
    // markup in the image loop
    $options['in_loop'] =
'
    <li class="photo">
        <!-- image title -->
        <h4>{post_title}</h4>
        <!-- image caption -->
        <div id="{ID}" class="{post_name}">{post_excerpt}</div>
        <!-- thumbnail linked to large image -->
        <a href="{src-large}">{img-thumbnail}</a>
    </li>
';
    // markup after the image loop
    $options['after_loop'] =
'
    <!-- end loop -->
    </ul>
</div>
<div class="clear"></div>
<!-- end gallery -->
';
    
    return $options;
}


/**
 * TN3 GALLERY
 * ------------------
*/

// This function takes arguments for WP_Query and the size of the image, medium by default
function fluff_return_tn3_gallery($args, $size){
    // run the query
    $query = new WP_Query($args);
    
    // create a variable to output the gallery content
    $content = '';
    
    // if there are images to show
    if( $query->have_posts() ){
        // start a gallery container
        $content .= '<!-- START GALLERY -->';
        $content .= '
<div id="gallery">
    <div class="tn3 album">
        <ol>
';
        
        // loop through photos
        while( $query->have_posts() ): $query->the_post();
            // get the post object (access any info in the posts table)
            global $post;
            setup_postdata( $post );
            // get the path to the large format image for fancybox, returns an array with src at $large[0]
            $large = wp_get_attachment_image_src( $post->ID, 'large' );
            
            // markup for each image we loop through
            $content .= '<li>';
                $content .= '<h4>' . $post->post_title . '</h4>';
                $content .= '<div class="tn3 description">' . $post->post_excerpt . '</div>';
                // link to large image
                $content .= '<a href="' . $large[0] . '">';
                    // the small image
                    $content .= wp_get_attachment_image( $post->ID, 'thumbnail' );
                $content .= '</a>';
            $content .= '</li>';
            
        endwhile;
        wp_reset_postdata();
        
        // end gallery container
        $content .= '
        </ol>
    </div>
</div>
<div class="clear"></div>
<!-- end gallery -->
';
        
        return $content;
    }
}
?>