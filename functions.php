<?php

declare( strict_types=1 );

require_once 'vendor/autoload.php';

if ( ! function_exists( 'dc23_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which runs
	 * before the init hook.
	 */
	function dc23_setup(): void {
		register_block_type( __DIR__ . '/build/assets/github-tree/block.json', [ 'render_callback' => \DC23\Blocks\GitHubTree\Tree::render( ... ) ] );
		register_block_type( __DIR__ . '/build/assets/github-breadcrumbs/block.json', [ 'render_callback' => \DC23\Blocks\GitHubBreadcrumbs\Breadcrumbs::render( ... ) ] );

		( new \DC23\Schema\Language\SiteLanguage() )->register();
		( new \DC23\Schema\Profile\Resume() )->register();
		( new \DC23\Schema\Blog\Category() )->register();
		( new \DC23\Schema\Blog\Post() )->register();
	}
endif;
add_action( 'init', 'dc23_setup' );
