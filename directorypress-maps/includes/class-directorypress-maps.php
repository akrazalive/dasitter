<?php

/**
 * @package    Directorypress_Maps
 * @subpackage Directorypress_Maps/includes
 * @author     DirectoryPress <team@directorypress.co>
 */
class Directorypress_Maps_Core {
	
	protected $loader;
	protected $plugin_name;
	protected $version;
	
	public function __construct() {
		if ( defined( 'DIRECTORYPRESS_MAPS_VERSION' ) ) {
			$this->version = DIRECTORYPRESS_MAPS_VERSION;
		} else {
			$this->version = '1.4.1';
		}
		$this->plugin_name = 'directorypress-maps';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}
	
	private function load_dependencies() {
		
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-directorypress-maps-loader.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-directorypress-maps-i18n.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-directorypress-maps-admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/functions.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/directorypress_maps.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/directorypress_gm_styles.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-geoname.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/shortcode/directorypress_map.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-directorypress-maps-public.php';
		//require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/license/license.php';
		
		$this->loader = new Directorypress_Maps_Loader();

	}
	
	private function set_locale() {

		$plugin_i18n = new Directorypress_Maps_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}
	
	private function define_admin_hooks() {

		$plugin_admin = new Directorypress_Maps_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

	}
	
	private function define_public_hooks() {

		$plugin_public = new Directorypress_Maps_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}
	
	public function run() {
		
		$this->loader->run();
		global $directorypress_object, $directorypress_shortcodes, $directorypress_shortcodes_init;
		$directorypress_shortcodes['directorypress-map'] = 'directorypress_map_handler';
		$directorypress_shortcodes_init['directorypress-map'] = 'directorypress_map_handler';
		add_shortcode('directorypress-map', array($directorypress_object, 'directorypress_shortcode_display'));
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
