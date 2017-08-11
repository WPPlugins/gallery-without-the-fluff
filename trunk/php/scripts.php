<?php

#--- LOAD ADMIN SCRIPTS
function fluff_load_scripts(){
    // sortable ui
    wp_enqueue_script('jquery-ui-sortable');
    // function call to that sortable script
    wp_enqueue_script('update-order', FLUFF_JS.'drag-and-drop.js');
    // our css
    wp_enqueue_style( 'gal-style', FLUFF_CSS.'admin.css' );
}

?>