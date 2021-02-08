<?php
/**
 * This file runs when the plugin in uninstalled (deleted).
 * This will not run when the plugin is deactivated.
 * Ideally you will add all your clean-up scripts here
 * that will clean-up unused meta, options, etc. in the database.
 *
 * @package WP Plugin Advanced Leaflet/Uninstall
 */

// If plugin is not being uninstalled, exit (do nothing).
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

// Load plugin class files.
require_once 'includes/class-wp-plugin-advanced-leaflet.php';
require_once 'includes/class-wp-plugin-advanced-leaflet-settings.php';

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

	return $instance;
}

$wp_plugin_advanced_leaflet = wp_plugin_advanced_leaflet();

$wp_plugin_advanced_leaflet->uninstall();

