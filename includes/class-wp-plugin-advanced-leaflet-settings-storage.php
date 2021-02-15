<?php


class WP_Plugin_Advanced_Leaflet_Settings_Storage
{
	/**
	 * Leaflet version
	 *
	 * @var string major minor patch version
	 */
	public static $leaflet_version = '1.7.1';

	private $setting_defaults = array();

	public function __construct() {
		$this->setting_defaults = array(
			'default_lat' => '44.67',
			'default_lng' => '-63.61',
			'default_zoom' => '12',
			'default_height' => '250',
			'default_width' => '100%',
			'fit_markers' => '0',
			'show_zoom_controls' => '0',
			'scroll_wheel_zoom' => '0',
			'double_click_zoom' => '0',
			'default_min_zoom' => '0',
			'default_max_zoom' => '20',
			'default_tiling_service' => 'other',
			'mapquest_appkey' => '',
			'map_tile_url' => 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
			'map_tile_url_subdomains' => 'abc',
			'detect_retina' => '0',
			'tilesize' => null,
			'mapid' => null,
			'accesstoken' => null,
			'zoomoffset' => null,
			'tile_no_wrap' => '0',
			'js_url' => sprintf('https://unpkg.com/leaflet@%s/dist/leaflet.js', self::$leaflet_version),
			'css_url' => sprintf('https://unpkg.com/leaflet@%s/dist/leaflet.css', self::$leaflet_version),
			'default_attribution' => sprintf(
				'<a href="http://leafletjs.com" title="%1$s">Leaflet</a>; © <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a> %2$s',
				__("A JS library for interactive maps", 'leaflet-map'),
				__("contributors", 'leaflet-map')
			),
			'show_scale' => '0',
			'geocoder' => 'osm',
			'google_appkey' => '',
			'togeojson_url' => 'https://unpkg.com/@mapbox/togeojson@0.16.0/togeojson.js',
			'shortcode_in_excerpt' => '0'
		);
	}

	/**
	 * Wrapper for WordPress get_options (adds prefix to default options)
	 *
	 * @param string $key
	 *
	 * @return varies
	 */
	public function get($key, $default_only = false)
	{
		$default = $this->setting_defaults[ $key ];
		$key = 'wppt_' . $key;
		if ($default_only) {
			return $default;
		}
		$option = get_option($key, $default);

		if (!$option) {
			return $default;
		}

		return $option;
	}
}
