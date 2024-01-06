<?php

namespace Tests;

use Brain\Faker\Providers as BrainFaker;
use Faker\Generator as FakerGenerator;
use PHPUnit\Framework\TestCase;

final class AuthorTest extends TestCase {
	use \Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

	protected FakerGenerator $faker;
	protected BrainFaker $wpFaker;

	public function setUp(): void {
		global $wpdb;
		
		parent::setUp();
		\Brain\Monkey\setUp();

		$wpdb = \Mockery::mock( \wpdb::class );

		$this->faker   = \Brain\faker();
		$this->wpFaker = $this->faker->wp();
	}

	public function tearDown(): void {
		global $wpdb;

		$wpdb = null;

		\Brain\fakerReset();
		
		\Brain\Monkey\tearDown();
		parent::tearDown();
	}

	public function testIt(): void {
		\Brain\Monkey\Functions\when('is_author')->justReturn(true);

		$author = $this->wpFaker->user();

		\var_dump($author);

		//update_user_meta( $author->ID, 'gender', 'Male' );
	}
}
