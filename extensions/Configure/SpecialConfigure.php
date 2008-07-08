<?php
if ( !defined( 'MEDIAWIKI' ) ) die();

/**
 * Special page allows authorised users to configure the wiki
 *
 * @file
 * @ingroup Extensions
 */
class SpecialConfigure extends ConfigurationPage {
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
		require( dirname( __FILE__ ) . '/Configure.settings-core.php' );
		self::$settings = $settings;
		self::$arrayDefs = $arrayDefs;
		self::$editRestricted = $editRestricted;
		self::$viewRestricted = $viewRestricted;
		self::$notEditableSettings = $notEditableSettings;
		self::$settingsVersion = $settingsVersion;
	}

	/**
	 * Get settings, grouped by section
	 *
	 * @return array
	 */
	public static function staticGetSettings(){
		self::loadSettingsDefs();
		return self::$settings;
	}

	/**
	 * Get the list of settings that are view restricted
	 *
	 * @return array
	 */
	public static function staticGetViewRestricted(){
		self::loadSettingsDefs();
		return self::$viewRestricted;
	}

	/**
	 * Get the list of settings that are edit restricted
	 *
	 * @return array
	 */
	public static function staticGetEditRestricted(){
		self::loadSettingsDefs();
		return self::$editRestricted;
	}

	/**
	 * Get the list of settings that aren't editable by anybody
	 *
	 * @return array
	 */
	public static function staticGetNotEditableSettings(){
		self::loadSettingsDefs();
		return self::$notEditableSettings;
	}

	/**
	 * Return true if the setting is available in this version of MediaWiki
	 *
	 * @param $setting String: setting name
	 * @return bool
	 */
	public static function staticIsSettingAvailable( $setting ){
		global $wgVersion;
		self::loadSettingsDefs();
		if( !array_key_exists( $setting, self::staticGetAllSettings() ) )
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
	public static function staticGetAllSettings(){
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
	public static function staticGetEditableSettings(){
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
	public static function staticGetSettingType( $setting ){
		$settings = self::staticGetAllSettings();
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
	public static function staticGetArrayType( $setting ){
		self::loadSettingsDefs();
		return isset( self::$arrayDefs[$setting] ) ?
			self::$arrayDefs[$setting] :
			null;
	}

	/**
	 * Constructor
	 */
	public function __construct(){
		parent::__construct( 'Configure', 'configure' );
		self::loadSettingsDefs();
	}

	// @{
	// Abstract methods from ConfigurationPage

	protected function isUserAllowedAll(){
		static $allowed = null;
		if( $allowed === null ){
			global $wgUser;
			$allowed = $wgUser->isAllowed( 'configure-all' );
		}
		return $allowed;
	}

	public function userCanEdit( $setting ){
		return ( ( !in_array( $setting, self::staticGetViewRestricted() )
			&& !in_array( $setting, self::staticGetEditRestricted() ) )
			|| $this->isUserAllowedAll() );
	}

	public function userCanRead( $setting ){
		return ( !in_array( $setting, self::staticGetViewRestricted() ) || $this->isUserAllowedAll() );
	}

	protected function doSubmit(){
		global $wgConf, $wgOut, $wgConfigureUpdateCacheEpoch;

		$current = $wgConf->getCurrent( $this->mWiki );
		$settings = $this->importFromRequest();
		$settings += $current;
		if( $wgConfigureUpdateCacheEpoch )
			$settings['wgCacheEpoch'] = max( $settings['wgCacheEpoch'], wfTimestampNow() ); 
		$ok = $wgConf->saveNewSettings( $settings, $this->mWiki );
		$msg = wfMsgNoTrans( $ok ? 'configure-saved' : 'configure-error' );
		$class = $ok ? 'successbox' : 'errorbox';

		$wgOut->addWikiText( "<div class=\"$class\"><strong>$msg</strong></div>" );
	}

	protected function getEditableSettings(){
		return self::staticGetEditableSettings();
	}
	
	protected function getArrayType( $setting ){
		return self::staticGetArrayType( $setting );
	}

	protected function isSettingAvailable( $setting ){
		return self::staticIsSettingAvailable( $setting );
	}

	// @}

	protected function cleanupSetting( $name, $val ){
		switch( $name ){
		case 'wgSharedDB':
		case 'wgLocalMessageCache':
			if( empty( $val ) )
				return null;
			else
				return $val;
		case 'wgExternalDiffEngine':
			if( empty( $val ) )
				return false;
			else
				return $val;
		default:
			return $val;
		}
	}

	/**
	 * Helper function for the diff engine
	 * @param $setting setting name
	 */
	public function isSettingEditable( $setting ){
		return ( self::staticIsSettingAvailable( $setting )
			&& $this->userCanEdit( $setting )
			&& ( self::staticGetSettingType( $setting ) != 'array'
				|| !in_array( self::staticGetArrayType( $setting ), array( 'array', null ) ) ) );
	}

	/**
	 * Show the diff between the current version and the posted version
	 */
	protected function showDiff(){
		global $wgConf, $wgOut;
		$wiki = $this->mWiki;
		$old = array( $wiki => $wgConf->getCurrent( $wiki ) );
		$new = array( $wiki => $this->conf );
		$diff = new CorePreviewConfigurationDiff( $old, $new, array( $wiki ) );
		$diff->setViewCallback( array( $this, 'isSettingEditable' ) );
		$wgOut->addHtml( $diff->getHtml() );
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
				if( !in_array( $setting, self::staticGetEditRestricted() ) && !in_array( $setting, self::staticGetNotEditableSettings() ) )
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
				$first = true;
				foreach( $groups as $group => $settings ){
					$ret .= $this->buildTableHeading( $group, !$first );
					$first = false;
					foreach( $settings as $setting => $type ){
						if( !in_array( $setting, self::staticGetNotEditableSettings() ) )
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
