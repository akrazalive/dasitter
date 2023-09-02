<?php

function directorypress_advanced_fields_has_template($template) {
	$templates = array(
			$template
	);

	foreach ($templates AS $template_to_check) {
		if (is_file($template_to_check)) {
			return $template_to_check;
		}elseif (is_file(get_stylesheet_directory() . '/directorypress/public/' . $template_to_check)) {
			return get_stylesheet_directory() . '/directorypress/public/' . $template_to_check;
		}elseif (is_file(get_template_directory() . '/directorypress/public/' . $template_to_check)) {
			return get_template_directory() . '/directorypress/public/' . $template_to_check;
		}elseif (is_file(DIRECTORYPRESS_ADVANCED_FIELDS_TEMPLATES_PATH . $template_to_check)) {
			return DIRECTORYPRESS_ADVANCED_FIELDS_TEMPLATES_PATH . $template_to_check;
		}
	}

	return false;
}

if (!function_exists('directorypress_advanced_fields_display_template')) {
	function directorypress_advanced_fields_display_template($template, $args = array(), $return = false) {
	
		if ($args) {
			extract($args);
		}
		
		$template = apply_filters('directorypress_advanced_fields_display_template', $template, $args);
		
		if (is_array($template)) {
			$template_path = $template[0];
			$template_file = $template[1];
			$template = $template_path . $template_file;
		}
		
		$template = directorypress_advanced_fields_has_template($template);

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