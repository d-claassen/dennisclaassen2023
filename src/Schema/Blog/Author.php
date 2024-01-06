<?php
declare(strict_types=1);

namespace DC23\Schema\Blog;

final class Author {

	public function register(): void {
		\add_filter(
			'wpseo_schema_person', 
			[ $this, 'enhance_author_page' ],
			11, 
			2
		);
	}

	public function enhance_author_page(
		$person_data,
		$context
	): array {
		assert( is_array( $person_data ) );
		assert( $context instanceof \Yoast\WP\SEO\Context\Meta_Tags_Context );

		// Only extend author pages.
		if ( ! is_author() ) {
			return $person_data;
		}

		$author_id = get_query_var( 'author' );

		if (  $context->site_user_id == $author_id ) {
			// author is site owner. maybe needs more person nodes??
		}
	}
}
