<?php 

class directorypress_field_search {
	public $value;

	public $field;
	
	public function assign_fields($field) {
		$this->field = $field;
	}
	
	public function convert_search_options() {
		if ($this->field->search_options) {
			if (is_string($this->field->search_options)) {
				$unserialized_options = unserialize($this->field->search_options);
			} elseif (is_array($this->field->search_options)) {
				$unserialized_options = $this->field->search_options;
			}
			if (count($unserialized_options) > 1 || $unserialized_options != array('')) {
				$this->field->search_options = $unserialized_options;
				if (method_exists($this, 'build_search_options')) {
					$this->build_search_options();
				}
				return $this->field->search_options;
			}
		}
		return array();
	}
	
	public function gat_base_url_args(&$args) {
		$field_index = 'field_' . $this->field->slug;
		
		if (isset($_REQUEST[$field_index]) && $_REQUEST[$field_index])
			$args[$field_index] = $_REQUEST[$field_index];
	}
	
	public function gat_vc_params() {
		return array();
	}
	
	public function is_this_field_param($param) {
		if ($param == 'field_' . $this->field->slug) {
			return true;
		}
	}
	
	public function reset_field_value() {
		$this->value = null;
	}
}
?>