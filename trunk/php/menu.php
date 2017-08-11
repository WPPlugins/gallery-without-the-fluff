<?php
/**
 * GALLERY MENU SCREEN
 * Adds submenu items in order of appearance to media menu
 *   http://codex.wordpress.org/Function_Reference/add_submenu_page
 * Creates the screens
 *  Gallery Management
 *  Images to Gallery
 *  FAQ
*/

#--- ADD GALLERY SCREEN TO MEDIA MENU
add_action('admin_menu', 'fluff_add_menu');

function fluff_add_menu(){
    
    // add our default screens
    if ( function_exists('add_submenu_page') ) {
        
        global $fluff_menu_screen;
        
        // Alternative library for assigning images to galleries
        $parent_slug = 'upload.php';
        $page_title = 'Add Images to Gallery';
        $menu_title = 'Images into Gallery';
        $capability = 'edit_pages';// editors too
        $menu_slug = 'gallery-images-to-gallery';
        $function = 'fluff_bulk_actions_function';
        
        $fluff_bulk_actions_screen = add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function);
        
        // load our scripts on this screen
        add_action('admin_print_styles-'.$fluff_bulk_actions_screen, 'fluff_load_scripts');
        
        
        // Add a screen for each gallery
        $screens = fluff_get_galleries();
        
        // loop through galleries and create a submenu for each
        foreach($screens as $term):
        
        if ( function_exists('add_submenu_page') ) {
            $parent_slug = 'upload.php';
            $page_title = $term->name . ' Gallery';
            $menu_title = $term->name;
            $capability = 'edit_pages';
            $menu_slug = $term->slug;
            $function = 'fluff_menu_function';
            
            $fluff_menu_screen = add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function);
            
            // load our scripts on this screen
            add_action('admin_print_styles-'.$fluff_menu_screen, 'fluff_load_scripts');
        }
        
        endforeach;
        
        
        // Lastly, add settings submenu
        $parent_slug = 'upload.php';
        $page_title = 'Settings: Setup Your Galleries (Administrators)';
        $menu_title = 'Settings (Admin)';
        $capability = 'manage_options';// admin only
        $menu_slug = 'gallery-settings';
        $function = 'fluff_settings_function';
        
        $fluff_settings_screen = add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function);
        
        // load our scripts on this screen
        add_action('admin_print_styles-'.$fluff_settings_screen, 'fluff_load_scripts');
    }
}


/**
 * CREATE GALLERY ORDERING SCREEN
 * ------------------------------
 * Creates the screen for each gallery
 * Adds table of thumbnails and inputs for title, caption etc.
 * Update button allows mass update of image meta
 */
function fluff_menu_function(){
    // page title
    $page_title = get_admin_page_title();
    
    // gallery slug
    global $plugin_page;
    
    // security token
    $ajax_nonce = wp_create_nonce("deep-fried-wallaby");
    
    // do update if form submitted
    if( isset($_POST['save']) ) $message = fluff_do_bulk_update($_POST);
    
    // refresh image order option whenever page loads
    fluff_refresh_image_order($plugin_page);
    
    ob_start();
    ?>
    <div id="fluff-<?php echo $plugin_page; ?>-menu" class="wrap fluff-dashboard">
        <div id="icon-upload" class="icon32"></div>
        <h2 class="screen-title"><?php echo $page_title;?></h2>
        <br />
        <?php
        // show update / error messages if present
        if( $message ) echo $message;
        ?>
        
        <form action="" method="post" id="termname">
            <input type="hidden" name="term" value="<?php echo $plugin_page; ?>" />
            <input type="hidden" name="security" value="<?php echo $ajax_nonce; ?>" />
            
            <p style="float:left">Drag and drop to order the images in your gallery. Use this code to add the gallery to a page or post: <strong>[photos gallery="<?php echo $plugin_page;?>"]</strong></p>
            <br />
            <!-- update bitton -->
            <input id="publish" class="button button-primary button-large" type="submit" value="Update" accesskey="p" name="save" />
            <div class="clear"></div>
            
            <table id="gallery-order" class="wp-list-table widefat fixed posts">
                <thead>
                    <tr>
                        <th class="fluff-gallery-image">Image</th>
                        <th class="fluff-gallery-title">Title</th>
                        <th class="fluff-gallery-caption">Caption</th>
                        <th class="fluff-gallery-alt">Alt Text</th>
                        <th class="fluff-gallery-order">Order</th>
                        <th class="fluff-gallery-remove">Remove</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>Image</th>
                        <th>Title</th>
                        <th>Caption</th>
                        <th>Alt Text</th>
                        <th>Order</th>
                        <th>Remove</th>
                    </tr>
                </tfoot>
                <tbody>
                    <?php if( function_exists('fluff_get_thumbnail_order') ) fluff_get_thumbnail_order($plugin_page); // admin-functions.php ?>
                </tbody>
            </table>
        </form>
    </div>
<?php
    echo ob_get_clean();
}


