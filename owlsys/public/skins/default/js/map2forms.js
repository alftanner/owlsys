function loadGMap(lat, lng, zoom) {
	//var myLatlng = new google.maps.LatLng(-8.111830376461585, -79.0286901204833);
	//latitud = typeof(lat) != 'undefined' ? lat : -8.111830376461585;
	//longitud = typeof(lng) != 'undefined' ? lat : -79.0286901204833;
	lat = lat || -8.10750109707859; // Trujillo por defecto
	lng = lng || -79.0286901204833;
	zoom = zoom || 12;
	
	jQuery('#lat').val( lat );
	jQuery('#lng').val( lng );
	document.getElementById('zoom').value = zoom;
	
	var myLatlng = new google.maps.LatLng(lat, lng);
	var myOptions = {
		zoom : zoom,
		center : myLatlng,
		mapTypeId : google.maps.MapTypeId.HYBRID
	};
	var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

	var marker = new google.maps.Marker({
		position : myLatlng,
		map : map,
		draggable : true
	});
	
	google.maps.event.addListener(marker, 'drag', function() {
		(function($){
			jQuery('#lat').val( marker.position.lat() );
			jQuery('#lng').val( marker.position.lng() );
		})(jQuery);
	});
	google.maps.event.addListener(map, 'zoom_changed', function() {
		document.getElementById('zoom').value = map.getZoom();
	});
}

