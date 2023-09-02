<?php

/**
 * @since      1.0.0
 * @package    Directorypress_Claim_Listing
 * @subpackage Directorypress_Claim_Listing/includes
 * @author     DirectoryPress <team@directorypress.co>
 */


class directorypress_listing_claim_extended extends directorypress_listing {
	
	public $is_claimable; // property of claim addon
	public $claim; // property of claim addon
	
	public function __construct() {
		add_filter('directorypress_listing_initializing', array($this, 'setclaim'), 1);
	}
	
	public function setclaim($post) {
		$this->setClaiming($post);
	}
	
	public function setClaiming($listing) {
		$listing->is_claimable = get_post_meta($listing->post->ID, '_is_claimable', true);
		$listing->is_claimable = apply_filters('directorypress_listing_is_claimable', $listing->is_claimable, $listing);
		
		$listing->claim = new directorypress_listing_claim($listing->post->ID);
		$listing->claim = apply_filters('directorypress_listing_claim', $listing->claim, $listing);
	}
	
}