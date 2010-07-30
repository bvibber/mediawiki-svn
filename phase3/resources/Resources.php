<?php

ResourceLoader::register( array(
	'jquery' => array(
		'script' => 'resources/jquery/jquery-1.4.2.js',
		'raw' => true,
	),
	'jquery.tabIndex' => array(
		'script' => 'resources/jquery/jquery.tabIndex.js',
		'raw' => true,
	),
	'mw' => array(
		'script' => 'resources/mw/mw.js',
		'raw' => true,
	),
	'mw.legacy.ajax' => array(
		'script' => 'resources/mw/legacy/mw.legacy.ajax.js',
	),
	'mw.legacy.ajaxwatch' => array(
		'script' => 'resources/mw/legacy/mw.legacy.ajaxwatch.js',
	),
	'mw.legacy.block' => array(
		'script' => 'resources/mw/legacy/mw.legacy.block.js',
	),
	'mw.legacy.changepassword' => array(
		'script' => 'resources/mw/legacy/mw.legacy.changepassword.js',
	),
	'mw.legacy.edit' => array(
		'script' => 'resources/mw/legacy/mw.legacy.edit.js',
	),
	'mw.legacy.enhancedchanges' => array(
		'script' => 'resources/mw/legacy/mw.legacy.enhancedchanges.js',
	),
	'mw.legacy.history' => array(
		'script' => 'resources/mw/legacy/mw.legacy.history.js',
	),
	'mw.legacy.htmlform' => array(
		'script' => 'resources/mw/legacy/mw.legacy.htmlform.js',
	),
	'mw.legacy.IEFixes' => array(
		'script' => 'resources/mw/legacy/mw.legacy.IEFixes.js',
	),
	'mw.legacy.metadata' => array(
		'script' => 'resources/mw/legacy/mw.legacy.metadata.js',
	),
	'mw.legacy.mwsuggest' => array(
		'script' => 'resources/mw/legacy/mw.legacy.mwsuggest.js',
	),
	'mw.legacy.prefs' => array(
		'script' => 'resources/mw/legacy/mw.legacy.prefs.js',
	),
	'mw.legacy.preview' => array(
		'script' => 'resources/mw/legacy/mw.legacy.preview.js',
	),
	'mw.legacy.protect' => array(
		'script' => 'resources/mw/legacy/mw.legacy.protect.js',
	),
	'mw.legacy.rightclickedit' => array(
		'script' => 'resources/mw/legacy/mw.legacy.rightclickedit.js',
	),
	'mw.legacy.search' => array(
		'script' => 'resources/mw/legacy/mw.legacy.search.js',
	),
	'mw.legacy.upload' => array(
		'script' => 'resources/mw/legacy/mw.legacy.upload.js',
	),
	'mw.legacy.wikibits' => array(
		'script' => 'resources/mw/legacy/mw.legacy.wikibits.js',
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
		'messages' => array( 'january', 'february', 'march', 'april', 'may', 'june' ),
	),
	'bar' => array(
		'script' => 'resources/test/bar.js',
		'loader' => 'resources/test/loader.js',
		'style' => 'resources/test/bar.css',
		'messages' => array( 'july', 'august', 'september', 'october', 'november', 'december' ),
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
) );