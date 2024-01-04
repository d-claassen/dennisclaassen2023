<?php

class AuthorTest extends \WP_UnitTestCase {

	private $author_id;

	public function setUp(): void {
		parent::setUp();

		// Author who's the main Yoast user.
		$this->author_id = self::factory()->user->create();

		\YoastSEO()->helpers->options->set( 'company_or_person', 'person' );
		\YoastSEO()->helpers->options->set( 'company_or_person_user_id', $this->author_id );
	}

	// override wordpress function thats incompatible
	// with phpunit 10.
	public function expectDeprecated(){}

	public function test_default_article_type_adds_blog(): void {
		$post_id = self::factory()->post->create(
			array(
				'post_content' => 'Hello world!',
        'post_author'  => $this->author_id,
			)
		);

		$author_url = \get_author_posts_url( $this->author_id );
		$this->go_to( $author_url );

		$schema_output = $this->get_schema_output( true );

		$this->assertJson( $schema_output );

		$schema_data = json_decode( $schema_output, JSON_OBJECT_AS_ARRAY );

		$this->assertSame('ProfilePage', $schema_data['@graph'][0]['@type'],'First graph piece should be BlogPosting');


		\print_r( \array_column( $schema_data['@graph'], '@type' ) );
			
		$this->assertSame(['Person', 'Organization'], $schema_data['@graph'][6]['@type'],'Sixth graph piece should be Blog');
		//$this->assertSame($schema_data['@graph'][0]['@id'], $schema_data['@graph'][6]['blogPost'][0]['@id'],'Blog should refer to BlogPosting');
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
		\libxml_use_internal_errors( true );
		$dom->strictErrorChecking = false;
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
