<?php
function DpMs_Templates($template) {
	$templates = array(
		$template
	);

	foreach ($templates AS $template_to_check) {
		if (is_file($template_to_check)) {
			return $template_to_check;
		} elseif (is_file(get_stylesheet_directory() . '/directorypress/public/' . $template_to_check)) { // theme or child theme templates folder
			return get_stylesheet_directory() . '/directorypress/public/' . $template_to_check;
		} elseif (is_file(DPMS_TEMPLATES_PATH . $template_to_check)) { // native plugin's templates folder
			return DPMS_TEMPLATES_PATH . $template_to_check;
		}
	}

	return false;
}

if (!function_exists('dpms_display_template')) {
	function dpms_display_template($template, $args = array(), $return = false) {
		global $directorypress_object;
	
		if ($args) {
			extract($args);
		}
		
		$template = apply_filters('dpms_display_template', $template, $args);
		
		if (is_array($template)) {
			$template_path = $template[0];
			$template_file = $template[1];
			$template = $template_path . $template_file;
		}
		
		$template = DpMs_Templates($template);

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