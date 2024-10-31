<?php
/**
 * Enqueue Blocks Frontend Scripts
 */

 if( ! defined( 'ABSPATH' ) ) exit(); // Exit if accessed directly

 if( ! class_exists( 'Posten_Blocks_Frontend_Scripts' ) ) {
    
    class Posten_Blocks_Frontend_Scripts {

        /**
         * Constructor
         * @return void
         */
        public function __construct() {
            // enqueue scripts using wp_enqueue_scripts hook
            add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ], 9999 );
        }

        /**
         * Register blocks
         * @return void
         */ 
        public function enqueue_assets() {

            $blocksFolder = trailingslashit( POSTEN_DIR ) . '/build/blocks';

            if ( is_dir( $blocksFolder ) ) {

                $contents = scandir( $blocksFolder );

                $blocks = array_filter( $contents, function( $item ) use ( $blocksFolder ) {
                    
                    $frontendScripts = $blocksFolder . DIRECTORY_SEPARATOR . $item . DIRECTORY_SEPARATOR . 'frontend.js';
                    $frontendDependencies = $blocksFolder . DIRECTORY_SEPARATOR . $item . DIRECTORY_SEPARATOR . 'frontend.asset.php';

                    if( file_exists( $frontendScripts ) && file_exists( $frontendDependencies ) ) {
                        $frontendDependencies = include_once $frontendDependencies;

                        $block_name = 'posten/' . $item;

                        if( has_block( $block_name ) ) {
                            wp_enqueue_script( 'posten-' . $item . '-frontend-script', trailingslashit( POSTEN_URL ) . 'build/blocks/' . $item . '/frontend.js', $frontendDependencies['dependencies'], $frontendDependencies['version'], true );
                        }
                    }
                });
            } 

        }

    }

 } 
 new Posten_Blocks_Frontend_Scripts();   // Initialize the class