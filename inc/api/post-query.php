<?php
/**
 * Post Query
 */
if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! class_exists( 'PostenPostQuery' ) ) {

    class PostenPostQuery {
        /**
         * Constructor
         * return void 
         */
        public function __construct(){
            add_action("rest_api_init", [$this, 'register_route']);
            add_action('init', [$this, 'register_post_views_meta']);
            add_action('wp_head', [$this, 'increment_post_views']);
        }

        // Register the post meta
        function register_post_views_meta() {
            register_post_meta('post', '_post_views_count', array(
                'type'         => 'integer',
                'single'       => true,
                'description'  => 'Total post views',
                'show_in_rest' => true,
            ));
        }

        // Increment the post views count using post meta
        function increment_post_views() {
            if (is_single()) {
                $post_id = get_the_ID();
                $views   = get_post_meta($post_id, '_post_views_count', true);
                if ($views == '') {
                    $views = 0;
                    delete_post_meta($post_id, '_post_views_count');
                    add_post_meta($post_id, '_post_views_count', '0');
                } else {
                    $views++;
                    update_post_meta($post_id, '_post_views_count', $views);
                }
            }
        }
        
        /**
         * Register Route
         * return void
         */
        public function register_route(){
            register_rest_route('posten/v1', 'posts', [
                'methods'             => 'POST',
                'callback'            => [$this, 'get_all_posts'],
                'permission_callback' => function () {
                    return true;
                }
            ]);
        }
        
        /**
         * Get All Posts
         * return void
         */
        public function get_all_posts($data){
            if ( ! wp_verify_nonce($data['posten_nonce'], 'posten-nonce') ) {
                wp_send_json_error(esc_html__('Session Expired!!', 'posten-post-blocks'));
            }
    
            $results = self::posten_posts_query($data['postQuery']);
    
            if ( ! empty($results["posts"]) ) {
                wp_send_json_success($results);
            } else {
                wp_send_json_error("no post found");
            }
        }
    
        public static function posten_get_post_args($data){
            $excluded_ids   = null;
            $showPagination = !empty($data['showPagination']) && $data['showPagination'] == 'true' ? true : false;
            $args           = [
                'post_status'    => 'publish',
                'post_type'      => isset($data['postType']) ? $data['postType'] : 'post',
                'orderby'        => isset($data['postOrderby']) ? $data['postOrderby'] : 'date',
                'order'          => isset($data['postOrder']) ? $data['postOrder'] : 'desc',
                'posts_per_page' => (int) isset($data['postPerPage']) ? $data['postPerPage'] : 4,
            ];
    
            if (isset($data['postAuthors']) && !empty($data['postAuthors'])) {
                $args['author__in'] = wp_list_pluck($data['postAuthors'], 'value');
            }
    
            if (isset($data['postInclude']) && !empty($data['postInclude'])) {
                $post_ids         = explode(',', $data['postInclude']);
                $post_ids         = array_map('trim', $post_ids);
                $args['post__in'] = $post_ids;
                if ($excluded_ids != null && is_array($excluded_ids)) {
                    $args['post__in'] = array_diff($post_ids, $excluded_ids);
                }
            }
    
            if ($showPagination) {
                $_paged        = is_front_page() ? "page" : "paged";
                $args['paged'] = get_query_var($_paged) ? absint(get_query_var($_paged)) : 1;
            }
    
            if (isset($data['postTaxonomies']) && !empty($data['postTaxonomies'])) {
                foreach ($data['postTaxonomies'] as $index => $texonomy) {
                    if (!empty($texonomy['options'])) {
                        $args['tax_query'][] = [
                            'taxonomy' => $texonomy['name'],
                            'field'    => 'term_id',
                            'terms'    => wp_list_pluck($texonomy['options'], 'value'),
                        ];
                    }
                }
            }
    
            if (isset($data['postExclude']) || isset($data['postOffset'])) {
    
                $excluded_ids = [];
                if ($data['postExclude']) {
                    $excluded_ids = explode(',', $data['postExclude']);
                    $excluded_ids = array_map('trim', $excluded_ids);
                }
    
                $offset_posts = [];
                if ($data['postOffset']) {
                    $_temp_args = $args;
                    unset($_temp_args['paged']);
                    $_temp_args['posts_per_page'] = $data['postOffset'];
                    $_temp_args['fields']         = 'ids';
                    $offset_posts                 = get_posts($_temp_args);
                }
    
                $excluded_post_ids    = array_merge($offset_posts, $excluded_ids);
                $args['post__not_in'] = array_unique($excluded_post_ids);
            }
    
            return apply_filters('posten_post_args', $args);
        }
    
        /**
         * Post Query
         * return void
         */
        public static function posten_posts_query($data){

            $results       = [];
            $args          = self::posten_get_post_args($data);
            $loop          = new WP_Query($args);
            $postThumbnail = !empty($data['postThumbnail']) ? $data['postThumbnail'] : '';
    
            if ($loop->have_posts()) {
    
                while ($loop->have_posts()) {
                    $loop->the_post();
                    $post_id                     = get_the_ID();
                    $content                     = get_post_field('post_content', get_the_ID());
                    $post                        = [];
                    $post['ID']                  = $post_id;
                    $post['title']               = get_the_title();
                    $post["thumbnail"]           = get_the_post_thumbnail($post_id, $postThumbnail);
                    $post['permalink']           = get_permalink();
                    $post['excerpt']             = wp_strip_all_tags(get_the_excerpt());
                    $post['content']             = wp_strip_all_tags(get_the_content());
                    $post['date']                = get_the_date();
                    $post['reading_time']        = self::content_reading_time($content);
                    $post['categories']          = self::posten_get_terms($post_id, 'category');
                    $post['tags']                = self::posten_get_terms($post_id, 'post_tag');
                    $post['total_comments']      = self::posten_get_total_comments($post_id);
                    $post['post_views_count']    = self::posten_post_views_count($post_id);
                    $post["author"]              = get_the_author();
                    $post["author_link"]         = get_the_author_link();
                    $post["author_archive_link"] = get_author_posts_url(get_the_author_meta('ID'));
                    $post["avatar"]              = get_avatar(get_the_author(), 50, '', 'avatar');
    
                    $results[] = $post;
                }
    
                wp_reset_postdata();
            }
    
            return [
                "total_page" => $loop->max_num_pages,
                'posts' => $results
            ];
        }
    
        /**
         * Get Terms
         * return void
         */
        public static function posten_get_terms($post_id, $taxnomy_name){
            $terms = [];
            $taxTerms = wp_get_object_terms($post_id, $taxnomy_name);
            if (!empty($taxTerms)) {
                foreach ($taxTerms as $taxTerm) {
                    $terms[] = sprintf('<a  href="%s">%s</a>', get_term_link($taxTerm), $taxTerm->name);
                }
            }
            return $terms;
        }

        /**
         * Get Post Total Comments 
         * return void
         */
        public static function posten_get_total_comments($post_id){
            $comments_count = wp_count_comments($post_id);
            return $comments_count->total_comments; 
        }

        /**
         * Post Views Count
         * return void
         */
        public static function posten_post_views_count($post_id) {
            return get_post_meta($post_id, '_post_views_count', true);
        }
    
        /**
         * Content Reading Time
         * return void
         */
        public static function content_reading_time($content){
            // Set the average reading speed in words per minute
            $reading_speed = 200;

            // Calculate the word count of the content
            $word_count = str_word_count(wp_strip_all_tags($content));

            // Calculate the reading time in minutes
            $reading_time = round($word_count / $reading_speed);
    
            // Set a minimum reading time of 1 minute
            if ($reading_time < 1) {
                $reading_time = 1;
            }
    
            return $reading_time;
        }
    }
}
new PostenPostQuery(); // initialize the class
