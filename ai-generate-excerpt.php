<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/sukafia/
 * @since             1.0.0
 * @package           WP_AI_Excerpt
 *
 * @wordpress-plugin
 * Plugin Name:       WP AI Excerpt
 * Plugin URI:        https://github.com/sukafia/wp-ai-excerpt
 * Description:       Uses AI to generate post excerpts in WordPress.
 * Version:           1.0
 * Author:            Sunday Ukafia
 * Author URI:        https://github.com/sukafia/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-ai-excerpt
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define plugin version.
 */
define( 'WP_AI_EXCERPT_VERSION', '1.0' );

/**
 * The code that runs during plugin activation.
 */
function activate_wp_ai_excerpt() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-ai-excerpt-activator.php';
	WP_AI_Excerpt_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_wp_ai_excerpt() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-ai-excerpt-deactivator.php';
	WP_AI_Excerpt_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wp_ai_excerpt' );
register_deactivation_hook( __FILE__, 'deactivate_wp_ai_excerpt' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wp-ai-excerpt.php';

/**
 * Enqueue Gutenberg script for adding the "Generate Excerpt" button.
 */
function wp_ai_excerpt_enqueue_script() {
    wp_enqueue_script(
        'wp-ai-excerpt-button',
        plugins_url('admin/generate-excerpt-button.js', __FILE__),
        array('wp-edit-post', 'wp-components', 'wp-data', 'wp-api-fetch'),
        filemtime(plugin_dir_path(__FILE__) . 'admin/generate-excerpt-button.js'),
        true
    );
}
add_action('enqueue_block_editor_assets', 'wp_ai_excerpt_enqueue_script');

/**
 * Register REST API endpoint for generating excerpts.
 */
function wp_ai_excerpt_rest_api() {
    register_rest_route('wp-ai-excerpt/v1', '/generate', array(
        'methods'  => 'POST',
        'callback' => 'wp_ai_excerpt_callback',
        'permission_callback' => '__return_true',
    ));
}

/**
 * Callback function for generating an excerpt.
 */
function wp_ai_excerpt_callback(WP_REST_Request $request) {
    $content = sanitize_text_field($request->get_param('content'));

    // AI-based excerpt generation logic (Replace this with actual AI API call)
    $excerpt = substr(strip_tags($content), 0, 150) . '...';

    return rest_ensure_response(array('excerpt' => $excerpt));
}
add_action('rest_api_init', 'wp_ai_excerpt_rest_api');

/**
 * Runs the plugin.
 */
function run_wp_ai_excerpt() {
	$plugin = new WP_AI_Excerpt();
	$plugin->run();
}

run_wp_ai_excerpt();
