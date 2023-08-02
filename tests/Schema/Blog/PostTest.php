<?php

namespace DC23\Tests\Schema\Blog;

use Yoast\WP\SEO\Context\Meta_Tags_Context;

class BlogTest extends \PHPUnit\Framework\TestCase {

	public function setUp(): void {
		parent::setUp();
		\Brain\Monkey\setUp();
	}

	private function getContext(): Meta_Tags_Context {
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
							new \Yoast\WP\SEO\Helpers\Environment_Helper()
							new \Yoast\WP\SEO\Helpers\Indexing_Helper(
								new \Yoast\WP\SEO\Helpers\Options_Helper(),
								new \Yoast\WP\SEO\Helpers\Date_Helper(),
								new \Yoast_Notification_Center()
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
					new \Yoast_Notification_Center()
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
							new \Yoast_Notification_Center()
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
		( new \DC23\Schema\Blog\Post() )->register();

		self::assertTrue( has_filter( 'wpseo_schema_graph_pieces' ) );
	}

	public function testRunningTheFilterRequiredSinglePage(): void {
		\Brain\Monkey\Functions\expect('is_single')
			->once()
			->andReturnFalse();

		( new \DC23\Schema\Blog\Post() )->register();

		$context = $this->getContext();
		$filter_result = apply_filter( 'wpseo_schema_graph_pieces', [], $context );

		self::assertSame( [], $filter_result );
	}

	public function testRunningTheFilterRequiredPostTypePost(): void {
		\Brain\Monkey\Functions\expect('is_single')
			->once()
			->andReturnTrue();

		\Brain\Monkey\Functions\expect('get_post_type')
			->once()
			->andReturn('page');

		( new \DC23\Schema\Blog\Post() )->register();

		$context = $this->getContext();
		$filter_result = apply_filter( 'wpseo_schema_graph_pieces', [], $context );

		self::assertSame( [], $filter_result );
	}

	public function testRunningTheFilterRequiredSchemaArticleTypeBlogPosting(): void {
		\Brain\Monkey\Functions\expect('is_single')
			->once()
			->andReturnTrue();

		\Brain\Monkey\Functions\expect('get_post_type')
			->once()
			->andReturn('post');

		( new \DC23\Schema\Blog\Post() )->register();

		$context = $this->getContext();
		// @TODO. $context->indexable might need to be assigned a \Yoast\WP\SEO\Models\Indexable.
		$context->indexable->schema_article_type = 'Article';
		$filter_result = apply_filter( 'wpseo_schema_graph_pieces', [], $context );

		self::assertSame( [], $filter_result );
	}

	public function testRunningTheFilteAddsBlogSchema(): void {
		\Brain\Monkey\Functions\expect('is_single')
			->once()
			->andReturnTrue();

		\Brain\Monkey\Functions\expect('get_post_type')
			->once()
			->andReturn('post');

		( new \DC23\Schema\Blog\Post() )->register();

		$context = $this->getContext();
		// @TODO. $context->indexable might need to be assigned a \Yoast\WP\SEO\Models\Indexable.
		$context->indexable->schema_article_type = 'BlogPosting';
		// @TODO. Expect many other errors: YoastSEO(), get_post(), get_permalink(), wp_get_post_categories(), wp_trim_excerpt(), get_bloginfo()
		$filter_result = apply_filter( 'wpseo_schema_graph_pieces', [], $context );

		self::assertSame( [], $filter_result );
	}

}
