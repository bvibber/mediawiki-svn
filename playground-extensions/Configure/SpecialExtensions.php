<?php
if ( !defined( 'MEDIAWIKI' ) ) die();

/**
 * Special page that allows authorised users to configure extensions
 *
 * @ingroup Extensions
 */
class SpecialExtensions extends ConfigurationPage {
	static $extensions;
	static $initialized = false;

	/**
	 * Load messages and initialise static variables
	 */
	protected static function loadSettingsDefs(){
		if( self::$initialized )
			return;
		self::$initialized = true;
		require( dirname( __FILE__ ) . '/Configure.settings-ext.php' );
		self::$extensions = $extensions;
	}

	// Specifc to extensions

	/**
	 * Get an array of WebExtensions objects
	 *
	 * @return array
	 */
	public static function staticGetAllExtensionsObjects(){
		static $list = array();
		if( !empty( $list ) )
			return $list;
		self::loadSettingsDefs();
		foreach( self::$extensions as $ext ){
			$ext = new WebExtension( $ext );
			if( $ext->isInstalled() ){
				$list[] = $ext;
			}
		}
		return $list;
	}

	/**
	 * Get a 3D array of settings
	 *
	 * @return array
	 */
	public static function staticGetSettings(){
		static $arr = array();
		if( count( $arr ) )
			return $arr;

		foreach( self::staticGetAllExtensionsObjects() as $ext ){
			$name = $ext->getName();
			if( count( $ext->getSettings() ) )
				$arr['mw-extensions'][$name] = $ext->getSettings();
		}
		return $arr;
	}

	/**
	 * Get the list of all settings that can be modified
	 *
	 * @return array
	 */
	public static function staticGetEditableSettings(){
		static $list = array();
		if( !empty( $list ) )
			return $list;
		foreach( self::staticGetAllExtensionsObjects() as $ext ){
			$list += $ext->getSettings();
		}
		return $list;
	}
	
	/**
	 * Get the list of all arrays settings, mapping setting name to its type
	 *
	 * @return array
	 */
	public static function staticGetArrayDefs(){
		static $list = array();
		if( !empty( $list ) )
			return $list;
		foreach( self::staticGetAllExtensionsObjects() as $ext ){
			$list += $ext->getArrayDefs();
		}
		return $list;
	}

	/**
	 * Get the array type of $setting
	 *
	 * @param $setting setting name
	 * @return string
	 */
	public static function staticGetArrayType( $setting ){
		$list = self::staticGetArrayDefs();
		return isset( $list[$setting] ) ? $list[$setting] : null;
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct( 'Extensions', 'extensions' );
		self::$initialized = false;
		self::loadSettingsDefs();
	}

	// @{
	// Abstract methods from ConfigurationPage

	/**
	 * Submit a posted form
	 */
	public function doSubmit(){
		global $wgConf, $wgOut;
		$current = $wgConf->getCurrent( $this->mWiki );
		$settings = $this->importFromRequest();
		$new = $settings + $current;
		$new['__includes'] = $this->getRequiredFiles(); 
		$ok = $wgConf->saveNewSettings( $new, $this->mWiki );
		$msg = wfMsgNoTrans( $ok ? 'configure-saved' : 'configure-error' );
		$class = $ok ? 'successbox' : 'errorbox';

		$wgOut->addWikiText( "<div class=\"$class\"><strong>$msg</strong></div>" );
	}

	/**
	 * Show the diff between the current version and the posted version
	 */
	protected function showDiff(){
		global $wgConf, $wgOut;
		$wiki = $this->mWiki;
		$old = array( $wiki => $wgConf->getCurrent( $wiki ) );
		$new = array( $wiki => $this->conf );
		$diff = new ExtPreviewConfigurationDiff( $old, $new, array( $wiki ) );
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

	protected function getRequiredFiles(){
		global $wgRequest;
		$arr = array();
		foreach( self::staticGetAllExtensionsObjects() as $ext ){
			if( $wgRequest->getCheck( $ext->getCheckName() ) )
				$arr[] = $ext->getFile();
		}
		return $arr;
	}

	protected function isUserAllowedAll(){
		static $allowed = null;
		if( $allowed === null ){
			global $wgUser;
			$allowed = $wgUser->isAllowed( 'extensions-all' );
		}
		return $allowed;
	}

	/**
	 * Simple wrapper to make it public
	 */
	public function buildInput( $conf, $type, $default ){
		return parent::buildInput( $conf, $type, $default );
	}

	/**
	 * Same as before
	 */
	public function getSettingValue( $setting ){
		return parent::getSettingValue( $setting );	
	}

	/**
	 * For the moment, it's always allowed
	 */
	public function userCanRead( $s ){
		return true;	
	}

	/**
	 * For the moment, it's always allowed
	 */
	public function userCanEdit( $s ){
		return true;	
	}

	protected function getEditableSettings(){
		return self::staticGetEditableSettings();
	}
	
	protected function getArrayType( $setting ){
		return self::staticGetArrayType( $setting );
	}

	/**
	 * Assume that...
	 */
	protected function isSettingAvailable( $setting ){
		return true;
	}

	/**
	 * Build the content of the form
	 *
	 * @return xhtml
	 */
	protected function buildAllSettings(){
		$ret = '';
		$globalDone = false;
		foreach( self::staticGetAllExtensionsObjects() as $ext ){
			$settings = $ext->getSettings();
			foreach( $settings as $setting => $type ){
				if( !isset( $GLOBALS[$setting] ) && !isset( $this->conf[$setting] ) ){
					if( !$globalDone ){
						extract( $GLOBALS, EXTR_REFS );
						$__hooks__ = $wgHooks;
						$globalDone = true;
					}
					require_once( $ext->getFile() );
					if( isset( $$setting ) )
						$this->conf[$setting] = $$setting;
				}	
			}
			$ext->setPageObj( $this );
			$ret .= $ext->getHtml();
		}
		if( isset( $__hooks__ ) )
			$GLOBALS['wgHooks'] = $__hooks__;
		return $ret;
	}
}
