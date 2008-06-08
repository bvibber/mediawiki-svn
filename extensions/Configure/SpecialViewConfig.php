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
				if( $this->isUserAllowedAll() ){
					$wiki = $wgRequest->getVal( 'wiki', $wgConf->getWiki() );
				} else {
					$wiki = $wgConf->getWiki();
				}

				$this->version = $version;
				$this->wiki = $wiki;

				if( $diff = $wgRequest->getVal( 'diff' ) ){
					if( !in_array( $diff, $versions ) ){
						$msg = wfMsgNoTrans( 'configure-old-not-available', $diff );
						$wgOut->addWikiText( "<div class='errorbox'>$msg</div>" );
						return;
					}
					$this->diff = $diff;
				}

				if( isset( $conf[$wiki] ) ){
					$this->conf = $conf[$wiki];
				} else if( !isset( $this->diff ) ){
					$msg = wfMsgNoTrans( 'configure-old-not-available', $version );
					$wgOut->addWikiText( "<div class='errorbox'>$msg</div>" );
					return;
				}
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
	public function userCanEdit( $setting ){
		return false;
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

		if( !$this->isWebConfig || !empty( $this->conf ) || isset( $this->diff ) ){
			if( isset( $this->diff ) ){
				$wikis = $this->isUserAllowedAll() ? true : array( $this->wiki );
				$diffEngine = new ConfigurationDiff( $this->diff, $this->version, $wikis );
				$diffEngine->setViewCallback( array( $this, 'userCanRead' ) );
				$wgOut->addHtml( $diffEngine->getHTML() );
			} else {
				$wgOut->addHtml(
					Xml::openElement( 'div' ) . "\n" .
					Xml::openElement( 'div', array( 'id' => 'preferences' ) ) . "\n" .
	
					$this->buildAllSettings() . "\n" .
	
					Xml::closeElement( 'div' ) . "\n" .
					Xml::closeElement( 'div' ) . "\n"
				);
			}
		} else {
			$wgOut->addHtml( $this->buildOldVersionSelect() );
		}
		$this->injectScriptsAndStyles();
	}

	/**
	 * Build links to old version of the configuration
	 * 
	 */
	protected function buildOldVersionSelect(){
		global $wgConf, $wgLang, $wgUser, $wgScript;
		if( !$this->isWebConfig )
			return '';

		$versions = $wgConf->listArchiveFiles();
		if( empty( $versions ) ){
			return wfMsgExt( 'configure-no-old', array( 'parse' ) );
		}
		$title = $this->getTitle();
		$skin = $wgUser->getSkin();
		$showDiff = count( $versions ) > 1;
		$allowedConfig = $wgUser->isAllowed( 'configure' );
		$allowedAll = $this->isUserAllowedAll();
		$allowedConfigAll = parent::isUserAllowedAll();
		if( $allowedConfig )
			$configTitle = is_callable( array( 'SpecialPage', 'getTitleFor' ) ) ? # 1.9 +
				SpecialPage::getTitleFor( 'Configure' ) :
				Title::makeTitle( NS_SPECIAL, 'Configure' );

		$text = wfMsgExt( 'configure-old-versions', array( 'parse' ) );
		if( $showDiff )
			$text .= Xml::openElement( 'form', array( 'action' => $wgScript ) ) . "\n" .
			Xml::hidden( 'title', $title->getPrefixedDBKey() ) . "\n" .
			$this->getButton() . "\n";
		$text .= "<ul>\n";
		
		$c = 0;
		foreach( array_reverse( $versions ) as $ts ){
			$c++;
			$time = $wgLang->timeAndDate( $ts );
			if( $allowedAll || $allowedConfigAll ){
				$settings = $wgConf->getOldSettings( $ts );
				$wikis = array_keys( $settings );
			}
			$actions = array();
			$view = $skin->makeKnownLinkObj( $title, wfMsg( 'configure-view' ), "version=$ts" );
			if( $allowedAll ){
				$viewWikis = array();
				foreach( $wikis as $wiki ){
					$viewWikis[] = $skin->makeKnownLinkObj( $title, $wiki, "version={$ts}&wiki={$wiki}" );
				}
				$view .= ' (' . implode( ', ', $viewWikis ) . ')';
			}
			$actions[] = $view;
			if( $allowedConfig ){
				$edit = $skin->makeKnownLinkObj( $configTitle, wfMsg( 'edit' ), "version=$ts" );
				if( $allowedConfigAll ){
					$viewWikis = array();
					foreach( $wikis as $wiki ){
						$viewWikis[] = $skin->makeKnownLinkObj( $configTitle, $wiki, "version={$ts}&wiki={$wiki}" );
					}
					$edit .= ' (' . implode( ', ', $viewWikis ) . ')';
				}
				$actions[] = $edit;
			}
			if( $showDiff ){
				$diffCheck = $c == 2 ? array( 'checked' => 'checked' ) : array();
				$versionCheck = $c == 1 ? array( 'checked' => 'checked' ) : array();
				$buttons =
					Xml::element( 'input', array_merge( 
						array( 'type' => 'radio', 'name' => 'diff', 'value' => $ts ),
						$diffCheck ) ) .
					Xml::element( 'input', array_merge(
						array( 'type' => 'radio', 'name' => 'version', 'value' => $ts ),
						$versionCheck ) );
						
			} else {
				$buttons = '';
			}
			$action = implode( ', ', $actions );
			$text .= "<li>{$buttons}{$time}: {$action}</li>\n";
		}
		$text .= "</ul>";
		if( $showDiff )
			$text .= $this->getButton() . "</form>";
		return $text;
	}

	/**
	 * Taken from PageHistory.php
	 */
	protected function getButton(){
		return Xml::submitButton( wfMsg( 'compareselectedversions' ),
				array(
					'class'     => 'historysubmit',
					'accesskey' => wfMsg( 'accesskey-compareselectedversions' ),
					'title'     => wfMsg( 'tooltip-compareselectedversions' ),
					)
				);
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
