<?php declare(strict_types=1);

use function \Yoast\WPTestUtils\WPIntegration\get_path_to_wp_test_dir;

// require_once __DIR__ . '/../vendor/autoload.php';
// require_once __DIR__ . '/../vendor/yoast/wordpress-seo/src/functions.php';
/* *****[ Wire in the integration ]***** */

$_tests_dir = get_path_to_wp_test_dir();

// Give access to tests_add_filter() function.
require_once $_tests_dir . 'includes/functions.php';

function load_plugins() {
	require_once __DIR__ . '/../vendor/yoast/wordpress-seo/wp-seo.php';
}

// Add plugin to active mu-plugins - to make sure it gets loaded.
tests_add_filter( 'muplugins_loaded', load_plugins(...) );

register_theme_directory( __DIR__ .'/../../' );
switch_theme( 'dennisclaassen2023' );
