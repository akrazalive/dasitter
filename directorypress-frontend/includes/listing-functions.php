<?php

// Admin Notice
if( !function_exists('dpfl_AdminNote_html') ){
	function dpfl_AdminNote_html(){            	
        $response 	= array(); 
		
		$listing_id = !empty( $_POST['listing_id'] ) ? sanitize_text_field( $_POST['listing_id'] ) : '';
		if(metadata_exists('post', $listing_id, '_notice_to_admin' ) ) {
			$content = get_post_meta( $listing_id, '_notice_to_admin', true );
		}else{
			$content = esc_html__('Your comment here!', 'directorypress-frontend');
		}
		echo '<div class="alert alert-info">'. esc_html__('Feel free to contact us if you have a special query regarding this listing.', 'directorypress-frontend').'</div>';
		echo '<form class="note_to_admin_listing_form">';
			echo '<textarea name="_notice_to_admin" row="35" placeholder="'.$content.'"></textarea>';
			echo '<input type="hidden" name="listing_id" value="'.$listing_id.'">';
		echo '</form>';
		
		die;		
	}
	add_action('wp_ajax_dpfl_AdminNote_html', 'dpfl_AdminNote_html');
    add_action('wp_ajax_nopriv_dpfl_AdminNote_html', 'dpfl_AdminNote_html');
}
if( !function_exists('dpfl_AdminNote') ){
	function dpfl_AdminNote(){
		global $directorypress_object;
		$user 		= wp_get_current_user();              	
        $response 	= array(); 
		
		$listing_id = !empty( $_POST['listing_id'] ) ? sanitize_text_field( $_POST['listing_id'] ) : '';
        $content  = !empty($_POST['_notice_to_admin']) ? $_POST['_notice_to_admin'] : '';
		
		if ($listing_id && directorypress_user_permission_to_edit_listing($listing_id) && ($listing = $directorypress_object->listings_handler_property->init_listing($listing_id))) {
				update_post_meta($listing_id, '_notice_to_admin', $content);
				$response['type'] = 'success';
				$response['message'] = esc_html__('Message Sent!', 'directorypress-frontend');
				wp_send_json($response); 
		}else{
			$response['type'] = 'error';
			$response['message'] = esc_html__('Error: You can not Manage this listing', 'directorypress-frontend');
			wp_send_json($response); 
		}
	}
	add_action('wp_ajax_dpfl_AdminNote', 'dpfl_AdminNote');
    add_action('wp_ajax_nopriv_dpfl_AdminNote', 'dpfl_AdminNote');
}

// Delete Listing
if( !function_exists('dpfl_deleteListing_html') ){
	function dpfl_deleteListing_html(){            	
        $response 	= array(); 
		
		$listing_id = !empty( $_POST['listing_id'] ) ? sanitize_text_field( $_POST['listing_id'] ) : '';
		echo '<div class="alert alert-warning">'. esc_html__('Listing will be deleted permanently along with all data.', 'directorypress-frontend').'</div>';
		echo '<form class="delete_listing_form">';
			echo '<input type="hidden" name="listing_id" value="'.$listing_id.'">';
		echo '</form>';
		die;		
	}
	add_action('wp_ajax_dpfl_deleteListing_html', 'dpfl_deleteListing_html');
    add_action('wp_ajax_nopriv_dpfl_deleteListing_html', 'dpfl_deleteListing_html');
}
if( !function_exists('dpfl_deleteListing') ){
	function dpfl_deleteListing(){
		global $directorypress_object;
		$user 		= wp_get_current_user();              	
        $response 	= array(); 
		
		$listing_id = !empty( $_POST['listing_id'] ) ? sanitize_text_field( $_POST['listing_id'] ) : '';
		if ($listing_id && directorypress_user_permission_to_edit_listing($listing_id) && ($listing = $directorypress_object->listings_handler_property->init_listing($listing_id))) {
				if (wp_delete_post($listing_id, true) !== FALSE) {
					$directorypress_object->listings_handler_property->delete_listing_data($listing_id);
					$response['type'] = 'success';
					$response['message'] = esc_html__('Deleted Sucessfully!', 'directorypress-frontend');
					wp_send_json($response);
					die();
				}else{
					$response['type'] = 'error';
					$response['message'] = esc_html__('Error: not allowed', 'directorypress-frontend');
					wp_send_json($response); 
				}
		}else{
			$response['type'] = 'error';
			$response['message'] = esc_html__('Error: You can not Delete this listing', 'directorypress-frontend');
			wp_send_json($response); 
		}
	}
	add_action('wp_ajax_dpfl_deleteListing', 'dpfl_deleteListing');
    add_action('wp_ajax_nopriv_dpfl_deleteListing', 'dpfl_deleteListing');
}

