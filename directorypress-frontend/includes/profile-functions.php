<?php

// Profile Update
if( !function_exists( 'dpfl_ProfileUpdate' ) ){
	function dpfl_ProfileUpdate(){
		global $current_user;
		$response = array();
		//Validations        
    	$do_check = check_ajax_referer('dpfl_profile_update_request', 'dpfl_profile_update_request', false);
        if ($do_check == false) {
            $response['type'] = 'error';
            $response['message'] = esc_html__('No kiddies please!', 'directorypress-frontend');
            wp_send_json($response);            
        }

        $first_name = !empty( $_POST['first_name'] ) ? sanitize_text_field( $_POST['first_name'] ) : '';
        $last_name  = !empty( $_POST['last_name'] ) ? sanitize_text_field( $_POST['last_name'] ) : '';
		$nickname  = !empty( $_POST['nickname'] ) ? sanitize_text_field( $_POST['nickname'] ) : '';
        $phone = !empty( $_POST['user_phone'] ) ? sanitize_text_field( $_POST['user_phone'] ) : '';
		$whatsapp_number = !empty( $_POST['user_whatsapp_number'] ) ? sanitize_text_field( $_POST['user_whatsapp_number'] ) : '';
        $gender		= !empty( $_POST['gender'] ) ? $_POST['gender'] : '';
		$display_name = !empty( $_POST['display_name'] ) ? $_POST['display_name'] : '';
		$user_email = !empty( $_POST['email'] ) ? $_POST['email'] : '';
        $website = !empty( $_POST['user_url'] ) ? $_POST['user_url'] : '';
        $author_fb    = !empty( $_POST['author_fb'] ) ? $_POST['author_fb'] : '';
		$author_tw    = !empty( $_POST['author_tw'] ) ? $_POST['author_tw'] : '';
		$author_linkedin    = !empty( $_POST['author_linkedin'] ) ? $_POST['author_linkedin'] : '';
		$author_pinterest    = !empty( $_POST['author_pinterest'] ) ? $_POST['author_pinterest'] : '';
		$author_flickr    = !empty( $_POST['author_flickr'] ) ? $_POST['author_flickr'] : '';
		$author_behance    = !empty( $_POST['author_behance'] ) ? $_POST['author_behance'] : '';
		$author_dribbble    = !empty( $_POST['author_dribbble'] ) ? $_POST['author_dribbble'] : '';
		$author_ytube    = !empty( $_POST['author_ytube'] ) ? $_POST['author_ytube'] : '';
		$author_vimeo    = !empty( $_POST['author_vimeo'] ) ? $_POST['author_vimeo'] : '';
		$author_instagram    = !empty( $_POST['author_instagram'] ) ? $_POST['author_instagram'] : '';
		$user_password    = !empty( $_POST['author_instagram'] ) ? $_POST['author_instagram'] : '';
		//if($ALSP_ADIMN_SETTINGS['frontend_panel_social_links']){
			update_user_meta($current_user->ID, 'author_fb', $author_fb);
			update_user_meta($current_user->ID, 'author_tw', $author_tw);
			update_user_meta($current_user->ID, 'author_linkedin', $author_linkedin);
			update_user_meta($current_user->ID, 'author_pinterest', $author_pinterest);
			update_user_meta($current_user->ID, 'author_flickr', $author_flickr);
			update_user_meta($current_user->ID, 'author_behance', $author_behance);
			update_user_meta($current_user->ID, 'author_dribbble', $author_dribbble);
			update_user_meta($current_user->ID, 'author_ytube', $author_ytube);
			update_user_meta($current_user->ID, 'author_vimeo', $author_vimeo);
			update_user_meta($current_user->ID, 'author_instagram', $author_instagram);
		//}
		
		update_user_meta( $current_user->ID, 'display_name', $display_name );
		update_user_meta( $current_user->ID, 'gender', $gender );
        update_user_meta( $current_user->ID, 'first_name', $first_name );
        update_user_meta( $current_user->ID, 'last_name', $last_name );
		update_user_meta( $current_user->ID, 'nickname', $nickname );
        update_user_meta( $current_user->ID, 'user_phone', $phone );
		update_user_meta( $current_user->ID, 'user_whatsapp_number', $whatsapp_number );
        wp_update_user( array( 'ID' => $current_user->ID, 'user_url' => $website ) );
		
		//reset email verification before update data
		if($user_email != $current_user->data->user_email){
			update_user_meta( $current_user->ID, 'email_verification_status', '');
		}
		wp_update_user( array( 'ID' => $current_user->ID, 'user_email' => $user_email ) );
		
		//wp_update_user( array( 'ID' => $user->data->ID, 'user_pass' => esc_attr( $new_password ) ) );
        $response['type'] = 'success';
        $response['message'] = esc_html__('Profile Updated!', 'directorypress-frontend');
        wp_send_json($response); 
	}
	add_action('wp_ajax_dpfl_ProfileUpdate', 'dpfl_ProfileUpdate');
    add_action('wp_ajax_nopriv_dpfl_ProfileUpdate', 'dpfl_ProfileUpdate');
}
// Password Update
if (!function_exists('dpfl_PasswordUpdate')) {

    function dpfl_PasswordUpdate() {
    	$user 		= wp_get_current_user();              	
        $response 	= array(); 
		
        $do_check = check_ajax_referer('change_user_password_request', 'change_user_password_request', false);
        if ( $do_check == false ) {
            $response['type'] = 'error';
            $response['message'] = esc_html__('No kiddies please.', 'directorypress-frontend');
            wp_send_json($response);            
        }
		
        if( empty( $_POST['old-password'] ) || empty( $_POST['new-password'] ) || empty( $_POST['confirm-password'] ) ){
        	$response['type'] = 'error';
            $response['message'] = esc_html__('All the fields are required!', 'directorypress-frontend');
            wp_send_json($response);  
        }

        $old_password = sanitize_text_field( $_POST['old-password'] );  
        $new_password = sanitize_text_field( $_POST['new-password'] );
        $confirm_password = sanitize_text_field( $_POST['confirm-password'] );
        $matched	= wp_check_password($old_password, $user->user_pass, $user->data->ID);
        if ( $matched ){
        	if( $new_password != $confirm_password ){
        		$response['type'] = 'error';
            	$response['message'] = esc_html__('New password did not match!', 'directorypress-frontend');
            	wp_send_json($response); 
        	}
        	wp_update_user( array( 'ID' => $user->data->ID, 'user_pass' => esc_attr( $new_password ) ) );
        	$response['type'] = 'success';
            $response['message'] = esc_html__('Password Has Been Changed Successfully', 'directorypress-frontend');
            wp_send_json($response); 

        } else {
        	//Warning
        	$response['type'] = 'error';
            $response['message'] = esc_html__('Old password did not match', 'directorypress-frontend');
            wp_send_json($response);  
        }
            
        $response['type'] = 'error';
        $response['message'] = esc_html__('Something went wrong, please try again', 'directorypress-frontend');
        wp_send_json($response);   
        
    }

    add_action('wp_ajax_dpfl_PasswordUpdate', 'dpfl_PasswordUpdate');
    add_action('wp_ajax_dpfl_PasswordUpdate', 'dpfl_PasswordUpdate');
}

