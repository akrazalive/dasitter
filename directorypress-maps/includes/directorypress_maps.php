<?php

class directorypress_maps {
	public $args;
	public $directorytypes;
	public $directorypress_handler;
	public $map_id;
	public $map_zoom;
	public $listings_array = array();
	public $locations_array = array();
	public $locations_option_array = array();

	public static $map_fields;

	public function __construct($args = array(), $directorypress_handler = 'directorypress_listings_handler', $directorytypes = null) {
		global $directorypress_object;
		
		$this->args = $args;
		$this->handler = $directorypress_handler;

		if (is_null($directorytypes)) {
			$this->directorytypes = array($directorypress_object->current_directorytype->id);
		} elseif (!empty($directorytypes)) {
			if ($directorytypes_ids = array_filter(explode(',', $directorytypes), 'trim')) {
				$this->directorytypes = $directorytypes_ids;
			}
		}
	}
	
	public function set_unique_id($unique_id) {
		$this->map_id = $unique_id;
	}

	public function collect_locations($listing) {
		global $directorypress_object, $directorypress_address_locations, $directorypress_tax_terms_locations, $DIRECTORYPRESS_ADIMN_SETTINGS;;

		if (count($listing->locations) == 1)
			$this->map_zoom = $listing->map_zoom;

		foreach ($listing->locations AS $location) {
			if ((!$directorypress_address_locations || in_array($location->id, $directorypress_address_locations)) && (!$directorypress_tax_terms_locations || in_array($location->selected_location, $directorypress_tax_terms_locations))) {
				if (($location->map_coords_1 && $location->map_coords_1 != '0.000000') || ($location->map_coords_2 && $location->map_coords_2 != '0.000000')) {
						$logo_image = '';
						$width = 250;
						$height = 150;
						if ($listing->logo_image) {
							
							
							$image_src_array = wp_get_attachment_image_src($listing->logo_image, 'full');
							$image_src = $image_src_array[0];
							$param = array(
								'width' => $width,
								'height' =>$height,
								'crop' => true
							);
							//$src = wp_get_attachment_image_src($listing->logo_image, array(,));
							$logo_image = '<img alt="'.$listing->title().'" src="'. bfi_thumb($image_src, $param).'" width="'.$width.'" height="'.$height.'" />';
							//$logo_image = $src[0];
						} elseif ($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_enable_nologo'] && $DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_nologo_url']) {
							$image_src = $DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_nologo_url']['url'];
							$param = array(
								'width' => $width,
								'height' => $height,
								'crop' => true
							);
							$logo_image = '<img alt="'.$listing->title().'" src="'. bfi_thumb($image_src, $param).'" width="'.$width.'" height="'.$height.'" />';
							//$logo_image = $DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_nologo_url'];
						}
					
	
	

					$listing_link = get_permalink($listing->post->ID);
	
					if ($directorypress_object->fields->get_map_fields())
						$fields_output = $listing->set_directorypress_fields_for_map($directorypress_object->fields->get_map_fields(), $location);
					else 
						$fields_output = '';
	
					$this->listings_array[] = $listing;
					$this->locations_array[] = $location;
					$this->locations_option_array[] = array(
							$location->id,
							$location->map_coords_1,
							$location->map_coords_2,
							$location->map_icon_file,
							$location->map_icon_color,
							$listing->map_zoom,
							$listing->title(),
							$logo_image,
							$listing_link,
							$fields_output,
							'post-' . $listing->post->ID,
							1,
					);
				}
			}
		}

