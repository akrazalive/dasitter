<?php 
	global $DIRECTORYPRESS_ADIMN_SETTINGS;
	if(is_user_logged_in()){
		if ($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_after_claim'] == 'expired'){
			echo '<div class="alert alert-warning">';
				echo esc_html__('After approval listing status would become expired.', 'directorypress-claim-listing');
			echo '</div>';
		}
		do_action('directorypress_claim_html', $listing);
		echo '<form class="listing-claim-form" method="post">';
			echo '<div class="form-group claim-form">';
				echo '<div><label>'. esc_html__('Additional Note', 'directorypress-claim-listing').'</label></div>';
				echo '<textarea name="claim_message" class="form-control" rows="15"></textarea>';
				echo '<input type="hidden" name="listing_id" value="'.$listing->post->ID.'" />';
			echo '</div>';
			echo '<a class="claim-action-button btn btn-primary" data-listing-id="'.$listing->post->ID .'" href="#">'. esc_html__('Send Claim', 'directorypress-claim-listing') .'</a>';
		echo '</form>';
	}else{
		directorypress_login_form();
	}