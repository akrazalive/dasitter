<?php
class directorypress_field_price_search extends directorypress_field_digit_search {

	public function search_configure($id, $action = '') {
		global $wpdb, $directorypress_object;

		if ($action == 'search_config') {
			$validation = new directorypress_form_validation();
			$validation->set_rules('mode', __('Search mode', 'directorypress-advanced-fields'), 'required|alpha_dash');
			$validation->set_rules('min_max_options[]', __('Min-Max options', 'directorypress-advanced-fields'), 'numeric');
			$validation->set_rules('slider_step_1_min', __('From option', 'directorypress-advanced-fields'), 'integer');
			$validation->set_rules('slider_step_1_max', __('To option', 'directorypress-advanced-fields'), 'integer');
			if ($validation->run()) {
				$result = $validation->result_array();
				if ($wpdb->update($wpdb->directorypress_fields, array('search_options' => serialize(array('mode' => $result['mode'], 'min_max_options' => $result['min_max_options[]'], 'slider_step_1_min' => $result['slider_step_1_min'], 'slider_step_1_max' => $result['slider_step_1_max']))), array('id' => $id), null, array('%d'))){
					directorypress_add_notification(__('Search field configuration was updated successfully!', 'directorypress-advanced-fields'));
				}
			} else {
				$this->mode = $validation->result_array('mode');
				$this->min_max_options = $validation->result_array('min_max_options[]');
				$this->slider_step_1_min = $validation->result_array('slider_step_1_min');
				$this->slider_step_1_max = $validation->result_array('slider_step_1_max');
				directorypress_add_notification($validation->error_array(), 'error');

				$search_field = $this;
				include('_html/configuration.php');
			}
		} else{
			$search_field = $this;
			include('_html/configuration.php');
		}
	}
	
	public function display_search($search_form_id, $columns = 2, $defaults = array(), $gap_in_fields = 10) {
		if ($this->mode == 'exact_number') {
			if (is_null($this->value)) {
				if (isset($defaults['field_' . $this->field->slug])) {
					$this->value = $defaults['field_' . $this->field->slug];
				}
			}
		} elseif ($this->mode == 'min_max' || $this->mode == 'min_max_slider' || $this->mode == 'range_slider') {
			if (is_null($this->min_max_value)) {
				if (isset($defaults['field_' . $this->field->slug . '_min'])) {
					$this->min_max_value['min'] = $defaults['field_' . $this->field->slug . '_min'];
				}
				if (isset($defaults['field_' . $this->field->slug . '_max'])) {
					$this->min_max_value['max'] = $defaults['field_' . $this->field->slug . '_max'];
				}
			}
		}elseif($this->mode == 'range'){
			if (isset($defaults['field_' . $this->field->slug])) {
				$this->value = $defaults['field_' . $this->field->slug];
			}
		}
		$search_field = $this;
		if ($this->mode == 'exact_number'){
			include('_html/digit.php');
		}elseif ($this->mode == 'min_max'){
			include('_html/min-max.php');
		}elseif ($this->mode == 'min_max_slider' || $this->mode == 'range_slider'){
			include('_html/slider.php');
		}elseif ($this->mode == 'range'){
			include('_html/range.php');
		}
	}
	
	public function printVisibleSearchParams($public_handler) {
		if ($this->mode == 'exact_number') {
			$field_index = 'field_' . $this->field->slug;
			if (isset($_REQUEST[$field_index]) && $_REQUEST[$field_index] && is_numeric($_REQUEST[$field_index])) {
				$value = $_REQUEST[$field_index];
				$url = remove_query_arg($field_index, $public_handler->base_url);
				echo directorypress_visible_search_param($this->field->name . ' ' . $this->field->formatPrice($value), $url);
			}
		} elseif ($this->mode == 'min_max' || $this->mode == 'min_max_slider' || $this->mode == 'range_slider') {
			$field_index = 'field_' . $this->field->slug . '_min';
			if (isset($_REQUEST[$field_index]) && $_REQUEST[$field_index] && is_numeric($_REQUEST[$field_index])) {
				$url = remove_query_arg($field_index, $public_handler->base_url);
				$value = $_REQUEST[$field_index];
				echo directorypress_visible_search_param(sprintf(__("%s from %s", "DIRECTORYPRESS"), $this->field->name, $this->field->formatPrice($value)), $url);
			}
	
			$field_index = 'field_' . $this->field->slug . '_max';
			if (isset($_REQUEST[$field_index]) && $_REQUEST[$field_index] && is_numeric($_REQUEST[$field_index])) {
				$url = remove_query_arg($field_index, $public_handler->base_url);
				$value = $_REQUEST[$field_index];
				echo directorypress_visible_search_param(sprintf(__("%s to %s", "DIRECTORYPRESS"), $this->field->name, $this->field->formatPrice($value)), $url);
			}
		}elseif($this->mode == 'range' ){
			$field_index = 'field_' . $this->field->slug;
			if (isset($_REQUEST[$field_index]) && $_REQUEST[$field_index]) {
				$value = $_REQUEST[$field_index];
				$url = remove_query_arg($field_index, $public_handler->base_url);
				echo directorypress_visible_search_param($this->field->name . ' ' . $value, $url);
			}
		}
	}
}