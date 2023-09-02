
var directorypress_draws = [];
var directorypress_draw_features = [];

//mapboxgl_edit.js -------------------------------------------------------------------------------------------------------------------------------------------
(function($) {
	"use strict";

	window.directorypress_init_backend_map_api = function() {
		if ($("#directorypress-maps-canvas").length) {
			mapboxgl.accessToken = directorypress_maps_instance.mapbox_api_key;
			directorypress_map_backend = new mapboxgl.Map({
			    container: "directorypress-maps-canvas",
			    style: 'mapbox://styles/mapbox/'+directorypress_maps_instance.map_style
			});
			var navigationControl = new mapboxgl.NavigationControl({
		        showCompass: false,
		        showZoom: true,
		    });
			directorypress_map_backend.addControl(navigationControl);

			if (directorypress_isAnyLocation_backend()) {
				//console.log(directorypress_isAnyLocation_backend());
				directorypress_generateMap_backend();
			} else {
				directorypress_map_backend.setCenter([0, 34]);
			}

			directorypress_map_backend.on('zoom', function() {
				if (directorypress_allow_map_zoom_backend) {
					$(".directorypress-map-zoom").val(Math.round(directorypress_map_backend.getZoom()));
				}
			});
			
		}
		directorypress_setupAutocomplete();
	}

	window.directorypress_setupAutocomplete = function() {
		//setInterval(function(){
		$(".directorypress-listing-field-autocomplete").listing_address_autocomplete();
		//}, 2000);
	}

	function directorypress_setMapCenter_backend(directorypress_coords_array_1, directorypress_coords_array_2) {
		var count = 0;
		var bounds = new mapboxgl.LngLatBounds();
		for (count == 0; count<directorypress_coords_array_1.length; count++)  {
			bounds.extend([directorypress_coords_array_2[count], directorypress_coords_array_1[count]]);
		}
		if (count == 1) {
			// required workaround: first zoom, then setCenter for initial load when single marker
			if ($(".directorypress-map-zoom").val() == '' || $(".directorypress-map-zoom").val() == 0) {
				var zoom_level = 1;
			} else {
				var zoom_level = parseInt($(".directorypress-map-zoom").val());
			}
			
			// allow/disallow map zoom in listener, this option needs because directorypress_map.setZoom() also calls this listener
			directorypress_allow_map_zoom_backend = false;
			directorypress_map_backend.setZoom(zoom_level);
			directorypress_allow_map_zoom_backend = true;
			
			directorypress_map_backend.setCenter([directorypress_coords_array_2[0], directorypress_coords_array_1[0]]);
		} else {
			directorypress_map_backend.fitBounds(bounds, {padding: 50, duration: 0});
		}
	}
	
	var directorypress_coords_array_1 = new Array();
	var directorypress_coords_array_2 = new Array();
	var directorypress_attempts = 0;
	window.directorypress_generateMap_backend = function() {
		directorypress_ajax_loader_target_show($("#directorypress-maps-canvas"));
		directorypress_coords_array_1 = new Array();
		directorypress_coords_array_2 = new Array();
		directorypress_attempts = 0;
		directorypress_clearOverlays_backend();
		directorypress_geocodeAddress_backend(0);
	}
	
	function directorypress_setFoundPoint(point, location_obj, i) {
		$(".directorypress-map-coords-1:eq("+i+")").val(point.lat);
		$(".directorypress-map-coords-2:eq("+i+")").val(point.lng);
		var map_coords_1 = point.lat;
		var map_coords_2 = point.lng;
		directorypress_coords_array_1.push(map_coords_1);
		directorypress_coords_array_2.push(map_coords_2);
		location_obj.setPoint(point);
		location_obj.directorypress_placeMarker();
		directorypress_geocodeAddress_backend(i+1);

		if ((i+1) == $(".directorypress-location-in-metabox").length) {
			directorypress_setMapCenter_backend(directorypress_coords_array_1, directorypress_coords_array_2);
			directorypress_ajax_loader_target_hide("directorypress-maps-canvas");
		}
	}

	window.directorypress_geocodeAddress_backend = function(i) {
		if ($(".directorypress-location-in-metabox:eq("+i+")").length) {
			var locations_drop_boxes = [];
			$(".directorypress-location-in-metabox:eq("+i+")").find("select").each(function(j, val) {
				if ($(this).val())
					locations_drop_boxes.push($(this).children(":selected").text());
			});
	
			var location_string = locations_drop_boxes.reverse().join(', ');
	
			if ($(".directorypress-manual-coords:eq("+i+")").is(":checked") && $(".directorypress-map-coords-1:eq("+i+")").val()!='' && $(".directorypress-map-coords-2:eq("+i+")").val()!='' && ($(".directorypress-map-coords-1:eq("+i+")").val()!=0 || $(".directorypress-map-coords-2:eq("+i+")").val()!=0)) {
				var map_coords_1 = $(".directorypress-map-coords-1:eq("+i+")").val();
				var map_coords_2 = $(".directorypress-map-coords-2:eq("+i+")").val();
				if ($.isNumeric(map_coords_1) && $.isNumeric(map_coords_2)) {
					var point = new mapboxgl.LngLat(map_coords_2, map_coords_1);
					directorypress_coords_array_1.push(map_coords_1);
					directorypress_coords_array_2.push(map_coords_2);
	
					var location_obj = new directorypress_glocation_backend(i, point, 
						location_string,
						$(".directorypress-address-line-1:eq("+i+")").val(),
						$(".directorypress-address-line-2:eq("+i+")").val(),
						$(".directorypress-zip-or-postal-index:eq("+i+")").val(),
						$(".directorypress-map-icon-file:eq("+i+")").val()
					);
					location_obj.directorypress_placeMarker();
				}
				directorypress_geocodeAddress_backend(i+1);
				if ((i+1) == $(".directorypress-location-in-metabox").length) {
					directorypress_setMapCenter_backend(directorypress_coords_array_1, directorypress_coords_array_2);
					directorypress_ajax_loader_target_hide("directorypress-maps-canvas");
				}
			} else if (location_string || $(".directorypress-address-line-1:eq("+i+")").val() || $(".directorypress-address-line-2:eq("+i+")").val() || $(".directorypress-zip-or-postal-index:eq("+i+")").val()) {
				var location_obj = new directorypress_glocation_backend(i, null, 
					location_string,
					$(".directorypress-address-line-1:eq("+i+")").val(),
					$(".directorypress-address-line-2:eq("+i+")").val(),
					$(".directorypress-zip-or-postal-index:eq("+i+")").val(),
					$(".directorypress-map-icon-file:eq("+i+")").val()
				);

				// Geocode by address
				function _directorypress_geocodeAddress_backend(status, lat, lng) {
					if (status) {
						directorypress_setFoundPoint(new mapboxgl.LngLat(lng, lat), location_obj, i);
					} else {
						alert("Sorry, we are unable to geocode that address (address #"+(i)+") for the following reason: " + status);
						directorypress_ajax_loader_target_hide("directorypress-maps-canvas");
					}
				}

				directorypress_geocodeAddress(location_obj.compileAddress(), _directorypress_geocodeAddress_backend, directorypress_maps_instance.address_autocomplete_code);
			} else
				directorypress_ajax_loader_target_hide("directorypress-maps-canvas");
		}
	}

	window.directorypress_placeMarker_backend = function(directorypress_glocation) {
		if (directorypress_maps_instance.map_markers_type != 'icons') {
			
			if (directorypress_maps_instance.global_map_icons_path != '') {
				var icon_file = directorypress_glocation.map_icon_file;
				var el = $("<div>", {
					id: 'marker-id-'+directorypress_glocation.index,
					style: 'background-image: url('+icon_file+');',
					class: 'directorypress-mapbox-marker'
				});
				var marker_div = el[0];
				
				var marker_options = {
						anchor: 'bottom',
						offset: [0, -20],
						element: marker_div,
						draggable: true
				};
				
				var popup = new mapboxgl.Popup({ offset: 25 })
			    .setText(directorypress_glocation.compileAddress());
				
				var marker = new mapboxgl.Marker(marker_options)
	    		.setLngLat(directorypress_glocation.point)
	    		.addTo(directorypress_map_backend)
	    		.setPopup(popup);
			} else {
				var marker = new mapboxgl.Marker()
	    		.setLngLat(location.point)
	    		.addTo(directorypress_map_backend);
			}
		} else {
			var map_marker_color = directorypress_maps_instance.default_marker_color;

			if(directorypress_glocation.map_icon_file == null){
				
				icon_file = directorypress_maps_instance.default_marker_icon;
			}else{
				icon_file = directorypress_glocation.map_icon_file;
			}

			var el = $("<div>", {
				id: 'marker-id-'+directorypress_glocation.index,
				class: 'directorypress-mapbox-marker',
				html: '<div class="directorypress-map-marker" style="background: '+map_marker_color+' none repeat scroll 0 0;"><span class="directorypress-map-marker-icon '+icon_file+'" style="color: '+map_marker_color+';"></span></div>'
			});
			var marker_div = el[0];
			
			var marker_options = {
				anchor: 'bottom',
				offset: [0, -20],
				element: marker_div,
				draggable: true
			};
			
			var popup = new mapboxgl.Popup({ offset: 25 })
		    .setText(directorypress_glocation.compileAddress());

			var marker = new mapboxgl.Marker(marker_options)
    		.setLngLat(directorypress_glocation.point)
    		.addTo(directorypress_map_backend)
    		.setPopup(popup);
		}
		
		directorypress_markersArray_backend.push(marker);
		
		marker.on('drag', function() {
			var point = marker.getLngLat();
			if (point !== undefined) {
				var selected_location_num = directorypress_glocation.index;
				$(".directorypress-manual-coords:eq("+directorypress_glocation.index+")").attr("checked", true);
				$(".directorypress-manual-coords:eq("+directorypress_glocation.index+")").parents(".directorypress-manual-coords-wrapper").find(".directorypress-manual-coords-block").show(200);
				
				$(".directorypress-map-coords-1:eq("+directorypress_glocation.index+")").val(point.lat);
				$(".directorypress-map-coords-2:eq("+directorypress_glocation.index+")").val(point.lng);
			}
		});
	}

	function directorypress_clearOverlays_backend() {
		if (directorypress_markersArray_backend) {
			for(var i = 0; i<directorypress_markersArray_backend.length; i++){
				directorypress_markersArray_backend[i].remove();
			}
		}
	}
	
	function directorypress_isAnyLocation_backend() {
		var is_location = false;
		$(".directorypress-location-in-metabox").each(function(i, val) {
			var locations_drop_boxes = [];
			$(this).find("select").each(function(j, val) {
				if ($(this).val()) {
					is_location = true;
					return false;
				}
			});
	
			if ($(".directorypress-manual-coords:eq("+i+")").is(":checked") && $(".directorypress-map-coords-1:eq("+i+")").val()!='' && $(".directorypress-map-coords-2:eq("+i+")").val()!='' && ($(".directorypress-map-coords-1:eq("+i+")").val()!=0 || $(".directorypress-map-coords-2:eq("+i+")").val()!=0)) {
				is_location = true;
				return false;
			}
		});
		if (is_location){
			return true;
		}
	
		if ($(".directorypress-address-line-1[value!='']").length != 0){
			return true;
		}
	
		if ($(".directorypress-address-line-2[value!='']").length != 0){
			return true;
		}
	
		if ($(".directorypress-zip-or-postal-index[value!='']").length != 0){
			return true;
		}
	}
})(jQuery);

