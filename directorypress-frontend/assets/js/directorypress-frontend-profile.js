(function( $ ) {
	'use strict';
	// Profile Update
	jQuery(document).on('click', '.dpfl_profile_update_action', function (e) {
        e.preventDefault();      
        var _this = jQuery(this);
		
        jQuery('.dpfl-user-profile .ajax-response').append(loader);
		_this.addClass('disabled');
        var profileForm = _this.parents('form.dpfl-user-profile').serialize();
        jQuery.ajax({
            type: "POST",
            url: dpfl_custom_vars.ajaxurl,
            data: profileForm + '&action=dpfl_ProfileUpdate',
            dataType: "json",
            success: function (response) {
               jQuery('.content-wrapper').find(loader_wrapper).remove();
			   _this.removeClass('disabled');
                if (response.type == 'success') {
					jQuery('.dpfl-user-profile .ajax-response').html('<div class="alert alert-success alert-dismissible">'+response.message+'<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></div>');									
                } else {			
					jQuery('.dpfl-user-profile .ajax-response').html('<div class="alert alert-danger alert-dismissible">'+response.message+'<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></div>');  
                }
            }
        });
    });
	
	//Password Updade
	jQuery(document).on('click', 'a.dpfl-change-password', function (e) {
        e.preventDefault();      
        var _this = jQuery(this);
         jQuery('.dpfl-userpass .ajax-response').append(loader);
		 _this.addClass('disabled');
        var profileForm = _this.parents('form.dpfl-change-password').serialize();
        jQuery.ajax({
            type: "POST",
            url: dpfl_custom_vars.ajaxurl,
            data: profileForm + '&action=dpfl_PasswordUpdate',
            dataType: "json",
            success: function (response) {
                 jQuery('.dpfl-userpass .ajax-response').find(loader_wrapper).remove();
				 _this.removeClass('disabled');
                if (response.type == 'success') {
					jQuery('.dpfl-userpass .ajax-response').html('<div class="alert alert-success alert-dismissible">'+response.message+'<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></div>');									
                } else {			
					jQuery('.dpfl-userpass .ajax-response').html('<div class="alert alert-danger alert-dismissible">'+response.message+'<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></div>');  
                }
            }
        });
    });
	//Password Updade
	jQuery(document).on('click', '.close-account-action', function (e) {
        e.preventDefault();      
        var _this = jQuery(this);
         jQuery('#user-close-account-modal .modal-body .response').append(loader);
		 _this.addClass('disabled');
        var form = _this.parents('form').serialize();
		
        jQuery.ajax({
            type: "POST",
            url: dpfl_custom_vars.ajaxurl,
            data: form + '&action=dpfl_closeUserAccount',
            dataType: "json",
            success: function (response) {
                jQuery('#user-close-account-modal .modal-body .response').find(loader_wrapper).remove();
                _this.removeClass('disabled');
				if (response.type == 'success') {
					jQuery('#user-close-account-modal .modal-body .response').html('<div class="alert alert-success alert-dismissible">'+response.message+'<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></div>');									
					window.location.href = response.redirect_to;
				} else {			
					jQuery('#user-close-account-modal .modal-body .response').html('<div class="alert alert-danger alert-dismissible">'+response.message+'<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></div>');  
                }
            }
        });
    });
	// profilePhoto
	$(document).on('change', 'input.avatar', function (e) {
        e.preventDefault();      
        var _this = jQuery(this);
        jQuery('.profile-img-inner').append(loader);
		jQuery('.author-thumbnail').append(loader);
		_this.addClass('disabled');
		var file = e.target.files;
		var data = new FormData();
		data.append("action", "dpfl_profilePhoto");
		$.each(file, function(key, value){
    		data.append("avatar", value);
  		});
        jQuery.ajax({
           url: dpfl_custom_vars.ajaxurl,
			type: 'POST',
			data: data,
			cache: false,
			dataType: 'json',
			processData: false,
			contentType: false,
            success: function (response) {
               jQuery('.profile-img-inner').find(loader_wrapper).remove();
			   jQuery('.author-thumbnail').find(loader_wrapper).remove();
			   _this.removeClass('disabled');
                if (response.type == 'success') {
					jQuery('.profile-img .ajax-response').html('<div class="alert alert-success alert-dismissible">'+response.message+'<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></div>');									
					jQuery('.profile-img-inner img').attr('src', response.src);
					jQuery('.author-thumbnail img').attr('src', response.src_sidebar);
				} else {			
					jQuery('.profile-img .ajax-response').html('<div class="alert alert-danger alert-dismissible">'+response.message+'<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></div>');  
                }
            }
        });
    });
	// remove avatar
	jQuery(document).on('click', '.remove-author-image', function (e) {
        e.preventDefault();      
        var _this = jQuery(this);
       jQuery('.profile-img-inner').append(loader);
		jQuery('.author-thumbnail').append(loader);
		_this.addClass('disabled');
		var remove_avatar = 1;
        jQuery.ajax({
            type: "POST",
            url: dpfl_custom_vars.ajaxurl,
			data: { 'action': 'dpfl_removeProfilePhoto', 'remove_avatar': remove_avatar},
            //data: '&action=dpfl_removeProfilePhoto',
            dataType: "json",
            success: function (response) {
				jQuery('.profile-img-inner').find(loader_wrapper).remove();
				jQuery('.author-thumbnail').find(loader_wrapper).remove();
				_this.removeClass('disabled');
                if (response.type == 'success') {
					jQuery('.profile-img .ajax-response').html('<div class="alert alert-success alert-dismissible">'+response.message+'<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></div>');									
					jQuery('.profile-img-inner img').attr('src', response.src);
					jQuery('.author-thumbnail img').attr('src', response.src_sidebar);
				} else {			
					jQuery('.profile-img .ajax-response').html('<div class="alert alert-danger alert-dismissible">'+response.message+'<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></div>');  
                }
            }
        });
    });
	// email verification
	jQuery(document).on('click', '.dpfl-email-verification.sendcode', function (e) {
        e.preventDefault();      
        var _this = jQuery(this);
       jQuery('#user-email-verification-modal .modal-body .response').html(loader);
	   _this.addClass('disabled');
        jQuery.ajax({
            type: "POST",
            url: dpfl_custom_vars.ajaxurl,
            data: '&action=dpfl_user_email_verification_code',
            dataType: "json",
            success: function (response) {
				jQuery('#user-email-verification-modal .modal-body .response').find(loader_wrapper).remove();
                _this.removeClass('disabled');
				if (response.type == 'success') {
					jQuery('#user-email-verification-modal .modal-body .response').html('<div class="alert alert-success alert-dismissible">'+response.message+'<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></div>');
                } else {			
					jQuery('#user-email-verification-modal .modal-body').html('<div class="alert alert-danger alert-dismissible">'+response.message+'<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></div>');  
					
				}
            }
        });
    });
	jQuery(document).on('click', '.dpfl-email-verification.action', function (e) {
        e.preventDefault();      
        var _this = jQuery(this);
		jQuery('#user-email-verification-modal .modal-body .response').html(loader);
		_this.addClass('disabled');
        var profileForm = _this.parents('form.dpfl-uev-form').serialize();
		console.log(profileForm);
        jQuery.ajax({
            type: "POST",
            url: dpfl_custom_vars.ajaxurl,
            data: profileForm + '&action=dpfl_user_email_verification',
            dataType: "json",
            success: function (response) {
				jQuery('#user-email-verification-modal .modal-body .response').find(loader_wrapper).remove();
                _this.removeClass('disabled');
				if (response.type == 'success') {
					jQuery('#user-email-verification-modal .modal-body').html('<div class="alert alert-success alert-dismissible">'+response.message+'<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></div>');
					load_verification_html();
					
                } else {
					jQuery('#user-email-verification-modal .modal-body').prepend('<div class="alert alert-danger alert-dismissible">'+response.message+'<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></div>');
					//load_verification_html();
				}
            }
        });
    });
	// Phone Verification
	jQuery(document).on('click', '.dpfl-phone-verification.sendcode', function (e) {
        e.preventDefault();      
        var _this = jQuery(this);
       jQuery('#user-phone-verification-modal .modal-body .response').html(loader);
	   _this.addClass('disabled');
        jQuery.ajax({
            type: "POST",
            url: dpfl_custom_vars.ajaxurl,
            data: '&action=dpfl_user_phone_verification_code',
            dataType: "json",
            success: function (response) {
				jQuery('#user-phone-verification-modal .modal-body .response').find(loader_wrapper).remove();
                _this.removeClass('disabled');
				if (response.type == 'success') {
					jQuery('#user-phone-verification-modal .modal-body .response').html('<div class="alert alert-success alert-dismissible">'+response.message+'<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></div>');
                } else {			
					jQuery('#user-phone-verification-modal .modal-body').html('<div class="alert alert-danger alert-dismissible">'+response.message+'<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></div>');  
                }
            }
        });
    });
	jQuery(document).on('click', '.dpfl-phone-verification.action', function (e) {
        e.preventDefault();      
        var _this = jQuery(this);
		jQuery('#user-phone-verification-modal .modal-body .response').html(loader);
		_this.addClass('disabled');
        var profileForm = _this.parents('form.dpfl-umv-form').serialize();
		console.log(profileForm);
        jQuery.ajax({
            type: "POST",
            url: dpfl_custom_vars.ajaxurl,
            data: profileForm + '&action=dpfl_user_phone_verification',
            dataType: "json",
            success: function (response) {
				jQuery('#user-phone-verification-modal .modal-body .response').find(loader_wrapper).remove();
                _this.removeClass('disabled');
				if (response.type == 'success') {
					jQuery('#user-phone-verification-modal .modal-body').html('<div class="alert alert-success alert-dismissible">'+response.message+'<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></div>');
					load_verification_html();
					
                } else {
					jQuery('#user-phone-verification-modal .modal-body').html('<div class="alert alert-danger alert-dismissible">'+response.message+'<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></div>');
                }
            }
        });
    });
	
	window.load_verification_html = function($) {
		jQuery(document).on('hide.bs.modal', '.user-verification-modal', function () {
			jQuery.ajax({
				type: "POST",
				url: dpfl_custom_vars.ajaxurl,
				data: { 'action': 'dpfl_user_verification_html_ajax'},
				dataType: "html",
				success: function (response) {
					jQuery('.dpfl-user-verification .verification-status-content').html(response);
				}
			});
		});
	}

})( jQuery );