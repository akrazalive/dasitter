<?php

/**
 * @package    Directorypress_Claim_Listing
 * @subpackage Directorypress_Claim_Listing/admin
 * @author     DirectoryPress <team@directorypress.co>
 */
class Directorypress_Claim_Listing_Admin_Handler {

	private $plugin_name;
	private $version;

	public function __construct() {
		add_filter('manage_'.DIRECTORYPRESS_POST_TYPE.'_posts_columns', array($this, 'add_listings_table_columns'));
		add_filter('manage_'.DIRECTORYPRESS_POST_TYPE.'_posts_custom_column', array($this, 'manage_listings_table_rows'), 10, 2);
		add_action('directorypress_after_general_settings', array($this, 'settings'), 10, 2);
		add_action('add_meta_boxes', array($this, 'addClaimingMetabox'));

	}
	
	public function addClaimingMetabox($post_type) {
		//if ($post_type == DIRECTORYPRESS_POST_TYPE) {
			add_meta_box('directorypress_listing_claim',
					__('Listing claim', 'directorypress-claim-listing'),
					array($this, 'listingClaimMetabox'),
					DIRECTORYPRESS_POST_TYPE,
					'normal',
					'high');
		//}
	}
	
	public function listingClaimMetabox($post) {
		$listing = directorypress_pull_current_listing_admin();
		directorypress_claim_display_template('metabox.php', array('listing' => $listing));
	}

	public function enqueue_styles() {
		
	}

	public function enqueue_scripts() {
		
	}
	
	public function add_listings_table_columns($columns) {
		global $directorypress_object, $DIRECTORYPRESS_ADIMN_SETTINGS;
		
		if ($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_claim_functionality']){
			$columns['directorypress_claim'] = __('Claim', 'directorypress-claim-listing');
		}
		return $columns;
	}
	
	public function manage_listings_table_rows($column, $post_id) {
		global $DIRECTORYPRESS_ADIMN_SETTINGS, $directorypress_object;
		
		switch ($column) {
			case "directorypress_claim": // property of claim addon
				if ($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_claim_functionality']) {
					$listing = new directorypress_listing();
					$listing->directorypress_init_lpost_listing($post_id);
	
					if ($listing->claim->isClaimed()){
						echo $listing->claim->getClaimMessage();
					}elseif ($listing->is_claimable){
						_e('Claimable', 'directorypress-claim-listing');
					}
				}
			break;
		}
	}
	
	public function settings($redux, $opt_name) {
		if ( ! class_exists( 'Redux' ) ) {
			return;
		}
		$redux::setSection( $opt_name, array(
			'title' => __( 'DirectoryPress Claim Addon', 'directorypress-claim-listing' ),
			'id' => 'directorypress_claim_addon_settings',
			'icon'  => 'fas fa-tachometer-alt',
		));
		$redux::setSection( $opt_name, array(
			'title' => __( 'General', 'directorypress-claim-listing' ),
			'subsection' => true,
			'id' => 'claim_settings',
			'fields' => array(
				array(
						'type' => 'switch',
						'id' => 'directorypress_claim_functionality',
						'title' => __('Enable claim functionality', 'directorypress-claim-listing'),
						'default' => false,
					),
					array(
						'type' => 'switch',
						'id' => 'directorypress_claim_approval',
						'title' => __('Approval of claim required', 'directorypress-claim-listing'),
						'desc' => __('In other case claim will be processed immediately without any notifications', 'directorypress-claim-listing'),
						'default' => false,
					),
					array(
						'type' => 'radio',
						'id' => 'directorypress_after_claim',
						'title' => __('What will be with listing status after successful approval?', 'directorypress-claim-listing'),
						'desc' => __('When set to expired - renewal may be payment option', 'directorypress-claim-listing'),
						'options' => array(
							'active' =>__('just approval', 'directorypress-claim-listing'),
							'expired' =>__('expired status', 'directorypress-claim-listing'),
						),
						'default' => 'active',
					)
			)
		));
	}

}
