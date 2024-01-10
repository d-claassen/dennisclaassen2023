<?php

use PHPUnit\Framework\Attributes;

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

	public function test_default_article_unchanged_no_blog(): void {
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

	public function test_default_article_type_adds_blog(): void {
		$post_id = self::factory()->post->create(
			array(
				'title'        => 'default setting',
				'post_content' => 'Hello world!',
			)
		);

		\YoastSEO()->helpers->options->set( 'schema-article-type-post', 'BlogPosting' );

		$this->go_to( \get_permalink( $post_id ) );

		$schema_output = $this->get_schema_output();

		$this->assertJson( $schema_output );

		$schema_data = json_decode( $schema_output, JSON_OBJECT_AS_ARRAY );

		$this->assertSame(['Article','BlogPosting'], $schema_data['@graph'][0]['@type'],'First graph piece should be BlogPosting');
		$this->assertSame('Blog', $schema_data['@graph'][6]['@type'],'Sixth graph piece should be Blog');
		$this->assertSame($schema_data['@graph'][0]['@id'], $schema_data['@graph'][6]['blogPost'][0]['@id'],'Blog should refer to BlogPosting');
	}

	public function test_indexable_article_type_adds_blog(): void {
		$post_id = self::factory()->post->create(
			array(
				'title'        => 'indexable setting',
				'post_content' => 'Hello world!',
			)
		);

		\YoastSEO()->helpers->meta->set_value( 'schema_article_type', 'BlogPosting', $post_id );

		$this->go_to( \get_permalink( $post_id ) );

		$schema_output = $this->get_schema_output();

		$this->assertJson( $schema_output );

		$schema_data = \json_decode( $schema_output, JSON_OBJECT_AS_ARRAY );

		$this->assertSame(['Article','BlogPosting'], $schema_data['@graph'][0]['@type'],'First graph piece should be BlogPosting');
		$this->assertSame('Blog', $schema_data['@graph'][6]['@type'],'Sixth graph piece should be Blog');
		$this->assertSame($schema_data['@graph'][0]['@id'], $schema_data['@graph'][6]['blogPost'][0]['@id'],'Blog should refer to BlogPosting');
	}

	private function get_schema_output( bool $debug_wpseo_head = true ): string {

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
