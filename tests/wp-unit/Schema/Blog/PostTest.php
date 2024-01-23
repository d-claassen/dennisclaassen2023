<?php

/**
 * Class PostTest.
 *
 * @testdox Schema for a "post" post
 */
class PostTest extends \WP_UnitTestCase {

	private $user_id;

	public function setUp(): void {
		parent::setUp();

		// Yoast user settings
		$this->user_id = self::factory()->user->create();

		\YoastSEO()->helpers->options->set( 'company_or_person', 'person' );
		\YoastSEO()->helpers->options->set( 'company_or_person_user_id', $this->user_id );
	}

	// override wordpress function thats incompatible
	// with phpunit 10.
	public function expectDeprecated(){}

	/**
	 * @testdox Should not contain Blog piece when the article type is not BlogPosting
	 */
	public function test_should_not_add_blog_piece_when_article_type_is_not_blog_posting(): void {
		$post_id = self::factory()->post->create(
			array(
				'title'        => 'unchanged',
				'post_content' => 'Hello world!',
			)
		);

		\YoastSEO()->helpers->options->set( 'schema-article-type-post', '' );
		\YoastSEO()->helpers->meta->delete( 'schema_article_type', $post_id );

		$this->go_to( \get_permalink( $post_id ) );

		$schema_output = $this->get_schema_output();

		$this->assertJson( $schema_output );

		$schema_data = json_decode( $schema_output, JSON_OBJECT_AS_ARRAY );

		$this->assertSame('Article', $schema_data['@graph'][0]['@type'],'First graph piece should be Article');
	}

	/**
	 * @testdox Should contain Blog piece when the article type for the post type is BlogPosting
	 */
	public function test_should_add_blog_piece_when_default_article_type_is_blog_posting(): void {
		$post_id = self::factory()->post->create(
			array(
				'title'        => 'default setting',
				'post_content' => 'Hello world!',
				'post_type'    => 'post',
			)
		);

		// Set the default Schema.org article type for the post type "post" to BlogPosting.
		\YoastSEO()->helpers->options->set( 'schema-article-type-post', 'BlogPosting' );

		$this->go_to( \get_permalink( $post_id ) );

		$schema_output = $this->get_schema_output();

		$this->assertJson( $schema_output );

		$schema_data = json_decode( $schema_output, JSON_OBJECT_AS_ARRAY );

		$this->assertSame(['Article','BlogPosting'], $schema_data['@graph'][0]['@type'],'First graph piece should be BlogPosting');
		$this->assertSame('Blog', $schema_data['@graph'][6]['@type'],'Sixth graph piece should be Blog');
		$this->assertSame($schema_data['@graph'][0]['@id'], $schema_data['@graph'][6]['blogPost'][0]['@id'],'Blog should refer to BlogPosting');
	}

	/**
	 * @testdox Should contain Blog piece when the article type for the single post is BlogPosting
	 */
	public function test_should_contain_blog_piece_when_article_type_for_post_is_blog_posting(): void {
		$post_id = self::factory()->post->create(
			array(
				'title'        => 'indexable setting',
				'post_content' => 'Hello world!',
			)
		);

		\YoastSEO()->helpers->meta->set_value( 'schema_article_type', 'BlogPosting', $post_id );

		// Update object to persist meta value to indexable.
		self::factory()->post->update_object( $post_id, [] );

		$this->go_to( \get_permalink( $post_id ) );

		$schema_output = $this->get_schema_output();

		$this->assertJson( $schema_output );

		$schema_data = \json_decode( $schema_output, JSON_OBJECT_AS_ARRAY );

		$this->assertSame(['Article','BlogPosting'], $schema_data['@graph'][0]['@type'],'First graph piece should be BlogPosting');
		$this->assertSame('Blog', $schema_data['@graph'][6]['@type'],'Sixth graph piece should be Blog');
		$this->assertSame($schema_data['@graph'][0]['@id'], $schema_data['@graph'][6]['blogPost'][0]['@id'],'Blog should refer to BlogPosting');
	}

	private function get_schema_output( bool $debug_wpseo_head = false ): string {

		ob_start();
		do_action( 'wpseo_head' );
		$wpseo_head = ob_get_contents();
		ob_end_clean();

		if ( $debug_wpseo_head ) {
			print $wpseo_head;
		}

		$dom = new \DOMDocument();
		$dom->loadHTML( $wpseo_head );
		$scripts = $dom->getElementsByTagName('script');
		foreach( $scripts as $script ) {
			if( $script instanceof \DOMElement && $script->getAttribute('type') === 'application/ld+json') {
				return $script->textContent;
			}
		}

		throw new \LengthException('No schema script was found in the wpseo_head output.' );
	}
}
