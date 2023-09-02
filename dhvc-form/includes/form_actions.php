<?php
function dhvc_form_action_mymail($data){
	if(!defined('MYMAIL_DIR'))
		return array(
			'success' => false,
			'message'  => __( 'myMail Newsletters not exists!.','dhvc-form' ),
		);
		$form_id = $_REQUEST['_dhvc_form_id'];
		$lists = dhvc_form_get_post_meta('_mymail',$form_id,array());
		$double_opt_in = dhvc_form_get_post_meta('_mymail_double_opt_in',$form_id,0) == '1' ? true : false ;
		$userdata['firstname'] = isset($data['firstname']) ? trim(preg_replace('/\s*\[[^)]*\]/', '', $data['firstname'])) : '';
		$userdata['lastname'] = isset($data['lastname']) ? trim(preg_replace('/\s*\[[^)]*\]/', '', $data['lastname'])) : '';
		$email = isset($data['email']) ? $data['email'] : '';
		if(!dhvc_form_is_email($email)){
			return array(
				'success' => false,
				'message'  => __( 'The email address isn\'t correct.','dhvc-form' ),
			);
		}
		$ret = mymail_subscribe( $email, $userdata, $lists, $double_opt_in, true);
		if (!$ret ) {
			return array(
				'success' => false,
				'message'  => __( 'Not Subscribe to our Newsletters!','dhvc-form' ),
			);
		} else {
			return array(
				'success' => true,
				'message'  => __( 'Subscribe to our Newsletters successful!', 'dhvc-form' ),
			);
		}
}

function dhvc_form_action_mailpoet($data){
	if(!class_exists('WYSIJA') && !class_exists(\MailPoet\API\API::class)){
		return array(
			'success' => false,
			'message'  => __( 'MailPoet Newsletters not exists!','dhvc-form' ),
		);
	}
	$form_id = $_REQUEST['_dhvc_form_id'];
	$list_ids = dhvc_form_get_post_meta('_mailpoet',$form_id,array());
	$first_name = isset($data['firstname']) ? trim($data['firstname']) : (isset($data['first_name'])  ? trim($data['first_name'])  : '');
	$last_name = isset($data['lastname']) ? trim($data['lastname']) : (isset($data['last_name'])  ? trim($data['last_name'])  : '');
	$email = isset($data['email']) ? $data['email'] : '';
	if(!dhvc_form_is_email($email)){
		return array(
			'success' => false,
			'message'  => __( 'The email address isn\'t correct.','dhvc-form' ),
		);
	}
	$result = false;
	$message = '';
	if (class_exists(\MailPoet\API\API::class)) {
		// Get MailPoet API instance
		$mailpoet_api = \MailPoet\API\API::MP('v1');
		// Fill subscribed data from $_POST (for simplicity it expects that subscriber field ids are used as input names)
		$subscriber = array(
			'email' 		=> $email,
			'first_name' 	=> $first_name,
			'last_name' 	=> $last_name
		);
		$subscriber = apply_filters('dhvc_form_mailpoet_subscriber', $subscriber, $data, $form_id);
		$subscriberOption = apply_filters('dhvc_form_mailpoet_subscriber_options', array());
		// Check if subscriber exists. If subscriber doesn't exist an exception is thrown
		$exists = false;
		try {
			$exists = $mailpoet_api->getSubscriber($subscriber['email']);
		} catch (\Exception $e) {}
		
		try { 
			if (!$exists) {
				// Subscriber doesn't exist let's create one
				$result = $mailpoet_api->addSubscriber($subscriber, $list_ids, $subscriberOption);
			} else {
				// In case subscriber exists just add him to new lists
				$result = $mailpoet_api->subscribeToLists($subscriber['email'], $list_ids, $subscriberOption);
			}
		} catch (\Exception $e) {
			$message = $e->getMessage();
		}
	}elseif (class_exists('WYSIJA')){
		$list_submit['user_list']['list_ids'] = $list_ids;
		$list_submit['user']['firstname'] = $first_name;
		$list_submit['user']['lastname'] = $last_name;
		$list_submit['user']['email'] = $email;
		//WYSIJA_help_user
		$helper_user = WYSIJA::get('user','helper');
		$result = $helper_user->addSubscriber($list_submit);
		$helper_user_message = $helper_user->getMsgs();
		$message = isset($helper_user_message['error']) ? $helper_user_message['error'] : '';
	}
	if(!$result){
		return array(
			'success' => false,
			'message'  => $message,
		);
	}else{
		return array(
			'success' => true,
			'message'  => sprintf(__( 'MailPoet Added: %s to list <strong>%s</strong>','dhvc-form' ),$email,implode(', ',dhvc_form_get_mailpoet_subscribers_list($list_ids))),
		);
	}
}

