<?php
/**
 * Register Blocks
 */

 if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.
 
 if( ! class_exists( 'Posten_Register_Blocks' ) ) {

    class Posten_Register_Blocks {

        /**
         * Constructor
         * return void 
         */
        public function __construct() {
            add_action( 'init', [ $this, 'register_blocks' ] );
        }

        /**
         * Register Blocks
         * return void
         */
        public function register_blocks() {
            $blocks = [
                'post-grid',
                'post-list',
            ];

            if ( ! empty( $blocks ) ) {
                foreach ( $blocks as $block ) {
                    register_block_type( trailingslashit( POSTEN_DIR ) . '/build/blocks/' . $block);
                }
            }
        }        
    }

 }

    new Posten_Register_Blocks();    // Initialize the class