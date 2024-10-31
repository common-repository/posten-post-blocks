<?php 
/**
 * Register Blocks Category
 */

 if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

 if( ! class_exists( 'Posten_Blocks_Category' ) ) {

    class Posten_Blocks_Category {

        /**
         * Constructor
         * return void 
         */
        public function __construct() {
            if( version_compare( $GLOBALS['wp_version'], '5.7', '<' ) ) {
                add_filter( 'block_categories', [ $this, 'register_block_category' ], 10, 2, 9999 );
            } else {
                add_filter( 'block_categories_all', [ $this, 'register_block_category' ], 10, 2, 9999 );
            }
        }

        /**
         * Register Block Category
         * return array
         */
        public function register_block_category( $categories, $post ) {
            return array_merge(
                array(
                    array(
                        'slug'  => 'posten-blocks',
                        'title' => __( 'Posten Blocks', 'posten-post-blocks' ),
                    ),
                ),
                $categories,
            );
        }

    }

 }

    new Posten_Blocks_Category();    // Initialize the class