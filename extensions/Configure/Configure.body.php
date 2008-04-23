<?php
if ( !defined( 'MEDIAWIKI' ) ) die();

/**
 * Special page allows authorised users to configure the wiki
 *
 * @addtogroup Extensions
 */
class SpecialConfigure extends SpecialPage {
	protected static $initialized = false;
	protected static $settings, $restricted, $arrayDefs, $settingsVersion;
	protected $conf;

	/**
	 * Constructor
	 * Load messages and initialise static variables
	 */
	public function __construct() {
		efConfigureLoadMessages();
		parent::__construct( 'Configure', 'configure' );
		if( !self::$initialized ){
			self::$initialized = true;
			require( dirname( __FILE__ ) . '/Configure.settings.php' );
			self::$settings = $settings;
			self::$restricted = $restricted;
			self::$arrayDefs = $arrayDefs;
			self::$settingsVersion = $settingsVersion;
		}
	}

	/**
	 * Show the special page
	 *
	 * @param mixed $par Parameter passed to the page
	 */
	public function execute( $par ) {
		global $wgUser, $wgRequest, $wgOut, $wgConf;

		$this->setHeaders();

		if ( !$this->userCanExecute( $wgUser ) ) {
			$this->displayRestrictionError();
			return;
		}

		// Since efConfigureSetup() should be explicitely called, don't go
		// further if that function wasn't called
		if( !$wgConf instanceof WebConfiguration ){
			$msg = wfMsgNoTrans( 'configure-no-setup' );
			$wgOut->addWikiText( "<div class='errorbox'><strong>$msg</strong></div>" );
			return;
		}

		if( !is_dir( $wgConf->getDir() ) ){
			$msg = wfMsgNoTrans( 'configure-no-directory', $wgConf->getDir() );
			$wgOut->addWikiText( "<div class='errorbox'><strong>$msg</strong></div>" );
			return;
		}

		$this->outputHeader();

		if( $version = $wgRequest->getVal( 'version' ) ){
			$versions = $wgConf->listArchiveFiles();
			if( in_array( $version, $versions ) ){
				 $conf = $wgConf->getOldSettings( $version );
				 $this->conf = $conf['default'];
				 $wgOut->addWikiText( wfMsgNoTrans( 'configure-edit-old' ) );
			} else {
				$msg = wfMsgNoTrans( 'configure-old-not-available', $version );
				$wgOut->addWikiText( "<div class='errorbox'>$msg</div>" );
				return;
			}
		}

		if( $wgRequest->wasPosted() ){
			if( $wgUser->matchEditToken( $wgRequest->getVal( 'wpEditToken' ) ) )
				$this->doSubmit();
			else
				$wgOut->addWikiText( wfMsgNoTrans( 'sessionfailure' ) );
		} else {
			$this->showForm();
		}
	}

	/**
	 * Retrieve the value of $setting
	 * @param string $setting setting name
	 * @return mixed value of $setting
	 */
	protected function getSettingValue( $setting ){
		if( isset( $this->conf[$setting] ) ){
			return $this->conf[$setting];
		} else {
			return isset( $GLOBALS[$setting] ) ? $GLOBALS[$setting] : null;
		}
	}

	/**
	 * Return true if the setting is available in this version of MediaWiki
	 *
	 * @param string $setting setting name
	 * @return bool
	 */
	protected function isSettingAvailable( $setting ){
		global $wgVersion;
		if( !array_key_exists( $setting, self::$settings ) )
			return false;
		if( !array_key_exists( $setting, self::$settingsVersion ) )
			return true;
		foreach( self::$settingsVersion[$setting] as $test ){
			list( $ver, $comp ) = $test;
			if( !version_compare( $wgVersion, $ver, $comp ) )
				return false;
		}
		return true;
	}

	/**
	 * Submit the posted request
	 */
	protected function doSubmit(){
		global $wgConf, $wgOut, $wgRequest, $wgUser;
		$allowedRestricted = $wgUser->isAllowed( 'configure-all' );
		if( $wiki = $wgRequest->getVal( 'wpWiki', false ) ){
			if( !$allowedRestricted ){
				$msg = wfMsgNoTrans( 'configure-no-transwiki' );
				$wgOut->addWikiText( "<div class='errorbox'><strong>$msg</strong></div>" );
				return;
			}
		}
		$settings = array();
		foreach( self::$settings as $name => $type ){
			if( in_array( $name, self::$restricted ) && !$allowedRestricted ){
				$settings[$name] = $this->getSettingValue( $name );
				continue;
			}
			switch( $type ){
			case 'array':
				$arrType = self::$arrayDefs[$name];
				switch( $arrType ){
				case 'simple':
					$text = $wgRequest->getText( 'wp' . $name );
					if( $text == '' )
						$arr = array();
					else
						$arr = explode( "\n", $text );
					$settings[$name] = $arr;
					break;
				case 'assoc':
					$i = 0;
					$arr = array();
					while( isset( $_REQUEST['wp' . $name . '-key-' .$i] ) && isset( $_REQUEST['wp' . $name . '-val-' .$i] ) ){
						$key = $_REQUEST['wp' . $name . '-key-' .$i];
						$val = $_REQUEST['wp' . $name . '-val-' .$i];
						if( $key !== '' || $val !== '' )
							$arr[$key] = $val;
						$i++;
					}
					$settings[$name] = $arr;
					break;
				case 'simple-dual':
					$text = $wgRequest->getText( 'wp' . $name );
					if( $text == '' ){
						$arr = array();
					} else {
						$arr = array();
						foreach( explode( "\n", $text ) as $line ){
							$items = array_map( 'intval', array_map( 'trim', explode( ',', $line ) ) );
							if( count( $items == 2 ) )
								$arr[] = $items;
						}
					}
					$settings[$name] = $arr;
					break;
				case 'ns-bool':
					global $wgContLang;
					$arr = array();
					foreach( $wgContLang->getNamespaces() as $ns => $unused ){
						$arr[$ns] = $wgRequest->getCheck( 'wp' . $name . '-ns' . strval( $ns ) );
					}
					$settings[$name] = $arr;
					break;
				case 'ns-text':
					global $wgContLang;
					$arr = array();
					foreach( $wgContLang->getNamespaces() as $ns => $unused ){
						$arr[$ns] = $wgRequest->getVal( 'wp' . $name . '-ns' . strval( $ns ) );
					}
					$settings[$name] = $arr;
					break;
				case 'ns-array':
					global $wgContLang;
					$arr = array();
					foreach( $wgContLang->getNamespaces() as $ns => $unused ){
						if( $ns < 0 )
							continue;
						$text = $wgRequest->getText( 'wp' . $name . '-ns' . strval( $ns ) );
						if( $text == '' )
							$nsProtection = array();
						else
							$nsProtection = explode( "\n", $text );
						$arr[$ns] = $nsProtection;
					}
					$settings[$name] = $arr;
					break;
				case 'group-bool':
				case 'group-array':
					$all = array();
					$iter = array_keys( $this->getSettingValue( 'wgGroupPermissions' ) );
					if( $arrType == 'group-bool' ){
						foreach( $this->getSettingValue( 'wgGroupPermissions' ) as $rights )
							$all = array_merge( $all, array_keys( $rights ) );
						$all = array_unique( $all );
					} else {
						if( $this->isSettingAvailable( 'wgImplicitGroups' ) )
							$all = array_diff( $iter, $this->getSettingValue( 'wgImplicitGroups' ) );
						else
							$all = $iter;
					}
					foreach( $iter as $group ){
						foreach( $all as $right ){
							$id = 'wp'.$name.'-'.$group.'-'.$right;
							if( $arrType == 'group-bool' )
								$settings[$name][$group][$right] = $wgRequest->getCheck( $id );
							else if( $wgRequest->getCheck( $id ) )
								$settings[$name][$group][] = $right;
						}
					}
					break;
				}
				break;
			case 'text':
			case 'lang':
				$settings[$name] = $wgRequest->getVal( 'wp' . $name );
				break;
			case 'int':
				$settings[$name] = $wgRequest->getInt( 'wp' . $name );
				break;
			case 'bool':
				$settings[$name] = $wgRequest->getCheck( 'wp' . $name );
				break;
			default:
				if( is_array( $type ) ){
					$val = $wgRequest->getVal( 'wp' . $name );
					if( !array_key_exists( $val, $type ) ){
						$perm = implode( ', ', $type );
						throw new MWException( "Value for \$$name setting is not in permitted (given: $val, permitted: $perm)" );
					}
				} else {
					throw new MWException( "Unknown setting type $type (setting name: \$$name)" );
				}
			}
			if( isset( $settings[$name] ) && $settings[$name] === null )
				unset( $settings[$name] );
		}

		$settings['wgCacheEpoch'] = max( $settings['wgCacheEpoch'], wfTimestampNow() ); 
		$ok = $wgConf->saveNewSettings( $settings, $wiki );
		$msg = wfMsgNoTrans( $ok ? 'configure-saved' : 'configure-error' );
		$class = $ok ? 'successbox' : 'errorbox';

		$wgOut->addWikiText( "<div class=\"$class\"><strong>$msg</strong></div>" );
	}

