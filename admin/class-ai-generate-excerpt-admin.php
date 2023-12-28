<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/chrishow/
 * @since      1.0.0
 *
 * @package    Ai_Generate_Excerpt
 * @subpackage Ai_Generate_Excerpt/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Ai_Generate_Excerpt
 * @subpackage Ai_Generate_Excerpt/admin
 * @author     Chris How <chrislhow@gmail.com>
 */
class Ai_Generate_Excerpt_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;


		add_action("wp_ajax_ai_generate_excerpt", array($this, 'ai_generate_excerpt'));


		add_action('admin_menu', 'ai_generate_excerpt_admin_add_page');
		function ai_generate_excerpt_admin_add_page() {
			add_options_page('AI Generate Excerpt', 'AI Generate Excerpt', 'manage_options', 'ai-generate-excerpt', array('Ai_Generate_Excerpt_Admin', 'plugin_options_page'));
		}


		add_action('admin_init', 'ai_generate_settings_init');
		function ai_generate_settings_init() {
			register_setting( 'plugin_options', 'ai_generate_excerpt_options', 'plugin_options_validate' );
			add_settings_section('plugin_main', 'Hugging Face API key', 'ai_generate_excerpt_section_text', 'plugin');
			add_settings_field('plugin_text_string', 'API key', 'ai_generate_excerpt_setting_string', 'plugin', 'plugin_main');

			function ai_generate_excerpt_options() {
				// Validator
			}

			function ai_generate_excerpt_section_text() {
				echo '<p>You need to add an API key for <a href="https://huggingface.co/" target="_blank">Hugging Face‘s AI Inference API</a>. </p>';
			}

			function ai_generate_excerpt_setting_string() {
				$options = get_option('ai_generate_excerpt_options');
				echo "<input id='plugin_text_string' name='ai_generate_excerpt_options[api_key]' size='40' type='text' value='{$options['api_key']}' />";
				
			}

		}
	}

	/**
	 * Draw the options for this plugin
	 */
	public static function plugin_options_page() {
		?>
		<div class=wrap>
		<h1>AI Generate Excerpt</h1>

		<form action="options.php" method="post">
		<?php settings_fields('plugin_options'); ?>
		<?php do_settings_sections('plugin'); ?>
		 
		<input name="Submit" class='button' type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
		</form></div>
		 
		<?php
	}

	/**
	 * Generate the excerpt
	 */
	public function ai_generate_excerpt() {
		// error_log('ai_generate_excerpt');

		$options = get_option('ai_generate_excerpt_options');
		$api_key = $options['api_key'] ?? NULL;

		if(!function_exists('curl_init')) {
			$response = [
				'type' => "error",
				'message' => "The cURL PHP extension must be installed to use this feature!"
			];
		} else if (!$api_key) {
			$response = [
				'type' => "error",
				'message' => "You must set a Hugging Face API key in Settings -> AI Generate Excerpt!"
			];
		} else {
			// Attempt to retrieve summary

			preg_match('/post=(.+)&/', wp_get_referer(), $matches);
			$post_id = $matches[1];

			// Get full post content
			if(class_exists('Wayve')) {
				$content = Wayve::get_post_full_content($post_id);
			} else {
				$content = apply_filters('the_content', get_post_field('post_content', $post_id));
			}

			$content = wp_strip_all_tags($content);
			// error_log($content);

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,"https://api-inference.huggingface.co/models/Falconsai/text_summarization");
			curl_setopt($ch, CURLOPT_POST, 1);

			$post_data = [
				'inputs' => $content,
				'type' => 'text-summary',
				'parameters' => [ 
					'max_new_tokens' =>  NULL,
					'max_length' => 200,
					'min_length' => 30,
					'options' => "{wait_for_model:true}"
				]
			];

			curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);  //Post Fields
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			
			$headers = [
				'Bearer: ' . $api_key,
				'Content-Type: application/json',
			];
			
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			
			$server_output = curl_exec ($ch);
			curl_close ($ch);
			
			error_log($server_output);

			$summary = NULL;

			// Default error message
			$error_message = 'The AI was unable to generate a summary for an unknown reason.';

			try {
				$json = json_decode($server_output, true);

				if(isset($json['error'])) {
					if($json['error'] == "Rate limit reached. Please log in or use your apiToken") {
						$error_message = "AI API rate limit reached. Please try again later.";
					} else {
						$error_message = $json['error'];
					}
				} else {
					$summary = $json[0]['summary_text'];
				}
				error_log(print_r($json, true));

				// Strip weird spaces before full-stops
				$summary = str_replace(' .', '.', $summary);

			} catch (Exception $e) {
				error_log(print_r($server_output, TRUE));
			}




			if($summary) {
				$response = [
					'type' => "success",
					'excerpt' => $summary
				];
			} else {
				$response = [
					'type' => "error",
					'message' => $error_message
				];
			}
		}
		

		echo json_encode($response);
		die(); // or WP returns 0!
	}


	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Ai_Generate_Excerpt_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Ai_Generate_Excerpt_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/ai-generate-excerpt-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Ai_Generate_Excerpt_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Ai_Generate_Excerpt_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/ai-generate-excerpt-admin.js', array( 'jquery' ), $this->version, false );

	}

}
