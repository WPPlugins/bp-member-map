<?php

class BP_Member_Map {

	function init() {
		/* Add root component */
		add_action( 'plugins_loaded', array( 'BP_Member_Map', 'add_root_component' ), 2 );

		/* Setup globals */
		add_action( 'plugins_loaded', array( 'BP_Member_Map', 'setup_globals' ), 5 );
		add_action( 'admin_menu', array( 'BP_Member_Map', 'setup_globals' ), 2 );

		/* wp */
		add_action( 'wp', array( 'BP_Member_Map', 'wp' ) );

		/* wp_head */
		add_action( 'wp_head', array( 'BP_Member_Map', 'wp_head' ) );
	}

	function add_root_component() {
		bp_core_add_root_component( BP_MEMBER_MAP_SLUG );
	}

	function setup_globals() {
		global $bp, $wpdb;

		/* For internal identification */
		$bp->member_map->id = 'member-map';
		$bp->member_map->format_notification_function = 'bp_member_map_format_notifications';
		$bp->member_map->slug = BP_MEMBER_MAP_SLUG;
		$bp->member_map->settings = BP_Member_Map::settings();
		$bp->loggedin_user->location = BP_Member_Map_User::location( $bp->loggedin_user->id );

		/* Register this in the active components array */
		$bp->active_components[$bp->member_map->slug] = $bp->member_map->id;

		do_action( 'bp_member_map_setup_globals' );
	}

	function settings() {
		$settings = get_site_option( 'bp_member_map_settings', false );
		
		$defaults = array(
			'google-api-key' => '',
			'location' => false,
			'units' => 'kms',
			'default_latitude' => BP_MEMBER_MAP_DEFAULT_LATITUDE,
			'default_longitude' => BP_MEMBER_MAP_DEFAULT_LONGITUDE,
			'default_zoom' => BP_MEMBER_MAP_DEFAULT_ZOOM,
			'default_data' => BP_MEMBER_MAP_DEFAULT_DATA,
			'object_id' => BP_MEMBER_MAP_OBJECT_ID,
		);

		return apply_filters( 'bp_member_map_settings', wp_parse_args( $settings, $defaults ) );
	}

	function location( $place ) {
		global $bp;

		$whereurl = apply_filters( 'bp_member_get_location', stripslashes( urlencode ( $place ) ) );

		/* This... */
		$location = file( "http://maps.google.com/maps/geo?q=$whereurl&output=csv&key=" . $bp->member_map->settings['google-api-key'] );
		if ( is_array( $location ) ) {
			list ( $stat, $acc, $longitude, $latitude ) = explode( ",", $location[0] );

		/* ...or this... */
		} else {
			$location = "http://maps.google.com/maps/geo?q=$whereurl&output=xml&key=" . $bp->member_map->settings['google-api-key'];
			$page = file_get_contents( $location );
			$xml = new SimpleXMLElement( $page );
			list( $longitude, $latitude, $altitude ) = explode( ",", $xml->Response->Placemark->Point->coordinates );
		}

		$location['latitude'] = $latitude;
		$location['longitude'] = $longitude;

		return $location;
	}

	function user_location( $user_id = '' ) {
		return BP_Member_Map_User::location( $user_id );
	}