	/**
	 * Show the main form
	 */
	protected function showForm(){
		global $wgOut, $wgUser, $wgRequest;

		if( $wiki = $wgRequest->getVal( 'wiki', false ) ){
			if( !$wgUser->isAllowed( 'configure-all' ) ){
				$msg = wfMsgNoTrans( 'configure-no-transwiki' );
				$wgOut->addWikiText( "<div class='errorbox'><strong>$msg</strong></div>" );
				return;
			}
		}

		$action = $this->getTitle()->escapeLocalURL();
		# We use <div id="preferences"> to have the tabs like in Special:Preferences
		$wgOut->addHtml(
			$this->buildOldVersionSelect() . "\n" .

			Xml::openElement( 'form', array( 'method' => 'post', 'action' => $action ) ) . "\n" .
			Xml::openElement( 'div', array( 'id' => 'preferences' ) ) . "\n" .

			Xml::openElement( 'fieldset' ) . "\n" .
			Xml::element( 'legend', null, wfMsgExt( 'configure-section-general', array( 'parseinline' ) ) ) . "\n" .
			$this->buildGeneralSettings() .
			Xml::closeElement( 'fieldset' ) . "\n" .

			Xml::openElement( 'fieldset' ) . "\n" .
			Xml::element( 'legend', null, wfMsgExt( 'configure-section-db', array( 'parseinline' ) ) ) . "\n" .
			$this->buildDbSettings() .
			Xml::closeElement( 'fieldset' ) . "\n" .

			Xml::openElement( 'fieldset' ) . "\n" .
			Xml::element( 'legend', null, wfMsgExt( 'configure-section-email', array( 'parseinline' ) ) ) . "\n" .
			$this->buildEmailSettings() .
			Xml::closeElement( 'fieldset' ) . "\n" .

			Xml::openElement( 'fieldset' ) . "\n" .
			Xml::element( 'legend', null, wfMsgExt( 'configure-section-localization', array( 'parseinline' ) ) ) . "\n" .
			$this->buildLocalizationSettings() .
			Xml::closeElement( 'fieldset' ) . "\n" .

			Xml::openElement( 'fieldset' ) . "\n" .
			Xml::element( 'legend', null, wfMsgExt( 'configure-section-debug', array( 'parseinline' ) ) ) . "\n" .
			$this->buildDebugSettings() .
			Xml::closeElement( 'fieldset' ) . "\n" .

			Xml::openElement( 'fieldset' ) . "\n" .
			Xml::element( 'legend', null, wfMsgExt( 'configure-section-site', array( 'parseinline' ) ) ) . "\n" .
			$this->buildSiteSettings() .
			Xml::closeElement( 'fieldset' ) . "\n" .

			Xml::openElement( 'fieldset' ) . "\n" .
			Xml::element( 'legend', null, wfMsgExt( 'configure-section-namespaces', array( 'parseinline' ) ) ) . "\n" .
			$this->buildNamespacesSettings() .
			Xml::closeElement( 'fieldset' ) . "\n" .

			Xml::openElement( 'fieldset' ) . "\n" .
			Xml::element( 'legend', null, wfMsgExt( 'configure-section-skin', array( 'parseinline' ) ) ) . "\n" .
			$this->buildSkinSettings() .
			Xml::closeElement( 'fieldset' ) . "\n" .

			Xml::openElement( 'fieldset' ) . "\n" .
			Xml::element( 'legend', null, wfMsgExt( 'configure-section-category', array( 'parseinline' ) ) ) . "\n" .
			$this->buildCategorySettings() .
			Xml::closeElement( 'fieldset' ) . "\n" .

			Xml::openElement( 'fieldset' ) . "\n" .
			Xml::element( 'legend', null, wfMsgExt( 'configure-section-cache', array( 'parseinline' ) ) ) . "\n" .
			$this->buildCacheSettings() .
			Xml::closeElement( 'fieldset' ) . "\n" .

			Xml::openElement( 'fieldset' ) . "\n" .
			Xml::element( 'legend', null, wfMsgExt( 'configure-section-interwiki', array( 'parseinline' ) ) ) . "\n" .
			$this->buildInterwikiSettings() .
			Xml::closeElement( 'fieldset' ) . "\n" .

			Xml::openElement( 'fieldset' ) . "\n" .
			Xml::element( 'legend', null, wfMsgExt( 'configure-section-access', array( 'parseinline' ) ) ) . "\n" .
			$this->buildAccessSettings() .
			Xml::closeElement( 'fieldset' ) . "\n" .

			Xml::openElement( 'fieldset' ) . "\n" .
			Xml::element( 'legend', null, wfMsgExt( 'configure-section-rates', array( 'parseinline' ) ) ) . "\n" .
			$this->buildRateLimitsSettings() .
			Xml::closeElement( 'fieldset' ) . "\n" .

			Xml::openElement( 'fieldset' ) . "\n" .
			Xml::element( 'legend', null, wfMsgExt( 'configure-section-proxy', array( 'parseinline' ) ) ) . "\n" .
			$this->buildProxySettings() .
			Xml::closeElement( 'fieldset' ) . "\n" .

			Xml::openElement( 'fieldset' ) . "\n" .
			Xml::element( 'legend', null, wfMsgExt( 'configure-section-squid', array( 'parseinline' ) ) ) . "\n" .
			$this->buildSquidSettings() .
			Xml::closeElement( 'fieldset' ) . "\n" .

			Xml::openElement( 'fieldset' ) . "\n" .
			Xml::element( 'legend', null, wfMsgExt( 'configure-section-cookie', array( 'parseinline' ) ) ) . "\n" .
			$this->buildCookieSettings() .
			Xml::closeElement( 'fieldset' ) . "\n" .

			Xml::openElement( 'fieldset' ) . "\n" .
			Xml::element( 'legend', null, wfMsgExt( 'configure-section-reduction', array( 'parseinline' ) ) ) . "\n" .
			$this->buildReductionSettings() .
			Xml::closeElement( 'fieldset' ) . "\n" .

			Xml::openElement( 'fieldset' ) . "\n" .
			Xml::element( 'legend', null, wfMsgExt( 'configure-section-upload', array( 'parseinline' ) ) ) . "\n" .
			$this->buildUploadSettings() .
			Xml::closeElement( 'fieldset' ) . "\n" .

			Xml::openElement( 'fieldset' ) . "\n" .
			Xml::element( 'legend', null, wfMsgExt( 'configure-section-images', array( 'parseinline' ) ) ) . "\n" .
			$this->buildImageSettings() .
			Xml::closeElement( 'fieldset' ) . "\n" .

			Xml::openElement( 'fieldset' ) . "\n" .
			Xml::element( 'legend', null, wfMsgExt( 'configure-section-parser', array( 'parseinline' ) ) ) . "\n" .
			$this->buildParserSettings() .
			Xml::closeElement( 'fieldset' ) . "\n" .

			Xml::openElement( 'fieldset' ) . "\n" .
			Xml::element( 'legend', null, wfMsgExt( 'configure-section-specialpages', array( 'parseinline' ) ) ) . "\n" .
			$this->buildSpecialPagesSettings() .
			Xml::closeElement( 'fieldset' ) . "\n" .

			Xml::openElement( 'fieldset' ) . "\n" .
			Xml::element( 'legend', null, wfMsgExt( 'configure-section-users', array( 'parseinline' ) ) ) . "\n" .
			$this->buildUsersSettings() .
			Xml::closeElement( 'fieldset' ) . "\n" .

			Xml::openElement( 'fieldset' ) . "\n" .
			Xml::element( 'legend', null, wfMsgExt( 'configure-section-feed', array( 'parseinline' ) ) ) . "\n" .
			$this->buildFeedSettings() .
			Xml::closeElement( 'fieldset' ) . "\n" .

			Xml::openElement( 'fieldset' ) . "\n" .
			Xml::element( 'legend', null, wfMsgExt( 'configure-section-job', array( 'parseinline' ) ) ) . "\n" .
			$this->buildJobSettings() .
			Xml::closeElement( 'fieldset' ) . "\n" .

			Xml::openElement( 'fieldset' ) . "\n" .
			Xml::element( 'legend', null, wfMsgExt( 'configure-section-extension', array( 'parseinline' ) ) ) . "\n" .
			$this->buildExtensionSettings() .
			Xml::closeElement( 'fieldset' ) . "\n" .

			Xml::openElement( 'fieldset' ) . "\n" .
			Xml::element( 'legend', null, wfMsgExt( 'configure-section-copyright', array( 'parseinline' ) ) ) . "\n" .
			$this->buildCopyrightSettings() .
			Xml::closeElement( 'fieldset' ) . "\n" .

			Xml::openElement( 'fieldset' ) . "\n" .
			Xml::element( 'legend', null, wfMsgExt( 'configure-section-htcp', array( 'parseinline' ) ) ) . "\n" .
			$this->buildHtcpSettings() .
			Xml::closeElement( 'fieldset' ) . "\n" .

			Xml::openElement( 'fieldset' ) . "\n" .
			Xml::element( 'legend', null, wfMsgExt( 'configure-section-misc', array( 'parseinline' ) ) ) . "\n" .
			$this->buildMiscSettings() .
			Xml::closeElement( 'fieldset' ) . "\n" .

			Xml::openElement( 'div', array( 'id' => 'prefsubmit' ) ) . "\n" .
			Xml::openElement( 'div', array() ) . "\n" .
			Xml::element( 'input', array( 'type' => 'submit', 'name' => 'wpSave', 'class' => 'btnSavePrefs', 'value' => wfMsgHtml( 'configure-btn-save' ) ) ) . "\n" .
			Xml::closeElement( 'div' ) . "\n" .
			Xml::closeElement( 'div' ) . "\n" .
			Xml::element( 'input', array( 'type' => 'hidden', 'name' => 'wpEditToken', 'value' => $wgUser->editToken() ) ) . "\n" .
			( $wiki ? Xml::element( 'input', array( 'type' => 'hidden', 'name' => 'wpWiki', 'value' => $wiki ) ) . "\n" : '' ) .
			Xml::closeElement( 'div' ) . "\n" .
			Xml::closeElement( 'form' )
		);
	}

