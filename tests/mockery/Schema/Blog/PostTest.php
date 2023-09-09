<?php

namespace DC23\Tests\Schema\Blog;

use Brain\Faker\Providers as BrainFaker;
use Brain\Monkey\Functions;
use Faker\Generator as FakerGenerator;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Yoast\WP\Lib\ORM as Yoast_ORM;
use Yoast\WP\SEO\Context\Meta_Tags_Context;
use Yoast\WP\SEO\Helpers\Image_Helper;
use Yoast\WP\SEO\Helpers\Indexable_Helper;
use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Helpers\Permalink_Helper;
use Yoast\WP\SEO\Helpers\Schema\ID_Helper as Schema_ID_Helper;
use Yoast\WP\SEO\Helpers\Site_Helper;
use Yoast\WP\SEO\Helpers\Url_Helper;
use Yoast\WP\SEO\Helpers\User_Helper;
use Yoast\WP\SEO\Models\Indexable;
use Yoast\WP\SEO\Repositories\Indexable_Repository;

class PostTest extends TestCase {
	use \Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

	protected FakerGenerator $faker;
	protected BrainFaker $wpFaker;

	private Options_Helper&MockInterface $options_helper;
	private Indexable_Helper&MockInterface $indexable_helper;
	private Indexable_Repository&MockInterface $indexable_repository;

	public function setUp(): void {
		parent::setUp();
		\Brain\Monkey\setUp();
		
		$this->faker = \Brain\faker();
		$this->wpFaker = $this->faker->wp();
	}

	public function tearDown(): void {
		\Brain\fakerReset();
        
		\Brain\Monkey\tearDown();
		parent::tearDown();
	}

	private function getContext(): Meta_Tags_Context {

		\Brain\Monkey\Functions\when('is_multisite')->justReturn(false);

		$this->options_helper = \Mockery::mock( Options_Helper::class );
		$url_helper = new Url_Helper();
		$image_helper = \Mockery::spy( Image_Helper::class );
		$id_helper = new Schema_ID_Helper();
		$replace_vars = new \WPSEO_Replace_Vars();
		$site_helper = new Site_Helper();
		$user_helper = new User_Helper();
		$permalink_helper = new Permalink_Helper();
		$this->indexable_helper = \Mockery::spy( Indexable_Helper::class );
		$this->indexable_repository = \Mockery::spy( Indexable_Repository::class );

		$context = new Meta_Tags_Context(
			$this->options_helper,
			$url_helper,
			$image_helper,
			$id_helper,
			$replace_vars,
			$site_helper,
			$user_helper,
			$permalink_helper,
			$this->indexable_helper,
			$this->indexable_repository,
		);
		$context->indexable = \Mockery::mock( Indexable::class );
		$context->indexable->orm = new class extends Yoast_ORM {

			public function __construct(
				public $table_name = '',
				public $data = []
				) {}
		};

		$context->site_url = 'https://example.com/';
		
		// Return actual model instead of prototype.
		return $context->of( [] );
	}

	public function testRegistrationAddsFilter(): void {
		$post = new \DC23\Schema\Blog\Post();
		$post->register();

		self::assertSame( 11, has_filter( 'wpseo_schema_graph_pieces', [ $post, 'add_blog_to_schema' ] ) );
	}

	public function testRunningTheFilterRequiredSinglePage(): void {
		\Brain\Monkey\Functions\expect('is_single')->andReturnFalse();

		( $post = new \DC23\Schema\Blog\Post() )->register();

		$context = $this->getContext();

		$filter_result = $post->add_blog_to_schema( [], $context );

		self::assertSame( [], $filter_result );
	}

	public function testRunningTheFilterRequiredPostTypePost(): void {
		\Brain\Monkey\Functions\expect('is_single')->andReturnTrue();
		\Brain\Monkey\Functions\expect('get_post_type')->andReturn('page');

		( $post = new \DC23\Schema\Blog\Post() )->register();


		$context = $this->getContext();
		$filter_result = $post->add_blog_to_schema( [], $context );

		self::assertSame( [], $filter_result );
	}

	public function testRunningTheFilterRequiredSchemaArticleTypeBlogPosting(): void {
		\Brain\Monkey\Functions\expect('is_single')->andReturnTrue();
		\Brain\Monkey\Functions\expect('get_post_type')->andReturn('post');

		$context = $this->getContext();
		
		( $post = new \DC23\Schema\Blog\Post() )->register();

		$context->indexable->schema_article_type = 'Article';
		$filter_result = $post->add_blog_to_schema( [], $context );

		self::assertSame( [], $filter_result );
	}

