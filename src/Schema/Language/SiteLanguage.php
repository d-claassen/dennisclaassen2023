<?php
declare(strict_types=1);

namespace DC23\Schema\Language;

use Yoast\WP\SEO\Context\Meta_Tags_Context;

final class SiteLanguage {

	private LanguageFactory $language_factory;

	public function __construct() {
		$this->language_factory = new LanguageFactory();
	}

	public function register(): void {
		\add_filter( 'wpseo_schema_graph_pieces', $this->add_site_language_to_schema( ... ), 11, 2 );
		\add_filter( 'wpseo_schema_webpage', $this->enhance_inlanguage_property( ... ), 11, 1 );
		\add_filter( 'wpseo_schema_website', $this->enhance_inlanguage_property( ... ), 11, 1 );
		\add_filter( 'wpseo_schema_imageobject', $this->enhance_inlanguage_property( ... ), 11, 1 );
		\add_filter( 'wpseo_schema_person', $this->enhance_person_image_inlanguage_property( ... ), 11, 2 );
	}

	private function add_site_language_to_schema( $pieces, $context ): array {
		assert( $context instanceof \Yoast\WP\SEO\Context\Meta_Tags_Context );
		if ( ! is_array( $pieces ) ) {
			return [];
		}

		$pieces[] = $this->language_factory->create_language( get_bloginfo( 'language' ) );
		return $pieces;
	}

	private function enhance_inlanguage_property( $schema_piece_data ) {
		// @todo is it time to investigate https://packagist.org/packages/azjezz/psl ?!
		if ( ! is_array( $schema_piece_data ) ) {
			return [];
		}

		if ( ! array_key_exists( 'inLanguage', $schema_piece_data ) || ! is_string( $schema_piece_data['inLanguage'] ) ) {
			return $schema_piece_data;
		}

		$canonical = YoastSEO()->meta->for_current_page()->canonical;

		$schema_piece_data['inLanguage'] = [
			'@id' => $canonical . '#/language/' . $schema_piece_data['inLanguage'],
		];

		return $schema_piece_data;
	}

	private function enhance_person_image_inlanguage_property( $person_data ): array {
		// @todo is it time to investigate https://packagist.org/packages/azjezz/psl ?!
		if ( ! is_array( $person_data ) ) {
			return [];
		}

		if ( ! array_key_exists( 'image', $person_data ) || ! is_array( $person_data['image'] ) ) {
			return $person_data;
		}

		$person_data['image'] = $this->enhance_inlanguage_property( $person_data['image'] );
		return $person_data;
	}
}
