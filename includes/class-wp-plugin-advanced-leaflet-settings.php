<?php
/**
 * Settings class file.
 *
 * @package WP Plugin Advanced Leaflet/Includes
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Settings class.
 */
class WP_Plugin_Advanced_Leaflet_Settings {

	/**
	 * The single instance of WP_Plugin_Advanced_Leaflet_Settings.
	 *
	 * @var     object
	 * @access  private
	 * @since   0.1.0
	 */
	private static $_instance = null; //phpcs:ignore

	/**
	 * The main plugin object.
	 *
	 * @var     object
	 * @access  public
	 * @since   0.1.0
	 */
	public $parent = null;

	/**
	 * Prefix for plugin settings.
	 *
	 * @var     string
	 * @access  public
	 * @since   0.1.0
	 */
	public $base = '';

	/**
	 * Available settings for plugin.
	 *
	 * @var     array
	 * @access  public
	 * @since   0.1.0
	 */
	public $settings = array();

	/**
	 * Constructor function.
	 *
	 * @param object $parent Parent object.
	 */
	public function __construct( $parent ) {
		$this->parent = $parent;

		$this->base = 'wppt_';

		// Initialise settings.
		add_action( 'init', array( $this, 'init_settings' ), 11 );

		// Register plugin settings.
		add_action( 'admin_init', array( $this, 'register_settings' ) );

		// Add settings page to menu.
		add_action( 'admin_menu', array( $this, 'add_menu_item' ) );

		// Add settings link to plugins page.
		add_filter(
			'plugin_action_links_' . plugin_basename( $this->parent->file ),
			array(
				$this,
				'add_settings_link',
			)
		);

		// Configure placement of plugin settings page. See readme for implementation.
		add_filter( $this->base . 'menu_settings', array( $this, 'configure_settings' ) );
	}

	/**
	 * Initialise settings
	 *
	 * @return void
	 */
	public function init_settings() {
		$this->settings = $this->settings_fields();
	}

	/**
	 * Add settings page to admin menu
	 *
	 * @return void
	 */
	public function add_menu_item() {
		$args = $this->menu_settings();

		// Do nothing if wrong location key is set.
		if ( is_array( $args ) && isset( $args['location'] ) && function_exists( 'add_' . $args['location'] . '_page' ) ) {
			switch ( $args['location'] ) {
				case 'options':
				case 'submenu':
					$page = add_submenu_page( $args['parent_slug'], $args['page_title'], $args['menu_title'], $args['capability'], $args['menu_slug'], $args['function'] );
					break;
				case 'menu':
					$page = add_menu_page( $args['page_title'], $args['menu_title'], $args['capability'], $args['menu_slug'], $args['function'], $args['icon_url'], $args['position'] );
					break;
				default:
					return;
			}
			add_action( 'admin_print_styles-' . $page, array( $this, 'settings_assets' ) );
		}
	}

	/**
	 * Prepare default settings page arguments
	 *
	 * @return mixed|void
	 */
	private function menu_settings() {
		return apply_filters(
			$this->base . 'menu_settings',
			array(
				'location'    => 'options', // Possible settings: options, menu, submenu.
				'parent_slug' => 'options-general.php',
				'page_title'  => __( 'WP Plugin Advanced Leaflet Settings', 'wp-plugin-advanced-leaflet' ),
				'menu_title'  => __( 'WP Plugin Advanced Leaflet Settings', 'wp-plugin-advanced-leaflet' ),
				'capability'  => 'manage_options',
				'menu_slug'   => $this->parent->_token . '_settings',
				'function'    => array( $this, 'settings_page' ),
				'icon_url'    => '',
				'position'    => null,
			)
		);
	}

	/**
	 * Container for settings page arguments
	 *
	 * @param array $settings Settings array.
	 *
	 * @return array
	 */
	public function configure_settings( $settings = array() ) {
		return $settings;
	}

