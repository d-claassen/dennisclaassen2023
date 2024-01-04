<?php

class AuthorTest extends \WP_UnitTestCase {

	private $author_id;

	public function setUp(): void {
		parent::setUp();

		// Author who's the main Yoast user.
		$this->author_id = self::factory()
			->user
			->create(
				[
				'first_name' => 'Jane',
				'last_name'  => 'Doe',
				]
			);

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

		// \print_r( \array_column( $schema_data['@graph'], '@type' ) );
		$this->assertSame('ProfilePage', $schema_data['@graph'][0]['@type'],'First graph piece should be BlogPosting');

		$this->assertSame(['Person', 'Organization'], $schema_data['@graph'][3]['@type'],'Fourth graph piece should be Person');

		$person_data = $schema_data['@graph'][3];
		$this->assertSame('Jane', $person_data['givenName']);
		$this->assertSame('Doe', $person_data['familyName']);

		//hardcoded
		$this->assertSame('Lead developer', $person_data['jobTitle']);
		$this->assertSame('https://schema.org/Male', $person_data['gender']);
		$this->assertSame(
			['@type'=>'Country', 'name'=>'Netherlands'],
			$person_data['nationality',
		);
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
