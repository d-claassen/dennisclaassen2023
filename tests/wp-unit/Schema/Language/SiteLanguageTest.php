<?php

final class SiteLanguageTest extends \WP_UnitTestCase {

    public function set_up(): void {
        parent::set_up();
        
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
    
    public function test_frontpage_has_enriched_language_nodes(): void {
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

		\var_dump( $post_id );
		\var_dump( $home_url = get_home_url() );
		$this->go_to( $home_url );

        $schema_output = $this->get_schema_output(true);
		$this->assertJson( $schema_output );

		$schema_data = json_decode( $schema_output, JSON_OBJECT_AS_ARRAY );

		$this->assertSame(['WebPage', 'ProfilePage'], $schema_data['@graph'][0]['@type'],'First graph piece should be ProfilePage');
        $this->assertSame(['Person', 'Organization'], $schema_data['@graph'][3]['@type'],'Fourth graph piece should be Person');

		$webpage_data = $schema_data['@graph'][0];
		$person_data  = $schema_data['@graph'][3];
        
        $this->assertSame(
            [ '@id' => 'http://example.org/#/schema/language/en-us'],
            $webpage_data['inLanguage']
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
		$dom->loadHTML( $wpseo_head );
		$scripts = $dom->getElementsByTagName('script');
		foreach( $scripts as $script ) {
			if( $script instanceof \DOMElement && $script->getAttribute('type') === 'application/ld+json') {
				return $script->textContent;
			}
		}

		throw new \LengthException('No schema script was found in the wpseo_head output.' );
	}

	/**
	 * Find a Schema.org piece in the root of the Graph by its type.
	 *
	 * @param array<int, array{"@type": string}> $graph Schema.org graph.
	 * @param string|array<int, string> $type Schema type to search for.
	 * @return array{"@type": string} The matching schema.org piece.
	 */
	private function get_piece_by_type( $graph, $type ): array {
		$nodes_of_type = array_filter( $graph, fn( $piece ) => $piece['@type'] === $type );

		if ( empty( $nodes_of_type ) ) {
			throw new InvalidArgumentException( 'No piece found for type' );
		}

		// Return first instance.
		return reset( $nodes_of_type );
	}
}