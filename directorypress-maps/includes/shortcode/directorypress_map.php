<?php 

class directorypress_map_handler extends directorypress_public {
	public $directorypress_client = 'directorypress_listings_handler';

	public function init($args = array()) {
		
		global $directorypress_object, $DIRECTORYPRESS_ADIMN_SETTINGS;
		
		parent::init($args);

		$shortcode_atts = array_merge(array(
				'custom_home' => 0,
				'directorytypes' => '',
				'num' => -1,
				'map_markers_is_limit' => (directorypress_has_map() && !empty($args['custom_home'])) ? (int)$DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_map_markers_is_limit'] : 1, // How many map markers to display on the map (when listings shortcode is connected with map by unique string)
				'width' => '',
				'height' => !empty($args['custom_home']) ? (int)$DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_default_map_height'] : 400,
				'radius_circle' => 0,
				'clusters' => !empty($args['custom_home']) ? (int)$DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_enable_clusters'] : 0,
				'has_sticky_scroll' => 0,
				'has_sticky_scroll_toppadding' => 0,
				'show_summary_button' => 0,
				'show_readmore_button' => 1,
				'has_sticky_has_featured' => 0,
				'ajax_loading' => 0,
				'ajax_markers_loading' => 1,
				'geolocation' => 1,
				'address' => '',
				'radius' => 0,
				'start_address' => '',
				'start_latitude' => '',
				'start_longitude' => '',
				'start_zoom' => (directorypress_has_map() && !empty($args['custom_home'])) ? (int)$DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_default_map_zoom'] : 0,
				'map_style' => !empty($args['custom_home']) ? (int)directorypress_map_style_selected() : 'default',
				'include_categories_children' => 0,
				'include_get_params' => 1,
				'search_on_map' =>  0,
				'search_on_map_open' => 0,
				'category' => 0,
				'exact_categories' => array(),
				'what_search' => '',
				'location' => 0,
				'exact_locations' => array(),
				'draw_panel' => !empty($args['custom_home']) ? (int)$DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_enable_draw_panel'] : 0,
				'author' => 0,
				'enable_full_screen' => !empty($args['custom_home']) ? (int)$DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_enable_full_screen'] : 1,
				'enable_wheel_zoom' => !empty($args['custom_home']) ? (int)$DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_enable_wheel_zoom'] : 1,
				'enable_dragging_touchscreens' => !empty($args['custom_home']) ? (int)$DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_enable_dragging_touchscreens'] : 1,
				'center_map_onclick' => !empty($args['custom_home']) ? (int)$DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_center_map_onclick'] : 0,
				'categories' => '',
				'locations' => '',
				'related_directory' => 0,
				'related_categories' => 0,
				'related_locations' => 0,
				'related_tags' => 0,
				'uid' => null,
		), $args);
		$shortcode_atts = apply_filters('directorypress_related_shortcode_args', $shortcode_atts, $args);
		
		$this->args = $shortcode_atts;
		if ($shortcode_atts['include_get_params']) {
			$get_params = $_GET;
			array_walk_recursive($get_params, 'sanitize_text_field');
			$this->args = array_merge($this->args, $get_params);
		}
		
		if ($this->args['custom_home'] || ($this->args['uid'] && $directorypress_object->getListingsShortcodeByuID($this->args['uid'])))
			return false;

		$args = array(
				'post_type' => DIRECTORYPRESS_POST_TYPE,
				'post_status' => 'publish',
				//'meta_query' => array(array('key' => '_listing_status', 'value' => 'active')),
				'posts_per_page' => ($shortcode_atts['num'] ? $shortcode_atts['num'] : -1),
		);
		if ($shortcode_atts['author'])
			$args['author'] = $shortcode_atts['author'];

		$args = apply_filters('directorypress_search_args', $args, $this->args, $this->args['include_get_params'], $this->hash);

		if (!empty($this->args['post__in'])) {
			if (is_string($this->args['post__in'])) {
				$args = array_merge($args, array('post__in' => explode(',', $this->args['post__in'])));
			} elseif (is_array($this->args['post__in'])) {
				$args['post__in'] = $this->args['post__in'];
			}
		}
		if (!empty($this->args['post__not_in'])) {
			$args = array_merge($args, array('post__not_in' => explode(',', $this->args['post__not_in'])));
		}
		
		if (!empty($this->args['directorytypes'])) {
			if ($directorytypes_ids = array_filter(explode(',', $this->args['directorytypes']), 'trim')) {
				$args = directorypress_set_directory_args($args, $directorytypes_ids);
			}
		}

		/* if (isset($this->args['packages']) && !is_array($this->args['packages'])) {
			if ($packages = array_filter(explode(',', $this->args['packages']), 'trim')) {
				$this->packages_ids = $packages;
				add_filter('posts_join', 'join_packages');
				add_filter('posts_where', array($this, 'where_packages_ids'));
			}
		} */
		
		if ($this->args['has_sticky_has_featured']) {
			//add_filter('posts_join', 'join_packages');
			add_filter('posts_where', 'where_has_sticky_has_featured');
		}

		if (isset($this->args['neLat']) && isset($this->args['neLng']) && isset($this->args['swLat']) && isset($this->args['swLng'])) {
			$y1 = $this->args['neLat'];
			$y2 = $this->args['swLat'];
			// when zoom package 2 - there may be problems with neLng and swLng of bounds
			if ($this->args['neLng'] > $this->args['swLng']) {
				$x1 = $this->args['neLng'];
				$x2 = $this->args['swLng'];
			} else {
				$x1 = 180;
				$x2 = -180;
			}
			
			global $wpdb;
			$results = $wpdb->get_results($wpdb->prepare(
				"SELECT DISTINCT
					post_id FROM {$wpdb->directorypress_locations_relation} AS directorypress_lr
				WHERE MBRContains(
				GeomFromText('Polygon((%f %f,%f %f,%f %f,%f %f,%f %f))'),
				GeomFromText(CONCAT('POINT(',directorypress_lr.map_coords_1,' ',directorypress_lr.map_coords_2,')')))", $y2, $x2, $y2, $x1, $y1, $x1, $y1, $x2, $y2, $x2), ARRAY_A);

			$post_ids = array();
			foreach ($results AS $row) {
				$post_ids[] = $row['post_id'];
			}
			$post_ids = array_unique($post_ids);

			if ($post_ids) {
				if (isset($args['post__in'])) {
					$args['post__in'] = array_intersect($args['post__in'], $post_ids);
					if (empty($args['post__in'])) {
						// Do not show any listings
						$args['post__in'] = array(0);
					}
				} else {
					$args['post__in'] = $post_ids;
				}
			} else {
				// Do not show any listings
				$args['post__in'] = array(0);
			}
		}
		
		if (isset($this->args['geo_poly']) && $this->args['geo_poly']) {
			$geo_poly = $this->args['geo_poly'];
			$sql_polygon = array();
			foreach ($geo_poly AS $vertex)
				$sql_polygon[] = $vertex['lat'] . ' ' . $vertex['lng'];
			$sql_polygon[] = $sql_polygon[0];

			global $wpdb, $directorypress_address_locations;
			if (function_exists('mysql_get_server_info') && version_compare(preg_replace('#[^0-9\.]#', '', mysql_get_server_info()), '5.6.1', '<')) {
				// below 5.6.1 version
				$thread_stack = @$wpdb->get_row("SELECT @@global.thread_stack", ARRAY_A);
				if ($thread_stack && $thread_stack['@@global.thread_stack'] <= 131072)
					@$wpdb->query("SET @@global.thread_stack = " . 256*1024);

				if (!$wpdb->get_row("SHOW FUNCTION STATUS WHERE name='GISWithin'", ARRAY_A))
					$o = $wpdb->query("CREATE FUNCTION GISWithin(pt POINT, mp MULTIPOLYGON) RETURNS INT(1) DETERMINISTIC
BEGIN
			
DECLARE str, xy TEXT;
DECLARE x, y, p1x, p1y, p2x, p2y, m, xinters DECIMAL(16, 13) DEFAULT 0;
DECLARE counter INT DEFAULT 0;
DECLARE p, pb, pe INT DEFAULT 0;
			
SELECT MBRWithin(pt, mp) INTO p;
IF p != 1 OR ISNULL(p) THEN
RETURN p;
END IF;
			
SELECT X(pt), Y(pt), ASTEXT(mp) INTO x, y, str;
SET str = REPLACE(str, 'POLYGON((','');
SET str = REPLACE(str, '))', '');
SET str = CONCAT(str, ',');
			
SET pb = 1;
SET pe = LOCATE(',', str);
SET xy = SUBSTRING(str, pb, pe - pb);
SET p = INSTR(xy, ' ');
SET p1x = SUBSTRING(xy, 1, p - 1);
SET p1y = SUBSTRING(xy, p + 1);
SET str = CONCAT(str, xy, ',');
			
WHILE pe > 0 DO
SET xy = SUBSTRING(str, pb, pe - pb);
SET p = INSTR(xy, ' ');
SET p2x = SUBSTRING(xy, 1, p - 1);
SET p2y = SUBSTRING(xy, p + 1);
IF p1y < p2y THEN SET m = p1y; ELSE SET m = p2y; END IF;
IF y > m THEN
IF p1y > p2y THEN SET m = p1y; ELSE SET m = p2y; END IF;
IF y <= m THEN
IF p1x > p2x THEN SET m = p1x; ELSE SET m = p2x; END IF;
IF x <= m THEN
IF p1y != p2y THEN
SET xinters = (y - p1y) * (p2x - p1x) / (p2y - p1y) + p1x;
END IF;
IF p1x = p2x OR x <= xinters THEN
SET counter = counter + 1;
END IF;
END IF;
END IF;
END IF;
SET p1x = p2x;
SET p1y = p2y;
SET pb = pe + 1;
SET pe = LOCATE(',', str, pb);
END WHILE;
			
RETURN counter % 2;
			
END;
");
				$results = $wpdb->get_results("SELECT id, post_id FROM {$wpdb->directorypress_locations_relation} AS directorypress_lr
				WHERE GISWithin(
				GeomFromText(CONCAT('POINT(',directorypress_lr.map_coords_1,' ',directorypress_lr.map_coords_2,')')), PolygonFromText('POLYGON((" . implode(', ', $sql_polygon) . "))'))", ARRAY_A);
			} else {
				// 5.6.1 version or higher
				$results = $wpdb->get_results("SELECT id, post_id FROM {$wpdb->directorypress_locations_relation} AS directorypress_lr
				WHERE ST_Contains(
				PolygonFromText('POLYGON((" . implode(', ', $sql_polygon) . "))'), GeomFromText(CONCAT('POINT(',directorypress_lr.map_coords_1,' ',directorypress_lr.map_coords_2,')')))", ARRAY_A);
			}

			$post_ids = array();
			$directorypress_address_locations = array();
			foreach ($results AS $row) {
				$post_ids[] = $row['post_id'];
				$directorypress_address_locations[] = $row['id'];
			}
			$post_ids = array_unique($post_ids);
			
			if ($post_ids) {
				if (isset($args['post__in'])) {
					$args['post__in'] = array_intersect($args['post__in'], $post_ids);
					if (empty($args['post__in'])) {
						// Do not show any listings
						$args['post__in'] = array(0);
					}
				} else {
					$args['post__in'] = $post_ids;
				}
			} else {
				// Do not show any listings
				$args['post__in'] = array(0);
			}
		}

		$this->map = new directorypress_maps($this->args, $this->directorypress_client, $this->args['directorytypes']);
		$this->map->set_unique_id($this->hash);

		if (!$this->map->is_ajax_markers_management()) {
			$this->query = new WP_Query($args);
			//var_dump($this->query->request);
			$this->processQuery($this->args['ajax_markers_loading']);
		}
		
		if ($this->args['has_sticky_has_featured']) {
			//remove_filter('posts_join', 'join_packages');
			remove_filter('posts_where', 'where_has_sticky_has_featured');
		}

		/* if ($this->packages_ids) {
			remove_filter('posts_join', 'join_packages');
			remove_filter('posts_where', array($this, 'where_packages_ids'));
		} */
		
		apply_filters('directorypress_map_handler_construct', $this);
	}
	
	public function processQuery($is_ajax_map = false, $map_args = array()) {
		while ($this->query->have_posts()) {
			$this->query->the_post();

			$listing = new directorypress_listing;
			if (!$is_ajax_map) {
				$listing->init_listing_on_map(get_post());
				$this->map->collect_locations($listing);
			} else {
				$listing->init_listing_on_map_with_ajax(get_post());
				$this->map->collect_locationsForAjax($listing);
			}

			$this->listings[get_the_ID()] = $listing;
		}

		global $directorypress_address_locations, $directorypress_tax_terms_locations;
		// empty this global arrays - there may be some maps on one page with different arguments
		$directorypress_address_locations = array();
		$directorypress_tax_terms_locations = array();

		// this is reset is really required after the loop ends
		wp_reset_postdata();
	}
	
	/* public function where_packages_ids($where = '') {
		if ($this->packages_ids)
			$where .= " AND (directorypress_packages.id IN (" . implode(',', $this->packages_ids) . "))";
		return $where;
	} */

	public function display() {
		global $directorypress_object, $DIRECTORYPRESS_ADIMN_SETTINGS;

		$width = false;
		$height = $DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_default_map_height'];
		if (isset($this->args['width']))
			$width = $this->args['width'];
		if (isset($this->args['height']))
			$height = $this->args['height'];

		ob_start();
		if ($this->args['custom_home'] || ($this->args['uid'] && $directorypress_listings_handler = $directorypress_object->getListingsShortcodeByuID($this->args['uid']))) {
			if ($directorypress_directory_handler = $directorypress_object->directorypress_get_property_of_shortcode('directorypress-main')) {
				// the map shortcode on custom home

				$show_summary_button = $this->args['show_summary_button'];
				$show_readmore_button = $this->args['show_readmore_button'];
				if ($directorypress_directory_handler->is_single) {
					$show_summary_button = false;
					$show_readmore_button = false;
				}

				// map may be disabled for index or excerpt pages in directorytype settings, so we need to check does this object exist in main shortcode.
				if ($directorypress_directory_handler->map) {
					$directorypress_directory_handler->map->args = array_merge($directorypress_directory_handler->map->args, $this->args);
					
					if (!$this->args['map_markers_is_limit']) {
						// Display all map markers
						$directorypress_directory_handler->collectAllLocations();
					}
					
					$directorypress_directory_handler->map->display(
							false,
							false,
							$this->args['radius_circle'],
							$this->args['clusters'],
							$show_summary_button,
							$show_readmore_button,
							$width,
							$height,
							$this->args['has_sticky_scroll'],
							$this->args['has_sticky_scroll_toppadding'],
							$this->args['map_style'],
							$this->args['search_on_map'],
							$this->args['draw_panel'],
							$this->args['custom_home'],
							$this->args['enable_full_screen'],
							$this->args['enable_wheel_zoom'],
							$this->args['enable_dragging_touchscreens'],
							$this->args['center_map_onclick']
					);
				}
			} elseif (isset($directorypress_listings_handler) && $directorypress_listings_handler) {
				// the map shortcode connected with listings shortcode

				$show_summary_button = $this->args['show_summary_button'];
				$show_readmore_button = $this->args['show_readmore_button'];
				if (!$directorypress_listings_handler->map) {
					$directorypress_listings_handler->map = new directorypress_maps($this->args, $this->directorypress_client, $this->args['directorytypes']);
					$directorypress_listings_handler->map->set_unique_id($this->hash);
					
					if ($this->args['map_markers_is_limit']) {
						// The only map markers of visible listings will be displayed
						foreach ($directorypress_listings_handler->listings AS $listing) {
							$directorypress_listings_handler->map->collect_locations($listing);
						}
					} elseif ($directorypress_listings_handler->query) {
						// Display all map markers
						$directorypress_listings_handler->collectAllLocations();
					}
				}
				$directorypress_listings_handler->map->display(
						false,
						false,
						$this->args['radius_circle'],
						$this->args['clusters'],
						$show_summary_button,
						$show_readmore_button,
						$width,
						$height,
						$this->args['has_sticky_scroll'],
						$this->args['has_sticky_scroll_toppadding'],
						$this->args['map_style'],
						$this->args['search_on_map'],
						$this->args['draw_panel'],
						$this->args['custom_home'],
						$this->args['enable_full_screen'],
						$this->args['enable_wheel_zoom'],
						$this->args['enable_dragging_touchscreens'],
						$this->args['center_map_onclick']
				);
			} else {
				// the map shortcode has uID, but listings shortcode does not exist

				$show_summary_button = false;
				$show_readmore_button = true;
				$map = new directorypress_maps($this->args);
				$map->set_unique_id($this->hash);
				$map->display(
						false,
						false,
						$this->args['radius_circle'],
						$this->args['clusters'],
						$show_summary_button,
						$show_readmore_button,
						$width,
						$height,
						$this->args['has_sticky_scroll'],
						$this->args['has_sticky_scroll_toppadding'],
						$this->args['map_style'],
						$this->args['search_on_map'],
						$this->args['draw_panel'],
						$this->args['custom_home'],
						$this->args['enable_full_screen'],
						$this->args['enable_wheel_zoom'],
						$this->args['enable_dragging_touchscreens'],
						$this->args['center_map_onclick']
				);
			}
		} else {
			// standard behaviour of map shortcode
			$this->map->display(
					false, // hide directions
					false, // static image
					$this->args['radius_circle'],
					$this->args['clusters'],
					$this->args['show_summary_button'],
					$this->args['show_readmore_button'],
					$width,
					$height,
					$this->args['has_sticky_scroll'],
					$this->args['has_sticky_scroll_toppadding'],
					$this->args['map_style'],
					$this->args['search_on_map'],
					$this->args['draw_panel'],
					$this->args['custom_home'],
					$this->args['enable_full_screen'],
					$this->args['enable_wheel_zoom'],
					$this->args['enable_dragging_touchscreens'],
					$this->args['center_map_onclick']
			);
		}

		$output = ob_get_clean();

		wp_reset_postdata();
	
		return $output;
	}
}

?>