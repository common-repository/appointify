<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       appointify.app
 * @since      1.0.0
 *
 * @package    Appointify
 * @subpackage Appointify/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Appointify
 * @subpackage Appointify/includes
 * @author     Appointify
 */
class Appointify_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function appointify_load_plugin_textdomain() {

		load_plugin_textdomain(
			'appointify',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