// Profile Photo Update
if( !function_exists('dpfl_profilePhoto') ){
	function dpfl_profilePhoto(){
		$user 		= wp_get_current_user();              	
        $response 	= array(); 
		$posted_data =  isset( $_POST ) ? $_POST : array();
		$file_data = isset( $_FILES ) ? $_FILES : array();
		$data = array_merge( $posted_data, $file_data );
		$avatar_field = $data['avatar'];
		if(!empty( $avatar_field )){
			$avatar_id = dpfl_handle_image_upload( $avatar_field );
			update_user_meta( $user->ID, 'avatar_id', $avatar_id);

			// New Image Src
			$avatar_id = get_user_meta( $user->ID, 'avatar_id', true );
			
			if(!empty($avatar_id) && is_numeric($avatar_id)) {
				$author_avatar_url = wp_get_attachment_image_src( $avatar_id, 'full' ); 
				$src = $author_avatar_url[0];
				$params = array( 'width' => 270, 'height' => 270, 'crop' => true );
				$params_sidebar = array( 'width' => 60, 'height' => 60, 'crop' => true );
				$src = bfi_thumb($src, $params );
				$src_sidebar = bfi_thumb($src, $params_sidebar );
			} else { 
				$src = get_avatar_url($user_ID, ['size' => '270']);	
				$src_sidebar = get_avatar_url($user_ID, ['size' => '60']);
			}
			$response['type'] = 'success';
			$response['message'] = esc_html__('Image Updated!', 'directorypress-frontend');
			$response['src'] = $src;
			$response['src_sidebar'] = $src_sidebar;
			wp_send_json($response); 
		
		}else{
			$response['type'] = 'error';
			$response['message'] = esc_html__('Error!', 'directorypress-frontend');
			wp_send_json($response); 
		}
	}
	add_action('wp_ajax_dpfl_profilePhoto', 'dpfl_profilePhoto');
	add_action('wp_ajax_nopriv_dpfl_profilePhoto', 'dpfl_profilePhoto');
}
// Remove Profile Photo
if( !function_exists('dpfl_removeProfilePhoto') ){
	function dpfl_removeProfilePhoto(){
		$user 		= wp_get_current_user();              	
        $response 	= array(); 
		//$posted_data =  isset( $_POST ) ? $_POST : array();
		//$file_data = isset( $_FILES ) ? $_FILES : array();
		//$data = array_merge( $posted_data, $file_data );
		//$avatar_field = $data['avatar'];
		$remove_avatar = $_POST['remove_avatar'];
		if($remove_avatar == 1){
			//$avatar_id = dpfl_handle_image_upload( $avatar_field );
			update_user_meta( $user->ID, 'avatar_id', '');

			// New Image Src
			
			$src = get_avatar_url($user_ID, ['size' => '270']);	
			$src_sidebar = get_avatar_url($user_ID, ['size' => '60']);
			
			$response['type'] = 'success';
			$response['message'] = esc_html__('Profile Photo Removed!', 'directorypress-frontend');
			$response['src'] = $src;
			$response['src_sidebar'] = $src_sidebar;
			wp_send_json($response); 
		
		}else{
			$response['type'] = 'error';
			$response['message'] = esc_html__('Error!', 'directorypress-frontend');
			wp_send_json($response); 
		}
	}
	add_action('wp_ajax_dpfl_removeProfilePhoto', 'dpfl_removeProfilePhoto');
	add_action('wp_ajax_nopriv_dpfl_removeProfilePhoto', 'dpfl_removeProfilePhoto');
}
// User Email verification

