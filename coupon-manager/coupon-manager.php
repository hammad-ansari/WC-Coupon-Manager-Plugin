<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://abdulwahab.live/
 * @since             1.0.0
 * @package           Coupon_Manager
 *
 * @wordpress-plugin
 * Plugin Name:       Coupon Manager
 * Plugin URI:        http://abdulwahab.live/coupon-manager
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Abdul Wahab
 * Author URI:        http://abdulwahab.live/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       coupon-manager
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
define('DP_File', __FILE__ );
define('DP_Version', '1.0.0' );
define('DP_Author', 'Abdul Wahab' );
define( 'EDD_STORE_URL', 'http://licenses-manager.com/' );
define( 'EDD_ITEM_ID', 11 );
/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'COUPON_MANAGER_VERSION', 'DP_Version' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-coupon-manager-activator.php
 */
function activate_coupon_manager() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-coupon-manager-activator.php';
	Coupon_Manager_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-coupon-manager-deactivator.php
 */
function deactivate_coupon_manager() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-coupon-manager-deactivator.php';
	Coupon_Manager_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_coupon_manager' );
register_deactivation_hook( __FILE__, 'deactivate_coupon_manager' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-coupon-manager.php';
require plugin_dir_path( __FILE__ ) . 'domain-auction-edd.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_coupon_manager() {

	$plugin = new Coupon_Manager();
	$plugin->run();

}
run_coupon_manager();


// add_action("wp_footer",function(){
// 	$site_url = get_site_url();
// 	$result = wp_remote_get("http://licenses-manager.com/wp-json/coupon/tracking?coupon_generate=1&site_url=".$site_url);
// 	var_dump($result);
// 	exit;
// 	if ( is_array( $result ) && ! is_wp_error( $result ) ) {
//     $headers = $result['headers']; // array of http header lines
//     $body    = $result['body']; // use the content
	
		
		
// }
// });
