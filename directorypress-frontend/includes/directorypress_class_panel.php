<?php 

class directorypress_dashboard_handler extends directorypress_public {
	public $listing_saved = 0;
	public $errors = array();
	public $success = array();
	public $listing_redirect_link = '';
	public function init($listing_args = array()) {
		global $directorypress_object, $directorypress_fsubmit_instance, $sitepress, $DIRECTORYPRESS_ADIMN_SETTINGS;
		
		parent::init($listing_args);

		if (!is_user_logged_in()) {
			if ($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_submit_login_page'] && $DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_submit_login_page'] != get_the_ID()) {
				$url = get_permalink($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_submit_login_page']);
				$url = add_query_arg('redirect_to', urlencode(get_permalink()), $url);
				wp_redirect($url);
			} else {
				//add_action('wp_enqueue_scripts', array($directorypress_fsubmit_instance, 'enqueue_login_scripts_styles'));
				$this->template = array(DPFL_TEMPLATES_PATH, 'login_form.php');
			}
		} else {
			if (isset($_POST['referer'])){
				$this->referer = $_POST['referer'];
			}else{
				$this->referer = wp_get_referer();
				if (isset($_POST['cancel']) && isset($_POST['referer'])) {
					wp_redirect($_POST['referer']);
					die();
				}
			}

			if (!$directorypress_object->action) {
				if (get_query_var('page')){
					$paged = get_query_var('page');
				}elseif (get_query_var('paged')){
					$paged = get_query_var('paged');
				}else{
					$paged = 1;
				}
				
			} else{
				$paged = -1;
			}
			if(isset($_GET['post_status'])){
				$status = $_GET['post_status'];
			}else{
				$status = 'any';
			}
			$args = array(
					'post_type' => DIRECTORYPRESS_POST_TYPE,
					'author' => get_current_user_id(),
					'paged' => $paged,
					'posts_per_page' => 10,
					'post_status' => $status
			);
			$args2 = array(
					'post_type' => DIRECTORYPRESS_POST_TYPE,
					'author' => get_current_user_id(),
					//'paged' => $paged,
					'posts_per_page' => 20,
					'post_status' => 'publish'
			);
			$args3 = array(
					'post_type' => DIRECTORYPRESS_POST_TYPE,
					'author' => get_current_user_id(),
					//'paged' => $paged,
					'posts_per_page' => 20,
					'post_status' => 'draft'
			);
			$args4 = array(
					'post_type' => DIRECTORYPRESS_POST_TYPE,
					'author' => get_current_user_id(),
					//'paged' => $paged,
					'posts_per_page' => 20,
					'post_status' => 'pending'
			);
			// property of claim addon
			//add_filter('posts_where', array($this, 'add_claimed_listings_where'));
			$this->query = new WP_Query($args);
			$this->query2 = new WP_Query($args2);
			$this->query3 = new WP_Query($args3);
			$this->query4 = new WP_Query($args4);
			//remove_filter('posts_where', array($this, 'add_claimed_listings_where'));
			wp_reset_postdata();
			
			$this->listings_count = $this->query->found_posts;
			$this->listings_count2 = $this->query2->found_posts;
			$this->listings_count3 = $this->query3->found_posts;
			$this->listings_count4 = $this->query4->found_posts;
			$listing_id = directorypress_get_input_value($_GET, 'listing_id');
			$this->active_tab = 'listings';

			if (!$directorypress_object->action) {
				
				$this->processQuery(false);
				$this->template = array(DPFL_TEMPLATES_PATH, 'directorypress_panel.php');
				$this->subtemplate = array(DPFL_TEMPLATES_PATH, 'adverts.php');
				
			} elseif ($directorypress_object->action == 'edit_advert' && $listing_id) {
				
				$this->edit_listing($listing_args);
				
			} elseif ($directorypress_object->action == 'profile') {
				$this->template = array(DPFL_TEMPLATES_PATH, 'directorypress_panel.php');
				$this->subtemplate = array(DPFL_TEMPLATES_PATH, 'profile.php');
				
			}elseif ($directorypress_object->action == 'messages') {
				$this->template = array(DPFL_TEMPLATES_PATH, 'directorypress_panel.php');
					$this->subtemplate = array(DPFL_TEMPLATES_PATH, 'messages.php');
					$this->active_tab = 'messages';
			}elseif (function_exists('wpml_object_id_filter') && $sitepress && $DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_enable_frontend_translations'] && $directorypress_object->action == 'add_translation' && isset($_GET['listing_id']) && isset($_GET['to_lang'])) {
				$master_post_id = $_GET['listing_id'];
				$lang_code = $_GET['to_lang'];

				global $iclTranslationManagement;

				require_once( ICL_PLUGIN_PATH . '/inc/translation-management/translation-management.class.php' );
				if (!isset($iclTranslationManagement))
					$iclTranslationManagement = new TranslationManagement;
				
				$post_type = get_post_type($master_post_id);
				if ($sitepress->is_translated_post_type($post_type)) {
					// WPML has special option sync_post_status, that controls post_status duplication
					if ($new_listing_id = $iclTranslationManagement->make_duplicate($master_post_id, $lang_code)) {
						$iclTranslationManagement->reset_duplicate_flag($new_listing_id);
						directorypress_add_notification(__('Translation was successfully created!', 'directorypress-frontend'));
						do_action('wpml_switch_language', $lang_code);
						wp_redirect(add_query_arg(array('directorypress_action' => 'edit_advert', 'listing_id' => $new_listing_id), get_permalink(apply_filters('wpml_object_id', $directorypress_object->dashboard_page_id, 'page', true, $lang_code))));
					} else {
						directorypress_add_notification(__('Translation was not created!', 'directorypress-frontend'), 'error');
						wp_redirect(directorypress_dashboardUrl());
					}
					die();
				}
			}
			$directorypress_object->listings_handler_property->get_current_listing($listing_id);
		}

		apply_filters('directorypress_dashboard_handler_construct', $this);
	}

