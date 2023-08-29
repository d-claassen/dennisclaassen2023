<?php

declare( strict_types=1 );

namespace DC23\Schema\Blog;

final class Post {

	public function register():void {
		\add_filter( 'wpseo_schema_graph_pieces', [ $this, 'add_blog_to_schema' ], 11, 2 );
	}

	private function should_add_post_data(): bool {
		return \is_single() && \get_post_type() === 'post';
	}

	public function add_blog_to_schema( $pieces, $context ) {
		assert( $context instanceof \Yoast\WP\SEO\Context\Meta_Tags_Context );
		if ( ! $this->should_add_post_data() ) {
			return $pieces;
		}

		if ( $context->indexable->schema_article_type !== 'BlogPosting' ) {
			return $pieces;
		}

		$post = \get_post( \get_the_ID() );
		assert( $post instanceof \WP_Post );

		$categories = \wp_get_post_categories( $post->ID, [ 'fields' => 'all' ] );
		if ( count( $categories ) !== 1 ) {
			// Only add Blog piece when there's one category:
			// - Without category, there's no blog to connect with,
			// - With multiple categories, it'll be a PITA to make sense.
			return $pieces;
		}

		$category = reset( $categories );

		$id      = \get_permalink( $post->ID ) . '#article';
		$post_id = [ '@id' => $id ];

		$canonical = $context->canonical;

		$blog = new Pregenerated_Piece(
			[
				'@id'         => $context->site_url . '#/schema/Blog/' . $category->term_id,
				'@type'       => 'Blog',
				'name'        => $category->name,
				'description' => \wp_trim_excerpt( $category->description ),
				'publisher'   => $context->site_represents_reference,
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
