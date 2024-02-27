<?php
declare( strict_types=1 );

namespace DC23\Schema\Language;

final class Language {

	public function __construct(
		private string $locale,
		private ?string $name_pretty = null,
		private ?string $same_as = null
	) {}

	public function is_needed(): bool {
		return true;
	}

	/**
	 * Generate Schema.org representation of language.
	 *
	 * @return array{
	 *       "@type": "Language",
	 *       "@id": string,
	 *       "name"?: non-empty-string,
	 *       "alternateName"?: non-empty-string,
	 *       "sameAs"?: non-empty-string
	 *     } The schema.org representation.
	 */
	public function generate(): array {
		$canonical = \YoastSEO()->meta->for_current_page()->canonical;

		\var_dump( $canonical );

		$data = [
			'@type'         => 'Language',
			'@id'           => $canonical . '#/language/' . $this->locale,
			'name'          => null,
			'alternateName' => null,
			'sameAs'        => $this->same_as,
		];

		if ( isset( $this->name_pretty ) ) {
			$data['name']          = $this->name_pretty;
			$data['alternateName'] = $this->locale;
		}
		else {
			$data['name'] = $this->locale;
		}

		return \array_filter( $data );
	}
}
