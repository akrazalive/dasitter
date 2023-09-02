<?php 

class directorypress_field_price extends directorypress_field {
	public $currency_symbol = '$';
	public $decimal_separator = ',';
	public $thousands_separator = ' ';
	public $symbol_position = 1;
	public $hide_decimals = 0;
	public $range_options = array();
	
	protected $is_configuration_page = true;
	protected $is_search_configuration_page = true;
	protected $can_be_searched = true;
	
	public function is_field_not_empty($listing) {
		if ($this->value) {
			return true;
		} else {
			return false;
		}
	}

	public function configure() {
		global $wpdb, $directorypress_object;

		if (directorypress_get_input_value($_POST, 'submit') && wp_verify_nonce($_POST['directorypress_configure_fields_nonce'], DIRECTORYPRESS_PATH)) {
			$validation = new directorypress_form_validation();
			$validation->set_rules('currency_symbol', __('Currency symbol', 'DIRECTORYPRESS'), 'required');
			$validation->set_rules('decimal_separator', __('Decimal separator', 'DIRECTORYPRESS'), 'required|max_length[1]');
			$validation->set_rules('thousands_separator', __('Thousands separator', 'DIRECTORYPRESS'), 'max_length[1]');
			$validation->set_rules('symbol_position', __('Currency symbol position', 'DIRECTORYPRESS'), 'integer');
			$validation->set_rules('hide_decimals', __('Hide decimals', 'DIRECTORYPRESS'), 'required');
			$validation->set_rules('range_options[]', __('Range options', 'DIRECTORYPRESS'), '');
			if ($validation->run()) {
				$result = $validation->result_array();
				if ($wpdb->update($wpdb->directorypress_fields, array('options' => serialize(
						array(
								'currency_symbol' => $result['currency_symbol'],
								'decimal_separator' => $result['decimal_separator'],
								'thousands_separator' => $result['thousands_separator'],
								'symbol_position' => $result['symbol_position'],
								'hide_decimals' => $result['hide_decimals'],
								'hide_decimals' => $result['hide_decimals'],
								'range_options' => $result['range_options[]'],
						)
					)), array('id' => $this->id), null, array('%d'))) {
						directorypress_add_notification(__('Field configuration was updated successfully!', 'DIRECTORYPRESS'));
				}
				
				$directorypress_object->fields_handler_property->showContentFieldsTable();
			} else {
				$this->currency_symbol = $validation->result_array('currency_symbol');
				$this->decimal_separator = $validation->result_array('decimal_separator');
				$this->thousands_separator = $validation->result_array('thousands_separator');
				$this->symbol_position = $validation->result_array('symbol_position');
				$this->hide_decimals = $validation->result_array('hide_decimals');
				$this->range_options = $validation->result_array('range_options[]');
				directorypress_add_notification($validation->error_array(), 'error');

				directorypress_display_template('directorypress_fields/fields/directorypress_price_configuration.php', array('field' => $this));
			}
		} else
			directorypress_display_template('directorypress_fields/fields/directorypress_price_configuration.php', array('field' => $this));
	}
	
	public function build_field_options() {
		if (isset($this->options['currency_symbol'])) {
			$this->currency_symbol = $this->options['currency_symbol'];
		}
		if (isset($this->options['decimal_separator'])) {
			$this->decimal_separator = $this->options['decimal_separator'];
		}
		if (isset($this->options['thousands_separator'])) {
			$this->thousands_separator = $this->options['thousands_separator'];
		}
		if (isset($this->options['symbol_position'])) {
			$this->symbol_position = $this->options['symbol_position'];
		}
		if (isset($this->options['hide_decimals'])) {
			$this->hide_decimals = $this->options['hide_decimals'];
		}
		if (isset($this->options['range_options'])) {
			$this->range_options = $this->options['range_options'];
		}
	}
	
	public function renderInput() {
		if (!($template = directorypress_has_template('directorypress_fields/fields/directorypress_price_input_'.$this->id.'.php'))) {
			$template = 'directorypress_fields/fields/directorypress_price_input.php';
		}
		
		$template = apply_filters('directorypress_field_input_template', $template, $this);
			
		directorypress_display_template($template, array('field' => $this));
	}
	
	public function validate_field_values(&$errors, $data) {
		$field_index = 'directorypress-field-input-' . $this->id;
		$field_index_end_value = 'directorypress-field-input-' . $this->id . '-end';
		$field_index_range_value = 'directorypress-field-input-' . $this->id . '-range';
		$validation = new directorypress_form_validation();
		$rules = 'numeric';
		$rules2 = 'numeric';
		if ($this->is_this_field_requirable() && $this->is_required)
			$rules .= '|required';
		$validation->set_rules($field_index, $this->name, $rules);
		$validation->set_rules($field_index_end_value, $this->name, $rules);
		$validation->set_rules($field_index_range_value, $this->name, $rules2);
		if (!$validation->run()) {
			$errors[] = $validation->error_array();
		}

		//return $validation->result_array($field_index);
		return array(
			'price_start' => $validation->result_array($field_index),
			'price_end' => $validation->result_array($field_index_end_value),
			'price_range' => $validation->result_array($field_index_range_value)
		);
	}
	
	public function save_field_value($post_id, $validation_results) {
		//return update_post_meta($post_id, '_field_' . $this->id, $validation_results);
		if ($validation_results && is_array($validation_results)) {
			update_post_meta($post_id, '_field_' . $this->id, $validation_results['price_start']);
			update_post_meta($post_id, '_field_' . $this->id . '_price_end', $validation_results['price_end']);
			update_post_meta($post_id, '_field_' . $this->id . '_price_range', $validation_results['price_range']);
			return true;
		}
	}
	
