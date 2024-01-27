<?php
declare(strict_types=1);

namespace DC23\Schema\Blog;

use DC23\Schema\Piece;
use WP_Post;
use WP_Query;
use WP_Term;
use Yoast\WP\SEO\Context\Meta_Tags_Context;
use Yoast\WP\SEO\Generators\Schema\Abstract_Schema_Piece;

final class Category {

	public function register(): void {

		\add_filter( 'wpseo_schema_webpage', [ $this, 'make_blog_main_entity' ], 11, 2 );

		\add_filter( 'wpseo_schema_graph_pieces', [ $this, 'add_blog_to_schema' ], 11, 2 );
	}

	private function should_add_blog_data(): bool {
		return \is_category();
	}

	/**
	 * Enhance the WebPage data with a mainEntity reference to the blog.
	 *
	 * @template T of array{"@type": string}
	 *
	 * @param T $webpage_data The webpage data.
	 * @param Meta_Tags_Context $context The current page context.
	 *
	 * @return T|(T&array{mainEntity: array{"@id": string}}) The enhanced webpage.
	 */
	public function make_blog_main_entity( $webpage_data, $context ) {

		if ( ! $this->should_add_blog_data() ) {
			return $webpage_data;
		}

		$category = \get_term( \get_query_var( 'cat' ), 'category' );
		\assert( $category instanceof WP_Term );

		$webpage_data['mainEntity'] = [
			'@id' => $context->site_url . '#/schema/Blog/' . $category->term_id,
		];

		return $webpage_data;
	}

	/**
	 * Get posts assigned to category.
	 *
	 * @return WP_Post[] The posts.
	 */
	private function get_category_posts( int $category_id ): array {

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

	/**
	 * Add new Blog piece to Schema.org graph.
	 *
	 * @param Abstract_Schema_Piece[] $pieces Existing schema pieces.
	 * @param Meta_Tags_Context $context The page context.
	 *
	 * @return array<Abstract_Schema_Piece> Schema pieces.
	 */
	public function add_blog_to_schema( $pieces, $context ) {

		if ( ! $this->should_add_blog_data() ) {
			return $pieces;
		}

		$category = \get_term( \get_query_var( 'cat' ), 'category' );
		\assert( $category instanceof WP_Term );

		$canonical = \YoastSEO()->meta->for_current_page()->canonical;

		$post_data = $this->get_category_posts( $category->term_id );
		$posts     = [];
		$post_ids  = [];
		foreach ( $post_data as $post ) {
			\assert( $post instanceof WP_Post );
			$id         = \get_permalink( $post->ID );
			$post_ids[] = [ '@id' => $id ];
			$posts[]    = new Piece(
				[
					'@id'               => $id,
					'@type'             => 'BlogPosting',
					'mainEntityOfPage'  => $id,
					'headline'          => $post->post_title,
					'name'              => $post->post_title,
					'description'       => \get_the_excerpt( $post ),
					'datePublished'     => \get_the_date( 'Y-m-d', $post ),
					'author'            => [
						'@id'  => \YoastSEO()->helpers->schema->id->get_user_schema_id( $context->site_user_id, $context ),
					],
					'publisher'         => [
						'@id'  => \YoastSEO()->helpers->schema->id->get_user_schema_id( $context->site_user_id, $context ),
					],
					'inLanguage'        => [
						'@id'  => $canonical . '#/language/' . \get_bloginfo( 'language' ),
					],
					'url'               => $id,
				],
				$id
			);
		}

		$blog = new Piece(
			[
				'@id'         => $context->site_url . '#/schema/Blog/' . $category->term_id,
				'@type'       => 'Blog',
				'name'        => $category->name,
				'description' => \wp_trim_excerpt( $category->description ),
				'publisher'   => [
					'@id' => \YoastSEO()->helpers->schema->id->get_user_schema_id( $context->site_user_id, $context ),
				],
				'inLanguage'  => [
					'@id' => $canonical . '#/language/' . \get_bloginfo( 'language' ),
				],
				'blogPost'    => $post_ids,
			]
		);

		\array_push(
			$pieces,
			$blog,
			...$posts
		);

		return $pieces;
	}
}
