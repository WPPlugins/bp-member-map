<?php

function bp_show_member_map( $args = '' ) {
	// type: active ( default ) | random | newest | popular | online | alphabetical
	$defaults = array(
		'width' => '250',
		'height' => '250',
		'class' => 'bp-member-map'
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );
?>
	<div id="member-map" class="<?php echo $class; ?>" style="width: <?php echo $width; ?>px; height: <?php echo $height; ?>px">
		<span></span>
	</div>
<?php
}

function bp_member_map_user_location( $user_id = '' ) {
	return BP_Member_Map_User::location( $user_id );
}

function bp_member_map_get_location( $place ) {
	return BP_Member_Map::location( $place );
}

/* If viewing a member profile, zoom in a little bit */
function bp_member_map_profile_zoom( $zoom, $latitude, $longitude ) {
	global $bp;

	if ( $latitude != $bp->member_map->settings['default_latitude'] && $longitude != $bp->member_map->settings['default_longitude'] ) {
		if ( 'public' == bp_current_action() )
			$zoom = '4';
	}
	return $zoom;
}
add_filter( 'bp_member_map_zoom', 'bp_member_map_profile_zoom', 10, 3 );

?>
