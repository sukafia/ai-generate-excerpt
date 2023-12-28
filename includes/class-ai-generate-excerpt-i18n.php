<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://github.com/chrishow/
 * @since      1.0.0
 *
 * @package    Ai_Generate_Excerpt
 * @subpackage Ai_Generate_Excerpt/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Ai_Generate_Excerpt
 * @subpackage Ai_Generate_Excerpt/includes
 * @author     Chris How <chrislhow@gmail.com>
 */
class Ai_Generate_Excerpt_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'ai-generate-excerpt',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
