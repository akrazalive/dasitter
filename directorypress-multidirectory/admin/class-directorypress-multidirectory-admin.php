<?php
/**
 * @package    Directorypress_Multidirectory
 * @subpackage Directorypress_Multidirectory/admin
 * @author     DirectoryPress <team@directorypress.co>
 */
class Directorypress_Multidirectory_Admin {

	public function __construct() {
		global $directorypress_object;
		if(is_object( $directorypress_object)){
			$directorypress_object->directorytypes_manager = new DirectoryPress_ListingTypes_Admin;
		}
		
		add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'), 0);
	}
	
	public function enqueue_scripts() {
		wp_enqueue_script( 'dpmd-admin-js', DPMD_URL . 'assets/js/directorypress-multidirectory-admin.js', array( 'jquery' ), false, false );
	}

}
