<?php

class CategoryTest extends \WP_UnitTestCase {

	// override wordpress function thats incompatible
	// with phpunit 10.
	public function expectDeprecated(){}
	
	public function testIt(): void {
		$this->assertSame( 'en-US', get_bloginfo( 'language' ) );
	}
}