	/**
	 * Build links to old version of the configuration
	 */
	protected function buildOldVersionSelect(){
		global $wgConf, $wgLang, $wgUser;
		$versions = $wgConf->listArchiveFiles();
		if( empty( $versions ) ){
			return wfMsgExt( 'configure-no-old', array( 'parse' ) );
		}
		$text = wfMsgExt( 'configure-old-versions', array( 'parse' ) );
		$text .= "<ul>\n";
		$skin = $wgUser->getSkin();
		$title = $this->getTitle();
		foreach( $versions as $ts ){
			$text .= "<li>" . $skin->makeKnownLinkObj( $title, $wgLang->timeAndDate( $ts ), "version=$ts" ) . "</li>\n";
		}
		$text .= "</ul>";
		return $text;
	}

	/**
	 * Build a beginning of table with a header if requested
	 *
	 * @param String $msg name of the message to display or null
	 * @return String xhtml fragment
	 */
	protected function buildTableStart( $msg = null ){
		$table = "<table>\n";
		if( $msg !== null )
			$table .= $this->buildTableHeading( $msg );
		return $table;
	}

	/**
	 * Like before but only for the header
	 *
	 * @param String $msg name of the message to display
	 * @return String xhtml fragment
	 */
	protected function buildTableHeading( $msg ){
		return '<tr><td colspan="2"><h2>' . wfMsgExt( $msg, array( 'parseinline' ) ) . "</h2></td></tr>\n";
	}

