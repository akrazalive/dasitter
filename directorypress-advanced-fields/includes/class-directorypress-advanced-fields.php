<?php

/**
 * @since      1.0.0
 * @package    Directorypress_Advanced_Fields
 * @subpackage Directorypress_Advanced_Fields/includes
 * @author     DirectoryPress <team@directorypress.co>
 */
class Directorypress_Advanced_Fields {

	
	protected $loader;
	protected $plugin_name;
	protected $version;

	public function __construct() {
		if ( defined( 'DIRECTORYPRESS_ADVANCED_FIELDS_VERSION' ) ) {
			$this->version = DIRECTORYPRESS_ADVANCED_FIELDS_VERSION;
		} else {
			$this->version = '1.0.2';
		}
		$this->plugin_name = 'directorypress-advanced-fields';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-directorypress-advanced-fields-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-directorypress-advanced-fields-i18n.php';
		
		include_once DIRECTORYPRESS_ADVANCED_FIELDS_PATH . 'includes/functions.php';
		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		
		
		
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-directorypress-advanced-fields-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-directorypress-advanced-fields-public.php';

		$this->loader = new Directorypress_Advanced_Fields_Loader();

	}

	private function set_locale() {

		$plugin_i18n = new Directorypress_Advanced_Fields_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	private function define_admin_hooks() {

		$plugin_admin = new Directorypress_Advanced_Fields_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

	}

	private function define_public_hooks() {

		$plugin_public = new Directorypress_Advanced_Fields_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	public function run() {
		add_action('directorypress_before_fields_loaded', array($this, 'inludes'));
		add_action('directorypress_before_search_fields_loaded', array($this, 'inludes_search_filters'));
		add_filter("directorypress_fields_types_name" , array($this, 'fields_type_names'));
		add_action('directorypress_fields_types_options', array($this, 'fields_type_options'), 10, 2);
		$this->loader->run();
	}

	public function inludes() {
		include_once DIRECTORYPRESS_ADVANCED_FIELDS_PATH . 'includes/fields/attachment/attachment.php';
		include_once DIRECTORYPRESS_ADVANCED_FIELDS_PATH . 'includes/fields/digit/digit.php';
		include_once DIRECTORYPRESS_ADVANCED_FIELDS_PATH . 'includes/fields/checkbox/checkbox.php';
		include_once DIRECTORYPRESS_ADVANCED_FIELDS_PATH . 'includes/fields/color/color.php';
		include_once DIRECTORYPRESS_ADVANCED_FIELDS_PATH . 'includes/fields/radio/radio.php';
		include_once DIRECTORYPRESS_ADVANCED_FIELDS_PATH . 'includes/fields/hours/hours.php';
		include_once DIRECTORYPRESS_ADVANCED_FIELDS_PATH . 'includes/fields/status/status.php';
	}
	
	public function inludes_search_filters() {
		include_once DIRECTORYPRESS_ADVANCED_FIELDS_PATH . 'includes/search/filters/checkbox/class-checkbox.php';
		include_once DIRECTORYPRESS_ADVANCED_FIELDS_PATH . 'includes/search/filters/radio/class-radio.php';
		include_once DIRECTORYPRESS_ADVANCED_FIELDS_PATH . 'includes/search/filters/digit/class-digit.php';
		include_once DIRECTORYPRESS_ADVANCED_FIELDS_PATH . 'includes/search/filters/price/class-price.php';
	}
	
	public function fields_type_names($types) {
		$types = $types + array(
				'digit' => __('Digit', 'directorypress-advanced-fields'),
				'radio' => __('Radio', 'directorypress-advanced-fields'),
				'checkbox' => __('Checkbox', 'directorypress-advanced-fields'),
				'color' => __('Color', 'directorypress-advanced-fields'),
				'attachment' => __('Attachment', 'directorypress-advanced-fields'),
				'hours' => __('Hours', 'directorypress-advanced-fields'),
				'status' => __('Status', 'directorypress-advanced-fields'),
		);
		return $types;
	}
	
	
	public function fields_type_options($field, $fields_types_names) {
		echo '<option value="attachment" '. selected($field->type, 'attachment') .' >'. $fields_types_names['attachment'] .'</option>';
		echo '<option value="radio" '. selected($field->type, 'radio') .' >'. $fields_types_names['radio'] .'</option>';
		echo '<option value="checkbox" '. selected($field->type, 'checkbox') .' >'. $fields_types_names['checkbox'] .'</option>';
		echo '<option value="hours" '. selected($field->type, 'hours') .' >'. $fields_types_names['hours'] .'</option>';
		echo '<option value="digit" '. selected($field->type, 'digit') .' >'. $fields_types_names['digit'] .'</option>';
		echo '<option value="color" '. selected($field->type, 'color') .' >'. $fields_types_names['color'] .'</option>';
		//echo '<option value="status" '. selected($field->type, 'status') .' >'. $fields_types_names['status'] .'</option>';
	}
	
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	public function get_loader() {
		return $this->loader;
	}

	public function get_version() {
		return $this->version;
	}

}
