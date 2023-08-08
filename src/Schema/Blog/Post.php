<?php

declare( strict_types=1 );

namespace DC23\Schema\Blog;

final class Post {

	public function register():void {
		\add_filter( 'wpseo_schema_graph_pieces', [ $this, 'add_blog_to_schema' ], 11, 2 );
	}

	private function should_add_post_data(): bool {
		return is_single() && get_post_type() === 'post';
	}

	public function add_blog_to_schema( $pieces, $context ) {
		assert( $context instanceof \Yoast\WP\SEO\Context\Meta_Tags_Context );
		if ( ! $this->should_add_post_data() ) {
			return $pieces;
		}

		if ( $context->indexable->schema_article_type !== 'BlogPosting' ) {
			return $pieces;
		}

		$canonical = \YoastSEO()->meta->for_current_page()->canonical;

		$post = \get_post();
		assert( $post instanceof \WP_Post );

		$id      = get_permalink( $post->ID ) . '#article';
		$post_id = [ '@id' => $id ];

		$categories = wp_get_post_categories( $post->ID, [ 'fields' => 'all' ] );
		$category   = reset( $categories );

		$blog = new Pregenerated_Piece(
			[
				'@id'         => $context->site_url . '#/schema/Blog/' . $category->term_id,
				'@type'       => 'Blog',
				'name'        => $category->name,
				'description' => \wp_trim_excerpt( $category->description ),
				'publisher'   => [
					'@id' => \YoastSEO()->helpers->schema->id->get_user_schema_id( $context->site_user_id, $context ),
				],
				'inLanguage'  => [
					'@id' => $canonical . '#/language/' . get_bloginfo( 'language' ),
				],
				'blogPost'    => [ $post_id ],
			]
		);

		\array_push(
			$pieces,
			$blog,
		);

		return $pieces;
	}
}

class Pregenerated_Piece extends \Yoast\WP\SEO\Generators\Schema\Abstract_Schema_Piece {

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
