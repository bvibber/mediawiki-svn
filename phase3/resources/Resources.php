<?php

ResourceLoader::register( array(

	/* Special resources who have their own classes */
	'sitejs' => array( 'class' =>  'ResourceLoaderSiteJSModule' ),
	
	/* jQuery */
	
	'jquery' => array( 'scripts' => 'resources/jquery/jquery.js' ),
	
	/* jQuery Plugins */
	
	'jquery.tabIndex' => array( 'scripts' => 'resources/jquery/jquery.tabIndex.js' ),
	'jquery.cookie' => array( 'scripts' => 'resources/jquery/jquery.cookie.js' ),
	
	/* jQuery UI */
	
	// Core
	'jquery.ui.core' => array(
		'scripts' => 'resources/jquery/ui/jquery.ui.core.js',
		'skinStyles' => array(
			'default' => array(
				'resources/jquery/ui/themes/default/jquery.ui.core.css',
				'resources/jquery/ui/themes/default/jquery.ui.theme.css',
			),
			'vector' => array(
				'resources/jquery/ui/themes/vector/jquery.ui.core.css',
				'resources/jquery/ui/themes/vector/jquery.ui.theme.css',
			),
		),
		'dependencies' => 'jquery',
	),
	'jquery.ui.widget' => array(
		'scripts' => 'resources/jquery/ui/jquery.ui.widget.js',
		'dependencies' => 'jquery.ui.core',
	),
	'jquery.ui.mouse' => array(
		'scripts' => 'resources/jquery/ui/jquery.ui.mouse.js',
		'dependencies' => 'jquery',
	),
	'jquery.ui.position' => array(
		'scripts' => 'resources/jquery/ui/jquery.ui.position.js',
		'dependencies' => 'jquery',
	),
	// Interactions
	'jquery.ui.draggable' => array(
		'scripts' => 'resources/jquery/ui/jquery.ui.draggable.js',
		'dependencies' => 'jquery.ui.core',
	),
	'jquery.ui.droppable' => array(
		'scripts' => 'resources/jquery/ui/jquery.ui.droppable.js',
		'dependencies' => array( 'jquery.ui.core', 'jquery.ui.draggable' ),
	),
	'jquery.ui.resizable' => array(
		'scripts' => 'resources/jquery/ui/jquery.ui.resizable.js',
		'skinStyles' => array(
			'default' => 'resources/jquery/ui/themes/default/jquery.ui.resizable.css',
			'vector' => 'resources/jquery/ui/themes/vector/jquery.ui.resizable.css',
		),
		'dependencies' => 'jquery.ui.core',
	),
	'jquery.ui.selectable' => array(
		'scripts' => 'resources/jquery/ui/jquery.ui.selectable.js',
		'skinStyles' => array(
			'default' => 'resources/jquery/ui/themes/default/jquery.ui.selectable.css',
			'vector' => 'resources/jquery/ui/themes/vector/jquery.ui.selectable.css',
		),
		'dependencies' => 'jquery.ui.core',
	),
	'jquery.ui.sortable' => array(
		'scripts' => 'resources/jquery/ui/jquery.ui.sortable.js',
		'dependencies' => 'jquery.ui.core',
	),
	// Widgets
	'jquery.ui.accordion' => array(
		'scripts' => 'resources/jquery/ui/jquery.ui.accordion.js',
		'dependencies' => 'jquery.ui.core',
		'skinStyles' => array(
			'default' => 'resources/jquery/ui/themes/default/jquery.ui.accordion.css',
			'vector' => 'resources/jquery/ui/themes/vector/jquery.ui.accordion.css',
		),
	),
	'jquery.ui.autocomplete' => array(
		'scripts' => 'resources/jquery/ui/jquery.ui.autocomplete.js',
		'dependencies' => array( 'jquery.ui.core', 'jquery.ui.widget', 'jquery.ui.position' ),
		'skinStyles' => array(
			'default' => 'resources/jquery/ui/themes/default/jquery.ui.autocomplete.css',
			'vector' => 'resources/jquery/ui/themes/vector/jquery.ui.autocomplete.css',
		),
	),
	'jquery.ui.button' => array(
		'scripts' => 'resources/jquery/ui/jquery.ui.button.js',
		'dependencies' => array( 'jquery.ui.core', 'jquery.ui.widget' ),
		'skinStyles' => array(
			'default' => 'resources/jquery/ui/themes/default/jquery.ui.button.css',
			'vector' => 'resources/jquery/ui/themes/vector/jquery.ui.button.css',
		),
	),
	'jquery.ui.datepicker' => array(
		'scripts' => 'resources/jquery/ui/jquery.ui.datepicker.js',
		'dependencies' => 'jquery.ui.core',
		'skinStyles' => array(
			'default' => 'resources/jquery/ui/themes/default/jquery.ui.datepicker.css',
			'vector' => 'resources/jquery/ui/themes/vector/jquery.ui.datepicker.css',
		),
		'languageScripts' => array(
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
		'scripts' => 'resources/jquery/ui/jquery.ui.dialog.js',
		'dependencies' => 'jquery.ui.core',
		'skinStyles' => array(
			'default' => 'resources/jquery/ui/themes/default/jquery.ui.dialog.css',
			'vector' => 'resources/jquery/ui/themes/vector/jquery.ui.dialog.css',
		),
	),
	'jquery.ui.progressbar' => array(
		'scripts' => 'resources/jquery/ui/jquery.ui.progressbar.js',
		'dependencies' => 'jquery.ui.core',
		'skinStyles' => array(
			'default' => 'resources/jquery/ui/themes/default/jquery.ui.progressbar.css',
			'vector' => 'resources/jquery/ui/themes/vector/jquery.ui.progressbar.css',
		),
	),
	'jquery.ui.slider' => array(
		'scripts' => 'resources/jquery/ui/jquery.ui.slider.js',
		'dependencies' => array( 'jquery.ui.core', 'jquery.ui.widget', 'jquery.ui.mouse' ),
		'skinStyles' => array(
			'default' => 'resources/jquery/ui/themes/default/jquery.ui.slider.css',
			'vector' => 'resources/jquery/ui/themes/vector/jquery.ui.slider.css',
		),
	),
	'jquery.ui.tabs' => array(
		'scripts' => 'resources/jquery/ui/jquery.ui.tabs.js',
		'dependencies' => 'jquery.ui.core',
		'skinStyles' => array(
			'default' => 'resources/jquery/ui/themes/default/jquery.ui.tabs.css',
			'vector' => 'resources/jquery/ui/themes/vector/jquery.ui.tabs.css',
		),
	),
	// Effects
	'jquery.effects.core' => array(
		'scripts' => 'resources/jquery/effects/jquery.effects.core.js',
		'dependencies' => 'jquery',
	),
	'jquery.effects.blind' => array(
		'scripts' => 'resources/jquery/effects/jquery.effects.blind.js',
		'dependencies' => 'jquery.effects.core',
	),
	'jquery.effects.bounce' => array(
		'scripts' => 'resources/jquery/effects/jquery.effects.bounce.js',
		'dependencies' => 'jquery.effects.core',
	),
	'jquery.effects.clip' => array(
		'scripts' => 'resources/jquery/effects/jquery.effects.clip.js',
		'dependencies' => 'jquery.effects.core',
	),
	'jquery.effects.drop' => array(
		'scripts' => 'resources/jquery/effects/jquery.effects.drop.js',
		'dependencies' => 'jquery.effects.core',
	),
	'jquery.effects.explode' => array(
		'scripts' => 'resources/jquery/effects/jquery.effects.explode.js',
		'dependencies' => 'jquery.effects.core',
	),
	'jquery.effects.fold' => array(
		'scripts' => 'resources/jquery/effects/jquery.effects.fold.js',
		'dependencies' => 'jquery.effects.core',
	),
	'jquery.effects.highlight' => array(
		'scripts' => 'resources/jquery/effects/jquery.effects.highlight.js',
		'dependencies' => 'jquery.effects.core',
	),
	'jquery.effects.pulsate' => array(
		'scripts' => 'resources/jquery/effects/jquery.effects.pulsate.js',
		'dependencies' => 'jquery.effects.core',
	),
	'jquery.effects.scale' => array(
		'scripts' => 'resources/jquery/effects/jquery.effects.scale.js',
		'dependencies' => 'jquery.effects.core',
	),
	'jquery.effects.shake' => array(
		'scripts' => 'resources/jquery/effects/jquery.effects.shake.js',
		'dependencies' => 'jquery.effects.core',
	),
	'jquery.effects.slide' => array(
		'scripts' => 'resources/jquery/effects/jquery.effects.slide.js',
		'dependencies' => 'jquery.effects.core',
	),
	'jquery.effects.transfer' => array(
		'scripts' => 'resources/jquery/effects/jquery.effects.transfer.js',
		'dependencies' => 'jquery.effects.core',
	),
	
	/* MediaWiki */
	
	'mediawiki' => array(
		'scripts' => 'resources/mediawiki/mediawiki.js',
		'debugScripts' => 'resources/mediawiki/mediawiki.log.js',
	),
	
	/* MediaWiki Legacy */
	
	'mediawiki.legacy.ajax' => array(
		'scripts' => 'resources/mediawiki/legacy/mediawiki.legacy.ajax.js',
		'dependencies' => 'mediawiki',
	),
	'mediawiki.legacy.ajaxwatch' => array(
		'scripts' => 'resources/mediawiki/legacy/mediawiki.legacy.ajaxwatch.js',
		'dependencies' => 'mediawiki',
	),
	'mediawiki.legacy.block' => array(
		'scripts' => 'resources/mediawiki/legacy/mediawiki.legacy.block.js',
		'dependencies' => 'mediawiki',
	),
	'mediawiki.legacy.changepassword' => array(
		'scripts' => 'resources/mediawiki/legacy/mediawiki.legacy.changepassword.js',
		'dependencies' => 'mediawiki',
	),
	'mediawiki.legacy.edit' => array(
		'scripts' => 'resources/mediawiki/legacy/mediawiki.legacy.edit.js',
		'dependencies' => 'mediawiki',
	),
	'mediawiki.legacy.enhancedchanges' => array(
		'scripts' => 'resources/mediawiki/legacy/mediawiki.legacy.enhancedchanges.js',
		'dependencies' => 'mediawiki',
	),
	'mediawiki.legacy.history' => array(
		'scripts' => 'resources/mediawiki/legacy/mediawiki.legacy.history.js',
		'dependencies' => 'mediawiki',
	),
	'mediawiki.legacy.htmlform' => array(
		'scripts' => 'resources/mediawiki/legacy/mediawiki.legacy.htmlform.js',
		'dependencies' => 'mediawiki',
	),
	'mediawiki.legacy.IEFixes' => array(
		'scripts' => 'resources/mediawiki/legacy/mediawiki.legacy.IEFixes.js',
		'dependencies' => 'mediawiki',
	),
	'mediawiki.legacy.metadata' => array(
		'scripts' => 'resources/mediawiki/legacy/mediawiki.legacy.metadata.js',
		'dependencies' => 'mediawiki',
	),
	'mediawiki.legacy.mwsuggest' => array(
		'scripts' => 'resources/mediawiki/legacy/mediawiki.legacy.mwsuggest.js',
		'dependencies' => 'mediawiki',
	),
	'mediawiki.legacy.prefs' => array(
		'scripts' => 'resources/mediawiki/legacy/mediawiki.legacy.prefs.js',
		'dependencies' => 'mediawiki',
	),
	'mediawiki.legacy.preview' => array(
		'scripts' => 'resources/mediawiki/legacy/mediawiki.legacy.preview.js',
		'dependencies' => 'mediawiki',
	),
	'mediawiki.legacy.protect' => array(
		'scripts' => 'resources/mediawiki/legacy/mediawiki.legacy.protect.js',
		'dependencies' => 'mediawiki',
	),
	'mediawiki.legacy.rightclickedit' => array(
		'scripts' => 'resources/mediawiki/legacy/mediawiki.legacy.rightclickedit.js',
		'dependencies' => 'mediawiki',
	),
	'mediawiki.legacy.search' => array(
		'scripts' => 'resources/mediawiki/legacy/mediawiki.legacy.search.js',
		'dependencies' => 'mediawiki',
	),
	'mediawiki.legacy.upload' => array(
		'scripts' => 'resources/mediawiki/legacy/mediawiki.legacy.upload.js',
		'dependencies' => 'mediawiki',
	),
	'mediawiki.legacy.wikibits' => array(
		'scripts' => 'resources/mediawiki/legacy/mediawiki.legacy.wikibits.js',
		'dependencies' => 'mediawiki',
		'messages' => array( 'showtoc', 'hidetoc' ),
	),
	
	/* MediaWiki Utilities */
	
	'mediawiki.utilities.client' => array(
		'scripts' => 'resources/mediawiki/utilities/mediawiki.utilities.client.js',
	),
	
	/* MediaWiki Views */
	
	'mediawiki.views.diff' => array(
		'scripts' => 'resources/mediawiki/views/mediawiki.views.diff.js',
	),
	'mediawiki.views.install' => array(
		'scripts' => 'resources/mediawiki/views/mediawiki.views.install.js',
	),
	
	/* Test */
	
	'test' => array(
		'scripts' => 'resources/test/test.js',
		'dependencies' => 'foo',
		'styles' => 'resources/test/test.css',
	),
	'foo' => array(
		'scripts' => 'resources/test/foo.js',
		'dependencies' => 'bar',
		'styles' => 'resources/test/foo.css',
		'messages' => array( 'january', 'february', 'march', 'april', 'may', 'june' ),
	),
	'bar' => array(
		'scripts' => 'resources/test/bar.js',
		'dependencies' => 'buz',
		'styles' => 'resources/test/bar.css',
		'messages' => array( 'july', 'august', 'september', 'october', 'november', 'december' ),
	),
	'buz' => array(
		'scripts' => 'resources/test/buz.js',
		'dependencies' => 'baz',
		'styles' => 'resources/test/buz.css',
	),
	'baz' => array(
		'scripts' => 'resources/test/baz.js',
		'styles' => 'resources/test/baz.css',
	),
) );