// BumpUp Listing
if( !function_exists('dpfl_bumpUpListing_html') ){
	function dpfl_bumpUpListing_html(){            	
        $response 	= array(); 
		
		$listing_id = !empty( $_POST['listing_id'] ) ? sanitize_text_field( $_POST['listing_id'] ) : '';
		echo '<div class="alert alert-info">'. esc_html__('Listing will be BumpedUp to the top of all listings.', 'directorypress-frontend').'</div>';
		echo '<form class="bumpup_listing_form">';
			echo '<input type="hidden" name="listing_id" value="'.$listing_id.'">';
		echo '</form>';
		die;		
	}
	add_action('wp_ajax_dpfl_bumpUpListing_html', 'dpfl_bumpUpListing_html');
    add_action('wp_ajax_nopriv_dpfl_bumpUpListing_html', 'dpfl_bumpUpListing_html');
}
if( !function_exists('dpfl_bumpUpListing') ){
	function dpfl_bumpUpListing(){
		global $directorypress_object;
		$user 		= wp_get_current_user();              	
        $response 	= array(); 
		$dashboard_object = new directorypress_dashboard_handler();
		$listing_id = !empty( $_POST['listing_id'] ) ? sanitize_text_field( $_POST['listing_id'] ) : '';
		
		if (directorypress_user_permission_to_edit_listing($listing_id)) {
			$listing = directorypress_get_listing($listing_id);
			$dashboard_object->action = 'show';
			if ($listing->process_bumpup()){
				$dashboard_object->action = 'raiseup';
				$response['type'] = 'success';
				$response['message'] = esc_html__('Listing BumpUp To Top', 'directorypress-frontend');
			}else{
				
			$response['type'] = 'success';
			$response['message'] = esc_html__('Notice: An order has been created, please pay by going to your orders to complete Bumpup process.', 'directorypress-frontend');
			}
		}else{
			$response['type'] = 'error';
			$response['message'] = esc_html__('Error: Not Allowed', 'directorypress-frontend');
		}
		wp_send_json($response); 		
	}
	add_action('wp_ajax_dpfl_bumpUpListing', 'dpfl_bumpUpListing');
    add_action('wp_ajax_nopriv_dpfl_bumpUpListing', 'dpfl_bumpUpListing');
}


// Renew Listing
if( !function_exists('dpfl_renewListing_html') ){
	function dpfl_renewListing_html(){
		$response 	= array(); 
		
		$listing_id = !empty( $_POST['listing_id'] ) ? sanitize_text_field( $_POST['listing_id'] ) : '';
		echo '<div class="alert alert-info">'. esc_html__('Listing will be Renewed as per purchased plan, Payment may apply if applicable.', 'directorypress-frontend').'</div>';
		echo '<form class="renew_listing_form">';
			echo '<input type="hidden" name="listing_id" value="'.$listing_id.'">';
		echo '</form>';
		die;
	}
	add_action('wp_ajax_dpfl_renewListing_html', 'dpfl_renewListing_html');
    add_action('wp_ajax_nopriv_dpfl_renewListing_html', 'dpfl_renewListing_html');
}
if( !function_exists('dpfl_renewListing') ){
	function dpfl_renewListing(){
		global $directorypress_object;
		$user 		= wp_get_current_user();              	
        $response 	= array(); 
		$dashboard_object = new directorypress_dashboard_handler();
		$listing_id = !empty( $_POST['listing_id'] ) ? sanitize_text_field( $_POST['listing_id'] ) : '';
		
		if (directorypress_user_permission_to_edit_listing($listing_id)) {
			$listing = directorypress_get_listing($listing_id);
			$dashboard_object->action = 'show';
			if ($listing->process_activation(true)){
				$dashboard_object->action = 'renew';
				$response['type'] = 'success';
				$response['message'] = esc_html__('Listing Renewed Sucessfully', 'directorypress-frontend');
			}else{
				
			$response['type'] = 'success';
			$response['message'] = esc_html__('Notice: An order has been created, please pay by going to your orders to complete Renew process.', 'directorypress-frontend');
			}
		}else{
			$response['type'] = 'error';
			$response['message'] = esc_html__('Error: Not Allowed', 'directorypress-frontend');
		}
		wp_send_json($response);
	}
	add_action('wp_ajax_dpfl_renewListing', 'dpfl_renewListing');
    add_action('wp_ajax_nopriv_dpfl_renewListing', 'dpfl_renewListing');
}

