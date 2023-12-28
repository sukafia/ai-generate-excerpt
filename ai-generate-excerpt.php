<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/chrishow/
 * @since             1.0.0
 * @package           Ai_Generate_Excerpt
 *
 * @wordpress-plugin
 * Plugin Name:       AI Generate Excerpt
 * Plugin URI:        https://github.com/chrishow/ai-generate-excerpt
 * Description:       Uses AI to generate post excerpt.
 * Version:           1.0.0
 * Author:            Chris How
 * Author URI:        https://github.com/chrishow//
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ai-generate-excerpt
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'AI_GENERATE_EXCERPT_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-ai-generate-excerpt-activator.php
 */
function activate_ai_generate_excerpt() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ai-generate-excerpt-activator.php';
	Ai_Generate_Excerpt_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-ai-generate-excerpt-deactivator.php
 */
function deactivate_ai_generate_excerpt() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ai-generate-excerpt-deactivator.php';
	Ai_Generate_Excerpt_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_ai_generate_excerpt' );
register_deactivation_hook( __FILE__, 'deactivate_ai_generate_excerpt' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-ai-generate-excerpt.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_ai_generate_excerpt() {

	$plugin = new Ai_Generate_Excerpt();
	$plugin->run();

}
run_ai_generate_excerpt();
