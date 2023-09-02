(function( $ ) {
	'use strict';
	var loader = '<div class="dpbackend-loader-wrapper"><div class="dpbackend-loader"><div class="dpbackend-ripple"><div></div><div></div></div></div></div>';
	var loader_wrapper = ".dpbackend-loader-wrapper";
	jQuery(document).on('click', '#listing_admin_configure .action-button', function (e) {
        e.preventDefault();      
        var _this = jQuery(this);
		var claim_action = _this.attr('data-claim-action');
		var listing_id = _this.attr('data-listing-id');
		var data = {'action': 'dpcl_claimListingProcess', 'listing_id': listing_id, 'claim_action': claim_action};
        jQuery('#listing_admin_configure .modal-body').append(loader);
        jQuery.ajax({
            type: "POST",
            url: dpcl_custom_vars.ajaxurl,
            data: data,
            dataType: "json",
            success: function (response) {
				jQuery('#listing_action_modal .modal-body').find(loader_wrapper).remove();
                if (response.type == 'success') {
					jQuery('#listing_admin_configure .modal-body').html('<div class="alert alert-success alert-dismissible">'+response.message+'<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></div>');									
					jQuery('#listing_admin_configure .listing-claim-action').html('N/A');
				} else if (response.type == 'decline') {
					jQuery('#listing_admin_configure .modal-body').html('<div class="alert alert-warning alert-dismissible">'+response.message+'<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></div>');									
					jQuery('#listing_admin_configure .listing-claim-action').html('N/A');
				} else{			
					jQuery('#listing_admin_configure .modal-body').html('<div class="alert alert-danger alert-dismissible">'+response.message+'<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></div>');  
                }
				//jQuery('#listing_action_modal').removeClass('listing_claim_modal');
            }
        });
    });

	jQuery(document).on('click', 'a.admin-listing-claim-process-link', function (e) { 
		jQuery('#listing_admin_configure .modal-body').append(loader);
		var listing_id = jQuery(this).attr('data-listing-id');
		jQuery.ajax({
			type: "POST",
			url: directorypress_js_instance.ajaxurl,
			data: { 'action': 'dpcl_claimListing_html', 'listing_id': listing_id},
			dataType: "html",
			success: function (response) {
				jQuery('#listing_admin_configure .modal-body').find(loader_wrapper).remove();
				jQuery('#listing_admin_configure .modal-body').html(response);
				//$('.directorypress-select2').select2();
			}
		});
    });

})( jQuery );
