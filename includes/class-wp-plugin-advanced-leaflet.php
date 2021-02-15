<?php
/**
 * Main plugin class file.
 *
 * @package WP Plugin Advanced Leaflet/Includes
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Main plugin class.
 */
class WP_Plugin_Advanced_Leaflet {

	/**
	 * The single instance of WP_Plugin_Advanced_Leaflet.
	 *
	 * @var     object
	 * @access  private
	 * @since   0.1.0
	 */
	private static $_instance = null; //phpcs:ignore

	/**
	 * Local instance of WP_Plugin_Advanced_Leaflet_Admin_API
	 *
	 * @var WP_Plugin_Advanced_Leaflet_Admin_API|null
	 */
	public $admin = null;

	/**
	 * Settings class object.
	 *
	 * @var     WP_Plugin_Advanced_Leaflet_Settings|null
	 * @access  public
	 * @since   0.1.0
	 */
	public $settings = null;

	/**
	 * ShortCodes class object.
	 *
	 * @var     WP_Plugin_Advanced_Leaflet_ShortCodes|null
	 * @access  public
	 * @since   0.1.0
	 */
	public $shortcodes_api = null;

	/**
	 * Post types list.
	 *
	 * @var     WP_Plugin_Advanced_Leaflet_Post_Type[]
	 * @access  public
	 * @since   0.1.0
	 */
	public $post_types = array();

	/**
	 * Taxonomies list.
	 *
	 * @var     WP_Plugin_Advanced_Leaflet_Taxonomy[]
	 * @access  public
	 * @since   0.1.0
	 */
	public $taxonomies = array();

	/**
	 * Shortcodes list.
	 *
	 * @var     WP_Plugin_Advanced_Leaflet_Shortcode[]
	 * @access  public
	 * @since   0.1.0
	 */
	public $shortcodes = array();

	/**
	 * The version number.
	 *
	 * @var     string
	 * @access  public
	 * @since   0.1.0
	 */
	public $_version; //phpcs:ignore

	/**
	 * The token.
	 *
	 * @var     string
	 * @access  public
	 * @since   0.1.0
	 */
	public $_token; //phpcs:ignore

	/**
	 * The main plugin file.
	 *
	 * @var     string
	 * @access  public
	 * @since   0.1.0
	 */
	public $file;

	/**
	 * The main plugin directory.
	 *
	 * @var     string
	 * @access  public
	 * @since   0.1.0
	 */
	public $dir;

	/**
	 * The plugin assets directory.
	 *
	 * @var     string
	 * @access  public
	 * @since   0.1.0
	 */
	public $assets_dir;

	/**
	 * The plugin assets URL.
	 *
	 * @var     string
	 * @access  public
	 * @since   0.1.0
	 */
	public $assets_url;

	/**
	 * Suffix for JavaScripts.
	 *
	 * @var     string
	 * @access  public
	 * @since   0.1.0
	 */
	public $script_suffix;

	/**
	 * Constructor funtion.
	 *
	 * @param string $file File constructor.
	 * @param string $version Plugin version.
	 */
	public function __construct( $file = '', $version = '0.1.0' ) {
		$this->_version = $version;
		$this->_token   = 'WP_Plugin_Advanced_Leaflet';

		// Load plugin environment variables.
		$this->file       = $file;
		$this->dir        = dirname( plugin_basename( $this->file ) );
		$this->assets_dir = trailingslashit( $this->dir ) . 'assets';
		$this->assets_url = esc_url( trailingslashit( plugins_url( '/assets/', $this->file ) ) );

		$this->script_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		register_activation_hook( $this->file, array( $this, 'install' ) );

		// Load frontend JS & CSS.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ), 10 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 10 );

		// Load admin JS & CSS.
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 10, 1 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_styles' ), 10, 1 );

		// Load API for generic admin functions.
		if ( is_admin() ) {
			$this->admin = new WP_Plugin_Advanced_Leaflet_Admin_API();
		}

		// Handle localisation.
		$this->load_plugin_textdomain();
		add_action( 'init', array( $this, 'load_localisation' ), 0 );
	} // End __construct ()

