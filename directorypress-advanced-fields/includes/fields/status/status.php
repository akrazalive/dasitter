<?php 

class directorypress_field_status extends directorypress_field_select {
	//public $how_display_items = 'checked';
	public $is_core_field = 1;
	public $value = array();
	public $check_icon_type;
	public $icon_selection_items = array();
	protected $is_slug = true;
	protected $can_be_searched = true;
	protected $is_search_configuration_page = true;
	
	public function __construct() {
		parent::__construct();
		
		add_action('directorypress_select_field_configuration_html', array($this, 'add_configuration_options'));
		
	}
	
	
	
	public function configure($id, $action = '') {
		global $wpdb, $directorypress_object;
		
		wp_enqueue_script('jquery-ui-sortable');
		//add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ) );
	
		if ($action == 'config') {
			$validation = new directorypress_form_validation();
			$validation->set_rules('selection_items[]', __('Options', 'directorypress-advanced-fields'), 'required');
			//$validation->set_rules('how_display_items', __('How to display Options', 'directorypress-advanced-fields'), 'required');
			//$validation->set_rules('check_icon_type', __('Select Icon Type', 'directorypress-advanced-fields'), 'required');
			$validation->set_rules('color_codes[]', __("Color code", 'directorypress-advanced-fields'), '');
			if ($validation->run()) {
				$result = $validation->result_array();
	
				$insert_update_args['selection_items'] = $result['selection_items[]'];
				//$insert_update_args['how_display_items'] = $result['how_display_items'];
				//$insert_update_args['check_icon_type'] = $result['check_icon_type'];
				$insert_update_args['color_codes'] = $result['color_codes[]'];
	
				$insert_update_args = apply_filters('directorypress_selection_items_update_args', $insert_update_args, $this, $result);
	
				if ($insert_update_args) {
					$wpdb->update($wpdb->directorypress_fields, array('options' => serialize($insert_update_args)), array('id' => $id), null, array('%d'));
				}
	
				directorypress_add_notification(__('updated successfully!', 'directorypress-advanced-fields'));
	
				do_action('directorypress_update_selection_items', $result['selection_items[]'], $this);
			} else {
				$this->selection_items = $validation->result_array('selection_items[]');
				directorypress_add_notification($validation->error_array(), 'error');
				$field = $this;
				include('_html/configuration.php');
			}
		} else {
			$field = $this;
			include('_html/configuration.php');
		}
	}
	public function build_field_options() {
		if (isset($this->options['selection_items'])) {
			$this->selection_items = $this->options['selection_items'];
		}
		//if (isset($this->options['how_display_items'])) {
			//$this->how_display_items = $this->options['how_display_items'];
		//}
		//if (isset($this->options['check_icon_type'])) {
			//$this->check_icon_type = $this->options['check_icon_type'];
		//}
		if (isset($this->options['color_codes'])) {
			$this->color_codes = $this->options['color_codes'];
		}
	}

	public function renderInput() {
		
		$field = $this;
		include('_html/input.php');
	}
	
	public function validate_field_values(&$errors, $data) {
		$field_index = 'directorypress-field-input-' . $this->id;

		$validation = new directorypress_form_validation();
		$rules = '';
		//if ($this->is_this_field_requirable() && $this->is_required)
			//$rules .= '|required';
		$validation->set_rules($field_index, $this->name);
		if (!$validation->run())
			$errors[] = implode("", $validation->error_array());
		elseif ($selected_item = $validation->result_array($field_index)) {
			if (!in_array($selected_item, array_keys($this->selection_items)))
				$errors[] = sprintf(__("This selection option index \"%d\" doesn't exist", 'directorypress-advanced-fields'), $selected_item);

			return $selected_item;
		}elseif ($this->is_this_field_requirable() && $this->is_required){
			$errors[] = sprintf(__('"%s" field is required', 'directorypress-advanced-fields'), $this->name);
		}
	}
	
	public function save_field_value($post_id, $validation_results) {
		return update_post_meta($post_id, '_field_' . $this->id, $validation_results);
		return true;
	}
	
	public function load_field_value($post_id) {
		$this->value = get_post_meta($post_id, '_field_' . $this->id, true);
		
		$this->value = apply_filters('directorypress_field_load', $this->value, $this, $post_id);
		return $this->value;
	}
	
	public function display_output($listing = null) {
		$field = $this;
		include('_html/output.php');
	}
	
	public function validate_csv_values($value, &$errors) {
		if ($value) {
			$output = array();
			foreach ((array) $value AS $key=>$selected_item) {
				if (array_key_exists($selected_item, $this->selection_items)) {
					$output[] = $selected_item;
					continue;
				}

				if (!in_array($selected_item, $this->selection_items))
					$errors[] = sprintf(__("This selection option \"%s\" doesn't exist", 'directorypress-advanced-fields'), $selected_item);
				else
					$output[] = array_search($selected_item, $this->selection_items);
			}
			return $output;
		} else 
			return '';
	}
	
	public function export_field_to_csv() {
		return implode(';', $this->value);
	}
	
	public function disaply_output_on_map($location, $listing) {
		$field = $this;
		include('_html/map.php');
	}
}
?>