<?php

class DefaultPreferences {
	static $defaultPreferences = null;
	
	static function getDefaultPreferences() {
		if (self::$defaultPreferences)
			return self::$defaultPreferences;
	
		global $wgLang, $wgRCMaxAge, $wgUser;
		
		$defaultPreferences = array();
		// What is quickbar even for?
		$defaultPreferences['quickbar'] = array( 'type' => 'toggle' );
		$defaultPreferences['underline'] =
				array(
					'type' => 'select',
					'options' => array(
						0 => wfMsg( 'underline-never' ),
						1 => wfMsg( 'underline-always' ),
						2 => wfMsg( 'underline-default' ),
					),
					'label-message' => 'tog-underline',
					'section' => 'misc',
				);
		$defaultPreferences['cols'] =
				array(
					'type' => 'int',
					'label-message' => 'columns',
					'section' => 'editing',
					'min' => 4,
					'max' => 1000,
				);
		$defaultPreferences['rows'] =
				array(
					'type' => 'int',
					'label-message' => 'rows',
					'section' => 'editing',
					'min' => 4,
					'max' => 1000,
				);
		$defaultPreferences['searchlimit'] =
				array(
					'type' => 'int',
					'label-message' => 'resultsperpage',
					'section' => 'search',
					'min' => 0,
				);
		$defaultPreferences['contextlines'] =
				array(
					'type' => 'int',
					'label-message' => 'contextlines',
					'section' => 'search',
					'min' => 0,
				);
		$defaultPreferences['contextchars'] =
				array(
					'type' => 'int',
					'label-message' => 'contextchars',
					'section' => 'search',
					'min' => 0,
				);
#		$defaultPreferences['disablesuggest'] =
#				array(
#					// Presumably a toggle, but not on the current preferences form...
#				);
		$defaultPreferences['skin'] =
				array(
					'type' => 'radio',
					'options' => self::generateSkinOptions(),
					'label-message' => 'skin',
					'section' => 'skin',
				);
		$defaultPreferences['math'] =
				array(
					'type' => 'radio',
					'options' => $wgLang->getMathNames(),
					'label' => '&nbsp;',
					'section' => 'math',
				);
		$defaultPreferences['usenewrc'] =
				array(
					'type' => 'toggle',
					'label-message' => 'tog-usenewrc',
					'section' => 'recentchanges',
				);
		$defaultPreferences['rcdays'] =
				array(
					'type' => 'int',
					'label-message' => 'recentchangesdays',
					'section' => 'recentchanges',
					'min' => 1,
					'max' => ceil($wgRCMaxAge / (3600*24)),
				);
		$defaultPreferences['rclimit'] =
				array(
					'type' => 'int',
					'label-message' => 'recentchangescount',
					'section' => 'recentchanges',
				);
		$defaultPreferences['wllimit'] =
				array(
					'type' => 'int',
					'min' => 0,
					'max' => 1000,
					'label-message' => 'prefs-watchlist-edits',
					'section' => 'watchlist'
				);
		$defaultPreferences['hideminor'] =
				array(
					'type' => 'toggle',
					'label-message' => 'tog-hideminor',
					'section' => 'recentchanges',
				);
		$defaultPreferences['hidepatrolled'] =
				array(
					'type' => 'toggle',
					'section ' => 'recentchanges',
					'label-message' => 'tog-hidepatrolled',
				);
		$defaultPreferences['newpageshidepatrolled'] =
				array(
					'type' => 'toggle',
					'section' => 'recentchanges',
					'label-message' => 'tog-newpageshidepatrolled',
				);
		$defaultPreferences['highlightbroken'] =
				array(
					'type' => 'toggle',
					'section' => 'misc',
					'label' => wfMsg('tog-highlightbroken'), // Raw HTML
				);
		$defaultPreferences['stubthreshold'] =
				array(
					'type' => 'int',
					'section' => 'misc',
					'label' => wfMsg('stub-threshold'), // Raw HTML message. Yay?
				);
		$defaultPreferences['previewontop'] =
				array(
					'type' => 'toggle',
					'section' => 'editing',
					'label-message' => 'tog-previewontop',
				);
		$defaultPreferences['previewonfirst'] =
				array(
					'type' => 'toggle',
					'section' => 'editing',
					'label-message' => 'tog-previewonfirst',
				);
		$defaultPreferences['editsection'] =
				array(
					'type' => 'toggle',
					'section' => 'editing',
					'label-message' => 'tog-editsection',
				);
		$defaultPreferences['editsectiononrightclick'] =
				array(
					'type' => 'toggle',
					'section' => 'editing',
					'label-message' => 'tog-editsectiononrightclick',
				);
		$defaultPreferences['editondblclick'] =
				array(
					'type' => 'toggle',
					'section' => 'editing',
					'label-message' => 'tog-editondblclick',
				);
		$defaultPreferences['editwidth'] =
				array(
					'type' => 'toggle',
					'section' => 'editing',
					'label-message' => 'tog-editwidth',
				);
		$defaultPreferences['showtoc'] =
				array(
					'type' => 'toggle',
					'section' => 'misc',
					'label-message' => 'tog-showtoc',
				);
		$defaultPreferences['showtoolbar'] =
				array(
					'type' => 'toggle',
					'section' => 'editing',
					'label-message' => 'tog-showtoolbar',
				);
		$defaultPreferences['minordefault'] =
				array(
					'type' => 'toggle',
					'section' => 'editing',
					'label-message' => 'tog-minordefault',
				);
		$defaultPreferences['date'] =
				array(
					'type' => 'radio',
					'options' => self::getDateOptions(),
					'label-message' => 'dateformat',
				);
		$defaultPreferences['imagesize'] =
				array(
					'type' => 'select',
					'options' => self::getImageSizes(),
					'label-message' => 'imagemaxsize',
				);
		$defaultPreferences['thumbsize'] =
				array(
					'type' => 'select',
					'options' => self::getThumbSizes(),
					'label-message' => 'thumbsize',
				);
		$defaultPreferences['rememberpassword'] =
				array(
					'type' => 'toggle',
					'label-message' => 'tog-rememberpassword',
					'section' => 'misc',
				);
		$defaultPreferences['nocache'] =
				array(
					'type' => 'toggle',
					'label-message' => 'tog-rememberpassword',
					'section' => 'misc',
				);
		$defaultPreferences['diffonly'] =
				array(
					'type' => 'toggle',
					'section' => 'misc',
					'label-message' => 'tog-diffonly',
				);
		$defaultPreferences['showhiddencats'] =
				array(
					'type' => 'toggle',
					'section' => 'misc',
					'label-message' => 'tog-showhiddencats'
				);
		$defaultPreferences['norollbackdiff'] =
				array(
					'type' => 'toggle',
					'section' => 'misc',
					'label-message' => 'tog-norollbackdiff',
				);
		$defaultPreferences['enotifwatchlistpages'] =
				array(
					'type' => 'toggle',
					'section' => 'misc',
					'label-message' => 'tog-enotifwatchlistpages',
				);
		$defaultPreferences['enotifusertalkpages'] =
				array(
					'type' => 'toggle',
					'section' => 'misc',
					'label-message' => 'tog-enotifusertalkpages',
				);
		$defaultPreferences['enotifminoredits'] =
				array(
					'type' => 'toggle',
					'section' => 'misc',
					'label-message' => 'tog-enotifminoredits',
				);
		$defaultPreferences['enotifrevealaddr'] =
				array(
					'type' => 'toggle',
					'section' => 'misc',
					'label-message' => 'tog-enotifrevealaddr'
				);
		$defaultPreferences['shownumberswatching'] =
				array(
					'type' => 'toggle',
					'section' => 'misc',
					'label-message' => 'tog-shownumberswatching',
				);
#		$defaultPreferences['fancysig'] =
#				array(
#
#				);
		$defaultPreferences['externaleditor'] =
				array(
					'type' => 'toggle',
					'section' => 'editing',
					'label-message' => 'tog-externaleditor',
				);
		$defaultPreferences['externaldiff'] =
				array(
					'type' => 'toggle',
					'section' => 'editing',
					'label-message' => 'tog-externaldiff',
				);
		$defaultPreferences['forceeditsummary'] =
				array(
					'type' => 'toggle',
					'section' => 'editing',
					'label-message' => 'tog-forceeditsummary',
				);
		$defaultPreferences['showjumplinks'] =
				array(
					'type' => 'toggle',
					'section' => 'misc',
					'label-message' => 'tog-showjumplinks',
				);
		$defaultPreferences['justify'] =
				array(
					'type' => 'toggle',
					'section' => 'misc',
					'label-message' => 'tog-justify',
				);
		$defaultPreferences['numberheadings'] =
				array(
					'type' => 'toggle',
					'section' => 'misc',
					'label-message' => 'tog-numberheadings',
				);
		$defaultPreferences['uselivepreview'] =
				array(
					'type' => 'toggle',
					'section' => 'misc',
					'label-message' => 'tog-uselivepreview',
				);
		$defaultPreferences['watchlistdays'] =
				array(
					'type' => 'int',
					'min' => 0,
					'max' => 7,
					'section' => 'watchlist',
					'label-message' => 'prefs-watchlist-days',
				);
		$defaultPreferences['extendwatchlist'] =
				array(
					'type' => 'toggle',
					'section' => 'watchlist',
					'label-message' => 'tog-extendwatchlist',
				);
		$defaultPreferences['watchlisthideminor'] =
				array(
					'type' => 'toggle',
					'section' => 'watchlist',
					'label-message' => 'tog-watchlisthideminor',
				);
		$defaultPreferences['watchlisthidebots'] =
				array(
					'type' => 'toggle',
					'section' => 'watchlist',
					'label-message' => 'tog-watchlisthidebots',
				);
		$defaultPreferences['watchlisthideown'] =
				array(
					'type' => 'toggle',
					'section' => 'watchlist',
					'label-message' => 'tog-watchlisthideown',
				);
		$defaultPreferences['watchlisthideanons'] =
				array(
					'type' => 'toggle',
					'section' => 'watchlist',
					'label-message' => 'tog-watchlisthideanons',
				);
		$defaultPreferences['watchlisthideliu'] =
				array(
					'type' => 'toggle',
					'section' => 'watchlist',
					'label-message' => 'tog-watchlisthideliu',
				);
		$defaultPreferences['watchlisthidepatrolled'] =
				array(
					'type' => 'toggle',
					'section' => 'watchlist',
					'label-message' => 'tog-watchlisthidepatrolled',
				);
			
		$watchTypes = array( 'edit' => 'watchdefault',
								'move' => 'watchmoves',
								'delete' => 'watchdeletion' );
		
		// Kinda hacky
		if( $wgUser->isAllowed( 'createpage' ) || $wgUser->isAllowed( 'createtalk' ) ) {
			$watchTypes['read'] = 'watchcreations';
		}
								
		foreach( $watchTypes as $action => $pref ) {
			if ( $wgUser->isAllowed( $action ) ) {
				$defaultPreferences[$pref] = array(
					'type' => 'toggle',
					'section' => 'watchlist',
					'label-message' => "tog-$pref",
				);
			}
		}
		
		global $wgContLang, $wgDisableLangConversion;
		/* see if there are multiple language variants to choose from*/
		if(!$wgDisableLangConversion) {
			$variants = $wgContLang->getVariants();
			$variantArray = array();

			$languages = Language::getLanguageNames( true );
			foreach($variants as $v) {
				$v = str_replace( '_', '-', strtolower($v));
				if( array_key_exists( $v, $languages ) ) {
					// If it doesn't have a name, we'll pretend it doesn't exist
					$variantArray[$v] = $languages[$v];
				}
			}

			$options = "\n";
			foreach( $variantArray as $code => $name ) {
#				$options .= Xml::option( "$code - $name", $code, $selected ) . "\n";
			}

			if(count($variantArray) > 1) {
				$wgOut->addHTML(
					$this->tableRow(
						Xml::label( wfMsg( 'yourvariant' ), 'wpUserVariant' ),
						Xml::tags( 'select',
							array( 'name' => 'wpUserVariant', 'id' => 'wpUserVariant' ),
							$options
						)
					)
				);
			}
		}
		
		if(count($variantArray) > 1 && !$wgDisableLangConversion && !$wgDisableTitleConversion) {
			$defaultPreferences['noconvertlink'] =
					array(
						'type' => 'toggle',
						'section' => 'misc',
						'label-message' => 'tog-noconvertlink',
					);
		}
		
		$defaultPreferences['gender'] =
				array(
					'type' => 'select',
					'section' => 'user',
					'options' => array(
						'male' => wfMsg('gender-male'),
						'female' => wfMsg('gender-female'),
						'unknown' => wfMsg('gender-unknown'),
					),
				);
				
		wfRunHooks( 'GetPreferences', array( &$defaultPreferences ) );
		
		self::$defaultPreferences = $defaultPreferences;
		
		return $defaultPreferences;
	}
	
