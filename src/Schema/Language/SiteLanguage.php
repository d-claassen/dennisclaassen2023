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
		\add_filter( 'wpseo_schema_person', $this->enhance_person_image_inlanguage_property( ... ), 11, 1 );
	}

	/**
	 * Add Language piece to Schema.org graph.
	 *
	 * @param Abstract_Schema_Piece[] $pieces The pieces already in the graph.
	 * @param Meta_Tags_Context $context The page context.
	 *
	 * @return Abstract_Schema_Piece[] The pieces for the graph.
	 */
	private function add_site_language_to_schema( $pieces, $context ): array {
		\assert( $context instanceof Meta_Tags_Context );
		if ( ! \is_array( $pieces ) ) {
			return [];
		}

		$pieces[] = $this->language_factory->create_language( \get_bloginfo( 'language' ) );
		return $pieces;
	}

	/**
	 * Enhance a schema piece with the inLanguage property.
	 *
	 * @template T of array{"@type": string, inLanguage?: string}
	 *
	 * @param T $schema_piece_data The original piece data.
	 *
	 * @return T|(T&array{inLanguage: array{"@id": string}}) The enhanced schema.org piece.
	 */
	private function enhance_inlanguage_property( $schema_piece_data ) {
		// @todo is it time to investigate https://packagist.org/packages/azjezz/psl ?!
		if ( ! \is_array( $schema_piece_data ) ) {
			return $schema_piece_data;
		}

		if ( ! \array_key_exists( 'inLanguage', $schema_piece_data ) || ! \is_string( $schema_piece_data['inLanguage'] ) ) {
			return $schema_piece_data;
		}

		$canonical = \YoastSEO()->meta->for_current_page()->canonical;

		$schema_piece_data['inLanguage'] = [
			'@id' => $canonical . '#/language/' . $schema_piece_data['inLanguage'],
		];

		return $schema_piece_data;
	}

	/**
	 * Enhance a schema piece with an image with the inLanguage property.
	 *
	 * @template T of array{"@type": string, image?: array{ inLanguage?: string }}
	 *
	 * @param T $person_data The original piece data.
	 *
	 * @return T|(T&array{image:array{inLanguage: array{"@id": string}}}) The enhanced schema.org piece.
	 */
	private function enhance_person_image_inlanguage_property( $person_data ): array {
		// @todo is it time to investigate https://packagist.org/packages/azjezz/psl ?!
		if ( ! \is_array( $person_data ) ) {
			return $person_data;
		}

		if ( ! \array_key_exists( 'image', $person_data ) || ! \is_array( $person_data['image'] ) ) {
			return $person_data;
		}

		$person_data['image'] = $this->enhance_inlanguage_property( $person_data['image'] );
		return $person_data;
	}
}
