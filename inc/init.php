<?php 
/**
 * Path: inc/init.php
 * Main plugin loader file
 */

 if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

 if( ! class_exists( 'Posten_Blocks_Loader' ) ) {

    class Posten_Blocks_Loader {

        /**
         * Constructor
         * @return void
         */
        public function __construct() {
            $this->includes();
        }

        /**
         * Include all the required files
         * @return void
         */
        public function includes() {
            require_once trailingslashit( POSTEN_DIR_PATH ) . 'inc/classes/blocks-category.php';
            require_once trailingslashit( POSTEN_DIR_PATH ) . 'inc/classes/register-blocks.php';
            require_once trailingslashit( POSTEN_DIR_PATH ) . 'inc/classes/frontend-scripts.php';
            require_once trailingslashit( POSTEN_DIR_PATH ) . 'inc/classes/generate-style.php';
            require_once trailingslashit( POSTEN_DIR_PATH ) . 'inc/classes/load-fonts.php';
            require_once trailingslashit( POSTEN_DIR_PATH ) . 'inc/classes/enqueue-assets.php';

            // ajax
            require_once trailingslashit( POSTEN_DIR_PATH ) . 'inc/classes/ajax.php';

            // functions
            require_once trailingslashit( POSTEN_DIR_PATH ) . 'inc/functions/functions.php';
            // api
            require_once trailingslashit( POSTEN_DIR_PATH ) . 'inc/api/post-query.php';
        }

    }

 }

 new Posten_Blocks_Loader(); // Initialize the class instance