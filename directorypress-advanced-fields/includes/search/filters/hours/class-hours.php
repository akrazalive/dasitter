<?php 

class directorypress_field_hours_search extends directorypress_field_search {
	//public $mode = 'exact_number';
	//public $min_max_options = array();
	//public $min_max_value;
	//public $slider_step_1_min = 0;
	//public $slider_step_1_max = 100;
	
	
	
	public function is_this_field_param($param) {
		
		if ($param == 'field_' . $this->field->slug){
			return true;
		}
		
	}
	
	public function display_search($search_form_id, $columns = 2, $defaults = array(), $gap_in_fields = 10) {
		//if ($this->search_input_mode == 'input') {
			if (is_null($this->value) && isset($defaults['field_' . $this->field->slug])) {
				$this->value = $defaults['field_' . $this->field->slug];
			}
			
			$search_field = $this;
			include('_html/input.php');
		//}
	}
	
	public function search_validation(&$args, $defaults = array(), $include_GET_params = true) {
		$days = $this->field->days_order();
		if(isset($this->field->value['timezone']) && !empty($this->field->value['timezone'])){
			$timezone = $this->field->value['timezone'];
			//date_default_timezone_set($timezone);
		}else{
			$timezone =  get_option('timezone_string');
		}
		
		$time = date('h:i A');
		$time = strtotime($time);
		$current_day = date('D');
		$opening = '';
		$closing = '';
		foreach ($days AS $key=>$day) {
			
			if($day == $current_day){
				$opening .= strtotime($this->field->value[$day.'_opening']);
				$closing .= strtotime($this->field->value[$day.'_closing']);
				
			}
		}
		if(!empty($opening) && !empty($closing)){
			
		
			$field_index = 'field_' . $this->field->slug;
	
			if ($include_GET_params){
				$this->value = ((directorypress_get_input_value($_REQUEST, $field_index, false) !== false) ? directorypress_get_input_value($_REQUEST, $field_index) : directorypress_get_input_value($defaults, $field_index));
			}else{
				$this->value = directorypress_get_input_value($defaults, $field_index, false);
			}
			
			if ($this->value !== false && $this->value !== "") {
				$args['meta_query']['relation'] = 'AND';
				foreach ($this->field->value AS $val) {
					$args['meta_query'][] = array(
							'key' => '_field_' . $this->field->id,
							'value' => $val[$current_day.'_opening'],
							'compare' => 'LIKE'
					);
				}
			}
		}
	}
	
	public function gat_base_url_args(&$args) {
		if ($this->mode == 'exact_number' || $this->mode == 'range' ) {
			parent::gat_base_url_args($args);
		} elseif ($this->mode == 'min_max' || $this->mode == 'min_max_slider' || $this->mode == 'range_slider') {
			$field_index = 'field_' . $this->field->slug . '_min';
			if (isset($_REQUEST[$field_index]) && is_numeric($_REQUEST[$field_index]))
				$args[$field_index] = $_REQUEST[$field_index];
				
			$field_index = 'field_' . $this->field->slug . '_max';
			if (isset($_REQUEST[$field_index]) && is_numeric($_REQUEST[$field_index]))
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