	static function generateSkinOptions() {
		global $wgDefaultSkin;
		$ret = array();
		
		$mptitle = Title::newMainPage();
		$previewtext = wfMsg( 'skin-preview' );
		# Only show members of Skin::getSkinNames() rather than
		# $skinNames (skins is all skin names from Language.php)
		$validSkinNames = Skin::getUsableSkins();
		# Sort by UI skin name. First though need to update validSkinNames as sometimes
		# the skinkey & UI skinname differ (e.g. "standard" skinkey is "Classic" in the UI).
		foreach ( $validSkinNames as $skinkey => &$skinname ) {
			$msgName = "skinname-{$skinkey}";
			$localisedSkinName = wfMsg( $msgName );
			if ( !wfEmptyMsg( $msgName, $localisedSkinName ) )  {
				$skinname = $localisedSkinName;
			}
		}
		asort($validSkinNames);
		
		foreach( $validSkinNames as $skinkey => $sn ) {
			$mplink = htmlspecialchars( $mptitle->getLocalURL( "useskin=$skinkey" ) );
			$previewlink = "(<a target='_blank' href=\"$mplink\">$previewtext</a>)";
			$extraLinks = '';
			global $wgAllowUserCss, $wgAllowUserJs;
			if( $wgAllowUserCss ) {
				$cssPage = Title::makeTitleSafe( NS_USER, $wgUser->getName().'/'.$skinkey.'.css' );
				$customCSS = $sk->makeLinkObj( $cssPage, wfMsgExt('prefs-custom-css', array() ) );
				$extraLinks .= " ($customCSS)";
			}
			if( $wgAllowUserJs ) {
				$jsPage = Title::makeTitleSafe( NS_USER, $wgUser->getName().'/'.$skinkey.'.js' );
				$customJS = $sk->makeLinkObj( $jsPage, wfMsgHtml('prefs-custom-js') );
				$extraLinks .= " ($customJS)";
			}
			if( $skinkey == $wgDefaultSkin )
				$sn .= ' (' . wfMsg( 'default' ) . ')';
			$ret[$skinkey] = "$sn $previewlink{$extraLinks}";
		}
		
		return $ret;
	}
	
	static function getDateOptions() {
		global $wgLang;
		$dateopts = $wgLang->getDatePreferences();
		
		$ret = array();
		
		if ($dateopts) {
			$idCnt = 0;
			$epoch = '20010115161234'; # Wikipedia day
			foreach( $dateopts as $key ) {
				if( $key == 'default' ) {
					$formatted = wfMsg( 'datedefault' );
				} else {
					$formatted = $wgLang->timeanddate( $epoch, false, $key );
				}
				$ret[$key] = $formatted;
			}
		}
		return $ret;
	}
	
	static function getImageSizes() {
		global $wgImageLimits;
		
		$ret = array();
		
		foreach ( $wgImageLimits as $index => $limits ) {
			$ret[$index] = "{$limits[0]}×{$limits[1]}" . wfMsg('unit-pixel');
		}
		
		return $ret;
	}
	
	static function getThumbSizes() {
		global $wgThumbLimits;
		
		$ret = array();
		
		foreach ( $wgThumbLimits as $index => $size ) {
			$ret[$index] = $size . wfMsg('unit-pixel');
		}
		
		return $ret;
	}
}
