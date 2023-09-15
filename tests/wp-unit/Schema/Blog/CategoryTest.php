<?php

class CategoryTest extends \WP_UnitTestCase {

	// override wordpress function thats incompatible
	// with phpunit 10.
	public function expectDeprecated(){}
	
	public function testIt(): void {
		global $wp_query;
		
		$post = self::factory()->post->create(
			array( 
				'post_content' => 'Hello world!',
			)
		);

		$wp_query = new \WP_Query( 
			array(
				'p' => $post,
			)
		);

		var_dump( $wp_query->is_singular() );

		$this->expectOutputRegex( '/"@type": "Blog"/' );

		
		while ( $wp_query->have_posts() ) {
			$wp_query->the_post();

			do_action( 'wpseo_head' );
		}
		
		$this->assertSame( 'en-US', get_bloginfo( 'language' ) );
	}
}
