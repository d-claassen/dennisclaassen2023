<?php

namespace DC23\Tests\Schema\Blog;

use Brain\Monkey\Functions;
use Mockery\MockInterface;
use Yoast\WP\SEO\Context\Meta_Tags_Context;
use Yoast\WP\SEO\Helpers\Indexable_Helper;
use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Repositories\Indexable_Repository;

class PostTest extends \PHPUnit\Framework\TestCase {

	private Options_Helper&MockInterface $options_helper;
	private Indexable_Helper&MockInterface $indexable_helper;
	private Indexable_Repository&MockInterface $indexable_repository;

	public function setUp(): void {
		parent::setUp();
		\Brain\Monkey\setUp();
	}

	private function getContext(): Meta_Tags_Context {

		\Brain\Monkey\Functions\when('is_multisite')->justReturn(false);

		$this->options_helper = \Mockery::mock( \Yoast\WP\SEO\Helpers\Options_Helper::class );
		$url_helper = new \Yoast\WP\SEO\Helpers\Url_Helper();
		$image_helper = \Mockery::spy( \Yoast\WP\SEO\Helpers\Image_Helper::class );
		$id_helper = new \Yoast\WP\SEO\Helpers\Schema\ID_Helper();
		$replace_vars = new \WPSEO_Replace_Vars();
		$site_helper = new \Yoast\WP\SEO\Helpers\Site_Helper();
		$user_helper = new \Yoast\WP\SEO\Helpers\User_Helper();
		$permalink_helper = new \Yoast\WP\SEO\Helpers\Permalink_Helper();
		$this->indexable_helper = \Mockery::spy( \Yoast\WP\SEO\Helpers\Indexable_Helper::class );
		$this->indexable_repository = \Mockery::spy( \Yoast\WP\SEO\Repositories\Indexable_Repository::class );

		$context = \Mockery::mock(
			Meta_Tags_Context::class,
			[
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
			] 
		);
		$context = $context->makePartial();
		$context->indexable = \Mockery::mock( \Yoast\WP\SEO\Models\Indexable::class );
		$context->indexable->orm = new class extends \Yoast\WP\Lib\ORM {

			public function __construct(
				public $table_name = '',
				public $data = []
				) {}
		};

		$context->site_url = 'https://example.com/';
		
		// Return actual model instead of prototype.
		return $context->of( [] );
		
		$context = new Meta_Tags_Context(
			new \Yoast\WP\SEO\Helpers\Options_Helper(),
			new \Yoast\WP\SEO\Helpers\Url_Helper(),
			new \Yoast\WP\SEO\Helpers\Image_Helper(
				new \Yoast\WP\SEO\Repositories\Indexable_Repository(
					new \Yoast\WP\SEO\Builders\Indexable_Builder(
						new \Yoast\WP\SEO\Builders\Indexable_Author_Builder(
							new \Yoast\WP\SEO\Helpers\Author_Archive_Helper(
								new \Yoast\WP\SEO\Helpers\Options_Helper(),
								new \Yoast\WP\SEO\Helpers\Post_Type_Helper(
									new \Yoast\WP\SEO\Helpers\Options_Helper()
								)
							),
							new \Yoast\WP\SEO\Values\Indexables\Indexable_Builder_Versions(),
							new \Yoast\WP\SEO\Helpers\Post_Helper(
								new \Yoast\WP\SEO\Helpers\String_Helper(),
								new \Yoast\WP\SEO\Helpers\Post_Type_Helper(
									new \Yoast\WP\SEO\Helpers\Options_Helper()
								)
							),
							\Mockery::mock('wpdb')
						),
						new \Yoast\WP\SEO\Builders\Indexable_Post_Builder(
							new \Yoast\WP\SEO\Helpers\Post_Helper(
								new \Yoast\WP\SEO\Helpers\String_Helper(),
								new \Yoast\WP\SEO\Helpers\Post_Type_Helper(
									new \Yoast\WP\SEO\Helpers\Options_Helper()
								)
							),
							new \Yoast\WP\SEO\Helpers\Post_Type_Helper(
								new \Yoast\WP\SEO\Helpers\Options_Helper()
							),
							new \Yoast\WP\SEO\Values\Indexables\Indexable_Builder_Versions(),
							new \Yoast\WP\SEO\Helpers\Meta_Helper()
						),
						new \Yoast\WP\SEO\Builders\Indexable_Term_Builder(
							new \Yoast\WP\SEO\Helpers\Taxonomy_Helper(
								new \Yoast\WP\SEO\Helpers\Options_Helper(),
								new \Yoast\WP\SEO\Helpers\String_Helper()
							),
							new \Yoast\WP\SEO\Values\Indexables\Indexable_Builder_Versions(),
							new \Yoast\WP\SEO\Helpers\Post_Helper(
								new \Yoast\WP\SEO\Helpers\String_Helper(),
								new \Yoast\WP\SEO\Helpers\Post_Type_Helper(
									new \Yoast\WP\SEO\Helpers\Options_Helper()
								)
							),
							\Mockery::mock('wpdb')
						),
						new \Yoast\WP\SEO\Builders\Indexable_Home_Page_Builder(
							new \Yoast\WP\SEO\Helpers\Options_Helper(),
							new \Yoast\WP\SEO\Helpers\Url_Helper(),
							new \Yoast\WP\SEO\Values\Indexables\Indexable_Builder_Versions(),
							new \Yoast\WP\SEO\Helpers\Post_Helper(
								new \Yoast\WP\SEO\Helpers\String_Helper(),
								new \Yoast\WP\SEO\Helpers\Post_Type_Helper(
									new \Yoast\WP\SEO\Helpers\Options_Helper()
								)
							),
							\Mockery::mock('wpdb')
						),
						new \Yoast\WP\SEO\Builders\Indexable_Post_Type_Archive_Builder(
							new \Yoast\WP\SEO\Helpers\Options_Helper(),
							new \Yoast\WP\SEO\Values\Indexables\Indexable_Builder_Versions(),
							new \Yoast\WP\SEO\Helpers\Post_Helper(
								new \Yoast\WP\SEO\Helpers\String_Helper(),
								new \Yoast\WP\SEO\Helpers\Post_Type_Helper(
									new \Yoast\WP\SEO\Helpers\Options_Helper()
								)
							),
							new \Yoast\WP\SEO\Helpers\Post_Type_Helper(
								new \Yoast\WP\SEO\Helpers\Options_Helper()
							),
							\Mockery::mock('wpdb')
						),
						new \Yoast\WP\SEO\Builders\Indexable_Date_Archive_Builder(
							new \Yoast\WP\SEO\Helpers\Options_Helper(),
							new \Yoast\WP\SEO\Values\Indexables\Indexable_Builder_Versions()
						),
						new \Yoast\WP\SEO\Builders\Indexable_System_Page_Builder(
							new \Yoast\WP\SEO\Helpers\Options_Helper(),
							new \Yoast\WP\SEO\Values\Indexables\Indexable_Builder_Versions()
						),
						new \Yoast\WP\SEO\Builders\Indexable_Hierarchy_Builder(
							new \Yoast\WP\SEO\Repositories\Indexable_Hierarchy_Repository(),
							new \Yoast\WP\SEO\Repositories\Primary_Term_Repository(),
							new \Yoast\WP\SEO\Helpers\Options_Helper(),
							new \Yoast\WP\SEO\Helpers\Post_Helper(
								new \Yoast\WP\SEO\Helpers\String_Helper(),
								new \Yoast\WP\SEO\Helpers\Post_Type_Helper(
									new \Yoast\WP\SEO\Helpers\Options_Helper()
								)
							)
						),
						new \Yoast\WP\SEO\Builders\Primary_Term_Builder(
							new \Yoast\WP\SEO\Repositories\Primary_Term_Repository(),
							new \Yoast\WP\SEO\Helpers\Primary_Term_Helper(),
							new \Yoast\WP\SEO\Helpers\Meta_Helper()
						),
						new \Yoast\WP\SEO\Helpers\Indexable_Helper(
							new \Yoast\WP\SEO\Helpers\Options_Helper(),
							new \Yoast\WP\SEO\Helpers\Environment_Helper(),
							new \Yoast\WP\SEO\Helpers\Indexing_Helper(
								new \Yoast\WP\SEO\Helpers\Options_Helper(),
								new \Yoast\WP\SEO\Helpers\Date_Helper(),
								\Yoast_Notification_Center::get()
							)
						),
						new \Yoast\WP\SEO\Services\Indexables\Indexable_Version_Manager(
							new \Yoast\WP\SEO\Values\Indexables\Indexable_Builder_Versions()
						),
						new \Yoast\WP\SEO\Builders\Indexable_Link_Builder(
							new \Yoast\WP\SEO\Repositories\SEO_Links_Repository(),
							new \Yoast\WP\SEO\Helpers\Url_Helper(),
							new \Yoast\WP\SEO\Helpers\Post_Helper(
								new \Yoast\WP\SEO\Helpers\String_Helper(),
								new \Yoast\WP\SEO\Helpers\Post_Type_Helper(
									new \Yoast\WP\SEO\Helpers\Options_Helper()
								)
							),
							new \Yoast\WP\SEO\Helpers\Options_Helper()
						)
					),
					new \Yoast\WP\SEO\Helpers\Current_Page_Helper(
						new \Yoast\WP\SEO\Wrappers\WP_Query_Wrapper()
					),
					new \Yoast\WP\SEO\Loggers\Logger(),
					new \Yoast\WP\SEO\Repositories\Indexable_Hierarchy_Repository(),
					\Mockery::mock('wpdb'),
					new \Yoast\WP\SEO\Services\Indexables\Indexable_Version_Manager(
						new \Yoast\WP\SEO\Values\Indexables\Indexable_Builder_Versions()
					)
				),
				new \Yoast\WP\SEO\Repositories\SEO_Links_Repository(),
				new \Yoast\WP\SEO\Helpers\Options_Helper(),
				new \Yoast\WP\SEO\Helpers\Url_Helper(),
			),
			new \Yoast\WP\SEO\Helpers\Schema\ID_Helper(),
			new \WPSEO_Replace_Vars(),
			new \Yoast\WP\SEO\Helpers\Site_Helper(),
			new \Yoast\WP\SEO\Helpers\User_Helper(),
			new \Yoast\WP\SEO\Helpers\Permalink_Helper(),
			new \Yoast\WP\SEO\Helpers\Indexable_Helper(
				new \Yoast\WP\SEO\Helpers\Options_Helper(),
				new \Yoast\WP\SEO\Helpers\Environment_Helper(),
				new \Yoast\WP\SEO\Helpers\Indexing_Helper(
					new \Yoast\WP\SEO\Helpers\Options_Helper(),
					new \Yoast\WP\SEO\Helpers\Date_Helper(),
					\Yoast_Notification_Center::get()
				)
			),
			new \Yoast\WP\SEO\Repositories\Indexable_Repository(
				new \Yoast\WP\SEO\Builders\Indexable_Builder(
					new \Yoast\WP\SEO\Builders\Indexable_Author_Builder(
						new \Yoast\WP\SEO\Helpers\Author_Archive_Helper(
							new \Yoast\WP\SEO\Helpers\Options_Helper(),
							new \Yoast\WP\SEO\Helpers\Post_Type_Helper(
								new \Yoast\WP\SEO\Helpers\Options_Helper()
							)
						),
						new \Yoast\WP\SEO\Values\Indexables\Indexable_Builder_Versions(),
						new \Yoast\WP\SEO\Helpers\Post_Helper(
							new \Yoast\WP\SEO\Helpers\String_Helper(),
							new \Yoast\WP\SEO\Helpers\Post_Type_Helper(
								new \Yoast\WP\SEO\Helpers\Options_Helper()
							)
						),
						\Mockery::mock('wpdb')
					),
					new \Yoast\WP\SEO\Builders\Indexable_Post_Builder(
						new \Yoast\WP\SEO\Helpers\Post_Helper(
							new \Yoast\WP\SEO\Helpers\String_Helper(),
							new \Yoast\WP\SEO\Helpers\Post_Type_Helper(
								new \Yoast\WP\SEO\Helpers\Options_Helper()
							)
						),
						new \Yoast\WP\SEO\Helpers\Post_Type_Helper(
							new \Yoast\WP\SEO\Helpers\Options_Helper()
						),
						new \Yoast\WP\SEO\Values\Indexables\Indexable_Builder_Versions(),
						new \Yoast\WP\SEO\Helpers\Meta_Helper()
					),
					new \Yoast\WP\SEO\Builders\Indexable_Term_Builder(
						new \Yoast\WP\SEO\Helpers\Taxonomy_Helper(
							new \Yoast\WP\SEO\Helpers\Options_Helper(),
							new \Yoast\WP\SEO\Helpers\String_Helper()
						),
						new \Yoast\WP\SEO\Values\Indexables\Indexable_Builder_Versions(),
						new \Yoast\WP\SEO\Helpers\Post_Helper(
							new \Yoast\WP\SEO\Helpers\String_Helper(),
							new \Yoast\WP\SEO\Helpers\Post_Type_Helper(
								new \Yoast\WP\SEO\Helpers\Options_Helper()
							)
						),
						\Mockery::mock('wpdb')
					),
					new \Yoast\WP\SEO\Builders\Indexable_Home_Page_Builder(
						new \Yoast\WP\SEO\Helpers\Options_Helper(),
						new \Yoast\WP\SEO\Helpers\Url_Helper(),
						new \Yoast\WP\SEO\Values\Indexables\Indexable_Builder_Versions(),
						new \Yoast\WP\SEO\Helpers\Post_Helper(
							new \Yoast\WP\SEO\Helpers\String_Helper(),
							new \Yoast\WP\SEO\Helpers\Post_Type_Helper(
								new \Yoast\WP\SEO\Helpers\Options_Helper()
							)
						),
						\Mockery::mock('wpdb')
					),
					new \Yoast\WP\SEO\Builders\Indexable_Post_Type_Archive_Builder(
						new \Yoast\WP\SEO\Helpers\Options_Helper(),
						new \Yoast\WP\SEO\Values\Indexables\Indexable_Builder_Versions(),
						new \Yoast\WP\SEO\Helpers\Post_Helper(
							new \Yoast\WP\SEO\Helpers\String_Helper(),
							new \Yoast\WP\SEO\Helpers\Post_Type_Helper(
								new \Yoast\WP\SEO\Helpers\Options_Helper()
							)
						),
						new \Yoast\WP\SEO\Helpers\Post_Type_Helper(
							new \Yoast\WP\SEO\Helpers\Options_Helper()
						),
						\Mockery::mock('wpdb')
					),
					new \Yoast\WP\SEO\Builders\Indexable_Date_Archive_Builder(
						new \Yoast\WP\SEO\Helpers\Options_Helper(),
						new \Yoast\WP\SEO\Values\Indexables\Indexable_Builder_Versions()
					),
					new \Yoast\WP\SEO\Builders\Indexable_System_Page_Builder(
						new \Yoast\WP\SEO\Helpers\Options_Helper(),
						new \Yoast\WP\SEO\Values\Indexables\Indexable_Builder_Versions()
					),
					new \Yoast\WP\SEO\Builders\Indexable_Hierarchy_Builder(
						new \Yoast\WP\SEO\Repositories\Indexable_Hierarchy_Repository(),
						new \Yoast\WP\SEO\Repositories\Primary_Term_Repository(),
						new \Yoast\WP\SEO\Helpers\Options_Helper(),
						new \Yoast\WP\SEO\Helpers\Post_Helper(
							new \Yoast\WP\SEO\Helpers\String_Helper(),
							new \Yoast\WP\SEO\Helpers\Post_Type_Helper(
								new \Yoast\WP\SEO\Helpers\Options_Helper()
							)
						)
					),
					new \Yoast\WP\SEO\Builders\Primary_Term_Builder(
						new \Yoast\WP\SEO\Repositories\Primary_Term_Repository(),
						new \Yoast\WP\SEO\Helpers\Primary_Term_Helper(),
						new \Yoast\WP\SEO\Helpers\Meta_Helper()
					),
					new \Yoast\WP\SEO\Helpers\Indexable_Helper(
						new \Yoast\WP\SEO\Helpers\Options_Helper(),
						new \Yoast\WP\SEO\Helpers\Environment_Helper(),
						new \Yoast\WP\SEO\Helpers\Indexing_Helper(
							new \Yoast\WP\SEO\Helpers\Options_Helper(),
							new \Yoast\WP\SEO\Helpers\Date_Helper(),
							\Yoast_Notification_Center::get()
						)
					),
					new \Yoast\WP\SEO\Services\Indexables\Indexable_Version_Manager(
						new \Yoast\WP\SEO\Values\Indexables\Indexable_Builder_Versions()
					),
					new \Yoast\WP\SEO\Builders\Indexable_Link_Builder(
						new \Yoast\WP\SEO\Repositories\SEO_Links_Repository(),
						new \Yoast\WP\SEO\Helpers\Url_Helper(),
						new \Yoast\WP\SEO\Helpers\Post_Helper(
							new \Yoast\WP\SEO\Helpers\String_Helper(),
							new \Yoast\WP\SEO\Helpers\Post_Type_Helper(
								new \Yoast\WP\SEO\Helpers\Options_Helper()
							)
						),
						new \Yoast\WP\SEO\Helpers\Options_Helper()
					)
				),
				new \Yoast\WP\SEO\Helpers\Current_Page_Helper(
					new \Yoast\WP\SEO\Wrappers\WP_Query_Wrapper()
				),
				new \Yoast\WP\SEO\Loggers\Logger(),
				new \Yoast\WP\SEO\Repositories\Indexable_Hierarchy_Repository(),
				\Mockery::mock('wpdb'),
				new \Yoast\WP\SEO\Services\Indexables\Indexable_Version_Manager(
					new \Yoast\WP\SEO\Values\Indexables\Indexable_Builder_Versions()
				)
			),
		);

		// Convert context prototype to modal.
		$the_context = $context->of( [] );

		return $the_context;
	}

