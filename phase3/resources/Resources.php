<?php

ResourceLoader::register( array(
	'jquery' => array(
		'script' => 'resources/base/jquery-1.4.2.js',
		'base' => true,
	),
	'mw' => array(
		'script' => 'resources/base/mw.js',
		'base' => true,
	),
	'mw.config' => array(
		'script' => 'resources/base/mw/mw.config.js',
		'base' => true,
	),
	'mw.loader' => array(
		'script' => 'resources/base/mw/mw.loader.js',
		'base' => true,
	),
	'mw.msg' => array(
		'script' => 'resources/base/mw/mw.msg.js',
		'base' => true,
	),
	'mw.util' => array(
		'script' => 'resources/base/mw/mw.util.js',
		'base' => true,
	),
	'mw.debug' => array(
		'script' => 'resources/base/mw/mw.debug.js',
		'base' => true,
		'debug' => true
	),
	'mw.log' => array(
		'script' => 'resources/base/mw/mw.log.js',
		'base' => true,
		'debug' => true
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