	/**
	 * Register post type function.
	 *
	 * @param string $post_type Post Type.
	 * @param string $plural Plural Label.
	 * @param string $single Single Label.
	 * @param string $description Description.
	 * @param array  $options Options array.
	 *
	 * @return bool|string|WP_Plugin_Advanced_Leaflet_Post_Type
	 */
	public function register_post_type( $post_type = '', $plural = '', $single = '', $description = '', $options = array() ) {
		if ( ! $post_type || ! $plural || ! $single ) {
			return false;
		}

		$post_type = new WP_Plugin_Advanced_Leaflet_Post_Type( $post_type, $plural, $single, $description, $options );

		return $post_type;
	}

	/**
	 * Wrapper function to register a new taxonomy.
	 *
	 * @param string $taxonomy Taxonomy.
	 * @param string $plural Plural Label.
	 * @param string $single Single Label.
	 * @param array  $post_types Post types to register this taxonomy for.
	 * @param array  $taxonomy_args Taxonomy arguments.
	 *
	 * @return bool|string|WP_Plugin_Advanced_Leaflet_Taxonomy
	 */
	public function register_taxonomy( $taxonomy = '', $plural = '', $single = '', $post_types = array(), $taxonomy_args = array() ) {
		if ( ! $taxonomy || ! $plural || ! $single ) {
			return false;
		}

		$taxonomy = new WP_Plugin_Advanced_Leaflet_Taxonomy( $taxonomy, $plural, $single, $post_types, $taxonomy_args );

		return $taxonomy;
	}

	/**
	 * Load frontend CSS.
	 *
	 * @access  public
	 * @return void
	 * @since   0.1.0
	 */
	public function enqueue_styles() {
		wp_register_style( $this->_token . '-frontend', esc_url( $this->assets_url ) . 'css/frontend.css', array(), $this->_version );
		wp_enqueue_style( $this->_token . '-frontend' );
	} // End enqueue_styles ()

	/**
	 * Load frontend Javascript.
	 *
	 * @access  public
	 * @return  void
	 * @since   0.1.0
	 */
	public function enqueue_scripts() {
		wp_register_script( $this->_token . '-frontend', esc_url( $this->assets_url ) . 'js/frontend' . $this->script_suffix . '.js', array( 'jquery' ), $this->_version, true );
		wp_enqueue_script( $this->_token . '-frontend' );
	} // End enqueue_scripts ()

	/**
	 * Admin enqueue style.
	 *
	 * @param string $hook Hook parameter.
	 *
	 * @return void
	 */
	public function admin_enqueue_styles( $hook = '' ) {
		wp_register_style( $this->_token . '-admin', esc_url( $this->assets_url ) . 'css/admin.css', array(), $this->_version );
		wp_enqueue_style( $this->_token . '-admin' );
	} // End admin_enqueue_styles ()

	/**
	 * Load admin Javascript.
	 *
	 * @access  public
	 *
	 * @param string $hook Hook parameter.
	 *
	 * @return  void
	 * @since   0.1.0
	 */
	public function admin_enqueue_scripts( $hook = '' ) {
		wp_register_script( $this->_token . '-admin', esc_url( $this->assets_url ) . 'js/admin' . $this->script_suffix . '.js', array( 'jquery' ), $this->_version, true );
		wp_enqueue_script( $this->_token . '-admin' );
	} // End admin_enqueue_scripts ()

	/**
	 * Load plugin localisation
	 *
	 * @access  public
	 * @return  void
	 * @since   0.1.0
	 */
	public function load_localisation() {
		load_plugin_textdomain( 'wp-plugin-advanced-leaflet', false, $this->dir . '/lang/' );
	} // End load_localisation ()

