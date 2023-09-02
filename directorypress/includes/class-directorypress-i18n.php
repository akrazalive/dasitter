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
 * @package    DirectoryPress
 * @subpackage DirectoryPress/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    DirectoryPress
 * @subpackage DirectoryPress/includes
 * @author     DirectoryPress <team@directorypress.co>
 */
class DirectoryPress_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'DIRECTORYPRESS',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
