<?php

declare( strict_types=1 );

namespace DC23\Schema;

use \Yoast\WP\SEO\Generators\Schema\Abstract_Schema_Piece;

/**
 * Generic Schema.org piece to wrap any kind of data.
 */
class Piece extends Abstract_Schema_Piece {

	public function __construct(
		private array $output
	) {}

	public function is_needed(): bool {
		return true;
	}

	public function generate(): array {
		return $this->output;
	}
}
