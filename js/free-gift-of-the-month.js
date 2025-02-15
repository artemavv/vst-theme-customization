/**
 * This script allows customers to claim "Free gifts" into their cart (on the cart page) if eligible.
 * 
 * NOTE:
 * "freegift_data" variable is provided by "wp_localize_script" in free-gift-of-the-month.php
 */
jQuery(document).ready(function($){        
	
    $('.woocommerce').on( 'click', 'ul.shop_table a.claim_free_gift', function() {
			
  	$(this).hide();
		
				// TODO add the gif
        $(this).next().show(); // show loader gif
  
        const freegift_id = $(this).data('freegift-id');
        
        const request_url = freegift_data.ajax_url + '?action=claim_freegift';
        
        $.ajax({
            url: request_url,
            //dataType: 'json',
            data: { freegift_id: freegift_id },
            type: 'POST',
            success: function(response) {
                window.location.reload(true);
            }
        });
    });
});