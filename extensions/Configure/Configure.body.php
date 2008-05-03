<?php
if ( !defined( 'MEDIAWIKI' ) ) die();

/**
 * Special page allows authorised users to configure the wiki
 *
 * @addtogroup Extensions
 */
class SpecialConfigure extends SpecialPage {
	protected static $initialized = false;
	protected static $settings, $restricted, $restrictedGroups,
		$arrayDefs, $notEditableSettings, $settingsVersion;
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
		self::$restricted = $restricted;
		self::$restrictedGroups = $restrictedGroups;
		self::$arrayDefs = $arrayDefs;
		self::$notEditableSettings = $notEditableSettings;
		self::$settingsVersion = $settingsVersion;
	}

	/**
	 * Return true if the setting is available in this version of MediaWiki
	 *
	 * @param string $setting setting name
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
	 * @param string $setting
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
	 * Constructor
	 */
	public function __construct() {
		efConfigureLoadMessages();
		parent::__construct( 'Configure', 'configure' );
		self::loadSettingsDefs();
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
		foreach( self::getEditableSettings() as $name => $type ){
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
							$all = array_diff( $all, User::getImplicitGroups() );
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
	 * @param String $name setting name
	 * @param mixed $val setting value
	 */
	protected function cleanupSetting( $name, $val ){
		switch( $name ){
		case 'wgSharedDB':
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
	 * @param String $type type of the setting
	 * @param String $default default value
	 * @return String xhtml fragment
	 */
	protected function buildInput( $conf, $type, $default ){
		$allowed = true;
		if( in_array( $conf, self::$restricted ) ){
			global $wgUser;
			if( !$wgUser->isAllowed( 'configure-all' ) )
				$allowed = false;
		}
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
				return "<pre>\n" . htmlspecialchars( ( is_array( $default ) ? implode( "\n", $default ) : $default ) ) . "\n</pre>";
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
				else
					$all = array_diff( $all, User::getImplicitGroups() );
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
					$desc = ( $type == 'group-bool' && is_callable( array( 'User', 'getRightDescription' ) ) ) ?
						User::getRightDescription( $right ) :
						$right;
					$row .= '<li>'.Xml::checkLabel( $desc, $id, $id, $checked, $attr ) . "</li>\n";
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
	 * @param String $type type of the setting
	 * @param String $default default value
	 * @return String xhtml fragment
	 */
	protected function buildTableRow( $msg, $conf, $type, $default ){
		global $wgContLang;

		$align = array();
		$align['align'] = $wgContLang->isRtl() ? 'right' : 'left';
		$msgVal = wfMsgExt( $msg, array( 'parseinline' ) );
		if( wfEmptyMsg( $msg, $msgVal ) )
			$msgVal = "\$$conf";
		$td1 = Xml::openElement( 'td', $align ) . $msgVal . '</td>';
		if( $this->isSettingAvailable( $conf ) )
			$td2 = Xml::openElement( 'td', $align ) . $this->buildInput( $conf, $type, $default ) . '</td>';
		else
			$td2 = Xml::openElement( 'td', $align ) . wfMsgExt( 'configure-setting-not-available', array( 'parseinline' ) ) . '</td>';

		return '<tr>' . $td1 . $td2 . "</tr>\n";
	}

	/**
	 * Build the content of the form
	 *
	 * @return xhtml
	 */
	protected function buildAllSettings(){
		global $wgUser;
		$ret = '';
		foreach( self::$settings as $title => $groups ){
			$ret .= Xml::openElement( 'fieldset' ) . "\n" .
				Xml::element( 'legend', null, wfMsgExt( 'configure-section-' . $title, array( 'parseinline' ) ) ) . "\n";
			if( in_array( $title, self::$restrictedGroups ) && !$wgUser->isAllowed( 'configure-all' ) ){
				$ret .= wfMsgExt( 'configure-section-' . $title . '-notallowed', array( 'parseinline' ) );
			} else {
				$ret .= Xml::openElement( 'table' ) . "\n";
				foreach( $groups as $group => $settings ){
					$ret .= $this->buildTableHeading( 'configure-section-' . $group );
					foreach( $settings as $setting => $type ){
						if( !in_array( $setting, self::$notEditableSettings ) )
							$ret .= $this->buildTableRow( 'configure-setting-' . $setting, $setting, $type, $this->getSettingValue( $setting ) );
					}
				}
				$ret .= Xml::closeElement( 'table' ) . "\n";
			}
			$ret .= Xml::closeElement( 'fieldset' );
		}
		return $ret;
	}
}