if( !function_exists('dpfl_user_email_verification_code') ){
	function dpfl_user_email_verification_code(){
		$user 		= wp_get_current_user();
		$user_data = get_userdata($user->ID);
        $response 	= array(); 
		$verification_code =  substr(md5(microtime()), 0, 8);
		update_user_meta( $user->ID, 'email_verification_status_code', $verification_code);
		$to = $user_data->user_email;
		$subject = esc_html__('Email verification code', 'directorypress-frontend');
		$body = $verification_code;
		$headers = array('Content-Type: text/html; charset=UTF-8');
			 
		wp_mail( $to, $subject, $body, $headers );
		$response['type'] = 'success';
		$response['message'] = esc_html__('verification code has been sent to your registered email id, Please check your inbox and provide verification code in input filed below', 'directorypress-frontend');
		wp_send_json($response);
	}
	add_action('wp_ajax_dpfl_user_email_verification_code', 'dpfl_user_email_verification_code');
	add_action('wp_ajax_nopriv_dpfl_user_email_verification_code', 'dpfl_user_email_verification_code');
}
if( !function_exists('dpfl_user_email_verification') ){
	function dpfl_user_email_verification(){
		$user 		= wp_get_current_user();              	
        $response 	= array(); 
		$verification_code_from_user =  $_POST['email_verification_status_code'];
		$verification_code_from_databse =  get_user_meta($user->ID, 'email_verification_status_code', true );
		if(!empty($verification_code_from_user) && $verification_code_from_databse == $verification_code_from_user){
			update_user_meta( $user->ID, 'email_verification_status', 'verified');

			$response['type'] = 'success';
			$response['message'] = esc_html__('email verified successfully', 'directorypress-frontend');
		
		}else{
			$response['type'] = 'error';
			$response['message'] = esc_html__('Please provide valid verification code', 'directorypress-frontend');
			//$response['html'] = '<input type="text" name="email_verification_status_code" class="form-control" placeholder="'.esc_html__('Insert Verification Code').'"><a href="javascript:void;" class="btn btn-primary dpfl-email-verification confirm-action">'.esc_html__('Verify E-mail', 'directorypress-frontend').'</a>';
		}
		wp_send_json($response);
	}
	add_action('wp_ajax_dpfl_user_email_verification', 'dpfl_user_email_verification');
	add_action('wp_ajax_nopriv_dpfl_user_email_verification', 'dpfl_user_email_verification');
}
// Phone verification
if( !function_exists('dpfl_user_phone_verification_code') ){
	function dpfl_user_phone_verification_code(){
		$user 		= wp_get_current_user();
		$user_data = get_userdata($user->ID);
        $response 	= array(); 
		$verification_code =  substr(md5(microtime()), 0, 8);
		update_user_meta( $user->ID, 'phone_verification_status_code', $verification_code);
		$to = get_user_meta( $user->ID, 'user_phone', true );
		if(directorypress_is_directorypress_twilio_active() && !empty($to)){
			directorypress_send_sms($to, $message);
		}
		$response['type'] = 'success';
		$response['message'] = esc_html__('verification code has been sent to your registered Phone number, Please check your inbox and provide verification code in input filed below', 'directorypress-frontend');
		wp_send_json($response);
	}
	add_action('wp_ajax_dpfl_user_phone_verification_code', 'dpfl_user_phone_verification_code');
	add_action('wp_ajax_nopriv_dpfl_user_phone_verification_code', 'dpfl_user_phone_verification_code');
}
if( !function_exists('dpfl_user_phone_verification') ){
	function dpfl_user_phone_verification(){
		$user 		= wp_get_current_user();              	
        $response 	= array(); 
		$verification_code_from_user =  $_POST['phone_verification_status_code'];
		$verification_code_from_databse =  get_user_meta($user->ID, 'phone_verification_status_code', true );
		if(!empty( $verification_code_from_user) && $verification_code_from_databse == $verification_code_from_user){
			update_user_meta( $user->ID, 'phone_verification_status', 'verified');

			$response['type'] = 'success';
			$response['message'] = esc_html__('Phone number verified successfully', 'directorypress-frontend');
		
		}else{
			$response['type'] = 'error';
			$response['message'] = esc_html__('Please provide valid verification code', 'directorypress-frontend');
		}
		wp_send_json($response);
	}
	add_action('wp_ajax_dpfl_user_phone_verification', 'dpfl_user_phone_verification');
	add_action('wp_ajax_nopriv_dpfl_user_phone_verification', 'dpfl_user_phone_verification');
}

