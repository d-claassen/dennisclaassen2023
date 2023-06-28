<?php declare(strict_types=1);

namespace DC23\Blocks\GitHubBreadcrumbs;

use WP_Post;
use WP_Query;
use WP_Term;

class Breadcrumbs {
    /**
     * Renders the `dc23/github-breadcrumbs` block on the server.
     *
     * @param array    $attributes Block attributes.
     * @param string   $content    Block default content.
     * @param WP_Block $block      Block instance.
     *
     * @return string Returns the filtered post title for the current post wrapped inside "h1" tags.
     */
    public static function render($attributes, $content, $block): string {
        return (new self())->render_block_dc23_github_breadcrumbs( $attributes, $content, $block );
    }
    
    /**
     * Renders the `dc23/github-tree` block on the server.
     *
     * @param array    $attributes Block attributes.
     * @param string   $content    Block default content.
     * @param WP_Block $block      Block instance.
     *
     * @return string Returns the filtered post title for the current post wrapped inside "h1" tags.
     */
    function render_block_dc23_github_breadcrumbs( $attributes, $content, $block ): string {
        $is_archive = is_category();
        if ( ! $is_archive ) {
            // Breadcrumbs only render on a category page.
            return '';
        }

        $category = get_category( get_query_var( 'cat' ) );
        assert( $category instanceof WP_Term );

        $separator = '<span class="breadcrumb-separator" aria-hidden="true">/</span>';
        $breadcrumb_list = sprintf(
            '<%1$s>%2$s %3$s</%1$s>',
            'li',
            $category->name,
            $separator
        );

        do {
            $parent_id = $category->parent;
            $breadcrumb_list = sprintf(
                '<%1$s>%2$s %3$s</%1$s>',
                'li',
                $this->get_category_as_link( $parent_id ),
                $separator
            ) . $breadcrumb_list;
            $category = get_category( $parent_id );
        }
        while( $parent_id > 0 );

        return <<<HTML
            <div class="breadcrumb-container">
                <nav class="breadcrumb">
                    <ol>
                        {$breadcrumb_list}
                    </ol>
                </nav>
            </div>
        HTML;
    }

    private function get_category_as_link( int $category_id ) {
        // Prepare defaults based on the current blog.
        $category_name = get_bloginfo( 'name' );
        $category_link = get_site_url();

        // If a valid category, load that category info.
        if ( is_int( $category_id ) && $category_id > 0 ) {
            $category = get_category( $category_id );
            $category_name = $category->name;
            $category_link = get_category_link( $category );
        }
        
        return <<<HTML
            <a href="{$category_link}">{$category_name}</a>
        HTML;
    }
}