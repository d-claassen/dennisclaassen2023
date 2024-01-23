<?php

declare(strict_types=1);

namespace DC23\Blocks\GitHubTree;

use WP_Post;
use WP_Query;
use WP_Term;

class Tree {

	/**
	 * Renders the `dc23/github-tree` block on the server.
	 *
	 * @param array<string, string> $attributes Block attributes.
	 * @param string   $content    Block default content.
	 * @param WP_Block $block      Block instance.
	 *
	 * @return string Returns the filtered post title for the current post wrapped inside "h1" tags.
	 */
	public static function render( $attributes, $content, $block ): string {
		return ( new self() )->render_block_dc23_github_tree( $attributes, $content, $block );
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
	private function render_block_dc23_github_tree( $attributes, $content, $block ): string {
		\assert( $block instanceof WP_Block );
		$is_home = \is_front_page();
		if ( $is_home ) {
			$root_categories  = \get_categories( [ 'parent' => 0 ] );
			$root_ids         = \array_column( $root_categories, 'term_id' );
			$child_categories = \implode( '', \array_map( $this->get_category_as_row( ... ), $root_ids ) );

			$wrapper_attributes = \get_block_wrapper_attributes( [ 'class' => 'Box' ] );

			return <<<HTML
			<div {$wrapper_attributes}>
				{$child_categories}
			</div>
			HTML;
		}

		$is_archive = \is_category();
		if ( ! $is_archive ) {
			return '';
		}

		$category = \get_category( \get_query_var( 'cat' ) );
		\assert( $category instanceof WP_Term );

		$parent_id  = $category->parent;
		$parent_row = null;
		$parent_row = $this->get_category_as_parent_row( $parent_id );

		$children_ids     = \get_term_children( $category->term_id, $category->taxonomy );
		$child_categories = \implode( '', \array_map( $this->get_category_as_row( ... ), $children_ids ) );

		$child_posts = $this->get_posts_as_row( $category->term_id );

		if (  $parent_row || $child_categories || $child_posts ) {
			$wrapper_attributes = \get_block_wrapper_attributes( [ 'class' => 'Box' ] );

			return <<<HTML
			<div {$wrapper_attributes}>
				{$parent_row}
				{$child_categories}
				{$child_posts}
			</div>
			HTML;
		}

		return '';
	}

	private function get_category_as_parent_row( int $parent_id ): string {

		$category_link = \get_site_url();
		if ( \is_int( $parent_id ) && $parent_id > 0 ) {
			$category_link = \get_category_link( $parent_id );
		}

		return <<<HTML
			<div class="Box-row">

				<div class="Box-icon" style="color: #54aeff">
					<svg aria-label="Directory" aria-hidden="true" height="16" viewBox="0 0 16 16" version="1.1" width="16" data-view-component="true" class="octicon octicon-file-directory-fill hx_color-icon-directory">
						<path d="M1.75 1A1.75 1.75 0 0 0 0 2.75v10.5C0 14.216.784 15 1.75 15h12.5A1.75 1.75 0 0 0 16 13.25v-8.5A1.75 1.75 0 0 0 14.25 3H7.5a.25.25 0 0 1-.2-.1l-.9-1.2C6.07 1.26 5.55 1 5 1H1.75Z"></path>
					</svg>
				</div>

				<div class="Box-primary">
					<a href="{$category_link}" class="Box-link">..</a>
				</div>
			</div>
			HTML;
	}

	private function get_latest_post_for_category( int $category_id ): ?WP_Post {
		$args = [
			'posts_per_page' => 1, // We need only the latest post, so get that post only.
			'cat'            => $category_id, // Use the category id, can also replace with category_name which uses category slug.
		];

		$str   = '';
		$posts = \get_posts( $args );

		foreach ($posts as $post) :
			return $post;
		endforeach;

		return null;
	}

	private function get_category_as_row( int $category_id ): string {
		$category         = \get_category( $category_id );
		$category_link    = \get_category_link( $category );
		$latest_post      = $this->get_latest_post_for_category( $category_id );
		$latest_post_link = \get_permalink( $latest_post->ID );
		$latest_post_date = \get_the_date( '', $latest_post );

		return <<<HTML
		<div class="Box-row">

			<div class="Box-icon" style="color: #54aeff">
				<svg aria-label="Directory" aria-hidden="true" height="16" viewBox="0 0 16 16" version="1.1" width="16" data-view-component="true" class="octicon octicon-file-directory-fill hx_color-icon-directory">
					<path d="M1.75 1A1.75 1.75 0 0 0 0 2.75v10.5C0 14.216.784 15 1.75 15h12.5A1.75 1.75 0 0 0 16 13.25v-8.5A1.75 1.75 0 0 0 14.25 3H7.5a.25.25 0 0 1-.2-.1l-.9-1.2C6.07 1.26 5.55 1 5 1H1.75Z"></path>
				</svg>
			</div>

			<div class="Box-primary" style="margin-right: auto">
				<a href="{$category_link}" class="Box-link">
					{$category->name}
				</a>
			</div>

			<div class="Box-secondary Box-truncate" style="width: 40%;">
				<a href="{$latest_post_link}" class="Box-link">
					{$latest_post->post_title}
				</a>
			</div>

			<div class="Box-secondary" style="width: 150px; text-align: right">
				{$latest_post_date}
			</div>
		</div>
		HTML;
	}

	/**
	 * Get all posts in category.
	 *
	 * @return array<WP_Post> List of posts.
	 */
	private function get_child_posts_for_category( int $category_id ): array {
		$posts = new WP_Query(
			[
				'post_type'      => 'post',
				'posts_per_page' => -1, // <-- Show all posts
				'hide_empty'     => true,
				'order'          => 'ASC',
				'orderby'        => 'title',
				'tax_query'      => [
					[
						'taxonomy'         => 'category',
						'terms'            => $category_id,
						'field'            => 'id',
						'include_children' => false,
					],
				],
			]
		);

		return $posts->get_posts();
	}

	private function get_posts_as_row( int $category_id ): string {
		$posts = $this->get_child_posts_for_category( $category_id );

		return \implode( '', array_map( $this->get_post_as_row( ... ), $posts ) );
	}

	private function get_post_as_row( WP_Post $post): string {
		$latest_post_title = $post->post_title;
		$latest_post_link  = \get_permalink( $post->ID );
		$latest_post_date  = \get_the_date( '', $post );
		return <<<HTML
		<div class="Box-row">
			<div class="Box-icon" style="color: #656d76">
				<svg aria-label="File" aria-hidden="true" height="16" viewBox="0 0 16 16" version="1.1" width="16" data-view-component="true" class="octicon octicon-file color-fg-muted">
				<path d="M2 1.75C2 .784 2.784 0 3.75 0h6.586c.464 0 .909.184 1.237.513l2.914 2.914c.329.328.513.773.513 1.237v9.586A1.75 1.75 0 0 1 13.25 16h-9.5A1.75 1.75 0 0 1 2 14.25Zm1.75-.25a.25.25 0 0 0-.25.25v12.5c0 .138.112.25.25.25h9.5a.25.25 0 0 0 .25-.25V6h-2.75A1.75 1.75 0 0 1 9 4.25V1.5Zm6.75.062V4.25c0 .138.112.25.25.25h2.688l-.011-.013-2.914-2.914-.013-.011Z"></path>
				</svg>
			</div>

			<div class="Box-primary" style="margin-right: auto">
				<a href="{$latest_post_link}" class="Box-link">{$latest_post_title}</a>
			</div>
			
			<div class="Box-secondary" style="width: 140px; text-align: right;">
				{$latest_post_date}
			</div>
		</div>
		HTML;
	}
}
