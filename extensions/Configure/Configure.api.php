<?php
if ( !defined( 'MEDIAWIKI' ) ) die();

class ApiConfigure extends ApiBase {

	public function __construct( $main, $moduleName ) {
		parent::__construct( $main, $moduleName );
	}

	public function execute() {
		global $wgConf, $wgUser;
		$params = $this->extractRequestParams();
		$result = $this->getResult();
		
		if( !$wgConf instanceof WebConfiguration ){
			$this->dieUsage( 'You need to call efConfigureSetup() to use this module', 'noconf' );	
		}

		// Version list
		if( in_array( 'versionlist', $params['prop'] ) ) {
			if( !$wgUser->isAllowed( 'viewconfig' ) )
				$this->dieUsage( 'viewconfig right required', 'noright' );
			$versions = $wgConf->listArchiveFiles();
			if( $wgUser->isAllowed( 'viewconfig-interwiki' ) ) {
				$oldVersions = $versions;
				$versions = array();
				foreach( $oldVersions as $version ){
					$settings = $wgConf->getOldSettings( $version );
					$wikis = array_keys( $settings );
					$wikis['id'] = $version;
					$result->setIndexedTagName( $wikis, 'wiki' );
					$versions[] = $wikis;
				}
			}
			$result->setIndexedTagName( $versions, 'version' );
			$result->addValue( 'configure', 'versions', $versions );
		}

		// Wiki list
		if( in_array( 'wikilist', $params['prop'] ) ) {
			if( !$wgUser->isAllowed( 'viewconfig-interwiki' ) && !$wgUser->isAllowed( 'configure-interwiki' ) && !$wgUser->isAllowed( 'extensions-interwiki' ) )
				$this->dieUsage( 'viewconfig-interwiki, configure-interwiki or extensions-interwiki right required', 'noright' );
			global $wgConfigureWikis;
			if( $wgConfigureWikis === false )
				$result->addValue( 'configure', 'wikis', array( 'denied' => '' ) );
			if( $wgConfigureWikis === true )
				$result->addValue( 'configure', 'wikis', array( 'any' => '' ) );
			if( is_array( $wgConfigureWikis ) ){
				$wikis = $wgConfigureWikis;
				$result->setIndexedTagName( $wikis, 'wiki' );
				$result->addValue( 'configure', 'wikis', $wikis );
			}
		}

		// Settings
		if( in_array( 'settings', $params['prop'] ) ) {
			if( !$wgUser->isAllowed( 'viewconfig' ) )
				$this->dieUsage( 'viewconfig right required', 'noright' );
			$version = $params['version'];
			$wiki = $params['wiki'] ? $params['wiki'] : $wgConf->getWiki();
			$settingsValues = $wgConf->getOldSettings( $version );
			if( !is_array( $settingsValues ) )
				$this->dieUsage( 'version not found', 'noversion' );
			if( !isset( $settingsValues[$wiki] ) || !is_array( $settingsValues[$wiki] ) )
				$this->dieUsage( 'wiki not found in version', 'nowiki' );
			$settingsValues = $settingsValues[$wiki];
			$conf = ConfigurationSettings::singleton( CONF_SETTINGS_BOTH );
			$notEditable = $conf->getNotEditableSettings();
			$ret = array();
			if( $params['group'] ){
				$sections = $conf->getSettings();
				foreach( $sections as $sectionName => $section ){
					$groupRet = array( 'name' => $sectionName );
					foreach( $section as $groupName => $group ){
						$settingsRet = array( 'name' => $groupName );
						foreach( $group as $setting => $type ){
							if( !$conf->isSettingAvailable( $setting ) || in_array( $setting, $notEditable ) )
								continue;
							$settingsRet[] = $this->getSettingResult( $setting, $type, $settingsValues, $conf, $result );
						}
						$result->setIndexedTagName( $settingsRet, 'setting' );
						$groupRet[] = $settingsRet;
					}
					$result->setIndexedTagName( $groupRet, 'group' );
					$ret[] = $groupRet;
				}
				$result->setIndexedTagName( $ret, 'section' );
			} else {
				$settings = $conf->getAllSettings();
				foreach( $settings as $setting => $type ){
					if( !$conf->isSettingAvailable( $setting ) || in_array( $setting, $notEditable ) )
						continue;
					$ret[] = $this->getSettingResult( $setting, $type, $settingsValues, $conf, $result );
				}
				$result->setIndexedTagName( $ret, 'setting' );
			}
			$result->addValue( 'configure', 'settings', $ret );
		}
		
		// Extensions
		if( in_array( 'extensions', $params['prop'] ) ) {
			if( !$wgUser->isAllowed( 'extensions' ) )
				$this->dieUsage( 'extensions right required', 'noright' );
			$conf = ConfigurationSettings::singleton( CONF_SETTINGS_EXT );
			$ret = array();
			foreach( $conf->getAllExtensionsObjects() as $ext ){
				$extArr = array();
				$extArr['name'] = $ext->getName();
				if( $ext->isActivated() )
					$extArr['activated'] = '';
				if( $ext->hasSchemaChange() )
					$extArr['schema'] = '';
				if( ( $url = $ext->getUrl() ) !== null )
					$extArr['url'] = $url;
				$ret[] = $extArr;
			}
			$result->setIndexedTagName( $ret, 'extension' );
			$result->addValue( 'configure', 'extension', $ret );
		}
	}