	/**
	 * Build an input for $conf setting with $default as default value
	 *
	 * @param String $conf name of the setting
	 * @param String $default default value
	 * @return String xhtml fragment
	 */
	protected function buildInput( $conf, $default ){
		$allowed = true;
		if( in_array( $conf, self::$restricted ) ){
			global $wgUser;
			if( !$wgUser->isAllowed( 'configure-all' ) )
				$allowed = false;
		}
		$type = self::$settings[$conf];
		if( $type == 'text' || $type == 'int' ){
			if( !$allowed )
				return htmlspecialchars( $default );
			return Xml::element( 'input', array( 'name' => 'wp' . $conf, 'type' => 'text', 'value' => $default ) );
		}
		if( $type == 'bool' ){
			if( !$allowed )
				return wfBoolToStr( $default );
			if( $default )
				$checked = array( 'checked' => 'checked' );
			else
				$checked = array();
			return Xml::element( 'input', array( 'name' => 'wp' . $conf, 'type' => 'checkbox', 'value' => '1' ) + $checked );
		}
		if( $type == 'array' ){
			return $this->buildArrayInput( $conf, $default, $allowed );
		}
		if( $type == 'lang' ){
			// Code taken from Xml.php, Xml::LanguageSelector only available since 1.11 and Xml::option since 1.8
			$languages = Language::getLanguageNames( true );
			if( !array_key_exists( $default, $languages ) ) {
				$languages[$default] = $default;
			}
			ksort( $languages );

			$options = "\n";
			foreach( $languages as $code => $name ) {
				$attribs = array( 'value' => $code );
				if( $code == $default )
					$attribs['selected'] = 'selected';
				$options .= Xml::element( 'option', $attribs, "$code - $name" ) . "\n";
			}

			return Xml::openElement( 'select', array( 'id' => 'wp' . $conf, 'name' => 'wp' . $conf ) ) .
				$options . "</select>";

		}
		if( is_array( $type ) ){
			if( !$allowed )
				return htmlspecialchars( $default );
			$ret = "\n";
			foreach( $type as $val => $name ){
				$ret .= Xml::radioLabel( $name, 'wp'.$conf, $val, 'wp'.$conf.$val, $default == $val ) . "\n";
			}
			return $ret;
		}
	}

	/**
	 * Build an input for an array setting
	 *
	 * @param str $conf setting name
	 * @param mixed $default current value (but should be array :)
	 * @param bool $allowed
	 */
	protected function buildArrayInput( $conf, $default, $allowed ){
		if( !isset( self::$arrayDefs[$conf] ) || self::$arrayDefs[$conf] == 'array' )
			return $allowed ? '<i>(array)</i>' : '<span style="text-decoration: line-through; color: #888; font-style: italic;">(array)</span>'; # FIXME
		$type = self::$arrayDefs[$conf];
		if( $type == 'simple' ){
			if( !$allowed ){
				return "<pre>\n" . htmlspecialchars( implode( "\n", $default ) ) . "\n</pre>";
			}
			$text = "<textarea id='wp{$conf}' name='wp{$conf}' cols='30' rows='8'>";
			if( is_array( $default ) )
				$text .= implode( "\n", $default );
			$text .= "</textarea>\n";
			return $text;
		}
		if( $type == 'assoc' ){
			$text = '<table border="1">';
			if( is_array( $default ) && count( $default ) > 0 ){
				$i = 0;
				foreach( $default as $key => $val ){
					$text .= '<tr><td>';
					if( $allowed )
						$text .= Xml::element( 'input', array(
							'name' => 'wp' . $conf . "-key-{$i}",
							'type' => 'text', 'value' => $key
						) ) . "<br/>\n";
					else
						$text .= htmlspecialchars( $key );
					$text .= '</td><td>';
					if( $allowed )
						$text .= Xml::element( 'input', array(
							'name' => 'wp' . $conf . "-val-{$i}",
							'type' => 'text', 'value' => $val
						) ) . "<br/>\n";
					else
						$text .= htmlspecialchars( $val );
					$text .= '</td></tr>';
					$i++;
				}
			} else {
				if( $allowed ){
					$text .= '<tr><td>';
					$text .= Xml::element( 'input', array(
						'name' => 'wp' . $conf . "-key-0",
						'type' => 'text', 'value' => ''
					) ) . "<br/>\n";
					$text .= '</td><td>';
					$text .= Xml::element( 'input', array(
						'name' => 'wp' . $conf . "-val-0",
						'type' => 'text', 'value' => ''
					) ) . "<br/>\n";
					$text .= '</td></tr>';
				} else {
					$text .= "<tr><td style='width:10em; height:1.5em;'><hr /></td><td style='width:10em; height:1.5em;'><hr /></td></tr>\n";
				}
			}
			$text .= '</table>';
			return $text;
		}
		if( $type == 'simple-dual' ){
			$var = array();
			foreach( $default as $arr ){
				$var[] = implode( ',', $arr );
			}
			if( !$allowed ){
				return "<pre>\n" . htmlspecialchars( implode( "\n", $var ) ) . "\n</pre>";
			}
			$text = "<textarea id='wp{$conf}' name='wp{$conf}' cols='30' rows='8'>";
			if( is_array( $var ) )
				$text .= implode( "\n", $var );
			$text .= "</textarea>\n";
			return $text;
		}
		if( $type == 'ns-bool' ){
			global $wgContLang;
			$text = '';
			foreach( $wgContLang->getNamespaces() as $ns => $name ){
				$name = str_replace( '_', ' ', $name );
				if( '' == $name ) {
					$name = wfMsgExt( 'blanknamespace', array( 'parseinline' ) );
				}
				$text .= Xml::checkLabel( $name, 'wp'.$conf."-ns{$ns}", 'wp'.$conf."-ns{$ns}", ( isset( $default[$ns] ) && $default[$ns] ) ) . "\n";
			}
			return $text;
		}
		if( $type == 'ns-text' ){
			global $wgContLang;
			$text = '<table border="1">';
			foreach( $wgContLang->getNamespaces() as $ns => $name ){
				$name = str_replace( '_', ' ', $name );
				if( '' == $name ) {
					$name = wfMsgExt( 'blanknamespace', array( 'parseinline' ) );
				}
				$text .= '<tr><td>'. $name . '</td><td>';
				$text .= Xml::element( 'input', array(
					'name' => "wp{$conf}-ns{$ns}",
					'type' => 'text', 'value' => isset( $default[$ns] ) ? $default[$ns] : ''
				) ) . "<br/>\n";
				$text .= '</td></tr>';
			}
			$text .= '</table>';
			return $text;
		}
		if( $type == 'ns-array' ){
			global $wgContLang;
			$text = '<table border="1">';
			foreach( $wgContLang->getNamespaces() as $ns => $name ){
				if( $ns < 0 )
					continue;
				$name = str_replace( '_', ' ', $name );
				if( '' == $name ) {
					$name = wfMsgExt( 'blanknamespace', array( 'parseinline' ) );
				}
				$text .= '<tr><td>'. $name . '</td><td>';
				if( $allowed )
					$text .= Xml::openElement( 'textarea', array(
						'name' => "wp{$conf}-ns{$ns}",
						'id' => "wp{$conf}-ns{$ns}",
						'cols' => 30,
						'rows' => 5, ) ) .
					( isset( $default[$ns] ) ? implode( "\n", $default[$ns] ) : '' ) .
					Xml::closeElement( 'textarea' ) . "<br/>\n";
				else 
					$text .= "<pre>\n" . ( isset( $default[$ns] ) ? htmlspecialchars( implode( "\n", $default[$ns] ) ) : '' ) . "\n</pre>";
				$text .= '</td></tr>';
			}
			$text .= '</table>';
			return $text;
		}
		if( $type == 'group-bool' || $type == 'group-array' ){
			$all = array();
			$attr = ( !$allowed ) ? array( 'disabled' => 'disabled' ) : array();
			if( $type == 'group-bool' ){
				foreach( $default as $rights )
					$all = array_merge( $all, array_keys( $rights ) );
				$all = array_unique( $all );
				$iter = $default;
			} else {
				$all = array_keys( $this->getSettingValue( 'wgGroupPermissions' ) );
				$iter = array();
				foreach( $all as $group )
					$iter[$group] = isset( $default[$group] ) && is_array( $default[$group] ) ? $default[$group] : array();
				if( $this->isSettingAvailable( 'wgImplicitGroups' ) )
					$all = array_diff( $all, $this->getSettingValue( 'wgImplicitGroups' ) );
			}
			$text = '<table border="1" cellpadding="1">';
			foreach( $iter as $group => $levs ){
				$row = '<div style="-moz-column-count:2"><ul>';
				foreach( $all as $right ){
					if( $type == 'group-bool' )
						$checked = ( isset( $levs[$right] ) && $levs[$right] );
					else
						$checked = in_array( $right, $levs );
					$id = Sanitizer::escapeId( 'wp'.$conf.'-'.$group.'-'.$right );
					$row .= '<li>'.Xml::checkLabel( $right, $id, $id, $checked, $attr ) . "</li>\n";
				}
				$row .= '</ul></div>';
				$groupName = User::getGroupName( $group );
				$text .= "<tr>\n<td>$groupName</td>\n<td>$row</td>\n</tr>";
			}
			$text .= '</table>';
			return $text;
		}
	}

