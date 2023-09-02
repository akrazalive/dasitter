<?php 

class directorypress_packages_table_handler extends directorypress_public {
	public $packages = array();
	public $template_args = array();

	public function init($args = array()) {
		global $directorypress_object;

		parent::init($args);
		
		$shortcode_atts = array_merge(array(
				'show_period' => 1,
				'show_has_sticky' => 1,
				'show_has_featured' => 1,
				'show_categories' => 1,
				'show_locations' => 1,
				'show_maps' => 1,
				'show_images' => 1,
				'show_videos' => 1,
				'columns_same_height' => 1,
				'columns' => 3,
				'packages' => null,
				'directorytype' => null,
		), $args);
		
		$this->args = $shortcode_atts;
		
		if ($this->args['directorytype']) {
			$directorytype = $directorypress_object->directorytypes->directory_by_id($this->args['directorytype']);
		} else {
			$directorytype = $directorypress_object->current_directorytype;
		}
		$this->template_args['directorytype'] = $directorytype;

		$this->packages = $directorypress_object->packages->packages_array;
		if ($this->args['packages']) {
			$packages_ids = array_filter(array_map('trim', explode(',', $this->args['packages'])));
			$this->packages = array_intersect_key($directorypress_object->packages->packages_array, array_flip($packages_ids));
		} elseif ($directorytype->packages) {
			$this->packages = array_intersect_key($directorypress_object->packages->packages_array, array_flip($directorytype->packages));
		}
		
		$this->template = array(DPFL_TEMPLATES_PATH, 'create_advert_packages.tpl.php');

		apply_filters('directorypress_public_construct', $this);
	}

	public function display() {
		if ($this->packages) {
			$output =  dpfl_renderTemplate($this->template, array_merge(array('public_handler' => $this), $this->template_args), true);
			wp_reset_postdata();
	
			return $output;
		}
	}
}

?>