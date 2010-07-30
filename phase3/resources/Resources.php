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
	'mediawiki' => array(
		'script' => 'resources/mediawiki/mediawiki.js',
		'raw' => true,
	),
	'mediawiki.legacy.ajax' => array(
		'script' => 'resources/mediawiki/legacy/mediawiki.legacy.ajax.js',
	),
	'mediawiki.legacy.ajaxwatch' => array(
		'script' => 'resources/mediawiki/legacy/mediawiki.legacy.ajaxwatch.js',
	),
	'mediawiki.legacy.block' => array(
		'script' => 'resources/mediawiki/legacy/mediawiki.legacy.block.js',
	),
	'mediawiki.legacy.changepassword' => array(
		'script' => 'resources/mediawiki/legacy/mediawiki.legacy.changepassword.js',
	),
	'mediawiki.legacy.edit' => array(
		'script' => 'resources/mediawiki/legacy/mediawiki.legacy.edit.js',
	),
	'mediawiki.legacy.enhancedchanges' => array(
		'script' => 'resources/mediawiki/legacy/mediawiki.legacy.enhancedchanges.js',
	),
	'mediawiki.legacy.history' => array(
		'script' => 'resources/mediawiki/legacy/mediawiki.legacy.history.js',
	),
	'mediawiki.legacy.htmlform' => array(
		'script' => 'resources/mediawiki/legacy/mediawiki.legacy.htmlform.js',
	),
	'mediawiki.legacy.IEFixes' => array(
		'script' => 'resources/mediawiki/legacy/mediawiki.legacy.IEFixes.js',
	),
	'mediawiki.legacy.metadata' => array(
		'script' => 'resources/mediawiki/legacy/mediawiki.legacy.metadata.js',
	),
	'mediawiki.legacy.mwsuggest' => array(
		'script' => 'resources/mediawiki/legacy/mediawiki.legacy.mwsuggest.js',
	),
	'mediawiki.legacy.prefs' => array(
		'script' => 'resources/mediawiki/legacy/mediawiki.legacy.prefs.js',
	),
	'mediawiki.legacy.preview' => array(
		'script' => 'resources/mediawiki/legacy/mediawiki.legacy.preview.js',
	),
	'mediawiki.legacy.protect' => array(
		'script' => 'resources/mediawiki/legacy/mediawiki.legacy.protect.js',
	),
	'mediawiki.legacy.rightclickedit' => array(
		'script' => 'resources/mediawiki/legacy/mediawiki.legacy.rightclickedit.js',
	),
	'mediawiki.legacy.search' => array(
		'script' => 'resources/mediawiki/legacy/mediawiki.legacy.search.js',
	),
	'mediawiki.legacy.upload' => array(
		'script' => 'resources/mediawiki/legacy/mediawiki.legacy.upload.js',
	),
	'mediawiki.legacy.wikibits' => array(
		'script' => 'resources/mediawiki/legacy/mediawiki.legacy.wikibits.js',
	),
	
	'mediawiki.utilities.client' => array(
		'script' => 'resources/mediawiki/utilities/mediawiki.utilities.client.js',
	),
	
	'mediawiki.views.diff' => array(
		'script' => 'resources/mediawiki/views/mediawiki.views.diff.js',
	),
	'mediawiki.views.install' => array(
		'script' => 'resources/mediawiki/views/mediawiki.views.install.js',
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