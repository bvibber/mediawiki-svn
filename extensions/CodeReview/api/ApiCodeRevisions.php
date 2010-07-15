<?php

/**
 * Created on July 06, 2010
 *
 * API for MediaWiki 1.8+
 *
 * Copyright © 2010 Sam Reed
 * Copyright © 2008 Bryan Tong Minh <Bryan.TongMinh@Gmail.com>
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
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 */

class ApiCodeRevisions extends ApiQueryBase {
	public function __construct( $query, $moduleName ) {
		parent::__construct( $query, $moduleName, 'cr' );
	}

	public function execute() {
		global $wgUser;
		// Before doing anything at all, let's check permissions
		if ( !$wgUser->isAllowed( 'codereview-use' ) ) {
			$this->dieUsage( 'You don\'t have permission to view code revisions', 'permissiondenied' );
		}
		$params = $this->extractRequestParams();
		if ( is_null( $params['repo'] ) ) {
			$this->dieUsageMsg( array( 'missingparam', 'repo' ) );
		}
		$this->props = array_flip( $params['prop'] );

		$listview = new CodeRevisionListView( $params['repo'] );
		if ( is_null( $listview->getRepo() ) ) {
			$this->dieUsage( "Invalid repo ``{$params['repo']}''", 'invalidrepo' );
		}
		$pager = $listview->getPager();

		if ( !is_null( $params['start'] ) ) {
			$pager->setOffset( $this->getDB()->timestamp( $params['start'] ) );
		}
		$limit = $params['limit'];
		$pager->setLimit( $limit );

		$pager->doQuery();

		$revisions = $pager->getResult();
		$data = array();

		$count = 0;
		$lastTimestamp = 0;
		while ( $row = $revisions->fetchObject() ) {
			if ( $count == $limit ) {
				$this->setContinueEnumParameter( 'start',
					wfTimestamp( TS_ISO_8601, $lastTimestamp ) );
				break;
			}

			$data[] = $this->formatRow( $row );
			$lastTimestamp = $row->cr_timestamp;
			$count++;
		}
		$revisions->free();

		$result = $this->getResult();
		$result->setIndexedTagName( $data, 'revision' );
		$result->addValue( 'query', $this->getModuleName(), $data );
	}

	private function formatRow( $row ) {
		$item = array();
		if ( isset( $this->props['revid'] ) ) {
			$item['revid'] = $row->cr_rev_id;
		}
		if ( isset( $this->props['status'] ) ) {
			$item['status'] = $row->cr_status;
		}
		if ( isset( $this->props['commentcount'] ) ) {
			$item['commentcount'] = $row->comments;
		}
		if ( isset( $this->props['path'] ) ) {
			$item['path'] = $row->cr_path;
		}
		if ( isset( $this->props['message'] ) ) {
			ApiResult::setContent( $item, $row->cr_message );
		}
		if ( isset( $this->props['author'] ) ) {
			$item['author'] = $row->cr_author;
		}
		if ( isset( $this->props['timestamp'] ) ) {
			$item['timestamp'] = wfTimestamp( TS_ISO_8601, $row->cr_timestamp );
		}

		return $item;
	}

	public function getAllowedParams() {
		return array(
			'repo' => null,
			'limit' => array(
				ApiBase::PARAM_DFLT => 10,
				ApiBase::PARAM_TYPE => 'limit',
				ApiBase::PARAM_MIN => 1,
				ApiBase::PARAM_MAX => ApiBase::LIMIT_BIG1,
				ApiBase::PARAM_MAX2 => ApiBase::LIMIT_BIG2
			),
			'start' => array(
				ApiBase::PARAM_TYPE => 'timestamp'
			),
			'prop' => array(
				ApiBase::PARAM_ISMULTI => true,
				ApiBase::PARAM_DFLT => 'revid|author|status|timestamp',
				ApiBase::PARAM_TYPE => array(
					'revid',
					'status',
					'commentcount',
					'path',
					'message',
					'author',
					'timestamp',
				),
			),
		);
	}

	public function getParamDescription() {
		return array(
			'repo' => 'Name of the repository',
			'limit' => 'How many revisions to return',
			'start' => 'Timestamp to start listing at',
			'prop' => 'Which properties to return',
		);
	}

	public function getDescription() {
		return 'List revisions in CodeReview';
	}

	public function getPossibleErrors() {
		return array_merge( parent::getPossibleErrors(), array(
			array( 'missingparam', 'repo' ),
			array( 'code' => 'permissiondenied', 'info' => 'You don\'t have permission to view code revisions' ),
			array( 'code' => 'invalidrepo', 'info' => "Invalid repo ``repo''" ),
		) );
	}

	public function getExamples() {
		return array(
			'api.php?action=query&list=coderevisions&crrepo=MediaWiki',
			'api.php?action=query&list=coderevisions&crrepo=MediaWiki&crprop=revid|author|status|timestamp',
		);
	}

	public function getVersion() {
		return __CLASS__ . ': $Id: ApiCodeComments.php 48777 2009-03-25 01:26:54Z aaron $';
	}
}
