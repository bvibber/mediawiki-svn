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
		$deflanguage = null;
		$resplanguage = null;
		$sections = null;
		$format = null;
		$collection = null;
		$relation = null;
		$relleft = null;
		$relright = null;
		$translanguage = null;
		$deflanguage = null;
		
		// read the request parameters
		extract($this->extractRequestParams());
		
		$datasets = wdGetDataSets();
		if (!isset($datasets[$dataset])) {
			$this->dieUsage("Unknown data set: $dataset", 'unknown_dataset');
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
					$printer->addErrorMessage("Unknown section: $section");
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
					$printer->addErrorMessage($langCode.' is not an ISO 639-3 language code.');
				}
			}
		}
		
		// The response language
		global $wgRecordSetLanguage;
		$wgRecordSetLanguage = getLanguageIdForIso639_3($resplanguage);
		
		// *************************
		// Handle the actual request
		// *************************
		if ($type == 'definedmeaning') {
			$dmModel = new DefinedMeaningModel($dmid, null, $datasets[$dataset]);
			$dmModel->loadRecord();
			$record = $dmModel->getRecord();
			$printer->addDefinedMeaningRecord($record);
		}
		// *******************
		// QUERY BY EXPRESSION
		// *******************
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
		// **********************
		// RANDOM DEFINED MEANING
		// **********************
		else if ($type == 'randomdm') {
			
			// I want all the allowed parameters for this type of query to work in combination. 
			// So a client can, for example, get a random defined meaning from the destiazione 
			// italia collection that has a definition and translation in spanish and that has 
			// a 'part of theme' relationship with some other defined meaning.  
			// We'll build one monster query for all these parameters. I've seen this take up
			// to one second on a development machine.
			
			$query =  "SELECT dm.defined_meaning_id " .
			          "FROM {$dataset}_defined_meaning dm ";
			
			// JOINS GO HERE 
			// dm must have a translation in given language
			if ($translanguage != null) {
				$query .= "INNER JOIN {$dataset}_syntrans st ON dm.defined_meaning_id=st.defined_meaning_id " .
			              "INNER JOIN {$dataset}_expression exp ON st.expression_id=exp.expression_id ";
			}
			
			// dm must have a definition in given language
			if ($deflanguage != null) {
				$query .= "INNER JOIN {$dataset}_translated_content tc ON dm.meaning_text_tcid=tc.translated_content_id ";
			}
			
			// dm must be part of given collection
			if ($collection != null) {
				$query .= "INNER JOIN {$dataset}_collection_contents col on dm.defined_meaning_id=col.member_mid ";
			}
					
			// dm must be related to another dm
			if ($relation != null && ($relleft != null || $relright != null)) {
				if ($relright != null) {
					$query .= "INNER JOIN {$dataset}_meaning_relations rel ON dm.defined_meaning_id=rel.meaning1_mid ";
				}
				else {
					$query .= "INNER JOIN {$dataset}_meaning_relations rel ON dm.defined_meaning_id=rel.meaning2_mid ";
				}
			}
			
			// WHERE CLAUSE GOES HERE
			$query .= "WHERE dm.remove_transaction_id is null ";
			
			// dm must have a translation in given language
			if ($translanguage != null) {
				$query .= "AND st.remove_transaction_id is null " .
			              "AND exp.remove_transaction_id is null " .
			              "AND exp.language_id=".getLanguageIdForIso639_3($translanguage)." ";
			}
			
			// dm must have a definition in given language
			if ($deflanguage != null) {
				$query .= "AND tc.remove_transaction_id is null " .
			              "AND tc.language_id=".getLanguageIdForIso639_3($deflanguage)." ";
			}
			
			// dm must be part of given collection
			if ($collection != null) {
				$query .= "AND col.remove_transaction_id is null " .
			              "AND col.collection_id=$collection ";
			}
			
			// dm must be related to another dm
			if ($relation != null && ($relleft != null || $relright != null)) {
				$query .= "AND rel.remove_transaction_id is null " .
			              "AND rel.relationtype_mid=$relation ";
				if ($relright != null) {
					$query .= "AND rel.meaning2_mid=$relright ";
				}
				else {
					$query .= "AND rel.meaning1_mid=$relleft ";
				}  
			}
			
			// We may get doubles for multiple expressions or relations. Pretty trivial, but affects probability
			$query .= "GROUP BY dm.defined_meaning_id ";
			// pick one at random
			$query .= "ORDER BY RAND() LIMIT 0,1";
			
			//echo $query;
			
			$dbr =& wfGetDB(DB_SLAVE);
			$result = $dbr->query($query);
			if ($dbr->numRows($result) > 0) {
				$row = $dbr->fetchRow($result);
				$dmModel = new DefinedMeaningModel($row[0], null, $datasets[$dataset]);
				$dmModel->loadRecord();
				$record = $dmModel->getRecord();
				$printer->addDefinedMeaningRecord($record);	
			}
			
		}
		else if ($type == 'relation') {
			if ($relation != null || $relleft != null || $relright != null) {
				$related = 	getRelationDefinedMeanings($relation, $relleft, $relright, $datasets[$dataset]);
				if (count($related) > 0) {
					foreach ($related as $dmId) {
						$dmModel = new DefinedMeaningModel($dmId, null, $datasets[$dataset]);
						$dmModel->loadRecord();
						$record = $dmModel->getRecord();
						$printer->addDefinedMeaningRecord($record);
					}
				}
				else {
					$printer->addErrorMessage("Your relations query did not return any results.");
				}
			}
			else {
				$printer->addErrorMessage('To get relations you must at least specify a left or right hand side dmid and optionally a relation type dmid.');
			}
			
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
					'randomdm',
					'relation'
				)
			),
			'expression' => null,
			'explanguage' => null,
			'dmid' => null,
			'collection' => null,
			'relation' => null,
			'relleft' => null,
			'relright' => null,
			'translanguage' => null,
			'deflanguage' => null,
			'dataset' => 'uw',
			'resplanguage' => 'eng',
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
				'randomdm:       a random defined meaning',
				'relation:       find relations or related defined meanings'
			),
			'expression' => 'For type \'expression\': the expression.',
			'explanguage' => 'For type \'expression\': the expression language. Omit to search all languages.',
			'dmid' => 'For type \'definedmeaning\': the defined meaning id.',
			'collection' => 'For type \'randomdm\': the collection (by dmid) to pick a defined meaning from.',
			'relation' => 'For type \'randomdm\'or \'relation\': the relation (by relationtype dmid) to another defined meaning, given in wdrelleft or wdrelright.',
			'relleft' => 'When looking for a related defined meaning: the left hand side of the relation by dmid.',
			'relright' => 'When looking for a related defined meaning: the right hand side of the relation by dmid.',
			'translanguage' => 'For type \'randomdm\': a translation in this language must be present. ISO 639-3 language code.',
			'deflanguage' => 'For type \'randomdm\': a definition in this language must be present. ISO 639-3 language code.',
			'dataset' => 'The dataset to query.',
			'resplanguage' => 'Response language: language strings in the response document are set in this language. ISO 639-3 language code.',
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
			'api.php?action=wikidata&wdexpression=bier&wdexplanguage=nld&wdsections=def|syntrans&wdlanguages=deu|eng|fra',
			'api.php?action=wikidata&wdtype=definedmeaning&wddmid=6715&wdformat=tbx',
			'api.php?action=wikidata&wdtype=randomdm&wdtranslanguage=nld&wddeflanguage=nld&wdcollection=376322',
			'api.php?action=wikidata&wdtype=relation&wdrelation=3&wdrelleft=6715',
			'api.php?action=wikidata&wdtype=relation&wdrelleft=339',
			'api.php?action=wikidata&wdtype=randomdm&wdrelation=3&wdrelright=339',
			'api.php?action=wikidata&wdtype=randomdm&wdformat=tbx&wdresplanguage=nld'
		);
	}

	public function getVersion() {
		return __CLASS__ . ': $Id: $';
	}
}
?>
