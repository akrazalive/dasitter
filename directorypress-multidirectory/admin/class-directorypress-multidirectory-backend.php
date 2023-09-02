<?php
class DirectoryPress_ListingTypes_Admin {
	public function __construct() {
		//DirectoryPress_ListingTypes_Admin_init($this);
		add_action('admin_menu', array($this, 'menu'));
	}

	public function menu() {
			$this->menu_page_hook = add_submenu_page('directorypress-admin-panel',
				__('Directorytypes', 'directorypress-multidirectory'),
				__('Directorytypes', 'directorypress-multidirectory'),
				'administrator',
				'directorypress_directorytypes',
				array($this, 'directorypress_manage_directorytypes_page')
			);
	}

	public function directorypress_manage_directorytypes_page() {
		if (isset($_GET['action']) && $_GET['action'] == 'add') {
			$this->addOrEditDirectory();
		} elseif (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['directory_id'])) {
			$this->addOrEditDirectory($_GET['directory_id']);
		} elseif (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['directory_id'])) {
			$this->deleteDirectory($_GET['directory_id']);
		} else {
			$this->showDirectorytypesTable();
		}
	}
	
	public function showDirectorytypesTable() {
		global $directorypress_object;
		
		$directorytypes = $directorypress_object->directorytypes;

		$directory_list = $this->table($directorytypes);

		dpmd_renderTemplate('partials/directorypress-multidirectory-backend.php', array('directory_list' => $directory_list));
	}
	public function showUpdatedDirectorytypesTable() {
		global $directorypress_object;
		
		$directorytypes = $directorypress_object->directorytypes;

		$directory_list = $this->table($directorytypes);
		echo $directory_list;
		die();
	}
	public function configure($directory_id) {
		global $directorypress_object, $wpdb;
		//$prefix = $wpdb->prefix; 
		$directorytypes = $directorypress_object->directorytypes;
		$first_directory = $directorytypes->directorypress_get_base_directorytype();
		$directorytype = $directorytypes->directory_by_id($directory_id);
		if ($directory_id == $first_directory->id) {
				$shortcode = '[directorypress-main]';
			} else {
				$shortcode = '[directorypress-main id="' . $directory_id . '"]';
			}
			
			if ($directorytype->url) {
				$directory_url = sprintf('<a href="%s" target="_blank">%s</a>', $directorytype->url, $directorytype->url);
			} else {
				$directory_url = '<strong>' . __('Required page is missing!', 'directorypress-multidirectory') . '</strong>';
			}
			echo '<div class="directorypress-data-list">';
				echo '<label>'.__('Label', 'directorypress-multidirectory').'</label><span>'. $directorytype->name.'</span>';
			echo '</div>';
			echo '<div class="directorypress-data-list">';
				echo '<label>'.__('Singular Name', 'directorypress-multidirectory').'</label><span>'. $directorytype->single.'</span>';
			echo '</div>';
			echo '<div class="directorypress-data-list">';
				echo '<label>'.__('Plural Name', 'directorypress-multidirectory').'</label><span>'. $directorytype->plural.'</span>';
			echo '</div>';
			echo '<div class="directorypress-data-list">';
				echo '<label>'.__('Directory Slug', 'directorypress-multidirectory').'</label><span>'. $directorytype->listing_slug.'</span>';
			echo '</div>';
			echo '<div class="directorypress-data-list">';
				echo '<label>'.__('Category Slug', 'directorypress-multidirectory').'</label><span>'. $directorytype->category_slug.'</span>';
			echo '</div>';
			echo '<div class="directorypress-data-list">';
				echo '<label>'.__('Location Slug', 'directorypress-multidirectory').'</label><span>'. $directorytype->location_slug.'</span>';
			echo '</div>';
			echo '<div class="directorypress-data-list">';
				echo '<label>'.__('Tags Slug', 'directorypress-multidirectory').'</label><span>'. $directorytype->tag_slug.'</span>';
			echo '</div>';
			echo '<div class="directorypress-data-list">';
				echo '<label>'.__('Directory URL', 'directorypress-multidirectory').'</label><span>'. $directory_url.'</span>';
			echo '</div>';
			echo '<div class="directorypress-data-list">';
				echo '<label>'.__('Directory Shortcode', 'directorypress-multidirectory').'</label><span>'. $shortcode.'</span>';
			echo '</div>';
			
			//echo $this->edit_delete_links($directory_id);
		
		die();
	}
	public function table($directorytypes) {
		global $directorypress_object, $wpdb;
		$items_array = array();
		$directorytypes = $directorypress_object->directorytypes;
		$output = '';
		$output .= '<div class="dp-list-section">';
			foreach ($directorytypes->directorypress_array_of_directorytypes as $id=>$directorytype) {
				$output .= '<div class="row dp-list-row">';
					$output .= '<div class="col-md-10 txt-left dp-list-item">'.$directorytype->name.'</div>';
					$output .= '<div class="col-md-2 text-right dp-list-item"><a class="directory_configure" href="#" data-toggle="modal" data-target="#directory_configure" data-id="'.$directorytype->id.'"><i class="fas fa-cog"></i>'.__('Configure', 'directorypress-multidirectory').'</a></div>';
				$output .= '</div>';
			}
		$output .= '</div>';
			
		return $output;
	}
	public function edit_delete_links($id) {
		global $directorypress_object;
		//$prefix = $wpdb->prefix; 
		$directorytypes = $directorypress_object->directorytypes;
		$output = '';
		$output .= '<div class="edit"><a href="#" data-id="'.$id.'">' . __('Edit', 'directorypress-multidirectory') . '</a></div>';
		if ($id != $directorypress_object->directorytypes->directorypress_get_base_directorytype()->id) {
			$output .='<div class="delete"><a href="#" data-toggle="modal" data-dismiss="modal" data-id="'.$id.'" data-target="#delete_directory">' . __('Delete', 'directorypress-multidirectory') . '</a></div>';
		}
			
		return $output;
	}
	public function addOrEditDirectory($directory_id = null, $submit = '') {
		global $directorypress_object;

		$directorytypes = $directorypress_object->directorytypes;
		
		if (!$directorytype = $directorytypes->directory_by_id($directory_id)){
			$directorytype = new directorypress_directorytype();
		}

		if ($submit == 'submit') {
			$validation = new directorypress_form_validation();
			$validation->set_rules('name', __('Directory name', 'directorypress-multidirectory'), 'required');
			$validation->set_rules('single', __('Single form', 'directorypress-multidirectory'), 'required');
			$validation->set_rules('plural', __('Plural form', 'directorypress-multidirectory'), 'required');
			$validation->set_rules('listing_slug', __('Listing slug', 'directorypress-multidirectory'), 'alpha_dash');
			$validation->set_rules('category_slug', __('Category slug', 'directorypress-multidirectory'), 'alpha_dash');
			$validation->set_rules('location_slug', __('Location slug', 'directorypress-multidirectory'), 'alpha_dash');
			$validation->set_rules('tag_slug', __('Tag slug', 'directorypress-multidirectory'), 'alpha_dash');
			$validation->set_rules('categories', __('Assigned categories', 'directorypress-multidirectory'));
			$validation->set_rules('locations', __('Assigned locations', 'directorypress-multidirectory'));
			$validation->set_rules('packages', __('Levels', 'directorypress-multidirectory'));
			apply_filters('directorypress_directory_validation', $validation);
		
			if ($validation->run() && $this->checkSlugs($validation->result_array())) {
				if ($directorytype->id) {
					if ($directorytypes->saveDirectoryFromArray($directory_id, $validation->result_array())) {
						directorypress_add_notification(__('Directory was updated successfully!', 'directorypress-multidirectory'));
					}
				} else {
					if ($directorytypes->createDirectoryFromArray($validation->result_array())) {
						directorypress_add_notification(__('Directory was created successfully!', 'directorypress-multidirectory'));
					}
				}
			} else {
				$directorytype->directorypress_create_directorytype_from_array_values($validation->result_array());
				directorypress_add_notification($validation->error_array(), 'error');
				dpmd_renderTemplate('partials/directorypress-create.php', array('directorytype' => $directorytype, 'directory_id' => $directory_id));
			}
		} else {
			dpmd_renderTemplate('partials/directorypress-create.php', array('directorytype' => $directorytype, 'directory_id' => $directory_id));
		}
	}
	
	public function deleteDirectory($id, $new_id, $action ) {
		global $directorypress_object;

		$directorytypes = $directorypress_object->directorytypes;
		if ($directorytype = $directorytypes->directory_by_id($id)) {
			if ($action == 'delete') {
				if ($directorytypes->deleteDirectory($id, $new_id))
					directorypress_add_notification(__('Directory was deleted successfully!', 'directorypress-multidirectory'));
			} else {
				$new_directory = '';
				$warning = sprintf(__('Are you sure you want delete "%s" directory?', 'directorypress-multidirectory'), $directorytype->name);
				$warning .= '<br />' . __('Existing listings will be moved to directory:', 'directorypress-multidirectory');
				foreach ($directorypress_object->directorytypes->directorypress_array_of_directorytypes AS $directorytype) {
					if ($directorytype->id != $id)
						$new_directory .= '<div class="directory_list"><input type="radio" name="new_directory" value="' . $directorytype->id . '" ' . checked($directorytype->id, $directorypress_object->directorytypes->directorypress_get_base_directorytype()->id, false) . ' />' . $directorytype->name . '</div>';
				}
				dpmd_renderTemplate('partials/delete.php', array('warning' => $warning, 'new_directory' => $new_directory, 'item_name' => $directorytype->name));
			}
		} else{
			$this->showLevelsTable();
		}
	}
	
	public function checkSlugs($validation_results) {
		global $directorypress_object;
		
		$slugs_to_check = array(
				$validation_results['listing_slug'],
				$validation_results['category_slug'],
				$validation_results['location_slug'],
				$validation_results['tag_slug'],
		);
		foreach ($directorypress_object->directorypress_all_archive_pages AS $page) {
			if (in_array($page['slug'], $slugs_to_check)) {
				directorypress_add_notification(__('One or several slugs equal to the slug of directorytype page! This may cause problems.', 'directorypress-multidirectory'), 'error');
				return false;
			}
		}
		return true;
	}
}