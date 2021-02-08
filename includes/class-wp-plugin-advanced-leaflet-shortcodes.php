<?php
/**
 * ShortCodes class file.
 *
 * @package WP Plugin Template/ShortCodes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ShortCodes class.
 */
class WP_Plugin_Template_ShortCodes {

	/**
	 * The single instance of WP_Plugin_Template_ShortCodes.
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
	 * Prefix for plugin shortcodes.
	 *
	 * @var     string
	 * @access  public
	 * @since   0.1.0
	 */
	public $base = '';

	/**
	 * Prefix for plugin HTML/CSS class shortcodes.
	 *
	 * @var     string
	 * @access  public
	 * @since   0.1.0
	 */
	public $html_base = '';

	/**
	 * Available shortcodes for plugin.
	 *
	 * @var     array
	 * @access  public
	 * @since   0.1.0
	 */
	public $shortcodes = array();

	/**
	 * Constructor function.
	 *
	 * @param object $parent Parent object.
	 */
	public function __construct( $parent ) {
		$this->parent = $parent;

		$this->base      = 'wppt_';
		$this->html_base = 'wppt-';

		// Initialise shortcodes.
		$this->init_shortcodes();

		// Register plugin shortcodes.
		add_action( 'init', array( $this, 'register_shortcodes' ) );
	}

	/**
	 * Initialise shortcodes
	 *
	 * @return void
	 */
	public function init_shortcodes() {
		$this->shortcodes = $this->define_shortcodes();
	}

	/**
	 * Build shortcodes callbacks.
	 *
	 * @return array ShortCodes to be registered as tag -> callback.
	 */
	private function define_shortcodes() {
		$shortcodes = array(
			'powered_by' => 'wppt_powered_by',
		);

		return $shortcodes;
	}

	/**
	 * Build shortcode [wppt_powered_by].
	 *
	 * @param array  $atts     Shortcode attributes. Default empty.
	 * @param string $content  Shortcode content. Default null.
	 * @param string $tag      Shortcode tag (name). Default empty.
	 * @return string The shortcode HTML content
	 */
	public function wppt_powered_by( $atts = array(), $content = null, $tag = '' ) {
		// normalize attribute keys, lowercase.
		$atts = array_change_key_case( (array) $atts, CASE_LOWER );

		// override default attributes with user attributes.
		$sc_atts = shortcode_atts(
			array(),
			$atts
		);

		// Start section.
		$output = __( 'Powered by WP Plugin Template', 'wp-plugin-template' );

		// enclosing tags.
		if ( ! is_null( $content ) ) {
			// run shortcode parser recursively.
			$output .= do_shortcode( $content );
		}

		return $output;
	}

	/**
	 * Register plugin shortcodes.
	 *
	 * @return void
	 */
	public function register_shortcodes() {
		if ( is_array( $this->shortcodes ) ) {
			foreach ( $this->shortcodes as $tag => $callback ) {
				// Add shortcode.
				add_shortcode( $this->base . $tag, array( $this, $callback ) );
			}
		}
	}

	/**
	 * Main WP_Plugin_Template_ShortCodes Instance.
	 *
	 * Ensures only one instance of WP_Plugin_Template_ShortCodes is loaded or can be loaded.
	 *
	 * @since 0.1.0
	 * @static
	 * @see WP_Plugin_Template()
	 * @param object $parent Object instance.
	 * @return object WP_Plugin_Template_ShortCodes instance
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
		_doing_it_wrong( __FUNCTION__, esc_html( __( 'Cloning of WP_Plugin_Template_ShortCodes is forbidden.', 'wp-plugin-template' ) ), esc_attr( $this->parent->_version ) );
	} // End __clone()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 0.1.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html( __( 'Unserializing instances of WP_Plugin_Template_ShortCodes is forbidden.', 'wp-plugin-template' ) ), esc_attr( $this->parent->_version ) );
	} // End __wakeup()

}
