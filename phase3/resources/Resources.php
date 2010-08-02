<?php

ResourceLoader::register( array(
	
	/* jQuery */
	
	'jquery' => array( 'script' => 'resources/jquery/jquery.js', 'raw' => true ),
	
	/* jQuery Plugins */
	
	'jquery.tabIndex' => array( 'script' => 'resources/jquery/jquery.tabIndex.js' ),
	'jquery.cookie' => array( 'script' => 'resources/jquery/jquery.cookie.js' ),
	
	/* jQuery UI */
	
	// Components
	'jquery.ui.core' => array(
		'script' => 'resources/jquery/ui/jquery.ui.core.js',
		'style' => 'resources/jquery/ui/themes/base/jquery.ui.theme.css',
		'themes' => array(
			'default' => 'resources/jquery/ui/themes/default/jquery.ui.theme.css',
			'vector' => 'resources/jquery/ui/themes/vector/jquery.ui.theme.css',
			'monobook' => 'resources/jquery/ui/themes/monobook/jquery.ui.theme.css',
		),
		'needs' => array( 'jquery' ),
	),
	'jquery.ui.accordion' => array(
		'script' => 'resources/jquery/ui/jquery.ui.accordion.js',
		'needs' => array( 'jquery.ui.core' ),
	),
	'jquery.ui.autocomplete' => array(
		'script' => 'resources/jquery/ui/jquery.ui.autocomplete.js',
		'needs' => array( 'jquery.ui.core' ),
	),
	'jquery.ui.button' => array(
		'script' => 'resources/jquery/ui/jquery.ui.button.js',
		'needs' => array( 'jquery.ui.core' ),
	),
	'jquery.ui.datepicker' => array(
		'script' => 'resources/jquery/ui/jquery.ui.datepicker.js',
		'needs' => array( 'jquery.ui.core' ),
		'style' => 'resources/jquery/ui/themes/base/jquery.ui.datepicker.css',
		'themes' => array(
			'default' => 'resources/jquery/ui/themes/default/jquery.ui.datepicker.css',
			'vector' => 'resources/jquery/ui/themes/vector/jquery.ui.datepicker.css',
			'monobook' => 'resources/jquery/ui/themes/monobook/jquery.ui.datepicker.css',
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
		'needs' => array( 'jquery.ui.core' ),
	),
	'jquery.ui.draggable' => array(
		'script' => 'resources/jquery/ui/jquery.ui.draggable.js',
		'needs' => array( 'jquery.ui.core' ),
	),
	'jquery.ui.droppable' => array(
		'script' => 'resources/jquery/ui/jquery.ui.droppable.js',
		'needs' => array( 'jquery.ui.core' ),
	),
	'jquery.ui.mouse' => array(
		'script' => 'resources/jquery/ui/jquery.ui.mouse.js',
		'needs' => array( 'jquery.ui.core' ),
	),
	'jquery.ui.position' => array(
		'script' => 'resources/jquery/ui/jquery.ui.position.js',
		'needs' => array( 'jquery.ui.core' ),
	),
	'jquery.ui.progressbar' => array(
		'script' => 'resources/jquery/ui/jquery.ui.progressbar.js',
		'needs' => array( 'jquery.ui.core' ),
	),
	'jquery.ui.resizable' => array(
		'script' => 'resources/jquery/ui/jquery.ui.resizable.js',
		'needs' => array( 'jquery.ui.core' ),
	),
	'jquery.ui.selectable' => array(
		'script' => 'resources/jquery/ui/jquery.ui.selectable.js',
		'needs' => array( 'jquery.ui.core' ),
	),
	'jquery.ui.slider' => array(
		'script' => 'resources/jquery/ui/jquery.ui.slider.js',
		'needs' => array( 'jquery.ui.core' ),
	),
	'jquery.ui.sortable' => array(
		'script' => 'resources/jquery/ui/jquery.ui.sortable.js',
		'needs' => array( 'jquery.ui.core' ),
	),
	'jquery.ui.tabs' => array(
		'script' => 'resources/jquery/ui/jquery.ui.tabs.js',
		'needs' => array( 'jquery.ui.core' ),
	),
	'jquery.ui.widget' => array(
		'script' => 'resources/jquery/ui/jquery.ui.widget.js',
		'needs' => array( 'jquery.ui.core' ),
	),
	// i18n
	'jquery.ui.datepicker-af' => array(
		'script' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-af.js',
		'needs' => array( 'jquery.ui.datepicker' ),
	),
	'jquery.ui.datepicker-ar' => array(
		'script' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-ar.js',
		'needs' => array( 'jquery.ui.datepicker' ),
	),
	'jquery.ui.datepicker-az' => array(
		'script' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-az.js',
		'needs' => array( 'jquery.ui.datepicker' ),
	),
	'jquery.ui.datepicker-bg' => array(
		'script' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-bg.js',
		'needs' => array( 'jquery.ui.datepicker' ),
	),
	'jquery.ui.datepicker-bs' => array(
		'script' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-bs.js',
		'needs' => array( 'jquery.ui.datepicker' ),
	),
	'jquery.ui.datepicker-ca' => array(
		'script' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-ca.js',
		'needs' => array( 'jquery.ui.datepicker' ),
	),
	'jquery.ui.datepicker-cs' => array(
		'script' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-cs.js',
		'needs' => array( 'jquery.ui.datepicker' ),
	),
	'jquery.ui.datepicker-da' => array(
		'script' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-da.js',
		'needs' => array( 'jquery.ui.datepicker' ),
	),
	'jquery.ui.datepicker-de' => array(
		'script' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-de.js',
		'needs' => array( 'jquery.ui.datepicker' ),
	),
	'jquery.ui.datepicker-el' => array(
		'script' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-el.js',
		'needs' => array( 'jquery.ui.datepicker' ),
	),
	'jquery.ui.datepicker-en-GB' => array(
		'script' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-en-GB.js',
		'needs' => array( 'jquery.ui.datepicker' ),
	),
	'jquery.ui.datepicker-eo' => array(
		'script' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-eo.js',
		'needs' => array( 'jquery.ui.datepicker' ),
	),
	'jquery.ui.datepicker-es' => array(
		'script' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-es.js',
		'needs' => array( 'jquery.ui.datepicker' ),
	),
	'jquery.ui.datepicker-et' => array(
		'script' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-et.js',
		'needs' => array( 'jquery.ui.datepicker' ),
	),
	'jquery.ui.datepicker-eu' => array(
		'script' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-eu.js',
		'needs' => array( 'jquery.ui.datepicker' ),
	),
	'jquery.ui.datepicker-fa' => array(
		'script' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-fa.js',
		'needs' => array( 'jquery.ui.datepicker' ),
	),
	'jquery.ui.datepicker-fi' => array(
		'script' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-fi.js',
		'needs' => array( 'jquery.ui.datepicker' ),
	),
	'jquery.ui.datepicker-fo' => array(
		'script' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-fo.js',
		'needs' => array( 'jquery.ui.datepicker' ),
	),
	'jquery.ui.datepicker-fr-CH' => array(
		'script' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-fr-CH.js',
		'needs' => array( 'jquery.ui.datepicker' ),
	),
	'jquery.ui.datepicker-fr' => array(
		'script' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-fr.js',
		'needs' => array( 'jquery.ui.datepicker' ),
	),
	'jquery.ui.datepicker-he' => array(
		'script' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-he.js',
		'needs' => array( 'jquery.ui.datepicker' ),
	),
	'jquery.ui.datepicker-hr' => array(
		'script' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-hr.js',
		'needs' => array( 'jquery.ui.datepicker' ),
	),
	'jquery.ui.datepicker-hu' => array(
		'script' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-hu.js',
		'needs' => array( 'jquery.ui.datepicker' ),
	),
	'jquery.ui.datepicker-hy' => array(
		'script' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-hy.js',
		'needs' => array( 'jquery.ui.datepicker' ),
	),
	'jquery.ui.datepicker-id' => array(
		'script' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-id.js',
		'needs' => array( 'jquery.ui.datepicker' ),
	),
	'jquery.ui.datepicker-is' => array(
		'script' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-is.js',
		'needs' => array( 'jquery.ui.datepicker' ),
	),
	'jquery.ui.datepicker-it' => array(
		'script' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-it.js',
		'needs' => array( 'jquery.ui.datepicker' ),
	),
	'jquery.ui.datepicker-ja' => array(
		'script' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-ja.js',
		'needs' => array( 'jquery.ui.datepicker' ),
	),
	'jquery.ui.datepicker-ko' => array(
		'script' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-ko.js',
		'needs' => array( 'jquery.ui.datepicker' ),
	),
	'jquery.ui.datepicker-lt' => array(
		'script' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-lt.js',
		'needs' => array( 'jquery.ui.datepicker' ),
	),
	'jquery.ui.datepicker-lv' => array(
		'script' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-lv.js',
		'needs' => array( 'jquery.ui.datepicker' ),
	),
	'jquery.ui.datepicker-ms' => array(
		'script' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-ms.js',
		'needs' => array( 'jquery.ui.datepicker' ),
	),
	'jquery.ui.datepicker-nl' => array(
		'script' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-nl.js',
		'needs' => array( 'jquery.ui.datepicker' ),
	),
	'jquery.ui.datepicker-no' => array(
		'script' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-no.js',
		'needs' => array( 'jquery.ui.datepicker' ),
	),
	'jquery.ui.datepicker-pl' => array(
		'script' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-pl.js',
		'needs' => array( 'jquery.ui.datepicker' ),
	),
	'jquery.ui.datepicker-pt-BR' => array(
		'script' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-pt-BR.js',
		'needs' => array( 'jquery.ui.datepicker' ),
	),
	'jquery.ui.datepicker-ro' => array(
		'script' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-ro.js',
		'needs' => array( 'jquery.ui.datepicker' ),
	),
	'jquery.ui.datepicker-ru' => array(
		'script' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-ru.js',
		'needs' => array( 'jquery.ui.datepicker' ),
	),
	'jquery.ui.datepicker-sk' => array(
		'script' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-sk.js',
		'needs' => array( 'jquery.ui.datepicker' ),
	),
	'jquery.ui.datepicker-sl' => array(
		'script' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-sl.js',
		'needs' => array( 'jquery.ui.datepicker' ),
	),
	'jquery.ui.datepicker-sq' => array(
		'script' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-sq.js',
		'needs' => array( 'jquery.ui.datepicker' ),
	),
	'jquery.ui.datepicker-sr-SR' => array(
		'script' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-sr-SR.js',
		'needs' => array( 'jquery.ui.datepicker' ),
	),
	'jquery.ui.datepicker-sr' => array(
		'script' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-sr.js',
		'needs' => array( 'jquery.ui.datepicker' ),
	),
	'jquery.ui.datepicker-sv' => array(
		'script' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-sv.js',
		'needs' => array( 'jquery.ui.datepicker' ),
	),
	'jquery.ui.datepicker-ta' => array(
		'script' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-ta.js',
		'needs' => array( 'jquery.ui.datepicker' ),
	),
	'jquery.ui.datepicker-th' => array(
		'script' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-th.js',
		'needs' => array( 'jquery.ui.datepicker' ),
	),
	'jquery.ui.datepicker-tr' => array(
		'script' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-tr.js',
		'needs' => array( 'jquery.ui.datepicker' ),
	),
	'jquery.ui.datepicker-uk' => array(
		'script' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-uk.js',
		'needs' => array( 'jquery.ui.datepicker' ),
	),
	'jquery.ui.datepicker-vi' => array(
		'script' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-vi.js',
		'needs' => array( 'jquery.ui.datepicker' ),
	),
	'jquery.ui.datepicker-zh-CN' => array(
		'script' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-zh-CN.js',
		'needs' => array( 'jquery.ui.datepicker' ),
	),
	'jquery.ui.datepicker-zh-HK' => array(
		'script' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-zh-HK.js',
		'needs' => array( 'jquery.ui.datepicker' ),
	),
	'jquery.ui.datepicker-zh-TW' => array(
		'script' => 'resources/jquery/ui/i18n/jquery.ui.datepicker-zh-TW.js',
		'needs' => array( 'jquery.ui.datepicker' ),
	),
	// Effects
	'jquery.effects.blind' => array(
		'script' => 'resources/jquery/effects/jquery.effects.blind.js',
		'needs' => array( 'jquery' ),
	),
	'jquery.effects.bounce' => array(
		'script' => 'resources/jquery/effects/jquery.effects.bounce.js',
		'needs' => array( 'jquery' ),
	),
	'jquery.effects.clip' => array(
		'script' => 'resources/jquery/effects/jquery.effects.clip.js',
		'needs' => array( 'jquery' ),
	),
	'jquery.effects.core' => array(
		'script' => 'resources/jquery/effects/jquery.effects.core.js',
		'needs' => array( 'jquery' ),
	),
	'jquery.effects.drop' => array(
		'script' => 'resources/jquery/effects/jquery.effects.drop.js',
		'needs' => array( 'jquery' ),
	),
	'jquery.effects.explode' => array(
		'script' => 'resources/jquery/effects/jquery.effects.explode.js',
		'needs' => array( 'jquery' ),
	),
	'jquery.effects.fold' => array(
		'script' => 'resources/jquery/effects/jquery.effects.fold.js',
		'needs' => array( 'jquery' ),
	),
	'jquery.effects.highlight' => array(
		'script' => 'resources/jquery/effects/jquery.effects.highlight.js',
		'needs' => array( 'jquery' ),
	),
	'jquery.effects.pulsate' => array(
		'script' => 'resources/jquery/effects/jquery.effects.pulsate.js',
		'needs' => array( 'jquery' ),
	),
	'jquery.effects.scale' => array(
		'script' => 'resources/jquery/effects/jquery.effects.scale.js',
		'needs' => array( 'jquery' ),
	),
	'jquery.effects.shake' => array(
		'script' => 'resources/jquery/effects/jquery.effects.shake.js',
		'needs' => array( 'jquery' ),
	),
	'jquery.effects.slide' => array(
		'script' => 'resources/jquery/effects/jquery.effects.slide.js',
		'needs' => array( 'jquery' ),
	),
	'jquery.effects.transfer' => array(
		'script' => 'resources/jquery/effects/jquery.effects.transfer.js',
		'needs' => array( 'jquery' ),
	),
	
	/* MediaWiki */
	
	'mediawiki' => array(
		'script' => 'resources/mediawiki/mediawiki.js', 'raw' => true ),
	
	/* MediaWiki Legacy */
	
	'mediawiki.legacy.ajax' => array(
		'script' => 'resources/mediawiki/legacy/mediawiki.legacy.ajax.js',
		'needs' => array( 'mediawiki' ),
	),
	'mediawiki.legacy.ajaxwatch' => array(
		'script' => 'resources/mediawiki/legacy/mediawiki.legacy.ajaxwatch.js',
		'needs' => array( 'mediawiki' ),
	),
	'mediawiki.legacy.block' => array(
		'script' => 'resources/mediawiki/legacy/mediawiki.legacy.block.js',
		'needs' => array( 'mediawiki' ),
	),
	'mediawiki.legacy.changepassword' => array(
		'script' => 'resources/mediawiki/legacy/mediawiki.legacy.changepassword.js',
		'needs' => array( 'mediawiki' ),
	),
	'mediawiki.legacy.edit' => array(
		'script' => 'resources/mediawiki/legacy/mediawiki.legacy.edit.js',
		'needs' => array( 'mediawiki' ),
	),
	'mediawiki.legacy.enhancedchanges' => array(
		'script' => 'resources/mediawiki/legacy/mediawiki.legacy.enhancedchanges.js',
		'needs' => array( 'mediawiki' ),
	),
	'mediawiki.legacy.history' => array(
		'script' => 'resources/mediawiki/legacy/mediawiki.legacy.history.js',
		'needs' => array( 'mediawiki' ),
	),
	'mediawiki.legacy.htmlform' => array(
		'script' => 'resources/mediawiki/legacy/mediawiki.legacy.htmlform.js',
		'needs' => array( 'mediawiki' ),
	),
	'mediawiki.legacy.IEFixes' => array(
		'script' => 'resources/mediawiki/legacy/mediawiki.legacy.IEFixes.js',
		'needs' => array( 'mediawiki' ),
	),
	'mediawiki.legacy.metadata' => array(
		'script' => 'resources/mediawiki/legacy/mediawiki.legacy.metadata.js',
		'needs' => array( 'mediawiki' ),
	),
	'mediawiki.legacy.mwsuggest' => array(
		'script' => 'resources/mediawiki/legacy/mediawiki.legacy.mwsuggest.js',
		'needs' => array( 'mediawiki' ),
	),
	'mediawiki.legacy.prefs' => array(
		'script' => 'resources/mediawiki/legacy/mediawiki.legacy.prefs.js',
		'needs' => array( 'mediawiki' ),
	),
	'mediawiki.legacy.preview' => array(
		'script' => 'resources/mediawiki/legacy/mediawiki.legacy.preview.js',
		'needs' => array( 'mediawiki' ),
	),
	'mediawiki.legacy.protect' => array(
		'script' => 'resources/mediawiki/legacy/mediawiki.legacy.protect.js',
		'needs' => array( 'mediawiki' ),
	),
	'mediawiki.legacy.rightclickedit' => array(
		'script' => 'resources/mediawiki/legacy/mediawiki.legacy.rightclickedit.js',
		'needs' => array( 'mediawiki' ),
	),
	'mediawiki.legacy.search' => array(
		'script' => 'resources/mediawiki/legacy/mediawiki.legacy.search.js',
		'needs' => array( 'mediawiki' ),
	),
	'mediawiki.legacy.upload' => array(
		'script' => 'resources/mediawiki/legacy/mediawiki.legacy.upload.js',
		'needs' => array( 'mediawiki' ),
	),
	'mediawiki.legacy.wikibits' => array(
		'script' => 'resources/mediawiki/legacy/mediawiki.legacy.wikibits.js',
		'needs' => array( 'mediawiki' ),
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
		'needs' => array( 'foo' ),
		'style' => 'resources/test/test.css',
	),
	'foo' => array(
		'script' => 'resources/test/foo.js',
		'needs' => array( 'bar' ),
		'style' => 'resources/test/foo.css',
		'messages' => array( 'january', 'february', 'march', 'april', 'may', 'june' ),
	),
	'bar' => array(
		'script' => 'resources/test/bar.js',
		'needs' => array( 'buz' ),
		'style' => 'resources/test/bar.css',
		'messages' => array( 'july', 'august', 'september', 'october', 'november', 'december' ),
	),
	'buz' => array(
		'script' => 'resources/test/buz.js',
		'needs' => array( 'baz' ),
		'style' => 'resources/test/buz.css',
	),
	'baz' => array(
		'script' => 'resources/test/baz.js',
		'style' => 'resources/test/baz.css',
	),
) );