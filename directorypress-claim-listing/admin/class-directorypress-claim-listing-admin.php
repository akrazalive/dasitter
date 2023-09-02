<?php

/**
 * @package    Directorypress_Claim_Listing
 * @subpackage Directorypress_Claim_Listing/admin
 * @author     DirectoryPress <team@directorypress.co>
 */
class Directorypress_Claim_Listing_Admin {

	private $plugin_name;
	private $version;

	public function __construct() {
		global $directorypress_object;
		add_filter('manage_'.DIRECTORYPRESS_POST_TYPE.'_posts_columns', array($this, 'add_listings_table_columns'));
		add_filter('manage_'.DIRECTORYPRESS_POST_TYPE.'_posts_custom_column', array($this, 'manage_listings_table_rows'), 10, 2);
		//$directorypress_object->claim_listing_admin_table = new Directorypress_Claim_Listing_Admin_Handler;
		
		add_action('directorypress_after_general_settings', array($this, 'settings'), 10, 2);
		add_action('add_meta_boxes', array($this, 'addClaimingMetabox'));
		add_action('directorypress_listing_update', array($this, 'listing_claim_status'), 10, 1);
		
		add_action('directorypress_after_post_filter_dropdown', array($this, 'claim_listing_filter_dropdown'));
		add_filter('directorypress_after_post_filters', array($this, 'claim_listing_filter'), 10, 1);
		
		add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'), 0);
		
	}
	// property of claim addon
		
	public function claim_listing_filter_dropdown() {
		global $DIRECTORYPRESS_ADIMN_SETTINGS;
		
		if ($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_claim_functionality']) {
			echo '<select name="directorypress_claim_filter">';
			echo '<option value="">' . __('Any listings claim', 'directorypress-claim-listing') . '</option>';
			echo '<option ' . selected(directorypress_get_input_value($_GET, 'directorypress_claim_filter'), 'claimable', false ) . 'value="claimable">' . __('Only claimable', 'directorypress-claim-listing') . '</option>';
			echo '<option ' . selected(directorypress_get_input_value($_GET, 'directorypress_claim_filter'), 'claimed', false ) . 'value="claimed">' . __('Awaiting approval', 'directorypress-claim-listing') . '</option>';
			echo '</select>';
		}
	}
	public function claim_listing_filter($vars) {
		global $DIRECTORYPRESS_ADIMN_SETTINGS;
		// property of claim addon
		if (isset($_GET['directorypress_claim_filter']) && $_GET['directorypress_claim_filter'] && $DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_claim_functionality']) {
			if ($_GET['directorypress_claim_filter'] == 'claimable') {
				$vars = array_merge(
						$vars,
						array(
								'meta_query' => array(
										'relation' => 'AND',
										array(
												'key'     => '_is_claimable',
												'value'   => 1,
												'type'    => 'numeric',
										)
								)
						)
				);
			} elseif ($_GET['directorypress_claim_filter'] == 'claimed') {
				$vars = array_merge(
						$vars,
						array(
								'meta_query' => array(
										'relation' => 'AND',
										array(
												'key'     => '_claimer_id',
												'compare' => 'EXISTS',
										)
								)
						)
				);
				$vars = array_merge(
						$vars,
						array(
								'meta_query' => array(
										'relation' => 'AND',
										array(
												'key'     => '_claim_data',
												'value'   => 'approved',
												'compare' => 'NOT LIKE',
										)
								)
						)
				);
			}
		}
		return $vars;
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
		if(is_admin()){
			do_action('directorypress_listing_submit_admin_info', 'listing_claim');
		}
		directorypress_claim_display_template('metabox.php', array('listing' => $listing));
		
	}
	
	public function listing_claim_status($listing) {
		global $DIRECTORYPRESS_ADIMN_SETTINGS;
		$id = $listing->post->ID;
		if ($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_claim_functionality']) {
			if (isset($_POST['is_claimable'])) {
				update_post_meta($id, '_is_claimable', true);
			} else {
				update_post_meta($id, '_is_claimable', false);
			}
		}

	}

	public function enqueue_styles() {
		
	}

	public function enqueue_scripts() {
		wp_enqueue_script( 'admin_'. $this->plugin_name, DIRECTORYPRESS_CLAIM_LISTING_ASSETS_URL . 'js/admin_script.js', array( 'jquery' ), $this->version, false );
		wp_localize_script('admin_'. $this->plugin_name, 'dpcl_custom_vars', array(
			'ajaxurl' => admin_url('admin-ajax.php'),
		));
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
						echo '<a class="admin-listing-claim-process-link" href="#" data-toggle="modal" data-target="#listing_admin_configure" data-listing-id="'.$post_id.'">'. esc_html__('Process Claim', 'directorypress-claim-listing') .'</a>';
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
					),
					array(
						'type' => 'switch',
						'id' => 'directorypress_hide_claim_contact_form',
						'title' => __('Hide contact form on claimable listings', 'directorypress-claim-listing'),
						'default' => false,
					),
			)
		));
	}

}
