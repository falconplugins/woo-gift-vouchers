<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://falconplugins.store
 * @since             1.0.0
 * @package           Woo_Gift_Vouchers
 *
 * @wordpress-plugin
 * Plugin Name:       WooCommerce Gift Vouchers
 * Plugin URI:        https://falconplugins.store/plugins/woo-gift-vouchers
 * Description:       Improve your sales by offering Gift Vouchers to your customers.
 * Version:           1.0.0
 * Author:            Falcon Plugins
 * Author URI:        https://falconplugins.store/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       woo-gift-vouchers
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WOO_GIFT_VOUCHERS_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-woo-gift-vouchers-activator.php
 */
function activate_woo_gift_vouchers() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-woo-gift-vouchers-activator.php';
	Woo_Gift_Vouchers_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-woo-gift-vouchers-deactivator.php
 */
function deactivate_woo_gift_vouchers() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-woo-gift-vouchers-deactivator.php';
	Woo_Gift_Vouchers_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_woo_gift_vouchers' );
register_deactivation_hook( __FILE__, 'deactivate_woo_gift_vouchers' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-woo-gift-vouchers.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_woo_gift_vouchers() {

	$plugin = new Woo_Gift_Vouchers();
	$plugin->run();

}
run_woo_gift_vouchers();
