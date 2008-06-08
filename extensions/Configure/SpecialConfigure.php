<?php
if ( !defined( 'MEDIAWIKI' ) ) die();

/**
 * Special page allows authorised users to configure the wiki
 *
 * @file
 * @ingroup Extensions
 */
class SpecialConfigure extends SpecialPage {
	protected static $initialized = false;
	protected static $settings, $arrayDefs, $editRestricted, $viewRestricted,
		$notEditableSettings, $settingsVersion;
	protected $conf;

	// Static methods

	/**
	 * Load messages and initialise static variables
	 */
	protected static function loadSettingsDefs(){
		if( self::$initialized )
			return;
		self::$initialized = true;
		require( dirname( __FILE__ ) . '/Configure.settings.php' );
		self::$settings = $settings;
		self::$arrayDefs = $arrayDefs;
		self::$editRestricted = $editRestricted;
		self::$viewRestricted = $viewRestricted;
		self::$notEditableSettings = $notEditableSettings;
		self::$settingsVersion = $settingsVersion;
	}

	/**
	 * Return true if the setting is available in this version of MediaWiki
	 *
	 * @param $setting String: setting name
	 * @return bool
	 */
	public static function isSettingAvailable( $setting ){
		global $wgVersion;
		self::loadSettingsDefs();
		if( !array_key_exists( $setting, self::getAllSettings() ) )
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
	 * Get settings, grouped by section
	 *
	 * @return array
	 */
	public static function getSettings(){
		self::loadSettingsDefs();
		return self::$settings;
	}

	/**
	 * Get a simple array with all config settings
	 *
	 * @return array
	 */
	public static function getAllSettings(){
		static $arr = null;
		if( is_array( $arr ) && !empty( $arr ) )
			return $arr;
		self::loadSettingsDefs();
		$arr = array();
		foreach( self::$settings as $section ){
			foreach( $section as $group ){
				$arr = array_merge( $arr, $group );
			}
		}
		return $arr;
	}

	/**
	 * Get a simple array with all editable config settings
	 *
	 * @return array
	 */
	public static function getEditableSettings(){
		static $arr = null;
		if( is_array( $arr ) && !empty( $arr ) )
			return $arr;
		self::loadSettingsDefs();
		$arr = array();
		foreach( self::$settings as $section ){
			foreach( $section as $group ){
				foreach( $group as $setting => $type ){
					if( !in_array( $setting, self::$notEditableSettings ) )
						$arr[$setting] = $type;
				}
			}
		}
		return $arr;
	}

	/**
	 * Get the type of a setting
	 *
	 * @param $setting String: setting name
	 * @return mixed
	 */
	public static function getSettingType( $setting ){
		$settings = self::getAllSettings();
		if( isset( $settings[$setting] ) )
			return $settings[$setting];
		else
			return false;
	}

	/**
	 * Get the array type of a setting
	 * 
	 * @param $setting String: setting name
	 */
	public static function getArrayType( $setting ){
		self::loadSettingsDefs();
		return isset( self::$arrayDefs[$setting] ) ?
			self::$arrayDefs[$setting] :
			null;
	}

	/**
	 * Constructor
	 */
	public function __construct( $name = 'Configure', $right = 'configure' ) {
		efConfigureLoadMessages();
		parent::__construct( $name, $right );
		self::loadSettingsDefs();
	}

	/**
	 * Show the special page
	 *
	 * @param $par Mixed: parameter passed to the page or null
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

		// Check that the directory exists...
		if( !is_dir( $wgConf->getDir() ) ){
			$msg = wfMsgNoTrans( 'configure-no-directory', $wgConf->getDir() );
			$wgOut->addWikiText( "<div class='errorbox'><strong>$msg</strong></div>" );
			return;
		}

		// And that it's writable by PHP
		if( !is_writable( $wgConf->getDir() ) ){
			$msg = wfMsgNoTrans( 'configure-directory-not-writable', $wgConf->getDir() );
			$wgOut->addWikiText( "<div class='errorbox'><strong>$msg</strong></div>" );
			return;
		}

		$this->outputHeader();

		if( $version = $wgRequest->getVal( 'version' ) ){
			$versions = $wgConf->listArchiveFiles();
			if( in_array( $version, $versions ) ){
				 $conf = $wgConf->getOldSettings( $version );
				 $this->conf = $conf[$wgConf->getWiki()];
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
	 * @param $setting String: setting name
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
	 * Return true if the current user is allowed to configure all settings.
	 * @return bool
	 */
	protected function isUserAllowedAll(){
		static $allowed = null;
		if( $allowed === null ){
			global $wgUser;
			$allowed = $wgUser->isAllowed( 'configure-all' );
		}
		return $allowed;
	}

	/**
	 * Return true if the current user is allowed to configure $setting.
	 * @return bool
	 */
	public function userCanEdit( $setting ){
		return ( ( !in_array( $setting, self::$viewRestricted )
			&& !in_array( $setting, self::$editRestricted ) )
			|| $this->isUserAllowedAll() );
	}

	/**
	 * Return true if the current user is allowed to see $setting.
	 * @return bool
	 */
	public function userCanRead( $setting ){
		return ( !in_array( $setting, self::$viewRestricted ) || $this->isUserAllowedAll() );
	}

	/**
	 * Submit the posted request
	 */
	protected function doSubmit(){
		global $wgConf, $wgOut, $wgRequest;

		if( $wiki = $wgRequest->getVal( 'wpWiki', false ) ){
			if( !$this->isUserAllowedAll() ){
				$msg = wfMsgNoTrans( 'configure-no-transwiki' );
				$wgOut->addWikiText( "<div class='errorbox'><strong>$msg</strong></div>" );
				return;
			}
		}
		$settings = array();
		foreach( self::getEditableSettings() as $name => $type ){
			if( !$this->userCanEdit( $name ) ){
				$settings[$name] = $this->getSettingValue( $name );
				continue;
			}
			switch( $type ){
			case 'array':
				$arrType = self::getArrayType( $name );
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
				case 'ns-simple':
					global $wgContLang;
					$arr = array();
					foreach( $wgContLang->getNamespaces() as $ns => $unused ){
						if( $wgRequest->getCheck( 'wp' . $name . '-ns' . strval( $ns ) ) )
							$arr[] = $ns;
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
					if( isset( $_REQUEST['wp'.$name.'-vals'] ) ){
						$iter = explode( "\n", $_REQUEST['wp'.$name.'-vals'] );
						foreach( $iter as &$group ){
							// Our own Sanitizer::unescapeId() :)
							$group = urldecode( str_replace( array( '.', "\r" ), array( '%', '' ), substr( $group, strlen( $name ) + 3 ) ) );
						}
						unset( $group ); // Unset the reference, just in case
					} else { // No javascript ?
						$iter = array_keys( $this->getSettingValue( 'wgGroupPermissions' ) );
					}
					if( $arrType == 'group-bool' ){
						if( is_callable( array( 'User', 'getAllRights' ) ) ){ // 1.13 +
							$all = User::getAllRights();
						} else {
							foreach( $this->getSettingValue( 'wgGroupPermissions' ) as $rights )
								$all = array_merge( $all, array_keys( $rights ) );
							$all = array_unique( $all );
						}
					} else {
						if( $this->isSettingAvailable( 'wgImplicitGroups' ) ) // 1.12 +
							$all = array_diff( $iter, $this->getSettingValue( 'wgImplicitGroups' ) );
						else
							$all = array_diff( $all, User::getImplicitGroups() );
					}
					foreach( $iter as $group ){
						foreach( $all as $right ){
							$id = 'wp'.$name.'-'.$group.'-'.$right;
							if( $arrType == 'group-bool' ){
								$encId = Sanitizer::escapeId( $id );
								if( $id != $encId ){
									$val = $wgRequest->getCheck( str_replace( '.', '_', $encId ) ) || $wgRequest->getCheck( $encId ) || $wgRequest->getCheck( $id );
								} else {
									$val = $wgRequest->getCheck( $id );
								}
								$settings[$name][$group][$right] = $val;
							} else if( $wgRequest->getCheck( $id ) ){
								$settings[$name][$group][] = $right;
							}
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

			if( isset( $settings[$name] ) ){
				$settings[$name] = $this->cleanupSetting( $name, $settings[$name] );
				if( $settings[$name] === null )
					unset( $settings[$name] );
			}
		}

		$settings['wgCacheEpoch'] = max( $settings['wgCacheEpoch'], wfTimestampNow() ); 
		$ok = $wgConf->saveNewSettings( $settings, $wiki );
		$msg = wfMsgNoTrans( $ok ? 'configure-saved' : 'configure-error' );
		$class = $ok ? 'successbox' : 'errorbox';

		$wgOut->addWikiText( "<div class=\"$class\"><strong>$msg</strong></div>" );
	}

	/**
	 * Cleanup some settings to respect some behaviour of the core
	 *
	 * @param $name String: setting name
	 * @param $val Mixed: setting value
	 * @return Mixed
	 */
	protected function cleanupSetting( $name, $val ){
		switch( $name ){
		case 'wgSharedDB':
		case 'wgLocalMessageCache':
			if( empty( $val ) )
				return null;
			else
				return $val;
		default:
			return $val;
		}
	}

	/**
	 * Show the main form
	 */
	protected function showForm(){
		global $wgOut, $wgUser, $wgRequest;

		if( $wiki = $wgRequest->getVal( 'wiki', false ) ){
			if( !$this->isUserAllowedAll() ){
				$msg = wfMsgNoTrans( 'configure-no-transwiki' );
				$wgOut->addWikiText( "<div class='errorbox'><strong>$msg</strong></div>" );
				return;
			}
		}

		$action = $this->getTitle()->escapeLocalURL();
		# We use <div id="preferences"> to have the tabs like in Special:Preferences
		$wgOut->addHtml(
			$this->buildOldVersionSelect() . "\n" .

			Xml::openElement( 'form', array( 'method' => 'post', 'action' => $action, 'id' => 'configure-form' ) ) . "\n" .
			Xml::openElement( 'div', array( 'id' => 'preferences' ) ) . "\n" .

			$this->buildAllSettings() . "\n" .

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
		$this->injectScriptsAndStyles();
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
	 * Inject JavaScripts and Stylesheets in page output
	 */
	protected function injectScriptsAndStyles() {
		global $wgOut, $wgScriptPath, $wgUseAjax, $wgJsMimeType, $wgConfigureStyleVersion;
		$wgOut->addLink(
			array(
				'rel' => 'stylesheet',
				'type' => 'text/css',
				'href' => "{$wgScriptPath}/extensions/Configure/Configure.css?{$wgConfigureStyleVersion}",
			)
		);
		if( is_callable( array( $wgOut, 'addScriptFile' ) ) ){ # 1.13 +
			$wgOut->addScriptFile( 'prefs.js' );
		}
		if( is_callable( array( 'Xml', 'encodeJsVar' ) ) ){ # 1.9 +
			$add = Xml::encodeJsVar( wfMsg( 'configure-js-add' ) );
			$remove = Xml::encodeJsVar( wfMsg( 'configure-js-remove' ) );
			$removeRow = Xml::encodeJsVar( wfMsg( 'configure-js-remove-row' ) );
			$promptGroup = Xml::encodeJsVar( wfMsg( 'configure-js-prompt-group' ) );
			$groupExists = Xml::encodeJsVar( wfMsg( 'configure-js-group-exists' ) );
		} else {
			$add = '"' . Xml::escapeJsString( wfMsg( 'configure-js-add' ) ). '"';
			$remove = '"' . Xml::escapeJsString( wfMsg( 'configure-js-remove' ) ) . '"';
			$removeRow = '"' . Xml::escapeJsString( wfMsg( 'configure-js-remove-row' ) ) . '"';
			$promptGroup = '"' . Xml::escapeJsString( wfMsg( 'configure-js-prompt-group' ) ) . '"';
			$groupExists = '"' . Xml::escapeJsString( wfMsg( 'configure-js-group-exists' ) ) . '"';
		}
		$ajax = isset( $wgUseAjax ) && $wgUseAjax ? 'true' : 'false';
		$script = array(
			"<script type=\"$wgJsMimeType\">/*<![CDATA[*/",
			"var wgConfigureAdd = {$add};",
			"var wgConfigureRemove = {$remove};",
			"var wgConfigureRemoveRow = {$removeRow};",
			"var wgConfigurePromptGroup = {$promptGroup};",
			"var wgConfigureGroupExists = {$groupExists};",
			"var wgConfigureUseAjax = {$ajax};",
		 	"/*]]>*/</script>",
			"<script type=\"{$wgJsMimeType}\" src=\"{$wgScriptPath}/extensions/Configure/Configure.js?{$wgConfigureStyleVersion}\"></script>",
		);
		$wgOut->addScript( implode( "\n\t\t", $script ) . "\n" );
	} 

	/**
	 * Like before but only for the header
	 *
	 * @param $msg String: name of the message to display
	 * @return String xhtml fragment
	 */
	protected function buildTableHeading( $msg ){
		return '<tr><td colspan="2"><h2>' . wfMsgExt( $msg, array( 'parseinline' ) ) . "</h2></td></tr>\n";
	}

	/**
	 * Build an input for $conf setting with $default as default value
	 *
	 * @param $conf String: name of the setting
	 * @param $type String: type of the setting
	 * @param $default String: default value
	 * @return String xhtml fragment
	 */
	protected function buildInput( $conf, $type, $default ){
		if( !$this->userCanRead( $conf ) )
			return '<span class="disabled">' . wfMsgExt( 'configure-view-not-allowed', array( 'parseinline' ) ) . '</span>';
		$allowed = $this->userCanEdit( $conf );
		if( $type == 'text' || $type == 'int' ){
			if( !$allowed )
				return '<code>' . htmlspecialchars( $default ) . '</code>';
			return Xml::element( 'input', array( 'name' => 'wp' . $conf, 'type' => 'text', 'value' => $default ) );
		}
		if( $type == 'bool' ){
			if( !$allowed )
				return '<code>' . ( $default ? 'true' : 'false' ) . '</code>';
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

			if( $allowed ){
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
			} else {
				return '<code>' . ( isset( $languages[$default] ) ?
					htmlspecialchars( "$default - " . $languages[$default] ) :
					htmlspecialchars( $default ) ) . '</code>';
			}
		}
		if( is_array( $type ) ){
			if( !$allowed )
				return '<code>' . htmlspecialchars( $default ) . '</code>';
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
	 * @param $conf String: setting name
	 * @param $default Mixed: current value (but should be array :)
	 * @param $allowed Boolean
	 */
	protected function buildArrayInput( $conf, $default, $allowed ){
		$type = self::getArrayType( $conf );
		if( $type === null || $type == 'array' )
			return $allowed ? '<span class="array">(array)</span>' : '<span class="array-disabled">(array)</span>';
		if( $type == 'simple' ){
			if( !$allowed ){
				return "<pre>" . htmlspecialchars( ( is_array( $default ) ? implode( "\n", $default ) : $default ) ) . "\n</pre>";
			}
			$text = "<textarea id='wp{$conf}' name='wp{$conf}' cols='30' rows='8'>";
			if( is_array( $default ) )
				$text .= implode( "\n", $default );
			$text .= "</textarea>\n";
			return $text;
		}
		if( $type == 'assoc' ){
			$keydesc = wfMsgHtml( 'configure-desc-key' );
			$valdesc = wfMsgHtml( 'configure-desc-val' );
			$class = ( !$allowed ) ? array( 'class' => 'disabled' ) : array();
			$encConf = htmlspecialchars( $conf );
			$text = "<table class='assoc' id='{$encConf}'>\n<tr><th>{$keydesc}</th><th>{$valdesc}</th></tr>\n";
			if( is_array( $default ) && count( $default ) > 0 ){
				$i = 0;
				foreach( $default as $key => $val ){
					$text .= '<tr>' . Xml::openElement( 'td', $class );
					if( $allowed )
						$text .= Xml::element( 'input', array(
							'name' => 'wp' . $conf . "-key-{$i}",
							'type' => 'text', 'value' => $key
						) ) . "<br/>\n";
					else
						$text .= '<code>' . htmlspecialchars( $key ) . '</code>';
					$text .= '</td>' . Xml::openElement( 'td', $class );
					if( $allowed )
						$text .= Xml::element( 'input', array(
							'name' => 'wp' . $conf . "-val-{$i}",
							'type' => 'text', 'value' => $val
						) ) . "<br/>\n";
					else
						$text .= '<code>' . htmlspecialchars( $val ) . '</code>';
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
					$text .= "<tr><td class='disabled' style='width:10em; height:1.5em;'><hr /></td>" .
						"<td class='disabled' style='width:10em; height:1.5em;'><hr /></td></tr>\n";
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
		if( $type == 'ns-bool' || $type == 'ns-simple' ){
			global $wgContLang;
			$text = '';
			$attr = ( !$allowed ) ? array( 'disabled' => 'disabled' ) : array();
			foreach( $wgContLang->getNamespaces() as $ns => $name ){
				$name = str_replace( '_', ' ', $name );
				if( '' == $name ) {
					$name = wfMsgExt( 'blanknamespace', array( 'parseinline' ) );
				}
				if( $type == 'ns-bool' ){
					$checked = isset( $default[$ns] ) && $default[$ns];
				} else {
					$checked = in_array( $ns, (array)$default );
				}
				$text .= Xml::checkLabel(
					$name,
					"wp{$conf}-ns{$ns}",
					"wp{$conf}-ns{$ns}",
					$checked,
					$attr
				) . "\n";
			}
			return $text;
		}
		if( $type == 'ns-text' ){
			global $wgContLang;
			$nsdesc = wfMsgHtml( 'configure-desc-ns' );
			$valdesc = wfMsgHtml( 'configure-desc-val' );
			$text = "<table class='ns-text'>\n<tr><th>{$nsdesc}</th><th>{$valdesc}</th></tr>\n";
			foreach( $wgContLang->getNamespaces() as $ns => $name ){
				$name = str_replace( '_', ' ', $name );
				if( '' == $name ) {
					$name = wfMsgExt( 'blanknamespace', array( 'parseinline' ) );
				}
				$text .= '<tr><td>'. $name . '</td><td>';
				if( $allowed )
					$text .= Xml::element( 'input', array(
						'name' => "wp{$conf}-ns{$ns}",
						'type' => 'text', 'value' => isset( $default[$ns] ) ? $default[$ns] : ''
					) ) . "\n";
				else
					$text .= htmlspecialchars( isset( $default[$ns] ) ? $default[$ns] : '' );
				$text .= '</td></tr>';
			}
			$text .= '</table>';
			return $text;
		}
		if( $type == 'ns-array' ){
			global $wgContLang;
			$nsdesc = wfMsgHtml( 'configure-desc-ns' );
			$valdesc = wfMsgHtml( 'configure-desc-val' );
			$text = "<table class='ns-array'>\n<tr><th>{$nsdesc}</th><th>{$valdesc}</th></tr>\n";
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
					$text .= "<pre>" . ( isset( $default[$ns] ) ? htmlspecialchars( implode( "\n", $default[$ns] ) ) : '' ) . "\n</pre>";
				$text .= '</td></tr>';
			}
			$text .= '</table>';
			return $text;
		}
		if( $type == 'group-bool' || $type == 'group-array' ){
			$all = array();
			$attr = ( !$allowed ) ? array( 'disabled' => 'disabled' ) : array();
			if( $type == 'group-bool' ){
				if( is_callable( array( 'User', 'getAllRights' ) ) ){ // 1.13 +
						$all = User::getAllRights();
				} else {
					foreach( $default as $rights )
						$all = array_merge( $all, array_keys( $rights ) );
					$all = array_unique( $all );
				}
				$iter = $default;
			} else {
				$all = array_keys( $this->getSettingValue( 'wgGroupPermissions' ) );
				$iter = array();
				foreach( $all as $group )
					$iter[$group] = isset( $default[$group] ) && is_array( $default[$group] ) ? $default[$group] : array();
				if( $this->isSettingAvailable( 'wgImplicitGroups' ) ) // 1.12 +
					$all = array_diff( $all, $this->getSettingValue( 'wgImplicitGroups' ) );
				else
					$all = array_diff( $all, User::getImplicitGroups() );
			}
			$groupdesc = wfMsgHtml( 'configure-desc-group' );
			$valdesc = wfMsgHtml( 'configure-desc-val' );
			$encConf = htmlspecialchars( $conf );
			$text = "<table id= '{$encConf}' class='{$type}'>\n<tr><th>{$groupdesc}</th><th>{$valdesc}</th></tr>\n";
			foreach( $iter as $group => $levs ){
				$row = '<div style="-moz-column-count:2"><ul>';
				foreach( $all as $right ){
					if( $type == 'group-bool' )
						$checked = ( isset( $levs[$right] ) && $levs[$right] );
					else
						$checked = in_array( $right, $levs );
					$id = Sanitizer::escapeId( 'wp'.$conf.'-'.$group.'-'.$right );
					$desc = ( $type == 'group-bool' && is_callable( array( 'User', 'getRightDescription' ) ) ) ?
						User::getRightDescription( $right ) :
						$right;
					$row .= '<li>'.Xml::checkLabel( $desc, $id, $id, $checked, $attr ) . "</li>\n";
				}
				$row .= '</ul></div>';
				$groupName = User::getGroupName( $group );
				$encId = Sanitizer::escapeId( 'wp'.$conf.'-'.$group );
				$text .= "<tr id=\"{$encId}\">\n<td>{$groupName}</td>\n<td>{$row}</td>\n</tr>";
			}
			$text .= '</table>';
			return $text;
		}
	}

	/**
	 * Build a table row for $conf setting with $default as default value
	 *
	 * @parm $msg String: message name to display, use $conf if the message is
	 *            empty
	 * @param $conf String: name of the setting
	 * @param $type String: type of the setting
	 * @param $default String: default value
	 * @return String xhtml fragment
	 */
	protected function buildTableRow( $msg, $conf, $type, $default ){
		global $wgContLang;

		$align = array();
		$align['align'] = $wgContLang->isRtl() ? 'right' : 'left';
		$align['valign'] = 'top';
		$msgVal = wfMsgExt( $msg, array( 'parseinline' ) );
		if( wfEmptyMsg( $msg, $msgVal ) )
			$msgVal = "\$$conf";
		$url = 'http://www.mediawiki.org/wiki/Manual:$' . $conf;
		$link = Xml::element( 'a', array( 'href' => $url, 'class' => 'configure-doc' ), $msgVal );
		$td1 = Xml::openElement( 'td', $align ) . $link . '</td>';
		if( $this->isSettingAvailable( $conf ) )
			$td2 = Xml::openElement( 'td', $align ) . $this->buildInput( $conf, $type, $default ) . '</td>';
		else
			$td2 = Xml::openElement( 'td', $align ) . 
				wfMsgExt( 'configure-setting-not-available', array( 'parseinline' ) ) . '</td>';

		return '<tr>' . $td1 . $td2 . "</tr>\n";
	}

	/**
	 * Return true if all settings in this section are restricted
	 *
	 * @param $sectArr Array: one value of self::$settings array
	 */
	protected function isSectionRestricted( $sectArr ){
		if( $this->isUserAllowedAll() )
			return false;
		$settings = array();
		foreach( $sectArr as $name => $sect ){
			foreach( array_keys( $sect ) as $setting ){
				if( !in_array( $setting, self::$editRestricted ) && !in_array( $setting, self::$notEditableSettings ) )
					return false;
			}
		}
		return true;
	}

	/**
	 * Build the content of the form
	 *
	 * @return xhtml
	 */
	protected function buildAllSettings(){
		$ret = '';
		foreach( self::$settings as $title => $groups ){
			$ret .= Xml::openElement( 'fieldset' ) . "\n" .
				Xml::element( 'legend', null, wfMsgExt( 'configure-section-' . $title, array( 'parseinline' ) ) ) . "\n";
			if( $this->isSectionRestricted( $groups ) ){
				$ret .= wfMsgExt( 'configure-section-' . $title . '-notallowed', array( 'parseinline' ) );
			} else {
				$ret .= Xml::openElement( 'table' ) . "\n";
				foreach( $groups as $group => $settings ){
					$ret .= $this->buildTableHeading( 'configure-section-' . $group );
					foreach( $settings as $setting => $type ){
						if( !in_array( $setting, self::$notEditableSettings ) )
							$ret .= $this->buildTableRow( 'configure-setting-' . $setting, 
								$setting, $type, $this->getSettingValue( $setting ) );
					}
				}
				$ret .= Xml::closeElement( 'table' ) . "\n";
			}
			$ret .= Xml::closeElement( 'fieldset' );
		}
		return $ret;
	}
}
