(function( $ ) {
	'use strict';
	jQuery(document).on('click', '.listing_setting_action_link_claim', function (e) {
        e.preventDefault();  
		jQuery('.modal .modal-body').append(loader);
        var _this = jQuery(this);
		
		var modal_title = _this.attr('data-modal-title');
		var listing_id = _this.attr('data-listing-id');
        jQuery('#listing_action_modal').find('.modal-title').text(modal_title);
		jQuery('#listing_action_modal').addClass('listing_claim_modal');
        
		var data = {'action': 'dpcl_claimListing_html', 'listing_id': listing_id};

        jQuery.ajax({
            type: "POST",
            url: dpfl_custom_vars.ajaxurl,
			data: data,
            dataType: "html",
            success: function(response) {
				
				jQuery('.modal .modal-body').find(loader_wrapper).remove();
				jQuery('#listing_action_modal .modal-body').html(response);	

				jQuery(document).on('hide.bs.modal', '#listing_action_modal', function () {
					jQuery('.modal .modal-body').html('');
					jQuery('.modal .modal-footer').find('.action-button').remove();
					location.reload();
				});
            }
        });
    });
	// Claim Listing
	jQuery(document).on('click', '.listing-claim-form .claim-action-button', function (e) {
        e.preventDefault();      
        var _this = jQuery(this);
		//var claim_action = _this.attr('data-claim-action');
		//var listing_id = _this.attr('data-listing-id');
		//var listing_id = _this.attr('data-listing-id');
		var claimForm = jQuery('form.listing-claim-form').serialize();
		//var data = {'action': 'dpcl_claimListing_form', 'listing_id': listing_id};
        jQuery('.single-claim  .directorypress-popup-content').append(loader);
        jQuery.ajax({
            type: "POST",
            url: dpcl_custom_vars.ajaxurl,
			data: claimForm + '&action=dpcl_claimListing_form',
            dataType: "json",
            success: function (response) {
				jQuery('.single-claim  .directorypress-popup-content').find('.dpcl-loader-wrapper').remove();
                if (response.type == 'success') {
					jQuery('.single-claim  .directorypress-popup-content').html('<div class="alert alert-success alert-dismissible">'+response.message+'<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></div>');									
					//jQuery('.dashboard-listings-claim .listing-claim-action').html('N/A');
				} else{			
					jQuery('.single-claim  .directorypress-popup-content').html('<div class="alert alert-danger alert-dismissible">'+response.message+'<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></div>');  
                }
				//jQuery('#listing_action_modal').removeClass('listing_claim_modal');
            }
        });
    });
	jQuery(document).on('click', '.listing_claim_modal .action-button', function (e) {
        e.preventDefault();      
        var _this = jQuery(this);
		var claim_action = _this.attr('data-claim-action');
		var listing_id = _this.attr('data-listing-id');
		var data = {'action': 'dpcl_claimListingProcess', 'listing_id': listing_id, 'claim_action': claim_action};
        jQuery('#listing_action_modal .modal-body').append(loader);
        jQuery.ajax({
            type: "POST",
            url: dpcl_custom_vars.ajaxurl,
            data: data,
            dataType: "json",
            success: function (response) {
				jQuery('#listing_action_modal .modal-body').find(loader_wrapper).remove();
                if (response.type == 'success') {
					jQuery('#listing_action_modal .modal-body').html('<div class="alert alert-success alert-dismissible">'+response.message+'<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></div>');									
					jQuery('.dashboard-listings-claim .listing-claim-action').html('N/A');
				} else if (response.type == 'decline') {
					jQuery('#listing_action_modal .modal-body').html('<div class="alert alert-warning alert-dismissible">'+response.message+'<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></div>');									
					jQuery('.dashboard-listings-claim .listing-claim-action').html('N/A');
				} else{			
					jQuery('#listing_action_modal .modal-body').html('<div class="alert alert-danger alert-dismissible">'+response.message+'<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></div>');  
                }
				jQuery('#listing_action_modal').removeClass('listing_claim_modal');
            }
        });
    });

})( jQuery );
