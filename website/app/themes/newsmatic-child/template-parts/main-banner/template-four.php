<?php
/**
 * Main Banner template four
 * 
 * @package Newsmatic
 * @since 1.0.0
 */
use Newsmatic\CustomizerDefault as ND;
$slider_args = $args['slider_args'];
?>
<div class="main-banner-wrap">
    <div class="main-banner-slider" data-auto="true" data-arrows="true">
        <?php
            $slider_query = new WP_Query( $slider_args );
            if( $slider_query -> have_posts() ) :
                while( $slider_query -> have_posts() ) : $slider_query -> the_post();
                ?>
                    <article class="slide-item <?php if(!has_post_thumbnail()){ echo esc_attr('no-feat-img');} ?>">
                        <figure class="post-thumb">
                            <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
                                <?php 
                                    if( has_post_thumbnail()) { 
                                        the_post_thumbnail( esc_html( ND\newsmatic_get_customizer_option( 'banner_slider_image_size' ) ) , array(
                                            'title' => the_title_attribute(array(
                                                'echo'  => false
                                            ))
                                        ));
                                    }
                                ?>
                            </a>
                        </figure>
                        <div class="post-element">
                            <div class="post-meta">
                                <?php newsmatic_get_post_categories( get_the_ID(), 2 ); ?>
                                <?php newsmatic_posted_on(); ?>
                            </div>
                            <h2 class="post-title"><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
                            <?php add_filter( 'excerpt_length', 'newsmatic_banner_excerpt_length', 999 ); ?>
                                    <div class="post-excerpt"><?php the_excerpt(); ?></div>
                            <?php remove_filter( 'excerpt_length', 'newsmatic_banner_excerpt_length', 999 ); ?>
                        </div>
                    </article>
                <?php
                endwhile;
                wp_reset_postdata();
            endif;
        ?>
    </div>
</div>

<div class="main-banner-block-posts banner-trailing-posts">
    <?php
        $main_banner_block_posts_categories = json_decode( ND\newsmatic_get_customizer_option( 'main_banner_block_posts_categories' ) );
        $main_banner_block_posts_order_by = ND\newsmatic_get_customizer_option( 'main_banner_block_posts_order_by' );
        $blockPostsOrderArray = explode( '-', $main_banner_block_posts_order_by );
        $block_posts_args = array(
            'numberposts' => 4,
            'order' => esc_html( $blockPostsOrderArray[1] ),
            'orderby' => esc_html( $blockPostsOrderArray[0] ),
            'category_name' => newsmatic_get_categories_for_args($main_banner_block_posts_categories)
        );
        $block_posts = get_posts( $block_posts_args );
        if( $block_posts ) :
            foreach( $block_posts as $popular_post ) :
                $popular_post_id  = $popular_post->ID;
                $image_id = get_post_thumbnail_id();
                $image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
                ?>
                    <article class="post-item<?php if(!has_post_thumbnail($popular_post_id)){ echo esc_attr(' no-feat-img');} ?>">
                        <figure class="post-thumb">
                            <?php if( has_post_thumbnail($popular_post_id) ): ?>
                                <a href="<?php echo esc_url(get_the_permalink($popular_post_id)); ?>">
                                    <img src="<?php echo esc_url( get_the_post_thumbnail_url($popular_post_id, ND\newsmatic_get_customizer_option( 'banner_slider_block_posts_image_size' ) ) ); ?>" alt="<?php echo $image_alt ?>"/>
                                </a>
                            <?php endif; ?>
                        </figure>
                        <div class="post-element">
                            <div class="post-meta">
                                <?php newsmatic_get_post_categories( $popular_post_id, 2 ); ?>
                            </div>
                            <h2 class="post-title"><a href="<?php the_permalink($popular_post_id); ?>"><?php echo wp_kses_post( get_the_title($popular_post_id) ); ?></a></h2>
                        </div>
                    </article>
            <?php
            endforeach;
        endif;
    ?>
</div>