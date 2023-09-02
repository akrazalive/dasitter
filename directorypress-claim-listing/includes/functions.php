<?php

function directorypress_has_claim_template($template) {
	$templates = array(
			$template
	);

	foreach ($templates AS $template_to_check) {
		if (is_file($template_to_check)) {
			return $template_to_check;
		}elseif (is_file(get_stylesheet_directory() . '/directorypress/public/' . $template_to_check)) {
			return get_stylesheet_directory() . '/directorypress/public/' . $template_to_check;
		}elseif (is_file(get_template_directory() . '/directorypress/public/' . $template_to_check)) {
			return get_template_directory() . '/directorypress/public/' . $template_to_check;
		}elseif (is_file(DIRECTORYPRESS_CLAIM_LISTING_TEMPLATES_PATH . $template_to_check)) {
			return DIRECTORYPRESS_CLAIM_LISTING_TEMPLATES_PATH . $template_to_check;
		}
	}

	return false;
}

if (!function_exists('directorypress_claim_display_template')) {
	function directorypress_claim_display_template($template, $args = array(), $return = false) {
	
		if ($args) {
			extract($args);
		}
		
		$template = apply_filters('directorypress_claim_display_template', $template, $args);
		
		if (is_array($template)) {
			$template_path = $template[0];
			$template_file = $template[1];
			$template = $template_path . $template_file;
		}
		
		$template = directorypress_has_claim_template($template);

		if ($template) {
			if ($return) {
				ob_start();
			}
		
			include($template);
			
			if ($return) {
				$output = ob_get_contents();
				ob_end_clean();
				return $output;
			}
		}
	}
}

