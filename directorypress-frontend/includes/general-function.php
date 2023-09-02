<?php

function DpFl_Templates($template) {
	$templates = array(
			$template
	);

	foreach ($templates AS $template_to_check) {
		if (is_file($template_to_check)) {
			return $template_to_check;
		} elseif (is_file(get_stylesheet_directory() . '/directorypress/public/' . $template_to_check)) { // theme or child theme templates folder
			return get_stylesheet_directory() . '/directorypress/public/' . $template_to_check;
		} elseif (is_file(DPFL_TEMPLATES_PATH . $template_to_check)) { // native plugin's templates folder
			return DPFL_TEMPLATES_PATH . $template_to_check;
		}
	}

	return false;
}

if (!function_exists('dpfl_renderTemplate')) {
	function dpfl_renderTemplate($template, $args = array(), $return = false) {
		global $directorypress_object;
	
		if ($args) {
			extract($args);
		}
		
		$template = apply_filters('dpfl_render_template', $template, $args);
		
		if (is_array($template)) {
			$template_path = $template[0];
			$template_file = $template[1];
			$template = $template_path . $template_file;
		}
		
		$template = DpFl_Templates($template);

		if ($template) {
			if ($return) {
				ob_start();
			}
		
			include($template);
			
			if ($return) {
				$output = ob_get_contents();
				ob_end_clean();
				return $output;
			}
		}
	}
}
add_filter( 'theme_page_templates', 'dashboad_template' );
function dashboad_template($page_templates  ){
			
		$path = 'template-dashboard.php';
		
		$page_templates[$path] =  __(' Directorypress Dashboard');
    return $page_templates;
}


add_filter( 'template_include', 'directorypress_load_template', 99 );
function directorypress_load_template( $template ) {
    global $post;
	if($post){
		$template_page = get_post_meta($post->ID, '_wp_page_template', true);
		if($template_page == 'template-dashboard.php'){
			$new_template = dpfl_renderTemplate('partials/dashboard/template-dashboard.php');
			return $new_template;	
		}
	}

    return $template;

}
function directorypress_install_fsubmit() {
	add_option('directorypress_fsubmit_default_status', 3);
	add_option('directorypress_fsubmit_login_mode', 1);

	directorypress_upgrade_fsubmit('1.5.0');
	directorypress_upgrade_fsubmit('1.5.4');
	directorypress_upgrade_fsubmit('1.6.2');
	directorypress_upgrade_fsubmit('1.8.3');
	directorypress_upgrade_fsubmit('1.8.4');
	directorypress_upgrade_fsubmit('1.9.0');
	directorypress_upgrade_fsubmit('1.9.7');
	directorypress_upgrade_fsubmit('1.10.0');
	directorypress_upgrade_fsubmit('1.12.7');
	directorypress_upgrade_fsubmit('1.13.0');
	
	add_option('directorypress_installed_fsubmit', 1);
}

function directorypress_upgrade_fsubmit($new_version) {
	
}

function directorypress_submitUrl($path = '') {
	global $directorypress_object;
	
	$submit_page_url = '';

	if (!empty($path['directorytype'])) {
		if (($directorytype = $directorypress_object->directorytypes->directory_by_id($path['directorytype'])) && isset($directorypress_object->submit_pages_all[$directorytype->id])) {
			$submit_page_url = $directorypress_object->submit_pages_all[$directorytype->id]['url'];
			unset($path['directorytype']);
		}
	} else {
		if (isset($directorypress_object->submit_pages_all[$directorypress_object->current_directorytype->id])) {
			$submit_page_url = $directorypress_object->submit_pages_all[$directorypress_object->current_directorytype->id]['url'];
		}
	}
	if (!$submit_page_url) {
		if (isset($directorypress_object->submit_pages_all[$directorypress_object->directorytypes->directorypress_get_base_directorytype()->id])) {
			$submit_page_url = $directorypress_object->submit_pages_all[$directorypress_object->directorytypes->directorypress_get_base_directorytype()->id]['url'];
		}
	}
		
	// adapted for WPML
	global $sitepress;
	if (function_exists('wpml_object_id_filter') && $sitepress) {
		if ($sitepress->get_option('language_negotiation_type') == 3) {
			// remove any previous value.
			$submit_page_url = remove_query_arg('lang', $submit_page_url);
		}
	}

	if (!is_array($path)) {
		if ($path) {
			// found that on some instances of WP "native" trailing slashes may be missing
			$url = rtrim($submit_page_url, '/') . '/' . rtrim($path, '/') . '/';
		} else
			$url = $submit_page_url;
	} else
		$url = add_query_arg($path, $submit_page_url);

	// adapted for WPML
	global $sitepress;
	if (function_exists('wpml_object_id_filter') && $sitepress) {
		$url = $sitepress->convert_url($url);
	}

	return $url;
}

function directorypress_dashboardUrl($path = '') {
	global $directorypress_object;
	
	if ($directorypress_object->dashboard_page_url) {
		// adapted for WPML
		global $sitepress;
		if (function_exists('wpml_object_id_filter') && $sitepress) {
			if ($sitepress->get_option('language_negotiation_type') == 3) {
				// remove any previous value.
				$directorypress_object->dashboard_page_url = remove_query_arg('lang', $directorypress_object->dashboard_page_url);
			}
		}
	
		if (!is_array($path)) {
			if ($path) {
				// found that on some instances of WP "native" trailing slashes may be missing
				$url = rtrim($directorypress_object->dashboard_page_url, '/') . '/' . rtrim($path, '/') . '/';
			} else
				$url = $directorypress_object->dashboard_page_url;
		} else
			$url = add_query_arg($path, $directorypress_object->dashboard_page_url);
	
		// adapted for WPML
		global $sitepress;
		if (function_exists('wpml_object_id_filter') && $sitepress) {
			$url = $sitepress->convert_url($url);
		}
	} else
		$url = directorypress_directorytype_url();

	return $url;
}

// Image Upload Processor
if( !function_exists('dpfl_handle_image_upload') ){
	function dpfl_handle_image_upload( $file, $attach_to = 0 ){
		$movefile = wp_handle_upload( $file, array( 'test_form' => false ) );

		if( !empty( $movefile['url'] ) ){
			$attachment = array(
				'guid'           => $movefile['url'],
				'post_mime_type' => $movefile['type'],
				'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $movefile['file'] ) ),
				'post_content'   => '',
				'post_status'    => 'inherit'
			);

			$attach_id = wp_insert_attachment( $attachment, $movefile['file'], $attach_to );

			require_once( ABSPATH . 'wp-admin/includes/image.php' );

			$attach_data = wp_generate_attachment_metadata( $attach_id, $movefile['file'] );
			wp_update_attachment_metadata( $attach_id, $attach_data );

			return $attach_id;
		}
	}
}

if( !function_exists( 'init_listings' ) ){
	function init_listings(){
		global $current_user, $directorypress_object,$public_handler;
		$response = array();
        $dashboard = new directorypress_dashboard_handler();
		$data = $dashboard->display();
        $response['type'] = 'success';
        $response['message'] = $data;
        wp_send_json($response); 
	}
	add_action('wp_ajax_init_listings', 'init_listings');
    add_action('wp_ajax_nopriv_init_listings', 'init_listings');
}

// remove unwanted woocomerce menu items
add_filter ( 'woocommerce_account_menu_items', 'dpfl_remove_woo_menu_items' );
function dpfl_remove_woo_menu_items( $menu_links ){
	unset( $menu_links['edit-account'] );
	unset( $menu_links['customer-logout'] );
	unset( $menu_links['dashboard'] );
	return $menu_links;
 
}
