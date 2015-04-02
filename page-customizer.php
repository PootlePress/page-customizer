<?php
/**
 * Plugin Name: Page Customizer
 * Plugin URI: http://pootlepress.com/
 * Description:	Adds options for individual pages, posts and products underneath the WordPress editor. Change background image and color, header background image and color, hide titles, menus, breadcrumbs, layouts and footer.
 * Version: 0.7
 * Author: PootlePress
 * Author URI: http://pootlepress.com/
 * Requires at least: 4.0.0
 * Tested up to: 4.1.1
 *
 * Text Domain: pootle-page-customizer
 * Domain Path: /languages/
 *
 * @package Pootle_Page_Customizer
 * @category Core
 * @author PootlePress
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require_once(dirname(__FILE__) . '/includes/class-pootlepress-updater.php');

/**
 * Instantiates Pootlepress_Updater
 */
function pp_updater(){
	if (!function_exists('get_plugin_data')) {
		include(ABSPATH . 'wp-admin/includes/plugin.php');
	}
	$data = get_plugin_data(__FILE__);
	$plugin_current_version = $data['Version'];
	$plugin_remote_path = 'http://www.pootlepress.com/?updater=1';
	$plugin_slug = plugin_basename(__FILE__);
	new Pootlepress_Updater ($plugin_current_version, $plugin_remote_path, $plugin_slug);
}
add_action('init', 'pp_updater');

/**
 * Returns the main instance of Pootle_Page_Customizer to prevent the need to use globals.
 *
 * @since  0.7
 * @return object Pootle_Page_Customizer
 */
function Pootle_Page_Customizer() {
	return Pootle_Page_Customizer::instance();
} // End Pootle_Page_Customizer()

Pootle_Page_Customizer();

/**
 * Main Pootle_Page_Customizer Class
 *
 * @class Pootle_Page_Customizer
 * @version	0.7
 * @since 0.7
 * @package	Pootle_Page_Customizer
 * @author PootlePress
 */
final class Pootle_Page_Customizer {
	/**
	 * Pootle_Page_Customizer The single instance of Pootle_Page_Customizer.
	 * @var 	object
	 * @access  private
	 * @since 	0.7
	 */
	private static $_instance = null;

	/**
	 * The token.
	 * @var     string
	 * @access  public
	 * @since   0.7
	 */
	public $token;

	/**
	 * The version number.
	 * @var     string
	 * @access  public
	 * @since   0.7
	 */
	public $version;

	/**
	 * The plugin directory URL.
	 * @var     string
	 * @access  public
	 * @since   0.7
	 */
	public $plugin_url;

	/**
	 * The plugin directory path.
	 * @var     string
	 * @access  public
	 * @since   0.7
	 */
	public $plugin_path;

	// Admin - Start
	/**
	 * The admin object.
	 * @var     object
	 * @access  public
	 * @since   0.7
	 */
	public $admin;
 
 	/**
	 * The settings object.
	 * @var     object
	 * @access  public
	 * @since   0.7
	 */
	public $settings;

	/**
	 * The post types we support.
	 * @var     array
	 * @access  public
	 * @since   0.7
	 */
	public $supported_post_types = array();

	/**
	 * The taxonomies we support.
	 * @var     array
	 * @access  public
	 * @since   0.7
	 */
	public $supported_taxonomies = array();

	/**
	 * All the post metas to populate.
	 * @var     array
	 * @access  public
	 * @since   0.7
	 */
	public $post_meta = array();

	/**
	 * Array of classes to be put in body
	 * @var array 
	 */
	public $body_classes = array();


