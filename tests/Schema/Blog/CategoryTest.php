<?php

use Brain\Faker\Providers as BrainFaker;
use Faker\Generator as FakerGenerator;
use DC23\Schema\Blog\Category;
use PHPUnit\Framework\TestCase;

class CategoryTest extends TestCase {
	use \Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

	protected FakerGenerator $faker;
	protected BrainFaker $wpFaker;

	public function setUp(): void {
		parent::setUp();
		\Brain\Monkey\setUp();

		$this->faker   = \Brain\faker();
		$this->wpFaker = $this->faker->wp();
	}

	public function tearDown(): void {
		\Brain\fakerReset();
		
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

	public function testOnlyAddsBlogOnCategoryPages(): void {
		\Brain\Monkey\Functions\when('is_category')->justReturn(false);

		$schema_original = [];
		$context = null;
		
		$category = new Category();
		$schema_result = $category->add_blog_to_schema( $schema_original, $context );

		self::assertSame( [], $schema_result );
	}

	public function testMakesBlogMainEntity(): void {
		\Brain\Monkey\Functions\when('is_category')->justReturn(true);

		$wp_term = $this->wpFaker->term(['term_id' => 1, 'taxonomy' => 'category']);

		\Brain\Monkey\Functions\expect('get_query_var')
			->once()
			->with('cat')
			->andReturn($wp_term->term_id);

		$context = new \stdClass();
		$context->site_url = 'https://www.example.com/';

		$category = new Category();
		$webpage_result = $category->make_blog_main_entity( [], $context );

		self::assertArrayHasKey('mainEntity', $webpage_result);
		self::assertSame(
			['@id' => 'https://www.example.com/#/schema/Blog/1'],
			$webpage_result['mainEntity']
		);
	}

	public function testAddsBlogToSchema(): void {
		\Brain\Monkey\Functions\when('is_category')->justReturn(true);
		\Brain\Monkey\Functions\when('site_url')->justReturn('https://www.example.org/');
		\Brain\Monkey\Functions\when('is_admin')->justReturn(false);
		\Brain\Monkey\Functions\when('get_current_blog_id')->justReturn(1);
		\Brain\Monkey\Functions\when('get_option')->alias(function($key, $default=false){
			switch($key){
				default:
				echo $key;
				return $default;
			}
		});

		$wp_term = $this->wpFaker->term(['term_id' => 1, 'taxonomy' => 'category']);

		\Brain\Monkey\Functions\expect('get_query_var')
			->once()
			->with('cat')
			->andReturn($wp_term->term_id);

		$context = new \stdClass();
		$context->site_url = 'https://www.example.com/';

		$category = new Category();
		$schema_result = $category->add_blog_to_schema( [], $context );

		// shoudnt actually be empty, just dor debugging now
		self::assertSame( [], $pieces );

	}
}
