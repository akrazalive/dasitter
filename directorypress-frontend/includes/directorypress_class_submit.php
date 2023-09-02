<?php 

class directorypress_submit_handler extends directorypress_public {
	public $packages = array();
	public $template_args = array();
	public $listing_created = 0;
	public $errors = array();
	public $success = array();

	public function init($args = array()) {
		global $directorypress_object, $directorypress_fsubmit_instance, $DIRECTORYPRESS_ADIMN_SETTINGS;

		parent::init($args);
		$pp_col = (isset($DIRECTORYPRESS_ADIMN_SETTINGS['pp_option_col']))? $DIRECTORYPRESS_ADIMN_SETTINGS['pp_option_col']: 3;
		$shortcode_atts = array_merge(array(
				'show_period' => $DIRECTORYPRESS_ADIMN_SETTINGS['pp_option_period'],
				'show_has_sticky' => $DIRECTORYPRESS_ADIMN_SETTINGS['pp_option_has_sticky'],
				'show_has_featured' => $DIRECTORYPRESS_ADIMN_SETTINGS['pp_option_has_featured'],
				'show_categories' => $DIRECTORYPRESS_ADIMN_SETTINGS['pp_option_category'],
				'show_locations' => $DIRECTORYPRESS_ADIMN_SETTINGS['pp_option_locations'],
				'show_images' => $DIRECTORYPRESS_ADIMN_SETTINGS['pp_option_images'],
				'show_videos' => $DIRECTORYPRESS_ADIMN_SETTINGS['pp_option_video'],
				'columns_same_height' => 1,
				'columns' => $pp_col,
				'packages' => null,
				'directorytype' => null,
		), $args);
		
		$this->args = $shortcode_atts;
		
		if ($this->args['directorytype']) {
			$directorytype = $directorypress_object->directorytypes->directory_by_id($this->args['directorytype']);
			$directorypress_object->setup_current_page_directorytype($directorytype);
		} else {
			$directorytype = $directorypress_object->current_directorytype;
		}
		$this->template_args['directorytype'] = $directorytype;
		
		$this->packages = $directorypress_object->packages->packages_array;
		
			if ($this->args['packages']) {
				$packages_ids = array_filter(array_map('trim', explode(',', $this->args['packages'])));
				$this->packages = array_intersect_key($directorypress_object->packages->packages_array, array_flip($packages_ids));
			} elseif ($directorytype->packages && class_exists('Directorypress_Extended_Locations')) {
				$this->packages = array_intersect_key($directorypress_object->packages->packages_array, array_flip($directorytype->packages));
			}
		
		$this->packages = apply_filters('directorypress_submission_packages', $this->packages);
		
		$is_package_selected = (isset($_GET['package']) && is_numeric($_GET['package']))? $_GET['package']: '';
		
		if($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_is_email_verification_required'] && !$this->user_email_verification_status()){
			$this->template = array(DPFL_TEMPLATES_PATH, 'email_verification_required.php');
		}elseif($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_is_phone_verification_required'] && directorypress_is_directorypress_twilio_active() && !$this->user_phone_verification_status()){
			$this->template = array(DPFL_TEMPLATES_PATH, 'phone_verification_required.php');
		}elseif ($this->package_selected($is_package_selected)) {
			$this->template = array(DPFL_TEMPLATES_PATH, 'create_advert_packages.php');
		} elseif (count($this->packages)) {
			
			$package_id = $this->get_package_id($is_package_selected);
			
			if (!array_key_exists($package_id, $this->packages)) {
				directorypress_add_notification(__('You are not allowed to submit in this package!', 'directorypress-frontend'), 'error');
				wp_redirect(directorypress_submitUrl());
			}

			if ($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_fsubmit_login_mode'] == 1 && !is_user_logged_in()) {
				$this->get_login_template($package_id);
			} else {
				$this->template = array(DPFL_TEMPLATES_PATH, 'create_advert.php');
				$this->get_submit_form_template($args);
			}
		}
		
		apply_filters('directorypress_submit_handler_construct', $this);
	}
	public function user_email_verification_status() {
		$user = wp_get_current_user();
		$status = get_user_meta($user->ID, 'email_verification_status', true );
		if($status == 'verified'){
			return true;
		}
	}
	
