<?php
/**
 * CONTEXTUAL HELP
 * ---------------
 * Adds our user help to tabs in screens we use
 * 
 * SCREEN ID's...
 * all media: upload
 * edit attachment:  attachment
 * add new media: media
 * manage gallery tax: edit-gallery
 * our gallery page: media_page_gallery-menu
*/

/* 
// function to show current screen ID
add_action( 'contextual_help', 'fluff_screen_id', 10, 3 );

function fluff_screen_id( $contextual_help, $screen_id, $screen ) {
    ?>
    <div class="updated"><p>Screen ID: <?php echo $screen->id; ?></p></div>
    <?php
}
*/

#--- ADD TO EXISTING HELP TABS
add_action( 'contextual_help', 'fluff_help', 10, 3 );

function fluff_help( $contextual_help, $screen_id, $screen ) {
    // Screen class
    $screen = get_current_screen();
    
    //array for tabs
    $tabs = array();
    
    // Check against our media screen id's
    switch($screen->id){
        
        // Media Library
        case 'upload':
            $help['title'] = 'Galleries';
            $help['content'] = '<h2>'.$help['title'].'</h2>';
            $help['content'] .= '<p>The Galleries column shows which of your image attachments are linked to a gallery. If it is blank they are not linked to a gallery. Images can be linked to multiple galleries. To set the gallery, <strong>Edit</strong> the image, or use the <strong>Images to Galleries</strong> screen.</p>';
            
            // add to tabs array
            $tabs[] = $help;
            break;
        
        // edit image
        case 'attachment':
            $help['title'] = 'Add Gallery';
            $help['content']= '<h2>'.$help['title'].'</h2>';
            $help['content'].= '<p>This box lets you add this image to any of the galleries that you set up in the Media: <strong>Add Gallery</strong> submenu. Simply click a checkbox to add or remove the image from that gallery and click the Update button when you are done. You can select multiple galleries.</p>';
            
            // add to tabs array
            $tabs[] = $help;
            break;
        
        // upload
        case 'media':
            
            break;
        
        // gallery taxonomy
        case 'edit-gallery':
            $help['title'] = 'Add Galleries';
            $help['content']= '<h2>'.$help['title'].'</h2>';
            $help['content'].= '<p>This screen allows you to add new galleries for your images, as well as edit or delete existing galleries. To add a gallery, write the name of the gallery in the <strong>Name</strong> field and click the <strong>Add New Category</strong> button.</p>
            <p>This works just like adding a category for your posts. Once the gallery is added you will be able to link an image to it when you edit that image from the <strong>Library</strong>. Or you can add images to galleries in bulk using <strong>Add Images to Galleries</strong>.</p>';
            
            // add to tabs array
            $tabs[] = $help;
            break;
        
        // image to gallery
        case 'media_page_gallery-images-to-gallery':
            $help['title'] = 'Add Images to Gallery';
            $help['content']= '<h2>'.$help['title'].'</h2>';
            $help['content'].= '<p>Select the images you want to add to a gallery and choose the appropriate gallery from the select box at the top of screen. Then click <strong>Update</strong>. You should now see the name of that gallery appear beside each image you selected. These images are now linked to that gallery.</p>
            <p>To remove images from a gallery, go to that gallery submenu.</p>';
            
            // add to tabs array
            $tabs[] = $help;
            break;
        
        // gallery settings
        case 'media_page_gallery-settings':
            // a tab
            $help['title'] = 'Show your Gallery';
            $help['content'] .= '<h3>Adding your Gallery to a Page, Post or Text Widget</h3>
            <p>You can add a gallery to any post, page or text widget by copying the shortcode from that gallery page and pasting it into your page/post editor, or text widget. The shortcode looks something like this...</p>
            <div class="help fluff"><p>[photos gallery="my-gallery"]</p></div>
            ';
            // add to tabs array
            $tabs[] = $help;
            
            // next tab
            $help['title'] = 'Theme Function';
            $help['content'] = '<h3>Adding your Gallery to a Theme File</h3>
            <p>To call a gallery directly from a file in your WordPress theme (outside the loop), use this function <b>inside php tags</b> at the place you want the gallery to appear. Again, my-gallery would be the slug of the gallery you want to add.</p>
            <div class="help fluff"><pre>&lt?php if( function_exists(\'fluff_add_my_gallery\') ) fluff_add_my_gallery(\'my-gallery\'); ?&gt</pre></div>
            ';
            // add to tabs array
            $tabs[] = $help;
            
            // next tab
            $help['title'] = 'Adding the jQuery';
            $help['content'] = '<h3>Adding my jQuery script</h3>
            <p>That\'s your bit! See the WordPress Codex for how to add this into your theme. <b>Hint:</b> the jQuery script will normally be placed into a folder of your theme with other scripts. You need to reference this script in the header.php file of your theme, e.g.</p>
            <div class="help fluff"><pre>&ltscript src="&lt?php bloginfo("template_url");?&gt/js/my-jquery-script.js" type="text/javascript">&lt/script></pre></div>
            <p>Typically, beneath this line you would have a jquery function that would trigger your script, using a selector in your gallery markup. Something like...</p>
<pre>
&ltscript type="text/javascript">
$(document).ready( function() { 
    $(".your-selector").some_gallery_function({
        // some settings
    });
});
&lt/script>
</pre>
            <p>You may also need to include the jQuery library itself if you theme does not include it already. This would be included before your script.</p>
            ';
            // add to tabs array
            $tabs[] = $help;
            
            // next tab
            $help['title'] = 'Credits';
            $help['content'] = '<h3>Credits</h3>
            <p>Gallery Without the Fluff is by Justyn Walker from revive web. If you like the plugin please vote for it, and if you are feeling generous why not share the love and <b><a href="http://www.facebook.com/reviveweb/" target="_blank">Like us on Facebook</a>?</b></p>
            ';
            // add to tabs array
            $tabs[] = $help;
            break;
    }
    
    // get galleries in use
    $terms = get_terms('gallery');
    
    // loop through terms
    foreach($terms as $term){
        // check current screen id against those screen ids
        if( $screen->id == 'media_page_'.$term->slug ){
            $help['title'] = "Manage $term->name";
            $help['content']= "<h2>Manage $term->name Gallery</h2>";
            $help['content'].= "<p>This screen shows all the images that are linked to the $term->name gallery. It also shows the information relating to the image and the current order of the images, beginning at 0. You can set the <strong>Order</strong> of the images by dragging them up or down the list and choosing <strong>Update</strong> when you are finished.</p>
            <ul>
                <li>To <strong>Edit</strong> a particular image, click the edit button beneath the thumbnail.</li>
                <li>To <strong>Remove</strong> images from the gallery, select the images to remove and click <strong>Update</strong>.</li>
                <li>Please note: the menu order column will not update until the page is refreshed.</li>
            </ul>";
            // add tab
            $tabs[] = $help;
        }
    }
    
    // if we have a winner
    // multiple tabs
    if( $tabs && is_array($tabs) ){
        // loop through each help tab
        foreach($tabs as $key => $tab){
            // add my help
            $screen->add_help_tab( array(
                'id' => 'fluff_help_tab'.$key, // must be unique
                'title'	=> $tab['title'],
                'content' => $tab['content']
            ));
        }
    }
    
}
?>