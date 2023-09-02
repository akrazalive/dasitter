<?php

add_action('vc_before_init', 'dpfl_vc_init');

function dpfl_vc_init() {
		vc_map( array(
			'name'                    => __('Listings submit', 'directorypress-frontend'),
			'description'             => __('Listings submission pages', 'directorypress-frontend'),
			'base'                    => 'directorypress-submit',
			'icon'                    => DIRECTORYPRESS_RESOURCES_URL . 'images/directorypress.png',
			'show_settings_on_create' => false,
			'category'                => __('Listing Content', 'directorypress-frontend'),
			'params'                  => array(
				array(
						'type' => 'packages',
						'param_name' => 'packages',
						'heading' => __('Listings packages', 'directorypress-frontend'),
						'description' => __('Choose exact packages to display', 'directorypress-frontend'),
						'value' => '',
				),
				array(
						'type' => 'directorytype',
						'param_name' => 'directorytype',
						'heading' => __("Specific directorytype", "directorypress-frontend"),
				),
				array(
						'type' => 'dropdown',
						'param_name' => 'columns',
						'value' => array('1' => '1', '2' => '2', '3' => '3', '4' => '4'),
						'std' => '3',
						'heading' => __('Columns', 'directorypress-frontend'),
				),
				array(
						'type' => 'dropdown',
						'param_name' => 'columns_same_height',
						'value' => array(__('No', 'directorypress-frontend') => '0', __('Yes', 'directorypress-frontend') => '1'),
						'heading' => __('Show negative parameters?', 'directorypress-frontend'),
						'description' => __('Show parameters those have negation. For example, such row in the table will be shown: Featured Listings - No. In other case this row will be hidden.', 'directorypress-frontend'),
				),
				array(
						'type' => 'dropdown',
						'param_name' => 'show_period',
						'value' => array(__('Yes', 'directorypress-frontend') => '1', __('No', 'directorypress-frontend') => '0'),
						'heading' => __('Show package active period on choose package page?', 'directorypress-frontend'),
				),
				array(
						'type' => 'dropdown',
						'param_name' => 'show_has_sticky',
						'value' => array(__('Yes', 'directorypress-frontend') => '1', __('No', 'directorypress-frontend') => '0'),
						'heading' => __('Show is package has_sticky on choose package page?', 'directorypress-frontend'),
				),
				array(
						'type' => 'dropdown',
						'param_name' => 'show_has_featured',
						'value' => array(__('Yes', 'directorypress-frontend') => '1', __('No', 'directorypress-frontend') => '0'),
						'heading' => __('Show is package has_featured on choose package page?', 'directorypress-frontend'),
				),
				array(
						'type' => 'dropdown',
						'param_name' => 'show_categories',
						'value' => array(__('Yes', 'directorypress-frontend') => '1', __('No', 'directorypress-frontend') => '0'),
						'heading' => esc_attr__("Show package categories number on choose package page?", 'directorypress-frontend'),
				),
				array(
						'type' => 'dropdown',
						'param_name' => 'show_locations',
						'value' => array(__('Yes', 'directorypress-frontend') => '1', __('No', 'directorypress-frontend') => '0'),
						'heading' => esc_attr__("Show package locations number on choose package page?", 'directorypress-frontend'),
				),
				array(
						'type' => 'dropdown',
						'param_name' => 'show_images',
						'value' => array(__('Yes', 'directorypress-frontend') => '1', __('No', 'directorypress-frontend') => '0'),
						'heading' => esc_attr__("Show package images number on choose package page?", 'directorypress-frontend'),
				),
				array(
						'type' => 'dropdown',
						'param_name' => 'show_videos',
						'value' => array(__('Yes', 'directorypress-frontend') => '1', __('No', 'directorypress-frontend') => '0'),
						'heading' => esc_attr__("Show package videos number on choose package page?", 'directorypress-frontend'),
				),
				array(
						'type' => 'checkbox',
						'param_name' => 'visibility',
						'heading' => __("Show only on directorytype pages", "directorypress-frontend"),
						'value' => 1,
						'description' => __("Otherwise it will load plugin's files on all pages.", "directorypress-frontend"),
				),
			),
		));
		vc_map( array(
			'name'                    => __('Pricing table', 'directorypress-frontend'),
			'description'             => __('Listings packages table. Works in the same way as 1st step on Listings submit, displays only pricing table. Note, that page with Listings submit element required.', 'directorypress-frontend'),
			'base'                    => 'directorypress-packages-table',
			'icon'                    => DIRECTORYPRESS_RESOURCES_URL . 'images/directorypress.png',
			'show_settings_on_create' => false,
			'category'                => __('Listing Content', 'directorypress-frontend'),
			'params'                  => array(
				array(
						'type' => 'packages',
						'param_name' => 'packages',
						'heading' => __('Listings packages', 'directorypress-frontend'),
						'description' => __('Choose exact packages to display', 'directorypress-frontend'),
						'value' => '',
				),
				array(
						'type' => 'directorytype',
						'param_name' => 'directorytype',
						'heading' => __("Specific directorytype", "directorypress-frontend"),
				),
				array(
						'type' => 'dropdown',
						'param_name' => 'columns',
						'value' => array('1' => '1', '2' => '2', '3' => '3', '4' => '4'),
						'std' => '3',
						'heading' => __('Columns', 'directorypress-frontend'),
				),
				array(
						'type' => 'dropdown',
						'param_name' => 'columns_same_height',
						'value' => array(__('No', 'directorypress-frontend') => '0', __('Yes', 'directorypress-frontend') => '1'),
						'heading' => __('Show negative parameters?', 'directorypress-frontend'),
						'description' => __('Show parameters those have negation. For example, such row in the table will be shown: Featured Listings - No. In other case this row will be hidden.', 'directorypress-frontend'),
				),
				array(
						'type' => 'dropdown',
						'param_name' => 'show_period',
						'value' => array(__('Yes', 'directorypress-frontend') => '1', __('No', 'directorypress-frontend') => '0'),
						'heading' => __('Show package active period on choose package page?', 'directorypress-frontend'),
				),
				array(
						'type' => 'dropdown',
						'param_name' => 'show_has_sticky',
						'value' => array(__('Yes', 'directorypress-frontend') => '1', __('No', 'directorypress-frontend') => '0'),
						'heading' => __('Show is package has_sticky on choose package page?', 'directorypress-frontend'),
				),
				array(
						'type' => 'dropdown',
						'param_name' => 'show_has_featured',
						'value' => array(__('Yes', 'directorypress-frontend') => '1', __('No', 'directorypress-frontend') => '0'),
						'heading' => __('Show is package has_featured on choose package page?', 'directorypress-frontend'),
				),
				array(
						'type' => 'dropdown',
						'param_name' => 'show_categories',
						'value' => array(__('Yes', 'directorypress-frontend') => '1', __('No', 'directorypress-frontend') => '0'),
						'heading' => esc_attr__("Show package categories number on choose package page?", 'directorypress-frontend'),
				),
				array(
						'type' => 'dropdown',
						'param_name' => 'show_locations',
						'value' => array(__('Yes', 'directorypress-frontend') => '1', __('No', 'directorypress-frontend') => '0'),
						'heading' => esc_attr__("Show package locations number on choose package page?", 'directorypress-frontend'),
				),
				array(
						'type' => 'dropdown',
						'param_name' => 'show_maps',
						'value' => array(__('Yes', 'directorypress-frontend') => '1', __('No', 'directorypress-frontend') => '0'),
						'heading' => __('Show is package supports maps on choose package page?', 'directorypress-frontend'),
				),
				array(
						'type' => 'dropdown',
						'param_name' => 'show_images',
						'value' => array(__('Yes', 'directorypress-frontend') => '1', __('No', 'directorypress-frontend') => '0'),
						'heading' => esc_attr__("Show package images number on choose package page?", 'directorypress-frontend'),
				),
				array(
						'type' => 'dropdown',
						'param_name' => 'show_videos',
						'value' => array(__('Yes', 'directorypress-frontend') => '1', __('No', 'directorypress-frontend') => '0'),
						'heading' => esc_attr__("Show package videos number on choose package page?", 'directorypress-frontend'),
				),
				array(
						'type' => 'checkbox',
						'param_name' => 'visibility',
						'heading' => __("Show only on directorytype pages", "directorypress-frontend"),
						'value' => 1,
						'description' => __("Otherwise it will load plugin's files on all pages.", "directorypress-frontend"),
				),
			),
		));
		vc_map( array(
			'name'                    => __('Users Dashboard', 'directorypress-frontend'),
			'description'             => __('Listing frontend dashboard', 'directorypress-frontend'),
			'base'                    => 'directorypress-dashboard',
			'icon'                    => DIRECTORYPRESS_RESOURCES_URL . 'images/directorypress.png',
			'show_settings_on_create' => false,
			'category'                => __('Listing Content', 'directorypress-frontend'),
		));
	
}


?>