	/**
	 * Load plugin textdomain
	 *
	 * @access  public
	 * @return  void
	 * @since   0.1.0
	 */
	public function load_plugin_textdomain() {
		$domain = 'wp-plugin-advanced-leaflet';

		$locale = apply_filters( 'plugin_locale', get_locale(), $domain ); //phpcs:ignore

		load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, false, $this->dir . '/lang/' );
	} // End load_plugin_textdomain ()

	/**
	 * Main WP_Plugin_Advanced_Leaflet Instance
	 *
	 * Ensures only one instance of WP_Plugin_Advanced_Leaflet is loaded or can be loaded.
	 *
	 * @param string $file File instance.
	 * @param string $version Version parameter.
	 *
	 * @return Object WP_Plugin_Advanced_Leaflet instance
	 * @see WP_Plugin_Advanced_Leaflet()
	 * @since 0.1.0
	 * @static
	 */
	public static function instance( $file = '', $version = '0.1.0' ) {
		if ( null === self::$_instance ) {
			self::$_instance = new self( $file, $version );
		}

		return self::$_instance;
	} // End instance ()

	/**
	 * Cloning is forbidden.
	 *
	 * @since 0.1.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, esc_html( __( 'Cloning of WP_Plugin_Advanced_Leaflet is forbidden', 'wp-plugin-advanced-leaflet' ) ), esc_attr( $this->_version ) );
	} // End __clone ()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 0.1.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html( __( 'Unserializing instances of WP_Plugin_Advanced_Leaflet is forbidden', 'wp-plugin-advanced-leaflet' ) ), esc_attr( $this->_version ) );
	} // End __wakeup ()

	/**
	 * Sanitize JSON
	 *
	 * Takes options for filtering/correcting inputs for use in JavaScript
	 *
	 * @param array $arr     user-input array
	 * @param array $args    array with key-value definitions on how to convert values
	 * @return array corrected for JavaScript
	 */
	public static function json_sanitize($arr, $args)
	{
		// remove nulls
		$arr = self::filter_null($arr);

		// sanitize output
		$args = array_intersect_key($args, $arr);
		$arr = filter_var_array($arr, $args);

		$output = json_encode($arr);

		// always return object; not array
		if ($output === '[]') {
			$output = '{}';
		}

		return $output;
	}

	/**
	 * Filter for removing nulls from array
	 *
	 * @param array $arr
	 *
	 * @return array with nulls removed
	 */
	public static function filter_null($arr)
	{
		if (!function_exists('remove_null')) {
			function remove_null ($var) {
				return $var !== null;
			}
		}

		return array_filter($arr, 'remove_null');
	}

	/**
	 * Parses liquid tags from a string
	 *
	 * @param string $str
	 *
	 * @return array|null
	 */
	public static function liquid ($str) {
		if (!is_string($str)) {
			return null;
		}
		$templateRegex = "/\{ *(.*?) *\}/";
		preg_match_all($templateRegex, $str, $matches);

		if (!$matches[1]) {
			return null;
		}

		$str = $matches[1][0];

		$tags = explode(' | ', $str);

		$original = array_shift($tags);

		if (!$tags) {
			return null;
		}

		$output = array();

		foreach ($tags as $tag) {
			$tagParts = explode(': ', $tag);
			$tagName = array_shift($tagParts);
			$tagValue = implode(': ', $tagParts) || true;

			$output[$tagName] = $tagValue;
		}

		// preserve the original
		$output['original'] = $original;

		return $output;
	}

	/**
	 * Renders a json-like string, removing quotes for values
	 *
	 * allows JavaScript variables to be added directly
	 *
	 * @return string
	 */
	public static function rawDict ($arr) {
		$obj = '{';

		foreach ($arr as $key=>$val) {
			$obj .= "\"$key\": $val,";
		}

		$obj .= '}';

		return $obj;
	}

	/**
	 * Filter for removing empty strings from array
	 *
	 * @param array $arr
	 *
	 * @return array with empty strings removed
	 */
	public static function filter_empty_string($arr)
	{
		if (!function_exists('remove_empty_string')) {
			function remove_empty_string ($var) {
				return $var !== "";
			}
		}

		return array_filter($arr, 'remove_empty_string');
	}

	/**
	 * Add Popups to Shapes
	 *
	 * used by leaflet-marker, leaflet-line and leaflet-circle
	 *
	 * @param array  $atts    user-input array
	 * @param string $content text to display
	 * @param string $shape   JavaScript variable for shape
	 *
	 * @return null
	 */
	public static function add_popup_to_shape($atts, $content, $shape)
	{
		if (!empty($atts)) {
			extract($atts);
		}

		$message = empty($message) ?
			(empty($content) ? '' : $content) : $message;
		$message = str_replace(array("\r\n", "\n", "\r"), '<br>', $message);
		$message = addslashes($message);
		$message = htmlspecialchars($message);
		$visible = empty($visible)
			? false
			: filter_var($visible, FILTER_VALIDATE_BOOLEAN);

		if (!empty($message)) {
			echo "{$shape}.bindPopup(window.WPLeafletMapPlugin.unescape('{$message}'))";
			if ($visible) {
				echo ".openPopup()";
			}
			echo ";";
		}
	}

	/**
	 * Get Style JSON for map shapes/geojson (svg or canvas)
	 *
	 * Takes atts for creating shapes on the map
	 *
	 * @param array $atts    user-input array
	 *
	 * @return array corrected for JavaScript
	 */
	public static function get_style_json($atts)
	{
		if ($atts) {
			extract($atts);
		}

		// from http://leafletjs.com/reference-1.0.3.html#path
		$style = array(
			'stroke' => isset($stroke) ? $stroke : null,
			'color' => isset($color) ? $color : null,
			'weight' => isset($weight) ? $weight : null,
			'opacity' => isset($opacity) ? $opacity : null,
			'lineCap' => isset($linecap) ? $linecap : null,
			'lineJoin' => isset($linejoin) ? $linejoin : null,
			'dashArray' => isset($dasharray) ? $dasharray : null,
			'dashOffset' => isset($dashoffset) ? $dashoffset : null,
			'fill' => isset($fill) ? $fill : null,
			'fillColor' => isset($fillcolor) ? $fillcolor : null,
			'fillOpacity' => isset($fillopacity) ? $fillopacity : null,
			'fillRule' => isset($fillrule) ? $fillrule : null,
			'className' => isset($classname) ? $classname : null,
			'radius' => isset($radius) ? $radius : null
		);

		$args = array(
			'stroke' => FILTER_VALIDATE_BOOLEAN,
			'color' => FILTER_SANITIZE_STRING,
			'weight' => FILTER_VALIDATE_FLOAT,
			'opacity' => FILTER_VALIDATE_FLOAT,
			'lineCap' => FILTER_SANITIZE_STRING,
			'lineJoin' => FILTER_SANITIZE_STRING,
			'dashArray' => FILTER_SANITIZE_STRING,
			'dashOffset' => FILTER_SANITIZE_STRING,
			'fill' => FILTER_VALIDATE_BOOLEAN,
			'fillColor' => FILTER_SANITIZE_STRING,
			'fillOpacity' => FILTER_VALIDATE_FLOAT,
			'fillRule' => FILTER_SANITIZE_STRING,
			'className' => FILTER_SANITIZE_STRING,
			'radius' => FILTER_VALIDATE_FLOAT
		);

		return self::json_sanitize($style, $args);
	}

	/**
	 * Installation. Runs on activation.
	 *
	 * @access  public
	 * @return  void
	 * @since   0.1.0
	 */
	public function install() {
		$this->_log_version_number();
	} // End install ()

	/**
	 * Log the plugin version number.
	 *
	 * @access  public
	 * @return  void
	 * @since   0.1.0
	 */
	private function _log_version_number() { //phpcs:ignore
		update_option( $this->_token . '_version', $this->_version );
	} // End _log_version_number ()

	/**
	 * Uninstallation. Runs on uninstall.
	 *
	 * @access  public
	 * @return  void
	 * @since   0.1.0
	 */
	public function uninstall() {
		$this->_delete_version_number();
	} // End install ()

	/**
	 * Remove the plugin version number.
	 *
	 * @access  public
	 * @return  void
	 * @since   0.1.0
	 */
	private function _delete_version_number() { //phpcs:ignore
		delete_option( $this->_token . '_version' );
	} // End _delete_version_number ()

}
