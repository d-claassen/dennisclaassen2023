<?php declare(strict_types=1);

namespace DC23\Schema\Language;

use Yoast\WP\SEO\Context\Meta_Tags_Context;

final class SiteLanguage {
    private LanguageFactory $languageFactory;

    public function __construct() {
        $this->languageFactory = new LanguageFactory();
    }

    public function register(): void {
        // functions.php
        \add_filter( 'wpseo_schema_graph_pieces', $this->add_site_language_to_schema(...), 11, 2 );
        \add_filter( 'wpseo_schema_webpage', $this->enhance_inlanguage_property(...), 11, 1 );
        \add_filter( 'wpseo_schema_website', $this->enhance_inlanguage_property(...), 11, 1 );
        \add_filter( 'wpseo_schema_imageobject', $this->enhance_inlanguage_property(...), 11, 1 );
        \add_filter( 'wpseo_schema_person', $this->enhance_person_image_inlanguage_property(...), 11, 2 );
    }

    private function add_site_language_to_schema( $pieces, $context ): array {
        if ( ! is_array( $pieces ) ) {
            return [];
        }

        $pieces[] = $this->languageFactory->createLanguage( get_bloginfo( 'language' ) );
        return $pieces;
    }

    private function enhance_inlanguage_property( $schemaPieceData ) {
        // @todo is it time to investigate https://packagist.org/packages/azjezz/psl ?!
        if ( ! is_array( $schemaPieceData ) ) {
            return [];
        }

        if ( ! array_key_exists( 'inLanguage', $schemaPieceData ) || ! is_string( $schemaPieceData[ 'inLanguage' ] ) ) {
            return $schemaPieceData;
        }

        $canonical = YoastSEO()->meta->for_current_page()->canonical;

        $schemaPieceData['inLanguage'] = [
            '@id' => $canonical . '#/language/' . $schemaPieceData['inLanguage']
        ];

        return $schemaPieceData;
    }

    private function enhance_person_image_inlanguage_property( $personData ): array {
        // @todo is it time to investigate https://packagist.org/packages/azjezz/psl ?!
        if ( ! is_array( $personData ) ) {
            return [];
        }

        if ( ! array_key_exists( 'image', $personData ) || ! is_array( $personData[ 'image' ] ) ) {
            return $personData;
        }

        $personData[ 'image' ] = $this->enhance_inlanguage_property( $personData[ 'image'] );
        return $personData;
    }
}

final class LanguageFactory {
    public function createLanguage( string $locale, ?string $namePretty = null, ?string $sameAs = null ) {
        switch( $locale ) {
            case 'en-US':
                return new Language( 'en-US', 'English (American)', 'https://en.wikipedia.org/wiki/American_English' );
            default:
                return new Language( $locale );
        }
    }
}

class Language {
    public function __construct(
        private string $locale, 
        private ?string $namePretty = null, 
        private ?string $sameAs = null
        ) {}

    public function is_needed(): bool {
        return true;
    }

    public function generate(): array {
        $canonical = YoastSEO()->meta->for_current_page()->canonical; 

        $data = [
            '@type' => 'Language',
            '@id' => $canonical . '#/language/' . $this->locale,
            'name' => null,
            'alternateName' => null,
            'sameAs' => $this->sameAs,
        ];

        if ( isset( $this->namePretty ) ) {
            $data['name'] = $this->namePretty;
            $data['alternateName'] = $this->locale;
        } else {
            $data['name'] = $this->locale;
        }

        return array_filter( $data );
    }
}