function dhvc_form_action_mailchimp($data){
	$mailchimp_api = dhvc_form_get_option('mailchimp_api',false);
	$success=false;
	$message='';
	if($mailchimp_api){
		if(!class_exists('Mailchimp')){
			require_once DHVC_FORM_DIR.'/includes/mailchimp/Mailchimp.php';
		}
		$mailchimp = new Mailchimp(
			$mailchimp_api,
			array(
				'ssl_verifypeer' => false
			)
		);
		$fname=isset($data['name']) ? '':'';
		
		$mailchimp_list_id = dhvc_form_get_post_meta('_mailchimp_list', $data['_dhvc_form_id']);
		if(empty($mailchimp_list_id)){
		    $mailchimp_list_id = dhvc_form_get_option('mailchimp_list',0);
		}
		$mailchimp_list_id = apply_filters('dhvc_form_mailchimp_list', $mailchimp_list_id, $data);
		
		$first_name = isset($data['first_name']) ? $data['first_name']:(isset($data['name']) ? $data['name'] : '');
		$last_name = isset($data['last_name']) ? $data['last_name']:(isset($data['name']) ? $data['name'] : '');
		$email_address = isset($data['email']) ? $data['email'] : '';
		$merge_vars = array(
			'FNAME' => $first_name,
			'LNAME' => $last_name,
		);
		$mailchimp_group_name = dhvc_form_get_option('mailchimp_group_name','');
		$mailchimp_group = dhvc_form_get_option('mailchimp_group','');
		$groups = isset($data['groups']) ? $data['groups'] : explode(',', $mailchimp_group);
		if(!empty($mailchimp_group) && !empty($mailchimp_group_name)){
			$merge_vars['GROUPINGS'] = array(
				array(
					'name'=> $mailchimp_group_name, 
					'groups'=>$groups
				),
			);
		}
		$merge_vars = apply_filters('dhvc_form_mailchimp_merge_vars', $merge_vars, $data);
		$double_optin = dhvc_form_get_option('mailchimp_opt_in','') === '1' ? true : false;
		$replace_interests = dhvc_form_get_option('mailchimp_replace_interests','') === '1' ? true : false;
		$send_welcome = dhvc_form_get_option('mailchimp_welcome_email','') === '1' ? true : false;

		try{
			$subscriber = $mailchimp->lists->subscribe(
			    $mailchimp_list_id,
				array('email'=>$email_address),
				$merge_vars,
				'html',
				$double_optin,
				false,
				$replace_interests,
				$send_welcome
			);
			if(! empty( $subscriber['leid'] )){
				$success = true;
			}
		}catch (Exception $e){
			if($e->getCode() == 214){
				$success = true;
				$message = __('This email is already subscribed.','dhvc-form');
			}else{
				$success = false;
				$message = $e->getMessage();
			}
		}	
	}
	// Check the results of our Subscribe and provide the needed feedback
	if (!$success ) {
		return array(
			'success' => false,
			'message'  => !empty($message) ? $message : __( 'Not Subscribe to Mailchimp!','dhvc-form' ),
		);
	} else {
		return array(
			'success' => true,
			'message'  => !empty($message) ? $message : __( 'Subscribe to Mailchimp Successful!', 'dhvc-form' ),
		);
	}
}

function dhvc_form_action_login($data){
	$data['user_login']         = isset($data['username']) ?  $data['username'] : '';
	$data['user_password']      = isset($data['password']) ?  $data['password']: '';
	$data['remember']           = isset($data['rememberme']) ? $data['rememberme']:'';
	$secure_cookie = is_ssl() ? true : false;;
	$secure_cookie = apply_filters('dhvc_form_login_secure_cookie', $secure_cookie);
	$user = wp_signon( $data, $secure_cookie );
	// Check the results of our login and provide the needed feedback
	if ( is_wp_error( $user ) ) {
		return array(
			'success' => false,
			'message'  => __( 'Wrong Username or Password!','dhvc-form' ),
		);
	} else {
		return array(
			'success' => true,
			'message'  => __( 'Login Successful!', 'dhvc-form' ),
		);
	}
}

