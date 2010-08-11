<?php

ResourceLoader::register( array(
	
	/* jQuery */
	
	'jquery' => array( 'script' => 'resources/jquery/jquery.js', 'raw' => true ),
	
	/* jQuery Plugins */
	
	'jquery.tabIndex' => array( 'script' => 'resources/jquery/jquery.tabIndex.js' ),
	'jquery.cookie' => array( 'script' => 'resources/jquery/jquery.cookie.js' ),
	
	/* jQuery UI */
	
	// Core
	'jquery.ui.core' => array(
		'script' => 'resources/jquery/ui/jquery.ui.core.js',
		'themes' => array(
			'default' => array(
				'resources/jquery/ui/themes/default/jquery.ui.core.css',
				'resources/jquery/ui/themes/default/jquery.ui.theme.css',
			),
			'vector' => array(
				'resources/jquery/ui/themes/vector/jquery.ui.core.css',
				'resources/jquery/ui/themes/vector/jquery.ui.theme.css',
			),
		),
		'needs' => 'jquery',
	),
	'jquery.ui.widget' => array(
		'script' => 'resources/jquery/ui/jquery.ui.widget.js',
		'needs' => 'jquery.ui.core',
	),
	'jquery.ui.mouse' => array(
		'script' => 'resources/jquery/ui/jquery.ui.mouse.js',
		'needs' => 'jquery',
	),
	'jquery.ui.position' => array(
		'script' => 'resources/jquery/ui/jquery.ui.position.js',
		'needs' => 'jquery',
	),
	// Interactions
	'jquery.ui.draggable' => array(
		'script' => 'resources/jquery/ui/jquery.ui.draggable.js',
		'needs' => 'jquery.ui.core',
	),
	'jquery.ui.droppable' => array(
		'script' => 'resources/jquery/ui/jquery.ui.droppable.js',
		'needs' => array( 'jquery.ui.core', 'jquery.ui.draggable' ),
	),
	'jquery.ui.resizable' => array(
		'script' => 'resources/jquery/ui/jquery.ui.resizable.js',
		'themes' => array(
			'default' => 'resources/jquery/ui/themes/default/jquery.ui.resizable.css',
			'vector' => 'resources/jquery/ui/themes/vector/jquery.ui.resizable.css',
		),
		'needs' => 'jquery.ui.core',
	),
	'jquery.ui.selectable' => array(
		'script' => 'resources/jquery/ui/jquery.ui.selectable.js',
		'themes' => array(
			'default' => 'resources/jquery/ui/themes/default/jquery.ui.selectable.css',
			'vector' => 'resources/jquery/ui/themes/vector/jquery.ui.selectable.css',
		),
		'needs' => 'jquery.ui.core',
	),
	'jquery.ui.sortable' => array(
		'script' => 'resources/jquery/ui/jquery.ui.sortable.js',
		'needs' => 'jquery.ui.core',
	),
	// Widgets
	'jquery.ui.accordion' => array(
		'script' => 'resources/jquery/ui/jquery.ui.accordion.js',
		'needs' => 'jquery.ui.core',
		'themes' => array(
			'default' => 'resources/jquery/ui/themes/default/jquery.ui.accordion.css',
			'vector' => 'resources/jquery/ui/themes/vector/jquery.ui.accordion.css',
		),
	),
	'jquery.ui.autocomplete' => array(
		'script' => 'resources/jquery/ui/jquery.ui.autocomplete.js',
		'needs' => array( 'jquery.ui.core', 'jquery.ui.widget', 'jquery.ui.position' ),
		'themes' => array(
			'default' => 'resources/jquery/ui/themes/default/jquery.ui.autocomplete.css',
			'vector' => 'resources/jquery/ui/themes/vector/jquery.ui.autocomplete.css',
		),
	),
	'jquery.ui.button' => array(
		'script' => 'resources/jquery/ui/jquery.ui.button.js',
		'needs' => array( 'jquery.ui.core', 'jquery.ui.widget' ),
		'themes' => array(
			'default' => 'resources/jquery/ui/themes/default/jquery.ui.button.css',
			'vector' => 'resources/jquery/ui/themes/vector/jquery.ui.button.css',
		),
	),
	'jquery.ui.datepicker' => array(
		'script' => 'resources/jquery/ui/jquery.ui.datepicker.js',
		'needs' => 'jquery.ui.core',
		'themes' => array(
			'default' => 'resources/jquery/ui/themes/default/jquery.ui.datepicker.css',
			'vector' => 'resources/jquery/ui/themes/vector/jquery.ui.datepicker.css',
		),
		'locales' => array(
			'af' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-af.js',
			'ar' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-ar.js',
			'az' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-az.js',
			'bg' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-bg.js',
			'bs' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-bs.js',
			'ca' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-ca.js',
			'cs' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-cs.js',
			'da' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-da.js',
			'de' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-de.js',
			'el' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-el.js',
			'en-gb' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-en-GB.js',
			'eo' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-eo.js',
			'es' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-es.js',
			'et' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-et.js',
			'eu' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-eu.js',
			'fa' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-fa.js',
			'fi' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-fi.js',
			'fo' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-fo.js',
			'fr-ch' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-fr-CH.js',
			'fr' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-fr.js',
			'he' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-he.js',
			'hr' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-hr.js',
			'hu' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-hu.js',
			'hy' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-hy.js',
			'id' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-id.js',
			'is' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-is.js',
			'it' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-it.js',
			'ja' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-ja.js',
			'ko' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-ko.js',
			'lt' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-lt.js',
			'lv' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-lv.js',
			'ms' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-ms.js',
			'nl' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-nl.js',
			'no' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-no.js',
			'pl' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-pl.js',
			'pt-br' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-pt-BR.js',
			'ro' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-ro.js',
			'ru' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-ru.js',
			'sk' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-sk.js',
			'sl' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-sl.js',
			'sq' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-sq.js',
			'sr-sr' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-sr-SR.js',
			'sr' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-sr.js',
			'sv' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-sv.js',
			'ta' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-ta.js',
			'th' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-th.js',
			'tr' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-tr.js',
			'uk' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-uk.js',
			'vi' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-vi.js',
			'zh-cn' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-zh-CN.js',
			'zh-hk' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-zh-HK.js',
			'zh-tw' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-zh-TW.js'
		),
	),
	'jquery.ui.dialog' => array(
		'script' => 'resources/jquery/ui/jquery.ui.dialog.js',
		'needs' => 'jquery.ui.core',
		'themes' => array(
			'default' => 'resources/jquery/ui/themes/default/jquery.ui.dialog.css',
			'vector' => 'resources/jquery/ui/themes/vector/jquery.ui.dialog.css',
		),
	),
	'jquery.ui.progressbar' => array(
		'script' => 'resources/jquery/ui/jquery.ui.progressbar.js',
		'needs' => 'jquery.ui.core',
		'themes' => array(
			'default' => 'resources/jquery/ui/themes/default/jquery.ui.progressbar.css',
			'vector' => 'resources/jquery/ui/themes/vector/jquery.ui.progressbar.css',
		),
	),
	'jquery.ui.slider' => array(
		'script' => 'resources/jquery/ui/jquery.ui.slider.js',
		'needs' => array( 'jquery.ui.core', 'jquery.ui.widget', 'jquery.ui.mouse' ),
		'themes' => array(
			'default' => 'resources/jquery/ui/themes/default/jquery.ui.slider.css',
			'vector' => 'resources/jquery/ui/themes/vector/jquery.ui.slider.css',
		),
	),
	'jquery.ui.tabs' => array(
		'script' => 'resources/jquery/ui/jquery.ui.tabs.js',
		'needs' => 'jquery.ui.core',
		'themes' => array(
			'default' => 'resources/jquery/ui/themes/default/jquery.ui.tabs.css',
			'vector' => 'resources/jquery/ui/themes/vector/jquery.ui.tabs.css',
		),
	),
	// Effects
	'jquery.effects.core' => array(
		'script' => 'resources/jquery/effects/jquery.effects.core.js',
		'needs' => 'jquery',
	),
	'jquery.effects.blind' => array(
		'script' => 'resources/jquery/effects/jquery.effects.blind.js',
		'needs' => 'jquery.effects.core',
	),
	'jquery.effects.bounce' => array(
		'script' => 'resources/jquery/effects/jquery.effects.bounce.js',
		'needs' => 'jquery.effects.core',
	),
	'jquery.effects.clip' => array(
		'script' => 'resources/jquery/effects/jquery.effects.clip.js',
		'needs' => 'jquery.effects.core',
	),
	'jquery.effects.drop' => array(
		'script' => 'resources/jquery/effects/jquery.effects.drop.js',
		'needs' => 'jquery.effects.core',
	),
	'jquery.effects.explode' => array(
		'script' => 'resources/jquery/effects/jquery.effects.explode.js',
		'needs' => 'jquery.effects.core',
	),
	'jquery.effects.fold' => array(
		'script' => 'resources/jquery/effects/jquery.effects.fold.js',
		'needs' => 'jquery.effects.core',
	),
	'jquery.effects.highlight' => array(
		'script' => 'resources/jquery/effects/jquery.effects.highlight.js',
		'needs' => 'jquery.effects.core',
	),
	'jquery.effects.pulsate' => array(
		'script' => 'resources/jquery/effects/jquery.effects.pulsate.js',
		'needs' => 'jquery.effects.core',
	),
	'jquery.effects.scale' => array(
		'script' => 'resources/jquery/effects/jquery.effects.scale.js',
		'needs' => 'jquery.effects.core',
	),
	'jquery.effects.shake' => array(
		'script' => 'resources/jquery/effects/jquery.effects.shake.js',
		'needs' => 'jquery.effects.core',
	),
	'jquery.effects.slide' => array(
		'script' => 'resources/jquery/effects/jquery.effects.slide.js',
		'needs' => 'jquery.effects.core',
	),
	'jquery.effects.transfer' => array(
		'script' => 'resources/jquery/effects/jquery.effects.transfer.js',
		'needs' => 'jquery.effects.core',
	),
	
	/* MediaWiki */
	
	'mediawiki' => array(
		'script' => 'resources/mediawiki/mediawiki.js',
		'debug' => 'resources/mediawiki/mediawiki.log.js',
		'raw' => true,
	),
	
	/* MediaWiki Legacy */
	
	'mediawiki.legacy.ajax' => array(
		'script' => 'resources/mediawiki/legacy/mediawiki.legacy.ajax.js',
		'needs' => 'mediawiki',
	),
	'mediawiki.legacy.ajaxwatch' => array(
		'script' => 'resources/mediawiki/legacy/mediawiki.legacy.ajaxwatch.js',
		'needs' => 'mediawiki',
	),
	'mediawiki.legacy.block' => array(
		'script' => 'resources/mediawiki/legacy/mediawiki.legacy.block.js',
		'needs' => 'mediawiki',
	),
	'mediawiki.legacy.changepassword' => array(
		'script' => 'resources/mediawiki/legacy/mediawiki.legacy.changepassword.js',
		'needs' => 'mediawiki',
	),
	'mediawiki.legacy.edit' => array(
		'script' => 'resources/mediawiki/legacy/mediawiki.legacy.edit.js',
		'needs' => 'mediawiki',
	),
	'mediawiki.legacy.enhancedchanges' => array(
		'script' => 'resources/mediawiki/legacy/mediawiki.legacy.enhancedchanges.js',
		'needs' => 'mediawiki',
	),
	'mediawiki.legacy.history' => array(
		'script' => 'resources/mediawiki/legacy/mediawiki.legacy.history.js',
		'needs' => 'mediawiki',
	),
	'mediawiki.legacy.htmlform' => array(
		'script' => 'resources/mediawiki/legacy/mediawiki.legacy.htmlform.js',
		'needs' => 'mediawiki',
	),
	'mediawiki.legacy.IEFixes' => array(
		'script' => 'resources/mediawiki/legacy/mediawiki.legacy.IEFixes.js',
		'needs' => 'mediawiki',
	),
	'mediawiki.legacy.metadata' => array(
		'script' => 'resources/mediawiki/legacy/mediawiki.legacy.metadata.js',
		'needs' => 'mediawiki',
	),
	'mediawiki.legacy.mwsuggest' => array(
		'script' => 'resources/mediawiki/legacy/mediawiki.legacy.mwsuggest.js',
		'needs' => 'mediawiki',
	),
	'mediawiki.legacy.prefs' => array(
		'script' => 'resources/mediawiki/legacy/mediawiki.legacy.prefs.js',
		'needs' => 'mediawiki',
	),
	'mediawiki.legacy.preview' => array(
		'script' => 'resources/mediawiki/legacy/mediawiki.legacy.preview.js',
		'needs' => 'mediawiki',
	),
	'mediawiki.legacy.protect' => array(
		'script' => 'resources/mediawiki/legacy/mediawiki.legacy.protect.js',
		'needs' => 'mediawiki',
	),
	'mediawiki.legacy.rightclickedit' => array(
		'script' => 'resources/mediawiki/legacy/mediawiki.legacy.rightclickedit.js',
		'needs' => 'mediawiki',
	),
	'mediawiki.legacy.search' => array(
		'script' => 'resources/mediawiki/legacy/mediawiki.legacy.search.js',
		'needs' => 'mediawiki',
	),
	'mediawiki.legacy.upload' => array(
		'script' => 'resources/mediawiki/legacy/mediawiki.legacy.upload.js',
		'needs' => 'mediawiki',
	),
	'mediawiki.legacy.wikibits' => array(
		'script' => 'resources/mediawiki/legacy/mediawiki.legacy.wikibits.js',
		'needs' => 'mediawiki',
		'messages' => array( 'showtoc', 'hidetoc' ),
	),
	
	/* MediaWiki Utilities */
	
	'mediawiki.utilities.client' => array(
		'script' => 'resources/mediawiki/utilities/mediawiki.utilities.client.js',
	),
	
	/* MediaWiki Views */
	
	'mediawiki.views.diff' => array(
		'script' => 'resources/mediawiki/views/mediawiki.views.diff.js',
	),
	'mediawiki.views.install' => array(
		'script' => 'resources/mediawiki/views/mediawiki.views.install.js',
	),
	
	/* Test */
	
	'test' => array(
		'script' => 'resources/test/test.js',
		'needs' => 'foo',
		'style' => 'resources/test/test.css',
	),
	'foo' => array(
		'script' => 'resources/test/foo.js',
		'needs' => 'bar',
		'style' => 'resources/test/foo.css',
		'messages' => array( 'january', 'february', 'march', 'april', 'may', 'june' ),
	),
	'bar' => array(
		'script' => 'resources/test/bar.js',
		'needs' => 'buz',
		'style' => 'resources/test/bar.css',
		'messages' => array( 'july', 'august', 'september', 'october', 'november', 'december' ),
	),
	'buz' => array(
		'script' => 'resources/test/buz.js',
		'needs' => 'baz',
		'style' => 'resources/test/buz.css',
	),
	'baz' => array(
		'script' => 'resources/test/baz.js',
		'style' => 'resources/test/baz.css',
	),
) );