<?php

/*
 * Created on 8 nov 2007
 *
 * API for WikiData
 *
 * Copyright (C) 2007 Edia <info@edia.nl>
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

class ApiWikiData extends ApiBase {
	
	private $printer;
	private $rintseIsRight = false;

	public function __construct($main, $action) {
		parent :: __construct($main, $action, 'wd');
	}

	public function execute() {
		$type = null;
		$expression = null;
		$dmid = null;
		$dataset = null;
		$languages = null;
		$explanguage = null;
		$sections = null;
		$format = null;
		$collection = null;
		
		// read the request parameters
		extract($this->extractRequestParams());
		
		$datasets = wdGetDataSets();
		if (!isset($datasets[$dataset])) {
			// TODO how do we report an error?
			$dataset = 'uw';
		}
		
		// **********************************
		// create and configure the formatter
		// **********************************
		$printer = $this->getCustomPrinter();
		
		$printer->setFormat($format);
		
		// Figure out what to output
		if ($sections != null) { // default is to show everything
			$printer->excludeAll();
			foreach ($sections as $section) {
				if ($section == 'syntrans') {
					$printer->setIncludeSynTrans(true);
				}
				else if ($section == 'syntransann') {
					$printer->setIncludeSynTransAnnotations(true);
				}
				else if ($section == 'def') {
					$printer->setIncludeDefinitions(true);
				}
				else if ($section == 'altdef') {
					$printer->setIncludeAltDefinitions(true);
				}
				else if ($section == 'ann') {
					$printer->setIncludeAnnotations(true);
				}
				else if ($section == 'classatt') {
					$printer->setIncludeClassAttributes(true);
				}
				else if ($section == 'classmem') {
					$printer->setIncludeClassMembership(true);
				}
				else if ($section == 'colmem') {
					$printer->setIncludeCollectionMembership(true);
				}
				else if ($section == 'rel') {
					$printer->setIncludeRelations(true);
				}
				else {
					// TODO handle error
				}
				
			}
		}
		
		// Figure out which languages to output
		if ($languages != null) {
			//echo $languages;
			$langCodes = explode('|', $languages);
			foreach ($langCodes as $langCode) {
				$langId = getLanguageIdForIso639_3($langCode);
				if ($langId != null) {
					$printer->addOutputLanguageId($langId);
				}
				else {
					// TODO handle error
				}
			}
		}
		
		// *************************
		// Handle the actual request
		// *************************
		if ($type == 'definedmeaning') {
			$dmModel = new DefinedMeaningModel($dmid, null, $datasets[$dataset]);
			$dmModel->loadRecord();
			$record = $dmModel->getRecord();
			$printer->addDefinedMeaningRecord($record);
		}
		else if ($type == 'expression') {
			$dmIds = array();
			if ($explanguage == null) {
				$dmIds = getExpressionMeaningIds($expression, $dataset);
			}
			else {
				$srcLanguages = array();
				$srcLanguages[] = getLanguageIdForIso639_3($explanguage);
				$dmIds = getExpressionMeaningIdsForLanguages($expression, $srcLanguages, $dataset);
			}
			
			$uniqueDmIds = array();
			foreach ($dmIds as $dmId) {
				// Should we return duplicates? If Rintse is right, we should.
				if (!$this->rintseIsRight && in_array($dmId, $uniqueDmIds)) {
					continue;
				}
				$uniqueDmIds[] = $dmId;
				
				$dmModel = new DefinedMeaningModel($dmId, null, $datasets[$dataset]);
				$dmModel->loadRecord();
				$record = $dmModel->getRecord();
				$printer->addDefinedMeaningRecord($record);
			}
			
		}
		else if ($type == 'randomdm') {
			$allDmIds = array();
			if ($collection != null) {
				// get collection contents
				$contents = getCollectionMembers($collection, $datasets[$dataset]);
				if (count($contents) > 0) {
					$allDmIds = $contents;
				}
				else {
					$printer->addErrorMessage("The collection is empty.");
				}
			}
			if ($relation != null) {
				if ($dmid != null) {
					$related = getRelatedDefinedMeanings($relation, $dmid, $dataset);
					if (count($related) > 0) {
						if (count($allDmIds) == 0) {
							$allDmIds = $related;
						}
						else {
							// only elements in both collections apply.
							$allDmIds = array_intersect($allDmIds, $related);
							if (count($allDmIds) == 0) {
								$printer->addErrorMessage("The combination of a collection and a relation query did not return any results.");
							}
						}
					}
					else {
						$printer->addErrorMessage("Your relations query did not return any results.");
					}
				}
				else {
					$printer->addErrorMessage("Missing parameter: wddmid");
				}
			}
			// get a random value from it
			$randomKey = array_rand($allDmIds);
			$randomDmId = $allDmIds[$randomKey];
			
			// look up that dmid
			$dmModel = new DefinedMeaningModel($randomDmId, null, $datasets[$dataset]);
			$dmModel->loadRecord();
			$record = $dmModel->getRecord();
			$printer->addDefinedMeaningRecord($record);
		}
		
	}
	
	public function & getCustomPrinter() {
		if (is_null($this->printer)) {
			$this->printer = new ApiWikiDataFormatXml($this->getMain());
		}
		return $this->printer;
	}
	
	protected function getAllowedParams() {
		return array (
			'type' => array (
				ApiBase :: PARAM_DFLT => 'expression',
				ApiBase :: PARAM_TYPE => array (
					'expression',
					'definedmeaning',
					'randomdm'
				)
			),
			'expression' => null,
			'explanguage' => null,
			'dmid' => null,
			'collection' => null,
			'relation' => null,
			'dataset' => 'uw',
			'languages' => null,
			'sections' => array (
				ApiBase :: PARAM_DFLT => null,
				ApiBase :: PARAM_ISMULTI => true,
				ApiBase :: PARAM_TYPE => array (
					'def',
					'altdef',
					'syntrans',
					'syntransann',
					'ann',
					'classatt',
					'classmem',
					'colmem',
					'rel'
				)
			),
			'format' => array (
				ApiBase :: PARAM_DFLT => 'plain',
				ApiBase :: PARAM_TYPE => array ('plain', 'tbx')
			)
		);
	}

	protected function getParamDescription() {
		return array (
			'type' => array (
				'Query type.',
				'expression:     query an expression.',
				'definedmeaning: defined meaning by id',
				'randomdm:       a radom defined meaning'
			),
			'expression' => 'For type \'expression\': the expression.',
			'explanguage' => 'Source language of an expression. Omit to search all languages.',
			'dmid' => 'For type \'definedmeaning\': the defined meaning id.',
			'collection' => 'For type \'randomdm\': the collection (by dmid) to pick a defined meaning from.',
			'relation' => 'For type \'randomdm\': the relation (by relationtype dmid) to another defined meaning, given in wddmid.',
			'dataset' => 'The dataset to query.',
			'languages' => array (
				'Output languages.',
				'Values (separate with \'|\'): any ISO 639-3 language code'
			),
			'sections' => array(
				'Which sections to include in the output. If omitted, everything is returned. ',
				'def:          include definitions',
				'altdef:       include alternative definitions',
				'syntrans:     include synonyms and translations',
				'syntransann:  include annotations of synonyms and translations such as part of speech',
				'ann:          include annotations of defined meanings such as part of theme',
				'classatt:     include class attributes',
				'classmem:     include class membership',
				'colmem:       include collection membership',
				'rel:          include relations'
			),
			'format' => 'The output format.'
		);
	}

	protected function getDescription() {
		return array (
			'This module provides an API to WikiData.'
		);
	}
	
	protected function getExamples() {
		return array(
			'api.php?action=wikidata&wdexpression=bier&wdexplanguage=fri&wdsections=def|syntrans&wdlanguages=nld|eng|fra'
		);
	}

	public function getVersion() {
		return __CLASS__ . ': $Id: $';
	}
}
?>
