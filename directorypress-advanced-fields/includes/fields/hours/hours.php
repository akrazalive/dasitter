<?php 

class directorypress_field_hours extends directorypress_field {
	public $hours_clock = 12;
	public $days;
	public $time_interval = 60;
	protected $can_be_required = false;
	protected $can_be_ordered = false;
	protected $is_configuration_page = true;
	protected $can_be_searched = false;
	protected $is_search_configuration_page = false;
	
	public function __construct() {
		$this->days = array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');
	}
	
	public function is_field_not_empty($listing) {
		if (array_filter($this->value))
			return true;
		else
			return false;
	}
	
	public function configure($id, $action = '') {
		global $wpdb, $directorypress_object;

		if ($action == 'config') {
			$validation = new directorypress_form_validation();
			$validation->set_rules('time_interval', __('Time Interval', 'directorypress-advanced-fields'), 'integer');
			if ($validation->run()) {
				$result = $validation->result_array();
				if ($wpdb->update($wpdb->directorypress_fields, array('options' => serialize(
						array(
							'time_interval' => $result['time_interval'],
						)
					)), array('id' => $id), null, array('%d'))) {
						directorypress_add_notification(__('Field configuration was updated successfully!', 'directorypress-advanced-fields'));
				}
			} else {
				$this->time_interval = $validation->result_array('time_interval');
				directorypress_add_notification($validation->error_array(), 'error');

				$field = $this;
				include('_html/configuration.php');
			}
		} else{
			directorypress_add_notification(__('Field configuration was not updated!', 'directorypress-advanced-fields'));
			$field = $this;
			include('_html/configuration.php');
		}
	}
	public function build_field_options() {
		
		if (isset($this->options['time_interval'])) {
			$this->time_interval = $this->options['time_interval'];
		}
		
	}
	
	public function days_order() {
		$week = array(intval(get_option('start_of_week')));
		while (count($week) < 7) {
			$day_num = $week[count($week)-1]+1;
			if ($day_num == 7) $day_num = 0;
			$week[] = $day_num;
		}
		foreach ($week AS $day_num)
			$days[$day_num] = $this->days[$day_num];
		
		$this->days_names = array(__('Sunday', 'directorypress-advanced-fields'), __('Monday', 'directorypress-advanced-fields'), __('Tuesday', 'directorypress-advanced-fields'), __('Wednesday', 'directorypress-advanced-fields'), __('Thursday', 'directorypress-advanced-fields'), __('Friday', 'directorypress-advanced-fields'), __('Saturday', 'directorypress-advanced-fields'));
		
		return $days;
	}

	public function renderInput() {
		$days = $this->days_order();
		$field = $this;
		include('_html/input.php');
	}
	
	public function validate_field_values(&$errors, $data) {
		$validation = new directorypress_form_validation();
		foreach ($this->days AS $day) {
			$validation->set_rules($day.'_opening_' . $this->id, $this->name);
			//$validation->set_rules($day.'_from_am_pm_' . $this->id, $this->name);
			$validation->set_rules($day.'_closing_' . $this->id, $this->name);
			//$validation->set_rules($day.'_to_am_pm_' . $this->id, $this->name);
			$validation->set_rules($day.'_off_' . $this->id, 'is_checked');
		}
		if (!$validation->run()) {
			$errors[] = implode("", $validation->error_array());
		}

		$value = array();
		
		foreach ($this->days AS $day) {
			if (!$validation->result_array($day.'_off_'.$this->id)) {
				$opening = $validation->result_array($day.'_opening_'.$this->id);
				$closing = $validation->result_array($day.'_closing_'.$this->id);
				
				$value[$day.'_opening'] = $opening;
				$value[$day.'_closing'] = $closing;
				
			} else {
				$value[$day.'_off'] = $validation->result_array($day.'_off_'.$this->id);
			}
		}
		
		return $value;
	}
	
	public function save_field_value($post_id, $validation_results) {
		return update_post_meta($post_id, '_field_' . $this->id, $validation_results);
	}
	