	public function tearDown(): void {
		\Brain\Monkey\tearDown();
		parent::tearDown();
	}

	public function testRegistrationAddsFilter(): void {
		//$this->markTestSkipped('no context');
		( new \DC23\Schema\Blog\Post() )->register();

		self::assertTrue( has_filter( 'wpseo_schema_graph_pieces' ) );
	}

	public function testRunningTheFilterRequiredSinglePage(): void {
		// $this->markTestSkipped('skip2');
		\Brain\Monkey\Functions\expect('is_single')->andReturnFalse();

		// $this->markTestSkipped('the expect works');
		( $post = new \DC23\Schema\Blog\Post() )->register();

		//echo '[testRunningTheFilterRequiredSinglePage:get_context]';
		// $this->markTestSkipped('registered post schema');
		$context = $this->getContext();

		// $this->markTestSkipped('got context');
		$filter_result = $post->add_blog_to_schema( [], $context );

		// $this->markTestSkipped('apply_filter worked too');
		self::assertSame( [], $filter_result );
	}

	public function testRunningTheFilterRequiredPostTypePost(): void {
		// $this->markTestSkipped('skip3');
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

	public function testRunningTheFilterAddsBlogSchema(): void {
		\Brain\Monkey\Functions\expect('is_single')->andReturnTrue();
		\Brain\Monkey\Functions\expect('get_post_type')->andReturn('post');

		$wp_post = \Mockery::mock( 'WP_Post' );
		$wp_post->ID = 1;
		
		\Brain\Monkey\Functions\expect('get_post')->andReturn( $wp_post );
		\Brain\Monkey\Functions\expect('get_permalink')->andReturn( 'https://example.com/page.html' );

		$category = \Mockery::mock( \WP_Term::class );
		$category->term_id = 1;
		$category->name = 'The category name';
		$category->description = 'Very extensive and detailed description about this category. It explains what the reader can find here, why this exists, and what may appear here in the future.';
		
		\Brain\Monkey\Functions\expect( 'wp_get_post_categories' )->andReturn( [ $category ] );
		
		\Brain\Monkey\Functions\when('wp_trim_excerpt')->returnArg();
		\Brain\Monkey\Functions\when('wp_hash')->alias('str_rot13');
		
		\Brain\Monkey\Functions\expect('get_bloginfo')->with('language')->andReturn('en-US');

		$context = $this->getContext();
		$context->indexable->schema_article_type = 'BlogPosting';
		$context->canonical = 'https://example.com/';

		$this->options_helper->expects('get')->with('company_or_person', false)->andReturns('person');
		$this->options_helper->expects('get')->with('company_or_person_user_id', false)->andReturns(1);
		
		$user = \Mockery::mock( \WP_User::class );
		$user->user_login = 'info@example.com';
		\Brain\Monkey\Functions\expect('get_user_by')->with('id', 1)->andReturn( $user );
		\Brain\Monkey\Functions\expect('get_userdata')->with('id', 1)->andReturn( $user );

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
