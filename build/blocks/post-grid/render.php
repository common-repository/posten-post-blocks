<?php
/**
 * Post Grid Frontend Template.
 */

 if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

 if( ! function_exists( 'posten_post_grid_callback' ) ) {

    function posten_post_grid_callback( $attributes ) {

            $defaultAttributes = [
                'pcVisibility'  => [
                    'showFeaturedImage' => true,
                    'showPostMeta'      => true,
                    'showCat'           => true,
                    'showAuthor'        => true,
                    'showDate'          => true,
                    'showComments'      => true,
                    'showViews'         => true,
                    'showReadingTime'   => true,
                    'showTitle'         => true,
                    'showExcerpt'       => true,
                    'showBtn'           => true
                ]
            ];

            $attributes     = wp_parse_args( $attributes, $defaultAttributes );
            $postQuery     = $attributes['postQuery'] ?? [];
            $postResults   = apply_filters('posten_posts_grid', PostenPostQuery::posten_posts_query($postQuery));
            $uniqueId      = isset( $attributes['uniqueId'] ) ? $attributes['uniqueId'] : '';
            $pcVisibility  = $attributes['pcVisibility'] ?? [];
            $titleTag      = isset( $attributes['titleTag'] ) ? $attributes['titleTag'] : 'h1';
            $excerptLength = isset( $attributes['excerptLength'] ) ? $attributes['excerptLength'] : 15;
            $btnLabel      = isset( $attributes['btnLabel'] ) ? $attributes['btnLabel'] : 'Read More';
            $wrapperClass  = get_block_wrapper_attributes([
                'class' => esc_attr( $uniqueId )
            ]);

        ?>
            <div <?php echo wp_kses_data( ( wp_sprintf( '%s', $wrapperClass ) ) ); ?>>
                <div class="posten-post-container posten-grid">
                    <?php 
                        if( ! empty( $postResults ) && is_countable( $postResults ) && count( $postResults ) > 0 ) :
                            foreach ($postResults['posts'] as $result) :
                                $post = (object) $result;
                    ?>
                        <div class="posten-item">
                            <?php if( $pcVisibility['showFeaturedImage'] ) : ?>
                            <div class="posten-head">
                                <a class="posten-thumbnail" href="<?php echo esc_url( $post->permalink );?>">
                                    <?php 
                                        echo wp_sprintf( '%s', $post->thumbnail );
                                    ?>
                                </a>
                                <?php if( $pcVisibility['showPostMeta'] && $pcVisibility['showCat'] ) : ?>
                                <div class="posten-cats">
                                    <?php 
                                        $cats = $post->categories;
                                        if( ! empty( $cats ) && is_countable( $cats ) && count( $cats ) > 0 ) :
                                            foreach ($cats as $cat) :
                                    ?>
                                        <div class="posten-cat">
                                            <?php 
                                                echo wp_sprintf( '%s', $cat );
                                            ?>
                                        </div>
                                    <?php 
                                            endforeach;
                                        endif;
                                    ?>
                                </div>
                                <?php endif; ?>
                                <?php if( $pcVisibility['showReadingTime'] ) : ?>
                                <div class="posten-reading-time">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        width="16"
                                        height="16"
                                        fill="currentColor"
                                        class="bi bi-clock-history"
                                        viewBox="0 0 16 16"
                                    >
                                        <path
                                            d="M8.515 1.019A7 7 0 0 0 8 1V0a8 8 0 0 1 .589.022zm2.004.45a7.003 7.003 0 0 0-.985-.299l.219-.976c.383.086.76.2 1.126.342zm1.37.71a7.01 7.01 0 0 0-.439-.27l.493-.87a8.025 8.025 0 0 1 .979.654l-.615.789a6.996 6.996 0 0 0-.418-.302zm1.834 1.79a6.99 6.99 0 0 0-.653-.796l.724-.69c.27.285.52.59.747.91l-.818.576zm.744 1.352a7.08 7.08 0 0 0-.214-.468l.893-.45a7.976 7.976 0 0 1 .45 1.088l-.95.313a7.023 7.023 0 0 0-.179-.483m.53 2.507a6.991 6.991 0 0 0-.1-1.025l.985-.17c.067.386.106.778.116 1.17l-1 .025zm-.131 1.538c.033-.17.06-.339.081-.51l.993.123a7.957 7.957 0 0 1-.23 1.155l-.964-.267c.046-.165.086-.332.12-.501zm-.952 2.379c.184-.29.346-.594.486-.908l.914.405c-.16.36-.345.706-.555 1.038l-.845-.535m-.964 1.205c.122-.122.239-.248.35-.378l.758.653a8.073 8.073 0 0 1-.401.432l-.707-.707z"
                                        />
                                        <path
                                            d="M8 1a7 7 0 1 0 4.95 11.95l.707.707A8.001 8.001 0 1 1 8 0z"
                                        />
                                        <path
                                            d="M7.5 3a.5.5 0 0 1 .5.5v5.21l3.248 1.856a.5.5 0 0 1-.496.868l-3.5-2A.5.5 0 0 1 7 9V3.5a.5.5 0 0 1 .5-.5"
                                        />
                                    </svg>
                                    <?php 
                                        echo wp_sprintf( '%s %2s', $post->reading_time, $post->reading_time > 1 ? 'mins' : 'min' );
                                    ?>
                                </div>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                            <div class="posten-body">
                                <?php if( $pcVisibility['showPostMeta'] ) : ?>
                                <div class="posten-meta">
                                    <?php if( $pcVisibility['showAuthor'] ) : ?>
                                    <div class="posten-single-meta">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            width="16"
                                            height="16"
                                            fill="currentColor"
                                            class="bi bi-person-fill"
                                            viewBox="0 0 16 16"
                                        >
                                            <path
                                            d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6"
                                            />
                                        </svg>

                                        <a href="<?php echo esc_url( $post->author_archive_link );?>">
                                            <?php 
                                                echo wp_sprintf( '%s', ucfirst( $post->author ) );
                                            ?>
                                        </a>
                                    </div>
                                    <?php endif; ?>
                                    <?php if( $pcVisibility['showDate'] ) : ?>
                                    <div class="posten-single-meta">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            width="16"
                                            height="16"
                                            fill="currentColor"
                                            class="bi bi-calendar2-plus"
                                            viewBox="0 0 16 16"
                                        >
                                            <path
                                            d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M2 2a1 1 0 0 0-1 1v11a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1z"
                                            />
                                            <path
                                            d="M2.5 4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5H3a.5.5 0 0 1-.5-.5zM8 8a.5.5 0 0 1 .5.5V10H10a.5.5 0 0 1 0 1H8.5v1.5a.5.5 0 0 1-1 0V11H6a.5.5 0 0 1 0-1h1.5V8.5A.5.5 0 0 1 8 8"
                                            />
                                        </svg>
                                        <?php 
                                            echo wp_sprintf( '%s', $post->date );
                                        ?>
                                    </div>
                                    <?php endif; ?>
                                    <?php if( $pcVisibility['showComments'] ) : ?>
                                    <div class="posten-single-meta">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            width="16"
                                            height="16"
                                            fill="currentColor"
                                            class="bi bi-chat-text"
                                            viewBox="0 0 16 16"
                                        >
                                            <path
                                            d="M2.678 11.894a1 1 0 0 1 .287.801 10.97 10.97 0 0 1-.398 2c1.395-.323 2.247-.697 2.634-.893a1 1 0 0 1 .71-.074A8.06 8.06 0 0 0 8 14c3.996 0 7-2.807 7-6 0-3.192-3.004-6-7-6S1 4.808 1 8c0 1.468.617 2.83 1.678 3.894m-.493 3.905a21.682 21.682 0 0 1-.713.129c-.2.032-.352-.176-.273-.362a9.68 9.68 0 0 0 .244-.637l.003-.01c.248-.72.45-1.548.524-2.319C.743 11.37 0 9.76 0 8c0-3.866 3.582-7 8-7s8 3.134 8 7-3.582 7-8 7a9.06 9.06 0 0 1-2.347-.306c-.52.263-1.639.742-3.468 1.105z"
                                            />
                                            <path
                                            d="M4 5.5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5M4 8a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7A.5.5 0 0 1 4 8m0 2.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1-.5-.5"
                                            />
                                        </svg>
                                        <?php 
                                            echo wp_sprintf( '%s %2s', $post->total_comments, $post->total_comments > 1 ? 'comments' : 'comment' );
                                        ?>
                                    </div>
                                    <?php endif; ?>
                                    <?php if( $pcVisibility['showViews'] ) : ?>
                                    <div class="posten-single-meta">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-bar-chart" viewBox="0 0 16 16">
                                            <path d="M4 11H2v3h2zm5-4H7v7h2zm5-5v12h-2V2zm-2-1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1zM6 7a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1zm-5 4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1z"/>
                                        </svg>
                                        <?php 
                                            echo wp_sprintf( '%s', $post->post_views_count ? $post->post_views_count : 0);
                                        ?>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>
                                <?php if( $pcVisibility['showTitle'] ) : ?>
                                <div class="posten-title-wrapper">
                                    <<?php echo esc_attr($titleTag); ?> class="posten-title">
                                        <a href="<?php echo esc_url( $post->permalink );?>" class="posten-title-link">
                                            <?php 
                                                echo wp_sprintf( '%s', $post->title );
                                            ?>
                                        </a>
                                    </<?php echo esc_attr($titleTag); ?>>
                                </div>
                                <?php endif; ?>
                                <?php if( $pcVisibility['showExcerpt'] ) : ?>
                                <div class="posten-excerpt">
                                    <?php 
                                        echo wp_sprintf( '%s', wp_trim_words( $post->excerpt, $excerptLength, '...' ) );
                                    ?>
                                </div>
                                <?php endif; ?>
                                <?php if( $pcVisibility['showBtn'] ) : ?>
                                <div class="posten-cta">
                                    <div class="posten-permalink">
                                        <a href="<?php echo esc_url( $post->permalink );?>" class="posten-permalink-btn">
                                            <?php 
                                                echo wp_sprintf( '%s', $btnLabel ); 
                                            ?>
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                width="16"
                                                height="16"
                                                fill="currentColor"
                                                class="bi bi-arrow-right"
                                                viewBox="0 0 16 16"
                                            >
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8"
                                                />
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php 
                            endforeach;
                        endif;
                    ?>
                </div>
            </div>
        <?php 
    }

 }

 posten_post_grid_callback( $attributes );