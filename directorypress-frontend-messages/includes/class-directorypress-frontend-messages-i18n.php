<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://designinvento.net/
 * @since      1.0.0
 *
 * @package    Directorypress_Frontend_Messages
 * @subpackage Directorypress_Frontend_Messages/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Directorypress_Frontend_Messages
 * @subpackage Directorypress_Frontend_Messages/includes
 * @author     Designinvento <help@designinvento.net>
 */
class Directorypress_Frontend_Messages_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'directorypress-frontend-messages',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
