(function( $ ) {
	'use strict';
	
	$(function() {
		directorypress_my_location_buttons();
	});

	window.directorypress_my_location_buttons = function() {
		if (directorypress_maps_instance.enable_my_location_button) {
			$(".directorypress-mylocation").attr("title", directorypress_maps_instance.my_location_button);
			$("body").on("click", ".directorypress-mylocation", function() {
				if (!$(this).hasClass('directorypress-search-input-reset')) {
					var input = $(this).parent().find("input");
					directorypress_geocodeField(input, directorypress_maps_instance.my_location_button_error);
				}
			});
		}
	}

})( jQuery );
