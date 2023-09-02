<?php
if( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
class directorypress_manage_directorytypes_table extends WP_List_Table {

	public function __construct() {
		parent::__construct(array(
				'singular' => __('directorytype', 'directorypress-multidirectory'),
				'plural' => __('directorytypes', 'directorypress-multidirectory'),
				'ajax' => false
		));
	}

	public function get_columns($directorytypes = array()) {
		$columns = array(
				'id' => __('ID', 'directorypress-multidirectory'),
				'directory_name' => __('Name', 'directorypress-multidirectory'),
				'configure' => __('Configure', 'directorypress-multidirectory'),
				//'shortcode' => __('Shortcode', 'directorypress-multidirectory'),
				//'page' => __('Page', 'directorypress-multidirectory'),
				//'listing_slug' => __('Listing slug', 'directorypress-multidirectory'),
				//'category_slug' => __('Category slug', 'directorypress-multidirectory'),
				//'location_slug' => __('Location slug', 'directorypress-multidirectory'),
				//'tag_slug' => __('Tag slug', 'directorypress-multidirectory'),
		);
		$columns = apply_filters('directorypress_directory_table_header', $columns, $directorytypes);

		return $columns;
	}
	
	public function getItems($directorytypes) {
		$items_array = array();
		$first_directory = $directorytypes->directorypress_get_base_directorytype();
		foreach ($directorytypes->directorypress_array_of_directorytypes as $id=>$directorytype) {
			if ($id == $first_directory->id) {
				$shortcode = '[directorypress-main]';
			} else {
				$shortcode = '[directorypress-main id="' . $directorytype->id . '"]';
			}
			
			if ($directorytype->url) {
				$directory_url = sprintf('<a href="%s" target="_blank">%s</a>', $directorytype->url, $directorytype->url);
			} else {
				$directory_url = '<strong>' . __('Required page is missing!', 'directorypress-multidirectory') . '</strong>';
			}
			$configure = '<a class="directory_configure" href="#" data-toggle="modal" data-target="#directory_configure" data-id="'.$directorytype->id.'">'.__('Configure', 'directorypress-multidirectory').'</a>';
			
			$items_array[$id] = array(
					'id' => $directorytype->id,
					'directory_name' => $directorytype->name,
					'configure' => $configure,
					//'shortcode' => $shortcode,
					//'page' => $directory_url,
				//	'listing_slug' => $directorytype->listing_slug,
					//'category_slug' => $directorytype->category_slug,
				//	'location_slug' => $directorytype->location_slug,
				//	'tag_slug' => $directorytype->tag_slug,
			);

			$items_array[$id] = apply_filters('directorypress_directory_table_row', $items_array[$id], $directorytype);
		}
		return $items_array;
	}
	
	public function prepareItems($directorytypes) {
		$this->_column_headers = array($this->get_columns($directorytypes), array(), array());
		
		$this->items = $this->getItems($directorytypes);
	}
	
	public function column_directory_name($item) {
		global $directorypress_object;

		$actions = array(
				'edit' => '<a href="#" data-toggle="modal" data-id="'.$item['id'].'" data-target="#edit_directory">' . __('Edit', 'directorypress-multidirectory') . '</a>',
				'delete' => '<a href="#" data-toggle="modal" data-id="'.$item['id'].'" data-target="#delete_directory">' . __('Delete', 'directorypress-multidirectory') . '</a>',
		);
		
		if ($item['id'] == $directorypress_object->directorytypes->directorypress_get_base_directorytype()->id) {
			unset($actions['delete']);
		}
		
		return sprintf('%1$s %2$s', sprintf('<a href="?page=%s&action=%s&directory_id=%d">' . $item['directory_name'] . '</a>', $_GET['page'], 'edit', $item['id']), $this->row_actions($actions));
	}

	public function column_default($item, $column_name) {
		switch($column_name) {
			default:
				return $item[$column_name];
		}
	}
	
	function no_items() {
		__('No directorytypes found.', 'directorypress-multidirectory');
	}
}

?>