	/**
	 * Build a table row for $conf setting with $default as default value
	 *
	 * @parm String $msg message name to display, use $conf if the message is
	 *                   empty
	 * @param String $conf name of the setting
	 * @param String $default default value
	 * @return String xhtml fragment
	 */
	protected function buildTableRow( $msg, $conf, $default ){
		global $wgContLang;

		$align = array();
		$align['align'] = $wgContLang->isRtl() ? 'right' : 'left';
		$msgVal = wfMsgExt( $msg, array( 'parseinline' ) );
		if( wfEmptyMsg( $msg, $msgVal ) )
			$msgVal = "\$$conf";
		$td1 = Xml::openElement( 'td', $align ) . $msgVal . '</td>';
		if( $this->isSettingAvailable( $conf ) )
			$td2 = Xml::openElement( 'td', $align ) . $this->buildInput( $conf, $default ) . '</td>';
		else
			$td2 = Xml::openElement( 'td', $align ) . wfMsgExt( 'configure-setting-not-available', array( 'parseinline' ) ) . '</td>';

		return '<tr>' . $td1 . $td2 . "</tr>\n";
	}

	/**
	 * Simple wrapper for self::buildTableRow()
	 *
	 * @param String $setting setting name
	 * @return String xhtml fragment
	 */
	protected function buildSimpleSetting( $setting ){
		return $this->buildTableRow( 'configure-setting-' . $setting, $setting, $this->getSettingValue( $setting ) );
	}

	/**
	 * Like self::buildSimpleSetting() but accepts an array of settings
	 *
	 * @param array $settingArr array of settings name
	 * @return string html
	 */
	protected function buildSimpleSettingArray( $settingArr ){
		$text = '';
		foreach( $settingArr as $setting ){
			$text .= $this->buildSimpleSetting( $setting );
		}
		return $text;
	}

	private function buildGeneralSettings(){
		$out = $this->buildTableStart( 'configure-section-general' );
		$out .= $this->buildSimpleSetting( 'wgSitename' );
		$out .= $this->buildTableHeading( 'configure-section-paths' );
		$arr = array( 'wgAppleTouchIcon', 'wgArticlePath', 'wgDiff3', 'wgFavicon',
			'wgLogo', 'wgMathDirectory', 'wgMathPath', 'wgRedirectScript', 'wgScriptExtension',
			'wgScriptPath', 'wgStyleDirectory', 'wgStylePath', 'wgTmpDirectory', 'wgUsePathInfo',
			'wgUploadNavigationUrl', 'wgVariantArticlePath' );
		$out .= $this->buildSimpleSettingArray( $arr );
		$out .= "</table>\n";
		return $out;
	}

	private function buildDbSettings(){
		global $wgUser;
		if( !$wgUser->isAllowed( 'configure-all' ) )
			return wfMsgExt( 'configure-section-db-notallowed', array( 'parse' ) );
		$out = $this->buildTableStart( 'configure-section-db' );
		$arr = array( 'wgAllDBsAreLocalhost', 'wgCheckDBSchema',  'wgDBAvgStatusPoll',
			'wgDBClusterTimeout', 'wgDBminWordLen', 'wgDBmwschema', 'wgDBmysql5',
			'wgDBprefix', 'wgDBservers', 'wgDBTableOptions', 'wgDBtransactions',
			'wgDBts2schema', 'wgDBtype', 'wgLBFactoryConf', 'wgDefaultExternalStore',
			'wgLocalDatabases', 'wgMasterWaitTimeout', 'wgSearchType', 'wgSlaveLagCritical',
			'wgSlaveLagWarning', 'wgExternalServers' );
		$out .= $this->buildSimpleSettingArray( $arr );
		$out .= "</table>\n";
		return $out;
	}

