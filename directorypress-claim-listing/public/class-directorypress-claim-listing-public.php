<?php

/**
 * @package    Directorypress_Claim_Listing
 * @subpackage Directorypress_Claim_Listing/public
 * @author     DirectoryPress <team@directorypress.co>
 */
class Directorypress_Claim_Listing_Public {

	private $plugin_name;
	private $version;

	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		
		// property of claim addon
		add_action('directorypress_claim_button', array($this, 'add_claim_button'), 10, 3);
		add_action('directorypress_listing_buttons_list_pre', array($this, 'add_single_listing_claim_button'), 10, 3);
		
		add_action('directorypress-dashboard-listing-after-expiry-html', array($this, 'dashboard_listing_claim_html'), 10, 1);
		
		add_action('directorypress_frontend_submit_listing_after', array($this, 'listing_claim_status'), 10, 2);
		add_action('directorypress_frontend_edit_listing_after', array($this, 'listing_claim_status'), 10, 2);
		
		add_action('frontend_listing_details_before_category_metabox', array($this, 'listing_claim_frontend_metabox'), 10, 1);
	}
	
	public function add_claim_button($id, $button_text = false, $button_style = 1) {
		global $directorypress_object, $DIRECTORYPRESS_ADIMN_SETTINGS;
		$text_string = ($button_text)? esc_html__('Claim', 'directorypress-claim-listing'): '';
		$tooltip = (!$button_text)? 'data-toggle="tooltip" title="'.esc_attr__('Claim Listing', 'directorypress-claim-listing').'"':'';
		if ($listing = directorypress_get_listing($id)) {	
			if ($listing && $listing->is_claimable && $directorypress_object->dashboard_page_url && $DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_claim_functionality'] && $listing->post->post_author != get_current_user_id()){
				$href = directorypress_dashboardUrl(array('listing_id' => $listing->post->ID, 'directorypress_action' => 'claim_listing'));
				//echo '<a class="directorypress-claim-listing-link" data-toggle="tooltip" title="'.esc_attr__('Claim This Listing', 'directorypress-claim-listing').'" href="' . $href . '" rel="nofollow"><i class="fas fa-exclamation"></i></a>';
				echo '<a class="button-style-'. $button_style .'" href="#" data-popup-open="single_claim_form" '. $tooltip .'><i class="fas fa-exclamation"></i>'. $text_string .'</a>';
				
				echo '<div class="directorypress-custom-popup" data-popup="single_claim_form">';
					echo '<div class="directorypress-custom-popup-inner single-claim">';
						echo '<div class="directorypress-popup-title">'.esc_html__('Claim This Listing', 'directorypress-claim-listing').'<a class="directorypress-custom-popup-close" data-popup-close="single_claim_form" href="#"><i class="far fa-times-circle"></i></a></div>';
						echo '<div class="directorypress-popup-content">';
							directorypress_claim_display_template('claim.php', array('listing' => $listing));
						echo'</div>';
					echo'</div>';
				echo'</div>';
			}	
		}
	}
	public function add_single_listing_claim_button($id, $button_text = false, $button_style = 1) {
		global $DIRECTORYPRESS_ADIMN_SETTINGS;
		$listing = directorypress_get_listing($id);
		
		if ( $listing->is_claimable && $DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_claim_functionality'] && $listing->post->post_author != get_current_user_id()){
			echo '<li>'. do_action('directorypress_claim_button', $listing->post->ID, true, 2) .'</li>';
		}

	}
	
	public function dashboard_listing_claim_html($listing) {
		global $DIRECTORYPRESS_ADIMN_SETTINGS;
		//$listing = directorypress_get_listing($id);
		
		if($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_claim_functionality']){
			echo '<div class="dashboard-listings-claim">';
				echo '<div class="listing-claim-label">'. esc_html__('Claim', 'directorypress-claim-listing') .'</div>';
				echo '<div class="listing-claim-action">';
					if ($listing->claim && $listing->claim->isClaimed()){
						echo '<a href="#" class="listing_setting_action_link_claim" data-modal-button-text="'. esc_attr__('Respond', 'directorypress-claim-listing') .'" data-modal-title="'. esc_attr__('Respond To Claim', 'directorypress-claim-listing') .'" data-modal-class="listing_claim_modal" data-listing-id="'. $listing->post->ID .'" data-toggle="modal" data-target="#listing_action_modal">'. esc_html__('Respond', 'directorypress-claim-listing') .'</a>';
					}else{
						echo '<span>'. esc_html__('N/A', 'directorypress-claim-listing') .'</span>';
					}
				echo '</div>';
			echo '</div>';
		}

	}
	
	public function listing_claim_status($title = '', $id) {
		global $DIRECTORYPRESS_ADIMN_SETTINGS;
		//$listing = directorypress_get_listing($id);
		if ($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_claim_functionality']) {
			if (isset($_POST['is_claimable'])) {
				update_post_meta($id, '_is_claimable', true);
			} else {
				update_post_meta($id, '_is_claimable', false);
			}
		}

	}
	
	public function listing_claim_frontend_metabox($listing) {
		global $DIRECTORYPRESS_ADIMN_SETTINGS;
		$admin_class = new Directorypress_Claim_Listing_Admin();
		if ($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_claim_functionality']){
			echo '<div class="field-wrap">';
				echo '<p class="directorypress-submit-section-label directorypress-submit-field-title">';
					_e('Is Claimable? ', 'directorypress-claim-listing');
					do_action('directorypress_listing_submit_user_info', esc_attr__('By checking this option you allow registered users to claim this listing.', 'directorypress-claim-listing'));
					do_action('directorypress_listing_submit_admin_info', 'listing_claim');
				echo '</p>';
				$admin_class->listingClaimMetabox($listing);
			echo '</div>';
		}

	}
	
	public function add_claimed_listings_where($where = '') {
		global $wpdb;
		
		$claimed_posts = '';
		$claimed_posts_ids = array();

		$results = $wpdb->get_results("SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key='_claimer_id' AND meta_value='" . get_current_user_id() . "'", ARRAY_A);
		foreach ($results AS $row)
			$claimed_posts_ids[] = $row['post_id'];
		if ($claimed_posts_ids)
			$claimed_posts = " OR {$wpdb->posts}.ID IN (".implode(',', $claimed_posts_ids).") ";
		$where .= " AND ({$wpdb->posts}.post_author IN (".get_current_user_id().")" . $claimed_posts . ")";
		
		return $where;
	}
	
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, DIRECTORYPRESS_CLAIM_LISTING_ASSETS_URL . 'css/style.css', array(), $this->version, 'all' );

	}

	public function enqueue_scripts() {
		
		wp_enqueue_script( $this->plugin_name, DIRECTORYPRESS_CLAIM_LISTING_ASSETS_URL . 'js/scripts.js', array( 'jquery' ), $this->version, false );
		wp_localize_script($this->plugin_name, 'dpcl_custom_vars', array(
			'ajaxurl' => admin_url('admin-ajax.php'),
		));
	}

}
