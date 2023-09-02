<?php

/**
 * @since      1.0.0
 * @package    Directorypress_Frontend_Messages
 * @subpackage Directorypress_Frontend_Messages/includes
 * @author     Designinvento <help@designinvento.net>
 */
class Directorypress_Frontend_Messages {
	protected $loader;
	protected $plugin_name;
	protected $version;
	
	public function __construct() {
		if ( defined( 'DIRECTORYPRESS_FRONTEND_MESSAGES_VERSION' ) ) {
			$this->version = DIRECTORYPRESS_FRONTEND_MESSAGES_VERSION;
		} else {
			$this->version = '5.4.1';
		}
		$this->plugin_name = 'directorypress-frontend-messages';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}
	
	private function load_dependencies() {
		
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-directorypress-frontend-messages-loader.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-directorypress-frontend-messages-i18n.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/functions.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-directorypress-frontend-messages-admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-directorypress-frontend-messages-public.php';

		$this->loader = new Directorypress_Frontend_Messages_Loader();

	}
	
	private function set_locale() {

		$plugin_i18n = new Directorypress_Frontend_Messages_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}
	
	private function define_admin_hooks() {

		$plugin_admin = new Directorypress_Frontend_Messages_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		//$this->loader->add_action( 'admin_init', $plugin_admin, 'actions_filters' );

	}
	
	private function define_public_hooks() {

		$plugin_public = new Directorypress_Frontend_Messages_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}
	public function run() {
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
