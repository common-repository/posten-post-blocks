<?php
/**
 * Posten Functions Helper
 */
if (!defined('ABSPATH')) exit; // Exit if accessed directly

if( ! class_exists( 'PostenFunctions' ) ) {

    class PostenFunctions {
        /**
         * Get Posten Post Types 
         * return void
         */
        public static function get_post_types(){
            $post_types = get_post_types(
                [
                    'public'            => true,
                    'show_in_nav_menus' => true,
                ],
                'objects'
            );
            $post_types     = wp_list_pluck($post_types, 'label', 'name');
            $excluded_types = apply_filters('posten_exclude_post_type', [
                'attachment'          => 'Attachment',
                'elementor_library'   => 'Elementor Library',
                'product_variation'   => 'Product Variation',
                'shop_order'          => 'Shop Order',
                'shop_coupon'         => 'Shop Coupon',
                'acf-field-group'     => 'ACF Field Group',
                'acf-field'           => 'ACF Field',
                'oembed_cache'        => 'oEmbed Cache',
                'user_request'        => 'User Request',
                'wpcf7_contact_form'  => 'Contact Form 7',
                'customize_changeset' => 'Customize Changeset',
                'custom_css'          => 'Custom CSS'
            ]);
            return array_diff_key($post_types, $excluded_types);
        }
    
        /**
         * Get Posten All Users 
         * return void
         */
        public static function get_all_users(){
            $users   = [];
            $authors = get_users(apply_filters('posten_author_arg', []));
            if (!empty($authors)) {
                foreach ($authors as $user) {
                    $users[] = array('value' => $user->ID, 'label' => $user->display_name);
                }
            }
            return $users;
        }
    
        /**
         * Get Posten Taxonomies
         * return void
         */
        public static function get_taxonomies(){
            $get_tax_object = get_taxonomies([], 'objects');
            $exclude_tax    = self::get_excluded_taxonomy();
            foreach ($exclude_tax as $_tax) {
                unset($get_tax_object[$_tax]);
            }
            return $get_tax_object;
        }
    
        /**
         * Get Posten All Categories
         * return void
         */
        public static function get_all_taxonomy(){
            $post_types     = self::get_post_types();
            $taxonomies     = get_taxonomies([], 'objects');
            $all_taxonomies = [];
            foreach ($taxonomies as $taxonomy => $object) {
                if (
                    !isset($object->object_type[0]) || !in_array($object->object_type[0], array_keys($post_types))
                    || in_array($taxonomy, self::get_excluded_taxonomy())
                ) {
                    continue;
                }
                $all_taxonomies[$taxonomy] = self::get_terms_by_texonomy($taxonomy);
            }
            return $all_taxonomies;
        }
    
        /**
         * Get Posten Excluded Taxonomy
         * return void
         */
        public static function get_excluded_taxonomy(){
            return apply_filters('posten_exclude_taxonomy', [
                'post_format',
                'nav_menu',
                'link_category',
                'wp_theme',
                'elementor_library_type',
                'elementor_library_type',
                'elementor_library_category',
                'product_visibility',
                'product_shipping_class',
                'product_type'
            ]);
        }
    
        /**
         * Get Posten Terms By Texonomy
         * return void
         */
        public static function get_terms_by_texonomy($cat = 'category'){
            $terms = get_terms([
                'taxonomy'   => $cat,
                'hide_empty' => true,
            ]);
    
            $options = [];
            if (!empty($terms) && !is_wp_error($terms)) {
                foreach ($terms as $term) {
                    $options[$term->term_id] = $term->name;
                }
            }
    
            return $options;
        }
    
        /**
         * Wrapper Class
         * return void
         */
        public static function get_wrapper_class($settings = [], $class_name = ''){
            $wrap_class = '';
    
            if (isset($settings['uniqueId'])) {
                $wrap_class .= $settings['uniqueId'];
            }
    
            if (!empty($class_name)) {
                $wrap_class .= ' ' . $class_name;
            }
    
            return $wrap_class;
        }
    
        public static function removeHtmlTagContents($contant, $tags){
            if (is_array($tags)) {
                foreach ($tags as $tag) {
                    $contant = preg_replace(
                        sprintf(
                            '/<%1$s\b[^>]*>(.*?)<\/%1$s>/is',
                            $tag
                        ),
                        '',
                        $contant
                    );
                }
            } else {
                $contant = preg_replace('/<figure\b[^>]*>(.*?)<\/figure>/is', '', $contant);
            }
    
            return $contant;
        }
    
        /**
         * Pagination
         * return void
         */
        public static function pagination($max_pages){
            global $paged;
    
            if (!empty(get_query_var('page')) || !empty(get_query_var('paged'))) {
                $paged = is_front_page() ? absint(get_query_var('page')) : absint(get_query_var('paged'));
            } else {
                $paged = 1;
            }
    
            if ($max_pages > 1) {
                $big = 9999999;
                return paginate_links(array(
                    'base'      => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
                    'format'    => '?paged=%#%',
                    'current'   => $paged,
                    'total'     => $max_pages,
                    'prev_text' => sprintf('<span>%1$s</span>', __('prev', 'posten-blocks')),
                    'next_text' => sprintf('<span>%1$s</span>', __('next', 'posten-blocks')),
                ));
            }
        }
    }

}

new PostenFunctions(); // Initialize the class instance