// Claim Listing
if( !function_exists('dpcl_claimListing_form') ){
	function dpcl_claimListing_form(){
		global $directorypress_object, $DIRECTORYPRESS_ADIMN_SETTINGS;
		$response 	= array(); 
		
		$listing_id = !empty( $_POST['listing_id'] ) ? sanitize_text_field( $_POST['listing_id'] ) : '';
		$claimer_message = !empty( $_POST['claim_message'] ) ? sanitize_textarea_field( $_POST['claim_message'] ) : '';
		if (($listing = directorypress_get_listing($listing_id)) && $listing->is_claimable) {
			$claimer_id = get_current_user_id();
			if ($listing->post->post_author != $claimer_id) {
				if ($listing->claim->updateRecord($claimer_id, $claimer_message, 'pending')) {
					update_post_meta($listing_id, '_is_claimable', false);
					if ($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_claim_approval']) {
						if ($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_claim_notification']) {
							$author = get_userdata($listing->post->post_author);
							$claimer = get_userdata($claimer_id);
							$subject = __('Claim notification', 'directorypress-claim-listing');
							$body = str_replace('[author]', $author->display_name,
									str_replace('[listing]', $listing->post->post_title,
									str_replace('[claimer]', $claimer->display_name,
									str_replace('[link]', directorypress_dashboardUrl(array('listing_id' => $listing->post->ID, 'directorypress_action' => 'process_claim')),
									str_replace('[message]', $claimer_message,
									$DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_claim_notification'])))));
		
							directorypress_mail($author->user_email, $subject, $body);
							$to = $author->user_phone;
							if(directorypress_is_directorypress_twilio_active() && !empty($to)){
								directorypress_send_sms($to, $body);
							}
						}
						$response['type'] = 'success';
						$response['message'] = __('Claim sent successfully, A notification has been sent to current listing owner, Your would be informed via email upon approval by current owner.', 'directorypress-claim-listing');
					} else {
						// Automatically process claim without approval
						$listing->claim->approve();
						$response['type'] = 'success';
						$response['message'] = __('Claim approved successfully!', 'directorypress-claim-listing');
					}
				}
			} else{
				$response['type'] = 'error';
				$response['message'] = __('Can not claim own listing', 'directorypress-claim-listing');
			}
			wp_send_json($response);
		}
	}
	add_action('wp_ajax_dpcl_claimListing_form', 'dpcl_claimListing_form');
	add_action('wp_ajax_nopriv_dpcl_claimListing_form', 'dpcl_claimListing_form');
}
// property of claim addon
if( !function_exists('dpcl_claimListing_html') ){
	function dpcl_claimListing_html(){
		global $directorypress_object;
		$response 	= array(); 
		
		$listing_id = !empty( $_POST['listing_id'] ) ? sanitize_text_field( $_POST['listing_id'] ) : '';
		$listing = directorypress_get_listing($listing_id);
		
		echo '<div class="alert alert-warning">';
			printf(__('User "%s" has claimed the listing "%s", Please approve or decline claim below', 'directorypress-claim-listing'), $listing->claim->claimer->display_name, $listing->title());
		echo '</div>';
		if ($listing->claim->claimer_message){
			echo '<div class="aditional-note-from-claimer">';
				echo '<div class="heading">'.esc_html__('Additional note from claimer:', 'directorypress-claim-listing') .'</div>';
				echo '<p>'. $listing->claim->claimer_message .'</p>';
			echo'</div>';
		}
		echo '<div class="alert alert-info">';
			echo esc_html__('A notification will be served to new owner in case of approval.', 'directorypress-claim-listing');
		echo '</div>';
		?>
		
		<a href="<?php echo directorypress_dashboardUrl(array('directorypress_action' => 'process_claim', 'listing_id' => $listing_id, 'claim_action' => 'approve')); ?>" class="action-button btn btn-primary" data-claim-action="approve" data-listing-id="<?php echo $listing_id; ?>"><?php _e('Approve', 'directorypress-claim-listing'); ?></a>
		<a href="<?php echo directorypress_dashboardUrl(array('directorypress_action' => 'process_claim', 'listing_id' => $listing_id, 'claim_action' => 'decline')); ?>" class="action-button btn btn-primary" data-claim-action="decline" data-listing-id="<?php echo $listing_id; ?>"><?php _e('Decline', 'directorypress-claim-listing'); ?></a>
		<?php
		die;
	}
	add_action('wp_ajax_dpcl_claimListing_html', 'dpcl_claimListing_html');
	add_action('wp_ajax_nopriv_dpcl_claimListing_html', 'dpcl_claimListing_html');
}
// property of claim addon
if( !function_exists('dpcl_claimListingProcess') ){
	function dpcl_claimListingProcess(){
		global $directorypress_object, $DIRECTORYPRESS_ADIMN_SETTINGS;
		$user 		= wp_get_current_user();              	
        $response 	= array(); 
		
		$listing_id = !empty( $_POST['listing_id'] ) ? sanitize_text_field( $_POST['listing_id'] ) : '';
		$claim_action = !empty( $_POST['claim_action'] ) ? sanitize_text_field( $_POST['claim_action'] ) : '';
		//$new_package_id = !empty( $_POST['new_package_id'] ) ? $_POST['new_package_id'] : '';
		
		if (directorypress_user_permission_to_edit_listing($listing_id) && ($listing = directorypress_get_listing($listing_id)) && $listing->claim->isClaimed()) {
					//$this->action = 'show';
			//if (isset($_GET['claim_action']) && ($_GET['claim_action'] == 'approve' || $_GET['claim_action'] == 'decline')) {
			if ($claim_action == 'approve') {
				$listing->claim->approve();
				if ($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_claim_approval_notification']) {
					$claimer = get_userdata($listing->claim->claimer_id);
					$subject = __('Approval of claim notification', 'directorypress-claim-listing');
					$body = str_replace('[claimer]', $claimer->display_name,
							str_replace('[listing]', $listing->post->post_title,
							str_replace('[link]', directorypress_dashboardUrl(),
							$DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_claim_approval_notification'])));
							
					directorypress_mail($claimer->user_email, $subject, $body);
					$to = $claimer->user_phone;
					if(directorypress_is_directorypress_twilio_active() && !empty($to)){
						directorypress_send_sms($to, $body);
					}
				}
				$response['type'] = 'success';
				$response['message'] = __('Listing claim was approved successfully!', 'directorypress-claim-listing');
				//directorypress_add_notification(__('Listing claim was approved successfully!', 'directorypress-claim-listing'));
			}elseif ($claim_action == 'decline') {
				$listing->claim->deleteRecord();
				if ($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_claim_decline_notification']) {
					$claimer = get_userdata($listing->claim->claimer_id);
					$subject = __('Claim decline notification', 'directorypress-claim-listing');	
					$body = str_replace('[claimer]', $claimer->display_name,
							str_replace('[listing]', $listing->post->post_title,
							$DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_claim_decline_notification']));
									
					directorypress_mail($claimer->user_email, $subject, $body);
					$to = $claimer->user_phone;
					if(directorypress_is_directorypress_twilio_active() && !empty($to)){
						directorypress_send_sms($to, $body);
					}
				}
				update_post_meta($listing->post->ID, '_is_claimable', true);
				$response['type'] = 'decline';
				$response['message'] = __('Listing claim was declined!', 'directorypress-claim-listing');
			}
		} else{
			$response['type'] = 'error';
			$response['message'] = __('You are not able to manage this listing!', 'directorypress-claim-listing');
		}
		
		wp_send_json($response);
	}
	add_action('wp_ajax_dpcl_claimListingProcess', 'dpcl_claimListingProcess');
    add_action('wp_ajax_nopriv_dpcl_claimListingProcess', 'dpcl_claimListingProcess');
}