/**
 * IMAGES INTO GALLERY SCREEN
 * -------------------
 * Shows all images in condensed library view
 * Allows bulk selection of images and assign to gallery via select box
 */
function fluff_bulk_actions_function(){
    // page title
    $page_title = get_admin_page_title();
    // gallery slug
    global $plugin_page;
    
    // do update if form submitted
    if( isset($_POST['save']) ) $message = fluff_do_bulk_assign($_POST);
    ?>
    <div id="fluff-bluk-actions-menu" class="wrap fluff-dashboard">
        <div id="icon-options-general" class="icon32"></div>
        
        <h2 class="screen-title"><?php echo $page_title;?></h2>
        <br />
        
        <?php if( $message ) echo $message; ?>
        
        <form name="fluff-bulk-actions" method="post" action="">
            <!-- update bitton -->
            <input id="publish" class="button button-primary button-large" type="submit" value="Update" accesskey="p" name="save" />
            <select name="fluff-bulk-assign">
                <option selected="selected" value="-1">Select the Gallery</option>
                <?php
                $terms = fluff_get_galleries();
                foreach($terms as $term){
                    echo '<option value="' . $term->slug . '">' . $term->name . '</option>';
                }
                ?>
                
            </select>
            <div class="clear"></div>
            
            <div>
                <?php fluff_media_library(); ?>
                <div class="clear"></div>
            </div>
        </form>
    </div>
    <?php
}


/**
 * CREATE FAQ / SETTINGS SCREEN
 * -----------------
 * Allow creation of the gallery output
 * Allow reset of the default output
 * FAQ
 */
function fluff_settings_function(){
    // page title
    $page_title = get_admin_page_title();
    // gallery slug
    global $plugin_page;
    
    // do update if form submitted
    if( isset($_POST['save']) || isset($_POST['reset']) ) $message = fluff_save_output_settings($_POST);
    
    if( $message ) echo $message;
    ?>
    <div id="fluff-settings-menu" class="wrap fluff-dashboard">
        <div id="icon-options-general" class="icon32"></div>
        
        <h2 class="screen-title"><?php echo $page_title;?></h2>
        <p style="float:left;">See the <b>Help</b> tab for instructions on how to insert your gallery into a page, post, text widget or template file.<br />
        The <b style="color: #ff0000;">Reset</b> button will restore the default gallery settings.</p>
        
        <form id="fluff-output-settings" name="fluff-output" method="post" action="">
            <!-- update bitton -->
            <input id="publish" class="button button-primary button-large" type="submit" value="Update" accesskey="p" name="save" />
            <!-- reset button -->
            <input id="reset" class="button button-primary button-large" type="submit" value="Reset" name="reset" />
            <div class="clear"></div>
            
            <h3 id="fluff-output-title">Save your gallery markup</h3>
            <p>The html markup you need for a jQuery gallery or slider script is built around a <b>loop</b> of the images in your gallery. The markup you put <em>in the loop</em> will repeat for each image, while the <em>before</em> and <em>after</em> markup wraps around the loop of images to add containers for your gallery.</p>
            
            <h4>Before the loop</h4>
            <textarea name="before-loop" class="fluff-code"><?php echo get_option('fluff_before_loop'); ?></textarea>
            <p class="textarea label">Add the html markup you need before the image loop.</p>
            
            <h4>In the loop</h4>
            <textarea name="in-loop" class="fluff-code"><?php echo get_option('fluff_in_loop'); ?></textarea>
            <p class="textarea label">You can use any of the following <em>in the loop</em>: <b>{ID}</b> = image ID, <b>{post_name}</b> = image slug, <b>{post_title}</b> = image title, <b>{post_excerpt}</b> = image caption, <b>{post_content}</b> = image content, <b>{alt_text}</b> = image alt text. <br />Get image sizes or src paths like this: <b>{img-thumbnail}</b> = display thumbnail, <b>{img-medium}</b> = display medium image, <b>{src-large}</b> = print src of large image.</p>
            
            <h4>After the loop</h4>
            <textarea name="after-loop" class="fluff-code"><?php echo get_option('fluff_after_loop'); ?></textarea>
            <p class="textarea label">Add the html markup you need after the image loop.</p>
        </form>
        
        
    </div>
    <?php
}

?>