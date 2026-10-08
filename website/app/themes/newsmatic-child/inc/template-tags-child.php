<?php

if( ! function_exists( 'newsmatic_tags_list' ) ) :
    /**
     * print the html for tags list
     */
    function newsmatic_child_tags_list() {
        // Hide category and tag text for pages.
        if ( 'post' === get_post_type() ) {
            /* translators: used between list items, there is a space after the comma */
            $tags_list = get_the_tag_list( '', ' ' );
            if ( $tags_list ) {
                /* translators: 1: list of tags. */
                printf( '<div class="tags-child-links article-before-content-text">' . '%1$s' . '</div>', $tags_list ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            }
        }
    }
endif;