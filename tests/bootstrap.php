<?php
/**
 * Tests bootstrap.
 *
 * @package WP Plugin Template/Tests
 */

// phpcs:ignorefile

require_once dirname( __DIR__ ) . '/vendor/autoload.php';

$_tests_dir = getenv( 'WP_TESTS_DIR' );
if ( ! $_tests_dir ) {
	$_tests_dir = '/tmp/wordpress-tests-lib';
}

require_once $_tests_dir . '/includes/functions.php';

/**
 * Manually load the test plugin.
 */
function _manually_load_plugin() {
	$_tests_plugin = getenv( 'WP_PLUGIN' );
	if ( ! $_tests_plugin ) {
		$_tests_plugin = dirname( '..' );
	}
	require __DIR__ . '/../' . $_tests_plugin . '.php';
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

require $_tests_dir . '/includes/bootstrap.php';
