<?php 

class directorypress_field_radio extends directorypress_field_select {
	protected $can_be_searched = true;
	protected $is_search_configuration_page = true;

	public function renderInput() {
		$field = $this;
		include('_html/input.php');
	}
}
?>