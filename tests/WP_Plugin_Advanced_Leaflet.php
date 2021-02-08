<?php
/**
 * Main plugin test file.
 *
 * @package WP Plugin Advanced Leaflet/Tests
 */

use phpmock\phpunit\PHPMock;

class WP_Plugin_Advanced_Leaflet_Test extends \PHPUnit_Framework_TestCase {

	use PHPMock;

	private $class_instance;

	public function setUp() {
		$this->class_instance = WP_Plugin_Advanced_Leaflet::instance();
	}

	public function test_instance() {
		$this->assertNotNull( $this->class_instance );
	}
}
