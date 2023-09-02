<?php 
/**
 * Profile page template
 * @desc Dashboard.
 */
/*Global variables*/
global $current_user, $DIRECTORYPRESS_ADIMN_SETTINGS;
$email 		= $current_user->data->user_email;
$username 	= $current_user->data->user_login;
$website    = $current_user->data->user_url;
$first_name = get_user_meta( $current_user->ID, 'first_name', true);
$last_name  = get_user_meta( $current_user->ID, 'last_name', true);
$nickname  = get_user_meta( $current_user->ID, 'nickname', true);
$display_name = get_user_meta( $current_user->ID, 'display_name', true);
$user_phone = get_user_meta( $current_user->ID, 'user_phone', true);
$user_whatsapp_number = get_user_meta( $current_user->ID, 'user_whatsapp_number', true);
$gender 	= get_user_meta( $current_user->ID, 'gender', true);
$profile_images = get_user_meta( $current_user->ID, 'profile_image', true);
$images = !empty( $profile_images['image_data'] ) ? $profile_images['image_data'] : array();
$address  = get_user_meta($current_user->ID, 'address', true );
$author_fb = get_user_meta($current_user->ID, 'author_fb', true );
$author_tw = get_user_meta($current_user->ID, 'author_tw', true );
$author_linkedin = get_user_meta($current_user->ID, 'author_linkedin', true );
$author_pinterest = get_user_meta($current_user->ID, 'author_pinterest', true );
$author_behance = get_user_meta($current_user->ID, 'author_behance', true );
$author_dribbble = get_user_meta($current_user->ID, 'author_dribbble', true );
$author_instagram = get_user_meta($current_user->ID, 'author_instagram', true );
$author_ytube = get_user_meta($current_user->ID, 'author_ytube', true );
$author_vimeo = get_user_meta($current_user->ID, 'author_vimeo', true );
$author_flickr = get_user_meta($current_user->ID, 'author_flickr', true );
$user_ID = $current_user->ID;
$uev_status = get_user_meta($current_user->ID, 'email_verification_status', true );
if($uev_status == 'verified'){
	$uev_status_string = esc_html__('Email Verification', 'directorypress-frontend');
	$uev_status_link = '<i class="fas fa-check"></i>';
}else{
	$uev_status_string = esc_html__('Email Verification', 'directorypress-frontend');
	$uev_status_link = '<a href="#" data-toggle="modal" data-target="#user-email-verification-modal">'.esc_html__('verify', 'directorypress-frontend').'</a>';
}
$umv_status = get_user_meta($current_user->ID, 'phone_verification_status', true );
if($umv_status == 'verified'){
	$umv_status_string = esc_html__('Phone Verification', 'directorypress-frontend');
	$umv_status_link = '<i class="fas fa-check"></i>';
}else{
	$umv_status_string = esc_html__('Phone Verification', 'directorypress-frontend');
	$umv_status_link = '<a href="#" data-toggle="modal" data-target="#user-phone-verification-modal">'.esc_html__('verify', 'directorypress-frontend').'</a>';
}
?>
<div class="row clearfix">
	<div class="col-md-4 col-sm-6 col-xs-12">
		<div class="profile-img clearfix">
			<div class="ajax-response"></div>
					<div class="profile-img-inner clearfix">		
					<?php
						$avatar_id = get_user_meta( $user_ID, 'avatar_id', true );
						
						if(!empty($avatar_id) && is_numeric($avatar_id)) {
							$author_avatar_url = wp_get_attachment_image_src( $avatar_id, 'full' ); 
							$src = $author_avatar_url[0];
							$params = array( 'width' => 270, 'height' => 270, 'crop' => true );

							echo "<img class='pacz-user-avatar' src='" . bfi_thumb($src, $params ) . "' alt='author' />";
							//echo $avatar_id;
						} else { 
							$avatar_url = get_avatar_url($user_ID, ['size' => '270']);
							?>
							<img class="pacz-user-avatar" src="<?php echo $avatar_url; ?>" alt="" />
							
					<?php } ?>
						<form class="dpfl-user-profile-photo">
								<label class="panel-btn1 choose-author-image" for="avatar"><i class="fas fa-camera"></i></label>
								<input class="avatar" type="file" name="avatar" id="avatar" value="" style="visibility:hidden;height:0;">
							<?php //wp_nonce_field('dpfl_profilePhotoRequest', 'dpfl_profilePhotoRequest'); ?>
						</form>
						<a class="remove-author-image" href="#"><i class="fas fa-trash-alt"></i></a>
					</div>
		</div>
		<div class="dpfl-user-verification">
			<?php do_action('dpfl_user_verification_html'); ?>
		</div>
		<?php do_action('directorypress_frontend_profile_after_user_verification', $current_user); ?>
		<div class="dpfl-userpass">
			<div class="password-form-header">
				<h6><?php esc_html_e('Change Password', 'directorypress-frontend'); ?></h6>
			</div>
			<form class="dpfl-change-password">
					<div class="ajax-response"></div>
					<div class="form-group">
						<input type="password" name="old-password" class="form-control" placeholder="<?php esc_html_e('Current password', 'directorypress-frontend'); ?>" value="">
					</div>
					<div class="form-group">
						<input type="password" name="new-password" class="form-control" placeholder="<?php esc_html_e('New password', 'directorypress-frontend'); ?>">
					</div>
					<div class="form-group">
						<input type="password" name="confirm-password" class="form-control" placeholder="<?php esc_html_e('Confirm new password', 'directorypress-frontend'); ?>">
					</div>
					<div class="form-group btn-wrapper">
						<a href="javascript:void;" class="btn btn-primary dpfl-change-password btn-block"><?php esc_html_e('Change Password', 'directorypress-frontend'); ?></a>
					</div>
					<?php wp_nonce_field('change_user_password_request', 'change_user_password_request'); ?>
			</form>
		</div>
		<?php do_action('directorypress_frontend_profile_before_user_close_account', $current_user); ?>
		<div class="dpfl-user-close-account">
			<div class="user-close-account-form-header">
				<h6><?php esc_html_e('Close Account', 'directorypress-frontend'); ?></h6>
			</div>
			<p><?php echo esc_html__('Be careful this action can not be reversed, All data would be deleted permanently.', 'directorypress-frontend') ?></p>
			<div class="form-group btn-wrapper">
				<a href="#" data-toggle="modal" data-target="#user-close-account-modal" class="btn btn-danger dpfl-close-account btn-block"><?php esc_html_e('Close Your Account', 'directorypress-frontend'); ?></a>
			</div>
		</div>
	</div>
	<div class="col-md-8 col-sm-6 col-xs-12">
		<form class="dpfl-user-profile">
			<div class="row form-group clearfix">
				<div class="col-md-12">
					<label for="user_login"><?php _e('Username', 'directorypress-frontend'); ?></label>
					<input type="text" name="user_login" class="form-control" value="<?php echo esc_attr($username); ?>" disabled="disabled" />
				</div>
				<div class="col-md-12 pt-15">
					<label for="first_name"><?php _e('First Name', 'directorypress-frontend') ?></label>
					<input type="text" name="first_name" class="form-control" value="<?php echo esc_attr($first_name); ?>" />
				</div>
				<div class="col-md-12 pt-15">
					<label for="last_name"><?php _e('Last Name', 'directorypress-frontend') ?></label>
					<input type="text" name="last_name" class="form-control" value="<?php echo esc_attr($last_name); ?>" />
				</div>
				<div class="col-md-12 pt-15">
					<label for="nickname"><?php _e('Nickname', 'directorypress-frontend') ?> <span class="description"><?php _e('(required)', 'directorypress-frontend'); ?></span></label>
					<input type="text" name="nickname" class="form-control" value="<?php echo esc_attr($nickname); ?>" />
				</div>
				<div class="col-md-12 pt-15">
					<label for="display_name"><?php _e('Display to Public as', 'directorypress-frontend') ?></label>
					<select name="display_name" class="form-control pacz-select2">
						<?php
							$public_display = array();
							$public_display['display_username']  = $username;
							$public_display['display_nickname']  = $nickname;
							if (!empty($first_name)){
								$public_display['display_firstname'] = $first_name;
							}
							if (!empty($last_name)){
								$public_display['display_lastname'] = $last_name;
							}
							if (!empty($first_name) && !empty($first_name)) {
								$public_display['display_firstlast'] = $first_name . ' ' . $last_name;
								$public_display['display_lastfirst'] = $last_name . ' ' . $first_name;
							}
							if (!in_array($display_name, $public_display)){ // Only add this if it isn't duplicated elsewhere
								$public_display = array('display_displayname' => $display_name) + $public_display;
							}
							$public_display = array_map('trim', $public_display);
							$public_display = array_unique($public_display);
							foreach ($public_display as $id => $item) { ?>
									<option id="<?php echo $id; ?>" value="<?php echo esc_attr($item); ?>"<?php selected($display_name, $item); ?>><?php echo $item; ?></option>
						<?php } ?>
					</select>
				</div>
				<div class="col-md-12 pt-15">
					<label for="email"><?php _e('E-mail', 'directorypress-frontend'); ?> <span class="description"><?php _e('(required)', 'directorypress-frontend'); ?></span></label>
					<input type="text" name="email" class="form-control" value="<?php echo esc_attr($email); ?>" />
				</div>
				<?php if ($DIRECTORYPRESS_ADIMN_SETTINGS['frontend_panel_user_website']){ ?>
					<div class="col-md-12 pt-15">
						<label for="user_url"><?php _e('Website', 'directorypress-frontend'); ?></label>
						<input type="text" name="user_url" class="form-control" value="<?php echo esc_attr($website); ?>" />
					</div>
				<?php } ?>
				<?php if ($DIRECTORYPRESS_ADIMN_SETTINGS['frontend_panel_user_phone']){ ?>
					<div class="col-md-12 pt-15 pt-15">
						<label for="user_phone"><?php _e('Phone Number', 'directorypress-frontend'); ?></label>
						<input type="text" name="user_phone" class="form-control" value="<?php echo esc_attr($user_phone); ?>" />
					</div>
				<?php } ?>
				<?php if (isset($DIRECTORYPRESS_ADIMN_SETTINGS['frontend_panel_user_whatsapp_number']) && $DIRECTORYPRESS_ADIMN_SETTINGS['frontend_panel_user_whatsapp_number']){ ?>
					<div class="col-md-12 pt-15 pt-15">
						<label for="user_phone"><?php _e('Whatsapp Number', 'directorypress-frontend'); ?></label>
						<input type="text" name="user_whatsapp_number" class="form-control" value="<?php echo esc_attr($user_whatsapp_number); ?>" />
					</div>
				<?php } ?>
				<?php do_action('directorypress_frontend_profile_fields'); ?>
				<?php if ($DIRECTORYPRESS_ADIMN_SETTINGS['frontend_panel_social_links']){ ?>
					<div class="col-md-12 pt-15">
						<label for="email"><?php _e('Facebook', 'directorypress-frontend'); ?></label>
						<input type="text" name="author_fb" class="form-control" value="<?php echo esc_attr($author_fb); ?>" />
					</div>
					<div class="col-md-12 pt-15">
						<label for="tw"><?php _e('Twitter', 'directorypress-frontend'); ?></label>
						<input type="text" name="author_tw" class="form-control" value="<?php echo esc_attr($author_tw); ?>" />
					</div>
					<div class="col-md-12 pt-15">
						<label for="email"><?php _e('Linkedin', 'directorypress-frontend'); ?></label>
						<input type="text" name="author_linkedin" class="form-control" value="<?php echo esc_attr($author_linkedin); ?>" />
					</div>
					<div class="col-md-12 pt-15">
						<label for="email"><?php _e('Pinterest', 'directorypress-frontend'); ?></label>
						<input type="text" name="author_pinterest" class="form-control" value="<?php echo esc_attr($author_pinterest); ?>" />
					</div>
					<div class="col-md-12 pt-15">
						<label for="email"><?php _e('Behance', 'directorypress-frontend'); ?></label>
						<input type="text" name="author_behance" class="form-control" value="<?php echo esc_attr($author_behance); ?>" />
					</div>
					<div class="col-md-12 pt-15">
						<label for="email"><?php _e('Dribbble', 'directorypress-frontend'); ?></label>
						<input type="text" name="author_dribbble" class="form-control" value="<?php echo esc_attr($author_dribbble); ?>" />
					</div>
					<div class="col-md-12 pt-15">
						<label for="email"><?php _e('Instagram', 'directorypress-frontend'); ?></label>
						<input type="text" name="author_instagram" class="form-control" value="<?php echo esc_attr($author_instagram); ?>" />
					</div>
					<div class="col-md-12 pt-15">
						<label for="email"><?php _e('YouTube', 'directorypress-frontend'); ?></label>
						<input type="text" name="author_ytube" class="form-control" value="<?php echo esc_attr($author_ytube); ?>" />
					</div>
					<div class="col-md-12 pt-15">
						<label for="email"><?php _e('Vimeo', 'directorypress-frontend'); ?></label>
						<input type="text" name="author_vimeo" class="form-control" value="<?php echo esc_attr($author_vimeo); ?>" />
					</div>
					<div class="col-md-12 pt-15">
						<label for="email"><?php _e('Flickr', 'directorypress-frontend'); ?></label>
						<input type="text" name="author_flickr" class="form-control" value="<?php echo esc_attr($author_flickr); ?>" />
					</div>
				<?php } ?>		
				<div class="col-md-12 pt-15 btn-wrapper">
					<a href="#" class="btn btn-primary dpfl_profile_update_action"><?php esc_html_e('Update', 'directorypress-frontend'); ?></a>
				</div>
				<?php wp_nonce_field('dpfl_profile_update_request', 'dpfl_profile_update_request'); ?>
			</div>
			<div class="ajax-response"></div>
		</form>
	</div>
</div>