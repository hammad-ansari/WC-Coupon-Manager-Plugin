<?php

define( 'EDD_PLUGIN_LICENSE_PAGE', 'license' );

if( !class_exists( 'EDD_SL_Plugin_Updater' ) ) {
	// load our custom updater
	include( dirname( __FILE__ ) . '/includes/EDD_SL_Plugin_Updater.php' );
}

function dsp_plugin_updater() {

	$active_plugins = get_option( 'active_plugins' );
		foreach ( $active_plugins as $active_plugin ) {
			if ( false !== strpos( $active_plugin, 'coupon-manager.php' ) ) {
				$plugin_dir_and_filename = $active_plugin;
				break;
			}
		}
		if ( ! isset( $plugin_dir_and_filename ) || empty( $plugin_dir_and_filename ) ) {
			echo ( 'Plugin not found! Check the name of your plugin file in the if check above' );
		}
	// retrieve our license key from the DB
	$license_key = trim( get_option( 'edd_license_key' ) );

	// setup the updater
	$edd_updater = new EDD_SL_Plugin_Updater( EDD_STORE_URL, DP_File ,
		array(             // license key (used get_option above to retrieve from DB)
			'version' => DP_Version,                    // current version number
			'license' => $license_key,             // license key (used get_option above to retrieve from DB)
			'item_id' => EDD_ITEM_ID,       // ID of the product
			'author'  => DP_Author, // author of this plugin
			'beta'    => false,
		)
	);

}
add_action( 'admin_init', 'dsp_plugin_updater', 0 );

function edd_license_menu() {

	add_submenu_page(
			'coupon-manager-admin',
			__( 'License', 'auctionsys' ),
			__( 'License', 'auctionsys' ),
			'manage_options',
			EDD_PLUGIN_LICENSE_PAGE,
			'edd_license_page'
		);
	
}
add_action('admin_menu', 'edd_license_menu',99);

