<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://abdulwahab.live/
 * @since      1.0.0
 *
 * @package    Coupon_Manager
 * @subpackage Coupon_Manager/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Coupon_Manager
 * @subpackage Coupon_Manager/includes
 * @author     Abdul Wahab <rockingwahab9@gmail.com>
 */
class Coupon_Manager_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'coupon-manager',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
