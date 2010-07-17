<?php

ResourceLoader::register( array(
	'jquery' => array(
		'script' => 'resources/base/jquery/jquery-1.4.2.js',
		'raw' => true,
	),
	'jquery.tabIndex' => array(
		'script' => 'resources/base/jquery/jquery.tabIndex.js',
		'raw' => true,
	),
	'mw' => array(
		'script' => 'resources/base/mw/mw.js',
		'raw' => true,
	),
	'test' => array(
		'script' => 'resources/test/test.js',
		'loader' => 'resources/test/loader.js',
		'style' => 'resources/test/test.css',
	),
	'foo' => array(
		'script' => 'resources/test/foo.js',
		'loader' => 'resources/test/loader.js',
		'style' => 'resources/test/foo.css',
	),
	'bar' => array(
		'script' => 'resources/test/bar.js',
		'loader' => 'resources/test/loader.js',
		'style' => 'resources/test/bar.css',
	),
	'buz' => array(
		'script' => 'resources/test/buz.js',
		'loader' => 'resources/test/loader.js',
		'style' => 'resources/test/buz.css',
	),
	'baz' => array(
		'script' => 'resources/test/baz.js',
		'loader' => 'resources/test/loader.js',
		'style' => 'resources/test/baz.css',
	),
	'wikibits' => array(
		'script' => 'skins/common/wikibits.js',
		'loader' => 'skins/common/loader.js',
	),
) );