	public function user_phone_verification_status() {
		$user = wp_get_current_user();
		$status = get_user_meta($user->ID, 'phone_verification_status', true );
		if($status == 'verified'){
			return true;
		}
	}
	
	public function package_selected($package_id = '') {
		global $DIRECTORYPRESS_ADIMN_SETTINGS, $directorypress_object;
		if (
			(!isset($package_id) || !is_numeric($package_id) || !array_key_exists($package_id, $directorypress_object->packages->packages_array))
			&&
			count($this->packages) > 1
			&&
			(!$DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_hide_choose_package_page'] || !$directorypress_object->listings_packages->is_any_listing_to_create())
			&&
			directorypress_is_payment_manager_active()
		){
			return true;
		}
		return false;
	}
	public function get_package_id($get_package = '') {
		global $DIRECTORYPRESS_ADIMN_SETTINGS, $directorypress_object;
		$package_id = 0;
		if(!directorypress_is_payment_manager_active()){
			$package = $directorypress_object->packages->get_default_package();
			$package_id = $package->id;
		}else{
			if (count($this->packages) == 1) {
				$_packages = array_keys($this->packages);
				$package_id = array_shift($_packages);
			} elseif ($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_hide_choose_package_page'] && $directorypress_object->listings_packages->is_any_listing_to_create()) {
				$package_id = $directorypress_object->listings_packages->is_any_listing_to_create();
			} elseif (isset($get_package) && is_numeric($get_package) && array_key_exists($get_package, $directorypress_object->packages->packages_array)) {
				$package_id = $get_package;
			} elseif ($package = $directorypress_object->packages->get_default_package()) {
				$package_id = $package->id;
			}
		}
		return $package_id;
	}
	public function get_login_template($package_id) {
		
		global $DIRECTORYPRESS_ADIMN_SETTINGS;
		if ($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_submit_login_page'] && $DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_submit_login_page'] != get_the_ID()) {
			$url = get_permalink($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_submit_login_page']);
			$url = add_query_arg('redirect_to', urlencode(directorypress_submitUrl(array('package' => $package_id))), $url);
			wp_redirect($url);
		} else {
			$this->template = array(DPFL_TEMPLATES_PATH, 'login_form.php');
		}
	}
	
	public function get_submit_form_template($args = array()) {
		
		global $DIRECTORYPRESS_ADIMN_SETTINGS, $directorypress_object;
		$_POST = $args;
		$is_package_selected = (isset($_GET['package']) && is_numeric($_GET['package']))? $_GET['package']: '';
		$package_id = $this->get_package_id($is_package_selected);
		$this->directorypress_user_contact_name = '';
		$this->directorypress_user_contact_email = '';
		if (!isset($_POST['listing_id']) || !isset($_POST['listing_id_hash']) || !is_numeric($_POST['listing_id']) || md5($_POST['listing_id'] . wp_salt()) != $_POST['listing_id_hash']) {
			// Create Auto-Draft
			$new_post_args = array(
				'post_title' => __('Auto Draft', 'directorypress-frontend'),
				'post_type' => DIRECTORYPRESS_POST_TYPE,
				'post_status' => 'auto-draft'
			);
			if ($new_post_id = wp_insert_post($new_post_args)) {
				$directorypress_object->listings_handler_property->current_listing = new directorypress_listing($package_id);
				$directorypress_object->listings_handler_property->save_initial_draft($new_post_id);

				$listing = directorypress_pull_current_listing_admin();
			}
		} elseif (isset($_POST['submit']) && (isset($_POST['_submit_nonce']) && wp_verify_nonce($_POST['_submit_nonce'], 'directorypress_submit'))) {
					// This is existed Auto-Draft
					
					$listing_id = $_POST['listing_id'];

					$listing = directorypress_get_listing($listing_id);
					$directorypress_object->current_listing = $listing;
					$directorypress_object->listings_handler_property->current_listing = $listing;
					
					$errors = array();

					if (!is_user_logged_in() && ($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_fsubmit_login_mode'] == 2 || $DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_fsubmit_login_mode'] == 3)) {
						if ($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_fsubmit_login_mode'] == 2)
							$required = '|required';
						else
							$required = '';
						$directorypress_form_validation = new directorypress_form_validation();
						$directorypress_form_validation->set_rules('directorypress_user_contact_name', __('Contact Name', 'directorypress-frontend'), $required);
						$directorypress_form_validation->set_rules('directorypress_user_contact_email', __('Contact Email', 'directorypress-frontend'), 'valid_email' . $required);
						if (!$directorypress_form_validation->run()) {
							$user_valid = false;
							$errors[] = $directorypress_form_validation->error_array();
						} else
							$user_valid = true;

						$this->directorypress_user_contact_name = $directorypress_form_validation->result_array('directorypress_user_contact_name');
						$this->directorypress_user_contact_email = $directorypress_form_validation->result_array('directorypress_user_contact_email');
					}

					// title
					if (!isset($_POST['post_title']) || !trim($_POST['post_title']) || $_POST['post_title'] == __('Auto Draft', 'directorypress-frontend')) {
						$errors[] = __('Listing title field required', 'directorypress-frontend');
						$post_title = __('Auto Draft', 'directorypress-frontend');
					} else {
						$post_title = trim($_POST['post_title']);
					}

					// categories
					$post_categories_ids = array();
					if ($listing->package->category_number_allowed > 0) {
						if ($post_categories_ids = $directorypress_object->terms_validator->validateCategories($listing->package, $_POST, $errors)) {
							
							foreach ($post_categories_ids AS $key=>$id)
								$post_categories_ids[$key] = intval($id);
						}
						wp_set_object_terms($listing->post->ID, $post_categories_ids, DIRECTORYPRESS_CATEGORIES_TAX);
					}
					
					// tags
					if ($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_enable_tags']) {
						if ($post_tags_ids = $directorypress_object->terms_validator->validateTags($_POST, $errors)) {
							foreach ($post_tags_ids AS $key=>$id)
								$post_tags_ids[$key] = intval($id);
						}
						wp_set_object_terms($listing->post->ID, $post_tags_ids, DIRECTORYPRESS_TAGS_TAX);
					}
					
					// fields
					$directorypress_object->fields->save_values($listing->post->ID, $post_categories_ids, $listing->package->id, $errors, $_POST);
					
					// address
					if ($listing->package->location_number_allowed) {
						if ($validation_results = $directorypress_object->locations_handler->validate_locations($listing->package, $errors)) {
							$directorypress_object->locations_handler->save_locations($listing->package, $listing->post->ID, $validation_results);
						}
					}
					
					// media
					if ($listing->package->images_allowed || $listing->package->videos_allowed) {
						if ($validation_results = $directorypress_object->media_handler_property->validate_attachments($listing->package, $errors)) {
							$directorypress_object->media_handler_property->save_attachments($listing->package, $listing->post->ID, $validation_results);
						}
					}
					
					// faqs
					if( isset($_POST['faqtitle']) && isset($_POST['faqanswer']) ){
						$faqQuestion = $_POST['faqtitle'];
						$faqanswer = $_POST['faqanswer'];
						$faqs = array('faqtitle'=>$faqQuestion,'faqanswer'=>$faqanswer);
						add_post_meta($listing->post->ID, '_listing_faqs', $faqs);
					}
					
					// social profiles
					if(isset($_POST['facebook_link'])){	
						add_post_meta($listing->post->ID, 'facebook_link', $_POST['facebook_link']);		
					}
					
					if(isset($_POST['twitter_link'])){	
						add_post_meta($listing->post->ID, 'twitter_link', $_POST['twitter_link']);		
					}
					
					if(isset($_POST['linkedin_link'])){	
						add_post_meta($listing->post->ID, 'linkedin_link', $_POST['linkedin_link']);		
					}
					
					if(isset($_POST['youtube_link'])){	
						add_post_meta($listing->post->ID, 'youtube_link', $_POST['youtube_link']);		
					}
					
					if(isset($_POST['instagram_link'])){	
						add_post_meta($listing->post->ID, 'instagram_link', $_POST['instagram_link']);		
					}
					
					do_action('save_custom_listing_metabox', $listing->post->ID);
					
					// contact form
					if ($DIRECTORYPRESS_ADIMN_SETTINGS['message_system'] == 'email_messages' && $DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_custom_contact_email']) {
						$directorypress_form_validation = new directorypress_form_validation();
						$directorypress_form_validation->set_rules('contact_email', __('Contact email', 'directorypress-frontend'), 'valid_email');
					
						if (!$directorypress_form_validation->run()) {
							$errors[] = $directorypress_form_validation->error_array();
						} else {
							update_post_meta($listing->post->ID, '_contact_email', $directorypress_form_validation->result_array('contact_email'));
						}
					}
					
					if (!directorypress_recaptcha_validated()) {
						$errors[] = esc_attr__("Anti-bot test wasn't passed!", 'directorypress-frontend');
					}

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
								'post_content' => (isset($_POST['post_content']))? $_POST['post_content']  : '',
								'post_excerpt' => (isset($_POST['post_excerpt']))? $_POST['post_excerpt'] : '',
								'post_type' => DIRECTORYPRESS_POST_TYPE,
						);
						$result = wp_update_post($postarr, true);
						if (is_wp_error($result)) {
							$errors[] = $result->get_error_message();
						}
							
						//foreach ($errors AS $error) {
							$this->errors = $errors;
						//}
					} else {
						if (!is_user_logged_in() && ($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_fsubmit_login_mode'] == 2 || $DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_fsubmit_login_mode'] == 3 || $DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_fsubmit_login_mode'] == 4)) {
							if (email_exists($this->directorypress_user_contact_email)) {
								$user = get_user_by('email', $this->directorypress_user_contact_email);
								$post_author_id = $user->ID;
								$post_author_username = $user->user_login;
							} else {
								$user_contact_name = trim($this->directorypress_user_contact_name);
								if ($user_contact_name) {
									$display_author_name = $user_contact_name;
									if (get_user_by('login', $user_contact_name))
										$login_author_name = $user_contact_name . '_' . time();
									else
										$login_author_name = $user_contact_name;
								} else {
									$display_author_name = 'Author_' . time();
									$login_author_name = 'Author_' . time();
								}
								if ($this->directorypress_user_contact_email) {
									$author_email = $this->directorypress_user_contact_email;
								} else {
									$author_email = '';
								}
								
								$password = wp_generate_password(6, false);
								
								$post_author_id = wp_insert_user(array(
										'display_name' => $display_author_name,
										'user_login' => $login_author_name,
										'user_email' => $author_email,
										'user_pass' => $password
								));
								$post_author_username = $login_author_name;
								
								if (!is_wp_error($post_author_id) && $author_email) {
									// WP auto-login
									wp_set_current_user($post_author_id);
									wp_set_auth_cookie($post_author_id);
									do_action('wp_login', $post_author_username, get_userdata($post_author_id));
	
									if ($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_newuser_notification']) {
										$subject = __('Registration notification', 'directorypress-frontend');
										$body = str_replace('[author]', $display_author_name,
												str_replace('[listing]', $post_title,
												str_replace('[login]', $login_author_name,
												str_replace('[password]', $password,
										$DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_newuser_notification']))));

										if (directorypress_mail($author_email, $subject, $body)){
											directorypress_add_notification(__('New user was created and added to the site, login and password were sent to provided contact email.', 'directorypress-frontend'));
										}
										
										//if(directorypress_is_directorypress_twilio_active()){
										//	$to = get_user_meta( $post_author_id, 'user_phone', true );
											//if(!empty($to)){
												//directorypress_send_sms($to, $body);
											//}
										//}
									}
								}
							}

						} elseif (is_user_logged_in())
							$post_author_id = get_current_user_id();
						else
							$post_author_id = 0;

						if ($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_fsubmit_moderation']) {
							$post_status = 'pending';
							$message = esc_attr__("Listing was saved successfully! Now it's awaiting moderators approval.", 'directorypress-frontend');
							update_post_meta($listing_id, '_requires_moderation', true);
						} else {
							$post_status = 'publish';
							$message = __('Listing was saved successfully!', 'directorypress-frontend');
						}

						$postarr = array(
								'ID' => $listing_id,
								'post_title' => apply_filters('directorypress_title_save_pre', $post_title, $listing),
								'post_name' => apply_filters('directorypress_name_save_pre', '', $listing),
								'post_content' => (isset($_POST['post_content']) ? $_POST['post_content'] : ''),
								'post_excerpt' => (isset($_POST['post_excerpt']) ? $_POST['post_excerpt'] : ''),
								'post_type' => DIRECTORYPRESS_POST_TYPE,
								'post_author' => $post_author_id,
								'post_status' => $post_status
						);
						$result = wp_update_post($postarr, true);
						if (is_wp_error($result)) {
							$this->errors[] = $result->get_error_message();
						} else {
							if (!$listing->package->package_no_expiry) {
								if ($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_change_expiration_date'] || current_user_can('manage_options'))
									$directorypress_object->listings_handler_property->change_listing_expiry();
								else {
									$expiration_date = directorypress_expiry_date(current_time('timestamp'), $listing->package);
									add_post_meta($listing->post->ID, '_expiration_date', $expiration_date);
								}
							}

							add_post_meta($listing->post->ID, '_listing_created', true);
							add_post_meta($listing->post->ID, '_order_date', time());
							add_post_meta($listing->post->ID, '_listing_status', 'active');

							directorypress_add_notification($message);
							
							// renew data inside $listing object
							$listing = directorypress_get_listing($listing_id);
							$to = $DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_admin_notifications_phone_number'];
							
							if (!empty($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_newlisting_admin_notification'])) {
								$author = get_userdata($listing->post->post_author);

								$subject = __('Notification about new listing creation (do not reply)', 'directorypress-frontend');
								$body = str_replace('[user]', $author->display_name,
										str_replace('[listing]', $post_title,
										str_replace('[link]', admin_url('post.php?post='.$listing->post->ID.'&action=edit'),
								$DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_newlisting_admin_notification'])));
	
								directorypress_mail(directorypress_admin_email(), $subject, $body);
								$to = $DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_admin_notifications_phone_number'];
								if(directorypress_is_directorypress_twilio_active() && !empty($to)){
									directorypress_send_sms($to, $body);
								}
							}
							if (isset($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_listing_submission_notification']) && !empty($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_listing_submission_notification'])) {
								$author = get_userdata($listing->post->post_author);

								$subject = __('New Submission', 'directorypress-frontend');
								$body = str_replace('[author]', $author->display_name,
										str_replace('[listing]', $post_title,
										str_replace('[link]', directorypress_dashboardUrl(),
								$DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_listing_submission_notification'])));
							
								directorypress_mail($author->user_email, $subject, $body);
								
								if(directorypress_is_directorypress_twilio_active() && !empty($to)){
									directorypress_send_sms($to, $body);
								}
							}
							apply_filters('directorypress_listing_creation_front', $listing);
							
							do_action('directorypress_frontend_submit_listing_after', apply_filters('directorypress_title_save_pre', $post_title, $listing), $listing->post->ID);
							
							
							$this->listing_created = 1;
						}
					}
					// renew data inside $listing object
					$listing = directorypress_get_listing($listing_id);
					$directorypress_object->current_listing = $listing;
					$directorypress_object->listings_handler_property->current_listing = $listing;
				}
				
				if ($listing->package->location_number_allowed > 0) {
					add_action('wp_enqueue_scripts', array($directorypress_object->locations_handler, 'admin_enqueue_scripts_styles'));
					wp_enqueue_script('directorypress-terms-js');
				}
				if ($listing->package->images_allowed > 0 || $listing->package->videos_allowed > 0)
					add_action('wp_enqueue_scripts', array($directorypress_object->media_handler_property, 'admin_enqueue_scripts_styles'));
				
	}
	
	public function display() {
		$output =  dpfl_renderTemplate($this->template, array_merge(array('public_handler' => $this), $this->template_args), true);
		wp_reset_postdata();

		return $output;
	}
}