	public function testIgnoresMultipleCategories(): void {
		\Brain\Monkey\Functions\expect('is_single')->once()->andReturnTrue();
		\Brain\Monkey\Functions\expect('get_post_type')->once()->andReturn('post');
		\Brain\Monkey\Functions\expect('get_the_ID')->once()->andReturn('1');

		$wp_post = $this->wpFaker->post( [ 'ID' => 1 ] );
		
		$category = $this->wpFaker->term( [ 
			'term_id'     => 1,
			'name'        => 'The category name',
			'description' => 'Very extensive and detailed description about this category. It explains what the reader can find here, why this exists, and what may appear here in the future.',
		]);
		
		$other_category = $this->wpFaker->term( [ 
			'term_id'     => 2,
			'name'        => 'Another category\'s name',
			'description' => 'A different category with a detailed description abot the completely different content it contains.',
		] );
		
		\Brain\Monkey\Functions\expect( 'wp_get_post_categories' )->once()->andReturn( [ $category, $other_category ] );

		$context = $this->getContext();
		$context->indexable->schema_article_type = 'BlogPosting';
		
		$user = $this->wpFaker->user( [ 'ID' => 1, 'user_login' => 'info@example.com'] );
		
		( $post = new \DC23\Schema\Blog\Post() )->register();
		
		$schema_pieces = $post->add_blog_to_schema( [], $context );

		self::assertCount( 0, $schema_pieces, 'no schema piece should be added' );
	}

	public function testWithoutCategory(): void {
		\Brain\Monkey\Functions\expect('is_single')->once()->andReturnTrue();
		\Brain\Monkey\Functions\expect('get_post_type')->once()->andReturn('post');
		\Brain\Monkey\Functions\expect('get_the_ID')->once()->andReturn('1');

		$wp_post = $this->wpFaker->post( [ 'ID' => 1 ] );
		
		// No category.
		\Brain\Monkey\Functions\expect( 'wp_get_post_categories' )->once()->andReturn( [] );

		$context = $this->getContext();
		$context->indexable->schema_article_type = 'BlogPosting';

		$user = $this->wpFaker->user( [ 'ID' => 1, 'user_login' => 'info@example.com'] );
		
		( $post = new \DC23\Schema\Blog\Post() )->register();
		
		$schema_pieces = $post->add_blog_to_schema( [], $context );

		self::assertCount( 0, $schema_pieces, 'no schema piece should be added' );
	}
	
	public function testRunningTheFilterAddsBlogSchema(): void {
		\Brain\Monkey\Functions\expect('is_single')->once()->andReturnTrue();
		\Brain\Monkey\Functions\expect('get_post_type')->once()->andReturn('post');
		\Brain\Monkey\Functions\expect('get_the_ID')->once()->andReturn('1');

		$wp_post = $this->wpFaker->post( [ 'ID' => 1 ] );
		
		\Brain\Monkey\Functions\expect('get_permalink')->once()->andReturn( 'https://example.com/page.html' );
		\Brain\Monkey\Functions\expect('get_term_link')->once()->andReturn( 'https://example.com/category.html' );

		$category = $this->wpFaker->term( [ 
			'term_id'     => 1,
			'name'        => 'The category name',
			'description' => 'Very extensive and detailed description about this category. It explains what the reader can find here, why this exists, and what may appear here in the future.',
		]);
		
		\Brain\Monkey\Functions\expect( 'wp_get_post_categories' )->once()->andReturn( [ $category ] );
		
		\Brain\Monkey\Functions\when('wp_trim_excerpt')->returnArg();
		\Brain\Monkey\Functions\when('wp_hash')->alias('str_rot13');
		
		\Brain\Monkey\Functions\expect('get_bloginfo')->once()->with('language')->andReturn('en-US');

		$context = $this->getContext();
		$context->indexable->schema_article_type = 'BlogPosting';
		$context->canonical = 'https://example.com/';

		$this->options_helper->expects('get')->once()->with('company_or_person', false)->andReturns('person');
		$this->options_helper->expects('get')->once()->with('company_or_person_user_id', false)->andReturns(1);
		
		$user = $this->wpFaker->user( [ 'ID' => 1, 'user_login' => 'info@example.com'] );
		
		( $post = new \DC23\Schema\Blog\Post() )->register();
		
		$schema_pieces = $post->add_blog_to_schema( [], $context );

		self::assertCount( 1, $schema_pieces, '1 schema piece should be added' );
		self::assertContainsOnlyInstancesOf( \Yoast\WP\SEO\Generators\Schema\Abstract_Schema_Piece::class, $schema_pieces );

		$piece = array_pop( $schema_pieces );
		self::assertTrue( $piece->is_needed(), 'The piece is needed in the output' );
		$schema = $piece->generate();

		self::assertThat(
			$schema,
			self::logicalAnd(
				self::arrayHasKey('@id'),
				self::arrayHasKey('@type')
			),
			'schema has id and type'
		);
		self::assertSame( 'https://example.com/#/schema/Blog/1', $schema['@id'], '@id uses: domain, term id, format');
		self::assertSame( 'Blog', $schema['@type']);
		self::assertSame('https://example.com/category.html', $schema['mainEntityOfPage']);
		self::assertSame('The category name', $schema['name']);
		self::assertThat(
			$schema['description'],
			self::logicalAnd(
				self::stringStartsWith('Very extensive and detailed description'),
				self::stringEndsWith('may appear here in the future.')
			)
		);
		self::assertSame(
			['@id'=>'https://example.com/#/schema/person/vasb@rknzcyr.pbz1'],
			$schema['publisher']
		);
		self::assertSame(
			['@id'=>'https://example.com/#/language/en-US'],
			$schema['inLanguage']
		);
		self::assertSame(
			[['@id'=>'https://example.com/page.html#article']],
			$schema['blogPost']
		);
	}

}