	function wp() {
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'bp_member_map_js', WP_CONTENT_URL . "/plugins/bp-member-map/js/map.js", array( 'jquery' ) );
		wp_enqueue_script( 'bp_member_map_google', 'http://maps.google.com/maps/api/js?sensor=true', array( 'jquery', 'bp_member_map_js' ) );
	}

	function wp_head() {
		global $bp;

		if ( $bp->displayed_user->id ) {
			$dul = BP_Member_Map_User::location( $bp->displayed_user->id );
			$latitude = $dul['latitude'];
			$longitude = $dul['longitude'];
		} else {
			$latitude = $bp->loggedin_user->location['latitude'];
			$longitude = $bp->loggedin_user->location['longitude'];
		}

		/* Allow values to be hot filtered immediately before run-time */
		$latitude = apply_filters( 'bp_member_map_center_latitude', $latitude );
		$longitude = apply_filters( 'bp_member_map_center_longitude', $longitude );
		$zoom = apply_filters( 'bp_member_map_zoom', $bp->member_map->settings['default_zoom'], $latitude, $longitude );
		$data = apply_filters( 'bp_member_map_data', $bp->member_map->settings['default_data'] );
		$object_id = apply_filters( 'bp_member_map_object_id', $bp->member_map->settings['object_id'] );

		if ( $bp->member_map->settings['google-api-key'] ) {
			$map_js = apply_filters( 'bp_member_map_head', '
			<script type="text/javascript" src="http://maps.google.com/maps?file=api&amp;v=2&amp;sensor=true&amp;key=' . $bp->member_map->settings['google-api-key'] . '"></script>
			<script type="text/javascript">
			//<![CDATA[
				jQuery(document).ready( function() {
					// Creates a marker whose info window displays the given number
					function createMarker(point, number) {
						var marker = new GMarker(point);
						var html = number;
						GEvent.addListener(marker, "click", function() {marker.openInfoWindowHtml(html);});
						return marker;
					};

					var memberMapLocation = "' . $bp->root_domain . BP_MEMBERS_SLUG . '/' . BP_MEMBER_MAP_SLUG . '/";
					var object = document.getElementById("' . $object_id . '");

					if (object) {
						map = new GMap2(object);
						map.setCenter(new GLatLng(' . $longitude . ', ' . $latitude . '), ' . $zoom . ', G_NORMAL_MAP);
						var point = new GLatLng(' . $longitude . ',' . $latitude . ');
						var marker = createMarker(point, "' . addslashes( $data ) . '");
						map.addOverlay(marker);
					}
				});
			//]]>
			</script>' );

			/* Spit out the map code */
			echo $map_js;
		}
	}
}

class BP_Member_Map_User {

	function init() {
		add_action( 'xprofile_data_before_save', array( 'BP_Member_Map_User', 'save_field' ), 10, 1 );
		add_action( 'xprofile_updated_profile', array( 'BP_Member_Map_User', 'update_profile' ) );
	}

	function location( $user_id = '' ) {
		global $bp;

		$user_lat = get_usermeta( $user_id, 'bp_member_map_lat' );
		$user_lon = get_usermeta( $user_id, 'bp_member_map_lon' );

		$location['latitude']	= apply_filters( 'bp_member_map_latitude', $user_lat ? $user_lat : $bp->member_map->settings['default_latitude'] );
		$location['longitude']	= apply_filters( 'bp_member_map_longitude', $user_lon ? $user_lon : $bp->member_map->settings['default_longitude'] );

		return $location;
	}

	function save_field( $field ) {
		global $bp;

		/* If passed value isn't the location field then stop */
		if ( $field->field_id != $bp->member_map->settings['location'] )
			return $field;

		/* Field is good, load entire field */
		$field_location = xprofile_get_field( $bp->member_map->settings['location'] );

		/* Got the field, get the info. */
		if ( $field_location )
			$whereurl = apply_filters( 'bp_member_save_location', stripslashes( urlencode ( $field->value ) ) );

		$location = bp_member_map_get_location( $whereurl );

		if ( is_numeric( $latitude ) && is_numeric( $longitude ) ) {
			$lat = update_usermeta( $bp->displayed_user->id, 'bp_member_map_lat', $location['latitude'] );
			$lon = update_usermeta( $bp->displayed_user->id, 'bp_member_map_lon', $location['longitude'] );
		}

		/* We're all done, return the field for processing */
		return $field;
	}

	/* If profile was just updated, make sure logged in user location is current */
	function update_profile() {
		global $bp;

		if ( bp_is_my_profile() )
			$bp->loggedin_user->location = BP_Member_Map_User::location( $bp->loggedin_user->id );
	}
}

?>