	private function buildEmailSettings(){
		$out = $this->buildTableStart( 'configure-section-email' );
		$arr = array( 'wgEmailAuthentication', 'wgEmergencyContact', 'wgEnableEmail',
			'wgEnableUserEmail', 'wgNoReplyAddress', 'wgPasswordSender', 'wgSMTP',
			'wgUserEmailUseReplyTo' );
		$out .= $this->buildSimpleSettingArray( $arr );
		$out .= $this->buildTableHeading( 'configure-section-enotif' );
		$arr = array( 'wgEnotifFromEditor', 'wgEnotifImpersonal', 'wgEnotifMaxRecips',
			'wgEnotifMinorEdits', 'wgEnotifRevealEditorAddress', 'wgEnotifUseJobQ',
			'wgEnotifUserTalk', 'wgEnotifWatchlist', 'wgShowUpdatedMarker',
			'wgUsersNotifedOnAllChanges', 'wgUsersNotifiedOnAllChanges' );
		$out .= $this->buildSimpleSettingArray( $arr );
		$out .= "</table>\n";
		return $out;
	}

	private function buildLocalizationSettings(){
		$out = $this->buildTableStart( 'configure-section-localization' );
		$arr = array( 'wgAmericanDates', 'wgDisableLangConversion', 'wgForceUIMsgAsContentMsg',
			'wgInterwikiMagic', 'wgLanguageCode', 'wgLegacyEncoding', 'wgLocaltimezone',
			'wgLocalTZoffset', 'wgLoginLanguageSelector', 'wgTranslateNumerals',
			'wgUseDatabaseMessages', 'wgUseDynamicDates', 'wgUseZhdaemon', 'wgZhdaemonHost',
			'wgZhdaemonPort' );
		$out .= $this->buildSimpleSettingArray( $arr );
		$out .= $this->buildTableHeading( 'configure-section-html' );
		$arr = array( 'wgDocType', 'wgDTD', 'wgMimeType', 'wgXhtmlDefaultNamespace', 'wgXhtmlNamespaces' );
		$out .= $this->buildSimpleSettingArray( $arr );
		$out .= "</table>\n";
		return $out;
	}

	private function buildDebugSettings(){
		$out = $this->buildTableStart( 'configure-section-debug' );
		$arr = array( 'wgColorErrors', 'wgDebugComments', 'wgDebugDumpSql', 'wgDebugLogFile',
			'wgDebugLogGroups', 'wgDebugRawPage', 'wgDebugRedirects', 'wgLogQueries',
			'wgShowSQLErrors', 'wgStatsMethod' );
		$out .= $this->buildSimpleSettingArray( $arr );
		$out .= $this->buildTableHeading( 'configure-section-profiling' );
		$arr = array( 'wgDebugFunctionEntry', 'wgDebugProfiling', 'wgDebugSquid',
			'wgProfileCallTree', 'wgProfileLimit', 'wgProfileOnly', 'wgProfilePerHost',
			'wgProfileToDatabase', 'wgUDPProfilerHost', 'wgUDPProfilerPort' );
		$out .= $this->buildSimpleSettingArray( $arr );
		$out .= "</table>\n";
		return $out;
	}

	private function buildSiteSettings(){
		$out = $this->buildTableStart( 'configure-section-site' );
		$arr = array( 'wgAllowUserCss', 'wgAllowUserJs', 'wgDefaultUserOptions',
			'wgCapitalLinks', 'wgDefaultLanguageVariant', 'wgDefaultRobotPolicy',
			'wgExtraLanguageNames', 'wgExtraSubtitle', 'wgHideInterlanguageLinks',
			'wgLegalTitleChars', 'wgNoFollowLinks', 'wgPageShowWatchingUsers',
			'wgPageShowWatchingUsers', 'wgRestrictionLevels', 'wgRestrictionTypes',
			'wgSiteNotice', 'wgSiteSupportPage', 'wgUrlProtocols', 'wgUseSiteCss',
			'wgUseSiteJs' );
		$out .= $this->buildSimpleSettingArray( $arr );
		$out .= $this->buildTableHeading( 'configure-section-ajax' );
		$arr = array( 'wgUseAjax', 'wgAjaxSearch', 'wgAjaxUploadDestCheck',
			'wgAjaxWatch', 'wgLivePreview' );
		$out .= $this->buildSimpleSettingArray( $arr );
		$out .= "</table>\n";
		return $out;
	}

	private function buildNamespacesSettings(){
		$out = $this->buildTableStart( 'configure-section-namespaces' );
		$arr = array( 'wgContentNamespaces', 'wgExtraNamespaces', 'wgMetaNamespace',
			'wgMetaNamespaceTalk', 'wgNamespaceAliases', 'wgNamespaceProtection',
			'wgNamespaceRobotPolicies', 'wgNamespacesToBeSearchedDefault',
			'wgNamespacesWithSubpages', 'wgNoFollowNsExceptions', 'wgNonincludableNamespaces',
			'wgArticleRobotPolicies' );
		$out .= $this->buildSimpleSettingArray( $arr );
		$out .= "</table>\n";
		return $out;
	}

	private function buildSkinSettings(){
		$out = $this->buildTableStart( 'configure-section-skin' );
		$arr = array( 'wgDefaultSkin', 'wgSkipSkin', 'wgSkipSkins', 'wgValidSkinNames' );
		$out .= $this->buildSimpleSettingArray( $arr );
		$out .= "</table>\n";
		return $out;
	}

	private function buildCategorySettings(){
		$out = $this->buildTableStart( 'configure-section-category' );
		$arr = array( 'wgCategoryMagicGallery', 'wgCategoryPagingLimit', 'wgUseCategoryBrowser' );
		$out .= $this->buildSimpleSettingArray( $arr );
		$out .= "</table>\n";
		return $out;
	}

	private function buildCacheSettings(){
		$out = $this->buildTableStart( 'configure-section-cache' );
		$arr = array( 'wgCacheEpoch', 'wgCachePages', 'wgForcedRawSMaxage', 'wgMainCacheType',
			'wgQueryCacheLimit', 'wgRevisionCacheExpiry', 'wgThumbnailEpoch', 'wgTranscludeCacheExpiry',
			'wgUseFileCache', 'wgFileCacheDirectory', 'wgUseGzip' );
		$out .= $this->buildSimpleSettingArray( $arr );
		$out .= $this->buildTableHeading( 'configure-section-pcache' );
		$arr = array( 'wgEnableParserCache', 'wgEnableSidebarCache', 'wgParserCacheType',
			'wgSidebarCacheExpiry' );
		$out .= $this->buildSimpleSettingArray( $arr );
		$out .= $this->buildTableHeading( 'configure-section-messagecache' );
		$arr = array( 'wgMessageCacheType', 'wgLocalMessageCache', 'wgMsgCacheExpiry',
			'wgCachedMessageArrays', 'wgCheckSerialized', 'wgLocalMessageCacheSerialized',
			'wgMaxMsgCacheEntrySize' );
		$out .= $this->buildSimpleSettingArray( $arr );
		$out .= $this->buildTableHeading( 'configure-section-memcached' );
		$arr = array( 'wgLinkCacheMemcached', 'wgMemCachedDebug', 'wgMemCachedPersistent',
			'wgMemCachedServers', 'wgSessionsInMemcached' );
		$out .= $this->buildSimpleSettingArray( $arr );
		$out .= "</table>\n";
		return $out;
	}