(function($) {
	"use strict";
	
	window.directorypress_buildPoint = function(lat, lng) {
		return [lng, lat];
	}

	window.directorypress_buildBounds = function() {
		return new mapboxgl.LngLatBounds();
	}

	window.directorypress_extendBounds = function(bounds, point) {
		bounds.extend(point);
	}

	window.directorypress_mapFitBounds = function(map_id, bounds) {
		directorypress_maps[map_id].fitBounds(bounds, {padding: 50, duration: 0});
	}

	window.directorypress_getMarkerPosition = function(marker) {
		return marker.getLngLat();
	}

	window.directorypress_closeInfoWindow = function(map_id) {
		if (typeof directorypress_infoWindows[map_id] != 'undefined') {
			directorypress_infoWindows[map_id].remove();
		}
	}
	
	class directorypress_point {
		constructor(lng, lat) {
			this.coord_1 = lng;
			this.coord_2 = lat;
		}
		lng() {
			return this.coord_1;
		}
		lat() {
			return this.coord_2;
		}
	}

	window.directorypress_setAjaxMarkersListener = function(map_id) {
		directorypress_setMapAjaxListener(directorypress_maps[map_id], map_id);
	}
	
	window.directorypress_geocodeAddress = function(address, callback, address_autocomplete_code) {
		if (typeof address_autocomplete_code != 'undefined' && address_autocomplete_code != '')
			var country = '&country='+address_autocomplete_code;
		else
			var country = '';

		$.get("https://api.mapbox.com/geocoding/v5/mapbox.places/"+encodeURIComponent(address)+".json?access_token="+directorypress_maps_instance.mapbox_api_key+country, function(data) {
			if (data.features.length) {
				// data.features[0].geometry.coordinates[0] - longitude
				// data.features[0].geometry.coordinates[1] - latitude
				callback(true, data.features[0].geometry.coordinates[1], data.features[0].geometry.coordinates[0]);
			} else {
				callback(false, 0, 0);
			}
		}).fail(function() {
			callback(false, 0, 0);
		});
	}
	
	window.directorypress_callMapResize = function(map_id) {
		directorypress_maps[map_id].resize();
	}

	window.directorypress_setMapCenter = function(map_id, center) {
		directorypress_maps[map_id].setCenter(center);
	}
	
	window.directorypress_set_map_zoom = function(map_id, zoom) {
		directorypress_maps[map_id].setZoom(parseInt(zoom));
	}

	window.directorypress_autocompleteService = function(term, address_autocomplete_code, common_array, response, callback) {
		if (address_autocomplete_code != '')
			var country = '&country='+address_autocomplete_code;
		else
			var country = '';

		var output_predictions = [];
		$.get("https://api.mapbox.com/geocoding/v5/mapbox.places/"+encodeURIComponent(term)+".json?access_token="+directorypress_maps_instance.mapbox_api_key+country, function(data) {
			$.map(data.features, function (prediction, i) {
				var output_prediction = {
						label: prediction.text,
						value: prediction.place_name,
						name: prediction.place_name,
						sublabel: prediction.place_name.replace(prediction.text + ", ", ""),
				};
				output_predictions.push(output_prediction);
			});
			
			callback(output_predictions, common_array, response);
		}).fail(function() {
			callback(output_predictions, common_array, response);
		});;
	}
	
	function directorypress_addPolygon(map_id) {
		var map = directorypress_maps[map_id];
		
		map.addSource('geo-poly-'+map_id, {
			'type': 'geojson',
			'data': directorypress_draw_features[map_id]
		});
		map.addLayer({
			'id': 'geo-poly-'+map_id,
			'type': 'fill',
			'source': 'geo-poly-'+map_id,
			'layout': {},
			'paint': {
				'fill-color': '#0099FF',
				'fill-opacity': 0.3,
				'fill-outline-color': '#AA2143'
			}
		});

		directorypress_polygons[map_id] = true;
	}
	
	function directorypress_drawFreeHandPolygon(map_id) {
		var geojson = {
				"type": "FeatureCollection",
				"features": []
		};

		var linestring = {
				"type": "Feature",
				"geometry": {
					"type": "LineString",
					"coordinates": []
				}
		};
		
		var map = directorypress_maps[map_id];

		map.addSource('geo-lines', {
			"type": "geojson",
			"data": geojson
		});

	    map.addLayer({
	        id: 'geo-lines',
	        type: 'line',
	        source: 'geo-lines',
	        layout: {
	            'line-cap': 'round',
	            'line-join': 'round'
	        },
	        paint: {
	            'line-color': '#AA2143',
	            'line-width': 2
	        },
	        filter: ['in', '$type', 'LineString']
	    });

	    var draw_move_event = function(e) {
	    	var features = map.queryRenderedFeatures(e.point, { layers: ['geo-lines'] });

	        // Remove the linestring from the group
	        // So we can redraw it based on the points collection
	        if (geojson.features.length > 1) geojson.features.pop();

	        var point = {
	        		"type": "Feature",
	                "geometry": {
	                    "type": "Point",
	                    "coordinates": [
	                        e.lngLat.lng,
	                        e.lngLat.lat
	                    ]
	                },
	                "properties": {
	                    "id": String(new Date().getTime())
	                }
	        };

	        geojson.features.push(point);

	        if (geojson.features.length > 1) {
	            linestring.geometry.coordinates = geojson.features.map(function(point) {
	                return point.geometry.coordinates;
	            });

	            geojson.features.push(linestring);
	        }

	        map.getSource('geo-lines').setData(geojson);
	    };
	    map.on('mousemove', draw_move_event);
	    map.on('touchmove', draw_move_event);
	    
	    var draw_up_event = function(e) {
	    	map.off('mousemove', draw_move_event);
	    	map.off('touchmove', draw_move_event);
	    	map.removeLayer('geo-lines');
	    	map.removeSource('geo-lines');

	    	var theArrayofLngLat = [];
	    	linestring.geometry.coordinates.map(function(point_feature) {
	    		theArrayofLngLat.push(new directorypress_point(point_feature[0], point_feature[1]));
	    	});
			var ArrayforPolygontoUse = directorypress_GDouglasPeucker(theArrayofLngLat, 1);
			
			var geo_poly_json = [];
			var geo_poly_ajax = [];
			if (ArrayforPolygontoUse.length) {
				var lat_lng;
				for (lat_lng in ArrayforPolygontoUse) {
					geo_poly_json.push([ArrayforPolygontoUse[lat_lng].lng(), ArrayforPolygontoUse[lat_lng].lat()]);
					geo_poly_ajax.push({ 'lat': ArrayforPolygontoUse[lat_lng].lat(), 'lng': ArrayforPolygontoUse[lat_lng].lng() });
				}
				geo_poly_json.push([ArrayforPolygontoUse[0].lng(), ArrayforPolygontoUse[0].lat()]);
			}

			if (geo_poly_json.length) {
				directorypress_sendGeoPolyAJAX(map_id, geo_poly_ajax);
				
				var geo_poly_feature = {
						'id': 'geo-poly-feature-'+map_id,
						'type': 'Feature',
						'properties': {},
		                'geometry': {
		                    'type': 'Polygon',
		                    'coordinates': [geo_poly_json]
		                }
				};
				
				directorypress_draw_features[map_id] = geo_poly_feature;
				
				directorypress_addPolygon(map_id);
				
				var editButton = $(map.getContainer()).find('.directorypress-map-edit').get(0);
				$(editButton).removeAttr('disabled');
			}
			var drawButton = $(map.getContainer()).find('.directorypress-map-draw').get(0);
			drawButton.drawing_state = 0;
			window.removeEventListener('touchmove', directorypress_stop_touchmove_listener, { passive: false });
			map.getCanvas().style.cursor = '';
			$(drawButton).removeClass('btn-active');
			directorypress_disableDrawingMode(map_id);
	    };
		map.once('mouseup', draw_up_event); 
		map.once('touchend', draw_up_event); 
	}
	function directorypress_enableDrawingMode(map_id) {
		$(directorypress_maps[map_id].getContainer()).find('.directorypress-map-custom-controls').hide();
		// if sidebar was not opened - hide search field
		if (!directorypress_isSidebarOpen(map_id) && $('#directorypress-map-search-wrapper-'+map_id).length) {
			$('#directorypress-map-search-wrapper-'+map_id).hide();
		}
		var map = directorypress_maps[map_id];

		map.scrollZoom.disable();
		map.dragRotate.disable();
		map.touchZoomRotate.disable();
		map.dragPan.disable();
	}
	function directorypress_disableDrawingMode(map_id) {
		var map = directorypress_maps[map_id];

		$(map.getContainer()).find('.directorypress-map-custom-controls').show();
		if ($('#directorypress-map-search-wrapper-'+map_id).length) $('#directorypress-map-search-wrapper-'+map_id).show();

		var attrs_array = directorypress_get_map_markers_attrs_array(map_id);
		var enable_wheel_zoom = attrs_array.enable_wheel_zoom;
		var enable_dragging_touchscreens = attrs_array.enable_dragging_touchscreens;
		if (enable_dragging_touchscreens || !('ontouchstart' in document.documentElement)) {
			map.dragRotate.enable();
			map.dragPan.enable();
			map.touchZoomRotate.enable();
		}
		if (enable_wheel_zoom) {
			map.scrollZoom.enable();
		}
	}
	
	window.directorypress_set_map_zoomCenter = function(map_id, map_attrs, markers_array) {
		if (typeof map_attrs.start_zoom != 'undefined' && map_attrs.start_zoom > 0)
			var zoom_level = map_attrs.start_zoom;
	    else if (markers_array.length == 1)
			var zoom_level = markers_array[0][5];
		else if (markers_array.length > 1)
			// fitbounds does not need zoom
			var zoom_level = false;
		else
			var zoom_level = 2;
	
	    if (typeof map_attrs.start_latitude != 'undefined' && map_attrs.start_latitude && typeof map_attrs.start_longitude != 'undefined' && map_attrs.start_longitude) {
			var start_latitude = map_attrs.start_latitude;
			var start_longitude = map_attrs.start_longitude;
			if (zoom_level == false) {
				zoom_level = 12;
			}
			// required workaround: first zoom, then setCenter
			directorypress_set_map_zoom(map_id, zoom_level);
			directorypress_setMapCenter(map_id, [start_longitude, start_latitude]);
			
			if (typeof map_attrs.ajax_loading != 'undefined' && map_attrs.ajax_loading == 1) {
			    // use closures here
			    directorypress_setMapAjaxListener(directorypress_maps[map_id], map_id);
			}
	    } else if (typeof map_attrs.start_address != 'undefined' && map_attrs.start_address) {
	    	// use closures here
	    	directorypress_geocodeStartAddress(map_attrs, map_id, zoom_level);
	    } else if (markers_array.length == 1) {
	    	directorypress_set_map_zoom(map_id, zoom_level);
		} else if (zoom_level) {
			// no fitbounds here
			// required workaround: first zoom, then setCenter for initial load when single marker
		    directorypress_set_map_zoom(map_id, zoom_level);
		    directorypress_setMapCenter(map_id, [34, 0]);
		}
    }

	window.directorypress_show_on_map_links = function() {
		$(".directorypress-show-on-map").each(function() {
			var location_id = $(this).data("location-id");

			var passed = false;
			for (var map_id in directorypress_maps) {
				if (typeof directorypress_global_locations_array[map_id] != 'undefined') {
					for (var i=0; i<directorypress_global_locations_array[map_id].length; i++) {
						if (typeof directorypress_global_locations_array[map_id][i] == 'object') {
							if (location_id == directorypress_global_locations_array[map_id][i].id) {
								passed = true;
							}
						}
					}
				}
			}
			if (passed) {
				$(this).parent('.directorypress-listing-figcaption-option').show();
			} else {
				$(this).css({'cursor': 'auto'});
				if ($(this).hasClass('btn')) {
					$(this).hide();
				}
			}
		});
	}

	function directorypress_load_maps() {
		for (var i=0; i<directorypress_map_markers_attrs_array.length; i++) {
			if (typeof directorypress_maps[directorypress_map_markers_attrs_array[i].map_id] == 'undefined') { // workaround for "tricky" themes and plugins to load maps twice
				directorypress_load_map(i);
			}
		}

		directorypress_show_on_map_links();
		
		directorypress_geolocatePosition();
	}

	window.directorypress_load_maps_api = function() {
		$(document).trigger('directorypress_mapbox_api_loaded');

		// are there any markers?
		if (typeof directorypress_map_markers_attrs_array != 'undefined' && directorypress_map_markers_attrs_array.length) {
			_directorypress_map_markers_attrs_array = JSON.parse(JSON.stringify(directorypress_map_markers_attrs_array));

			directorypress_load_maps();
		}
		
		$(".directorypress-listing-field-autocomplete").listing_address_autocomplete();
		
		$('body').on('click', '.directorypress-show-on-map', function() {
			var location_id = $(this).data("location-id");

			for (var map_id in directorypress_maps) {
				if (typeof directorypress_global_locations_array[map_id] != 'undefined') {
					for (var i=0; i<directorypress_global_locations_array[map_id].length; i++) {
						if (typeof directorypress_global_locations_array[map_id][i] == 'object') {
							if (location_id == directorypress_global_locations_array[map_id][i].id) {
								var location_obj = directorypress_global_locations_array[map_id][i];
								var side_offset = 0;
								if ($("#directorypress-maps-canvas-"+map_id).hasClass("directorypress-sidebar-open")) {
									if (directorypress_js_instance.is_rtl) {
										side_offset = 200;
									} else {
										side_offset = -200;
									}
								}
								directorypress_maps[map_id].panToWithOffset(location_obj.marker.getLngLat(), side_offset, -100);
								directorypress_setInfoWindow(location_obj, location_obj.marker, map_id, 'bottom', 'onbuttonclick');
							}
						}
					}
				}
			}
		});
	}

	document.addEventListener("DOMContentLoaded", function() {
		window[directorypress_maps_function_call.callback]();
	});
	
	function directorypress_positionCustomControls(map_id, customControls) {
		var mapDiv = directorypress_maps[map_id].getContainer();
	    if ($(mapDiv).parent().hasClass('directorypress-map-search-input-enabled') && $(mapDiv).width() <= 500) {
	    	$(customControls).addClass('directorypress-map-custom-controls-lower');
	    } else {
	    	$(customControls).removeClass('directorypress-map-custom-controls-lower');
	    }
	}

	function directorypress_load_map(i) {
		var map_id = directorypress_map_markers_attrs_array[i].map_id;
		var markers_array = directorypress_map_markers_attrs_array[i].markers_array;
		var enable_radius_circle = directorypress_map_markers_attrs_array[i].enable_radius_circle;
		var enable_clusters = directorypress_map_markers_attrs_array[i].enable_clusters;
		var show_summary_button = directorypress_map_markers_attrs_array[i].show_summary_button;
		var map_style = directorypress_map_markers_attrs_array[i].map_style;
		var draw_panel = directorypress_map_markers_attrs_array[i].draw_panel;
		var show_readmore_button = directorypress_map_markers_attrs_array[i].show_readmore_button;
		var enable_full_screen = directorypress_map_markers_attrs_array[i].enable_full_screen;
		var enable_wheel_zoom = directorypress_map_markers_attrs_array[i].enable_wheel_zoom;
		var enable_dragging_touchscreens = directorypress_map_markers_attrs_array[i].enable_dragging_touchscreens;
		var show_directions = directorypress_map_markers_attrs_array[i].show_directions;
		var map_attrs = directorypress_map_markers_attrs_array[i].map_attrs;
		
		if (document.getElementById("directorypress-maps-canvas-"+map_id)) {
			if (typeof directorypress_fullScreens[map_id] == "undefined" || !directorypress_fullScreens[map_id]) {
				
				if (typeof directorypress_maps[map_id] != 'undefined') {
					directorypress_maps[map_id].remove();
				}
				
				mapboxgl.accessToken = directorypress_maps_instance.mapbox_api_key;
				var map = new mapboxgl.Map({
				    container: "directorypress-maps-canvas-"+map_id,
				    style: 'mapbox://styles/mapbox/'+map_style
				});
				
				if (directorypress_maps_instance.is_rtl) {
					mapboxgl.setRTLTextPlugin('https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-rtl-text/v0.1.0/mapbox-gl-rtl-text.js');
				}
				
				var options = {};
				if (directorypress_maps_instance.lang) {
					options = { defaultLanguage:  directorypress_maps_instance.lang}; 
				}
				map.addControl(new MapboxLanguage(options));
				
				if (!enable_wheel_zoom) {
					map.scrollZoom.disable();
				}

				directorypress_maps[map_id] = map;
			    directorypress_maps_attrs[map_id] = map_attrs;
			    
			    if (show_directions) {
					var directions = new MapboxDirections({
					    accessToken: mapboxgl.accessToken,
					    language: directorypress_maps_instance.lang,
					    geocoder: {
							language: directorypress_maps_instance.lang
						}
					});
					
					if (directorypress_js_instance.is_rtl) {
						var cposition = 'top-right';
					} else {
						var cposition = 'top-left';
					}
					map.addControl(directions, cposition);
				}

			    class directorypress_custom_controls {
					  onAdd(map){
					    this.map = map;

					    var customControls = document.createElement('div');
					    $(customControls).addClass('mapboxgl-ctrl directorypress-map-custom-controls');
					    $(customControls).html('<div class="btn-group"><button class="btn btn-primary directorypress-map-btn-zoom-in"><span class="glyphicon glyphicon-plus"></span></button><button class="btn btn-primary directorypress-map-btn-zoom-out"><span class="glyphicon glyphicon-minus"></span></button></div> <div class="btn-group">'+(enable_full_screen ? '<button class="btn btn-primary directorypress-map-btn-fullscreen"><span class="glyphicon glyphicon-fullscreen"></span></button>' : '')+'</div>');
					    
					    directorypress_positionCustomControls(map_id, customControls);
					    map.on('resize', function() {
					    	directorypress_positionCustomControls(map_id, customControls);
					    });
					    
					    this.container = customControls;
					    return this.container;
					  }
					  onRemove(){
					    this.container.parentNode.removeChild(this.container);
					    this.map = undefined;
					  }
				}
				var customControls = new directorypress_custom_controls();
				
				if (directorypress_js_instance.is_rtl) {
					var cposition = 'top-left';
				} else {
					var cposition = 'top-right';
				}
				map.addControl(customControls, cposition);
				
				$(customControls.container).find('.directorypress-map-btn-zoom-in').on("click", function() {
			    	directorypress_maps[map_id].zoomIn();
			    });
			    $(customControls.container).find('.directorypress-map-btn-zoom-out').on("click", function() {
			    	directorypress_maps[map_id].zoomOut();
			    });
				
			    var interval;
			    var mapDiv = directorypress_maps[map_id].getContainer();
			    var mapDivParent = $(mapDiv).parent().parent();
			    var divStyle = mapDiv.style;
			    if (mapDiv.runtimeStyle)
			        divStyle = mapDiv.runtimeStyle;
			    var originalPos = divStyle.position;
			    var originalWidth = divStyle.width;
			    var originalHeight = divStyle.height;
			    // ie8 hack
			    if (originalWidth === "")
			        originalWidth = mapDiv.style.width;
			    if (originalHeight === "")
			        originalHeight = mapDiv.style.height;
			    var originalTop = divStyle.top;
			    var originalLeft = divStyle.left;
			    var originalZIndex = divStyle.zIndex;
			    var bodyStyle = document.body.style;
			    if (document.body.runtimeStyle)
			        bodyStyle = document.body.runtimeStyle;
			    var originalOverflow = bodyStyle.overflow;
			    var thePanoramaOpened = false;

			    //directorypress_fullScreens[map_id] = true;
			    //openFullScreen();

			    function openFullScreen() {
			    	mapDivParent.after("<div id='directorypress-map-placeholder-"+map_id+"'></div>");
			    	mapDivParent.appendTo('body');
			    	
			    	var elements_to_zindex = [
			                                  "#directorypress-map-search-wrapper-"+map_id,
			                                  "#directorypress-map-search-panel-wrapper-"+map_id,
			                                  "#directorypress-map-sidebar-toggle-container-"+map_id,
			        ];
			        $(elements_to_zindex).each(function() {
			        	if ($(this).length) {
			        		$(this).css('position', 'fixed').zIndex(100001);
			        	}
			        });
			    	
			    	var center = directorypress_maps[map_id].getCenter();
			        mapDiv.style.position = "fixed";
			        mapDiv.style.width = "100%";
			        mapDiv.style.height = "100%";
			        mapDiv.style.top = "0";
			        mapDiv.style.left = "0";
			        mapDiv.style.zIndex = "100000";
			        $(mapDiv).parent(".directorypress-maps-container").zIndex(100000).css('overflow', 'initial');
			        document.body.style.overflow = "hidden";
			        $(customControls.container).find('.directorypress-map-btn-fullscreen span').removeClass('glyphicon-fullscreen');
			        $(customControls.container).find('.directorypress-map-btn-fullscreen span').addClass('glyphicon-resize-small');
			        
			        directorypress_callMapResize(map_id);
			        directorypress_setMapCenter(map_id, center);
			        
			        
			        
			        $(window).trigger('resize');
			        if ($(".directorypress-map-listings-panel").length) {
			        	$(".directorypress-map-listings-panel").getNiceScroll().resize();
			        }
			    }
			    function closeFullScreen() {
			    	$('#directorypress-map-placeholder-'+map_id).after(mapDivParent);
			    	$('#directorypress-map-placeholder-'+map_id).detach();
			    	
			    	var elements_to_zindex = [
			                                  "#directorypress-map-search-wrapper-"+map_id,
			                                  "#directorypress-map-search-panel-wrapper-"+map_id,
			                                  "#directorypress-map-sidebar-toggle-container-"+map_id,
			        ];
			        $(elements_to_zindex).each(function() {
			        	if ($(this).length) {
			        		$(this).css('position', 'absolute').zIndex(1);
			        	}
			        });
			    	
		            if (originalPos === "") {
		                mapDiv.style.position = "relative";
		            } else {
		                mapDiv.style.position = originalPos;
		            }
		            var center = directorypress_maps[map_id].getCenter();
		            mapDiv.style.width = originalWidth;
		            mapDiv.style.height = originalHeight;
		            mapDiv.style.top = originalTop;
		            mapDiv.style.left = originalLeft;
		            mapDiv.style.zIndex = originalZIndex;
		            $(mapDiv).parent(".directorypress-maps-container").zIndex(originalZIndex).css('overflow', 'hidden');;
		            document.body.style.overflow = originalOverflow;
		            $(customControls.container).find('.directorypress-map-btn-fullscreen span').removeClass('glyphicon-resize-small');
			        $(customControls.container).find('.directorypress-map-btn-fullscreen span').addClass('glyphicon-fullscreen');

			        directorypress_callMapResize(map_id);
			        directorypress_setMapCenter(map_id, center);
		            
			        $(window).trigger('resize');
			        if ($(".directorypress-map-listings-panel").length) {
			        	$(".directorypress-map-listings-panel").getNiceScroll().resize();
			        }
			    }
			    if (enable_full_screen) {
			    	$(customControls.container).find('.directorypress-map-btn-fullscreen').on("click", function() {
				    	if (typeof directorypress_fullScreens[map_id] == "undefined" || !directorypress_fullScreens[map_id]) {
				    		$("#directorypress-maps-container-"+map_id).addClass("directorypress-map-full-screen");
				    		directorypress_fullScreens[map_id] = true;
				    		openFullScreen();
				    	} else {
				    		$("#directorypress-maps-container-"+map_id).removeClass("directorypress-map-full-screen");
				    		directorypress_fullScreens[map_id] = false;
				    		closeFullScreen();
				    	}
				    });
				    $(document).on("keyup", function(e) {
				    	if (typeof directorypress_fullScreens[map_id] != "undefined" && directorypress_fullScreens[map_id] && e.keyCode == 27) {
				    		$("#directorypress-maps-container-"+map_id).removeClass("directorypress-map-full-screen");
				    		directorypress_fullScreens[map_id] = false;
				    		closeFullScreen();
				    	}
				    });
			    }

			    if (draw_panel) {
				    var drawPanel = document.createElement('div');
				    $(drawPanel).addClass('directorypress-map-draw-panel');

				    class directorypress_dummy_control {
				    	constructor(drawPanel) {
				    		this.drawPanel = drawPanel;
				    	}
				    	onAdd(map){
						    this.map = map;

						    var customControls = document.createElement('div');
						    $(customControls).addClass('mapboxgl-ctrl directorypress-map-draw-panel');
						    customControls.appendChild(this.drawPanel);
						    
						    this.container = customControls;
						    return this.container;
						  }
						  onRemove(){
						    this.container.parentNode.removeChild(this.container);
						    this.map = undefined;
						  }
					}
					var dummyDiv = new directorypress_dummy_control(drawPanel);
					map.addControl(dummyDiv, cposition);

				    var drawButton = document.createElement('button');
				    $(drawButton)
				    .addClass('btn btn-primary directorypress-map-draw')
				    .attr("title", directorypress_maps_instance.draw_area_button)
				    .html('<span class="glyphicon glyphicon-pencil"></span>');
				    
				    drawPanel.appendChild(drawButton);
				    drawButton.map_id = map_id;
					drawButton.drawing_state = 0;
					$(drawButton).on("click", function(e) {
						var map_id = drawButton.map_id;
						if (this.drawing_state == 0) {
							this.drawing_state = 1;
							window.addEventListener('touchmove', directorypress_stop_touchmove_listener, { passive: false });
							directorypress_clearMarkers(map_id);
							directorypress_closeInfoWindow(map_id);
							directorypress_removeShapes(map_id);
		
							directorypress_enableDrawingMode(map_id);
							
							var editButton = $(directorypress_maps[map_id].getContainer()).find('.directorypress-map-edit').get(0);
							$(editButton).removeClass('btn-active');
							$(editButton).attr('disabled', 'disabled');
							$(editButton).find('.directorypress-map-edit-label').text(directorypress_maps_instance.edit_area_button);
							editButton.editing_state = 0;
		
							// remove ajax_loading and set drawing_state
							var map_attrs_array;
							if (map_attrs_array = directorypress_get_map_markers_attrs_array(map_id)) {
								map_attrs_array.map_attrs.drawing_state = 1;
								delete map_attrs_array.map_attrs.ajax_loading;
							}
			
							directorypress_maps[map_id].getCanvas().style.cursor = 'crosshair';
							$(this).toggleClass('btn-active');

							directorypress_maps[map_id].getContainer().map_id = map_id;
							
							var draw_down_event = function(e) {
								var el = e.target;
			                    do {
			                        if ($(el).hasClass('directorypress-map-draw-panel')) {
			                            return;
			                        }
			                    } while (el = el.parentNode);
								directorypress_drawFreeHandPolygon(map_id);
							};
							
							directorypress_maps[map_id].once('mousedown', draw_down_event);
							directorypress_maps[map_id].once('touchstart', draw_down_event);
						} else if (this.drawing_state == 1) {
							this.drawing_state = 0;
							window.removeEventListener('touchmove', directorypress_stop_touchmove_listener, { passive: false });
							map.getCanvas().style.cursor = '';
							$(drawButton).removeClass('btn-active');
							directorypress_disableDrawingMode(map_id);

							// repair ajax_loading and set drawing_state
							var map_attrs_array;
							if (map_attrs_array = directorypress_get_map_markers_attrs_array(map_id)) {
								map_attrs_array.map_attrs.drawing_state = 0;
								if (typeof directorypress_get_original_map_markers_attrs_array(map_id).map_attrs.ajax_loading != 'undefined' && directorypress_get_original_map_markers_attrs_array(map_id).map_attrs.ajax_loading == 1) {
									map_attrs_array.map_attrs.ajax_loading = 1;
								}
							}
						}
					});
				    
				    var editButton = document.createElement('button');
				    $(editButton)
				    .addClass('btn btn-primary directorypress-map-edit')
				    .attr("title", directorypress_maps_instance.edit_area_button)
				    .html('<span class="glyphicon glyphicon-edit"></span>')
				    .attr('disabled', 'disabled');
				    
				    drawPanel.appendChild(editButton);
				    editButton.map_id = map_id;
				    editButton.editing_state = 0;
				    $(editButton).on("click", function(e) {
				    	var map_id = editButton.map_id;
						if (this.editing_state == 0) {
							this.editing_state = 1;
							$(this).toggleClass('btn-active');
							$(this).attr("title", directorypress_maps_instance.apply_area_button);

							directorypress_removeShapes(map_id);

							var draw = new MapboxDraw({
								displayControlsDefault: false,
								styles: [
										// line stroke
										{
											"id": "gl-draw-line",
											"type": "line",
											"filter": ["all", ["==", "$type", "LineString"], ["!=", "mode", "static"]],
											"layout": {
												"line-cap": "round",
												"line-join": "round"
											},
											"paint": {
												"line-color": "#AA2143",
												"line-dasharray": [0.2, 2],
												"line-width": 1
											}
										},
										// polygon fill
										{
											"id": "gl-draw-polygon-fill",
											"type": "fill",
											"filter": ["all", ["==", "$type", "Polygon"], ["!=", "mode", "static"]],
											"paint": {
												"fill-color": "#0099FF",
												"fill-outline-color": "#AA2143",
												"fill-opacity": 0.3
											}
										},
										// vertex point halos
										{
											"id": "gl-draw-polygon-and-line-vertex-halo-active",
											"type": "circle",
											"filter": ["all", ["==", "meta", "vertex"], ["==", "$type", "Point"], ["!=", "mode", "static"]],
											"paint": {
												"circle-radius": 5,
												"circle-color": "#FFF"
											}
										},
										// vertex points
										{
											"id": "gl-draw-polygon-and-line-vertex-active",
											"type": "circle",
											"filter": ["all", ["==", "meta", "vertex"], ["==", "$type", "Point"], ["!=", "mode", "static"]],
											"paint": {
												"circle-radius": 3,
												"circle-color": "#AA2143",
											}
										}
								]
							});
							map.addControl(draw);
							draw.add(directorypress_draw_features[map_id]);
							draw.changeMode('direct_select', { featureId: directorypress_draw_features[map_id].id });

							directorypress_draws[map_id] = draw;
							
						} else if (this.editing_state == 1) {
							this.editing_state = 0;
							$(this).toggleClass('btn-active');
							$(this).attr("title", directorypress_maps_instance.edit_area_button);
							if (typeof directorypress_draws[map_id] != 'undefined' && directorypress_draws[map_id]) {
								var draw = directorypress_draws[map_id];
								draw.changeMode('simple_select', { featureId: directorypress_draw_features[map_id].id });
								directorypress_draw_features[map_id] = draw.get(directorypress_draw_features[map_id].id);
								directorypress_addPolygon(map_id);

								var geo_poly_ajax = [];
								directorypress_draw_features[map_id].geometry.coordinates[0].map(function(point_feature) {
									geo_poly_ajax.push({ 'lat': point_feature[1], 'lng': point_feature[0] });
						    	});

								if (geo_poly_ajax.length) {
									directorypress_sendGeoPolyAJAX(map_id, geo_poly_ajax);
								}
							}
							map.removeControl(directorypress_draws[map_id]);
							directorypress_draws[map_id] = false;
						}
				    });
				    
				    var reloadButton = document.createElement('button');
				    $(reloadButton)
				    .addClass('btn btn-primary directorypress-map-reload')
				    .attr("title", directorypress_maps_instance.reload_map_button)
				    .html('<span class="glyphicon glyphicon-refresh"></span>');
				    
				    drawPanel.appendChild(reloadButton);
				    reloadButton.map_id = map_id;
				    $(reloadButton).on("click", function(e) {
						var map_id = reloadButton.map_id;
						for (var i=0; i<directorypress_map_markers_attrs_array.length; i++) {
							if (directorypress_map_markers_attrs_array[i].map_id == map_id) {
								directorypress_map_markers_attrs_array[i] = JSON.parse(JSON.stringify(_directorypress_map_markers_attrs_array[i]));

								window.removeEventListener('touchmove', directorypress_stop_touchmove_listener, { passive: false });
		
								var editButton = $(directorypress_maps[map_id].getContainer()).find('.directorypress-map-edit').get(0);
								$(editButton).removeClass('btn-active');
								$(editButton).find('.directorypress-map-edit-label').text(directorypress_maps_instance.edit_area_button);
								$(editButton).attr('disabled', 'disabled');

								directorypress_disableDrawingMode(map_id);
								directorypress_clearMarkers(map_id);
								directorypress_closeInfoWindow(map_id);
								directorypress_removeShapes(map_id);
								directorypress_load_map(i);
								if (directorypress_global_markers_array[map_id].length) {
									var markers_array = [];
									var bounds = directorypress_buildBounds();
									for (var j=0; j<directorypress_global_markers_array[map_id].length; j++) {
										var marker = directorypress_global_markers_array[map_id][j];
									    directorypress_extendBounds(bounds, directorypress_getMarkerPosition(marker));
									    markers_array.push(marker);
						    		}
									directorypress_mapFitBounds(map_id, bounds);
									
									var map_attrs = directorypress_map_markers_attrs_array[i].map_attrs;
									directorypress_set_map_zoomCenter(map_id, map_attrs, markers_array);
						    	}
								break;
							}
						}
						
					});

				    if (directorypress_maps_instance.enable_my_location_button) {
				    	var locationButton = document.createElement('button');
						$(locationButton)
						.addClass('btn btn-primary directorypress-map-location')
						.attr("title", directorypress_maps_instance.my_location_button)
						.html('<span class="glyphicon glyphicon-screenshot"></span>');

						drawPanel.appendChild(locationButton);
						
						locationButton.map_id = map_id;
					    $(locationButton).on("click", function(e) {
							var map_id = locationButton.map_id;
							if (navigator.geolocation) {
						    	navigator.geolocation.getCurrentPosition(
						    		function(position) {
							    		var start_latitude = position.coords.latitude;
							    		var start_longitude = position.coords.longitude;
									    directorypress_setMapCenter(map_id, directorypress_buildPoint(start_latitude, start_longitude));
							    	},
							    	function(e) {
								   		//alert(e.message);
								    },
								   	{timeout: 10000}
							    );
							}
						});
				    }
			    }
			} // end of (!fullScreen)

		    directorypress_global_markers_array[map_id] = [];
		    directorypress_global_locations_array[map_id] = [];
		    
		    
		    if (markers_array.length) {
		    	var bounds = directorypress_buildBounds();
		    		
			    if (typeof map_attrs.ajax_markers_loading != 'undefined' && map_attrs.ajax_markers_loading == 1) {
					var is_ajax_markers = true;
			    } else {
					var is_ajax_markers = false;
			    }
		
			    var markers = [];
			    for (var j=0; j<markers_array.length; j++) {
		    		var map_coords_1 = markers_array[j][1];
				   	var map_coords_2 = markers_array[j][2];
				   	if ($.isNumeric(map_coords_1) && $.isNumeric(map_coords_2)) {
				    	var point = [map_coords_2, map_coords_1];
				    	directorypress_extendBounds(bounds, point);

		    			var location_obj = new directorypress_glocation(
		    				markers_array[j][0],  // location ID
		    				point, 
		    				markers_array[j][3],  // map icon file
		    				markers_array[j][4],  // map icon color
		    				markers_array[j][6],  // listing title
		    				markers_array[j][7],  // logo image
		    				markers_array[j][8],  // listing link
		    				markers_array[j][9],  // content fields output
		    				markers_array[j][10],  // listing link anchor
		    				markers_array[j][11], // is nofollow link
		    				show_summary_button,
		    				show_readmore_button,
		    				map_id,
		    				is_ajax_markers
			    		);
			    		var marker = location_obj.directorypress_placeMarker(map_id);
			    		markers.push(marker);
		
			    		directorypress_global_locations_array[map_id].push(location_obj);
			    	}
	    		}
			    	
			    directorypress_mapFitBounds(map_id, bounds);

			    directorypress_setClusters(enable_clusters, map_id);
			    
			    if (enable_radius_circle && typeof window['radius_params_'+map_id] != 'undefined') {
		    		var radius_params = window['radius_params_'+map_id];
					var map_radius = parseFloat(radius_params.radius_value);
					directorypress_draw_radius(radius_params, map_radius, map_id);
				}
		    }

		    directorypress_set_map_zoomCenter(map_id, map_attrs, markers_array);
		}
	}

	function directorypress_setMapAjaxListener(map, map_id, search_button_obj) {
		var search_button_obj = typeof search_button_obj !== 'undefined' ? search_button_obj : null;

		map.on('load', function() {
			directorypress_setAjaxMarkers(map, map_id, search_button_obj);
		});
		map.on('moveend', function() {
			directorypress_setAjaxMarkers(map, map_id, search_button_obj);
		});
		map.on('zoomend', function() {
			directorypress_setAjaxMarkers(map, map_id, search_button_obj);
		});
	}
	function directorypress_geocodeStartAddress(map_attrs, map_id, zoom_level) {
		var start_address = map_attrs.start_address;
		function _geocodeStartAddress(status, start_latitude, start_longitude) {
			if (status == true) {
				directorypress_set_map_zoom(map_id, zoom_level);
			    directorypress_setMapCenter(map_id, [start_longitude, start_latitude]);
			    
			    if (typeof map_attrs.ajax_loading != 'undefined' && map_attrs.ajax_loading == 1) {
				    // use closures here
				    directorypress_setMapAjaxListener(directorypress_maps[map_id], map_id);
			    }
			}
		}
		directorypress_geocodeAddress(start_address, _geocodeStartAddress);
	}
	function directorypress_geolocatePosition() {
		if (navigator.geolocation) {
			var geolocation_maps = [];
	    	for (var map_id in directorypress_maps_attrs) {
	    		if (typeof directorypress_maps_attrs[map_id].geolocation != 'undefined' && directorypress_maps_attrs[map_id].geolocation == 1) {
	    			geolocation_maps.push({ 'map': directorypress_maps[map_id], 'map_id': map_id});
	    		}
	    	}
	    	if (geolocation_maps.length) {
	    		navigator.geolocation.getCurrentPosition(
	    			function(position) {
		    			var start_latitude = position.coords.latitude;
		    			var start_longitude = position.coords.longitude;
				    	for (var i in geolocation_maps) {
				    		var map_id = geolocation_maps[i].map_id;
				    		
				    		directorypress_setMapCenter(geolocation_maps[i].map_id, [start_longitude, start_latitude]);
				    		
				    		if (typeof directorypress_maps_attrs[map_id].start_zoom != 'undefined' && directorypress_maps_attrs[map_id].start_zoom > 0) {
				    			directorypress_set_map_zoom(map_id, directorypress_maps_attrs[map_id].start_zoom);
				    		}
				    		
				    		for (var j=0; j<directorypress_map_markers_attrs_array.length; j++) {
								if (directorypress_map_markers_attrs_array[j].map_id == map_id) {
									directorypress_map_markers_attrs_array[j].map_attrs.start_latitude = start_latitude;
									directorypress_map_markers_attrs_array[j].map_attrs.start_longitude = start_longitude;
								}
				    		}
				    	}
		    		}, 
		    		function(e) {
		    			//alert(e.message);
			    	},
			    	{timeout: 10000}
		    	);
	    	}
		}
	}

	window.directorypress_setAjaxMarkers = function(map, map_id, search_button_obj) {
		var attrs_array = directorypress_get_map_markers_attrs_array(map_id);
		var map_attrs = attrs_array.map_attrs;
		var enable_radius_circle = attrs_array.enable_radius_circle;
		var enable_clusters = attrs_array.enable_clusters;
		var show_summary_button = attrs_array.show_summary_button;
		var show_readmore_button = attrs_array.show_readmore_button;
		var search_button_obj = typeof search_button_obj !== 'undefined' ? search_button_obj : null;

		var address_string = '';
		if (typeof map_attrs.address != 'undefined' && map_attrs.address) {
			var address_string = map_attrs.address;
		} else if (typeof map_attrs.location_id_text != 'undefined' && map_attrs.location_id_text) {
			var address_string = map_attrs.location_id_text;
		}
		if (address_string) {
			if (typeof directorypress_searchAddresses[map_id] == "undefined" || directorypress_searchAddresses[map_id] != address_string) {
				function _geocodeSearchAddress(status, latitude, longitude) {
					if (status == true) {
						map.panTo([longitude, latitude]);
	
						if (search_button_obj) {
							directorypress_delete_iloader_from_element(search_button_obj);
						}
						directorypress_setAjaxMarkers(map, map_id);
					}
				}
				directorypress_geocodeAddress(address_string, _geocodeSearchAddress);
				
				directorypress_searchAddresses[map_id] = address_string;
			}
		}
	
		var bounds_new = map.getBounds();
		if (bounds_new) {
			var south_west = bounds_new.getSouthWest();
			var north_east = bounds_new.getNorthEast();
		} else
			return false;
		
		function inBoundingBox(bl/*bottom left*/, tr/*top right*/, p) {
			// in case longitude 180 is inside the box
			function isLongInRange(bl, tr, p) {
				if (tr.lng < bl.lng) {
					if (p.lng >= bl.lng || p.lng <= tr.lng) {
						return true;
					}
				} else
					if (p.lng >= bl.lng && p.lng <= tr.lng) {
						return true;
					}
			}

			if (p.lat >= bl.lat  &&  p.lat <= tr.lat  &&  isLongInRange(bl, tr, p)) {
				return true;
			} else {
				return false;
			}
		}
	
		if (typeof map_attrs.swLat != 'undefined' && typeof map_attrs.swLng != 'undefined' && typeof map_attrs.neLat != 'undefined' && typeof map_attrs.neLng != 'undefined') {
			var sw_point = new mapboxgl.LngLat(map_attrs.swLng, map_attrs.swLat);
		    var ne_point = new mapboxgl.LngLat(map_attrs.neLng, map_attrs.neLat);

		    var worldCoordinate_new = map.project(sw_point);
		    var worldCoordinate_old = map.project(south_west);
		    if (
		    	(inBoundingBox(sw_point, ne_point, south_west) && inBoundingBox(sw_point, ne_point, north_east))
		    	||
			    	(140 > Math.abs(Math.floor(worldCoordinate_new.x) - Math.floor(worldCoordinate_old.x))
			    	&&
			    	140 > Math.abs(Math.floor(worldCoordinate_new.y) - Math.floor(worldCoordinate_old.y)))
		    )
		    	return false;
		}
		map_attrs.swLat = south_west.lat;
		map_attrs.swLng = south_west.lng;
		map_attrs.neLat = north_east.lat;
		map_attrs.neLng = north_east.lng;
		
		directorypress_ajax_loader_target_show($('#directorypress-maps-canvas-'+map_id));
	
		var ajax_params = {};
		for (var attrname in map_attrs) {
			if (attrname != 'start_latitude' && attrname != 'start_longitude') {
				ajax_params[attrname] = map_attrs[attrname];
			}
		}
		ajax_params.action = 'directorypress_get_map_markers';
		ajax_params.hash = map_id;

		var listings_args_array;
		if (listings_args_array = directorypress_get_handler_args_array(map_id)) {
			ajax_params.hide_order = listings_args_array.hide_order;
			ajax_params.hide_count = listings_args_array.hide_count;
			ajax_params.hide_paginator = listings_args_array.hide_paginator;
			ajax_params.show_views_switcher = listings_args_array.show_views_switcher;
			ajax_params.listings_view_type = listings_args_array.listings_view_type;
			ajax_params.listings_view_grid_columns = listings_args_array.listings_view_grid_columns;
			ajax_params.listing_thumb_width = listings_args_array.listing_thumb_width;
			ajax_params.wrap_logo_list_view = listings_args_array.wrap_logo_list_view;
			ajax_params.logo_animation_effect = listings_args_array.logo_animation_effect;
			ajax_params.grid_view_logo_ratio = listings_args_array.grid_view_logo_ratio;
			ajax_params.scrolling_paginator = listings_args_array.scrolling_paginator;
			ajax_params.perpage = listings_args_array.perpage;
			ajax_params.onepage = listings_args_array.onepage;
			ajax_params.order = listings_args_array.order;
			ajax_params.order_by = listings_args_array.order_by;
			ajax_params.base_url = listings_args_array.base_url;
	
			directorypress_ajax_loader_target_show($('#directorypress-handler-'+map_id));
		} else
			ajax_params.without_listings = 1;
		
		if ($("#directorypress-map-listings-panel-"+map_id).length) {
			ajax_params.map_listings = 1;
			directorypress_ajax_loader_target_show($("#directorypress-map-search-panel-wrapper-"+map_id));
		}
	
		$.ajax({
			type: "POST",
			url: directorypress_js_instance.ajaxurl,
			data: ajax_params,
			dataType: 'json',
			success: function(response_from_the_action_function) {
				if (response_from_the_action_function) {
					var responce_hash = response_from_the_action_function.hash;
	
					if (response_from_the_action_function.html) {
						var listings_block = $('#directorypress-handler-'+responce_hash);
						listings_block.replaceWith(response_from_the_action_function.html);
						directorypress_ajax_loader_target_hide('directorypress-handler-'+responce_hash);
					}
					
					var map_listings_block = $('#directorypress-map-listings-panel-'+responce_hash);
			    	if (map_listings_block.length) {
			    		map_listings_block.html(response_from_the_action_function.map_listings);
			    		directorypress_ajax_loader_target_hide('directorypress-map-search-panel-wrapper-'+responce_hash);
			    	}
	
					directorypress_clearMarkers(map_id);
					directorypress_removeShapes(map_id);

					if (typeof map_attrs.ajax_markers_loading != 'undefined' && map_attrs.ajax_markers_loading == 1)
						var is_ajax_markers = true;
					else
						var is_ajax_markers = false;
		
					var markers_array = response_from_the_action_function.map_markers;
					directorypress_global_locations_array[map_id] = [];
			    	for (var j=0; j<markers_array.length; j++) {
		    			var map_coords_1 = markers_array[j][1];
				    	var map_coords_2 = markers_array[j][2];
				    	if ($.isNumeric(map_coords_1) && $.isNumeric(map_coords_2)) {
			    			var point = directorypress_buildPoint(map_coords_1, map_coords_2);
	
			    			var location_obj = new directorypress_glocation(markers_array[j][0], point, 
			    				markers_array[j][3],
			    				markers_array[j][4],
			    				markers_array[j][6],
			    				markers_array[j][7],
			    				markers_array[j][8],
			    				markers_array[j][9],
			    				markers_array[j][10],
			    				markers_array[j][11],
			    				show_summary_button,
			    				show_readmore_button,
			    				map_id,
			    				is_ajax_markers
				    		);
				    		var marker = location_obj.directorypress_placeMarker(map_id);
	
				    		directorypress_global_locations_array[map_id].push(location_obj);
				    	}
		    		}
			    	directorypress_setClusters(enable_clusters, map_id, directorypress_global_markers_array[map_id]);

			    	if (enable_radius_circle && typeof response_from_the_action_function.radius_params != 'undefined') {
			    		var radius_params = response_from_the_action_function.radius_params;
						var map_radius = parseFloat(radius_params.radius_value);
						directorypress_draw_radius(radius_params, map_radius, responce_hash);
					}
				}
			},
			complete: directorypress_completeAJAXSearchOnMap(map_id, search_button_obj)
		});
	}
	var directorypress_completeAJAXSearchOnMap = function(map_id, search_button_obj) {
		return function() {
			directorypress_ajax_loader_target_hide("directorypress-handler-"+map_id);
			directorypress_ajax_loader_target_hide("directorypress-maps-canvas-"+map_id);
			directorypress_equalColumnsHeight();
			if (search_button_obj) {
				directorypress_delete_iloader_from_element(search_button_obj);
			}
		}
	}
	window.directorypress_draw_radius = function(radius_params, map_radius, map_id) {
		if (radius_params.dimension == 'miles')
			map_radius *= 1.609344;
		var map_coords_1 = radius_params.map_coords_1;
		var map_coords_2 = radius_params.map_coords_2;

		if ($.isNumeric(map_coords_1) && $.isNumeric(map_coords_2)) {
			var map = directorypress_maps[map_id];
			 map.on('load', function() {
			      map.addSource("source-circle-"+map_id, {
			        "type": "geojson",
			        "data": {
			          "type": "FeatureCollection",
			          "features": [{
			            "type": "Feature",
			            "geometry": {
			              "type": "Point",
			              "coordinates": [map_coords_2, map_coords_1]
			            }
			          }]
			        }
			      });
	
			      const metersToPixelsAtMaxZoom = (meters, latitude) =>
			      meters / 0.075 / Math.cos(latitude * Math.PI / 180)
			      
			      map.addLayer({
			        "id": "radius-circle-"+map_id,
			        "type": "circle",
			        "source": "source-circle-"+map_id,
			        "paint": {
			        	"circle-radius": {
			        		  stops: [
			        		    [0, 0],
			        		    [20, metersToPixelsAtMaxZoom(map_radius*1000, map_coords_1)]
			        		  ],
			        		  base: 2
			        		},
			          "circle-color": "#FF0000",
			          "circle-opacity": 0.1,
			          "circle-stroke-width": 1,
			          "circle-stroke-color": "#FF0000",
			          "circle-stroke-opacity": 0.25
			        }
			      });
			      
			      directorypress_drawCircles[map_id] = true;
			    });
		}
	}
	mapboxgl.Map.prototype.panToWithOffset = function(lnglat, offsetX, offsetY) {
		var map = this;
		var aPoint = map.project(lnglat);
		aPoint.x = aPoint.x+offsetX;
		aPoint.y = aPoint.y+offsetY;
		map.panTo(map.unproject(aPoint));
	};
	window.directorypress_placeMarker = function(location, map_id) {
		if (directorypress_maps_instance.map_markers_type != 'icons') {
			if (directorypress_maps_instance.global_map_icons_path != '') {
				var re = /(?:\.([^.]+))?$/;
				var icon_file = location.map_icon_file;
				
				var el = $("<div>", {
					id: 'marker-id-'+location.id,
					style: 'background-image: url('+icon_file+');',
					class: 'directorypress-mapbox-marker '
				});
				var marker_div = el[0];
				
				var marker_options = {
						anchor: 'bottom',
						element: marker_div
				};
				
				var marker = new mapboxgl.Marker(marker_options)
	    		.setLngLat(location.point)
	    		.addTo(directorypress_maps[map_id]);
			} else {
				var marker = new mapboxgl.Marker()
	    		.setLngLat(location.point)
	    		.addTo(directorypress_maps[map_id]);
			}
		} else {
			
			var map_marker_color = location.map_icon_color;
			

			//if (typeof location.map_icon_file == 'string' && location.map_icon_file.indexOf("directorypress-fa-") != -1) {
				var map_marker_icon = '<span class="directorypress-map-marker-icon '+location.map_icon_file+'" style="color: '+map_marker_color+';"></span>';
			//	var map_marker_class = 'directorypress-map-marker';
			

			var el = $("<div>", {
				id: 'marker-id-'+location.id,
				class: 'directorypress-mapbox-marker',
				html: '<div class="directorypress-map-marker" style="background: '+map_marker_color+' none repeat scroll 0 0;">'+map_marker_icon+'</div>'
			});
			var marker_div = el[0];
			
			var marker_options = {
				anchor: 'bottom',
				offset: [0, -20],
				element: marker_div
			};

			var marker = new mapboxgl.Marker(marker_options)
    		.setLngLat(location.point)
    		.addTo(directorypress_maps[map_id]);
		}
		
		directorypress_global_markers_array[map_id].push(marker);

		marker_div.addEventListener('click', function() {
			var attrs_array = directorypress_get_map_markers_attrs_array(map_id);
			if (attrs_array.center_map_onclick) {
				var map_attrs = attrs_array.map_attrs;
				if (typeof map_attrs.ajax_loading == 'undefined' || map_attrs.ajax_loading == 0) {
					//directorypress_maps[map_id].panTo(marker.getLngLat());
					directorypress_maps[map_id].panToWithOffset(marker.getLngLat(), 0, -100);
					directorypress_setInfoWindow(location, marker, map_id, 'bottom', 'onmarkerclick');
				}
			} else {
				directorypress_setInfoWindow(location, marker, map_id, '', 'onmarkerclick');
			}
			
			if ($('#directorypress-map-listings-panel-'+map_id).length) {
				if ($('#directorypress-map-listings-panel-'+map_id+' #post-'+location.id).length) {
					$('#directorypress-map-listings-panel-'+map_id).animate({scrollTop: $('#directorypress-map-listings-panel-'+map_id).scrollTop() + $('#directorypress-map-listings-panel-'+map_id+' #post-'+location.id).position().top}, 'fast');
				}
			}

			
		});
		//var listing = $('.marker-'+location.id);
		//var target_marker = $('#marker-id-'+location.id);
		//alert('test');
		//listing.hover(function() {
			//alert('test');
			//var attrs_array = directorypress_get_map_markers_attrs_array(map_id);
			//if (attrs_array.center_map_onclick) {
				//var map_attrs = attrs_array.map_attrs;
				//if (typeof map_attrs.ajax_loading == 'undefined' || map_attrs.ajax_loading == 0) {
					//directorypress_maps[map_id].panTo(marker.getLngLat());
					//directorypress_maps[map_id].panToWithOffset(marker.getLngLat(), 0, -100);
					//directorypress_maps[map_id]({center: [-74.5, 40],zoom: 9});
					//if(target_marker.hasClass('active')){
						//target_marker.removeClass("active").addClass('normal');
					//}else{
						//target_marker.removeClass("normal").addClass('active');
				//	}
					//target_marker.toggleClass("active normal");
					//$('#marker-id-'+location.id+' .directorypress-map-marker').addClass;
					//directorypress_maps[map_id].zoomIn({duration: 1000});
					//directorypress_setInfoWindow(location, marker, map_id, 'bottom', 'onmarkerclick');
				//}
			//} else {
				//directorypress_setInfoWindow(location, marker, map_id, '', 'onmarkerclick');
			//}

			
		//});
		return marker;
	}
	
	window.directorypress_setInfoWindow = function(location, marker, map_id, anchor, event) {
		if (!location.is_ajax_markers) {
			directorypress_showInfoWindow(location, marker, map_id, anchor, event);
		} else {
			directorypress_ajax_loader_target_show($('#directorypress-maps-canvas-'+map_id));

			var post_data = {'location_id': location.id, 'action': 'directorypress_get_map_marker_info'};
			$.ajax({
	    		type: "POST",
	    		url: directorypress_js_instance.ajaxurl,
	    		data: eval(post_data),
	    		dataType: 'json',
	    		success: function(response_from_the_action_function) {
	    			var marker_array = response_from_the_action_function;
	    			var map_coords_1 = marker_array[1];
			    	var map_coords_2 = marker_array[2];
			    	if ($.isNumeric(map_coords_1) && $.isNumeric(map_coords_2)) {
		    			var point = directorypress_buildPoint(map_coords_1, map_coords_2);

		    			var new_location_obj = new directorypress_glocation(marker_array[0], point, 
		    				marker_array[3],
		    				marker_array[4],
		    				marker_array[6],
		    				marker_array[7],
		    				marker_array[8],
		    				marker_array[9],
		    				marker_array[10],
		    				marker_array[11],
		    				location.show_summary_button,
		    				location.show_readmore_button,
		    				map_id,
		    				true
			    		);
		    			directorypress_showInfoWindow(new_location_obj, marker, map_id, anchor, 'onbuttonclick');
			    	}
	    		},
	    		complete: function() {
					directorypress_ajax_loader_target_hide("directorypress-maps-canvas-"+map_id);
				}
			});
		}
	}
	
	// This function builds info Window and shows it hiding another
	function directorypress_showInfoWindow(directorypress_glocation, marker, map_id, anchor, event) {
		// we use global infoWindow, not to close/open it - just to set new content (in order to prevent blinking)
	    if (typeof directorypress_infoWindows[map_id] != 'undefined' && directorypress_infoWindows[map_id]) {
	    	directorypress_infoWindows[map_id].remove();
	    	directorypress_infoWindows[map_id] = false;
	    }
	    
		if (directorypress_glocation.nofollow)
			var nofollow = 'rel="nofollow"';
		else
			var nofollow = '';
	
		var windowHtml = '<div class="directorypress-map-info-window">';

		if (directorypress_glocation.listing_logo) {
			windowHtml += '<div class="directorypress-map-info-window-logo" style="width: 250px;height: 150px;overflow:hidden;">';
				windowHtml += '<span class="directorypress-close-info-window fas fa-times" onClick="directorypress_infoWindows[&quot;' + map_id + '&quot;].remove();"></span>';
			if (directorypress_glocation.listing_url && directorypress_glocation.show_readmore_button)
				windowHtml += '<a href="' + directorypress_glocation.listing_url + '" ' + nofollow + '>';
			windowHtml += directorypress_glocation.listing_logo;
			if (directorypress_glocation.listing_url && directorypress_glocation.show_readmore_button)
				windowHtml += '</a>';
			
			if ((directorypress_glocation.show_summary_button && $("#"+directorypress_glocation.anchor).length) || (directorypress_glocation.listing_url && directorypress_glocation.show_readmore_button)) {
				if (!(directorypress_glocation.show_summary_button && $("#"+directorypress_glocation.anchor).length) || !(directorypress_glocation.listing_url && directorypress_glocation.show_readmore_button))
					var button_class = 'directorypress-map-info-window-buttons-single';
				else
					var button_class = 'directorypress-map-info-window-buttons';
		
				windowHtml += '<div class="' + button_class + ' clearfix">';
				if (directorypress_glocation.show_summary_button && $("#"+directorypress_glocation.anchor).length)
					windowHtml += '<a href="javascript:void(0);" class="simptip simptip-position-top simptip-movable  directorypress-scroll-to-listing" data-tooltip="' + directorypress_maps_instance.directorypress_map_info_window_button_summary + '"onClick="directorypress_scrollToListing(&quot;' + directorypress_glocation.anchor + '&quot;, &quot;' + map_id + '&quot;);"><i class="far fa-eye"></i></a>';
				if (directorypress_glocation.listing_url && directorypress_glocation.show_readmore_button)
					windowHtml += '<a href="' +  directorypress_glocation.listing_url + '" ' + nofollow + ' class="simptip simptip-position-top simptip-movable" data-tooltip="' + directorypress_maps_instance.directorypress_map_info_window_button_readmore + '"><i class="fas fa-external-link-alt"></i></a>';
				windowHtml += '</div>';
			}
			windowHtml += '</div>';
		}
		windowHtml += '<div class="directorypress-map-info-window-title">';
		if (directorypress_glocation.listing_url && directorypress_glocation.show_readmore_button)
			windowHtml += '<a class="directorypress-map-info-window-title-link" href="' + directorypress_glocation.listing_url + '" ' + nofollow + '>';
		windowHtml += directorypress_glocation.listing_title;
		if (directorypress_glocation.listing_url && directorypress_glocation.show_readmore_button)
			windowHtml += '</a>';
		windowHtml += '</div>';
		windowHtml += '<div class="directorypress-map-info-window-content clearfix">';
		if (directorypress_glocation.fields) {
			for (var i=0; i<directorypress_glocation.fields.length; i++) {
				if (directorypress_glocation.fields[i]) {
					windowHtml += '<div class="directorypress-map-info-window-field">';
					if (directorypress_maps_instance.directorypress_map_fields_icons[i])
						windowHtml += '<span class="directorypress-map-field-icon ' + directorypress_maps_instance.directorypress_map_fields_icons[i] + '"></span>';
					windowHtml += directorypress_glocation.fields[i];
					windowHtml += '</div>';
				}
			}
		}
		windowHtml += '</div>';
		
		var tongue_pos = (135);
	
		windowHtml += '<div class="directorypress-map-info-window-tongue"></div>';
		windowHtml += '</div>';

		var options = {
				offset: {'bottom': [0,-75]},
				closeOnClick: false,
				anchor: anchor
		};
		var popup = new mapboxgl.Popup(options)
		.setHTML(windowHtml)
		.addTo(directorypress_maps[map_id]);

		marker.setPopup(popup);
		// This is needed workaround, otherwise it will not open infoWindow on "On map" button click due to popup.addTo(directorypress_maps[map_id])
		if (event == 'onmarkerclick') {
			marker.addTo(directorypress_maps[map_id]);
		}
		
		directorypress_infoWindows[map_id] = popup;
	}

	window.directorypress_scrollToListing = function(anchor, map_id) {
		var scroll_to_anchor = $("#"+anchor);
		var has_sticky_scroll_toppadding = 0;
		if (typeof window["directorypress_has_sticky_scroll_toppadding_"+map_id] != 'undefined') {
			has_sticky_scroll_toppadding = window["directorypress_has_sticky_scroll_toppadding_"+map_id];
		}

		if (scroll_to_anchor.length) {
			$('html,body').animate({scrollTop: scroll_to_anchor.position().top - has_sticky_scroll_toppadding}, 'fast');
		}
	}

	window.directorypress_setClusters = function(enable_clusters, map_id) {
		if (enable_clusters) {
			var map = directorypress_maps[map_id],
			clusters = {},
			markers = [],
			clustersGeojson = {};

			var displayFeatures = function (features) {
				if (directorypress_global_locations_array[map_id].length) {
		            $.each(directorypress_global_locations_array[map_id], function (i, marker) {
		            	$("#marker-id-"+directorypress_global_locations_array[map_id][i].id).hide();
		            });
				}

				$.each(features, function (i, feature) {
					var isCluster = (!!feature.properties.cluster) ? true : false,
						$feature;

					if (isCluster) {
						var count = feature.properties.point_count,
							className;
						if (count > 1) {
							className = 'directorypress-mapbox-cluster-m1';
						} else if (count > 10) {
							className = 'directorypress-mapbox-cluster-m2';
						} else if (count > 20) {
							className = 'directorypress-mapbox-cluster-m3';
						} else if (count > 30) {
							className = 'directorypress-mapbox-cluster-m4';
						} else if (count > 50) {
							className = 'directorypress-mapbox-cluster-m5';
						}

						$feature = $('<div class="directorypress-mapbox-cluster ' + className + '" tabindex="0">' + feature.properties.point_count_abbreviated + '</div>');
						clusters[feature.properties.cluster_id] = new mapboxgl.Marker($feature[0]).setLngLat(feature.geometry.coordinates).addTo(map);
						
						$feature.on("click", function() {
							var cluster_coords = feature.geometry.coordinates;
							var cluster_zoom = clusterIndex.getClusterExpansionZoom(feature.properties.cluster_id);
							map.flyTo({ 
								center: cluster_coords,
								zoom: cluster_zoom
							});
						});
					} else {
						$("#marker-id-"+feature.location_id).show();
					}
				});
			};

			var updateClusters = function () {
				var bounds = map.getBounds(),
					zoom = map.getZoom();

				clustersGeojson = clusterIndex.getClusters([
					bounds.getWest(),
					bounds.getSouth(),
					bounds.getEast(),
					bounds.getNorth()
				], Math.floor(zoom));

				if (Object.keys(clusters).length) {
					$.each(clusters, function (i, cluster) {
						cluster.remove();
					});
				}

				displayFeatures(clustersGeojson);
			};

			var feature_collection = [];
			for (var j=0; j<directorypress_global_locations_array[map_id].length; j++) {
				feature_collection.push({
					"type": "Feature",
					"properties": {},
					"geometry": {
						"type": "Point",
						"coordinates": directorypress_global_locations_array[map_id][j].point
					},
					"location_id": directorypress_global_locations_array[map_id][j].id
				});
			}

			var clusterIndex = supercluster({
				maxZoom: 20
			});
			clusterIndex.load(feature_collection);
			map.on('moveend', updateClusters);
			updateClusters();

			directorypress_markerClusters[map_id] = clusters;
		}
	}
	window.directorypress_clearMarkers = function(map_id) {
		if (typeof directorypress_markerClusters[map_id] != 'undefined') {
			for (var i = 0; i < directorypress_markerClusters[map_id].length; i++) {
				directorypress_markerClusters[map_id][i].remove();
			};
		}
	
		if (directorypress_global_markers_array[map_id]) {
			for (var i = 0; i < directorypress_global_markers_array[map_id].length; i++) {
				directorypress_global_markers_array[map_id][i].remove();
			}
		}
		directorypress_global_markers_array[map_id] = [];
		directorypress_global_locations_array[map_id] = [];
	}
	window.directorypress_removeShapes = function(map_id) {
		if (typeof directorypress_drawCircles[map_id] != 'undefined' && directorypress_drawCircles[map_id]) {
			directorypress_maps[map_id].removeLayer('radius-circle-'+map_id);
			directorypress_maps[map_id].removeSource('radius-circle-'+map_id);
			directorypress_drawCircles[map_id] = false;
		}

		if (typeof directorypress_polygons[map_id] != 'undefined' && directorypress_polygons[map_id]) {
			directorypress_maps[map_id].removeLayer('geo-poly-'+map_id);
			directorypress_maps[map_id].removeSource('geo-poly-'+map_id);
			directorypress_polygons[map_id] = false;
		}
		
		if (typeof directorypress_draws[map_id] != 'undefined' && directorypress_draws[map_id]) {
			directorypress_maps[map_id].removeControl(directorypress_draws[map_id]);
			directorypress_draws[map_id] = false;
		}
	}
	window.directorypress_setZoomCenter = function(map) {
		var zoom = map.getZoom();
		var center = map.getCenter();
		map.resize();
		map.setZoom(zoom);
		map.setCenter(center);
	}

	window.directorypress_geocodeField = function(field, error_message) {
		if (navigator.geolocation) {
			navigator.geolocation.getCurrentPosition(
				function(position) {
					$.get("https://api.mapbox.com/geocoding/v5/mapbox.places/"+position.coords.longitude + ',' + position.coords.latitude+".json?access_token="+directorypress_maps_instance.mapbox_api_key, function(data) {
						if (data.features.length) {
							field.val(data.features[0].place_name);
							field.trigger('change');
						}
					});
			    },
			    function(e) {
			    	//alert(e.message);
		    	},
			    {enableHighAccuracy: true, timeout: 10000, maximumAge: 0}
		    );
		} else{
			alert(error_message);
		}
		
	}
})(jQuery);

