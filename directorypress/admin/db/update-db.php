<?php
if(!get_option('directorypress_db_update_3_3_4') || get_option('directorypress_db_update_3_3_4') != 'updated'){
	directorypress_db_update_3_3_4();
}
function directorypress_db_update_3_3_4(){
	global $wpdb;
	$prefix = $wpdb->prefix;
	if($wpdb->get_var("SELECT id FROM `".$prefix."directorypress_fields` WHERE slug = 'categories_list' AND is_configuration_page = '0'")){
		$wpdb->query("UPDATE `".$prefix."directorypress_fields` SET is_configuration_page ='1' WHERE slug ='categories_list'");
	}
	update_option('directorypress_db_update_3_3_4', 'updated');
}
// version 3.4.0
if(!get_option('directorypress_db_update_3_4_0') || get_option('directorypress_db_update_3_4_0') != 'updated'){
	directorypress_db_update_3_4_0();
}
function directorypress_db_update_3_4_0(){
	global $DIRECTORYPRESS_ADIMN_SETTINGS, $wpdb;
	$prefix = $wpdb->prefix;

	if (!$wpdb->get_var("SELECT id FROM `".$prefix."directorypress_fields` WHERE slug = 'status'")){
		$wpdb->query("INSERT INTO `".$prefix."directorypress_fields` (`is_core_field`, `order_num`, `name`, `slug`, `description`, `fieldwidth`, `fieldwidth_archive`, `type`, `icon_image`, `is_required`, `is_configuration_page`, `is_search_configuration_page`, `is_ordered`, `is_hide_name`, `is_hide_name_on_grid`, `is_hide_name_on_list`, `is_hide_name_on_search`, `is_field_in_line`, `on_exerpt_page`, `on_exerpt_page_list`, `on_listing_page`, `on_search_form`, `on_map`, `advanced_search_form`, `categories`, `options`, `checkbox_icon_type`, `search_options`, `group_id`) VALUES(1, 6, 'Status', 'status', '', '', '', 'status', '', 0, 1, 0, 0, 0, 'hide', 'hide', 0, 0, 0, 0, 0, 0, 0, 0, '', 'a:2:{s:15:\"selection_items\";a:3:{i:1;s:8:\"For Sale\";i:2;s:8:\"For Rent\";i:3;s:6:\"Wanted\";}s:11:\"color_codes\";a:3:{i:1;s:7:\"#81d742\";i:2;s:7:\"#1e73be\";i:3;s:7:\"#dd9933\";}}', '', '', 0);");
	}
	if(isset($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_single_listing_style']) && ($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_single_listing_style'] == 1 || empty($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_single_listing_style']))){
		Redux::set_option( 'directorypress_admin_settings', 'directorypress_single_listing_style', 'default' );
	}
	update_option('directorypress_db_update_3_4_0', 'updated');
}