	private function buildInterwikiSettings(){
		$out = $this->buildTableStart( 'configure-section-interwiki' );
		$arr = array( 'wgEnableScaryTranscluding', 'wgImportSources', 'wgInterwikiCache',
			'wgInterwikiExpiry', 'wgInterwikiFallbackSite', 'wgInterwikiScopes', 'wgLocalInterwiki' );
		$out .= $this->buildSimpleSettingArray( $arr );
		$out .= "</table>\n";
		return $out;
	}

	private function buildAccessSettings(){
		$out = $this->buildTableStart( 'configure-section-access' );
		$arr = array( 'wgAutopromote', 'wgAccountCreationThrottle', 'wgAllowPageInfo',
			'wgAutoblockExpiry', 'wgDeleteRevisionsLimit', 'wgDisabledActions',
			'wgEmailConfirmToEdit', 'wgEnableCascadingProtection', 'wgEnableAPI',
			'wgEnableWriteAPI', 'wgImplicitGroups', 'wgPasswordSalt', 'wgReadOnly',
			'wgReadOnlyFile', 'wgWhitelistRead' );
		$out .= $this->buildSimpleSettingArray( $arr );
		$out .= $this->buildTableHeading( 'configure-section-groups' );
		$arr = array( 'wgGroupPermissions', 'wgAddGroups', 'wgRemoveGroups', 'wgGroupsAddToSelf',
			'wgGroupsRemoveFromSelf' );
		$out .= $this->buildSimpleSettingArray( $arr );
		$out .= $this->buildTableHeading( 'configure-section-block' );
		$arr = array(  'wgBlockAllowsUTEdit', 'wgSysopEmailBans', 'wgSysopRangeBans',
			'wgSysopUserBans' );
		$out .= $this->buildSimpleSettingArray( $arr );
		$out .= "</table>\n";
		return $out;
	}

	private function buildRateLimitsSettings(){
		$out = $this->buildTableStart( 'configure-section-rates' );
		$arr = array( 'wgRateLimitLog', 'wgRateLimits', 'wgRateLimitsExcludedGroups' );
		$out .= $this->buildSimpleSettingArray( $arr );
		$out .= "</table>\n";
		return $out;
	}

	private function buildProxySettings(){
		$out = $this->buildTableStart( 'configure-section-proxy' );
		$arr = array( 'wgBlockOpenProxies', 'wgEnableSorbs', 'wgProxyList', 'wgProxyMemcExpiry',
			'wgProxyPorts', 'wgProxyScriptPath', 'wgProxyWhitelist', 'wgSecretKey' );
		$out .= $this->buildSimpleSettingArray( $arr );
		$out .= "</table>\n";
		return $out;
	}

	private function buildSquidSettings(){
		$out = $this->buildTableStart( 'configure-section-squid' );
		$arr = array( 'wgInternalServer', 'wgMaxSquidPurgeTitles', 'wgSquidMaxage',
			'wgSquidServers', 'wgSquidServersNoPurge', 'wgUseESI', 'wgUseSquid' );
		$out .= $this->buildSimpleSettingArray( $arr );
		$out .= "</table>\n";
		return $out;
	}

	private function buildCookieSettings(){
		$out = $this->buildTableStart( 'configure-section-cookie' );
		$arr = array( 'wgCookieDomain', 'wgCookieExpiration', 'wgCookieHttpOnly',
			'wgCookiePath', 'wgCookieSecure', 'wgDisableCookieCheck', 'wgSessionName' );
		$out .= $this->buildSimpleSettingArray( $arr );
		$out .= "</table>\n";
		return $out;
	}

	private function buildReductionSettings(){
		$out = $this->buildTableStart( 'configure-section-reduction' );
		$arr = array( 'wgDisableAnonTalk', 'wgDisableCounters', 'wgDisableQueryPages',
			'wgDisableQueryPageUpdate', 'wgDisableSearchContext', 'wgDisableSearchUpdate',
			'wgDisableTextSearch', 'wgMiserMode', 'wgShowHostnames', 'wgUseDumbLinkUpdate',
			'wgWantedPagesThreshold' );
		$out .= $this->buildSimpleSettingArray( $arr );
		$out .= "</table>\n";
		return $out;
	}

	private function buildUploadSettings(){
		$out = $this->buildTableStart( 'configure-section-upload' );
		$arr = array( 'wgAjaxLicensePreview', 'wgAllowCopyUploads', 'wgCheckFileExtensions',
			'wgEnableUploads', 'wgFileBlacklist', 'wgFileExtensions', 'wgFileStore',
			'wgLocalFileRepo', 'wgRemoteUploads', 'wgStrictFileExtensions', 'wgUploadSizeWarning',
			'wgMaxUploadSize', 'wgHTTPTimeout', 'wgHTTPProxy', 'wgSaveDeletedFiles' );
		$out .= $this->buildSimpleSettingArray( $arr );
		$out .= $this->buildTableHeading( 'configure-section-sharedupload' );
		$out .= $this->buildSimpleSetting( 'wgForeignFileRepos' );
		$out .= $this->buildTableHeading( 'configure-section-mime' );
		$arr = array( 'wgLoadFileinfoExtension', 'wgMimeDetectorCommand', 'wgMimeInfoFile',
			'wgMimeTypeFile', 'wgVerifyMimeType', 'wgMimeTypeBlacklist' );
		$out .= $this->buildSimpleSettingArray( $arr );
		$out .= "</table>\n";
		return $out;
	}

	private function buildImageSettings(){
		$out = $this->buildTableStart( 'configure-section-images' );
		$arr = array( 'wgDjvuPostProcessor', 'wgDjvuRenderer', 'wgDjvuToXML',
			'wgGenerateThumbnailOnParse', 'wgFileRedirects', 'wgIgnoreImageErrors',
			'wgImageLimits', 'wgImageMagickConvertCommand', 'wgMaxImageArea', 'wgMediaHandlers',
			'wgThumbnailScriptPath', 'wgThumbUpright', 'wgUseImageMagick', 'wgShowEXIF',
			'wgThumbLimits', 'wgTrustedMediaFormats' );
		$out .= $this->buildSimpleSettingArray( $arr );
		$out .= $this->buildTableHeading( 'configure-section-svg' );
		$arr = array( 'wgAllowTitlesInSVG', 'wgSVGConverter', 'wgSVGConverterPath',
			'wgSVGConverters', 'wgSVGMaxSize' );
		$out .= $this->buildSimpleSettingArray( $arr );
		$out .= $this->buildTableHeading( 'configure-section-antivirus' );
		$arr = array( 'wgAntivirus', 'wgAntivirusRequired', 'wgAntivirusSetup' );
		$out .= $this->buildSimpleSettingArray( $arr );
		$out .= "</table>\n";
		return $out;
	}

