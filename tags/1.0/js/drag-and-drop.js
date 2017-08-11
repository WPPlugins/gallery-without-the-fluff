/* DRAG & DROP SCRIPT */
jQuery(document).ready(function($){
    // look for this table id
    $('#gallery-order').sortable({
        items: '.list_item',// these are the sortable items
        opacity: 0.6,
        cursor: 'move',
        axis: 'y', // vertical only
        // run this function on order update
        update: function(){
            
            // get nonce security token
            var security = $('input[name=security]').val();
            
            // get term for current gallery to know which gallery to update, a hidden field on menu screen
            var term = $('input[name=term]').val();
            
            // get the order into a serialized array, plus the gallery term, plus nonce token, plus a call to the wp_ajax_"fluff_update_order" action
            var order = $(this).sortable('serialize') + '&term=' + term + '&security=' + security + '&action=fluff_update_order';
            
            // post this order array, plus other variables, for our ajax function to pickup
            $.post(ajaxurl, order, function(response){
                // success message
                //alert('Gallery order updated.');
                //alert(response);
            });
        }
    });
});