	/**
	 * Constructor function.
	 * @access  public
	 * @since   0.7
	 * @return  void
	 */
	public function __construct() {
		$this->token 			= 'pootle-page-customizer';
		$this->plugin_url 		= plugin_dir_url( __FILE__ );
		$this->plugin_path 		= plugin_dir_path( __FILE__ );
		$this->version 			= '0.7';

		register_activation_hook( __FILE__, array( $this, 'install' ) );

		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		add_action( 'init', array( $this, 'setup' ) );

		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'plugin_links' ) );
	}

	/**
	 * Main Pootle_Page_Customizer Instance
	 *
	 * Ensures only one instance of Pootle_Page_Customizer is loaded or can be loaded.
	 *
	 * @since 0.7
	 * @static
	 * @see Pootle_Page_Customizer()
	 * @return Main Pootle_Page_Customizer instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) )
			self::$_instance = new self();
		return self::$_instance;
	} // End instance()

	/**
	 * Load the localisation file.
	 * @access  public
	 * @since   0.7
	 * @return  void
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'pootle-page-customizer', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 0.7
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '0.7' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 0.7
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '0.7' );
	}

	/**
	 * Plugin page links
	 *
	 * @since  0.7
	 */
	public function plugin_links( $links ) {
		$plugin_links = array(
			'<a href="http://support.woothemes.com/">' . __( 'Support', 'pootle-page-customizer' ) . '</a>',
			'<a href="http://docs.woothemes.com/document/pootle-page-customizer/">' . __( 'Docs', 'pootle-page-customizer' ) . '</a>',
		);

		return array_merge( $plugin_links, $links );
	}

	/**
	 * Installation.
	 * Runs on activation. Logs the version number and assigns a notice message to a WordPress option.
	 * @access  public
	 * @since   0.7
	 * @return  void
	 */
	public function install() {
		$this->_log_version_number();
	}

	/**
	 * Log the plugin version number.
	 * @access  private
	 * @since   0.7
	 * @return  void
	 */
	private function _log_version_number() {
		// Log the version number.
		update_option( $this->token . '-version', $this->version );
	}

	/**
	 * Setup all the things.
	 * @return void
	 */
	public function setup() {
		$theme = wp_get_theme();

		$this->get_supported_post_types();
		$this->get_meta_fields();
		
		add_action('admin_init', array($this, 'register_meta_box'));
		add_action('save_post', array($this, 'save_post'));

		add_action('admin_print_scripts', array($this, 'admin_scripts'));
		add_action( 'wp_enqueue_scripts', array( $this, 'styles' ) );
		add_action( 'admin_print_scripts', array( $this, 'script' ) );
		add_action( 'customize_register', array( $this, 'customize_register' ) );
		add_action( 'customize_preview_init', array( $this, 'customize_preview_js' ) );
		add_filter( 'body_class', array( $this, 'body_class' ) );
		add_action( 'admin_notices', array( $this, 'customizer_notice' ) );
	}

	/**
	 * Admin notice
	 * Checks the notice setup in install(). If it exists display it then delete the option so it's not displayed again.
	 * @since   0.7
	 * @return  void
	 */
	public function customizer_notice() {
		$notices = get_option( 'activation_notice' );

		if ( $notices = get_option( 'activation_notice' ) ) {

			foreach ( $notices as $notice ) {
				echo '<div class="updated">' . $notice . '</div>';
			}

			delete_option( 'activation_notice' );
		}
	}

	/**
	 * Customizer Controls and settings
	 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
	 */
	public function customize_register( $wp_customize ) {/*Placeholder for future*/}

	public function register_meta_box() {
		add_meta_box('ppc-meta-box', 'Page Customizer settings', array($this, 'custom_fields'), 'post');
		add_meta_box('ppc-meta-box', 'Page Customizer settings', array($this, 'custom_fields'), 'page');
	}

	public function save_post($postID) {
		$post = get_post($postID);
		 
		//check if post type is post,page or product
		if (!in_array($post->post_type, $this->supported_post_types)) {
			return;
		}

		if (isset($_REQUEST[$this->token]) && is_array($_REQUEST[$this->token])) {
			$PPCValues = $_REQUEST[$this->token];

			//Automating the saving of our post metas
			$all_meta = $this->post_meta;
			foreach($all_meta as $meta){
				$meta_id = $this->get_meta_key($meta['section'], $meta['id']);
				$new_val = $PPCValues[$meta['section']][$meta['id']];
				update_post_meta($postID, $meta_id, $new_val);
			}
		}
	}

	private function get_supported_post_types(){
		$this->supported_post_types = array(
		  'post',
		  'page'
		);
	}
	
	private function get_meta_fields() {
		$this->post_meta = array(
		  //Body Controls
			'background-image' => array(
				'id' => 'background-image',
				'section' => 'body',
				'label' => 'Page background image',
				'type' => 'image',
				'default' => '',
			),
			'background-color' => array(
				'id' => 'background-color',
				'section' => 'body',
				'label' => 'Page background color',
				'type' => 'color',
				'default' => '',
			),
		  //Header Options
			'hide-header' => array(
				'id' => 'hide-header',
				'section' => 'header',
				'label' => 'Hide header',
				'type' => 'checkbox',
				'default' => '',
			),
			'header-background-image' => array(
				'id' => 'header-background-image',
				'section' => 'header',
				'label' => 'Header background image',
				'type' => 'image',
				'default' => '',
			),
			'header-background-color' => array(
				'id' => 'header-background-color',
				'section' => 'header',
				'label' => 'Header background color',
				'type' => 'color',
				'default' => '',
			),
		  //Footer
			'hide-footer' => array(
				'id' => 'hide-footer',
				'section' => 'footer',
				'label' => 'Hide footer',
				'type' => 'checkbox',
				'default' => '',
			),

		);
	}
	
	public function custom_fields() {
		$fields = $this->post_meta;

		foreach ($fields as $key => $field) {
			$this->render_field($field);
		}
	}

	/**
	 * Gets value of post meta
	 * @global type $post
	 * @param type $section
	 * @param type $id
	 * @param type $default
	 * @param type $post_id
	 * @return string
	 */
	protected function get_value($section, $id, $default = null, $post_id=false) {
		//Getting post id if not set
		if( !$post_id ){ global $post; $post_id = $post->ID; }

		$metaKey = $this->get_meta_key($section, $id);

		$ret = get_post_meta($post_id, $metaKey, true);
		if (isset($ret) && $ret != false) {
			return $ret;
		} else {
			return $default;
		}
	}

	private function get_meta_key($section, $id) {
		return '_'.$this->token . '-' . $section . '-' . $id;
	}

	private function get_field_key($section, $id) {
		return $this->token . '[' . $section . '][' . $id . ']';
	}

	/**
	 * Enqueue CSS and custom styles.
	 * @since   0.7
	 * @return  void
	 */
	public function styles() {
		wp_enqueue_style( 'ppc-styles', plugins_url( '/assets/css/style.css', __FILE__ ) );

		$hideHeader = $this->get_value('header', 'hide-header', false, $current_post);
		$headerBgColor = $this->get_value('header', 'header-background-color', null, $current_post);
		$headerBgImage = $this->get_value('header', 'header-background-image', null, $current_post);

		$BgColor = $this->get_value('body', 'background-color', null, $current_post);
		$BgImage = $this->get_value('body', 'background-image', null, $current_post);

		$hideFooter = $this->get_value('footer', 'hide-footer', false, $current_post);

		$css = '';
		wp_add_inline_style( 'ppc-styles', $css );
	}

	/**
	 * Print custom js
	 * @since   0.7
	 * @return  void
	 */
	public function script() {
	?>
	<script id='ppc-script'>
	jQuery(document).ready(function($){
	<?php
	?>
	})
	</script>
	<?php
	}

	/**
	 * Enqueue Js 
	 * @global type $pagenow
	 * @return null
	 */
	public function admin_scripts() {
		global $pagenow;

		if($pagenow=='edit-tags.php'){
			//Though everything is commented this if section is still important coz it prevents returning the function
			//wp_enqueue_script('wp-color-picker');
			//wp_enqueue_script('ppc-tax-script', trailingslashit($this->plugin_url) . 'assets/js/admin/taxonomy.js', array('wp-color-picker', 'thickbox', 'jquery'));
		}elseif(
		  (!isset($pagenow) || !($pagenow == 'post-new.php' || $pagenow == 'post.php'))
		  OR
		  (isset($_REQUEST['post-type']) && strtolower($_REQUEST['post_type']) != 'page')
		) {
			return;
		}

		// only in post and page create and edit screen

		wp_enqueue_script('wp-color-picker');
		wp_enqueue_script('ppc-admin-script', trailingslashit($this->plugin_url) . 'assets/js/admin/admin.js', array('wp-color-picker', 'jquery', 'thickbox'));

		wp_enqueue_style('wp-color-picker');
		wp_enqueue_style('thickbox');
		wp_enqueue_style('ppc-admin-style', trailingslashit($this->plugin_url) . 'assets/css/admin/admin.css');
	}

	
	/**
	 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
	 *
	 * @since  0.7
	 */
	public function customize_preview_js() {
		wp_enqueue_script( 'ppc-customizer', plugins_url( '/assets/js/customizer.min.js', __FILE__ ), array( 'customize-preview' ), '1.1', true );
	}

	/**
	 * SFX Page Customizer Body Class
	 * Adds a class based on the extension name and any relevant settings.
	 */
	public function body_class( $classes ) {
		$this->body_classes[] = 'pootle-page-customizer-active';
		return array_merge($classes, $this->body_classes);
	}

	/**
	 * Render a field of a given type.
	 * @access public
	 * @since 0.7
	 * @param array $args The field parameters.
	 * @param string $output_format = ( post || termEdit || termAdd )
	 * @param array $tax_data - Taxonomy data if rendering for taxonomy
	 * @return string
	 */
	public function render_field ( $args, $output_format = 'post', $tax_data=null ) {
		$html = '';

		if ( ! in_array( $args['type'], array( 'text', 'checkbox', 'radio', 'textarea', 'select', 'color', 'image' ) ) ) return ''; // Supported field type sanity check.

		// Make sure we have some kind of default, if the key isn't set.
		if ( ! isset( $args['default'] ) ) {
			$args['default'] = '';
		}

		$method = 'render_field_' . $args['type'];

		if ( ! method_exists( $this, $method ) ) {
			$method = 'render_field_text';
		}

		// Construct the key.
		$key = $this->get_field_key($args['section'], $args['id']);
		
		//Prefix to field
		$html_prefix = ''
		. '<div class="field ppc-field ' . $args['id'] . '">'
		. '<label class="label" for="' . esc_attr($key) . '">' . esc_html($args['label']) . '</label>'
		. '<div class="control">';

		//Getting current value
		$current_val = $this->get_value( $args['section'], $args['id'], $args['default'] );

		//Suffix to field
		$html_suffix = ''
			. '</div>'
			. '</div>';

		//Prefix
		$html .= $html_prefix;

		//Output the field
		$method_output = $this->$method( $key, $args, $current_val );
		$html .= $method_output;

		// Output the description
		if ( isset( $args['description'] ) ) {
			$description = '<p class="description">' . wp_kses_post( $args['description'] ) . '</p>' . "\n";
			if ( in_array( $args['type'], (array)apply_filters( 'wf_newline_description_fields', array( 'textarea', 'select' ) ) ) ) {
					$description = wpautop( $description );
				}
			$html .= $description;
		}

		//Suffix
		$html .= $html_suffix;
		
		echo $html;
	}

	/**
	 * Render HTML markup for the "text" field type.
	 * @access  protected
	 * @since   1.0
	 * @param   string $key  The unique ID of this field.
	 * @param   array $args  Arguments used to construct this field.
	 * @return  string       HTML markup for the field.
	 */
	protected function render_field_text ( $key, $args, $current_val=null ) {
		$html = '<input id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" size="40" type="text" value="' . esc_attr( $current_val ) . '" />' . "\n";
		return $html;
	}

	/**
	 * Render HTML markup for the "radio" field type.
	 * @access  protected
	 * @since   1.0
	 * @param   string $key  The unique ID of this field.
	 * @param   array $args  Arguments used to construct this field.
	 * @return  string       HTML markup for the field.
	 */
	protected function render_field_radio ( $key, $args, $current_val=null ) {
		$html = '';
		if ( isset( $args['options'] ) && ( 0 < count( (array)$args['options'] ) ) ) {
			foreach ( $args['options'] as $k => $v ) {
				$html .= '<label for="' . esc_attr( $key ) . '"><input type="radio" name="' . esc_attr( $key ) . '" value="' . esc_attr( $k ) . '"' . checked( esc_attr( $current_val ), $k, false ) . ' /> ' . $v . '</label><br>' . "\n";
			}
		}
		return $html;
	}

	/**
	 * Render HTML markup for the "textarea" field type.
	 * @access  protected
	 * @since   1.0
	 * @param   string $key  The unique ID of this field.
	 * @param   array $args  Arguments used to construct this field.
	 * @return  string       HTML markup for the field.
	 */
	protected function render_field_textarea ( $key, $args, $current_val=null ) {
		$html = '<textarea id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" cols="42" rows="5">' . $current_val . '</textarea>' . "\n";
		return $html;
	}

	/**
	 * Render HTML markup for the "checkbox" field type.
	 * @access  protected
	 * @since   1.0
	 * @param   string $key  The unique ID of this field.
	 * @param   array $args  Arguments used to construct this field.
	 * @return  string       HTML markup for the field.
	 */
	protected function render_field_checkbox ( $key, $args, $current_val=null ) {
		$html = '<input id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" type="checkbox" value="true" ' . checked( $current_val, 'true', false ) . ' />';
		return $html;
	}

	/**
	 * Render HTML markup for the "select" field type.
	 * @access  protected
	 * @since   1.0
	 * @param   string $key  The unique ID of this field.
	 * @param   array $args  Arguments used to construct this field.
	 * @return  string       HTML markup for the field.
	 */
	protected function render_field_select ( $key, $args, $current_val=null ) {
		$html = '';
		if ( isset( $args['options'] ) && ( 0 < count( (array)$args['options'] ) ) ) {
			$html .= '<select id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '">' . "\n";
			foreach ( $args['options'] as $k => $v ) {
				$html .= '<option value="' . esc_attr( $k ) . '"' . selected( esc_attr( $current_val ), $k, false ) . '>' . esc_html( $v ) . '</option>' . "\n";
			}
			$html .= '</select>' . "\n";
		}
		return $html;
	}

	/**
	 * Render HTML markup for the "color" field type.
	 * @access  protected
	 * @since   1.0
	 * @param   string $key  The unique ID of this field.
	 * @param   array $args  Arguments used to construct this field.
	 * @return  string       HTML markup for the field.
	 */
	protected function render_field_color( $key, $args, $current_val=null ) {
		$html = '<input class="color-picker-hex" id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" type="text" value="' . esc_attr( $current_val ) . '" />';
		return $html;
	}

	/**
	 * Render HTML markup for the "image" field type.
	 * @access  protected
	 * @since   1.0
	 * @param   string $key  The unique ID of this field.
	 * @param   array $args  Arguments used to construct this field.
	 * @return  string       HTML markup for the field.
	 */
	protected  function render_field_image( $key, $args, $current_val=null ) {
		$html = '<input class="image-upload-path" type="text" id="' . esc_attr($key) . '" style="width: 200px; max-width: 100%;" name="' . esc_attr($key) . '" value="' . esc_attr( $current_val ) . '" /><button class="button upload-button">Upload</button>';
		return $html;
	}

} // End Class