	private function buildParserSettings(){
		$out = $this->buildTableStart( 'configure-section-parser' );
		$arr = array( 'wgAllowDisplayTitle', 'wgAllowExternalImages', 'wgAllowExternalImagesFrom',
			'wgExpensiveParserFunctionLimit', 'wgMaxPPNodeCount', 'wgMaxPPExpandDepth',
			'wgParserConf', 'wgParserCacheExpireTime' );
		$out .= $this->buildSimpleSettingArray( $arr );
		$out .= $this->buildTableHeading( 'configure-section-html' );
		$arr = array( 'wgRawHtml', 'wgUserHtml' );
		$out .= $this->buildSimpleSettingArray( $arr );
		$out .= $this->buildTableHeading( 'configure-section-tex' );
		$arr = array( 'wgTexvc', 'wgUseTeX' );
		$out .= $this->buildSimpleSettingArray( $arr );
		$out .= $this->buildTableHeading( 'configure-section-tidy' );
		$arr = array( 'wgAlwaysUseTidy', 'wgDebugTidy', 'wgTidyBin', 'wgTidyConf',
			'wgTidyInternal', 'wgTidyOpts', 'wgUseTidy', 'wgValidateAllHtml' );
		$out .= $this->buildSimpleSettingArray( $arr );
		$out .= "</table>\n";
		return $out;
	}

	private function buildSpecialPagesSettings(){
		$out = $this->buildTableStart( 'configure-section-specialpages' );
		$arr = array( 'wgAllowSpecialInclusion', 'wgExportAllowHistory', 'wgCountCategorizedImagesAsUsed',
			'wgExportAllowListContributors', 'wgExportMaxHistory', 'wgImportTargetNamespace',
			'wgLogRestrictions', 'wgMaxRedirectLinksRetrieved', 'wgUseNPPatrol', 'wgSortSpecialPages' );
		$out .= $this->buildSimpleSettingArray( $arr );
		$out .= $this->buildTableHeading( 'configure-section-recentchanges' );
		$arr = array( 'wgAllowCategorizedRecentChanges', 'wgPutIPinRC', 'wgRCChangedSizeThreshold',
			'wgRCMaxAge', 'wgRCShowChangedSize', 'wgRCShowWatchingUsers', 'wgUseRCPatrol',
			'wgRC2UDPAddress', 'wgRC2UDPPort', 'wgRC2UDPPrefix' );
		$out .= $this->buildSimpleSettingArray( $arr );
		$out .= "</table>\n";
		return $out;
	}

	private function buildUsersSettings(){
		$out = $this->buildTableStart( 'configure-section-users' );
		$arr = array( 'wgAutoConfirmAge', 'wgAutoConfirmCount', 'wgAllowRealName',
			'wgMaxNameChars', 'wgMinimalPasswordLength', 'wgMaxSigChars', 'wgPasswordReminderResendTime',
			'wgReservedUsernames', 'wgBrowserBlackList' );
		$out .= $this->buildSimpleSettingArray( $arr );
		$out .= "</table>\n";
		return $out;
	}

	private function buildFeedSettings(){
		$out = $this->buildTableStart( 'configure-section-feed' );
		$arr = array( 'wgFeed', 'wgFeedCacheTimeout', 'wgFeedDiffCutoff', 'wgFeedLimit' );
		$out .= $this->buildSimpleSettingArray( $arr );
		$out .= "</table>\n";
		return $out;
	}

	private function buildCopyrightSettings(){
		$out = $this->buildTableStart( 'configure-section-copyright' );
		$arr = array( 'wgCheckCopyrightUpload', 'wgCopyrightIcon', 'wgEnableCreativeCommonsRdf',
			'wgEnableDublinCoreRdf', 'wgMaxCredits', 'wgRightsIcon', 'wgRightsPage',
			'wgRightsText', 'wgRightsUrl', 'wgShowCreditsIfMax', 'wgUseCopyrightUpload' );
		$out .= $this->buildSimpleSettingArray( $arr );
		$out .= "</table>\n";
		return $out;
	}

	private function buildJobSettings(){
		$out = $this->buildTableStart( 'configure-section-job' );
		$arr = array( 'wgJobRunRate', 'wgUpdateRowsPerJob' );
		$out .= $this->buildSimpleSettingArray( $arr );
		$out .= "</table>\n";
		return $out;
	}

	private function buildExtensionSettings(){
		$out = $this->buildTableStart( 'configure-section-extension' );
		$arr = array( 'wgAllowSlowParserFunctions', 'wgDisableInternalSearch',
			'wgExternalStores', 'wgSearchForwardUrl' );
		$out .= $this->buildSimpleSettingArray( $arr );
		$out .= "</table>\n";
		return $out;
	}

	private function buildHtcpSettings(){
		$out = $this->buildTableStart( 'configure-section-htcp' );
		$arr = array( 'wgHTCPMulticastAddress', 'wgHTCPMulticastTTL', 'wgHTCPPort' );
		$out .= $this->buildSimpleSettingArray( $arr );
		$out .= "</table>\n";
		return $out;
	}

	private function buildMiscSettings(){
		$out = $this->buildTableStart( 'configure-section-misc' );
		$arr = array( 'wgAntiLockFlags', 'wgBreakFrames', 'wgClockSkewFudge', 'wgCommandLineDarkBg',
			'wgCompressRevisions', 'wgDisableHardRedirects', 'wgDisableOutputCompression',
			'wgEnableMWSuggest', 'wgExternalDiffEngine', 'wgExtraRandompageSQL',
			'wgGoToEdit', 'wgGrammarForms', 'wgHitcounterUpdateFreq', 'wgJsMimeType',
			'wgMaxArticleSize', 'wgMaxShellFileSize', 'wgMaxShellMemory', 'wgMaxTocLevel',
			'wgMWSuggestTemplate', 'wgOpenSearchTemplate', 'wgRedirectSources',
			'wgShowIPinHeader', 'wgSpamRegex', 'wgUpdateRowsPerQuery', 'wgUseCommaCount',
			'wgUseETag', 'wgUseExternalEditor' );
		$out .= $this->buildSimpleSettingArray( $arr );
		$out .= "</table>\n";
		return $out;
	}
}
