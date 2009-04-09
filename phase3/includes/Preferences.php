<?php

class Preferences {
	static $defaultPreferences = null;
	
	static function getPreferences( $user ) {
		if (self::$defaultPreferences)
			return self::$defaultPreferences;
	
		global $wgLang, $wgRCMaxAge;
		
		$defaultPreferences = array();
		
		## User info #####################################
		// Information panel
		$defaultPreferences['username'] =
				array(
					'type' => 'info',
					'label-message' => 'username',
					'default' => $user->getName(),
					'section' => 'personal',
				);
		
		$defaultPreferences['userid'] =
				array(
					'type' => 'info',
					'label-message' => 'uid',
					'default' => $user->getId(),
					'section' => 'personal',
				);
		
		# Get groups to which the user belongs
		$userEffectiveGroups = $user->getEffectiveGroups();
		$userEffectiveGroupsArray = array();
		foreach( $userEffectiveGroups as $ueg ) {
			if( $ueg == '*' ) {
				// Skip the default * group, seems useless here
				continue;
			}
			$userEffectiveGroupsArray[] = User::makeGroupLinkHTML( $ueg );
		}
		asort( $userEffectiveGroupsArray );
		
		$defaultPreferences['usergroups'] =
				array(
					'type' => 'info',
					'label' => wfMsgExt( 'prefs-memberingroups', 'parseinline',
								count($userEffectiveGroupsArray) ),
					'default' => $wgLang->commaList( $userEffectiveGroupsArray ),
					'raw' => true,
					'section' => 'personal',
				);
		
		$defaultPreferences['editcount'] =
				array(
					'type' => 'info',
					'label-message' => 'prefs-edits',
					'default' => $user->getEditCount(),
					'section' => 'personal',
				);
		
		if ($user->getRegistration()) {
			$defaultPreferences['registrationdate'] =
					array(
						'type' => 'info',
						'label-message' => 'prefs-registration',
						'default' => $wgLang->timeanddate( $user->getRegistration() ),
						'section' => 'personal',
					);
		}
				
		// Actually changeable stuff
		global $wgAllowRealName;
		if ($wgAllowRealName) {
			$defaultPreferences['realname'] =
					array(
						'type' => 'text',
						'default' => $user->getRealName(),
						'section' => 'personal',
						'label-message' => 'yourrealname',
						'help-message' => 'prefs-help-realname',
					);
		}
				
		global $wgEmailConfirmToEdit;
		
		$defaultPreferences['emailaddress'] =
				array(
					'type' => 'text',
					'default' => $user->getEmail(),
					'section' => 'personal',
					'label-message' => 'youremail',
					'help-message' => $wgEmailConfirmToEdit
										? 'prefs-help-email-required'
										: 'prefs-help-email',
					'validation-callback' => array( 'Preferences', 'validateEmail' ),
				);
		
		global $wgAuth;
		if ($wgAuth->allowPasswordChange()) {
			global $wgUser; // For skin.
			$link = $wgUser->getSkin()->link( SpecialPage::getTitleFor( 'ResetPass' ),
				wfMsgHtml( 'prefs-resetpass' ), array() ,
				array('returnto' => SpecialPage::getTitleFor( 'Preferences') ) );
				
			$defaultPreferences['password'] =
					array(
						'type' => 'info',
						'raw' => true,
						'default' => $link,
						'label-message' => 'yourpassword',
						'section' => 'personal',
					);
		}
		
		$defaultPreferences['gender'] =
				array(
					'type' => 'select',
					'section' => 'personal',
					'options' => array(
						'male' => wfMsg('gender-male'),
						'female' => wfMsg('gender-female'),
						'unknown' => wfMsg('gender-unknown'),
					),
					'label-message' => 'yourgender',
					'help-message' => 'prefs-help-gender',
				);
				
		// Language
		global $wgContLanguageCode;
		$languages = Language::getLanguageNames( false );
		if( !array_key_exists( $wgContLanguageCode, $languages ) ) {
			$languages[$wgContLanguageCode] = $wgContLanguageCode;
		}
		ksort( $languages );
		
		$options = array();
		foreach( $languages as $code => $name ) {
			$options[$code] = "$code - $name";
		}
		$defaultPreferences['language'] =
				array(
					'type' => 'select',
					'section' => 'personal',
					'options' => $options,
					'default' => $wgContLanguageCode,
					'label-message' => 'yourlanguage',
				);
				
		global $wgContLang, $wgDisableLangConversion;
		/* see if there are multiple language variants to choose from*/
		$variantArray = array();
		if(!$wgDisableLangConversion) {
			$variants = $wgContLang->getVariants();

			$languages = Language::getLanguageNames( true );
			foreach($variants as $v) {
				$v = str_replace( '_', '-', strtolower($v));
				if( array_key_exists( $v, $languages ) ) {
					// If it doesn't have a name, we'll pretend it doesn't exist
					$variantArray[$v] = $languages[$v];
				}
			}

			$options = array();
			foreach( $variantArray as $code => $name ) {
				$options[$code] = "$code - $name";
			}

			if(count($variantArray) > 1) {
				$defaultPreferences['variant'] =
					array(
						'label-message' => 'yourvariant',
						'type' => 'select',
						'options' => $options,
						'section' => 'personal',
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
		
		global $wgMaxSigChars;
		$defaultPreferences['nickname'] =
				array(
					'type' => 'text',
					'maxlength' => $wgMaxSigChars,
					'label-message' => 'yournick',
					'validation-callback' =>
						array( 'Preferences', 'validateSignature' ),
					'section' => 'personal',
					'filter-callback' => array( 'Preferences', 'cleanSignature' ),
				);
		$defaultPreferences['fancysig'] =
				array(
					'type' => 'toggle',
					'label-message' => 'tog-fancysig',
					'section' => 'personal'
				);
					
		
		## Skin #####################################
		global $wgAllowUserSkin;
		
		if ($wgAllowUserSkin) {
			$defaultPreferences['skin'] =
					array(
						'type' => 'radio',
						'options' => self::generateSkinOptions(),
						'label-message' => 'skin',
						'section' => 'skin',
					);
		}
		
		## TODO QUICKBAR
				
		## Math #####################################
		global $wgUseTeX;
		if ($wgUseTeX) {
			$defaultPreferences['math'] =
					array(
						'type' => 'radio',
						'options' => array_map( 'wfMsg', $wgLang->getMathNames() ),
						'label' => '&nbsp;',
						'section' => 'math',
						'label-message' => 'math',
					);
		}
		
		## Files #####################################
		$defaultPreferences['imagesize'] =
				array(
					'type' => 'select',
					'options' => self::getImageSizes(),
					'label-message' => 'imagemaxsize',
					'section' => 'files',
				);
		$defaultPreferences['thumbsize'] =
				array(
					'type' => 'select',
					'options' => self::getThumbSizes(),
					'label-message' => 'thumbsize',
					'section' => 'files',
				);
		
		## Date and time #####################################
		$dateOptions = self::getDateOptions();
		if ($dateOptions) {
			$defaultPreferences['date'] =
					array(
						'type' => 'radio',
						'options' => $dateOptions,
						'label-message' => 'dateformat',
						'section' => 'date',
					);
		}
		
		## TODO OFFSET
		
		## Editing #####################################
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
				
		## RecentChanges #####################################
		$defaultPreferences['usenewrc'] =
				array(
					'type' => 'toggle',
					'label-message' => 'tog-usenewrc',
					'section' => 'rc',
				);
		$defaultPreferences['rcdays'] =
				array(
					'type' => 'int',
					'label-message' => 'recentchangesdays',
					'section' => 'rc',
					'min' => 1,
					'max' => ceil($wgRCMaxAge / (3600*24)),
				);
		$defaultPreferences['rclimit'] =
				array(
					'type' => 'int',
					'label-message' => 'recentchangescount',
					'section' => 'rc',
				);
		$defaultPreferences['hideminor'] =
				array(
					'type' => 'toggle',
					'label-message' => 'tog-hideminor',
					'section' => 'rc',
				);
				
		global $wgUseRCPatrol;
		if ($wgUseRCPatrol) {
			$defaultPreferences['hidepatrolled'] =
					array(
						'type' => 'toggle',
						'section' => 'rc',
						'label-message' => 'tog-hidepatrolled',
					);
			$defaultPreferences['newpageshidepatrolled'] =
					array(
						'type' => 'toggle',
						'section' => 'rc',
						'label-message' => 'tog-newpageshidepatrolled',
					);
		}
		
		global $wgRCShowWatchingUsers;
		if ($wgRCShowWatchingUsers) {
			$defaultPreferences['shownumberswatching'] =
					array(
						'type' => 'toggle',
						'section' => 'misc',
						'label-message' => 'tog-shownumberswatching',
					);
		}
				
		## Watchlist #####################################
		$defaultPreferences['wllimit'] =
				array(
					'type' => 'int',
					'min' => 0,
					'max' => 1000,
					'label-message' => 'prefs-watchlist-edits',
					'section' => 'watchlist'
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
		
		if ( $wgUseRCPatrol ) {
			$defaultPreferences['watchlisthidepatrolled'] =
					array(
						'type' => 'toggle',
						'section' => 'watchlist',
						'label-message' => 'tog-watchlisthidepatrolled',
					);
		}
		
		$watchTypes = array( 'edit' => 'watchdefault',
								'move' => 'watchmoves',
								'delete' => 'watchdeletion' );
		
		// Kinda hacky
		if( $user->isAllowed( 'createpage' ) || $user->isAllowed( 'createtalk' ) ) {
			$watchTypes['read'] = 'watchcreations';
		}
								
		foreach( $watchTypes as $action => $pref ) {
			if ( $user->isAllowed( $action ) ) {
				$defaultPreferences[$pref] = array(
					'type' => 'toggle',
					'section' => 'watchlist',
					'label-message' => "tog-$pref",
				);
			}
		}
		
		## Search #####################################
		$defaultPreferences['searchlimit'] =
				array(
					'type' => 'int',
					'label-message' => 'resultsperpage',
					'section' => 'searchoptions',
					'min' => 0,
				);
		$defaultPreferences['contextlines'] =
				array(
					'type' => 'int',
					'label-message' => 'contextlines',
					'section' => 'searchoptions',
					'min' => 0,
				);
		$defaultPreferences['contextchars'] =
				array(
					'type' => 'int',
					'label-message' => 'contextchars',
					'section' => 'searchoptions',
					'min' => 0,
				);
		
		$nsOptions = array();
		foreach( $wgContLang->getNamespaces() as $ns => $name ) {
			if ($ns < 0) continue;
			$displayNs = str_replace( '_', ' ', $name );
			
			if (!$displayNs) $displayNs = wfMsg( 'blanknamespace' );
			
			$nsOptions[$ns] = $displayNs;
		}
		
		$defaultPreferences['searchNs'] =
				array(
					'type' => 'multiselect',
					'label-message' => 'defaultns',
					'options' => $nsOptions,
					'section' => 'searchoptions',
				);
				
		global $wgEnableMWSuggest;
		if ($wgEnableMWSuggest) {
			$defaultPreferences['disablesuggest'] =
					array(
						'type' => 'toggle',
						'label-message' => 'mwsuggest-disable',
						'section' => 'searchoptions',
					);
		}
		
		## Misc #####################################
		
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
		$defaultPreferences['showtoc'] =
				array(
					'type' => 'toggle',
					'section' => 'misc',
					'label-message' => 'tog-showtoc',
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
				
		## Email #######################################
		## Email stuff
		global $wgEnableEmail, $wgEnableUserEmail;
		if ($wgEnableEmail) {
		
			if ($wgEnableUserEmail) {
				$defaultPreferences['disableemail'] =
						array(
							'type' => 'toggle',
							'invert' => true,
							'section' => 'email',
							'label-message' => 'allowemail',
						);
				$defaultPreferences['ccmeonemails'] =
						array(
							'type' => 'toggle',
							'section' => 'email',
							'label-message' => 'tog-ccmeonemails',
						);
			}
			
			$defaultPreferences['enotifwatchlistpages'] =
					array(
						'type' => 'toggle',
						'section' => 'email',
						'label-message' => 'tog-enotifwatchlistpages',
					);
			$defaultPreferences['enotifusertalkpages'] =
					array(
						'type' => 'toggle',
						'section' => 'email',
						'label-message' => 'tog-enotifusertalkpages',
					);
			$defaultPreferences['enotifminoredits'] =
					array(
						'type' => 'toggle',
						'section' => 'email',
						'label-message' => 'tog-enotifminoredits',
					);
			$defaultPreferences['enotifrevealaddr'] =
					array(
						'type' => 'toggle',
						'section' => 'email',
						'label-message' => 'tog-enotifrevealaddr'
					);
		}
				
		## Prod in defaults from the user
		global $wgDefaultUserOptions;
		foreach( $defaultPreferences as $name => &$info ) {
			$prefFromUser = $user->getOption( $name );
			$field = HTMLForm::loadInputFromParameters( $info ); // For validation
			$globalDefault = isset($wgDefaultUserOptions[$name])
								? $wgDefaultUserOptions[$name]
								: null;
			
			// If it validates, set it as the default
			if ( isset( $user->mOptions[$name] ) && // Make sure we're not just pulling nothing
					$field->validate( $prefFromUser, $user->mOptions ) ) {
				$info['default'] = $prefFromUser;
			} elseif ( isset($info['default']) ) {
				// Already set, no problem
				continue;
			} elseif( $field->validate( $globalDefault, $user->mOptions ) ) {
				$info['default'] = $globalDefault;
			}
		}
				
		wfRunHooks( 'GetPreferences', array( &$defaultPreferences ) );
		
		self::$defaultPreferences = $defaultPreferences;
		
		return $defaultPreferences;
	}
	
	static function savePreferencesFromForm( $data, $user ) {
		
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
				$cssPage = Title::makeTitleSafe( NS_USER, $user->getName().'/'.$skinkey.'.css' );
				$customCSS = $sk->makeLinkObj( $cssPage, wfMsgExt('prefs-custom-css', array() ) );
				$extraLinks .= " ($customCSS)";
			}
			if( $wgAllowUserJs ) {
				$jsPage = Title::makeTitleSafe( NS_USER, $user->getName().'/'.$skinkey.'.js' );
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
	
	static function validateSignature( $signature, $alldata ) {
		global $wgParser, $wgMaxSigChars, $wgLang;
		if( mb_strlen( $signature ) > $wgMaxSigChars ) {
			return
				Xml::element( 'span', array( 'class' => 'error' ),
					wfMsgExt( 'badsiglength', 'parsemag',
						$wgLang->formatNum( $wgMaxSigChars )
					)
				);
		} elseif( !empty( $alldata['fancysig'] ) &&
				false === $wgParser->validateSig( $signature ) ) {
			return Xml::element( 'span', array( 'class' => 'error' ), wfMsg( 'badsig' ) );
		} else {
			return true;
		}
	}
	
	static function cleanSignature( $signature, $alldata ) {
		global $wgParser;
		if( $alldata['fancysig'] ) {
			$signature = $wgParser->cleanSig( $signature );
		} else {
			// When no fancy sig used, make sure ~{3,5} get removed.
			$signature = $wgParser->cleanSigInSig( $signature );
		}
		
		return $signature;
	}
	
	static function validateEmail( $email, $alldata ) {
		global $wgUser; // To check
		if ( !$wgUser->isValidEmailAddr( $email ) ) {
			return wfMsgExt( 'invalidemailaddress', 'parseinline' );
		}
		
		global $wgEmailConfirmToEdit;
		if( $wgEmailConfirmToEdit && !$email ) {
			return wfMsgExt( 'noemailtitle', 'parseinline' );
		}
		return true;
	}
}
