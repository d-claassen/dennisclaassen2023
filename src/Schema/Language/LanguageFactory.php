<?php
declare(strict_types=1);

namespace DC23\Schema\Language;

final class LanguageFactory {

	public function create_language( string $locale ) {
		switch ( $locale ) {
			case 'en-US':
				return new Language( 'en-US', 'English (American)', 'https://en.wikipedia.org/wiki/American_English' );
			default:
				return new Language( $locale );
		}
	}
}
