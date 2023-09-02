<?php

/**
 * @package    Directorypress_Maps
 * @subpackage Directorypress_Maps/admin
 * @author     DirectoryPress <team@directorypress.co>
 */
class Directorypress_Maps_Admin {

	
	private $plugin_name;
	private $version;
	
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		
		add_action('directorypress_after_address_settings', array($this, 'settings'), 10, 2);
	}
	
	public function enqueue_styles() {
		if (directorypress_is_listing_admin_edit_page() && directorypress_has_map()) {
			if (directorypress_map_type() == 'mapbox') {
				wp_register_style('dpm-mbgl', 'https://api.tiles.mapbox.com/mapbox-gl-js/' . DPMBV . '/mapbox-gl.css');
				wp_register_style('dpm-mbdraw', 'https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-draw/v1.0.9/mapbox-gl-draw.css');
				wp_register_style('dpm-mbdirections', 'https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-directions/v3.1.3/mapbox-gl-directions.css');
				
				wp_enqueue_style('dpm-mbgl');
				wp_enqueue_style('dpm-mbdraw');
				wp_enqueue_style('dpm-mbdirections');
			}
		}
	}
	
	public function enqueue_scripts() {
		//wp_enqueue_script('directorypress-terms-js');
		if (directorypress_has_map() && directorypress_is_listing_admin_edit_page()) {
			
			wp_enqueue_script('jquery-ui-selectmenu');
			wp_enqueue_script('jquery-ui-autocomplete');
			wp_enqueue_script('jquery-ui-dialog');
			if (directorypress_map_type() == 'mapbox') {
				
				wp_register_script('dpm-mbgl', 'https://api.tiles.mapbox.com/mapbox-gl-js/' . DPMBV . '/mapbox-gl.js');
				wp_register_script('dpm-mb', DIRECTORYPRESS_MAPS_ASSETS_URL . 'js/directorypress_mapboxgl.js');			
				wp_register_script('dpm-mbdraw', 'https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-draw/v1.0.9/mapbox-gl-draw.js');		
				wp_register_script('dpm-mbdirections', 'https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-directions/v3.1.3/mapbox-gl-directions.js');		
				wp_register_script('dpm-mblang', 'https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-language/v0.10.1/mapbox-gl-language.js');
				
				
				wp_enqueue_script('dpm-mbgl');
				wp_enqueue_script('dpm-mb');
				wp_enqueue_script('dpm-mbdraw');
				wp_enqueue_script('dpm-mbdirections');
				wp_enqueue_script('dpm-mblang');
			}else{
				//add_action('wp_print_scripts', array($this, 'dequeue_maps_googleapis'), 1000);
				wp_register_script('directorypress_google_maps', DIRECTORYPRESS_MAPS_ASSETS_URL . 'js/directorypress_google_maps.js', array('jquery'), DIRECTORYPRESS_VERSION, true);
				wp_enqueue_script('directorypress_google_maps');
			}
			
		}

	}
	
	public function settings($redux, $opt_name) {
		if ( ! class_exists( 'Redux' ) ) {
			return;
		}
		global $directorypress_google_maps_styles;
	
		if (is_array($directorypress_google_maps_styles)){
			$google_map_styles = array(array('value' => 'default', 'label' => 'Default style'));
				foreach ($directorypress_google_maps_styles AS $name=>$style){
					$google_map_styles[] = array('value' => $name, 'label' => $name);
				}	
					
			$new_google_map_styles = array();
			foreach($google_map_styles as $listItem) {
				$new_google_map_styles[$listItem['value']] = $listItem['label'];
			}
		}
		
		$country_codes = array();
		$directorypress_countrycodes = directorypress_countrycodes();
		foreach ($directorypress_countrycodes AS $country=>$code){
			$country_codes[] = array('value' => $code, 'label' => $country);
		}	
		$new_country_codes = array();
		foreach($country_codes as $newcode) {
			$new_country_codes[$newcode['value']] = $newcode['label'];
		}
		
		$redux::setSection( $opt_name, array(
			'title' => __( 'DirectoryPress Maps Addon', 'directorypress-maps' ),
			'id'    => 'map_settings_section',
			'icon'  => 'el el-map-marker'
		) );
		
		$redux::setSection( $opt_name, array(
			'id' => 'maps',
			'title' => __('General', 'directorypress-maps'),
			'subsection' => true,
			'fields' => array(
				array(
					'type' => 'text',
					'id' => 'directorypress_google_api_key',
					'title' => __('Google JavaScript API key', 'directorypress-maps'),
					'desc' => sprintf(__('get your Google API key <a href="%s" target="_blank">here</a>, following APIs must be enabled in the console: Google Maps JavaScript API, Google Static Maps API and Google Maps Directions API.', 'directorypress-maps'), 'https://code.google.com/apis/console'),
					'default' => '',
				),
				array(
					'type' => 'text',
					'id' => 'directorypress_google_api_key_server',
					'title' => __('Google server API key', 'directorypress-maps'),
					'desc' => sprintf(__('get your Google API key <a href="%s" target="_blank">here</a>, following APIs must be enabled in the console: Google Maps Geocoding API for radius search functionality.', 'directorypress-maps'), 'https://code.google.com/apis/console'),
					'default' => '',
				),
				apply_filters("directorypress_mapbox_api_option" , 'directorypress_mapbox_api'),
				array(
					'type' => 'switch',
					'id' => 'directorypress_map_on_index',
					'title' => __('Show Map On Main Directory Page?', 'directorypress-maps'),
					'default' => true,
				),
				array(
					'type' => 'switch',
					'id' => 'directorypress_map_on_excerpt',
					'title' => __('Show Map On Archive Page?', 'directorypress-maps'),
					'desc' => __('archive pages includes search result page, category page, location page and tags page', 'directorypress-maps'),
					'default' => true,
				),
				array(
					'type' => 'switch',
					'id' => 'map_on_single_listing_tab',
					'title' => __('Show map on single listing', 'directorypress-maps'),
					'desc' => __('Turn map on/Off at single listing page', 'directorypress-maps'),
					'default' => 1,
				),
				array(
					'type' => 'radio',
					'id' => 'directorypress_listings_map_position',
					'title' => __('Listings Map Position', 'directorypress-maps'),
					'default' => 'intab',
					'options' => array(
						'intab' => __('In Tabs', 'directorypress-maps'),
						'notab' => __('Out Side of Tabs', 'directorypress-maps'),
					),
				),
				array(
					'type' => 'radio',
					'id' => 'directorypress_map_markers_is_limit',
					'title' => __('How many map markers to display on the map', 'directorypress-maps'),
					'options' => array(
						'1' => __('The only map markers of visible listings will be displayed', 'directorypress-maps'),
						'0' => __('Display all map markers (lots of markers on one page may slow down page loading)', 'directorypress-maps'),
					),
					'default' => '1'
				),
				array(
					'type' => 'select',
					'id' => 'directorypress_address_autocomplete_code',
					'title' => __('Restriction of address fields for the default country (autocomplete and search)', 'directorypress-maps'),
					'options' => $new_country_codes,
					'default' => '0',
				),
				array(
					'type' => 'switch',
					'id' => 'directorypress_show_directions',
					'title' => __('Show directions panel for individual listing map?', 'directorypress-maps'),
					'default' => true,
				),
				array(
					'type' => 'radio',
					'id' => 'directorypress_directions_functionality',
					'title' => __('Directions functionality', 'directorypress-maps'),
					'options' => array(
						'builtin' =>__('Built-in routing', 'directorypress-maps'),
						'google' =>__('Link to Google Maps', 'directorypress-maps'),
					),
					'default' => 'builtin',
												
				),
				array(
					'type' => 'slider',
					'id' => 'directorypress_default_map_zoom',
					'title' => __('Default Google Maps zoom Level (for submission page)', 'directorypress-maps'),
					'min' => 1,
					'max' => 19,
					'default' => 11,
				),
				array(
					'type' => 'slider',
					'id' => 'directorypress_start_map_zoom',
					'title' => __('Default Google Maps zoom Level (for Archive pages)', 'directorypress-maps'),
					'min' => 1,
					'max' => 19,
					'default' => 11,
				),
				array(
					'type' => 'text',
					'id' => 'directorypress_start_address',
					'title' => __('Archive pages map starting address', 'directorypress-maps'),
					'default' => '',
				),
				array(
					'type' => 'text',
					'id' => 'directorypress_start_latitude',
					'title' => __('Archive pages map starting latitude', 'directorypress-maps'),
					'default' => '',
				),
				array(
					'type' => 'text',
					'id' => 'directorypress_start_longitude',
					'title' => __('Archive pages map starting longitude', 'directorypress-maps'),
					'default' => '',
				),
				apply_filters("directorypress_map_type_option" , 'directorypress_map_type'),
				
				array(
					'type' => 'select',
					'id' => 'directorypress_map_style',
					'title' => __('Google Maps style', 'directorypress-maps'),
					'options' => $new_google_map_styles,
					'default' => 'default',
				),
				apply_filters("directorypress_mapbox_styles_option" , 'directorypress_mapbox_styles'),
				array(
					'type' => 'text',
					'id' => 'directorypress_default_map_height',
					'title' => __('Default map height (in pixels)', 'directorypress-maps'),
					'default' => 450,
					'validation' => 'required|numeric',
				),
				array(
					'type' => 'switch',
					'id' => 'directorypress_enable_radius_search_cycle',
					'title' => __('Show cycle during radius search?', 'directorypress-maps'),
					'default' => false,
				),
				array(
					'type' => 'switch',
					'id' => 'directorypress_enable_clusters',
					'title' => __('Enable clusters of map markers?', 'directorypress-maps'),
					'default' => false,
				),
			),
		) );
		
		$redux::setSection( $opt_name, array(
			'id' => 'maps_controls',
			'title' => __('Controls', 'directorypress-maps'),
			'subsection' => true,
			'fields' => array(
			array(
					'type' => 'switch',
					'id' => 'directorypress_enable_draw_panel',
					'title' => __('Enable Draw Panel', 'directorypress-maps'),
					'desc' => __('Very important: MySQL version must be 5.6.1 and higher or MySQL server variable "thread stack" must be 256K and higher. Ask your host about it if "Draw Area" does not work.', 'directorypress-maps'),
					'default' => false,
				),
				array(
					'type' => 'switch',
					'id' => 'directorypress_enable_full_screen',
					'title' => __('Enable full screen button', 'directorypress-maps'),
					'default' => true,
				),
				array(
					'type' => 'switch',
					'id' => 'directorypress_enable_wheel_zoom',
					'title' => __('Enable zoom by mouse wheel', 'directorypress-maps'),
					'desc' => __('For desktops', 'directorypress-maps'),
					'default' => false,
				),
				array(
					'type' => 'switch',
					'id' => 'directorypress_enable_dragging_touchscreens',
					'title' => __('Enable map dragging on touch screen devices', 'directorypress-maps'),
					'default' => true,
				),
				array(
					'type' => 'switch',
					'id' => 'directorypress_center_map_onclick',
					'title' => __('Center map on marker click', 'directorypress-maps'),
					'default' => true,
				),
			),
		) );
		
		$redux::setSection( $opt_name, array(
			'id' => 'markers',
			'title' => __('Markers & Info Window', 'directorypress-maps'),
			'subsection' => true,
			'fields' => array(
				array(
					'type' => 'radio',
					'id' => 'directorypress_map_markers_type',
					'title' => __('Type of Map Markers', 'directorypress-maps'),
					'options' => array(
						'icons' => __('Font icons (recommended)', 'directorypress-maps'),
						'images' => __('PNG images', 'directorypress-maps'),
					),
					'default' => 'icons',
				),
				array(
					'type' => 'color',
					'id' => 'directorypress_default_marker_color',
					'title' => __('Default Map Marker color', 'directorypress-maps'),
					'default' => '#5580ff',
					'desc' => __('For Font Awesome icons.', 'directorypress-maps'),
				),
				array(
					'type' => 'text',
					'id' => 'directorypress_default_marker_icon',
					'title' => __('Default Map Marker icon'),
					'desc' => __('add font class e.g(fas fa-map-marker-alt), you can use fontawesome or any thirdparty icon from a plugin or theme', 'directorypress-maps'),
					'default' => 'fas fa-map-marker-alt',
				),
				
				
				
			),
		) );
	}

}
