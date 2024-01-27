<?php
declare(strict_types=1);
namespace DC23\Blocks\GitHubBreadcrumbs;

use WP_Block;
use WP_Post;
use WP_Term;

final class Breadcrumbs {

	/**
	 * Renders the `dc23/github-breadcrumbs` block on the server.
	 *
	 * @param array<string, string> $attributes Block attributes.
	 * @param string   $content    Block default content.
	 * @param WP_Block $block      Block instance.
	 *
	 * @return string Returns the filtered post title for the current post wrapped inside "h1" tags.
	 */
	public static function render( $attributes, $content, $block ): string {
		return ( new self() )->render_block_dc23_github_breadcrumbs( $attributes, $content, $block );
	}

	/**
	 * Renders the `dc23/github-tree` block on the server.
	 *
	 * @param array<string, string> $attributes Block attributes.
	 * @param string   $content    Block default content.
	 * @param WP_Block $block      Block instance.
	 *
	 * @return string Returns the filtered post title for the current post wrapped inside "h1" tags.
	 */
	public function render_block_dc23_github_breadcrumbs( $attributes, $content, $block ): string {
		\assert( \is_array( $attributes ) );
		\assert( $block instanceof WP_Block );

		$is_single  = \is_single();
		$is_archive = \is_category();
		if ( $is_archive ) {
			$category = \get_category( \get_query_var( 'cat' ) );
			\assert( $category instanceof WP_Term );

			$breadcrumb_list = $this->get_category_breadcrumb_list( $category );
		}
		elseif ( $is_single ) {
			$post = \get_post();
			\assert( $post instanceof WP_Post );

			$breadcrumb_list = $this->get_post_breadcrumb_list( $post );
		}
		else {
			// Breadcrumbs only render on a category page.
			return '';
		}

		$wrapper_attributes = \get_block_wrapper_attributes( [ 'class' => 'breadcrumb-container' ] );

		return <<<HTML
		<div {$wrapper_attributes}>
			<nav class="breadcrumb">
			<ol>
				{$breadcrumb_list}
			</ol>
			</nav>
		</div>
		HTML;
	}

	private function get_post_breadcrumb_list( WP_Post $post ): string {

		$separator = $this->get_separator();

		$breadcrumb_list = \sprintf(
			'<%1$s>%2$s %3$s</%1$s>',
			'li',
			$post->post_title,
			''
		);

		$categories       = \wp_get_post_categories( $post->ID, [ 'fields' => 'all' ] );
		$initial_category = \reset( $categories );
		$category         = null;

		do {
			$category_id     = ( $category->parent ?? $initial_category->term_id );
			$breadcrumb_list = \sprintf(
				'<%1$s>%2$s%3$s</%1$s>',
				'li',
				$this->get_category_as_link( $category_id ),
				$separator
			) . $breadcrumb_list;
			$category        = \get_category( $category_id );
		}
		while ( $category_id > 0 );

		return $breadcrumb_list;
	}

	private function get_category_breadcrumb_list( WP_Term $category ): string {

		$separator = $this->get_separator();

		$breadcrumb_list = \sprintf(
			'<%1$s>%2$s %3$s</%1$s>',
			'li',
			$category->name,
			''
		);

		do {
			$parent_id       = $category->parent;
			$breadcrumb_list = \sprintf(
				'<%1$s>%2$s%3$s</%1$s>',
				'li',
				$this->get_category_as_link( $parent_id ),
				$separator
			) . $breadcrumb_list;
			$category        = \get_category( $parent_id );
		}
		while ( $parent_id > 0 );

		return $breadcrumb_list;
	}

	private function get_separator(): string {
		return '<span class="breadcrumb-separator" aria-hidden="true">/</span>';
	}

	private function get_category_as_link( int $category_id ): string {
		// Prepare defaults based on the current blog.
		$category_name = \get_bloginfo( 'name' );
		$category_link = \get_site_url();

		// If a valid category, load that category info.
		if ( \is_int( $category_id ) && $category_id > 0 ) {
			$category      = \get_category( $category_id );
			$category_name = $category->name;
			$category_link = \get_category_link( $category );
		}

		return <<<HTML
			<a href="{$category_link}">{$category_name}</a>
		HTML;
	}
}
