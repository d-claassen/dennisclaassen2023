<?php

use DC23\Schema\Blog\Category;
use PHPUnit\Framework\TestCase;

class CategoryTest extends TestCase {

	public function setUp(): void {
		parent::setUp();
		\Brain\Monkey\setUp();
	}

	public function tearDown(): void {
		\Brain\Monkey\tearDown();
		parent::tearDown();
	}
	public function testRegistersFilters(): void {
		$category = new Category();
		$category->register();

		self::assertSame(11, has_filter( 'wpseo_schema_webpage', [ $category, 'make_blog_main_entity' ] ) );
		self::assertSame(11, has_filter( 'wpseo_schema_graph_pieces', [ $category, 'add_blog_to_schema' ] ) );
	}

	public function testOnlyChangeWebPageMainEntityOnCategoryPages(): void {
		\Brain\Monkey\Functions\when('is_category')->justReturn(false);

		$webpage_original = [];
		$context = null;
		
		$category = new Category();
		$webpage_result = $category->make_blog_main_entity( $webpage_original, $context );

		self::assertSame( [], $webpage_result );
	}
}
