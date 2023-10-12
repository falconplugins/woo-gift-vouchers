<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://falconplugins.store
 * @since      1.0.0
 *
 * @package    Woo_Gift_Vouchers
 * @subpackage Woo_Gift_Vouchers/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Woo_Gift_Vouchers
 * @subpackage Woo_Gift_Vouchers/includes
 * @author     Falcon Plugins <plugins@falconplugins.store>
 */
class Woo_Gift_Vouchers_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'woo-gift-vouchers',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
