<?php
/**
 * Enqueue Assets
 */
 
 if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

 if( ! class_exists( 'Posten_Assets' ) ) {

    class Posten_Assets {
        
        /**
         * Constructor
         * return void 
         */
        public function __construct() {
            add_action( 'enqueue_block_editor_assets', [ $this, 'enqueue_editor_assets' ], 2 );
        }

        /**
         * Enqueue Block Assets [ Editor Only ]
         * return void
         */
        public function enqueue_editor_assets(){
            if( file_exists( trailingslashit( POSTEN_DIR_PATH ) . './build/global/global.asset.php' ) ){
                $dependency_file = include_once trailingslashit( POSTEN_DIR_PATH ) . './build/global/global.asset.php';
            }
    
            if( is_array( $dependency_file ) && ! empty( $dependency_file ) ) {
                wp_enqueue_script(
                    'posten-blocks-global-script',
                    trailingslashit( POSTEN_URL ) . './build/global/global.js',
                    isset( $dependency_file['dependencies'] ) ? $dependency_file['dependencies'] : [],
                    isset( $dependency_file['version'] ) ? $dependency_file['version'] : POSTEN_VERSION,
                    true
                );
            }
    
            wp_enqueue_style(
                'posten-blocks-global-style',
                trailingslashit( POSTEN_URL ) . './build/global/global.css',
                [],
                POSTEN_VERSION
            );

            // modules assets
            $this->enqueue_modules_assets();

            // localize script 
            wp_localize_script( 'posten-blocks-global-script', 'postenParams', [
                'ajaxurl'        => admin_url( 'admin-ajax.php' ),
                'post_types'     => PostenFunctions::get_post_types(),
                'get_users'      => PostenFunctions::get_all_users(),
                'get_taxonomies' => PostenFunctions::get_taxonomies(),
                'all_term_list'  => PostenFunctions::get_all_taxonomy(),
                'posten_nonce'     => wp_create_nonce( 'posten-nonce' )
            ] );
        }

        /**
         * Enqueue Modules Assets
         * return void
         */
        public function enqueue_modules_assets() {
            if( file_exists( trailingslashit( POSTEN_DIR_PATH ) . './build/modules/modules.asset.php' ) ){
                $dependency = include_once trailingslashit( POSTEN_DIR_PATH ) . './build/modules/modules.asset.php';
            }
    
            if( is_array( $dependency ) && ! empty( $dependency ) ) {
                wp_enqueue_script(
                    'posten-blocks-modules-script',
                    trailingslashit( POSTEN_URL ) . './build/modules/modules.js',
                    isset( $dependency['dependencies'] ) ? $dependency['dependencies'] : [],
                    isset( $dependency['version'] ) ? $dependency['version'] : POSTEN_VERSION,
                    false
                );
            }
        }
    }

 }

    new Posten_Assets();    // Initialize the class