<?php
/**
 * Generate Dynamic Style
 */

 if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

 if( ! class_exists( 'Posten_Dynamic_Style' ) ) {

    class Posten_Dynamic_Style {

        /**
         * Constructor
         * return void 
         */
        public function __construct() {
            add_filter( 'render_block', [ $this, 'generate_dynamic_style' ], 10, 2 );
        }

        function generate_dynamic_style($block_content, $block) {

            if (isset($block['blockName']) && str_contains($block['blockName'], 'posten/')) {
                do_action( 'posten_render_block', $block );

                if (isset($block['attrs']['blockStyle'])) {
                    $style = $block['attrs']['blockStyle'];
                    $handle = isset( $block['attrs']['uniqueId'] ) ? $block['attrs']['uniqueId'] : 'posten-blocks';

                    if ( is_array( $style ) && !empty( $style ) ) {
                        $style = implode(' ', $style);
                    }

                    // minify style to remove extra space
                    $style = preg_replace( '/\s+/', ' ', $style );

                    // register style
                    wp_register_style( $handle, false, [], POSTEN_VERSION, 'all' );
                    wp_enqueue_style( $handle, false, [], POSTEN_VERSION, 'all' );
                    wp_add_inline_style( $handle, $style );
                }
            }

            return $block_content;
        }

    }

 }

    new Posten_Dynamic_Style();    // Initialize the class