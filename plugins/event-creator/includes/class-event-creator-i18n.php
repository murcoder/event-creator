<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       www.murdesign.at
 * @since      1.0.0
 *
 * @package    Event_Creator
 * @subpackage Event_Creator/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Event_Creator
 * @subpackage Event_Creator/includes
 * @author     Christoph Murauer <christoph.murauer@speedy-space.com>
 */
class Event_Creator_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		// load_plugin_textdomain(
		// 	'event-creator',
		// 	false,
		// 	dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		// );

		load_plugin_textdomain(
			'de_AT',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
