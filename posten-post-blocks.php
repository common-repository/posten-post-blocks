<?php
/**
 * Plugin Name:       Posten - Post Blocks
 * Description:       Custom Gutenberg Blocks to showcase blog posts in different styles.
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Version:           0.0.1
 * Author:            Zakaria Binsaifullah
 * Author URI:        https://gutenbergkits.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       posten-post-blocks
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! class_exists( 'Posten_Post_Blocks' ) ) {

	final class Posten_Post_Blocks {

		protected static $instance = null;

		/**
		 * Constructor
		 * @return void
		 */
		public function __construct() {
			$this->define_constants();
			$this->includes();
		}

		/**
		 * Definte the plugin constants
		 * @return void
		 */
		public function define_constants() {
			define( 'POSTEN_VERSION', '0.0.1' );
			define( 'POSTEN_DIR', __DIR__ );
			define( 'POSTEN_URL', plugin_dir_url( __FILE__ ) );
			define( 'POSTEN_DIR_PATH', plugin_dir_path( __FILE__ ) );
		}

		/**
		 * Include all the required files
		 * @return void
		 */
		public function includes() {
			require_once __DIR__ . '/inc/init.php';
		}

		/**
		 * Initialize the plugin
		 * @return \Posten_Post_Blocks
		 */
		public static function init() {
			if( is_null( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}
	}
}

/**
 * Initialize the plugin
 * @return \Posten_Post_Blocks
 */
function posten_post_blocks_init() {
	return Posten_Post_Blocks::init();
}

// kick-off the plugin
posten_post_blocks_init();
