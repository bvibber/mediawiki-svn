<?php
if ( !defined( 'MEDIAWIKI' ) ) die();

/**
 * Special page allows authorised users to see wiki's configuration
 * This should also be available if efConfigureSetup() hasn't been called
 *
 * @ingroup Extensions
 */
class SpecialViewConfig extends SpecialConfigure {
	protected $isWebConfig;

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct( 'ViewConfig', 'viewconfig' );
	}

	/**
	 * Show the special page
	 *
	 * @param $par Mixed: parameter passed to the page or null
	 */
	public function execute( $par ) {
		global $wgUser, $wgRequest, $wgOut, $wgConf;
		$this->isWebConfig = $wgConf instanceof WebConfiguration;

		$this->setHeaders();

		if ( !$this->userCanExecute( $wgUser ) ) {
			$this->displayRestrictionError();
			return;
		}

		$this->outputHeader();

		if( $this->isWebConfig && $version = $wgRequest->getVal( 'version' ) ){
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

		$this->showForm();
	}

	/**
	 * Return true if the current user is allowed to configure all settings.
	 * @return bool
	 */
	protected function isUserAllowedAll(){
		static $allowed = null;
		if( $allowed === null ){
			global $wgUser;
			$allowed = $wgUser->isAllowed( 'viewconfig-all' );
		}
		return $allowed;
	}

	/**
	 * Return true if the current user is allowed to configure $setting.
	 * @return bool
	 */
	protected function userCanEdit( $setting ){
		return false;
	}

	protected function buildInput( $conf, $type, $default ){
		if( in_array( $conf, parent::$viewRestricted ) && !$this->isUserAllowedAll() )
			return '<span class="disabled">' . wfMsgExt( 'configure-view-not-allowed', array( 'parseinline' ) ) . '</span>';
		return parent::buildInput( $conf, $type, $default );
	}

	/**
	 * Just in case, security
	 */
	protected function doSubmit(){}

	/**
	 * Show the main form
	 */
	protected function showForm(){
		global $wgOut, $wgUser, $wgRequest;

		# We use <div id="preferences"> to have the tabs like in Special:Preferences
		$wgOut->addHtml(
			$this->buildOldVersionSelect() . "\n\n" .

			Xml::openElement( 'div' ) . "\n" .
			Xml::openElement( 'div', array( 'id' => 'preferences' ) ) . "\n" .

			$this->buildAllSettings() . "\n" .

			Xml::closeElement( 'div' ) . "\n" .
			Xml::closeElement( 'div' ) . "\n"
		);
		$this->injectScriptsAndStyles();
	}

	/**
	 * Build links to old version of the configuration
	 * 
	 */
	protected function buildOldVersionSelect(){
		global $wgConf, $wgLang, $wgUser;
		if( !$this->isWebConfig )
			return '';

		$versions = $wgConf->listArchiveFiles();
		if( empty( $versions ) ){
			return wfMsgExt( 'configure-no-old', array( 'parse' ) );
		}
		$text = wfMsgExt( 'configure-old-versions', array( 'parse' ) );
		$text .= "<ul>\n";
		$skin = $wgUser->getSkin();
		$title = $this->getTitle();
		$allowedConfig = $wgUser->isAllowed( 'configure' );
		if( $allowedConfig )
			$configTitle = SpecialPage::getTitleFor( 'Configure' );
		foreach( $versions as $ts ){
			$view = $skin->makeKnownLinkObj( $title, $wgLang->timeAndDate( $ts ), "version=$ts" );
			$edit = $allowedConfig ?
				' (' . $skin->makeKnownLinkObj( $configTitle, wfMsg( 'edit' ), "version=$ts" ) . ')' :
				'';
			$text .= "<li>" . $view . $edit . "</li>\n";
		}
		$text .= "</ul>";
		return $text;
	}

	/**
	 * Inject JavaScripts and Stylesheets in page output
	 */
	protected function injectScriptsAndStyles() {
		global $wgOut, $wgScriptPath, $wgConfigureStyleVersion;
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
	}

	/**
	 * Return true if all settings in this section are restricted
	 *
	 * @param $sectArr Array: one value of self::$settings array
	 */
	protected function isSectionRestricted( $sectArr ){
		return false;
	}
}
