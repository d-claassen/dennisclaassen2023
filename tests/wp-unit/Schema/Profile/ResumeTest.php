<?php

namespace DC23\WP_Tests\Schema\Profile;

/**
 * Class ResumeTest.
 *
 * @testdox Schema for a one author blog frontpage
 */
class ResumeTest extends \WP_UnitTestCase {

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

	/**
	 * @testdox Should connect frontpage to Person, when set as ProfilePage
	 */
	public function test_frontpage_as_profile_page_connects_to_blog_author(): void {
		$post_id = self::factory()->post->create(
			array(
				'post_content' => 'Custom homepage',
				'post_author'  => $this->author_id,
				'post_type'    => 'page',
			)
		);

		// Configure the custom homepage as "ProfilePage".
		\YoastSEO()->helpers->meta->set_value( 'schema_page_type', 'ProfilePage', $post_id );

		// Update object to persist meta value to indexable.
		self::factory()->post->update_object( $post_id, [] );

		// Configure the page to be the custom frontpage.
		\update_option( 'show_on_front', 'page' );
		\update_option( 'page_on_front', $post_id );

		$this->go_to( get_home_url() );

		$schema_output = $this->get_schema_output();
		$this->assertJson( $schema_output );

		$schema_data = json_decode( $schema_output, JSON_OBJECT_AS_ARRAY );

		$this->assertSame(['WebPage', 'ProfilePage'], $schema_data['@graph'][0]['@type'],'First graph piece should be ProfilePage');
		$this->assertSame(['Person', 'Organization'], $schema_data['@graph'][3]['@type'],'Fourth graph piece should be Person');

		$webpage_data = $schema_data['@graph'][0];
		$person_data  = $schema_data['@graph'][3];
		$this->assertSame(
			$person_data['@id'],
			$webpage_data['mainEntity']['@id'],
			'ProfilePage main entity should point to Person'
		);
	}

	/**
	 * @testdox Should enhance blog author Person with user data
	 */
	public function test_should_enhance_blog_person_with_author_data(): void {
		$post_id = self::factory()->post->create(
			array(
				'post_content' => 'Custom homepage',
				'post_author'  => $this->author_id,
				'post_type'    => 'page',
			)
		);

		// Configure the custom homepage as "ProfilePage".
		\YoastSEO()->helpers->meta->set_value( 'schema_page_type', 'ProfilePage', $post_id );

		// Update object to persist meta value to indexable.
		self::factory()->post->update_object( $post_id, [] );

		// Configure the page to be the custom frontpage.
		\update_option( 'show_on_front', 'page' );
		\update_option( 'page_on_front', $post_id );

		$this->go_to( get_home_url() );

		$schema_output = $this->get_schema_output();
		$this->assertJson( $schema_output );

		$schema_data = json_decode( $schema_output, JSON_OBJECT_AS_ARRAY );

		$this->assertSame(['WebPage', 'ProfilePage'], $schema_data['@graph'][0]['@type'],'First graph piece should be ProfilePage');
		$this->assertSame(['Person', 'Organization'], $schema_data['@graph'][3]['@type'],'Fourth graph piece should be Person');

		$person_data  = $schema_data['@graph'][3];
		$this->assertSame('Jane', $person_data['givenName']);
		$this->assertSame('Doe', $person_data['familyName']);
	}

	/**
	 * @testdox Should enhance blog author Person with resume data
	 */
	public function test_frontpage_as_profile_page_extended_with_resume(): void {
		$post_id = self::factory()->post->create(
			array(
				'post_content' => 'Custom homepage',
				'post_author'  => $this->author_id,
				'post_type'    => 'page',
			)
		);

		// Configure the custom homepage as "ProfilePage".
		\YoastSEO()->helpers->meta->set_value( 'schema_page_type', 'ProfilePage', $post_id );

		// Update object to persist meta value to indexable.
		self::factory()->post->update_object( $post_id, [] );

		// Configure the page to be the custom frontpage.
		\update_option( 'show_on_front', 'page' );
		\update_option( 'page_on_front', $post_id );

		$this->go_to( get_home_url() );

		$schema_output = $this->get_schema_output();
		$this->assertJson( $schema_output );

		$schema_data = json_decode( $schema_output, JSON_OBJECT_AS_ARRAY );

		$this->assertSame(['WebPage', 'ProfilePage'], $schema_data['@graph'][0]['@type'],'First graph piece should be ProfilePage');
		$this->assertSame(['Person', 'Organization'], $schema_data['@graph'][3]['@type'],'Fourth graph piece should be Person');

		$person_data  = $schema_data['@graph'][3];

		//hardcoded
		$this->assertSame('Lead developer', $person_data['jobTitle']);
		$this->assertSame('http://schema.org/Male', $person_data['gender']);
		$this->assertSame(
			[
				'@type' => 'Country',
				'name' => 'Netherlands',
				'alternateName' => 'NL',
				'sameAs' => 'https://en.wikipedia.org/wiki/Netherlands',
			],
			$person_data['nationality'],
		);

		$this->assertCount( 10, $person_data['worksFor'] );
		$this->assertCount( 1, $person_data['alumniOf'] );
		$this->assertCount( 5, $person_data['knowsAbout'] );
	}

	/**
	 * @testdox Should enhance site publisher Person with some resume data
	 */
	public function test_frontpage_with_linited_resume(): void {
		$post_id = self::factory()->post->create(
			array(
				'post_content' => 'Custom homepage',
				'post_author'  => $this->author_id,
				'post_type'    => 'page',
			)
		);

		// Configure the custom homepage as "CollectionPage".
		\YoastSEO()->helpers->meta->set_value( 'schema_page_type', 'CollectionPage', $post_id );

		// Update object to persist meta value to indexable.
		self::factory()->post->update_object( $post_id, [] );

		// Configure the page to be the custom frontpage.
		\update_option( 'show_on_front', 'page' );
		\update_option( 'page_on_front', $post_id );

		$this->go_to( get_home_url() );

		$schema_output = $this->get_schema_output();
		$this->assertJson( $schema_output );

		$schema_data = json_decode( $schema_output, JSON_OBJECT_AS_ARRAY );

		$this->assertSame(['WebPage', 'CollectionPage'], $schema_data['@graph'][0]['@type'],'First graph piece should be ProfilePage');
		$this->assertSame(['Person', 'Organization'], $schema_data['@graph'][3]['@type'],'Fourth graph piece should be Person');

		$person_data = $schema_data['@graph'][3];

		//hardcoded
		$this->assertSame('Lead developer', $person_data['jobTitle']);
		$this->assertSame('http://schema.org/Male', $person_data['gender']);
		$this->assertSame(
			[
				'@type' => 'Country',
				'name' => 'Netherlands',
				'alternateName' => 'NL',
				'sameAs' => 'https://en.wikipedia.org/wiki/Netherlands',
			],
			$person_data['nationality'],
		);

		$this->assertArrayKeyNotExist( 'worksFor', $person_data );
		$this->assertArrayKeyNotExist( 'alumniOf', $person_data );
		$this->assertArrayKeyNotExist( 'knowsAbout', $person_data );
		$this->assertArrayKeyNotExist( 'knowsLanguage', $person_data );
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