	public function load_field_value($post_id) {
		$this->value = array(
			'price_start' => 0,
			'price_end' => 0
		);
		$price_start = 0;
		$price_end = 0;
		$price_range = '';
		
		$price_start = get_post_meta($post_id, '_field_' . $this->id, true);
		if (get_post_meta($post_id, '_field_' . $this->id . '_price_end', true)) {
			$price_end = get_post_meta($post_id, '_field_' . $this->id . '_price_end', true);
		}
		if (get_post_meta($post_id, '_field_' . $this->id . '_price_range', true)) {
			$price_range = get_post_meta($post_id, '_field_' . $this->id . '_price_range', true);
		}
		$this->value = array(
			'price_start' => $price_start,
			'price_end' => $price_end,
			'price_range' => $price_range
		);
		$this->value = apply_filters('directorypress_field_load', $this->value, $this, $post_id);
		return $this->value;
	}
	public function renderRangeOutput($listing = null) {
		
		if ($this->value['price_start'] || $this->value['price_end'] || $this->value['price_range']) {
			if (is_numeric($this->value['price_start'])) {
				$price_start = $this->value['price_start'];
			}
			
			$price_end = $this->value['price_end'];
			$price_range = $this->value['price_range'];
			if (!($template = directorypress_has_template('directorypress_fields/fields/directorypress_price_output_range_'.$this->id.'.php'))) {
				$template = 'directorypress_fields/fields/directorypress_price_output_range.php';
			}
			
			$template = apply_filters('directorypress_field_output_template', $template, $this, $listing);
			directorypress_display_template($template, array('field' => $this, 'price_start' => $price_start, 'price_end' => $price_end, 'price_range' => $price_range, 'listing' => $listing));	
			
		}
	}
	public function display_output($listing = null) {
		/* if (is_numeric($this->value)) {
			if (!($template = directorypress_has_template('directorypress_fields/fields/directorypress_price_output_'.$this->id.'.php'))) {
				$template = 'directorypress_fields/fields/directorypress_price_output.php';
			}
			
			$template = apply_filters('directorypress_field_output_template', $template, $this, $listing);
				
			directorypress_display_template($template, array('field' => $this, 'listing' => $listing));
		} */
		if ($this->value['price_start'] || $this->value['price_end'] || $this->value['price_range']) {
			if (is_numeric($this->value['price_start'])) {
				$price_start = $this->value['price_start'];
			}
			//if (is_numeric($this->value['price_end'])) {
				$price_end = $this->value['price_end'];
			//}
			//if (isset($this->value['price_range'])) {
				$price_range = $this->value['price_range'];
		//	}
			if (!($template = directorypress_has_template('directorypress_fields/fields/directorypress_price_output_'.$this->id.'.php'))) {
				$template = 'directorypress_fields/fields/directorypress_price_output.php';
			}
			
			$template = apply_filters('directorypress_field_output_template', $template, $this, $listing);
				
			directorypress_display_template($template, array('field' => $this, 'price_start' => $price_start, 'price_end' => $price_end, 'price_range' => $price_range, 'listing' => $listing));
		}
	}
	
	public function order_params() {
		global $DIRECTORYPRESS_ADIMN_SETTINGS;
		$order_params = array('orderby' => 'meta_value_num', 'meta_key' => '_field_' . $this->id);
		if ($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_orderby_exclude_null']){
			$order_params['meta_query'] = array(
				array(
					'key' => '_field_' . $this->id,
					'value'   => array(''),
					'compare' => 'NOT IN'
				)
			);
		}
		return $order_params;
	}
	
	public function validate_csv_values($value, &$errors) {
		if (!is_numeric($value)) {
			$errors[] = sprintf(__('The %s field must contain only numbers.', 'DIRECTORYPRESS'), $this->name);
		}

		return $value;
	}
	
	public function disaply_output_on_map($location, $listing) {
		if (is_numeric($this->value)) {
			return $this->formatPrice();
		}
	}
	
	public function formatPrice() {
		if ($this->hide_decimals) {
			$decimals = 0;
		} else {
			$decimals = 2;
		}
		$formatted_price = number_format($this->value['price_start'], $decimals, $this->decimal_separator, $this->thousands_separator);
		$out = $formatted_price;
		 switch ($this->symbol_position) {
			case 1:
				$out = $this->currency_symbol . $out;
				break;
			case 2:
				$out = $this->currency_symbol . ' ' . $out;
				break;
			case 3:
				$out = $out . $this->currency_symbol;
				break;
			case 4:
				$out = $out . ' ' . $this->currency_symbol;
				break;
		}
		
		return $out;
	}
	public function formatPriceEnd() {
		if($this->value['price_end'] == 0){
			return;
		}
			if ($this->hide_decimals) {
				$decimals = 0;
			} else {
				$decimals = 2;
			}
			//$formatted_price = number_format($this->value['price_start'], $decimals, $this->decimal_separator, $this->thousands_separator);
			
			$formatted_price = number_format($this->value['price_end'], $decimals, $this->decimal_separator, $this->thousands_separator);
			$price_split = ' - ';
			$out = $formatted_price;
			switch ($this->symbol_position) {
				case 1:
					$out = $this->currency_symbol . $out;
					break;
				case 2:
					$out = $this->currency_symbol . ' ' . $out;
					break;
				case 3:
					$out = $out . $this->currency_symbol;
					break;
				case 4:
					$out = $out . ' ' . $this->currency_symbol;
					break;
			}
			
			return $price_split . $out;
		
	}
	public function RangePrice() {
		//$range_price = $this->value['price_range'];
		$range_price = $this->range_options[$this->value['price_range']];
		$out = $range_price;
		return $out;
	}
	
}
?>