<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link        https://directorypress.co/
 * @since      1.0.0
 *
 * @package    Directorypress_Maps
 * @subpackage Directorypress_Maps/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Directorypress_Maps
 * @subpackage Directorypress_Maps/public
 * @author     DirectoryPress <team@directorypress.co>
 */
class Directorypress_Maps_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		
		//add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'), 0);
		//add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'), 0);
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_register_style('dpm-styles', DIRECTORYPRESS_MAPS_ASSETS_URL. 'css/map.css');
		wp_register_style('dpm-mbgl-css', 'https://api.tiles.mapbox.com/mapbox-gl-js/' . DPMBV . '/mapbox-gl.css');
		wp_register_style('dpm-mbdraw-css', 'https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-draw/v1.0.9/mapbox-gl-draw.css');
		wp_register_style('dpm-mbdirections-css', 'https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-directions/v3.1.3/mapbox-gl-directions.css');
		
		if (directorypress_map_type() == 'mapbox') {		
			wp_enqueue_style('dpm-mbgl-css');
			wp_enqueue_style('dpm-mbdraw-css');
			wp_enqueue_style('dpm-mbdirections-css');
		}
		wp_enqueue_style('dpm-styles');
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		if (directorypress_has_map()) {
			if (directorypress_map_type() == 'mapbox') {
				wp_register_script('dpm-mbgl-js', 'https://api.tiles.mapbox.com/mapbox-gl-js/' . DPMBV . '/mapbox-gl.js');
				wp_register_script('dpm-mb-js', DIRECTORYPRESS_MAPS_ASSETS_URL . 'js/directorypress_mapboxgl.js', array('jquery'), DIRECTORYPRESS_MAPS_VERSION, true);			
				wp_register_script('dpm-mbdraw-js', 'https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-draw/v1.0.9/mapbox-gl-draw.js');		
				wp_register_script('dpm-mbdirections-js', 'https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-directions/v3.1.3/mapbox-gl-directions.js');		
				wp_register_script('dpm-mblang-js', 'https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-language/v0.10.1/mapbox-gl-language.js');
				
				wp_enqueue_script('dpm-mbgl-js');
				wp_enqueue_script('dpm-mb-js');
				wp_enqueue_script('dpm-mbdraw-js');
				wp_enqueue_script('dpm-mbdirections-js');
				wp_enqueue_script('dpm-mblang-js');
			}else{
				//add_action('wp_print_scripts', array($this, 'dequeue_maps_googleapis'), 1000);
				wp_register_script('directorypress_google_maps', DIRECTORYPRESS_MAPS_ASSETS_URL . 'js/directorypress_google_maps.js', array('jquery'), DIRECTORYPRESS_VERSION, true);
				wp_enqueue_script('directorypress_google_maps');
			}
			
		}
	}
}