// Upgrade Listing
if( !function_exists('dpfl_upgradeListing_html') ){
	function dpfl_upgradeListing_html(){
		global $directorypress_object;
		$response 	= array(); 
		
		$listing_id = !empty( $_POST['listing_id'] ) ? sanitize_text_field( $_POST['listing_id'] ) : '';
		$listing = directorypress_get_listing($listing_id);
		//$new_package_id = !empty( $_POST['new_package_id'] ) ? $_POST['new_package_id'] : '';
		echo '<div class="alert alert-info">'. esc_html__('You can upgrade listing to available options.', 'directorypress-frontend').'</div>';
		echo '<form class="upgrade_listing_wrapper">';
			foreach ($directorypress_object->packages->packages_array AS $package){
				if($listing->package->id != $package->id && (!isset($listing->package->upgrade_meta[$package->id]) || !$listing->package->upgrade_meta[$package->id]['disabled'])){
					echo '<div><label><input type="radio" name="new_package_id" value="'.$package->id.'" />'.apply_filters('directorypress_package_upgrade_option', $package->name, $listing->package, $package),'</label></div>';
				}
			}
			echo '<input type="hidden" name="listing_id" value="'.$listing_id.'">';
		echo '</form>';
		die;
	}
	add_action('wp_ajax_dpfl_upgradeListing_html', 'dpfl_upgradeListing_html');
	add_action('wp_ajax_nopriv_dpfl_upgradeListing_html', 'dpfl_upgradeListing_html');
}
if( !function_exists('dpfl_upgradeListing') ){
	function dpfl_upgradeListing(){
		global $directorypress_object;
		$user 		= wp_get_current_user();              	
        $response 	= array(); 
		
		$listing_id = !empty( $_POST['listing_id'] ) ? sanitize_text_field( $_POST['listing_id'] ) : '';
		$new_package_id = !empty( $_POST['new_package_id'] ) ? $_POST['new_package_id'] : '';
		if (directorypress_user_permission_to_edit_listing($listing_id) && ($listing = directorypress_get_listing($listing_id)) /* && $listing->status == 'active' */) {
			$directorypress_object->action = 'show';
			
			$directorypress_form_validation = new directorypress_form_validation();
			$directorypress_form_validation->set_rules('new_package_id', __('New package ID', 'directorypress-frontend'), 'required|integer');

			if ($directorypress_form_validation->run()) {
				if ($listing->change_listing_package($directorypress_form_validation->result_array('new_package_id'))){
						
					$directorypress_object->action = 'upgrade';
					$response['message'] = esc_html__('Upgraded Sucessfully!', 'directorypress-frontend');
					
				}else{
					$response['message'] = esc_html__('Notice: An order has been created, please pay by going to your orders to complete upgradation.', 'directorypress-frontend');
				}
				$response['type'] = 'success';
			}else{
				$response['type'] = 'error';
				$response['message'] = esc_html__('Something Went Wrong, Please Try Again', 'directorypress-frontend');
			}
		}else{
			$response['type'] = 'error';
			$response['message'] = esc_html__('Not Allowed', 'directorypress-frontend');
		}
		wp_send_json($response);
	}
	add_action('wp_ajax_dpfl_upgradeListing', 'dpfl_upgradeListing');
    add_action('wp_ajax_nopriv_dpfl_upgradeListing', 'dpfl_upgradeListing');
}



