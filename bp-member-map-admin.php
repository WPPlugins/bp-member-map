<?php

class BP_Member_Map_Admin {

	function init() {
		add_action( 'admin_menu', array( 'BP_Member_Map_Admin', 'add_buddypress_page' ) );
		add_action( 'admin_head', array( 'BP_Member_Map_Admin', 'admin_head' ) );
	}

	function admin_head() {
		if ( isset( $_GET['page'] ) && $_GET['page'] == 'bp-member-map-admin' ) {
			echo '<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>';
		}
	}

	function add_buddypress_page() {
		if ( !is_site_admin() )
			return false;

		add_submenu_page( 'bp-general-settings', __( "Member Map Setup", 'bp-member-map' ), __( "Member Map Setup", 'bp-member-map' ), 'admin-options', 'bp-member-map-admin', array( 'BP_Member_Map_Admin', 'page' ) );
	}

	function page() {
		global $bp, $field;

		$fields = array();

		if ( bp_has_profile() ) {
			while ( bp_profile_groups() ) {
				bp_the_profile_group();
				while ( bp_profile_fields() ) {
					bp_the_profile_field();

					$one_field = array();
					$one_field['id'] = $field->id;
					$one_field['name'] = $field->name;

					$fields[] = $one_field;
				}
			}
		}

		if ( isset( $_POST[ 'submit' ] ) ) {
			check_admin_referer( 'BP_Member_Map_Admin' );

			$bp->member_map->settings['google-api-key'] = strip_tags( $_POST['google-api-key'] );
			$bp->member_map->settings['location'] = strip_tags( $_POST['location'] );
			$bp->member_map->settings['units'] = strip_tags( $_POST['units'] );

			update_site_option( 'bp_member_map_settings', $bp->member_map->settings ); ?>
			<div class="updated"><p><strong><?php _e( 'Settings saved.', 'bp-member-map' ); ?></strong></p></div>
<?php	} ?>

		<div class="wrap">
		    <h2><?php _e( 'BP Member Map Settings', 'bp-member-map' ) ?></h2>
			<form name="options" method="post" action="">
				<table class="form-table">
					<tbody>
						<tr valign="top">
							<th scope="row"><label for="google-api-key"><?php _e( 'Google Maps API Key:', 'bp-member-map' ) ?></label></th>
							<td>
								<input type="text" id="google-api-key" name="google-api-key" value="<?php echo $bp->member_map->settings['google-api-key']; ?>" style="width: 75%;"/>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><label for="location"><?php _e( "Field that represents user's location:", 'bp-member-map' ); ?></label></th>
							<td>
								<select id="location" name="location">
<?php foreach( $fields as $field ) { ?>
									<option value="<?php echo $field['id']; ?>"<?php if ( $bp->member_map->settings['location'] == $field['id'] ) echo " selected"; ?>><?php echo $field['name']; ?></option>
<?php } ?>
								</select>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><label for="units"><?php _e( "Unit of measurement:", 'bp-member-map' ); ?></label></th>
							<td>
								<select id="units" name="units">
									<option value="miles"<?php if ( $bp->member_map->settings['units'] == "miles" ) echo " selected"; ?>><?php _e( "Miles", 'bp-member-map' ); ?></option>
									<option value="kms"<?php if ( $bp->member_map->settings['units'] == "kms" ) echo " selected"; ?>><?php _e( "Kilometers", 'bp-member-map' ); ?></option>
								</select>
							</td>
						</tr>
					</tbody>
				</table>
				<p class="submit">
					<?php wp_nonce_field( 'BP_Member_Map_Admin' ) ?>
					<input type="submit" name="submit" value="<?php esc_attr_e( 'Update Settings', 'bp-member-map' ) ?>" />
				</p>
			</form>
		</div>
<?php
	}
}
add_action( 'init', array( 'BP_Member_Map_Admin', 'init' ) );
?>