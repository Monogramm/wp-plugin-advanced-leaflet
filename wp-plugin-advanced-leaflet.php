<?php
/**
 * Plugin Name: WP Plugin Advanced Leaflet
 * Version: 0.1.0
 * Plugin URI: https://github.com/Monogramm/wp-plugin-advanced-leaflet/
 * Description: WP Plugin Advanced Leaflet with Unit Tests and docker env
 * Author: Monogramm
 * Author URI: http://www.monogramm.io/
 * Requires at least: 5.2
 * Tested up to: 5.6
 *
 * Text Domain: wp-plugin-advanced-leaflet
 * Domain Path: /lang/
 *
 * @package WordPress
 * @author Monogramm
 * @since 0.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

define('LEAFLET_MAP__PLUGIN_DIR', plugin_dir_path(__FILE__));
define('LEAFLET_MAP__PLUGIN_VERSION', '2.22.1');
define('LEAFLET_MAP__PLUGIN_FILE', __FILE__);


// Load plugin class files.
require_once 'includes/class-wp-plugin-advanced-leaflet.php';
require_once 'includes/class-wp-plugin-advanced-leaflet-settings.php';
require_once 'includes/class-wp-plugin-advanced-leaflet-shortcodes.php';

// Load plugin libraries.
require_once 'includes/lib/class-wp-plugin-advanced-leaflet-admin-api.php';
require_once 'includes/lib/class-wp-plugin-advanced-leaflet-post-type.php';
require_once 'includes/lib/class-wp-plugin-advanced-leaflet-taxonomy.php';

/**
 * Returns the main instance of WP_Plugin_Advanced_Leaflet to prevent the need to use globals.
 *
 * @since  0.1.0
 * @return object WP_Plugin_Advanced_Leaflet
 */
function wp_plugin_advanced_leaflet() {
	$instance = WP_Plugin_Advanced_Leaflet::instance( __FILE__, '0.1.0' );

	if ( null === $instance->settings ) {
		$instance->settings = WP_Plugin_Advanced_Leaflet_Settings::instance( $instance );
	}

	if ( null === $instance->shortcodes_api ) {
		$instance->shortcodes_api = WP_Plugin_Advanced_Leaflet_ShortCodes::instance( $instance );
	}

	return $instance;
}

wp_plugin_advanced_leaflet();
