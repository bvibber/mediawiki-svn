<?php
if ( !defined( 'MEDIAWIKI' ) ) die();

/**
 * Special page allows authorised users to see wiki's configuration
 * This should also be available if efConfigureSetup() hasn't been called
 *
 * @ingroup Extensions
 */
class SpecialViewConfig extends ConfigurationPage {
	protected $isWebConfig;
	var $mRequireWebConf = false;
	var $mCanEdit = false;

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct( 'ViewConfig', 'viewconfig' );
	}

	protected function getVersion(){
		global $wgOut, $wgRequest, $wgConf;

		$this->isWebConfig = $wgConf instanceof WebConfiguration;

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
					return false;
				}
			} else {
				$msg = wfMsgNoTrans( 'configure-old-not-available', $version );
				$wgOut->addWikiText( "<div class='errorbox'>$msg</div>" );
				return false;
			}
		}

		return true;
	}

	/**
	 * Return true if the current user is allowed to configure $setting.
	 * @return bool
	 */
	public function userCanEdit( $setting ){
		return false;
	}

	public function userCanRead( $setting ){
		if( $this->isUserAllowedAll() )
			return true;
		if( array_key_exists( $setting, SpecialConfigure::staticGetAllSettings() ) )
			return !in_array( $setting, SpecialConfigure::staticGetViewRestricted() );
		return true;
	}

	/**
	 * Just in case, security
	 */
	protected function doSubmit(){}

	/**
	 * Show diff
	 */
	protected function showDiff(){
		global $wgOut;
		$wikis = $this->isUserAllowedAll() ? true : array( $this->wiki );
		$diffEngine = new HistoryConfigurationDiff( $this->diff, $this->version, $wikis );
		$diffEngine->setViewCallback( array( $this, 'userCanRead' ) );
		$wgOut->addHtml( $diffEngine->getHTML() );
	}

	/**
	 * Show the main form
	 */
	protected function showForm(){
		global $wgOut, $wgRequest;

		if( !$this->isWebConfig || !empty( $this->conf ) || isset( $this->diff ) ){
			if( isset( $this->diff ) ){
				$this->showDiff();
			} else {
				$wgOut->addHtml(
					Xml::openElement( 'div', array( 'id' => 'configure-form' ) ) . "\n" .
					Xml::openElement( 'div', array( 'id' => 'configure' ) ) . "\n" .
	
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
		$allowedExtensions = $wgUser->isAllowed( 'extensions' );

		$allowedAll = $this->isUserAllowedInterwiki();
		$allowedConfigAll = $wgUser->isAllowed( 'configure-interwiki' );
		$allowedExtensionsAll = $wgUser->isAllowed( 'extensions-interwiki' );

		if( $allowedConfig )
			$configTitle = is_callable( array( 'SpecialPage', 'getTitleFor' ) ) ? # 1.9 +
				SpecialPage::getTitleFor( 'Configure' ) :
				Title::makeTitle( NS_SPECIAL, 'Configure' );

		if( $allowedExtensions )
			$extTitle = is_callable( array( 'SpecialPage', 'getTitleFor' ) ) ? # 1.9 +
				SpecialPage::getTitleFor( 'Extensions' ) :
				Title::makeTitle( NS_SPECIAL, 'Extensions' );

		$text = wfMsgExt( 'configure-old-versions', array( 'parse' ) );
		if( $showDiff )
			$text .= Xml::openElement( 'form', array( 'action' => $wgScript ) ) . "\n" .
			Xml::hidden( 'title', $title->getPrefixedDBKey() ) . "\n" .
			$this->getButton() . "\n";
		$text .= "<ul>\n";
		
		$editMsg = wfMsg( 'edit' ) . ': ';
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
			$editDone = false;
			if( $allowedConfig ){
				$editCore = $editMsg . $skin->makeKnownLinkObj( $configTitle, wfMsg( 'configure-edit-core' ), "version=$ts" );
				if( $allowedConfigAll ){
					$viewWikis = array();
					foreach( $wikis as $wiki ){
						$viewWikis[] = $skin->makeKnownLinkObj( $configTitle, $wiki, "version={$ts}&wiki={$wiki}" );
					}
					$editCore .= ' (' . implode( ', ', $viewWikis ) . ')';
				}
				$actions[] = $editCore;
			}
			if( $allowedExtensions ){
				$editExt = '';
				if( !$allowedConfig )
					$editExt .= $editMsg;
				$editExt .= $skin->makeKnownLinkObj( $extTitle, wfMsg( 'configure-edit-ext' ), "version=$ts" );
				if( $allowedExtensionsAll ){
					$viewWikis = array();
					foreach( $wikis as $wiki ){
						$viewWikis[] = $skin->makeKnownLinkObj( $extTitle, $wiki, "version={$ts}&wiki={$wiki}" );
					}
					$editExt .= ' (' . implode( ', ', $viewWikis ) . ')';
				}
				$actions[] = $editExt;
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

	protected function getEditableSettings(){
		return SpecialConfigure::staticGetEditableSettings() +
			SpecialExtensions::staticGetSettings();
	}
	
	protected function getArrayType( $setting ){
		$type = SpecialConfigure::staticGetArrayType( $setting );
		if( !$type )
			$type = SpecialExtensions::staticGetArrayType( $setting );
		return $type;
	}

	protected function isSettingAvailable( $setting ){
		return SpecialConfigure::staticIsSettingAvailable( $setting ) ||
			array_key_exists( $setting, SpecialExtensions::staticGetEditableSettings() );
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

	protected function buildAllSettings(){
		$ret = '';
		$settings = SpecialConfigure::staticGetSettings() + SpecialExtensions::staticGetSettings();
		foreach( $settings as $title => $groups ){
			$msgName = 'configure-section-' . $title;
			$msg = wfMsg( $msgName );
			if( wfEmptyMsg( $msgName, $msg ) )
				$msg = $title;
			$ret .= Xml::openElement( 'fieldset' ) . "\n" .
				Xml::element( 'legend', null, $msg ) . "\n";
			$first = true;
			foreach( $groups as $group => $settings ){
				$ret .= $this->buildTableHeading( $group, !$first );
				$first = false;
				foreach( $settings as $setting => $type ){
					if( !in_array( $setting, SpecialConfigure::staticGetNotEditableSettings() ) )
						$ret .= $this->buildTableRow( 'configure-setting-' . $setting, 
								$setting, $type, $this->getSettingValue( $setting ), !( $title == 'mw-extensions' ) );
				}
			}
			$ret .= Xml::closeElement( 'table' ) . "\n";
			$ret .= Xml::closeElement( 'fieldset' );
		}
		return $ret;
	}
}
