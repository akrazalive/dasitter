<?php

/**
 * @package    Directorypress_Advanced_Fields
 * @subpackage Directorypress_Advanced_Fields/admin
 * @author     DirectoryPress <team@directorypress.co>
 */
class Directorypress_Advanced_Fields_Admin {

	
	private $plugin_name;
	private $version;

	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		global $pagenow;

		if (($pagenow == 'post-new.php' && ($post_type = $_GET['post_type']) && (in_array($post_type, array('dp_listing')))) || ($pagenow == 'post.php' && ($post_id = $_GET['post']) && ($post = get_post($post_id)) && (in_array($post->post_type, array('dp_listing'))))){
			add_action('add_meta_boxes', array($this, 'add_fields_metabox'));
		}
		
	}
	
	public function add_fields_metabox($post_type) {
		if ($post_type == 'dp_listing') {
			global $directorypress_object;
			
			if ($directorypress_object->fields->is_this_field_slug('status')){
				add_meta_box(
					'directorypress_status_field',
					__('Listing Status', 'directorypress-advanced-fields'),
					array($this, 'directorypress_status_field_metabox'),
					'dp_listing',
					'normal',
					'high'
				);
			}
		}
	}
	public function directorypress_status_field_metabox() {
		global $directorypress_object;
		$listing = directorypress_pull_current_listing_admin();
		$directorypress_object->fields_handler_property->directorypress_fields_metabox_by_slug_type('status', 'status', $listing);
		
	}
	public function enqueue_styles() {

	}

	public function enqueue_scripts() {

	}

}