// Listing Change Status
if( !function_exists('dpfl_listingStatusChange_triger') ){
	function dpfl_listingStatusChange_triger(){ 
		
		$listing_id = !empty( $_POST['listing_id'] ) ? sanitize_text_field( $_POST['listing_id'] ) : '';
		$current_status = get_post_status ( $listing_id); 
		$listing_status = get_post_meta($listing_id, '_listing_status', true);
		if($listing_status == 'active' && get_post_status ( $listing_id) == 'publish'){
				$new_status_text = 'Private';
				$current_status_text = 'Publich';
		}else{
				$new_status_text = 'Publish';
				$current_status_text = 'Private';
		}
											
	echo '<div id="listing_change_status" data-listing-id="'.$listing_id.'" class="alert alert-info">'. sprintf(esc_html__('Your Current Listing Status is %s you can change Listing Status to %s below.', 'directorypress-frontend'),$current_status_text, $new_status_text).'</div>';
  die();
	}
	add_action('wp_ajax_dpfl_listingStatusChange_triger', 'dpfl_listingStatusChange_triger');
	add_action('wp_ajax_nopriv_dpfl_listingStatusChange_triger', 'dpfl_listingStatusChange_triger');
}

if( !function_exists('dpfl_listingStatusChange') ){
	function dpfl_listingStatusChange(){           	
        $response 	= array(); 
		
		$listing_id = !empty( $_POST['listing_id'] ) ? sanitize_text_field( $_POST['listing_id'] ) : '';
		$current_status = get_post_status ($listing_id); 
		if ($listing_id && directorypress_user_permission_to_edit_listing($listing_id)) {
			if($current_status == 'private'){
				wp_update_post(array('ID' => $listing_id, 'post_status' => 'publish'));
				$response['message'] = esc_html__('Listing Status Changed to Publish Sucessfully!', 'directorypress-frontend');
				$response['button_text'] = esc_html__('Make Private', 'directorypress-frontend');
			}else{
				wp_update_post(array('ID' => $listing_id, 'post_status' => 'private'));
				$response['message'] = esc_html__('Listing Status Changed to Private Sucessfully!', 'directorypress-frontend');
				$response['button_text'] = esc_html__('Publish', 'directorypress-frontend');
			}
			$response['type'] = 'success';
		}else{
			$response['type'] = 'error';
			$response['message'] = esc_html__('Error: not allowed', 'directorypress-frontend');
			
		}
		
		wp_send_json($response); 
	}
	add_action('wp_ajax_dpfl_listingStatusChange', 'dpfl_listingStatusChange');
    add_action('wp_ajax_nopriv_dpfl_listingStatusChange', 'dpfl_listingStatusChange');
}