// supercluster.js
//https://unpkg.com/supercluster@3.0.2/dist/supercluster.min.js
!function(t){if("object"==typeof exports&&"undefined"!=typeof module)module.exports=t();else if("function"==typeof define&&define.amd)define([],t);else{("undefined"!=typeof window?window:"undefined"!=typeof global?global:"undefined"!=typeof self?self:this).supercluster=t()}}(function(){return function t(n,o,e){function r(s,u){if(!o[s]){if(!n[s]){var a="function"==typeof require&&require;if(!u&&a)return a(s,!0);if(i)return i(s,!0);var h=new Error("Cannot find module '"+s+"'");throw h.code="MODULE_NOT_FOUND",h}var p=o[s]={exports:{}};n[s][0].call(p.exports,function(t){var o=n[s][1][t];return r(o||t)},p,p.exports,t,n,o,e)}return o[s].exports}for(var i="function"==typeof require&&require,s=0;s<e.length;s++)r(e[s]);return r}({1:[function(t,n,o){"use strict";function e(t){return new r(t)}function r(t){this.options=h(Object.create(this.options),t),this.trees=new Array(this.options.maxZoom+1)}function i(t){return{type:"Feature",properties:s(t),geometry:{type:"Point",coordinates:[function(t){return 360*(t-.5)}(t.x),function(t){var n=(180-360*t)*Math.PI/180;return 360*Math.atan(Math.exp(n))/Math.PI-90}(t.y)]}}}function s(t){var n=t.numPoints,o=n>=1e4?Math.round(n/1e3)+"k":n>=1e3?Math.round(n/100)/10+"k":n;return h(h({},t.properties),{cluster:!0,cluster_id:t.id,point_count:n,point_count_abbreviated:o})}function u(t){return t/360+.5}function a(t){var n=Math.sin(t*Math.PI/180),o=.5-.25*Math.log((1+n)/(1-n))/Math.PI;return o<0?0:o>1?1:o}function h(t,n){for(var o in n)t[o]=n[o];return t}function p(t){return t.x}function f(t){return t.y}var c=t("kdbush");n.exports=e,n.exports.default=e,r.prototype={options:{minZoom:0,maxZoom:16,radius:40,extent:512,nodeSize:64,log:!1,reduce:null,initial:function(){return{}},map:function(t){return t}},load:function(t){var n=this.options.log;n&&console.time("total time");var o="prepare "+t.length+" points";n&&console.time(o),this.points=t;for(var e=[],r=0;r<t.length;r++)t[r].geometry&&e.push(function(t,n){var o=t.geometry.coordinates;return{x:u(o[0]),y:a(o[1]),zoom:1/0,id:n,parentId:-1}}(t[r],r));this.trees[this.options.maxZoom+1]=c(e,p,f,this.options.nodeSize,Float32Array),n&&console.timeEnd(o);for(var i=this.options.maxZoom;i>=this.options.minZoom;i--){var s=+Date.now();e=this._cluster(e,i),this.trees[i]=c(e,p,f,this.options.nodeSize,Float32Array),n&&console.log("z%d: %d clusters in %dms",i,e.length,+Date.now()-s)}return n&&console.timeEnd("total time"),this},getClusters:function(t,n){if(t[0]>t[2]){var o=this.getClusters([t[0],t[1],180,t[3]],n),e=this.getClusters([-180,t[1],t[2],t[3]],n);return o.concat(e)}for(var r=this.trees[this._limitZoom(n)],s=r.range(u(t[0]),a(t[3]),u(t[2]),a(t[1])),h=[],p=0;p<s.length;p++){var f=r.points[s[p]];h.push(f.numPoints?i(f):this.points[f.id])}return h},getChildren:function(t){var n=t>>5,o=t%32,e="No cluster with the specified id.",r=this.trees[o];if(!r)throw new Error(e);var s=r.points[n];if(!s)throw new Error(e);for(var u=this.options.radius/(this.options.extent*Math.pow(2,o-1)),a=r.within(s.x,s.y,u),h=[],p=0;p<a.length;p++){var f=r.points[a[p]];f.parentId===t&&h.push(f.numPoints?i(f):this.points[f.id])}if(0===h.length)throw new Error(e);return h},getLeaves:function(t,n,o){n=n||10,o=o||0;var e=[];return this._appendLeaves(e,t,n,o,0),e},getTile:function(t,n,o){var e=this.trees[this._limitZoom(t)],r=Math.pow(2,t),i=this.options.extent,s=this.options.radius/i,u=(o-s)/r,a=(o+1+s)/r,h={features:[]};return this._addTileFeatures(e.range((n-s)/r,u,(n+1+s)/r,a),e.points,n,o,r,h),0===n&&this._addTileFeatures(e.range(1-s/r,u,1,a),e.points,r,o,r,h),n===r-1&&this._addTileFeatures(e.range(0,u,s/r,a),e.points,-1,o,r,h),h.features.length?h:null},getClusterExpansionZoom:function(t){for(var n=t%32-1;n<this.options.maxZoom;){var o=this.getChildren(t);if(n++,1!==o.length)break;t=o[0].properties.cluster_id}return n},_appendLeaves:function(t,n,o,e,r){for(var i=this.getChildren(n),s=0;s<i.length;s++){var u=i[s].properties;if(u&&u.cluster?r+u.point_count<=e?r+=u.point_count:r=this._appendLeaves(t,u.cluster_id,o,e,r):r<e?r++:t.push(i[s]),t.length===o)break}return r},_addTileFeatures:function(t,n,o,e,r,i){for(var u=0;u<t.length;u++){var a=n[t[u]];i.features.push({type:1,geometry:[[Math.round(this.options.extent*(a.x*r-o)),Math.round(this.options.extent*(a.y*r-e))]],tags:a.numPoints?s(a):this.points[a.id].properties})}},_limitZoom:function(t){return Math.max(this.options.minZoom,Math.min(t,this.options.maxZoom+1))},_cluster:function(t,n){for(var o=[],e=this.options.radius/(this.options.extent*Math.pow(2,n)),r=0;r<t.length;r++){var i=t[r];if(!(i.zoom<=n)){i.zoom=n;var s=this.trees[n+1],u=s.within(i.x,i.y,e),a=i.numPoints||1,h=i.x*a,p=i.y*a,f=null;this.options.reduce&&(f=this.options.initial(),this._accumulate(f,i));for(var c=(r<<5)+(n+1),l=0;l<u.length;l++){var d=s.points[u[l]];if(!(d.zoom<=n)){d.zoom=n;var m=d.numPoints||1;h+=d.x*m,p+=d.y*m,a+=m,d.parentId=c,this.options.reduce&&this._accumulate(f,d)}}1===a?o.push(i):(i.parentId=c,o.push(function(t,n,o,e,r){return{x:t,y:n,zoom:1/0,id:o,parentId:-1,numPoints:e,properties:r}}(h/a,p/a,c,a,f)))}}return o},_accumulate:function(t,n){var o=n.numPoints?n.properties:this.options.map(this.points[n.id].properties);this.options.reduce(t,o)}}},{kdbush:2}],2:[function(t,n,o){"use strict";function e(t,n,o,e,i){n=n||function(t){return t[0]},o=o||function(t){return t[1]},i=i||Array,this.nodeSize=e||64,this.points=t,this.ids=new i(t.length),this.coords=new i(2*t.length);for(var s=0;s<t.length;s++)this.ids[s]=s,this.coords[2*s]=n(t[s]),this.coords[2*s+1]=o(t[s]);r(this.ids,this.coords,this.nodeSize,0,this.ids.length-1,0)}var r=t("./sort"),i=t("./range"),s=t("./within");n.exports=function(t,n,o,r,i){return new e(t,n,o,r,i)},e.prototype={range:function(t,n,o,e){return i(this.ids,this.coords,t,n,o,e,this.nodeSize)},within:function(t,n,o){return s(this.ids,this.coords,t,n,o,this.nodeSize)}}},{"./range":3,"./sort":4,"./within":5}],3:[function(t,n,o){"use strict";n.exports=function(t,n,o,e,r,i,s){for(var u,a,h=[0,t.length-1,0],p=[];h.length;){var f=h.pop(),c=h.pop(),l=h.pop();if(c-l<=s)for(var d=l;d<=c;d++)u=n[2*d],a=n[2*d+1],u>=o&&u<=r&&a>=e&&a<=i&&p.push(t[d]);else{var m=Math.floor((l+c)/2);u=n[2*m],a=n[2*m+1],u>=o&&u<=r&&a>=e&&a<=i&&p.push(t[m]);var v=(f+1)%2;(0===f?o<=u:e<=a)&&(h.push(l),h.push(m-1),h.push(v)),(0===f?r>=u:i>=a)&&(h.push(m+1),h.push(c),h.push(v))}}return p}},{}],4:[function(t,n,o){"use strict";function e(t,n,o,i,s,u){if(!(s-i<=o)){var a=Math.floor((i+s)/2);r(t,n,a,i,s,u%2),e(t,n,o,i,a-1,u+1),e(t,n,o,a+1,s,u+1)}}function r(t,n,o,e,s,u){for(;s>e;){if(s-e>600){var a=s-e+1,h=o-e+1,p=Math.log(a),f=.5*Math.exp(2*p/3),c=.5*Math.sqrt(p*f*(a-f)/a)*(h-a/2<0?-1:1);r(t,n,o,Math.max(e,Math.floor(o-h*f/a+c)),Math.min(s,Math.floor(o+(a-h)*f/a+c)),u)}var l=n[2*o+u],d=e,m=s;for(i(t,n,e,o),n[2*s+u]>l&&i(t,n,e,s);d<m;){for(i(t,n,d,m),d++,m--;n[2*d+u]<l;)d++;for(;n[2*m+u]>l;)m--}n[2*e+u]===l?i(t,n,e,m):i(t,n,++m,s),m<=o&&(e=m+1),o<=m&&(s=m-1)}}function i(t,n,o,e){s(t,o,e),s(n,2*o,2*e),s(n,2*o+1,2*e+1)}function s(t,n,o){var e=t[n];t[n]=t[o],t[o]=e}n.exports=e},{}],5:[function(t,n,o){"use strict";function e(t,n,o,e){var r=t-o,i=n-e;return r*r+i*i}n.exports=function(t,n,o,r,i,s){for(var u=[0,t.length-1,0],a=[],h=i*i;u.length;){var p=u.pop(),f=u.pop(),c=u.pop();if(f-c<=s)for(var l=c;l<=f;l++)e(n[2*l],n[2*l+1],o,r)<=h&&a.push(t[l]);else{var d=Math.floor((c+f)/2),m=n[2*d],v=n[2*d+1];e(m,v,o,r)<=h&&a.push(t[d]);var g=(p+1)%2;(0===p?o-i<=m:r-i<=v)&&(u.push(c),u.push(d-1),u.push(g)),(0===p?o+i>=m:r+i>=v)&&(u.push(d+1),u.push(f),u.push(g))}}return a}},{}]},{},[1])(1)});