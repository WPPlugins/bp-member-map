<?php

/* Load required files */
require_once( WP_PLUGIN_DIR . '/bp-member-map/bp-member-map-classes.php' );
require_once( WP_PLUGIN_DIR . '/bp-member-map/bp-member-map-templatetags.php' );
require_once( WP_PLUGIN_DIR . '/bp-member-map/bp-member-map-directory.php' );

if ( is_admin() )
	require_once( WP_PLUGIN_DIR . '/bp-member-map/bp-member-map-admin.php' );

?>