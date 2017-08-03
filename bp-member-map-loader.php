<?php
/*
Plugin Name: BP Member Map
Plugin URI: http://buddypress.org
Description: Adds a member map to BuddyPress. Based heavily on a similar plugin by Brave New Code.
Author: John James Jacoby
Version: 1.1.5
Author URI: http://johnjamesjacoby.com
*/

define( 'BP_MEMBER_MAP_VERSION', '1.1.5' );

/* Default slug for bp-geo */
if ( !defined( 'BP_MEMBER_MAP_SLUG' ) )
	define( 'BP_MEMBER_MAP_SLUG', 'map' );

/* Default latitude when no data is available */
if ( !defined( 'BP_MEMBER_MAP_DEFAULT_LATITUDE' ) )
	define( 'BP_MEMBER_MAP_DEFAULT_LATITUDE', '-97' );

/* Default latitude when no data is available */
if ( !defined( 'BP_MEMBER_MAP_DEFAULT_LONGITUDE' ) )
	define( 'BP_MEMBER_MAP_DEFAULT_LONGITUDE', '38' );

/* Default zoom height when no data is available */
if ( !defined( 'BP_MEMBER_MAP_DEFAULT_ZOOM' ) )
	define( 'BP_MEMBER_MAP_DEFAULT_ZOOM', '2' );

/* Default DOM id of the map */
if ( !defined( 'BP_MEMBER_MAP_OBJECT_ID' ) )
	define( 'BP_MEMBER_MAP_OBJECT_ID', 'member-map' );

/* Default zoom height when no data is available */
if ( !defined( 'BP_MEMBER_MAP_DEFAULT_DATA' ) )
	define( 'BP_MEMBER_MAP_DEFAULT_DATA', __( 'No Location', 'bp-member-map' ) );

/* Default distance to search for */
if ( !defined( 'BP_MEMBER_MAP_DEFAULT_SEARCH' ) )
	define( 'BP_MEMBER_MAP_DEFAULT_SEARCH', 1000 );

function bp_member_map_loader() {
	require_once( WP_PLUGIN_DIR . '/bp-member-map/bp-member-map.php' );

	/* Initialize action hooks */
	BP_Member_Map::init();
	BP_Member_Map_User::init();

	if ( is_admin() )
		BP_Member_Map_Admin::init();

	/*
	 * For developers:
	 * ---------------------
	 * If you want to make sure your code is loaded after this plugin
	 * have your code load on this action
	 */
	do_action ( 'bp_member_map_init' );
}

if ( defined( 'BP_VERSION' ) )
	bp_member_map_loader();
else
	add_action( 'bp_init', 'bp_member_map_loader' );

?>