	/**
	 * Load settings JS & CSS
	 *
	 * @return void
	 */
	public function settings_assets() {

		// We're including the farbtastic script & styles here because they're needed for the colour picker
		// If you're not including a colour picker field then you can leave these calls out as well as the farbtastic dependency for the wpt-admin-js script below.
		wp_enqueue_style( 'farbtastic' );
		wp_enqueue_script( 'farbtastic' );

		// We're including the WP media scripts here because they're needed for the image upload field.
		// If you're not including an image upload then you can leave this function call out.
		wp_enqueue_media();

		wp_register_script( $this->parent->_token . '-settings-js', $this->parent->assets_url . 'js/settings' . $this->parent->script_suffix . '.js', array( 'farbtastic', 'jquery' ), '0.1.0', true );
		wp_enqueue_script( $this->parent->_token . '-settings-js' );
	}

	/**
	 * Add settings link to plugin list table
	 *
	 * @param  array $links Existing links.
	 * @return array        Modified links.
	 */
	public function add_settings_link( $links ) {
		$settings_link = '<a href="options-general.php?page=' . $this->parent->_token . '_settings">' . __( 'Settings', 'wp-plugin-advanced-leaflet' ) . '</a>';
		array_push( $links, $settings_link );
		return $links;
	}

	/**
	 * Build settings fields
	 *
	 * @return array Fields to be displayed on settings page
	 */
	private function settings_fields() {

		include_once 'class-wp-plugin-advanced-leaflet-settings-storage.php';
		$settings_storage = new WP_Plugin_Advanced_Leaflet_Settings_Storage();

		$settings['standard'] = array(
			'title'       => __( 'Standard', 'wp-plugin-advanced-leaflet' ),
			'description' => __( 'These are fairly standard form input fields.', 'wp-plugin-advanced-leaflet' ),
			'fields'      => array(
				array(
					'id'          => 'default_lat',
					'label'       => __('Default Latitude', 'leaflet-map'),
					'description' => sprintf(
						'%1$s <br /> <code>[leaflet-map lat="44.67"]</code>',
						__('Default latitude for maps.', 'leaflet-map'),
					),
					'type'        => 'number',
					'default'     => $settings_storage->get('default_lat', true),
					'placeholder' => '',
				),
				array(
					'id'          => 'default_lng',
					'label'       => __('Default Longitude', 'leaflet-map'),
					'description' => sprintf(
						'%1$s <br /> <code>[leaflet-map lng="-63.61"]</code>',
						__('Default longitude for maps.', 'leaflet-map'),
					),
					'type'        => 'number',
					'default'     => $settings_storage->get('default_lng', true),
					'placeholder' => '',
				),
				array(
					'id'          => 'default_zoom',
					'label'       => __('Default Zoom', 'leaflet-map'),
					'description' => sprintf(
						'%1$s <br /> <code>[leaflet-map zoom="5"]</code>',
						__('Default zoom for maps.', 'leaflet-map'),
					),
					'type'        => 'number',
					'default'     => $settings_storage->get('default_zoom', true),
					'placeholder' => '',
				),
				array(
					'id'          => 'default_height',
					'label'       => __('Default Height', 'leaflet-map'),
					'description' => sprintf(
						'%1$s <br /> <code>[leaflet-map height="250"]</code>',
						__('Default height for maps. Values can include "px" but it is not necessary. Can also be "%". ', 'leaflet-map'),
					),
					'type'        => 'text',
					'default'     => $settings_storage->get('default_height', true),
					'placeholder' => '',
				),
				array(
					'id'          => 'default_width',
					'label'       => __('Default Width', 'leaflet-map'),
					'description' => sprintf(
						'%1$s <br /> <code>[leaflet-map width="100%%"]</code>',
						__('Default width for maps. Values can include "px" but it is not necessary.  Can also be "%".', 'leaflet-map')
					),
					'type'        => 'text',
					'default'     => $settings_storage->get('default_width', true),
				),
				array(
					'id'          => 'fit_markers',
					'label'       => __('Fit Bounds', 'leaflet-map'),
					'description' => sprintf(
						'%1$s <br /> <code>[leaflet-map fitbounds]</code>',
						__('If enabled, all markers on each map will alter the view of the map; i.e. the map will fit to the bounds of all of the markers on the map.', 'leaflet-map'),
					),
					'type'        => 'checkbox',
					'default'     => $settings_storage->get('fit_markers', true),
				),
				array(
					'id'          => 'show_zoom_controls',
					'label'       => __( 'Some Options', 'wp-plugin-advanced-leaflet' ),
					'description' => sprintf(
						'%1$s <br /> <code>[leaflet-map !zoomcontrol]</code>',
						__('The zoom buttons can be large and annoying.', 'leaflet-map'),
					),
					'type'        => 'checkbox',
					'default'     => $settings_storage->get('show_zoom_controls', true),
				),
				array(
					'id'          => 'scroll_wheel_zoom',
					'label'       => __('Scroll Wheel Zoom', 'leaflet-map'),
					'description' => sprintf(
						'%1$s <br /> <code>[leaflet-map !scrollwheel]</code>',
						__('Disable zoom with mouse scroll wheel.  Sometimes someone wants to scroll down the page, and not zoom the map.', 'leaflet-map')
					),
					'type'        => 'checkbox',
					'default'     => $settings_storage->get('scroll_wheel_zoom', true),
				),
				array(
					'id'          => 'double_click_zoom',
					'label'       => __('Double Click Zoom', 'leaflet-map'),
					'description' => sprintf(
						'%1$s <br /> <code>[leaflet-map !doubleClickZoom]</code>',
						__('If enabled, your maps will zoom with a double click.  By default it is disabled: If we\'re going to remove zoom controls and have scroll wheel zoom off by default, we might as well stick to our guns and not zoom the map.', 'leaflet-map')
					),
					'type'        => 'checkbox',
					'default'     => $settings_storage->get('double_click_zoom', true),
				),
				array(
					'id'          => 'default_min_zoom',
					'label'       => __('Default Min Zoom', 'leaflet-map'),
					'description' => sprintf(
						'%1$s <br /> <code>[leaflet-map min_zoom="1"]</code>',
						__('Restrict the viewer from zooming in past the minimum zoom.  Can set per map in shortcode or adjust for all maps here.', 'leaflet-map')
					),
					'type'        => 'number',
					'default'     => $settings_storage->get('default_min_zoom', true),
				),
				array(
					'id'          => 'default_max_zoom',
					'label'       => __('Default Max Zoom', 'leaflet-map'),
					'description' => sprintf(
						'%1$s <br /> <code>%2$s</code>',
						__('Restrict the viewer from zooming out past the maximum zoom.  Can set per map in shortcode or adjust for all maps here', 'leaflet-map'),
						'[leaflet-map max_zoom="10"]'
					),
					'type'        => 'number',
					'default'     => $settings_storage->get('default_max_zoom', true),
				),
				array(
					'id'          => 'default_tiling_service',
					'label'       => __('Default Tiling Service', 'leaflet-map'),
					'default'     => $settings_storage->get('default_tiling_service', true),
					'type'        => 'select',
					'options'     => array(
						'other' => __('I will provide my own map tile URL', 'leaflet-map'),
						'mapquest' => __('MapQuest (I have an API key)', 'leaflet-map'),
					),
					'description' => __('Choose a tiling service or provide your own.', 'leaflet-map')
				),
				array(
					'id'          => 'mapquest_appkey',
					'label'       => __('MapQuest API Key (optional)', 'leaflet-map'),
					'default'     => __('Supply an API key if you choose MapQuest', 'leaflet-map'),
					'type'        => 'text',
					'noreset'     => true,
					'description' => sprintf(
						'%1$s <a href="https://developer.mapquest.com/plan_purchase/steps/business_edition/business_edition_free/register" target="_blank"> %2$s </a>, %3$s <a href="https://developer.mapquest.com/user/me/apps" target="_blank"> %4$s </a> %5$s',
						__('If you choose MapQuest, you must provide an API key.', 'leaflet-map'),
						__('Sign up', 'leaflet-map'),
						__('then', 'leaflet-map'),
						__('Create a new app', 'leaflet-map'),
						__('then supply the "Consumer Key" here.', 'leaflet-map')
					)
				),
				array(
					'id'          => 'map_tile_url',
					'label'       => __('Map Tile URL', 'leaflet-map'),
					'default'     => $settings_storage->get('map_tile_url', true),
					'type'        => 'text',
					'description' => sprintf(
						'%1$s: <a href="http://wiki.openstreetmap.org/wiki/Tile_servers" target="_blank"> %2$s </a>. %3$s: <a href="http://devblog.mapquest.com/2016/06/15/modernization-of-mapquest-results-in-changes-to-open-tile-access/" target="_blank"> %4$s </a>. <br/> <code>[leaflet-map tileurl=http://{s}.tile.stamen.com/watercolor/{z}/{x}/{y}.jpg subdomains=abcd]</code>',
						__('See more tile servers', 'leaflet-map'),
						__('here', 'leaflet-map'),
						__('Please note: free tiles from MapQuest have been discontinued without use of an API key', 'leaflet-map'),
						__('blog post', 'leaflet-map'),
					)
				),
				array(
					'id'          => 'map_tile_url_subdomains',
					'label'       => __('Map Tile URL Subdomains', 'leaflet-map'),
					'default'     => $settings_storage->get('map_tile_url_subdomains', true),
					'type'        => 'text',
					'description' => sprintf(
						'%1$s <br/> <code>[leaflet-map subdomains="1234"]</code>',
						__('Some maps get tiles from multiple servers with subdomains such as a,b,c,d or 1,2,3,4', 'leaflet-map'),
					)
				),
				array(
					'id'          => 'detect_retina',
					'label'       => __('Detect Retina', 'leaflet-map'),
					'default'     => $settings_storage->get('detect_retina', true),
					'type'        => 'checkbox',
					'description' => sprintf(
						'%1$s <br /> <code>[leaflet-map detect-retina]</code>',
						__('Fetch tiles at different zoom levels to appear smoother on retina displays.', 'leaflet-map')
					)
				),
				array(
					'id'          => 'tilesize',
					'label'       => __('Tile Size', 'leaflet-map'),
					'default'     => $settings_storage->get('tilesize', true),
					'type'        => 'text',
					'description' => sprintf(
						'%1$s <br /> <code>[leaflet-map tilesize=512]</code>',
						__('Width and height of tiles (in pixels) in the grid. Default is 256', 'leaflet-map')
					)
				),
				array(
					'id'          => 'mapid',
					'label'       => __('Tile Id', 'leaflet-map'),
					'default'     => $settings_storage->get('mapid', true),
					'type'        => 'text',
					'description' => sprintf(
						'%1$s <br /> <code>[leaflet-map mapid="mapbox/streets-v11"]</code>',
						__('An id that is passed to L.tileLayer; useful for Mapbox', 'leaflet-map')
					)
				),
				array(
					'id'          => 'accesstoken',
					'label'       => __('Access Token', 'leaflet-map'),
					'default'     => $settings_storage->get('accesstoken', true),
					'type'        => 'text',
					'description' => sprintf(
						'%1$s <br /> <code>[leaflet-map accesstoken="your.mapbox.access.token"]</code>',
						__('An access token that is passed to L.tileLayer; useful for Mapbox tiles', 'leaflet-map')
					)
				),
				array(
					'id'          => 'zoomoffset',
					'label'       => __('Zoom Offset', 'leaflet-map'),
					'default'     => $settings_storage->get('zoomoffset', true),
					'type'        => 'number',
					'description' => sprintf(
						'%1$s <br /> <code>[leaflet-map zoomoffset="-1"]</code>',
						__('The zoom number used in tile URLs will be offset with this value', 'leaflet-map')
					)
				),
				array(
					'id'          => 'tile_no_wrap',
					'label'       => __('No Wrap (tiles)', 'leaflet-map'),
					'default'     => $settings_storage->get('tile_no_wrap', true),
					'type'        => 'checkbox',
					'description' => sprintf(
						'%1$s <br /> <code>[leaflet-map nowrap]</code>',
						__('Boolean for whether the layer is wrapped around the antimeridian', 'leaflet-map')
					)
				),
				array(
					'id'          => 'js_url',
					'label'       => __('JavaScript URL', 'leaflet-map'),
					'default'     => $settings_storage->get('js_url', true),
					'type'        => 'text',
					'description' => __('If you host your own Leaflet files, then paste the URL here.', 'leaflet-map')
				),
				array(
					'id'          => 'css_url',
					'label'       => __('CSS URL', 'leaflet-map'),
					'default'     => $settings_storage->get('css_url', true),
					'type'        => 'text',
					'description' => __('Same as above.', 'leaflet-map')
				),
				array(
					'id'          => 'default_attribution',
					'label'       => __('Default Attribution', 'leaflet-map'),
					'default'     => $settings_storage->get('default_attribution', true),
					'type'        => 'textarea',
					'description' => __('Attribution to a custom tile url.  Use semi-colons (;) to separate multiple.', 'leaflet-map')
				),
				array(
					'id'          => 'show_scale',
					'label'       => __('Show Scale', 'leaflet-map'),
					'default'     => $settings_storage->get('show_scale', true),
					'type'        => 'checkbox',
					'description' => __(
						'Add a scale to each map. Can also be added via shortcode <br /> <code>[leaflet-scale]</code>',
						'leaflet-map'
					)
				),
				array(
					'id'          => 'geocoder',
					'label'       => __('Geocoder', 'leaflet-map'),
					'default'     => $settings_storage->get('geocoder', true),
					'type'        => 'select',
					'options' => array(
						'osm' => __('OpenStreetMap Nominatim', 'leaflet-map'),
						'google' => __('Google Maps', 'leaflet-map'),
						'dawa' => __('Denmark Addresses', 'leaflet-map')
					),
					'description' => __('Select the Geocoding provider to use to retrieve addresses defined in shortcode.', 'leaflet-map')
				),
				array(
					'id'          => 'google_appkey',
					'label'       => __('Google API Key (optional)', 'leaflet-map'),
					'default'     => __('Supply a Google API Key', 'leaflet-map'),
					'type'        => 'text',
					'noreset' => true,
					'description' => sprintf(
						'%1$s: <a href="https://cloud.google.com/maps-platform/?apis=places" target="_blank">%2$s</a>.  %3$s %4$s',
						__('The Google Geocoder requires an API key with the Places product enabled', 'leaflet-map'),
						__('here', 'leaflet-map'),
						__('You must create a project and set up a billing account, then you will be given an API key.', 'leaflet-map'),
						__('You are unlikely to ever be charged for geocoding.', 'leaflet-map')
					),
				),
				array(
					'id'          => 'togeojson_url',
					'label'       => __('KML/GPX JavaScript Converter', 'leaflet-map'),
					'default'     => $settings_storage->get('togeojson_url', true),
					'type'        => 'text',
					'noreset' => true,
					'description' =>  __('ToGeoJSON converts KML and GPX files to GeoJSON; if you plan to use [leaflet-kml] or [leaflet-gpx] then this library is loaded.  You can change the default if you need.', 'leaflet-map')
				),
				array(
					'id'          => 'shortcode_in_excerpt',
					'label'       => __('Show maps in excerpts', 'leaflet-map'),
					'default'     => $settings_storage->get('shortcode_in_excerpt', true),
					'type'        => 'checkbox',
					'description' =>  ''
				),
			),
		);

		$settings['extra'] = array(
			'title'       => __( 'Extra', 'wp-plugin-advanced-leaflet' ),
			'description' => __( 'These are some extra input fields that maybe aren\'t as common as the others.', 'wp-plugin-advanced-leaflet' ),
			'fields'      => array(
				array(
					'id'          => 'number_field',
					'label'       => __( 'A Number', 'wp-plugin-advanced-leaflet' ),
					'description' => __( 'This is a standard number field - if this field contains anything other than numbers then the form will not be submitted.', 'wp-plugin-advanced-leaflet' ),
					'type'        => 'number',
					'default'     => '',
					'placeholder' => __( '42', 'wp-plugin-advanced-leaflet' ),
				),
				array(
					'id'          => 'colour_picker',
					'label'       => __( 'Pick a colour', 'wp-plugin-advanced-leaflet' ),
					'description' => __( 'This uses WordPress\' built-in colour picker - the option is stored as the colour\'s hex code.', 'wp-plugin-advanced-leaflet' ),
					'type'        => 'color',
					'default'     => '#21759B',
				),
				array(
					'id'          => 'an_image',
					'label'       => __( 'An Image', 'wp-plugin-advanced-leaflet' ),
					'description' => __( 'This will upload an image to your media library and store the attachment ID in the option field. Once you have uploaded an imge the thumbnail will display above these buttons.', 'wp-plugin-advanced-leaflet' ),
					'type'        => 'image',
					'default'     => '',
					'placeholder' => '',
				),
				array(
					'id'          => 'multi_select_box',
					'label'       => __( 'A Multi-Select Box', 'wp-plugin-advanced-leaflet' ),
					'description' => __( 'A standard multi-select box - the saved data is stored as an array.', 'wp-plugin-advanced-leaflet' ),
					'type'        => 'select_multi',
					'options'     => array(
						'linux'   => 'Linux',
						'mac'     => 'Mac',
						'windows' => 'Windows',
					),
					'default'     => array( 'linux' ),
				),
			),
		);

		$settings = apply_filters( $this->parent->_token . '_settings_fields', $settings );

		return $settings;
	}

