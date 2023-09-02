<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://directorypress.co/
 * @since      1.0.0
 *
 * @package    Directorypress_Advanced_Fields
 * @subpackage Directorypress_Advanced_Fields/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Directorypress_Advanced_Fields
 * @subpackage Directorypress_Advanced_Fields/includes
 * @author     DirectoryPress <team@directorypress.co>
 */
class Directorypress_Advanced_Fields_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'directorypress-advanced-fields',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
