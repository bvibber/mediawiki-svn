<?php

/*
 * Created on Oct 15, 2007
 *
 * API for MediaWiki 1.8+
 *
 * Copyright (C) 2006 Alberto Bernal
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
	require_once ('ApiQueryBase.php');
}

/**
 * A query action to get a random page
 * 
 * @addtogroup API
 */
class ApiQueryRandomPage extends ApiQueryGeneratorBase {

	public function __construct($query, $moduleName) {
		parent :: __construct($query, $moduleName, 'rp');
	}

	public function execute() {
		$this->run();
	}

	public function executeGenerator($resultPageSet) {
		$this->run($resultPageSet);
	}

	private function run($resultPageSet = null) {
		global $wgOut, $wgContLang;

		$params = $this->extractRequestParams();

		$MyRandom = new RandomPage();

		if(!is_null( $params['namespace'] )){
			$MyRandom->setNamespace( $params['namespace'] );
		}else{
			$MyRandom->setNamespace( $wgContLang->getNsIndex( $par ) );
		}

		$MyRandom->setRedirect( false );
	
		$title = $MyRandom->getRandomTitle();
	
		if( is_null( $title ) ) {
			$data[] = array(
				'result' => "RANDOMPAGE-NOPAGES");

		}else{
			$data[] = array(
				'pageid' => intval($title->getArticleID()),
				'ns' => intval($title->getNamespace()),
				'title' => $title->getPrefixedText(),
				'url' => $title->getFullUrl());
		}

		$result = $this->getResult();
		$result->setIndexedTagName($data, 'page');
		$result->addValue('query', $this->getModuleName(), $data);
	}

	protected function getAllowedParams() {
		return array (
			'namespace' => array (
				ApiBase :: PARAM_DFLT => 0,
				ApiBase :: PARAM_TYPE => 'namespace'
			),
		);
	}

	protected function getParamDescription() {
		return array (
            'namespace' => 'Namespace to select pages from',
		);
	}

	protected function getDescription() {
		return array (
			'Get a wiki Random Page.',
		);
	}

	protected function getExamples() {
		return array (
			'Get a wiki Random Page with namespace=6.":',
			'  api.php?action=query&prop=randompage&grpnamespace=6',
			'Get a wiki Random Page.":',
			'  api.php?action=query&generator=randompage',
		);
	}

	public function getVersion() {
		return __CLASS__ . ': $Id: ApiQueryRandomPage.php 25838 2007-10-15 23:48:35Z abernala $';
	}
}

