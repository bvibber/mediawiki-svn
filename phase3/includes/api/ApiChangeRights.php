<?php

/*
 * Created on Sep 11, 2007
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
class ApiChangeRights extends ApiBase {

	public function __construct($main, $action) {
		parent :: __construct($main, $action);
	}

	public function execute() {
		global $wgUser, $wgRequest;
		$params = $this->extractRequestParams();

		$ur = new UserrightsForm($wgRequest);
		$allowed = $ur->changeableGroups();
		$res = array();

		if($params['listgroups'])
			$res['allowedgroups'] = $allowed;
		if(is_null($params['user']))
			$this->dieUsage('The user parameter must be set', 'nouser');

		$uName = User::getCanonicalName($params['user']);
		if(!$uName)
			$this->dieUsage("Invalid username ``{$params['user']}''", 'invaliduser');
		$u = User::newFromName($uName);
		if(!$u)
			$this->dieUsage("User ``{$params['user']}'' doesn't exist", 'nosuchuser')
;
		if($params['gettoken'])
		{
			$res['changerightstoken'] = $wgUser->editToken($uName);
			$this->getResult()->addValue(null, $this->getModuleName(), $res);
			return;
		}

		if(empty($params['addto']) && empty($params['rmfrom']))
			$this->dieUsage('At least one of the addto and rmfrom parameters must be set', 'nochange');
		if(is_null($params['token']))
			$this->dieUsage('The token parameter must be set', 'notoken');
		if(!$wgUser->matchEditToken($params['token'], $uName))
			$this->dieUsage('Invalid token', 'badtoken');

		if(!$wgUser->isAllowed('userrights'))
			$this->dieUsage('You don\'t have permission to change users\' rights', 'permissiondenied');

		// Check $wgUser can really add and remove all the groups he wants to
		foreach($params['addto'] as $g)
			if(!in_array($g, $allowed['add']))
				$this->dieUsage("You don't have permission to add to group ``$g''", 'cantadd');
		foreach($params['rmfrom'] as $g)
			if(!in_array($g, $allowed['remove']))
				$this->dieUsage("You don't have permission to remove from group ``$g''", 'cantremove');

		$ur->doSaveUserGroups($uName, $params['rmfrom'], $params['addto'], $params['reason']);
		$res['user'] = $uName;
		$res['addedto'] = $params['addto'];
		$res['removedfrom'] = $params['rmfrom'];
		$res['reason'] = $params['reason'];

		$this->getResult()->addValue(null, $this->getModuleName(), $res);
	}

	protected function getAllowedParams() {
		return array (
			'user' => null,
			'token' => null,
			'gettoken' => false,
			'listgroups' => false,
			'addto' => array(
				ApiBase :: PARAM_MULTI => true
			),
			'rmfrom' => array(
				ApiBase :: PARAM_MULTI => true
			),
			'reason' => ''
		);
	}

	protected function getParamDescription() {
		return array (
			'user' => 'The user you want to add to or remove from groups.',
			'token' => 'A changerights token previously obtained through the gettoken parameter.',
			'gettoken' => 'Output a token. Note that the user parameter still has to be set.',
			'listgroups' => 'List the groups you can add users to and remove them from.',
			'addto' => 'Pipe-separated list of groups to add this user to',
			'rmfrom' => 'Pipe-separated list of groups to remove this user from',
			'reason' => 'Reason for change (optional)'
		);
	}

	protected function getDescription() {
		return array(
			'Add or remove a user from certain groups.'
		);
	}

	protected function getExamples() {
		return array (
			'api.php?action=changerights&user=Bob&gettoken&listgroups',
			'api.php?action=changerights&user=Bob&token=123ABC&addto=sysop&reason=Promoting%20per%20RFA'
		);
	}

	public function getVersion() {
		return __CLASS__ . ': $Id$';
	}
}
?>