	public function getSettingDefault($key) {
		if ( isset($this->settings_default[$key]) ) {
			return $key;
		}

		return null;
	}

	/**
	 * Register plugin settings
	 *
	 * @return void
	 */
	public function register_settings() {
		if ( is_array( $this->settings ) ) {

			// Check posted/selected tab.
			//phpcs:disable
			$current_section = '';
			if ( isset( $_POST['tab'] ) && $_POST['tab'] ) {
				$current_section = $_POST['tab'];
			} else {
				if ( isset( $_GET['tab'] ) && $_GET['tab'] ) {
					$current_section = $_GET['tab'];
				}
			}
			//phpcs:enable

			foreach ( $this->settings as $section => $data ) {
				if ( $current_section && $current_section !== $section ) {
					continue;
				}

				// Add section to page.
				add_settings_section( $section, $data['title'], array( $this, 'settings_section' ), $this->parent->_token . '_settings' );

				foreach ( $data['fields'] as $field ) {

					// Validation callback for field.
					$validation = '';
					if ( isset( $field['callback'] ) ) {
						$validation = $field['callback'];
					}

					// Register field.
					$option_name = $this->base . $field['id'];
					register_setting( $this->parent->_token . '_settings', $option_name, $validation );

					// Add field to page.
					add_settings_field(
						$field['id'],
						$field['label'],
						array( $this->parent->admin, 'display_field' ),
						$this->parent->_token . '_settings',
						$section,
						array(
							'field'  => $field,
							'prefix' => $this->base,
						)
					);
				}

				if ( ! $current_section ) {
					break;
				}
			}
		}
	}