// user verification html

if( !function_exists('dpfl_user_verification_html') ){
	function dpfl_user_verification_html(){
		$user 		= wp_get_current_user();              	
        $response 	= '';
		$uev_status = get_user_meta($user->ID, 'email_verification_status', true );
		
		if($uev_status == 'verified'){
			$uev_status_string = esc_html__('Email Verification', 'directorypress-frontend');
			$uev_status_link = '<i class="fas fa-check"></i>';
			$uev_icon = 'active';
		}else{
			$uev_status_string = esc_html__('Email Verification', 'directorypress-frontend');
			$uev_status_link = '<a class="user-email-verification-link" href="#" data-toggle="modal" data-target="#user-email-verification-modal">'.esc_html__('verify', 'directorypress-frontend').'</a>';
			$uev_icon = 'normal';
		}
		if(directorypress_is_directorypress_twilio_active()){
			$umv_status = get_user_meta($user->ID, 'phone_verification_status', true );
			if($umv_status == 'verified'){
				$umv_status_string = esc_html__('Phone Verification', 'directorypress-frontend');
				$umv_status_link = '<i class="fas fa-check"></i>';
				$umv_icon = 'active';
			}else{
				$umv_status_string = esc_html__('Phone Verification', 'directorypress-frontend');
				$umv_status_link = '<a class="user-phone-verification-link" href="#" data-toggle="modal" data-target="#user-phone-verification-modal">'.esc_html__('verify', 'directorypress-frontend').'</a>';
				$umv_icon = 'normal';
			}
		}
		?>
		<div class="verification-status-header"><h6><?php esc_html_e('User Verification', 'directorypress-frontend'); ?></h6></div>
			<div class="verification-status-content">
				<div class="email-verification-status"><img src="<?php echo DIRECTORYPRESS_FSUBMIT_RESOURCES_URL .'images/email-'.$uev_icon.'.png'; ?>" alt="email id" /><span><?php echo $uev_status_string; ?></span><?php echo $uev_status_link; ?></div>
				<?php if(directorypress_is_directorypress_twilio_active()): ?>
					<div class="phone-verification-status"><img src="<?php echo DIRECTORYPRESS_FSUBMIT_RESOURCES_URL .'images/phone-'.$umv_icon.'.png'; ?>" alt="email id" /><span><?php echo $umv_status_string; ?></span><?php echo $umv_status_link; ?></div>
				<?php endif; ?>
			</div>
	<?php
	}
	add_action('dpfl_user_verification_html', 'dpfl_user_verification_html');
}

