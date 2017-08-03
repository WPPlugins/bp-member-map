var bpgeoGeocoder;

function buddyPressGeoOnload() {
	bpgeoGeocoder = new google.maps.Geocoder();
	
	if ( jQuery( "#geo-submit" ).length > 0 ) {
		jQuery( "#geo-submit" ).click(
			function() {
				var dest = jQuery( "form#geo-form" ).attr( "method" );
				var loc = jQuery( "input#geo-search-field" ).attr( "value" );
				var distance = jQuery( "select#geo-distance" ).attr( "value" );
				
				buddyPressGeoDetermineLatLatFromAdddress( loc, function( lat, lon ) {
					var newLocation = memberMapLocation + "/?type=near&lat=" + lat + "&lon=" + lon + "&friendly=" + loc + "&within=" + distance;
					window.location = newLocation;
				});
				
				return false;
			}
		);		
	}
}

function buddyPressGeoDetermineLatLatFromAdddress( address, callback ) {
	if (bpgeoGeocoder) {
		bpgeoGeocoder.geocode( { 'address': address },
		function(results, status) {
			if (status == google.maps.GeocoderStatus.OK) {
				var l = String(results[0].geometry.location);
				l = l.substring( 1, l.length - 1 );
				var latlngStr = l.split(", ",2);
				var lat = latlngStr[0];
				var lng = latlngStr[1];
				callback( lat, lng );
			}
		});
	}
}

jQuery( document ).ready( function() { 
	buddyPressGeoOnload();
});