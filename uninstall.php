<?php
/**
 * This file runs when the plugin in uninstalled (deleted).
 * This will not run when the plugin is deactivated.
 * Ideally you will add all your clean-up scripts here
 * that will clean-up unused meta, options, etc. in the database.
 *
 * @package WP Plugin Template/Uninstall
 */

// If plugin is not being uninstalled, exit (do nothing).
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

// Load plugin class files.
require_once 'includes/class-wp-plugin-template.php';
require_once 'includes/class-wp-plugin-template-settings.php';

// Load plugin libraries.
require_once 'includes/lib/class-wp-plugin-template-admin-api.php';
require_once 'includes/lib/class-wp-plugin-template-post-type.php';
require_once 'includes/lib/class-wp-plugin-template-taxonomy.php';

/**
 * Returns the main instance of WP_Plugin_Template to prevent the need to use globals.
 *
 * @since  0.1.0
 * @return object WP_Plugin_Template
 */
function wp_plugin_template() {
	$instance = WP_Plugin_Template::instance( __FILE__, '0.1.0' );

	return $instance;
}

$wp_plugin_template = wp_plugin_template();

$wp_plugin_template->uninstall();