// listing performance ajax
if( !function_exists('directorypress_listing_peformance') ){
	function directorypress_listing_peformance(){
		$listing_id = !empty( $_POST['listing_id'] ) ? sanitize_text_field( $_POST['listing_id'] ) : ''; 
		return directorypress_listing_peformance_data($listing_id);
		die;

	}
	add_action('wp_ajax_directorypress_listing_peformance', 'directorypress_listing_peformance');
	add_action('wp_ajax_nopriv_directorypress_listing_peformance', 'directorypress_listing_peformance');
}
if( !function_exists('directorypress_listing_peformance_data') ){
	function directorypress_listing_peformance_data($listing_id){
		global $directorypress_object;

		$months_names = array(
			1 => __('January', 'directorypress-frontend'),	
			2 => __('February', 'directorypress-frontend'),	
			3 => __('March', 'directorypress-frontend'),	
			4 => __('April', 'directorypress-frontend'),	
			5 => __('May', 'directorypress-frontend'),	
			6 => __('June', 'directorypress-frontend'),	
			7 => __('July', 'directorypress-frontend'),	
			8 => __('August', 'directorypress-frontend'),	
			9 => __('September', 'directorypress-frontend'),	
			10 => __('October', 'directorypress-frontend'),	
			11 => __('November', 'directorypress-frontend'),	
			12 => __('December', 'directorypress-frontend'),	
		);
		if ($clicks_data = get_post_meta($listing_id, '_clicks_data', true)) {
			foreach ($clicks_data AS $month_year=>$count) {
				$month_year = explode('-', $month_year);
				$data[$month_year[1]][$month_year[0]] = $count;
			}
			ksort($data);
		}
		if (isset($data)): 

			foreach ($data AS $year=>$months_counts):
				echo '<h6>'. $year .'</h6>';
				echo '<canvas id="canvas-'.esc_attr($year).'" class="listing-performance-chart"></canvas>';
			?>
			<script>
			var chartData_<?php echo esc_attr($year); ?> = {
				labels : ["<?php echo implode('","', $months_names); ?>"],
				datasets : [
					{
						fillColor : "rgba(151,187,205,0.2)",
						strokeColor : "rgba(151,187,205,1)",
						type: 'line',
						<?php
						foreach ($months_names AS $month_num=>$name)
							if (!isset($months_counts[$month_num]))
								$months_counts[$month_num] = 0;
						ksort($months_counts);
						?>
						data : [<?php echo implode(',', $months_counts); ?>]
					}
				]
			};
		
			(function($) {
				"use strict";

				$(function() {
					var ctx_<?php echo esc_attr($year); ?> = document.getElementById("canvas-<?php echo esc_attr($year); ?>").getContext("2d");
					window.myLine_<?php echo esc_attr($year); ?> = new Chart(ctx_<?php echo esc_attr($year); ?>).Line(chartData_<?php echo esc_attr($year); ?>, {
						responsive: true
					});
				});
			})(jQuery);
			</script>
			<?php
			endforeach;
		endif;
		die();
	}
}

// Listing Change Status
if( !function_exists('dpfl_listingtrans_html') ){
	function dpfl_listingtrans_html(){ 
		
		$listing_id = !empty( $_POST['listing_id'] ) ? sanitize_text_field( $_POST['listing_id'] ) : '';
		global $sitepress, $DIRECTORYPRESS_ADIMN_SETTINGS;
		if (function_exists('wpml_object_id_filter') && $sitepress && $DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_enable_frontend_translations'] && ($languages = $sitepress->get_active_languages()) && count($languages) > 1){
			if (directorypress_user_permission_to_edit_listing($listing_id)){
														
				$trid = $sitepress->get_element_trid($listing_id, 'post_' . DIRECTORYPRESS_POST_TYPE);
				$translations = $sitepress->get_element_translations($trid);
				foreach ($languages AS $lang_code=>$lang):
				
					if ($lang_code != ICL_LANGUAGE_CODE){
						$lang_details = $sitepress->get_language_details($lang_code);
						do_action('wpml_switch_language', $lang_code);
						if (isset($translations[$lang_code])){
							echo '<div class="translation-language clearfix">';
								echo '<span class="language-flag"><img src="'.$sitepress->get_flag_url( $lang_code ).'" /></span>';
								echo '<span class="language-label">'.$lang_details['display_name'].'</span>';
								echo '<span class="language-action">';
									echo '<a class="btn btn-primary" href="'. add_query_arg(array('directorypress_action' => 'edit_advert', 'listing_id' => apply_filters('wpml_object_id', $listing_id, DIRECTORYPRESS_POST_TYPE, true, $lang_code)), get_permalink(apply_filters('wpml_object_id', $directorypress_object->dashboard_page_id, 'page', true, $lang_code))).'">'. esc_html__('Edit', 'sitepress').'</a>';
								echo '</span>';
							echo '</div>';
						}else{
							echo '<div class="translation-language clearfix">';
								echo '<span class="language-flag"><img src="'.$sitepress->get_flag_url( $lang_code ).'" /></span>';
								echo '<span class="language-label">'. $lang_details['display_name'].'</span>';
								echo '<span class="language-action">';
									echo '<a class="btn btn-primary" href="'. directorypress_dashboardUrl(array('directorypress_action' => 'add_translation', 'listing_id' => $listing_id, 'to_lang' => $lang_code)).'">'. esc_html__('Add', 'sitepress').'</a>';
								echo '</span>';
							echo '</div>';
						}
					}		
				endforeach;
				do_action('wpml_switch_language', ICL_LANGUAGE_CODE);
			}else{
				echo '<div class="alert alert-warning">'. esc_html__('No Permission.', 'directorypress-frontend').'</div>';
			}
			
		}else{
			echo '<div class="alert alert-warning">'. esc_html__('WPMP Plugin Required.', 'directorypress-frontend').'</div>';
		}
		
		die();
	}
	add_action('wp_ajax_dpfl_listingtrans_html', 'dpfl_listingtrans_html');
	add_action('wp_ajax_nopriv_dpfl_listingtrans_html', 'dpfl_listingtrans_html');
}

