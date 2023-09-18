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
		$user_id = self::factory()->user->create( array() );

		\YoastSEO()->helpers->options->set( 'company_or_person', 'person' );
		\YoastSEO()->helpers->options->set( '‎company_or_person_user_id', $user_id );

		$wp_query = new \WP_Query( 
			array(
				'p' => $post,
			)
		);

		$this->assertTrue( $wp_query->is_singular(), 'Query should be singular context' );

		$this->expectOutputRegex( '/"@type": "Blog"/' );

		
		while ( $wp_query->have_posts() ) {
			$wp_query->the_post();

			do_action( 'wpseo_head' );
		}
		
		$this->assertSame( 'en-US', get_bloginfo( 'language' ) );
	}
}
