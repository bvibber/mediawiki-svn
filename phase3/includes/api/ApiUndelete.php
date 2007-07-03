<?php

/*
 * Created on Jun 20, 2007
 * API for MediaWiki 1.8+
 *
 * Copyright (C) 2007 Roan Kattouw <Firstname>.<Lastname>@home.nl
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 * http://www.gnu.org/copyleft/gpl.html
 */

if (!defined('MEDIAWIKI')) {
	// Eclipse helper - will be ignored in production
	require_once ("ApiBase.php");
}

/**
 * @addtogroup API
 */
class ApiUndelete extends ApiBase {

	public function __construct($main, $action) {
		parent :: __construct($main, $action);
	}

	public function execute() {
		global $wgUser;
		$params = $this->extractRequestParams();
		
		$titleObj = NULL;
		if(!isset($params['title']))
			$this->dieUsage('The title parameter must be set', 'notarget');
		if(!isset($params['token']))
			$this->dieUsage('The token parameter must be set', 'notoken');

		if(!$wgUser->isAllowed('delete'))
			$this->dieUsage('You don\'t have permission to restore deleted revisions', 'permissiondenied');
		if($wgUser->isBlocked())
			$this->dieUsage('You have been blocked from editing', 'blocked');
		if(wfReadOnly())
			$this->dieUsage('The wiki is in read-only mode', 'readonly');
		if(!$wgUser->matchEditToken($params['token']))
			$this->dieUsage('Invalid token', 'badtoken');

		$titleObj = Title::newFromText($params['title']);
		if(!$titleObj)
			$this->dieUsage("bad title {$params['title']}", 'invalidtitle');
		$pa = new PageArchive($titleObj);
		if(!$pa->undelete((isset($params['timestamps']) ? $params['timestamps'] : array()), $params['reason']))
			$this->dieUsage('Undeletion failed for unknown reason', 'failed');

		$info['title'] = $params['title'];
		$info['reason'] = $params['reason']; // FIXME
		$this->getResult()->addValue(null, $this->getModuleName(), $info);
	}

	protected function getAllowedParams() {
		return array (
			'title' => null,
			'token' => null,
			'reason' => "",
			'timestamps' => array(
				ApiBase :: PARAM_ISMULTI => true
			)
		);
	}

	protected function getParamDescription() {
		return array (
			'title' => 'Title of the page you want to restore.',
			'token' => 'An undelete token previously retrieved through list=deletedrevs',
			'reason' => 'Reason for restoring (optional)',
			'timestamps' => 'Timestamps of the revisions to restore. If not set, all revisions will be restored.'
		);
	}

	protected function getDescription() {
		return array(
			'Restore certain revisions of a deleted page. A list of deleted revisions (including timestamps) can be',
			'retrieved through list=deletedrevs'
		);
	}

	protected function getExamples() {
		return array (
			'api.php?action=undelete&title=Main%20Page&token=123ABC&reason=Restoring%20main%20page',
			'api.php?action=undelete&title=Main%20Page&token=123ABC&timestamps=20070703220045|20070702194856'
		);
	}

	public function getVersion() {
		return __CLASS__ . ': $Id: ApiUndelete.php 22289 2007-05-20 23:31:44Z yurik $';
	}
}
?>