if( !function_exists('dpfl_listingtrans') ){
	function dpfl_listingtrans(){           	
        $response 	= array(); 
		
		$listing_id = !empty( $_POST['listing_id'] ) ? sanitize_text_field( $_POST['listing_id'] ) : '';
		$current_status = get_post_status ($listing_id); 
		if ($listing_id && directorypress_user_permission_to_edit_listing($listing_id)) {
			if($current_status == 'private'){
				wp_update_post(array('ID' => $listing_id, 'post_status' => 'publish'));
				$response['message'] = esc_html__('Listing Status Changed to Publish Sucessfully!', 'directorypress-frontend');
			}else{
				wp_update_post(array('ID' => $listing_id, 'post_status' => 'private'));
				$response['message'] = esc_html__('Listing Status Changed to Private Sucessfully!', 'directorypress-frontend');
			}
			$response['type'] = 'success';
		}else{
			$response['type'] = 'error';
			$response['message'] = esc_html__('Error: not allowed', 'directorypress-frontend');
			
		}
		
		wp_send_json($response); 
	}
	add_action('wp_ajax_dpfl_listingtrans', 'dpfl_listingtrans');
    add_action('wp_ajax_nopriv_dpfl_listingtrans', 'dpfl_listingtrans');
}

if( !function_exists('dpfl_submit_listing_html') ){
	function dpfl_submit_listing_html(){
		$response 	= array(); 
		$package = (!empty( $_POST['package'] )) ? $_POST['package'] : '';
		//$directorytype = !empty( $_POST['directorytype'] ) ? sanitize_text_field( $_POST['directorytype'] ) : '';
		$submit = new directorypress_submit_handler();
		//$args = array();
		$args['selected_package'] = $package;
		$submit->init($args);
		echo $submit->display();
		//print_r($submit->template);
		//echo $submit;
		
		die;
	}
	add_action('wp_ajax_dpfl_submit_listing_html', 'dpfl_submit_listing_html');
    add_action('wp_ajax_nopriv_dpfl_submit_listing_html', 'dpfl_submit_listing_html');
}

if( !function_exists('dpfl_action_modal_html') ){
	function dpfl_action_modal_html(){
		echo '<div id="listing_action_modal" class="modal fade" role="dialog">';
			echo '<div class="modal-dialog modal-dialog-centered">';
				//Modal content
				echo '<div class="modal-content">';
					echo '<div class="directorypress-modal-top-border"></div>';
					echo '<div class="modal-header">';
						echo '<button type="button" class="close" data-dismiss="modal">&times;</button>';
						echo '<h4 class="modal-title"></h4>';
					echo '</div>';
					echo '<div class="modal-body"></div>';
					echo '<div class="modal-footer">';
						echo '<button type="button" class="btn btn-default" data-dismiss="modal">'. esc_html__('Close', 'directorypress-frontend').'</button>';
					echo '</div>';
				echo '</div>';
			echo '</div>';
		echo '</div>';
	}
}
add_action('wp_footer', 'dpfl_action_modal_html');