		if ($this->locations_option_array)
			return true;
		else
			return false;
	}
	
	public function collect_locationsForAjax($listing) {	
		global $directorypress_address_locations, $directorypress_tax_terms_locations;

		foreach ($listing->locations AS $location) {
			if ((!$directorypress_address_locations || in_array($location->id, $directorypress_address_locations))  && (!$directorypress_tax_terms_locations || in_array($location->selected_location, $directorypress_tax_terms_locations))) {
				if (($location->map_coords_1 && $location->map_coords_1 != '0.000000') || ($location->map_coords_2 && $location->map_coords_2 != '0.000000')) {
					$this->listings_array[] = $listing;
					$this->locations_array[] = $location;
					$this->locations_option_array[] = array(
							$location->id,
							$location->map_coords_1,
							$location->map_coords_2,
							$location->map_icon_file,
							$location->map_icon_color,
							null,
							null,
							null,
							null,
							null,
							null,
							null,
					);
				}
			}
		}
		if ($this->locations_option_array)
			return true;
		else
			return false;
	}
	
	public function buildListingsContent($show_directions_button = true, $show_readmore_button = true) {
		$out = '';
		foreach ($this->locations_array AS $key=>$location) {
			$listing = $this->listings_array[$key];
			$listing->set_directorypress_fields();
			//if(get_query_var('location-directorypress')){
				$out .= directorypress_display_template('partials/archive/location-archive.php', array('listing' => $listing, 'location' => $location, 'show_directions_button' => $show_directions_button, 'show_readmore_button' => $show_readmore_button), true);
			//}
		}
		return $out;
	}
	
	public function buildStaticMap() {
		global $DIRECTORYPRESS_ADIMN_SETTINGS;
		if (directorypress_map_type() == 'google') {
			$html = '<img src="//maps.googleapis.com/maps/api/staticmap?size=795x350&';
			foreach ($this->locations_array  AS $location) {
				if ($location->map_coords_1 != 0 && $location->map_coords_2 != 0) {
					$html .= 'markers=';
					if (DIRECTORYPRESS_MAP_ICONS_URL && $location->map_icon_file) {
						$html .= 'icon:' . DIRECTORYPRESS_MAP_ICONS_URL . 'icons/' . urlencode($location->map_icon_file) . '%7C';
					}
				}
				$html .= $location->map_coords_1 . ',' . $location->map_coords_2 . '&';
			}
			if ($this->map_zoom) {
				$html .= 'zoom=' . $this->map_zoom;
			}
			if ($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_google_api_key']) {
				$html .= '&key='.$DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_google_api_key'];
			}
			$html .= '" />';
		} elseif (directorypress_map_type() == 'mapbox') {
			$html = '';
			if ($this->map_zoom) {
				$zoom = $this->map_zoom;
			} else {
				$zoom = 10;
			}
			foreach ($this->locations_array  AS $location) {
				$html .= '<address>' . $location->get_full_address(false) . '</address>';
				$html .= '<img src="//api.mapbox.com/styles/v1/mapbox/' . directorypress_map_style_selected() . '/static/';
				if ($location->map_coords_1 != 0 && $location->map_coords_2 != 0) {
					$html .= 'pin-l+ea3a83(' . $location->map_coords_2 . ',' . $location->map_coords_1 . ')/' . $location->map_coords_2 . ',' . $location->map_coords_1 . ',' . $zoom . '/';
				}
				$html .= '795x350?access_token=' . $DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_mapbox_api_key'] . '" /><br /><br />';
			}
		}
		return $html;
	}

	public function display($show_directions = true, $static_image = false, $enable_radius_circle = true, $enable_clusters = true, $show_summary_button = true, $show_readmore_button = true, $width = false, $height = false, $has_sticky_scroll = false, $has_sticky_scroll_toppadding = 10, $map_style_name = '', $search_form = false, $draw_panel = false, $custom_home = false, $enable_full_screen = true, $enable_wheel_zoom = true, $enable_dragging_touchscreens = true, $center_map_onclick = false) {
		//if ($this->locations_option_array || $this->is_ajax_markers_management()) {
			$locations_options = json_encode($this->locations_option_array);
			$map_args = json_encode($this->args);
			dpms_display_template('partials/map.php',
					array(
							'locations_options' => $locations_options,
							'locations_array' => $this->locations_array,
							'show_directions' => $show_directions,
							'static_image' => $static_image,
							'enable_radius_circle' => $enable_radius_circle,
							'enable_clusters' => $enable_clusters,
							'map_zoom' => $this->map_zoom,
							'show_summary_button' => $show_summary_button,
							'show_readmore_button' => $show_readmore_button,
							'map_style' => directorypress_map_style_selected($map_style_name),
							'search_form' => $search_form,
							'draw_panel' => $draw_panel,
							'custom_home' => $custom_home,
							'width' => $width,
							'height' => $height,
							'has_sticky_scroll' => $has_sticky_scroll,
							'has_sticky_scroll_toppadding' => $has_sticky_scroll_toppadding,
							'enable_full_screen' => $enable_full_screen,
							'enable_wheel_zoom' => $enable_wheel_zoom,
							'enable_dragging_touchscreens' => $enable_dragging_touchscreens,
							'center_map_onclick' => $center_map_onclick,
							'directorytypes' => $this->directorytypes,
							'handler' => $this->handler,
							'map_object' => $this,
							'map_id' => $this->map_id,
							//'listings_content' => $this->buildListingsContent(),
							'map_args' => $map_args,
							'args' => $this->args
					));
			wp_enqueue_script('maps_infobox');
		//}
	}
	
	public function is_ajax_markers_management() {
		if (isset($this->args['ajax_loading']) && $this->args['ajax_loading'] && ((isset($this->args['start_address']) && $this->args['start_address']) || ((isset($this->args['start_latitude']) && $this->args['start_latitude']) && (isset($this->args['start_longitude']) && $this->args['start_longitude']))))
			return true;
		else
			return false;
	}
}


?>