function dhvc_form_action_register($data){
	if(get_option( 'users_can_register' )){
		$user_login = isset($data['user_login']) ? $data['user_login'] : '';
		$user_email = isset($data['user_email']) ? $data['user_email'] : '';
		$user_password  = isset($data['user_password']) ? $data['user_password'] : '';
		$cuser_password = isset($data['cuser_password']) ? $data['cuser_password'] : '';
			
		$ret = _dhvc_form_register_new_user($user_login, $user_email,$user_password,$cuser_password,$data);


		if ( is_wp_error( $ret ) ) {
			return array(
				'success' => false,
				'message'   => $ret->get_error_message(),
			);
		} else {
			if ( apply_filters( 'dhvc_form_registration_auth_new_customer', true, $ret ) ) {
				wp_set_auth_cookie( $ret );
			}
			
			do_action('dhvc_form_register_new_user_success', $ret, $data);
			
			return array(
				'success'     => true,
				'message'	=> __( 'Registration complete.', 'dhvc-form' )
			);
		}
	}else {
		return array(
			'success' => false,
			'message'   =>__( 'Not allow register in site.', 'dhvc-form' ),
		);
	}
}

function _dhvc_form_register_new_user( $user_login, $user_email, $user_password='', $cuser_password='',$data=array()) {

	$errors = new WP_Error();
	$sanitized_user_login = sanitize_user( $user_login );
	$user_email = apply_filters( 'user_registration_email', $user_email );

	// Check the username was sanitized
	if ( $sanitized_user_login == '' ) {
		$errors->add( 'empty_username', __( 'Please enter a username.', 'dhvc-form' ) );
	} elseif ( ! validate_username( $user_login ) ) {
		$errors->add( 'invalid_username', __( 'This username is invalid because it uses illegal characters. Please enter a valid username.', 'dhvc-form' ) );
		$sanitized_user_login = '';
	} elseif ( username_exists( $sanitized_user_login ) ) {
		$errors->add( 'username_exists', __( 'This username is already registered. Please choose another one.', 'dhvc-form' ) );
	}

	// Check the email address
	if ( $user_email == '' ) {
		$errors->add( 'empty_email', __( 'Please type your email address.', 'dhvc-form' ) );
	} elseif ( ! is_email( $user_email ) ) {
		$errors->add( 'invalid_email', __( 'The email address isn\'t correct.', 'dhvc-form' ) );
		$user_email = '';
	} elseif ( email_exists( $user_email ) ) {
		$errors->add( 'email_exists', __( 'This email is already registered, please choose another one.', 'dhvc-form' ) );
	}
	$form_has_password = false;
	
	//Check the password
	if(empty($user_password)){
		$user_password = wp_generate_password( 12, false );
	}else{
		$form_has_password = true;
		if(strlen($user_password) < 6){
			$errors->add( 'minlength_password', __( 'Password must be 6 character long.', 'dhvc-form' ) );
		}elseif (empty($cuser_password)){
			$errors->add( 'not_cpassword', __( 'Not see password confirmation field.', 'dhvc-form' ) );
		}elseif ($user_password != $cuser_password){
			$errors->add( 'unequal_password', __( 'Passwords do not match.', 'dhvc-form' ) );
		}
	}

	$errors = apply_filters( 'registration_errors', $errors, $sanitized_user_login, $user_email );

	if ( $errors->get_error_code() ){
		return $errors;
	}

	$new_user_data = array(
		'user_login' => wp_slash($sanitized_user_login),
		'user_pass'  => $user_password,
		'user_email' => wp_slash($user_email),
	);
	
	$optional_user_data = array(
		'user_nicename',
		'user_url',
		'user_email',
		'display_name',
		'nickname',
		'first_name',
		'last_name',
		'description',
		'rich_editing',
		'user_registered',
		'role',
		'jabber',
		'aim',
		'yim',
		'show_admin_bar_front'
	);
	
	foreach ($optional_user_data as $v){
		if(isset($data[$v]) && !empty($data[$v])){
			$new_user_data[$v] = $data[$v];
		}
	}
		
	$new_user_data = apply_filters('dhvc_form_new_user_data', $new_user_data);
	$user_id = wp_insert_user( $new_user_data );
	
	if ( ! $user_id || is_wp_error( $user_id )  ) {
		$errors->add( 'registerfail', __( 'Couldn\'t register you... please contact the site administrator', 'dhvc-form' ) );
		return $errors;
	}
	
	if(apply_filters('dhvc_form_user_password_nag', false)){
		update_user_option( $user_id, 'default_password_nag', true, true ); // Set up the Password change nag.
	}
	
	do_action( 'register_new_user', $user_id );

	if(apply_filters('dhvc_form_new_user_notification', true)){
		//@todo
		$notify = $form_has_password ? 'admin' : '';
		$notify = apply_filters('dhvc_form_new_user_notify', $notify);
		
		wp_new_user_notification( $user_id, null, $notify );
	}
	
	// Update user meta.
	$user_meta = apply_filters('dhvc_form_save_user_meta', array());
	foreach ((array) $user_meta as $meta_key){
		if(isset($data[$meta_key]) && !empty($data[$meta_key])){
			update_user_meta( $user_id, $meta_key, $data[$meta_key] );
		}
	}
	
	if(!empty($user_password)){
		
		$user = get_userdata( $user_id );
		
		$data_login['user_login']             = $user->user_login;
		$data_login['user_password']          = $user_password;
		$user_login                    	      = wp_signon( $data_login, false );
	}

	return $user_id;
}

