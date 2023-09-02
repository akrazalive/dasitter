<?php 

class directorypress_field_digit extends directorypress_field {
	public $is_integer = false;
	public $decimal_separator = ',';
	public $thousands_separator = ' ';
	public $min = 0;
	public $max;

	protected $is_configuration_page = true;
	protected $is_search_configuration_page = true;
	protected $can_be_searched = true;
	
	public function is_field_not_empty($listing) {
		if ($this->value)
			return true;
		else
			return false;
	}

	public function configure($id, $action = '') {
		global $wpdb, $directorypress_object;

		if ($action == 'config') {
			$validation = new directorypress_form_validation();
			$validation->set_rules('is_integer', __('Is integer or decimal', 'directorypress-advanced-fields'), 'required|natural');
			$validation->set_rules('decimal_separator', __('Decimal separator',  'directorypress-advanced-fields'), 'required|max_length[1]');
			$validation->set_rules('thousands_separator', __('Thousands separator', 'directorypress-advanced-fields'), 'max_length[1]');
			$validation->set_rules('min', __('Min', 'directorypress-advanced-fields'), 'numeric');
			$validation->set_rules('max', __('Max', 'directorypress-advanced-fields'), 'numeric');
			if ($validation->run()) {
				$result = $validation->result_array();
				if ($wpdb->update($wpdb->directorypress_fields, array('options' => serialize(array('is_integer' => $result['is_integer'], 'decimal_separator' => $result['decimal_separator'], 'thousands_separator' => $result['thousands_separator'], 'min' => $result['min'], 'max' => $result['max']))), array('id' => $id), null, array('%d'))){
					directorypress_add_notification(__('Field configuration was updated successfully!', 'directorypress-advanced-fields'));
				}
			} else {
				$this->is_integer = $validation->result_array('is_integer');
				$this->decimal_separator = $validation->result_array('decimal_separator');
				$this->thousands_separator = $validation->result_array('thousands_separator');
				$this->min = $validation->result_array('min');
				$this->max = $validation->result_array('max');
				directorypress_add_notification($validation->error_array(), 'error');

				$field = $this;
				include('_html/configuration.php');
			}
		} else{
			$field = $this;
			include('_html/configuration.php');
		}
	}
	
	public function build_field_options() {
		if (isset($this->options['is_integer']))
			$this->is_integer = $this->options['is_integer'];
		if (isset($this->options['decimal_separator']))
			$this->decimal_separator = $this->options['decimal_separator'];
		if (isset($this->options['thousands_separator']))
			$this->thousands_separator = $this->options['thousands_separator'];
		if (isset($this->options['min']))
			$this->min = $this->options['min'];
		if (isset($this->options['max']))
			$this->max = $this->options['max'];
	}
	
	public function renderInput() {
		$field = $this;
		include('_html/input.php');
	}
	
	public function validate_field_values(&$errors, $data) {
		$field_index = 'directorypress-field-input-' . $this->id;

		$validation = new directorypress_form_validation();
		$rules = 'numeric';
		if ($this->is_this_field_requirable() && $this->is_required)
			$rules .= '|required';
		if ($this->is_integer)
			$rules .= '|integer';
		if (is_numeric($this->min))
			$rules .= '|greater_than[' . $this->min . ']';
		if (is_numeric($this->max))
			$rules .= '|less_than[' . $this->max . ']';
		$validation->set_rules($field_index, $this->name, $rules);
		if (!$validation->run())
			$errors[] = implode("", $validation->error_array());
	
		return $validation->result_array($field_index);
	}
	
	public function save_field_value($post_id, $validation_results) {
		return update_post_meta($post_id, '_field_' . $this->id, $validation_results);
	}
	
	public function load_field_value($post_id) {
		$this->value = get_post_meta($post_id, '_field_' . $this->id, true);
		
		$this->value = apply_filters('directorypress_field_load', $this->value, $this, $post_id);
		return $this->value;
	}
	
	public function display_output($listing = null) {
		if (is_numeric($this->value)) {
			if ($this->is_integer)
				$decimals = 0;
			else 
				$decimals = 2;
			$formatted_number = number_format($this->value, $decimals, $this->decimal_separator, $this->thousands_separator);

			$field = $this;
			include('_html/output.php');
		}
	}
	
	public function order_params() {
		global $DIRECTORYPRESS_ADIMN_SETTINGS;
		$order_params = array('orderby' => 'meta_value_num', 'meta_key' => '_field_' . $this->id);
		if ($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_orderby_exclude_null'])
			$order_params['meta_query'] = array(
				array(
					'key' => '_field_' . $this->id,
					'value'   => array(''),
					'compare' => 'NOT IN'
				)
			);
		return $order_params;
	}
	
	public function validate_csv_values($value, &$errors) {
		if (!is_numeric($value))
			$errors[] = sprintf(__('The %s field must contain only numbers.', 'directorypress-advanced-fields'), $this->name);
		elseif ($this->is_integer && !ctype_digit($value))
			$errors[] = sprintf(__('The %s field must contain an integer.', 'directorypress-advanced-fields'), $this->name);
		elseif (is_numeric($this->min) && $value < $this->min)
			$errors[] = sprintf(__('The %s field must contain a number greater than %s.', 'directorypress-advanced-fields'), $this->name, $this->min);
		elseif (is_numeric($this->max) && $value > $this->max)
			$errors[] = sprintf(__('The %s field must contain a number less than %s.', 'directorypress-advanced-fields'), $this->name, $this->max);

		return $value;
	}
	
	public function disaply_output_on_map($location, $listing) {
		if (is_numeric($this->value)) {
			if ($this->is_integer)
				$decimals = 0;
			else 
				$decimals = 2;
			$formatted_number = number_format($this->value, $decimals, $this->decimal_separator, $this->thousands_separator);
	
			return $formatted_number;
		}
	}
}
?>