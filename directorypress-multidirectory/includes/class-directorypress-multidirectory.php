<?php

/*
 * @package    Directorypress_Multidirectory
 * @subpackage Directorypress_Multidirectory/includes
 * @author     DirectoryPress <team@directorypress.co>
 */
class Directorypress_Multidirectory {
	
	protected $loader;
	protected $plugin_name;
	protected $version;
	public function __construct() {
		
		$this->version = DIRECTORYPRESS_MULTIDIRECTORY_VERSION;
		$this->plugin_name = 'directorypress-multidirectory';
		$this->load_dependencies();
		$this->set_locale();

	}

	private function load_dependencies() {
		
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-directorypress-multidirectory-loader.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-directorypress-multidirectory-i18n.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/general-function.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-directorypress-multidirectory-backend.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-directorypress-multidirectory-admin.php';
		
		$this->loader = new Directorypress_Multidirectory_Loader();
	}
	
	private function set_locale() {

		$plugin_i18n = new Directorypress_Multidirectory_i18n();
		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}
	
	public function directorypress_init_classes() {
		
		global $DIRECTORYPRESS_ADIMN_SETTINGS, $directorypress_object;
		if(is_object( $directorypress_object)){
			$directorypress_object->admin = new Directorypress_Multidirectory_Admin;
		}
	}
	
	public function run() {
		$this->directorypress_init_classes();
		$this->loader->run();
	}
	
	public function get_plugin_name() {
		return $this->plugin_name;
	}
	
	public function get_loader() {
		return $this->loader;
	}
	
	public function get_version() {
		return $this->version;
	}

}