function edd_license_page() {
	$license = get_option( 'edd_license_key' );
	$status  = get_option( 'edd_license_status' );
	?>
	<div class="wrap">
		<h2><?php _e('Plugin License Options'); ?></h2>
		<form method="post" action="options.php">

			<?php settings_fields('edd_license'); ?>

			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row" valign="top">
							<?php _e('License Key'); ?>
						</th>
						<td>
							<input id="edd_license_key" name="edd_license_key" type="text" class="regular-text" value="<?php esc_attr_e( $license ); ?>" />
							<label class="description" for="edd_license_key"><?php _e('Enter your license key'); ?></label>
						</td>
					</tr>
					<?php if( false !== $license ) { ?>
						<tr valign="top">
							<th scope="row" valign="top">
								<?php _e('Activate License'); ?>
							</th>
							<td>
								<?php if( $status !== false && $status == 'valid' ) { ?>
									<span style="color:green;"><?php _e('active'); ?></span>
									<?php wp_nonce_field( 'edd_nonce', 'edd_nonce' ); ?>
									<input type="submit" class="button-secondary" name="edd_license_deactivate" value="<?php _e('Deactivate License'); ?>"/>
								<?php } else {
									wp_nonce_field( 'edd_nonce', 'edd_nonce' ); ?>
									<input type="submit" class="button-secondary" name="edd_license_activate" value="<?php _e('Activate License'); ?>"/>
								<?php } ?>
							</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
			<?php submit_button(); ?>

		</form>
	<?php
}

function edd_register_option() {
	// creates our settings in the options table
	register_setting('edd_license', 'edd_license_key', 'edd_sanitize_license' );
}
add_action('admin_init', 'edd_register_option');

function edd_sanitize_license( $new ) {
	$old = get_option( 'edd_license_key' );
	if( $old && $old != $new ) {
		delete_option( 'edd_license_status' ); // new license has been entered, so must reactivate
	}
	return $new;
}

function edd_activate_license() {

	// listen for our activate button to be clicked
	if( isset( $_POST['edd_license_activate'] ) ) {

		// run a quick security check
	 	if( ! check_admin_referer( 'edd_nonce', 'edd_nonce' ) )
			return; // get out if we didn't click the Activate button

		$license = trim( get_option( 'edd_license_key' ) );
		
		$api_params = array(
			'edd_action' => 'activate_license',
			'license'    => $license,	
			'item_name' => urlencode( 'Coupon Manager' ),
			'url'        => home_url()
		);

		// Call the custom API.
		$response = wp_remote_post( EDD_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

			if ( is_wp_error( $response ) ) {
				$message = $response->get_error_message();
			} else {
				$message = __( 'An error occurred, please try again.' );
			}

		} else {

			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			if ( false === $license_data->success ) {

				switch( $license_data->error ) {

					case 'expired' :

						$message = sprintf(
							__( 'Your license key expired on %s.' ),
							date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
						);
						break;

					case 'disabled' :
					case 'revoked' :

						$message = __( 'Your license key has been disabled.' );
						break;

					case 'missing' :

						$message = __( 'Invalid license.' );
						break;

					case 'invalid' :
					case 'site_inactive' :

						$message = __( 'Your license is not active for this URL.' );
						break;
					case 'item_name_mismatch' :

						$message = sprintf( __( 'This appears to be an invalid license key for %s.' ), 'WP Domain Auctions' );
						break;
		
					case 'no_activations_left':

						$message = __( 'Your license key has reached its activation limit.' );
						break;

					default :

						$message = __( 'An error occurred, please try again.' );
						break;
				}

			}

		}

		// Check if anything passed on a message constituting a failure
		if ( ! empty( $message ) ) {
			$base_url = admin_url( 'admin.php?page=' . EDD_PLUGIN_LICENSE_PAGE );
			$redirect = add_query_arg( array( 'sl_activation' => 'false', 'message' => urlencode( $message ) ), $base_url );

			wp_redirect( $redirect );
			exit();
		}

		// $license_data->license will be either "valid" or "invalid"

		update_option( 'edd_license_status', $license_data->license );
		wp_redirect( admin_url( 'admin.php?page=' . EDD_PLUGIN_LICENSE_PAGE ) );
		exit();
	}
}
add_action('admin_init', 'edd_activate_license');

function edd_deactivate_license() {

	// listen for our activate button to be clicked
	if( isset( $_POST['edd_license_deactivate'] ) ) {

		// run a quick security check
	 	if( ! check_admin_referer( 'edd_nonce', 'edd_nonce' ) )
			return; // get out if we didn't click the Activate button

		// retrieve the license from the database
		$license = trim( get_option( 'edd_license_key' ) );


		// data to send in our API request
		$api_params = array(
			'edd_action' => 'deactivate_license',
			'license'    => $license,				
			'item_name' => urlencode( 'Coupon Manager' ),
			'url'        => home_url()
		);

		// Call the custom API.
		$response = wp_remote_post( EDD_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );
		

		// make sure the response came back okay
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

			if ( is_wp_error( $response ) ) {
				$message = $response->get_error_message();
			} else {
				$message = __( 'An error occurred, please try again.' );
			}

			$base_url = admin_url( 'admin.php?page=' . EDD_PLUGIN_LICENSE_PAGE );
			$redirect = add_query_arg( array( 'sl_activation' => 'false', 'message' => urlencode( $message ) ), $base_url );

			wp_redirect( $redirect );
			exit();
		}

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// $license_data->license will be either "deactivated" or "failed"
		if( $license_data->license == 'deactivated' ) {
			delete_option( 'edd_license_status' );
		}elseif($license_data->license == 'failed'){
			delete_option( 'edd_license_status' );
		}
		
		$base_url = admin_url( 'admin.php?page=' . EDD_PLUGIN_LICENSE_PAGE );
			$redirect = add_query_arg( array( 'sl_activation' => 'false', 'message' => urlencode( "License  Deactivated" ) ), $base_url );

		wp_redirect( $redirect );
		exit();

	}
}
add_action('admin_init', 'edd_deactivate_license');

function edd_check_license() {

	global $wp_version;

	$license = trim( get_option( 'edd_license_key' ) );

	$api_params = array(
		'edd_action' => 'check_license',
		'license' => $license,		
		'item_name' => urlencode( 'WP Domain Auctions' ),
		'url'       => home_url()
	);

	// Call the custom API.
	$response = wp_remote_post( EDD_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

	if ( is_wp_error( $response ) )
		return false;

	$license_data = json_decode( wp_remote_retrieve_body( $response ) );

	if( $license_data->license == 'valid' ) {
		echo 'valid'; exit;
		// this license is still valid
	} else {
		echo 'invalid'; exit;
		// this license is no longer valid
	}
}


/**
 * This is a means of catching errors from the activation method above and displaying it to the customer
 */
function edd_admin_notices() {
	if ( isset( $_GET['sl_activation'] ) && ! empty( $_GET['message'] ) ) {

		switch( $_GET['sl_activation'] ) {

			case 'false':
				$message = urldecode( $_GET['message'] );
				?>
				<div class="error">
					<p><?php echo $message; ?></p>
				</div>
				<?php
				break;

			case 'true':
			default:
				
				break;

		}
	}
}
add_action( 'admin_notices', 'edd_admin_notices' );