	/**
	 * Settings section.
	 *
	 * @param array $section Array of section ids.
	 * @return void
	 */
	public function settings_section( $section ) {
		$html = '<p> ' . $this->settings[ $section['id'] ]['description'] . '</p>' . "\n";
		echo $html; //phpcs:ignore
	}

	/**
	 * Load settings page content.
	 *
	 * @return void
	 */
	public function settings_page() {

		// Build page HTML.
		$html      = '<div class="wrap" id="' . $this->parent->_token . '_settings">' . "\n";
			$html .= '<h2>' . __( 'WP Plugin Advanced Leaflet Settings', 'wp-plugin-advanced-leaflet' ) . '</h2>' . "\n";

			$tab = '';
		//phpcs:disable
		if ( isset( $_GET['tab'] ) && $_GET['tab'] ) {
			$tab .= $_GET['tab'];
		}
		//phpcs:enable

		// Show page tabs.
		if ( is_array( $this->settings ) && 1 < count( $this->settings ) ) {
			$html .= '<h2 class="nav-tab-wrapper">' . "\n";

			$count = 0;
			foreach ( $this->settings as $section => $data ) {

				// Set tab class.
				$class = 'nav-tab';
				if ( ! isset( $_GET['tab'] ) ) { //phpcs:ignore
					if ( 0 === $count ) {
						$class .= ' nav-tab-active';
					}
				} else {
					if ( isset( $_GET['tab'] ) && $section == $_GET['tab'] ) { //phpcs:ignore
						$class .= ' nav-tab-active';
					}
				}

				// Set tab link.
				$tab_link = add_query_arg( array( 'tab' => $section ) );
				if ( isset( $_GET['settings-updated'] ) ) { //phpcs:ignore
					$tab_link = remove_query_arg( 'settings-updated', $tab_link );
				}

				// Output tab.
				$html .= '<a href="' . $tab_link . '" class="' . esc_attr( $class ) . '">' . esc_html( $data['title'] ) . '</a>' . "\n";

				++$count;
			}

			$html .= '</h2>' . "\n";
		}

			$html .= '<form method="post" action="options.php" enctype="multipart/form-data">' . "\n";

				// Get settings fields.
				ob_start();
				settings_fields( $this->parent->_token . '_settings' );
				do_settings_sections( $this->parent->_token . '_settings' );
				$html .= ob_get_clean();

				$html     .= '<p class="submit">' . "\n";
					$html .= '<input type="hidden" name="tab" value="' . esc_attr( $tab ) . '" />' . "\n";
					$html .= '<input name="Submit" type="submit" class="button-primary" value="' . esc_attr( __( 'Save Settings', 'wp-plugin-advanced-leaflet' ) ) . '" />' . "\n";
				$html     .= '</p>' . "\n";
			$html         .= '</form>' . "\n";
		$html             .= '</div>' . "\n";

		echo $html; //phpcs:ignore
	}

	/**
	 * Main WP_Plugin_Advanced_Leaflet_Settings Instance
	 *
	 * Ensures only one instance of WP_Plugin_Advanced_Leaflet_Settings is loaded or can be loaded.
	 *
	 * @since 0.1.0
	 * @static
	 * @see WP_Plugin_Advanced_Leaflet()
	 * @param object $parent Object instance.
	 * @return object WP_Plugin_Advanced_Leaflet_Settings instance
	 */
	public static function instance( $parent ) {
		if ( null === self::$_instance ) {
			self::$_instance = new self( $parent );
		}
		return self::$_instance;
	} // End instance()

	/**
	 * Cloning is forbidden.
	 *
	 * @since 0.1.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, esc_html( __( 'Cloning of WP_Plugin_Advanced_Leaflet_API is forbidden.', 'wp-plugin-advanced-leaflet' ) ), esc_attr( $this->parent->_version ) );
	} // End __clone()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 0.1.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html( __( 'Unserializing instances of WP_Plugin_Advanced_Leaflet_API is forbidden.', 'wp-plugin-advanced-leaflet' ) ), esc_attr( $this->parent->_version ) );
	} // End __wakeup()

}
