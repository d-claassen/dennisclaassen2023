<?php

// namespace DC23\Tests\Schema\Blog;

use Yoast\WP\SEO\Context\Meta_Tags_Context;

class PostTest extends \PHPUnit\Framework\TestCase {

	public function setUp(): void {
		parent::setUp();
		\Brain\Monkey\setUp();
	}

	private function getContext(): Meta_Tags_Context {
		$context = \Mocker::mock( Meta_Tags_Context::class );
		$context->indexable = \Mockery::mock( \Yoast\WP\SEO\Models\Indexable::class );
		return $context;
		
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

		return $context;
	}

	public function tearDown(): void {
		\Brain\Monkey\tearDown();
		parent::tearDown();
	}

	public function testRegistrationAddsFilter(): void {
		// $this->markTestSkipped('skip');
		( new \DC23\Schema\Blog\Post() )->register();

		self::assertTrue( has_filter( 'wpseo_schema_graph_pieces' ) );
	}

	public function testRunningTheFilterRequiredSinglePage(): void {
		// $this->markTestSkipped('skip2');
		\Brain\Monkey\Functions\expect('is_single')
			->zeroOrMoreTimes()
			->andReturnFalse();

		// $this->markTestSkipped('the expect works');
		( new \DC23\Schema\Blog\Post() )->register();

		// $this->markTestSkipped('registered post schema');
		$context = $this->getContext();

		// $this->markTestSkipped('got context');
		$filter_result = apply_filters( 'wpseo_schema_graph_pieces', [], $context );

		// $this->markTestSkipped('apply_filter worked too');
		self::assertSame( [], $filter_result );
	}

	public function testRunningTheFilterRequiredPostTypePost(): void {
		// $this->markTestSkipped('skip3');
		\Brain\Monkey\Functions\expect('is_single')
			->zeroOrMoreTimes()
			->andReturnTrue();

		\Brain\Monkey\Functions\expect('get_post_type')
			->zeroOrMoreTimes()
			->andReturn('page');

		( new \DC23\Schema\Blog\Post() )->register();

		$context = $this->getContext();
		$filter_result = apply_filters( 'wpseo_schema_graph_pieces', [], $context );

		self::assertSame( [], $filter_result );
	}

	public function testRunningTheFilterRequiredSchemaArticleTypeBlogPosting(): void {
		// $this->markTestSkipped('skip4');
		\Brain\Monkey\Functions\expect('is_single')
			->zeroOrMoreTimes()
			->andReturnTrue();

		\Brain\Monkey\Functions\expect('get_post_type')
			->zeroOrMoreTimes()
			->andReturn('post');

		( new \DC23\Schema\Blog\Post() )->register();

		$context = $this->getContext();
		// @TODO. $context->indexable might need to be assigned a \Yoast\WP\SEO\Models\Indexable.
		$context->indexable->schema_article_type = 'Article';
		$filter_result = apply_filters( 'wpseo_schema_graph_pieces', [], $context );

		self::assertSame( [], $filter_result );
	}

	public function testRunningTheFilteAddsBlogSchema(): void {
		// $this->markTestSkipped('skip5');
		\Brain\Monkey\Functions\expect('is_single')
			->zeroOrMoreTimes()
			->andReturnTrue();

		\Brain\Monkey\Functions\expect('get_post_type')
			->zeroOrMoreTimes()
			->andReturn('post');

		( new \DC23\Schema\Blog\Post() )->register();

		$context = $this->getContext();
		// @TODO. $context->indexable might need to be assigned a \Yoast\WP\SEO\Models\Indexable.
		$context->indexable->schema_article_type = 'BlogPosting';
		// @TODO. Expect many other errors: YoastSEO(), get_post(), get_permalink(), wp_get_post_categories(), wp_trim_excerpt(), get_bloginfo()
		$filter_result = apply_filters( 'wpseo_schema_graph_pieces', [], $context );

		self::assertSame( [], $filter_result );
	}

}
