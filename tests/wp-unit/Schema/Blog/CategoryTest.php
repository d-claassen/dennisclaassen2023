<?php

class CategoryTest extends \WP_UnitTestCase {

	// override wordpress function thats incompatible
	// with phpunit 10.
	public function expectDeprecated(){}
	
	public function testIt(): void {
		$post = self::factory()->post->create(
			array( 
				'post_content' => 'Hello world!',
			)
		);

		$post_filled = new \WP_Query( 
			array(
				'p' => $post,
			)
		);
		while ( $post_filled->have_posts() ) {
			$post_filled->the_post();

			do_action( 'wpseo_head' );
		}
		
		$this->assertSame( 'en-US', get_bloginfo( 'language' ) );
	}
}
