<?php

/**
 * Class definition for MWReleases API Module
 */

class ApiMWReleases extends ApiBase {
	public function __construct($main, $action) {
		parent :: __construct($main, $action);
	}

	public function execute() {
		$results = array();
		$params = $this->extractRequestParams();
		$releases = $params['allreleases'] ?
			ReleaseRepo::singleton()->getAllReleases() :
			ReleaseRepo::singleton()->getSupportedReleases();

		foreach( $releases as $release ) {
			$r = array();
			if( ReleaseRepo::singleton()->getLatestStableRelease()->getId()
				== $release->getId() )
			{
				$r['latest'] = '';
			}
			foreach( $params['prop'] as $prop ) {
				switch( $prop ) {
					case 'name':
						$r['name'] = $release->getName();
						break;
					case 'number':
						$r['number'] = $release->getNumber();
						break;
					case  'reldate':
						$r['reldate'] = $release->getReldate();
						break;
					case 'eoldate':
						$r['eoldate'] = $release->getEoldate();
						break;
					case 'tagurl':
						$r['tagurl'] = $release->getTagUrl();
						break;
					case 'branchurl':
						$r['branchurl'] = $release->getBranchUrl();
						break;
					case 'announceurl':
						$r['announceturl'] = $release->getAnnounceUrl();
						break;
					case 'supported':
						if( $release->isSupported() ) {
							$r['supported'] = '';
						}
						break;
				}
			}
			$results[] = $r;
		}
		$this->getResult()->setIndexedTagName($results, 'release');
		$this->getResult()->addValue(null, $this->getModuleName(), $results);
	}

	public function getAllowedParams() {
		return array(
			'prop' => array(
				ApiBase::PARAM_ISMULTI => true,
				ApiBase::PARAM_TYPE => array (
					'name',
					'number',
					'reldate',
					'eoldate',
					'tagurl',
					'branchurl',
					'announceurl',
					'supported',
				),
				ApiBase::PARAM_DFLT => 'number',
			),
			'allreleases' => false,
		);
	}

	public function getParamDescription() {
		return array(
			'prop' => 'Properties about the release',
			'allreleases' => 'Show all releases, not just currently supported ones',
		);
	}

	public function getDescription() {
		return array (
			'Get the list of current Mediawiki releases'
		);
	}

	protected function getExamples() {
		return array(
			'api.php?action=mwreleases&prop=tagurl|branchurl',
			'api.php?action=mwreleases&&allreleases=1&prop=name|reldate|eoldate'
		);
	}
	public function getVersion() {
		return __CLASS__ . ': ' . MWRELEASES_VERSION;
	}
}