if( !function_exists('dpfl_new_listng_submit') ){
	function dpfl_new_listng_submit(){
		global $directorypress_object, $DIRECTORYPRESS_ADIMN_SETTINGS;
		
		$response = array(); 
		$errors = '';
		
		// check security tokken first.
		$do_check = check_ajax_referer('directorypress_submit', '_submit_nonce', false);
		if ($do_check == false) {
			$response['type'] = 'error';
           $response = esc_html__('No kiddies please!', 'directorypress-extended-locations');        
        }
		
		$package = (!empty( $_POST['selected_package'] )) ? $_POST['selected_package'] : '';
		$args = $_POST;
		$args['submit'] = 'submit';
		
		// call submit
		$submit_handler = new directorypress_submit_handler();
		$submit_handler->get_submit_form_template($args);
		
		//response after submission
		if($submit_handler->listing_created == 1){
			
			$listing = directorypress_get_listing($args['listing_id']);
			if(directorypress_has_wc() && (isset($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_payments_addon']) && $DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_payments_addon'] == 'directorypress_woo_payment')){
				$package_product = new directorypress_listing_single_product();
			}
			if (directorypress_has_wc() && (isset($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_payments_addon']) && $DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_payments_addon'] == 'directorypress_woo_payment') && directorypress_is_payment_manager_active() && $listing && ($product = $package_product->get_product_by_package_id($listing->package->id)) && directorypress_recalcPrice($product->get_price()) > 0 && !$directorypress_object->listings_packages->can_user_create_listing_in_package($args['selected_package'])) {
				$redirect_to = $package_product->create_listing_single_order_ajax($listing->post->ID, $args['selected_package'], 'activation');
			}elseif ($directorypress_object->dashboard_page_url){
				$redirect_to = directorypress_dashboardUrl();
			}else{
				$redirect_to = directorypress_directorytype_url();
			}
				
			$redirect_to = apply_filters('directorypress_redirect_after_submit', $redirect_to);
			$response['type'] = 'success';
			$response['redirect_to'] = $redirect_to;
			
		}else{
			$response['type'] = 'error';
			 ob_start();
				foreach($submit_handler->errors AS $erorr){
					echo '<div class="alert alert-danger alert-dismissible">'. $erorr .'<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></div>';
				}
				$response['message'] = ob_get_contents();
			ob_end_clean();
		}
		
		wp_send_json($response); 
	}
}
add_action('wp_ajax_dpfl_new_listng_submit', 'dpfl_new_listng_submit');
add_action('wp_ajax_nopriv_dpfl_new_listng_submit', 'dpfl_new_listng_submit');

if( !function_exists('dpfl_updatListingData') ){
	function dpfl_updatListingData(){ 
		global $directorypress_object;
        $response = array();
		$do_check = check_ajax_referer('directorypress_edit', $_POST['_edit_nonce'], false);
		if ($do_check == false) {
			$response['type'] = 'error';
           $response['message'] = esc_html__('No kiddies please!', 'directorypress-extended-locations');        
        }
		
		$args = $_POST;
		$args['submit'] = 'submit';
		$panel_instance = new directorypress_dashboard_handler();
		$panel_instance->edit_listing($args);
		
		//response after submission
		if($panel_instance->listing_saved == 1){
			$redirect_to = $panel_instance->listing_redirect_link;
			$response['type'] = 'success';
			$response['redirect_to'] = $redirect_to;
		}else{
			 
			$response['type'] = 'error';
			 ob_start();
				
				foreach($panel_instance->errors AS $erorr){
					echo '<div class="alert alert-danger alert-dismissible">'. $erorr .'<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></div>';
				}
				//print_r($response);
				//die();
				$response['message'] = ob_get_contents();
			ob_end_clean();
		} 
		wp_send_json($response); 
	}
}
add_action('wp_ajax_dpfl_updatListingData', 'dpfl_updatListingData');
add_action('wp_ajax_nopriv_dpfl_updatListingData', 'dpfl_updatListingData');