<?php 

class directorypress_field_checkbox extends directorypress_field_select {
	public $how_display_items = 'checked';
	public $value = array();
	public $check_icon_type;
	public $icon_selection_items = array();
	protected $can_be_searched = true;
	protected $is_search_configuration_page = true;
	
	public function __construct() {
		parent::__construct();
		
		add_action('directorypress_select_field_configuration_html', array($this, 'add_configuration_options'));
	}
	
	public function add_configuration_options($field) {
		if (is_a($field, 'directorypress_field_checkbox')) {
			echo '
				<div class="checkbox_how_to_show">
					<div class="item-label">
						<label>' . __('How to display items', 'directorypress-advanced-fields') . '</label>
					</div>
					<div class="row field-holder">
						<div class="col-md-6">
							<div>' . __('All items with checked/unchecked marks', 'directorypress-advanced-fields') . '</div>	
							<div>
								<label class="switch">
									<input name="how_display_items" type="radio" value="all" ' . checked($this->how_display_items, 'all', true) . ' />
									<span class="slider"></span>
								</label>
							</div>
						</div>
						<div class="col-md-6">
							<div>' . __('Only checked items', 'directorypress-advanced-fields') . '</div>	
							<div>
								<label class="switch">
									<input name="how_display_items" type="radio" value="checked" ' . checked($this->how_display_items, 'checked', false) . ' />
									<span class="slider"></span>
								</label>
							</div>
						</div>
					</div>
				</div>
				<div class="check_icon_type">
					<div class="item-label">
						<label>' . __('Select icon type', 'directorypress-advanced-fields') . '</label>
					</div>
					<div class="row field-holder">
						<div class="col-md-6">
							<div>' . __('default check icon ', 'directorypress-advanced-fields') . '</div>	
							<div>
								<label class="switch">
									<input name="check_icon_type" type="radio" value="default" ' . checked($this->check_icon_type, 'default', true) . ' />
									<span class="slider"></span>
								</label>
							</div>
						</div>
						<div class="col-md-6">
							<div>' . __('Custom icons', 'directorypress-advanced-fields') . '</div>	
							<div>
								<label class="switch">
									<input name="check_icon_type" type="radio" value="custom_icon" ' . checked($this->check_icon_type, 'custom_icon', false) . ' />
									<span class="slider"></span>
								</label>
							</div>
						</div>
					</div>
				</div>';
		}
	}
	
	
	
	public function configure($id, $action = '') {
		global $wpdb, $directorypress_object;
		
		wp_enqueue_script('jquery-ui-sortable');
	
		if ($action == 'config') {
			$validation = new directorypress_form_validation();
			$validation->set_rules('selection_items[]', __('Options', 'directorypress-advanced-fields'), 'required');
			$validation->set_rules('how_display_items', __('How to display Options', 'directorypress-advanced-fields'), 'required');
			$validation->set_rules('check_icon_type', __('Select Icon Type', 'directorypress-advanced-fields'), 'required');
			$validation->set_rules('icon_selection_items[]', __("Option's Icon", 'directorypress-advanced-fields'), '');
			if ($validation->run()) {
				$result = $validation->result_array();
	
				$insert_update_args['selection_items'] = $result['selection_items[]'];
				$insert_update_args['how_display_items'] = $result['how_display_items'];
				$insert_update_args['check_icon_type'] = $result['check_icon_type'];
				$insert_update_args['icon_selection_items'] = $result['icon_selection_items[]'];
	
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
		if (isset($this->options['how_display_items'])) {
			$this->how_display_items = $this->options['how_display_items'];
		}
		if (isset($this->options['check_icon_type'])) {
			$this->check_icon_type = $this->options['check_icon_type'];
		}
		if (isset($this->options['icon_selection_items'])) {
			$this->icon_selection_items = $this->options['icon_selection_items'];
		}
	}

	public function renderInput() {
		
		$field = $this;
		include('_html/input.php');
	}
	
	public function validate_field_values(&$errors, $data) {
		$field_index = 'directorypress-field-input-' . $this->id . '[]';

		$validation = new directorypress_form_validation();
		$validation->set_rules($field_index, $this->name);
		if (!$validation->run())
			$errors[] = implode("", $validation->error_array());
		elseif ($selected_items_array = $validation->result_array($field_index)) {
			foreach ($selected_items_array AS $selected_item) {
				if (!in_array($selected_item, array_keys($this->selection_items)))
					$errors[] = sprintf(__("This selection option index \"%d\" doesn't exist", 'directorypress-advanced-fields'), $selected_item);
			}
	
			return $selected_items_array;
		} elseif ($this->is_this_field_requirable() && $this->is_required)
			$errors[] = sprintf(__('At least one option must be selected in "%s" content field', 'directorypress-advanced-fields'), $this->name);
	}
	
	public function save_field_value($post_id, $validation_results) {
		delete_post_meta($post_id, '_field_' . $this->id);
		if ($validation_results && is_array($validation_results)) {
			foreach ($validation_results AS $value)
				add_post_meta($post_id, '_field_' . $this->id, $value);
		}
		return true;
	}
	
	public function load_field_value($post_id) {
		if (!($this->value = get_post_meta($post_id, '_field_' . $this->id)) || $this->value[0] == '')
			$this->value = array();
		else {
			$result = array();
			foreach ($this->selection_items AS $key=>$value) {
				if (array_search($key, $this->value) !== FALSE)
					$result[] = $key;
			}
			$this->value = $result;
		}
		
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