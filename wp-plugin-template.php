<?php
/**
 * Plugin Name: WP Plugin Template
 * Version: 0.1.0
 * Plugin URI: https://github.com/Monogramm/wp-plugin-template/
 * Description: WP Plugin Template with Unit Tests and docker env
 * Author: Monogramm
 * Author URI: http://www.monogramm.io/
 * Requires at least: 5.2
 * Tested up to: 5.5
 *
 * Text Domain: wp-plugin-template
 * Domain Path: /lang/
 *
 * @package WordPress
 * @author Monogramm
 * @since 0.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

// Load plugin class files.
require_once 'includes/class-wp-plugin-template.php';
require_once 'includes/class-wp-plugin-template-settings.php';
require_once 'includes/class-wp-plugin-template-shortcodes.php';

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

	if ( null === $instance->settings ) {
		$instance->settings = WP_Plugin_Template_Settings::instance( $instance );
	}

	if ( null === $instance->shortcodes ) {
		$instance->shortcodes = WP_Plugin_Template_ShortCodes::instance( $instance );
	}

	return $instance;
}

wp_plugin_template();