	public function load_field_value($post_id) {
		$value = get_post_meta($post_id, '_field_' . $this->id, true);
		foreach ($this->days AS $day) {
			foreach (array('_opening', '_closing', '_off') AS $opening_closing) {
				if (isset($value[$day.$opening_closing])) {
					$this->value[$day.$opening_closing] = $value[$day.$opening_closing];
				} else {
					$this->value[$day.$opening_closing] = '';
				}
			}
		}
		$this->value = apply_filters('directorypress_field_load', $this->value, $this, $post_id);
		return $this->value;
	}
	
	public function display_output($listing = null) {
		$field = $this;
		include('_html/output.php');
	}
	
	public function disaply_output_on_map($location, $listing) {
		if ($strings = $this->processStrings())
			return '<div class="directorypress-map-field-hours">' . implode('<br />', $this->processStrings()) . '</div>';
	}
	
	public function time_interval(){
		
		return $this->time_interval;
	}
	
	public function processStrings() {
		$days = $this->days_order();
		
		$this->days_names = array(__('Sunday', 'directorypress-advanced-fields'), __('Monday', 'directorypress-advanced-fields'), __('Tuesday', 'directorypress-advanced-fields'), __('Wednesday', 'directorypress-advanced-fields'), __('Thursday', 'directorypress-advanced-fields'), __('Friday', 'directorypress-advanced-fields'), __('Saturday', 'directorypress-advanced-fields'));
		$strings = array();
		
		foreach ($days AS $key=>$day) {
			if (($this->value[$day.'_opening'] || $this->value[$day.'_closing']) && ($this->value[$day.'_off'] == 0 || !isset($this->value[$day.'_off']))){
				$strings[] = '<strong>' . $this->days_names[$key] . '</strong><span>' . $this->value[$day.'_opening'] . ' - ' . $this->value[$day.'_closing'].'</span>';
			}elseif($this->value[$day.'_off'] == 1){
				$strings[] = '<strong>' . $this->days_names[$key] . '</strong><span>' . esc_html__('Closed', 'directorypress-advanced-fields') .'</span>';
			}elseif($this->value[$day.'_off'] == 2){
				$strings[] = '<strong>' . $this->days_names[$key] . '</strong><span>' . esc_html__('Open 24 hours', 'directorypress-advanced-fields') .'</span>';
			}
		}
		
		$strings = apply_filters('directorypress_field_hours_strings', $strings);
		
		return $strings;
	}
	
	public function status($listing = null, $time_string = false) {
		$days = $this->days_order();
		$timezone =  wp_timezone_string();
		$current_day = date('D');
		$timestamp = time();
		$time = new DateTime("now", new DateTimeZone($timezone)); //first argument "must" be a string
		$time->setTimestamp($timestamp); //adjust the object to correct timestamp
		$time_format =  $time->format('h:i A');
		$current_timestamp = strtotime($time_format); //get new timestamp
		
		foreach ($days AS $key=>$day) {
			$opening = strtotime($this->value[$day.'_opening']);
			$closing = strtotime($this->value[$day.'_closing']);
			if($day == $current_day){
				if ($time_string && ($this->value[$day.'_opening'] || $this->value[$day.'_closing'] || $this->value[$day.'_off'])){
					//$tstring =  '<span>' . (($this->value[$day.'_off']) ? __('Closed', 'directorypress-advanced-fields') : $this->value[$day.'_opening'] . ' - ' . $this->value[$day.'_closing']).'</span>';
					if($this->value[$day.'_off'] == 0){
						$tstring =  '<span>' . $this->value[$day.'_opening'] . ' - ' . $this->value[$day.'_closing'].'</span>';
					}elseif($this->value[$day.'_off'] == 1){
						$tstring =  '<span>' . __('day off', 'directorypress-advanced-fields') .'</span>';
					}elseif($this->value[$day.'_off'] == 2){
						$tstring =  '<span>' . __('24 hours', 'directorypress-advanced-fields') .'</span>';
					}
				}else{
					$tstring = '';
				}
				
				if(($current_timestamp > $opening && $current_timestamp < $closing) || $this->value[$day.'_off'] == 2){
					echo '<div class="open">'. esc_html__('Open', 'directorypress-advanced-fields'). $tstring .'</div>';
				}else{
					echo '<div class="closed">'. esc_html__('Closed', 'directorypress-advanced-fields'). $tstring .'</div>';
				}
			}
		}
		
		
	}
}