	protected function userCanRead( $setting, $conf ){
		global $wgUser;
		if( in_array( $setting, $conf->getNotEditableSettings() )
			|| ( in_array( $setting, $conf->getViewRestricted() )
			&& !$wgUser->isAllowed( 'viewconfig-all' ) ) )
			return false;
		global $wgConfigureViewRestrictions;
		if( !isset( $wgConfigureViewRestrictions[$setting] ) )
			return true;
		foreach( $wgConfigureViewRestrictions[$setting] as $right ){
			if( !$wgUser->isAllowed( $right ) )
				return false;
		}
		return true;
	}

	protected function getSettingResult( $setting, $type, $settingsValues, $conf, $result ){
		$settingRet = array( 'name' => $setting );
		if( !$this->userCanRead( $setting, $conf ) ){
			$settingRet['restricted'] = '';
			return $settingRet;
		}
		if( isset( $settingsValues[$setting] ) ){
			$settingVal = $settingsValues[$setting];
			switch( $type ){
			case 'bool':
				$settingRet['type'] = $type;
				$settingRet['value'] = $settingVal ? 'true' : 'false';
				break;
			case 'text':
			case 'lang':
				$settingVal = (string)$settingVal;
			case 'int':
				$settingRet['type'] = $type;
				$settingRet['value'] = $settingVal;
				break;
			case 'array':
				$settingRet['type'] = $type;
				$arrType = ConfigurationSettings::singleton( CONF_SETTINGS_BOTH )->getArrayType( $setting );
				$settingRet['array'] = $arrType;
				switch( $arrType ){
				case 'simple':
					$result->setIndexedTagName( $settingVal, 'value' );
					$settingRet['values'] = $settingVal;
					break;
				case 'assoc':
					$settingRet['values'] = array();
					$result->setIndexedTagName( $settingRet['values'], 'value' );
					foreach( (array)$settingVal as $key => $val ){
						$arrRet = array( 'key' => $key, 'value' => $val );
						$settingRet['values'][] = $arrRet;
					}
					break;
				case 'simple-dual':
					$settingRet['values'] = array();
					$result->setIndexedTagName( $settingRet['values'], 'value' );
					foreach( (array)$settingVal as $val ){
						$settingRet['values'][] = implode( ',', $val );
					}
					break;
				case 'ns-bool':
					global $wgContLang;
					$settingRet['values'] = array();
					$result->setIndexedTagName( $settingRet['values'], 'value' );
					foreach( $wgContLang->getNamespaces() as $ns => $unused ){
						$settingRet['values'][] = array( 'index' => $ns, 'value' => ( isset( $settingVal[$ns] ) && $settingVal[$ns] ) ? 'true' : 'false' );
					}
					break;
				case 'ns-text':
					global $wgContLang;
					$settingRet['values'] = array();
					$result->setIndexedTagName( $settingRet['values'], 'value' );
					foreach( $wgContLang->getNamespaces() as $ns => $unused ){
						$settingRet['values'][] = array( 'index' => $ns, 'value' => isset( $settingVal[$ns] ) ? $settingVal[$ns] : '' );
					}
					break;
				case 'ns-simple':
					$settingRet['values'] = $settingVal;
					$result->setIndexedTagName( $settingRet['values'], 'value' );
					break;
				case 'ns-array':
					global $wgContLang;
					$settingRet['values'] = array();
					$result->setIndexedTagName( $settingRet['values'], 'value' );
					foreach( $wgContLang->getNamespaces() as $ns => $unused ){
						$nsRet = array( 'index' => $ns );
						$result->setIndexedTagName( $nsRet, 'item' );
						$nsRet += isset( $vals[$ns] ) && is_array( $settingVal[$ns] ) ? $settingVal[$ns] : array();
						$settingRet['values'][] = $nsRet;
					}
					break;
				case 'group-bool':
					$settingRet['values'] = array();
					$result->setIndexedTagName( $settingRet['values'], 'group' );
					if( is_callable( array( 'User', 'getAllRights' ) ) ){ // 1.13 +
						$all = User::getAllRights();
					} else {
						foreach( $settingVal as $rights )
							$all = array_merge( $all, array_keys( $rights ) );
						$all = array_unique( $all );
					}
					foreach( $settingVal as $group => $rights ){
						$arr = array( 'name' => $group, 'rights' => array() );
						$result->setIndexedTagName( $arr['rights'], 'permission' );
						foreach( $all as $name ){
							$arr['rights'][] = array(
								'name' => $name,
								'allowed' => $rights[$name] ? 'true' : 'false'
							);
						}
						$settingRet['values'][] = $arr;
					}
					break;
				case 'group-array':
					$settingRet['values'] = array();
					$result->setIndexedTagName( $settingRet['values'], 'group' );
					$all = array_keys( $settingsValues['wgGroupPermissions'] );
					$iter = array();
					foreach( $all as $group )
						$iter[$group] = isset( $settingVal[$group] ) && is_array( $settingVal[$group] ) ? $settingVal[$group] : array();
					if( $conf->isSettingAvailable( 'wgImplicitGroups' ) ) // 1.12 +
						$all = array_diff( $all, $settingsValues['wgImplicitGroups'] );
					else
						$all = array_diff( $all, User::getImplicitGroups() );
					}
					foreach( $iter as $group => $value ){
						$arr = array( 'name' => $group, 'values' => $value );
						$result->setIndexedTagName( $arr['values'], 'value' );
						$settingRet['values'][] = $arr;
					}
				break;
			default:
				if( is_array( $type ) ){
					$allowed = array();
					$settingRet['type'] = 'multi';
					foreach( $type as $val => $desc ){
						$allowed[] = array( 'desc' => $desc, 'value' => $val );
					}
					$result->setIndexedTagName( $allowed, 'allowed' );
					$settingRet['type'] = $allowed;
					$settingRet['value'] = $settingsValues[$setting];
				}
			}
		} else {
			$settingRet['missing'] = '';
		}
		return $settingRet;
	}

	protected function getAllowedParams() {
		return array(
			'prop' => array(
                ApiBase::PARAM_ISMULTI => true,
                ApiBase::PARAM_TYPE => array(
                	'versionlist',
                	'wikilist',
                	'settings',
                	'extensions',
                ),
                ApiBase::PARAM_DFLT => 'versionlist|wikilist',
            ),
			'version' => null,
			'wiki' => null,
			'group' => false,
		);
	}

	protected function getParamDescription() {
		return array(
			'prop' => array(
				'Which information to get:',
				'- versionlist: Get the list of old configurations',
				'- wikilist:    Get the list the wikis to configuration',
				'- settings:    Get settings of a specific version',
				'- extensions:  List of installed extensions',
			),
			'version' => 'Version to get settings from',
			'wiki' => 'Wiki to get settings from (default: current wiki)',
			'group' => 'Whether to group settings',
		);
	}

	protected function getDescription() {
		return 'Configure extension\'s API module';
	}

	protected function getExamples() {
		return array (
			'api.php?action=configure',
		);
	}

	public function getVersion() {
		return __CLASS__ . ': $Id$';
	}
}
