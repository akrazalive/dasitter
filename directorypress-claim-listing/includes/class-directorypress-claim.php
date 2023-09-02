<?php

/**
 * @since      1.0.0
 * @package    Directorypress_Claim_Listing
 * @subpackage Directorypress_Claim_Listing/includes
 * @author     DirectoryPress <team@directorypress.co>
 */


class directorypress_listing_claim {
	public $listing_id;
	public $claimer_id;
	public $claimer;
	public $claimer_message;
	public $status = null;
	
	public function __construct($listing_id) {
		$this->listing_id = $listing_id;
		if ($claim_record = get_post_meta($listing_id, '_claim_data', true)) {
			if (isset($claim_record['claimer_id'])) {
				$this->claimer_id = $claim_record['claimer_id'];
				if ($claimer = get_userdata($claim_record['claimer_id']))
					$this->claimer = $claimer;
			}
			if (isset($claim_record['claimer_message']))
				$this->claimer_message = $claim_record['claimer_message'];
			if (isset($claim_record['status']))
				$this->status = $claim_record['status'];
			else 
				$this->status = 'pending';
		}
	}
	
	public function updateRecord($claimer_id = null, $claimer_message = null, $status = null) {
		if ($claimer_id !== null) {
			$this->claimer_id = $claimer_id;
			update_post_meta($this->listing_id, '_claimer_id', $this->claimer_id);
			if ($claimer = get_userdata($claimer_id))
				$this->claimer = $claimer;
		}
		if ($claimer_message !== null)
			$this->claimer_message = $claimer_message;
		if ($status !== null)
			$this->status = $status;
		return update_post_meta($this->listing_id, '_claim_data', array('claimer_id' => $this->claimer_id, 'claimer_message' => $this->claimer_message, 'status' => $this->status));
	}
	
	public function deleteRecord() {
		delete_post_meta($this->listing_id, '_claimer_id');
		return delete_post_meta($this->listing_id, '_claim_data');
	}
	
	public function isClaimed() {
		return (bool) ($this->status == 'pending');
	}

	public function getClaimMessage() {
		if ($this->claimer_id == get_current_user_id()) {
			if ($this->status == 'approved')
				return __('Your claim was approved successully', 'directorypress-claim-listing');
			else
				return __('Your claim was not approved yet', 'directorypress-claim-listing');
		} else {
			if ($this->status != 'approved')
				return sprintf(__('You may approve or decline claim <a href="%s">here</a>', 'directorypress-claim-listing'), directorypress_dashboardUrl(array('listing_id' => $this->listing_id, 'directorypress_action' => 'process_claim')));
		}
	}
	
	public function approve() {
		global $DIRECTORYPRESS_ADIMN_SETTINGS;
		$postarr = array(
				'ID' => $this->listing_id,
				'post_author' => $this->claimer_id
		);
		$result = wp_update_post($postarr, true);
		if (!is_wp_error($result)) {
			if ($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_after_claim'] == 'expired') {
				update_post_meta($this->listing_id, '_listing_status', 'expired');
				wp_update_post(array('ID' => $this->listing_id, 'post_status' => 'draft'));
			}
			update_post_meta($this->listing_id, '_is_claimable', false);

			return $this->updateRecord(null, null, 'approved');
		}
	}
	
}