function dhvc_form_action_forgotten($data){
	$user_login = isset($data['user_login']) ? $data['user_login'] : '';
	
	$user_forgotten = _dhvc_form_action_retrieve_password( $user_login );

	if ( is_wp_error( $user_forgotten ) ) {
		return array(
			'success' 	 => false,
			'message' => $user_forgotten->get_error_message(),
		);
	} else {
		return array(
			'success'   => true,
			'message' => __( 'Password Reset. Please check your email.', 'dhvc-form' ),
		);
	}

}

function _dhvc_form_action_retrieve_password($post_user_login) {
	global $wpdb, $wp_hasher;

	$errors = new WP_Error();

	if ( empty( $post_user_login ) ) {
		$errors->add('empty_username', __('<strong>ERROR</strong>: Enter a username or email address.'));
	} elseif ( strpos( $post_user_login, '@' ) ) {
		$user_data = get_user_by( 'email', trim( $post_user_login ) );
		if ( empty( $user_data ) ){
			$errors->add('invalid_email', __('<strong>ERROR</strong>: There is no user registered with that email address.'));
		}
	} else {
		$login = trim($post_user_login);
		$user_data = get_user_by('login', $login);
	}

	do_action( 'lostpassword_post', $errors );

	if ( $errors->get_error_code() )
		return $errors;

	if ( !$user_data ) {
		$errors->add('invalidcombo', __('<strong>ERROR</strong>: Invalid username or email.'));
		return $errors;
	}

	// Redefining user_login ensures we return the right case in the email.
	$user_login = $user_data->user_login;
	$user_email = $user_data->user_email;

	do_action( 'retreive_password', $user_login );

	$allow = apply_filters( 'allow_password_reset', true, $user_data->ID );

	if ( ! $allow ){
		return new WP_Error('no_password_reset', __('Password reset is not allowed for this user'));
	}else if ( is_wp_error($allow) ){
		return $allow;
	}
	
	if(function_exists('get_password_reset_key')){
		$key = get_password_reset_key( $user_data );
	}else{
		$key = wp_generate_password( 20, false );
		do_action( 'retrieve_password_key', $user_login, $key );
		if ( empty( $wp_hasher ) ) {
			require_once ABSPATH . 'wp-includes/class-phpass.php';
			$wp_hasher = new PasswordHash( 8, true );
		}
		$hashed = time() . ':' . $wp_hasher->HashPassword( $key );
		$key_saved = $wpdb->update( $wpdb->users, array( 'user_activation_key' => $hashed ), array( 'user_login' => $user_login ) );
		if ( false === $key_saved ) {
			return new WP_Error( 'no_password_key_update', __( 'Could not save password reset key to database.' ) );
		}
	}


	if ( is_wp_error( $key ) ) {
		return $key;
	}

	$message = __('Someone has requested a password reset for the following account:') . "\r\n\r\n";
	$message .= network_home_url( '/' ) . "\r\n\r\n";
	$message .= sprintf(__('Username: %s'), $user_login) . "\r\n\r\n";
	$message .= __('If this was a mistake, just ignore this email and nothing will happen.') . "\r\n\r\n";
	$message .= __('To reset your password, visit the following address:') . "\r\n\r\n";
	$message .= '<' . network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user_login), 'login') . ">\r\n";

	if ( is_multisite() ){
		$blogname = $GLOBALS['current_site']->site_name;
	}else{
		$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
	}
	$title = sprintf( __('[%s] Password Reset'), $blogname );

	$title = apply_filters( 'retrieve_password_title', $title, $user_login, $user_data );

	$message = apply_filters( 'retrieve_password_message', $message, $key, $user_login, $user_data );

	if ( $message && !wp_mail( $user_email, wp_specialchars_decode( $title ), $message ) ){
		return new WP_Error( __('The email could not be sent.') . "<br />\n" . __('Possible reason: your host may have disabled the mail() function.') );
	}
	return true;
}