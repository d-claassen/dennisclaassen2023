<?php

namespace DC23\Tests;

class FirstTest extends \PHPUnit\Framework\TestCase {

	/**
	 * Covers nothing.
	 *
	 * @coversNothing
	 */
	public function testTheTest(): void {
		$this->assertEquals( 1, '1' );

		$this->assertSame(
			$this->testTheTest( ... ),
			$this->testTheTest( ... ),
			'same method'
		);
	}
}