	public function edit_listing($listing_args = array()) {
		global $directorypress_object, $DIRECTORYPRESS_ADIMN_SETTINGS;
		$_POST = $listing_args;
		if (isset($_POST['referer'])){
			$this->referer = $_POST['referer'];
		}else{
			$this->referer = wp_get_referer();
		}
		$listing_id = (isset($_POST['listing_id']) && !empty($_POST['listing_id']))? $_POST['listing_id']: directorypress_get_input_value($_GET, 'listing_id');
		//$listing_id = 5005;
		if ($listing_id && directorypress_user_permission_to_edit_listing($listing_id)) {
			//$this->listing_redirect_link = 'test3';
					$listing = directorypress_get_listing($listing_id);
					$directorypress_object->current_listing = $listing;
					$directorypress_object->listings_handler_property->current_listing = $listing;
					
					if (isset($_POST['submit'])) {
						//$this->listing_redirect_link = 'test3';
						$errors = array();

						if (!isset($_POST['post_title']) || !trim($_POST['post_title']) || $_POST['post_title'] == __('Auto Draft')) {
							$errors[] = __('Listing title field required', 'directorypress-frontend');
							$post_title = __('Auto Draft');
						} else
							$post_title = trim($_POST['post_title']);

						$post_categories_ids = array();
						
						if ($listing->package->category_number_allowed > 0) {
							
							if ($post_categories_ids = $directorypress_object->terms_validator->validateCategories($listing->package, $_POST, $errors)) {
								foreach ($post_categories_ids AS $key=>$id)
									$post_categories_ids[$key] = intval($id);
							}
							wp_set_object_terms($listing->post->ID, $post_categories_ids, DIRECTORYPRESS_CATEGORIES_TAX);
						}

						if ($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_enable_tags']) {
							if ($post_tags_ids = $directorypress_object->terms_validator->validateTags($_POST, $errors)) {
								foreach ($post_tags_ids AS $key=>$id)
									$post_tags_ids[$key] = intval($id);
							}
							wp_set_object_terms($listing->post->ID, $post_tags_ids, DIRECTORYPRESS_TAGS_TAX);
						}
						
						$directorypress_object->fields->save_values($listing->post->ID, $post_categories_ids, $listing->package->id, $errors, $_POST);
						
						if ($listing->package->location_number_allowed) {
							if ($validation_results = $directorypress_object->locations_handler->validate_locations($listing->package, $errors)) {
								$directorypress_object->locations_handler->save_locations($listing->package, $listing->post->ID, $validation_results);
							}
						}
						
						if ($listing->package->images_allowed || $listing->package->videos_allowed) {
							if ($validation_results = $directorypress_object->media_handler_property->validate_attachments($listing->package, $errors))
								$directorypress_object->media_handler_property->save_attachments($listing->package, $listing->post->ID, $validation_results);
						}
						
						if( isset($_POST['faqtitle']) && isset($_POST['faqanswer']) ){
							$faqQuestion = $_POST['faqtitle'];
							$faqanswer = $_POST['faqanswer'];
							$faqs = array('faqtitle'=>$faqQuestion,'faqanswer'=>$faqanswer);
							update_post_meta($listing->post->ID, '_listing_faqs', $faqs);
						}
						
						if ($DIRECTORYPRESS_ADIMN_SETTINGS['message_system'] == 'email_messages' && $DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_custom_contact_email']) {
							$directorypress_form_validation = new directorypress_form_validation();
							$directorypress_form_validation->set_rules('contact_email', __('Contact email', 'directorypress-frontend'), 'valid_email');
						
							if (!$directorypress_form_validation->run()) {
								$errors[] = $directorypress_form_validation->error_array();
							} else {
								update_post_meta($listing->post->ID, '_contact_email', $directorypress_form_validation->result_array('contact_email'));
							}
						}
						if(isset($_POST['post_markas'])){
							if(!metadata_exists('post', $listing->post->ID, '_listing_mark_as' ) ) {
								add_post_meta($listing->post->ID, '_listing_mark_as', $_POST['post_markas']);
							}else{
								update_post_meta($listing->post->ID, '_listing_mark_as', $_POST['post_markas']);
							}
						}
						
						
						if(isset($_POST['facebook_link'])){
							if(!metadata_exists('post', $listing->post->ID, 'facebook_link' ) ) {
								add_post_meta($listing->post->ID, 'facebook_link', $_POST['facebook_link']);
							}else{
								update_post_meta($listing->post->ID, 'facebook_link', $_POST['facebook_link']);
							}
						}
						
						if(isset($_POST['twitter_link'])){
							if(!metadata_exists('post', $listing->post->ID, 'twitter_link' ) ) {
								add_post_meta($listing->post->ID, 'twitter_link', $_POST['twitter_link']);
							}else{
								update_post_meta($listing->post->ID, 'twitter_link', $_POST['twitter_link']);
							}
						}
						
						if(isset($_POST['linkedin_link'])){
							if(!metadata_exists('post', $listing->post->ID, 'linkedin_link' ) ) {
								add_post_meta($listing->post->ID, 'linkedin_link', $_POST['linkedin_link']);
							}else{
								update_post_meta($listing->post->ID, 'linkedin_link', $_POST['linkedin_link']);
							}
						}
						
						if(isset($_POST['youtube_link'])){
							if(!metadata_exists('post', $listing->post->ID, 'youtube_link' ) ) {
								add_post_meta($listing->post->ID, 'youtube_link', $_POST['youtube_link']);
							}else{
								update_post_meta($listing->post->ID, 'youtube_link', $_POST['youtube_link']);
							}
						}
						
						if(isset($_POST['instagram_link'])){
							if(!metadata_exists('post', $listing->post->ID, 'instagram_link' ) ) {
								add_post_meta($listing->post->ID, 'instagram_link', $_POST['instagram_link']);
							}else{
								update_post_meta($listing->post->ID, 'instagram_link', $_POST['instagram_link']);
							}
						}
						
						// save custom listing metabox
						do_action('save_custom_listing_metabox', $listing->post->ID);
						// adapted for WPML
						global $sitepress;
						if (
						(
							(function_exists('wpml_object_id_filter') && $sitepress && $sitepress->get_default_language() != ICL_LANGUAGE_CODE && ($tos_page = $DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_tospage_'.ICL_LANGUAGE_CODE]))
							||
							(isset($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_tospage']) && !empty($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_tospage']))
						)
						&&
						(!isset($_POST['directorypress_tospage']) || !$_POST['directorypress_tospage'])
						) {
							$errors[] = __('Please check the box to agree the Terms of Services.', 'directorypress-frontend');
						}

						if ($errors) {
							$postarr = array(
									'ID' => $listing_id,
									'post_title' => apply_filters('directorypress_title_save_pre', $post_title, $listing),
									'post_name' => apply_filters('directorypress_name_save_pre', '', $listing),
									'post_content' => (isset($_POST['post_content'])) ? $_POST['post_content'] : '',
									'post_excerpt' => (isset($_POST['post_excerpt'])) ? $_POST['post_excerpt'] : ''
							);
							$result = wp_update_post($postarr, true);
							if (is_wp_error($result))
								$errors[] = $result->get_error_message();

							//foreach ($errors AS $error)
								$this->errors = $errors;
							//$listing = directorypress_get_listing($listing_id);
						} else {
							if (!$listing->package->package_no_expiry && $listing->status != 'expired') {
								if ($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_change_expiration_date'] || current_user_can('manage_options')) {
									$directorypress_object->listings_handler_property->change_listing_expiry();
								} else {
									$expiration_date = directorypress_expiry_date(current_time('timestamp'), $listing->package);
									add_post_meta($listing->post->ID, '_expiration_date', $expiration_date);
								}
							}
							

							if ($listing->post->post_status == 'publish') {
								if ($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_fsubmit_edit_moderation']) {
									$post_status = 'pending';
									update_post_meta($listing_id, '_listing_approved', false);
									update_post_meta($listing_id, '_requires_moderation', true);
								} else {
									$post_status = 'publish';
								}
							}
							if ($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_fsubmit_edit_moderation']) {
								$message = esc_attr__("Listing was saved successfully! Now it's awaiting moderators approval.", 'directorypress-frontend');
							} else {
								$message = __('Listing was saved successfully!', 'directorypress-frontend');
							}

							$postarr = array(
									'ID' => $listing_id,
									'post_title' => apply_filters('directorypress_title_save_pre', $post_title, $listing),
									'post_name' => apply_filters('directorypress_name_save_pre', '', $listing),
									'post_content' => (isset($_POST['post_content'])) ? $_POST['post_content'] : '',
									'post_excerpt' => (isset($_POST['post_excerpt'])) ? $_POST['post_excerpt'] : ''
							);
							if (isset($post_status))
								$postarr['post_status'] = $post_status;

							$result = wp_update_post($postarr, true);
							if (is_wp_error($result)) {
								$this->errors[] = $result->get_error_message();
							} else {
								directorypress_add_notification($message);
								
								if ($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_editlisting_admin_notification']) {
									// Wehn listing was published and became pending after modification
									if ($listing->post->post_status == 'publish' && $post_status == 'pending') {
										$author = wp_get_current_user();
	
										$subject = __('Notification about listing modification (do not reply)', 'directorypress-frontend');
										$body = str_replace('[user]', $author->display_name,
												str_replace('[listing]', $listing->title(),
												str_replace('[link]', admin_url('post.php?post='.$listing->post->ID.'&action=edit'),
										$DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_editlisting_admin_notification'])));
			
										directorypress_mail(directorypress_admin_email(), $subject, $body);
										$to = $DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_admin_notifications_phone_number'];
										if(directorypress_is_directorypress_twilio_active() && !empty($to)){
											directorypress_send_sms($to, $body);
										}
									}
								}
								
								if (!$this->referer || $listing->post->post_status != 'publish') {
									$this->referer = directorypress_dashboardUrl();
								}
								// 2 ways to redirect: listing single page and dashboard
								if (strpos($this->referer, directorypress_dashboardUrl()) !== false) {
									$this->listing_redirect_link = $this->referer;
								} else {
									// the slug could be changed and we will get 404
									$this->listing_redirect_link = get_permalink($listing->post->ID);
								}
								
								do_action('directorypress_frontend_edit_listing_after', apply_filters('directorypress_title_save_pre', $post_title, $listing), $listing->post->ID);
								
								$this->listing_saved = 1;
							}
							
						}
						
						// renew data inside $listing object
						$listing = directorypress_get_listing($listing_id);
						$directorypress_object->current_listing = $listing;
						$directorypress_object->listings_handler_property->current_listing = $listing;
						
					}

					$this->template = array(DPFL_TEMPLATES_PATH, 'directorypress_panel.php');
					$this->subtemplate = array(DPFL_TEMPLATES_PATH, 'edit_advert.php');
					
					if ($listing->package->location_number_allowed > 0) {
						add_action('wp_enqueue_scripts', array($directorypress_object->locations_handler, 'admin_enqueue_scripts_styles'));
						wp_enqueue_script('directorypress-terms-js');
					}
					
					if ($listing->package->images_allowed > 0 || $listing->package->videos_allowed > 0){
						add_action('wp_enqueue_scripts', array($directorypress_object->media_handler_property, 'admin_enqueue_scripts_styles'));
					}
				}
	}

	public function display() {
		$output =  dpfl_renderTemplate($this->template, array('public_handler' => $this), true);
		wp_reset_postdata();

		return $output;
	}
	
	
	public function enqueue_scripts_styles() {
		wp_register_script('directorypress_stats', DIRECTORYPRESS_RESOURCES_URL . 'js/min/chart.min.js');
		wp_enqueue_script('directorypress_stats');
	}
}

?>