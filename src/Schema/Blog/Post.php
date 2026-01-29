<?php

declare( strict_types=1 );

namespace DC23\Schema\Blog;

use DC23\Schema\Piece as Pregenerated_Piece;
use WP_Post;
use Yoast\WP\SEO\Context\Meta_Tags_Context;
use Yoast\WP\SEO\Generators\Schema\Abstract_Schema_Piece;

final class Post {

	public function register(): void {
		\add_filter( 'wpseo_schema_graph_pieces', [ $this, 'add_blog_to_schema' ], 11, 2 );
	}

	private function should_add_post_data(): bool {
		return \is_single() && \get_post_type() === 'post';
	}

	/**
	 * Add Blog piece to Schema.org graph.
	 *
	 * @param array<Abstract_Schema_Piece> $pieces  Pieces in Graph.
	 * @param Meta_Tags_Context            $context Current page context.
	 *
	 * @return array<Abstract_Schema_Piece> Pieces for the Graph.
	 */
	public function add_blog_to_schema( $pieces, $context ) {
		\assert( $context instanceof Meta_Tags_Context );
		if ( ! $this->should_add_post_data() ) {
			return $pieces;
		}

		$schema_article_type = (array) $context->schema_article_type;

		if ( ! \in_array( 'BlogPosting', $schema_article_type, true ) ) {
			return $pieces;
		}

		$post = \get_post( \get_the_ID() );
		\assert( $post instanceof WP_Post );

		$categories = \wp_get_post_categories( $post->ID, [ 'fields' => 'all' ] );
		if ( \count( $categories ) !== 1 ) {
			// Only add Blog piece when there's one category:
			// - Without category, there's no blog to connect with,
			// - With multiple categories, it'll be a PITA to make sense.
			return $pieces;
		}

		$category = \reset( $categories );

		$id      = \get_permalink( $post->ID ) . '#article';
		$post_id = [ '@id' => $id ];

		$canonical = $context->canonical;

		$category_url = \get_term_link( $category->term_id, 'category' );

		$blog = new Pregenerated_Piece(
			[
				'@id'              => $context->site_url . '#/schema/blog/' . $category->term_id,
				'@type'            => 'Blog',
				'mainEntityOfPage' => $category_url,
				'name'             => $category->name,
				'description'      => \wp_trim_excerpt( $category->description ),
				'publisher'        => $context->site_represents_reference,
				'inLanguage'       => \get_bloginfo( 'language' ),
				'blogPost'         => [ $post_id ],
			]
		);

		\array_push(
			$pieces,
			$blog,
		);

		return $pieces;
	}
}
