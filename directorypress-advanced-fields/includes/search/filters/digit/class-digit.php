<?php 

class directorypress_field_digit_search extends directorypress_field_search {
	public $mode = 'exact_number';
	public $min_max_options = array();
	public $min_max_value;
	public $slider_step_1_min = 0;
	public $slider_step_1_max = 100;

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
	
	public function build_search_options() {
		if (isset($this->field->search_options['mode']))
			$this->mode = $this->field->search_options['mode'];
		if (isset($this->field->search_options['min_max_options']))
			$this->min_max_options = $this->field->search_options['min_max_options'];
		if (isset($this->field->search_options['slider_step_1_min']))
			$this->slider_step_1_min = $this->field->search_options['slider_step_1_min'];
		if (isset($this->field->search_options['slider_step_1_max']))
			$this->slider_step_1_max = $this->field->search_options['slider_step_1_max'];

		// set up Search range slider with step 1 when there aren't enough min-max options
		if ($this->mode == 'min_max_slider' && count($this->min_max_options) < 2)
			$this->mode = 'range_slider';
			
	}
	
	public function is_this_field_param($param) {
		if ($this->mode == 'exact_number') {
			if ($param == 'field_' . $this->field->slug)
				return true;
		} elseif ($this->mode == 'min_max' || $this->mode == 'min_max_slider' || $this->mode == 'range_slider') {
			if ($param == 'field_' . $this->field->slug . '_min' || $param == 'field_' . $this->field->slug . '_max')
				return true;
		}elseif($this->mode == 'range'){
			if ($param == 'field_' . $this->field->slug)
				return true;
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
		}elseif($this->mode == 'range') {
			if (is_null($this->value)) {
				if (isset($defaults['field_' . $this->field->slug])) {
					$this->value = $defaults['field_' . $this->field->slug];
				}
			}
		}
		$unique_id = uniqid();
		$search_field = $this;
		if ($this->mode == 'exact_number')
			include('_html/digit.php');
		elseif ($this->mode == 'min_max')
			include('_html/min-max.php');
		elseif ($this->mode == 'min_max_slider' || $this->mode == 'range_slider')
			include('_html/slider.php');
	}
	
	public function search_validation(&$args, $defaults = array(), $include_GET_params = true) {
		if ($this->mode == 'exact_number') {
			$field_index = 'field_' . $this->field->slug;

			if ($include_GET_params)
				$this->value = ((directorypress_get_input_value($_REQUEST, $field_index, false) !== false) ? directorypress_get_input_value($_REQUEST, $field_index) : directorypress_get_input_value($defaults, $field_index));
			else
				$this->value = directorypress_get_input_value($defaults, $field_index, false);

			if ($this->value !== false && is_numeric($this->value)) {
				$args['meta_query']['relation'] = 'AND';
				$args['meta_query'][] = array(
						'key' => '_field_' . $this->field->id,
						'value' => $this->value,
						'type' => 'numeric'
				);
			}
		} elseif ($this->mode == 'min_max' || $this->mode == 'min_max_slider' || $this->mode == 'range_slider') {
			$field_index = 'field_' . $this->field->slug . '_min';
			
			if ($include_GET_params)
				$this->min_max_value['min'] = ((directorypress_get_input_value($_REQUEST, $field_index, false) !== false) ? directorypress_get_input_value($_REQUEST, $field_index) : directorypress_get_input_value($defaults, $field_index));
			else
				$this->min_max_value['min'] = directorypress_get_input_value($defaults, $field_index, false);

			if ($this->min_max_value['min'] !== false && is_numeric($this->min_max_value['min'])) {
				$args['meta_query']['relation'] = 'AND';
				$args['meta_query'][] = array(
						'key' => '_field_' . $this->field->id,
						'value' => $this->min_max_value['min'],
						'type' => 'numeric',
						'compare' => '>='
				);
			}

			$field_index = 'field_' . $this->field->slug . '_max';
			
			if ($include_GET_params)
				$this->min_max_value['max'] = ((directorypress_get_input_value($_REQUEST, $field_index, false) !== false) ? directorypress_get_input_value($_REQUEST, $field_index) : directorypress_get_input_value($defaults, $field_index));
			else
				$this->min_max_value['max'] = directorypress_get_input_value($defaults, $field_index, false);
			
			if ($this->min_max_value['max'] !== false && is_numeric($this->min_max_value['max'])) {
				$args['meta_query']['relation'] = 'AND';
				$args['meta_query'][] = array(
						'key' => '_field_' . $this->field->id,
						'value' => $this->min_max_value['max'],
						'type' => 'numeric',
						'compare' => '<='
				);
			}
		}elseif($this->mode == 'range'){
			$field_index = 'field_' . $this->field->slug;
	
			if ($include_GET_params){
				$this->value = ((directorypress_get_input_value($_REQUEST, $field_index, false) != false) ? directorypress_get_input_value($_REQUEST, $field_index) : directorypress_get_input_value($defaults, $field_index));
			}else{
				$this->value = directorypress_get_input_value($defaults, $field_index, false);
			}
			if ($this->value) {
				if (!is_array($this->value)) {
					$this->value = array_filter(explode(',', $this->value), 'strlen');
				}

				$args['meta_query']['relation'] = 'AND';

				foreach ($this->value AS $val) {
					$args['meta_query'][] = array(
							'key' => '_field_' . $this->field->id.'_range_options',
							'value' => $val
					);
				}
			}
		}
	}
	
	public function gat_base_url_args(&$args) {
		if ($this->mode == 'exact_number') {
			parent::gat_base_url_args($args);
		} elseif ($this->mode == 'min_max' || $this->mode == 'min_max_slider' || $this->mode == 'range_slider') {
			$field_index = 'field_' . $this->field->slug . '_min';
			if (isset($_REQUEST[$field_index]) && is_numeric($_REQUEST[$field_index]))
				$args[$field_index] = $_REQUEST[$field_index];
				
			$field_index = 'field_' . $this->field->slug . '_max';
			if (isset($_REQUEST[$field_index]) && is_numeric($_REQUEST[$field_index]))
				$args[$field_index] = $_REQUEST[$field_index];
			
		}elseif($this->mode == 'range'){
			$field_index = 'field_' . $this->field->slug;
			if (isset($_REQUEST[$field_index]))
				$args[$field_index] = $_REQUEST[$field_index];
		}
	}
	
	public function gat_vc_params() {
		if ($this->mode == 'exact_number') {
			return array(
					array(
							'type' => 'textfield',
							'param_name' => 'field_' . $this->field->slug,
							'heading' => $this->field->name,
					),
			);
		} elseif ($this->mode == 'min_max' || $this->mode == 'min_max_slider' || $this->mode == 'range_slider') {
			return array(
					array(
							'type' => 'textfield',
							'param_name' => 'field_' . $this->field->slug . '_min',
							'heading' => $this->field->name . ' ' . __('Min', 'directorypress-advanced-fields'),
					),
					array(
							'type' => 'textfield',
							'param_name' => 'field_' . $this->field->slug . '_max',
							'heading' => $this->field->name . ' ' . __('Max', 'directorypress-advanced-fields'),
					),
			);
		}
	}
	
	public function reset_field_value() {
		$this->min_max_value = array('min' => '', 'max' => '');
	}
}
?>