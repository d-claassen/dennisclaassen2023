<?php declare(strict_types=1);

// require_once __DIR__ . '/../vendor/autoload.php';
// require_once __DIR__ . '/../vendor/yoast/wordpress-seo/src/functions.php';

function load_plugins() {
	require_once __DIR__ . '/../vendor/yoast/wordpress-seo/wp-seo.php';
}

// Add plugin to active mu-plugins - to make sure it gets loaded.
tests_add_filter( 'muplugins_loaded', load_plugins(...) );

register_theme_directory( __DIR__ .'/../../' );
switch_theme( 'dennisclaassen2023' );