if( !function_exists('dpfl_user_verification_html_ajax') ){
	function dpfl_user_verification_html_ajax(){
		$user 		= wp_get_current_user();              	
        $response 	= '';
		$uev_status = get_user_meta($user->ID, 'email_verification_status', true );
		if($uev_status == 'verified'){
			$uev_status_string = esc_html__('Email Verification', 'directorypress-frontend');
			$uev_status_link = '<i class="fas fa-check"></i>';
			$uev_icon = 'active';
		}else{
			$uev_status_string = esc_html__('Email Verification', 'directorypress-frontend');
			$uev_status_link = '<a class="user-email-verification-link" href="#" data-toggle="modal" data-target="#user-email-verification-modal">'.esc_html__('verify', 'directorypress-frontend').'</a>';
			$uev_icon = 'normal';
		}
		if(directorypress_is_directorypress_twilio_active()){
			$umv_status = get_user_meta($user->ID, 'phone_verification_status', true );
			if($umv_status == 'verified'){
				$umv_status_string = esc_html__('Phone Verification', 'directorypress-frontend');
				$umv_status_link = '<i class="fas fa-check"></i>';
				$umv_icon = 'active';
			}else{
				$umv_status_string = esc_html__('Phone Verification', 'directorypress-frontend');
				$umv_status_link = '<a class="user-phone-verification-link" href="#" data-toggle="modal" data-target="#user-phone-verification-modal">'.esc_html__('verify', 'directorypress-frontend').'</a>';
				$umv_icon = 'normal';
			}
		}
		?>
			
		<div class="email-verification-status"><img src="<?php echo DIRECTORYPRESS_FSUBMIT_RESOURCES_URL .'images/email-'.$uev_icon.'.png'; ?>" alt="email id" /><span><?php echo $uev_status_string; ?></span><?php echo $uev_status_link; ?></div>
		<?php if(directorypress_is_directorypress_twilio_active()): ?>
			<div class="phone-verification-status"><img src="<?php echo DIRECTORYPRESS_FSUBMIT_RESOURCES_URL .'images/phone-'.$umv_icon.'.png'; ?>" alt="email id" /><span><?php echo $umv_status_string; ?></span><?php echo $umv_status_link; ?></div>
		<?php endif; ?>	
		<?php
		die();
	}
	add_action('wp_ajax_dpfl_user_verification_html_ajax', 'dpfl_user_verification_html_ajax');
	add_action('wp_ajax_nopriv_dpfl_user_verification_html_ajax', 'dpfl_user_verification_html_ajax');
}
if( !function_exists('dpfl_user_verification_modal_html') ){
	function dpfl_user_verification_modal_html(){
		?>
		<div class="modal fade user-verification-modal" id="user-email-verification-modal" role="dialog">
			<div class="modal-dialog modal-dialog-centered">
					<!-- Modal content-->
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal">&times;</button>
							<h4 class="modal-title"><?php echo __('Email Verification', 'directorypress-frontend'); ?></h4>
						</div>
						<div class="modal-body">
							<form class="dpfl-uev-form">
								<div class="response">
									<div class="alert alert-info"><?php echo esc_html__('Click Get New link for verification code, Code will be sent to your registered email.', 'directorypress-frontend'); ?></div>
								</div>
								<div class="dpfl-input-field">
									<input type="text" name="email_verification_status_code" class="form-control" placeholder="<?php echo esc_html__('Insert Verification Code'); ?>" />
								</div>
								<a href="javascript:void;" class="btn btn-primary dpfl-email-verification action"><?php echo esc_html__('Verify E-mail', 'directorypress-frontend'); ?></a>
								<a href="javascript:void;" class="btn btn-primary dpfl-email-verification sendcode"><?php echo esc_html__('Get New', 'directorypress-frontend'); ?></a>		
							</form>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo esc_html__('Close', 'directorypress-frontend'); ?></button>
						</div>
					</div>
			</div>
		</div>
		<?php if(directorypress_is_directorypress_twilio_active()): ?>
				<div class="modal fade user-verification-modal" id="user-phone-verification-modal" role="dialog">
					<div class="modal-dialog modal-dialog-centered">
						<!-- Modal content-->
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
								<h4 class="modal-title"><?php echo __('Phone Verification', 'directorypress-frontend'); ?></h4>
							</div>
							<div class="modal-body">
								<form class="dpfl-umv-form">
									<div class="response">
										<div class="alert alert-info"><?php echo esc_html__('Click Get New link for verification code, Code will be sent to your registered Phone Number.', 'directorypress-frontend'); ?></div>
									</div>
									<div class="dpfl-input-field">
										<input type="text" name="phone_verification_status_code" class="form-control" placeholder="<?php echo esc_html__('Insert Verification Code'); ?>" />
									</div>
									<a href="javascript:void;" class="btn btn-primary dpfl-phone-verification action"><?php echo esc_html__('Verify Phone', 'directorypress-frontend'); ?></a>
									<a href="javascript:void;" class="btn btn-primary dpfl-phone-verification sendcode"><?php echo esc_html__('Get New', 'directorypress-frontend'); ?></a>		
								</form>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo esc_html__('Close', 'directorypress-frontend'); ?></button>
							</div>
						</div>
					</div>
				</div>
		<?php endif; ?>
	<?php
	}
	add_action('wp_footer', 'dpfl_user_verification_modal_html');
}
// Password Update
if (!function_exists('dpfl_closeUserAccount')) {

    function dpfl_closeUserAccount() {
    	global $current_user;             	
        $response 	= array(); 
		
        $do_check = check_ajax_referer('close_user_account_request', 'close_user_account_request', false);
        if ( $do_check == false ) {
            $response['type'] = 'error';
            $response['message'] = esc_html__('No kiddies please.', 'directorypress-frontend');
            wp_send_json($response);            
        }
		
		
        if(is_user_logged_in()) {
			if(!current_user_can('administrator')){
				if($current_user->ID) {
					require_once( ABSPATH.'wp-admin/includes/user.php' );
					wp_delete_user( $current_user->ID );
					$response['type'] = 'success';
					$response['message'] = esc_html__('Account Deleted Permanently!', 'directorypress-frontend');
					$response['redirect_to'] = home_url('/');
					//do_action('alsp_redirect_home_page');
				}else{
					$response['type'] = 'error';
					$response['message'] = esc_html__('Something went wrong, please try again.', 'directorypress-frontend');
				}
			}else{    
				$response['type'] = 'error';
				$response['message'] = esc_html__('You Can not delete Administrator Account Here.', 'directorypress-frontend');
			}
		}else{    
			$response['type'] = 'error';
			$response['message'] = esc_html__('You are not allowed to perform this action.', 'directorypress-frontend');
        }
		
		wp_send_json($response);   
        
    }

    add_action('wp_ajax_dpfl_closeUserAccount', 'dpfl_closeUserAccount');
    add_action('wp_ajax_dpfl_closeUserAccount', 'dpfl_closeUserAccount');
}
if( !function_exists('dpfl_user_close_account_modal_html') ){
	function dpfl_user_close_account_modal_html(){
		?>
		<div class="modal fade user-close-account-modal" id="user-close-account-modal" role="dialog">
			<div class="modal-dialog modal-dialog-centered">
				<!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title"><?php echo __('Close Your Account', 'directorypress-frontend'); ?></h4>
					</div>
					<div class="modal-body">
						<div class="response">
							<div class="alert alert-warning"><?php echo esc_html__('Are you sure? Be aware this action can not undo.', 'directorypress-frontend'); ?></div>
						</div>	
					</div>
					<div class="modal-footer">
						<form action="">
							<button type="button" class="btn btn-danger close-account-action"><?php echo esc_html__('Delete Account', 'directorypress-frontend'); ?></button>
							<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo esc_html__('Cancel', 'directorypress-frontend'); ?></button>
							<?php wp_nonce_field('close_user_account_request', 'close_user_account_request'); ?>
						</form>
					</div>
				</div>
			</div>
		</div>
	<?php
	}
	add_action('wp_footer', 'dpfl_user_close_account_modal_html');
}