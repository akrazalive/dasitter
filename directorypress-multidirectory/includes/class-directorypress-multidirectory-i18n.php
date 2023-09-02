<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link        https://directorypress.co/
 * @since      1.0.0
 *
 * @package    Directorypress_Multidirectory
 * @subpackage Directorypress_Multidirectory/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Directorypress_Multidirectory
 * @subpackage Directorypress_Multidirectory/includes
 * @author     DirectoryPress <team@directorypress.co>
 */
class Directorypress_Multidirectory_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'directorypress-multidirectory',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
