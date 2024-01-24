<?php

/**
 * Class PostTest.
 *
 * @testdox Schema for a single post "post"
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

		$article_piece = $this->get_piece_by_type( $schema_data['@graph'], 'Article' );

		$this->assertSame('Article', $article_piece['@type'],'Article piece should just be Article');
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

		$article_piece = $this->get_piece_by_type( $schema_data['@graph'], [ 'Article', 'BlogPosting' ] );
		$blog_piece = $this->get_piece_by_type( $schema_data['@graph'], 'Blog' );

		$this->assertSame(['Article','BlogPosting'], $article_piece['@type'],'Article graph piece should be BlogPosting');
		$this->assertSame('Blog', $blog_piece['@type'],'Blog graph piece should exist');
		$this->assertSame($article_piece['@id'], $blog_piece['blogPost'][0]['@id'],'Blog should refer to BlogPosting');
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

		$article_piece = $this->get_piece_by_type( $schema_data['@graph'], [ 'Article', 'BlogPosting' ] );
		$blog_piece = $this->get_piece_by_type( $schema_data['@graph'], 'Blog' );

		$this->assertSame(['Article','BlogPosting'], $article_piece['@type'],'Article piece should be BlogPosting');
		$this->assertSame('Blog', $blog_piece['@type'],'Blog graph piece should exit');
		$this->assertSame($article_piece['@id'], $blog_piece['blogPost'][0]['@id'],'Blog should refer to BlogPosting');
	}

	/**
	 * @testdox Should not contain Blog piece when the post has multiple categories
	 */
	public function test_should_not_contain_blog_piece_when_post_has_multiple_categories(): void {
		$post_id = self::factory()->post->create(
			array(
				'title'        => 'indexable setting',
				'post_content' => 'Hello world!',
			)
		);

		\YoastSEO()->helpers->meta->set_value( 'schema_article_type', 'BlogPosting', $post_id );

		\wp_set_post_categories(
			$post_id,
			[
				self::factory()->category->create(),
				self::factory()->category->create(),
				self::factory()->category->create(),
			]
		);

		// Update object to persist meta value to indexable.
		self::factory()->post->update_object( $post_id, [] );

		$this->go_to( \get_permalink( $post_id ) );

		$schema_output = $this->get_schema_output();

		$this->assertJson( $schema_output );

		$schema_data = \json_decode( $schema_output, JSON_OBJECT_AS_ARRAY );

		$article_piece = $this->get_piece_by_type( $schema_data['@graph'], [ 'Article', 'BlogPosting' ] );

		$this->assertSame([ 'Article', 'BlogPosting' ], $article_piece['@type'],'Article graph piece should be BlogPosting');
	}

	public function test_should_add_time_required_to_webpage(): void {
		$post_id = self::factory()->post->create(
			array(
				'title'        => 'WebPage with estimated reading time',
				'post_content' => <<<'EOL'
				Far far away, behind the word mountains, far from the countries Vokalia and Consonantia, there live 
				the blind texts. Separated they live in Bookmarksgrove right at the coast of the Semantics, a large 
				language ocean. A small river named Duden flows by their place and supplies it with the necessary
				regelialia. It is a paradisematic country, in which roasted parts of sentences fly into your mouth.
				Even the all-powerful Pointing has no control about the blind texts it is an almost unorthographic
				life One day however a small line of blind text by the name of Lorem Ipsum decided to leave for
				the far World of Grammar. The Big Oxmox advised her not to do so, because there were thousands
				of bad Commas, wild Question Marks and devious Semikoli, but the Little Blind Text didn’t
				listen. She packed her seven versalia, put her initial into the belt and made herself on the
				way. When she reached the first hills of the Italic Mountains, she had a last view back on
				the skyline of her hometown Bookmarksgrove, the headline of Alphabet Village and the subline
				of her own road, the Line Lane. Pityful a rethoric question ran over her cheek, then she
				continued her way. On her way she met a copy. The copy warned the Little Blind Text, that
				where it came from it would have been rewritten a thousand times and everything that was left
				from its origin would be the word "and".
				EOL,
			)
		);

		// Update object to persist meta value to indexable.
		self::factory()->post->update_object( $post_id, [] );

		$this->go_to( \get_permalink( $post_id ) );

		$schema_output = $this->get_schema_output();

		$this->assertJson( $schema_output );

		$schema_data = \json_decode( $schema_output, JSON_OBJECT_AS_ARRAY );

		$this->assertSame( 'WebPage', $schema_data['@graph'][1]['@type'], '2nd piece should be WebPage' );
		$this->assertSame( 'PT1M', $schema_data['@graph'][1]['timeRequired'], 'timeRequired should be filled for WebPage' );
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
