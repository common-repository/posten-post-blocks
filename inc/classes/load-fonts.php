<?php 
/**
 * Load Google Fonts
 */

 if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

 if( ! class_exists( 'Posten_Load_Fonts' ) ) {

    class Posten_Load_Fonts {

        private static $all_fonts = [];

        /**
         * Constructor
         * return void 
         */
        public function __construct() {
            add_action( 'wp_enqueue_scripts', [ $this, 'fonts_loader' ] );
            add_action( 'admin_enqueue_scripts', [ $this, 'fonts_loader' ] );
            add_action('posten_render_block', [ $this, 'font_generator' ]);
        }

        /**
         * Font generator
         * return void
         */
        public function font_generator( $block ) {
            if ( isset( $block['attrs'] ) && is_array( $block['attrs'] ) ) {
                $attributes = $block['attrs'];

                foreach ( $attributes as $key => $value ) {
                    if ( ! empty( $value ) && strpos( $key, 'posten_' ) === 0 && strpos( $key, 'FontFamily' ) !== false ) {
                        self::$all_fonts[] = $value;
                    }
                }
            }
        }

        /**
         * Load fonts
         * return void
         */
        public function fonts_loader() {
            if ( is_array( self::$all_fonts ) && count( self::$all_fonts ) > 0 ) {

                $fonts = array_filter( array_unique( self::$all_fonts ) );

                if ( ! empty( $fonts ) ) {

                    $system = array(
                        'Arial',
                        'Tahoma',
                        'Verdana',
                        'Helvetica',
                        'Times New Roman',
                        'Trebuchet MS',
                        'Georgia',
                    );

                    $gfonts = '';
                    $gfonts_attr = ':100,200,300,400,500,600,700,800,900';

                    foreach ($fonts as $font) {
                        if ( ! in_array( $font, $system, true) && ! empty( $font ) ) {
                            $gfonts .= str_replace( ' ', '+', trim( $font ) ) . $gfonts_attr . '|';
                        }
                    }

                    if ( ! empty( $gfonts ) ) {

                        $query_args = [ 'family' => $gfonts ];

                        wp_register_style(
                            'posten-fonts',
                            add_query_arg( $query_args, '//fonts.googleapis.com/css' ),
                            [],
                            POSTEN_VERSION,
                            'all'
                        );

                        wp_enqueue_style( 'posten-fonts', false, [], POSTEN_VERSION, 'all' );
                    }

                    // Reset.
                    $gfonts = '';
                }
            }
        }

    }

 }
    new Posten_Load_Fonts();    // Initialize the class