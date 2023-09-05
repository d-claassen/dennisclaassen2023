<?php
declare(strict_types=1);

namespace DC23\Schema\Blog;

final class Category {

	public function register():void {

		\add_filter( 'wpseo_schema_webpage', [ $this, 'make_blog_main_entity' ], 11, 2 );

		\add_filter( 'wpseo_schema_graph_pieces', [ $this, 'add_blog_to_schema' ], 11, 2 );
	}

	private function should_add_blog_data(): bool {
		return is_category();
	}

	public function make_blog_main_entity( $webpage_data, $context) {

		if ( ! $this->should_add_blog_data() ) {
			return $webpage_data;
		}

		$category = \get_term( \get_query_var( 'cat' ), 'category' );
		assert( $category instanceof \WP_Term );

		$webpage_data['mainEntity'] = [
			'@id' => $context->site_url . '#/schema/Blog/' . $category->term_id,
		];

		return $webpage_data;
	}

	private function get_category_posts( int $category_id ): array {

		$posts = new \WP_Query(
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

	public function add_blog_to_schema( $pieces, $context) {

		if ( ! $this->should_add_blog_data() ) {
			return $pieces;
		}

		$category = get_term( get_query_var( 'cat' ), 'category' );
		assert( $category instanceof \WP_Term );


		$canonical = \YoastSEO()->meta->for_current_page()->canonical;

		$post_data = $this->get_category_posts( $category->term_id );
		$posts     = [];
		$post_ids  = [];
		foreach ( $post_data as $post ) {
			assert( $post instanceof \WP_Post );
			$id         = get_permalink( $post->ID );
			$post_ids[] = [ '@id' => $id ];
			$posts[]    = new Piece(
				[
					'@id'               => $id,
					'@type'             => 'BlogPosting',
					'mainEntityOfPage'  => $id,
					'headline'          => $post->title,
					'name'              => $post->title,
					'description'       => get_the_excerpt( $post ),
					'datePublished'     => get_the_date( 'Y-m-d', $post ),
					'author'            => [
						'@id'  => YoastSEO()->helpers->schema->id->get_user_schema_id( $context->site_user_id, $context ),
					],
					'publisher'         => [
						'@id'  => YoastSEO()->helpers->schema->id->get_user_schema_id( $context->site_user_id, $context ),
					],
					'inLanguage'        => [
						'@id'  => $canonical . '#/language/' . get_bloginfo( 'language' ),
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
				'description' => wp_trim_excerpt( $category->description ),
				'publisher'   => [
					'@id' => YoastSEO()->helpers->schema->id->get_user_schema_id( $context->site_user_id, $context ),
				],
				'inLanguage'  => [
					'@id' => $canonical . '#/language/' . get_bloginfo( 'language' ),
				],
				'blogPost'    => $post_ids,
			]
		);

		array_push(
			$pieces,
			$blog,
			...$posts
		);

		return $pieces;
	}
}

class Piece extends \Yoast\WP\SEO\Generators\Schema\Abstract_Schema_Piece {

	public function __construct(
		private array $output,
		public $identifier = null
	) {}

	public function is_needed(): bool {
		return true;
	}

	public function generate(): array {
		return $this->output;
	}
}
