<?php

/*
 * Created on Jan 14, 2009
 *
 * Copyright (C) 2009 Soxred93 soxred93 [-at-] gee mail [-dot-] com,
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
	require_once ('ApiBase.php');
}

class CheckUserApi extends ApiBase {
	
	public function execute() {
		global $wgUser;
		$this->getMain()->requestWriteMode();
		$params = $this->extractRequestParams();
		
		if(is_null($params['user']))
			$this->dieUsageMsg(array('missingparam', 'user'));
		if(is_null($params['type']))
			$this->dieUsageMsg(array('missingparam', 'type'));
		if(is_null($params['duration']))
			$this->dieUsageMsg(array('missingparam', 'duration'));
		if(!isset($params['reason'])) {
			$reason = '';
		}
		else {
			$reason = $params['reason'];
		}
		if(!$wgUser->isAllowed('checkuser'))
			$this->dieUsageMsg(array('cantcheckuser'));
		
		$user = $params['user'];
		$checktype = $params['type'];
		$period = $params['duration'];
		
		# An IPv4?
		if( preg_match( '#^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}(/\d{1,2}|)$#', $user ) ) {
			$ip = $user; 
			$name = ''; 
			$xff = '';
		# An IPv6?
		} else if( preg_match( '#^[0-9A-Fa-f]{1,4}(:[0-9A-Fa-f]{1,4})+(/\d{1,3}|)$#', $user ) ) {
			$ip = IP::sanitizeIP($user); 
			$name = ''; 
			$xff = '';
		# An IPv4 XFF string?
		} else if( preg_match( '#^(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})(/\d{1,2}|)/xff$#', $user, $matches ) ) {
			list( $junk, $xffip, $xffbit) = $matches;
			$ip = ''; 
			$name = ''; 
			$xff = $xffip . $xffbit;
		# An IPv6 XFF string?
		} else if( preg_match( '#^([0-9A-Fa-f]{1,4}(:[0-9A-Fa-f]{1,4})+)(/\d{1,3}|)/xff$#', $user, $matches ) ) {
			list( $junk, $xffip, $xffbit ) = $matches;
			$ip = ''; 
			$name = ''; 
			$xff = IP::sanitizeIP( $xffip ) . $xffbit;
		# A user?
		} else {
			$ip = ''; 
			$name = $user; 
			$xff = '';
		}
		
		if( $checktype=='subuserips' ) {
			$res = $this->doUserIPsRequest( $name, $reason, $period );
		} else if( $xff && $checktype=='subipedits' ) {
			$res = $this->doIPEditsRequest( $xff, true, $reason, $period );
		} else if( $checktype=='subipedits' ) {
			$res = $this->doIPEditsRequest( $ip, false, $reason, $period );
		} else if( $xff && $checktype=='subipusers' ) {
			$res = $this->doIPUsersRequest( $xff, true, $reason, $period );
		} else if( $checktype=='subipusers' ) {
			$res = $this->doIPUsersRequest( $ip, false, $reason, $period );
		} else if( $checktype=='subuseredits' ) {
			$res = $this->doUserEditsRequest( $user, $reason, $period );
		}
		
		if( !is_null( $res ) ) {
			$this->getResult()->setIndexedTagName($res, 'cu');
			$this->getResult()->addValue(null, $this->getModuleName(), $res);
		}
	}

	public function __construct($main, $action) {
		parent :: __construct($main, $action);
		ApiBase::$messageMap['cantcheckuser'] = array('code' => 'cantcheckuser', 'info' => "You dont have permission to run a checkuser");
		ApiBase::$messageMap['checkuserlogfail'] = array('code' => 'checkuserlogfail', 'info' => "Inserting a log entry failed");
		ApiBase::$messageMap['nomatch'] = array('code' => 'nomatch', 'info' => "No matches found");
		ApiBase::$messageMap['nomatchedit'] = array('code' => 'nomatch', 'info' => "No matches found. Last edit was on $1 at $2");
	}
	
	public function mustBePosted() { return true; }

	public function getAllowedParams() {
		return array (
			'user' => null,
			'type' => null,
			'duration' => null,
			'reason' => null,
		);
	}

	public function getParamDescription() {
		return array (
			'user' => 'The user (or IP) you want to check',
			'type' => 'The type of check you want to make (subuserips, subipedits, subipusers, or subuseredits)',
			'duration' => 'How far back you want to check',
			'reason' => 'The reason for checking',
		);
	}

	public function getDescription() {
		return array (
			'Run a CheckUser on a username or IP address'
		);
	}

	protected function getExamples() {
		return array(
			'api.php?action=checkuser&user=127.0.0.1/xff&type=subipedits&duration=all',
			'api.php?action=checkuser&user=Example&type=subuserips&duration=2_weeks',
		);
	}

	public function getVersion() {
		return __CLASS__ . ': $Id: CheckUser_api.php 45575 2009-01-14 22:50:32Z soxred93 $';
	}
}