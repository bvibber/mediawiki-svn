<?php

require_once('Expression.php');
require_once('forms.php');
require_once('table.php');
require_once('type.php');
require_once('languages.php');

/**
 * Renders a content page from WiktionaryZ based on the GEMET database.
 * @package MediaWiki
 */
class WiktionaryZ {
	/* TODOs:
		use $dbr->select() instead of $dbr->query() wherever possible; it lets MediaWiki handle additional
		table prefixes and such.
	*/
	function view() {
		global 
			$wgOut, $wgTitle, $wgUser, $wgLanguageNames;

		$userlang=$wgUser->getOption('language');
		$skin = $wgUser->getSkin();
		$dbr =& wfGetDB( DB_MASTER );

		$wgOut->addHTML("Your user interface language preference: <b>$userlang</b> - " . $skin->makeLink("Special:Preferences", "set your preferences"));

		# Get entry record from GEMET namespace
		$res=$dbr->query("SELECT * from uw_expression_ns WHERE spelling=BINARY ".$dbr->addQuotes($wgTitle->getText()));

		while($row=$dbr->fetchObject($res)) {
			$expressionId = $row->expression_id;
			$definedMeaningIds = $this->getDefinedMeaningsForExpression($expressionId);
			$synonymAndTranslationTables = $this->getSynonymAndTranslationTables($definedMeaningIds, $expressionId);
			$definedMeaningTexts = $this->getDefinedMeaningTexts($definedMeaningIds);
			$alternativeMeaningTexts = $this->getAlternativeMeaningTexts($definedMeaningIds);
			$definedMeaningRelations = $this->getDefinedMeaningRelations($definedMeaningIds);

			$wgOut->addHTML($skin->editSectionLink($wgTitle, $expressionId));
			$wgOut->addHTML("<h2><i>Spelling</i>: $row->spelling - <i>Language:</i> ".$wgLanguageNames[$row->language_id]. "</h2>");

			$attributesPerDefinedMeaning = $this->getDefinedMeaningAttributes($definedMeaningIds);			
			$typenames=$this->getRelationTypes();
			$attnames=$this->getAttributeValues();

			$wgOut->addHTML('<ul>');
			foreach($definedMeaningIds as $definedMeaningId) {
				$wgOut->addHTML($skin->editSectionLink($wgTitle, "$expressionId-$definedMeaningId"));
				$wgOut->addHTML("<li><h3>Definition</h3>");
				$this->viewTranslatedContent($definedMeaningTexts[$definedMeaningId]);
				
 				if ($alternativeMeaningTexts[$definedMeaningId]) {
					foreach($alternativeMeaningTexts[$definedMeaningId] as $alternativeMeaningTextId) { 
						$wgOut->addHTML("<h3>Alternative definition</h3>");
						$this->viewTranslatedContent($alternativeMeaningTextId);	
					}
 				}
 				
 				# Get spellings of translations and synonyms
				$wgOut->addHTML("<table border='0' cellpadding='5'><tr valign='top'><td>");
				$wgOut->addHTML("<h4>Translations and Synonyms:</h4>");
				$wgOut->addHTML(getTableAsHTML($synonymAndTranslationTables[$definedMeaningId]));
				
				# Relations
				$wgOut->addHTML("</td><td>");
				$wgOut->addHTML("<h4>Relations:</h4>");

				$relations = $definedMeaningRelations[$definedMeaningId];
				foreach($relations as $type => $rellist) {
					$wgOut->addHTML("<p>$typenames[$type]:</p>");
					$wgOut->addHTML("<ul>");
					foreach($rellist as $rel) {
						$rs_res=$dbr->query("SELECT expression_id from uw_defined_meaning where defined_meaning_id=".$rel." LIMIT 1");
						$rs_row=$dbr->fetchObject($rs_res);
						if($rs_row->expression_id) {
							$li_res=$dbr->query("SELECT spelling from uw_expression_ns where expression_id=".$rs_row->expression_id);
							$li_row=$dbr->fetchObject($li_res);
							$wgOut->addHTML("<li>". $skin->makeLink("WiktionaryZ:".$li_row->spelling, $li_row->spelling) ."</li>");
						}
					}
					$wgOut->addHTML("</ul>");
				}
				$wgOut->addHTML("<h4>Attributes:</h4><ul>");
				$attributes = $attributesPerDefinedMeaning[$definedMeaningId];

				foreach($attributes as $attribute) {
					$attributeName = $attnames[$attribute];
					$wgOut->addHTML("<li>". $skin->makeLink("WiktionaryZ:".$attributeName, $attributeName) ."</li>");
				}
				
				$wgOut->addHTML("</ul></td></tr></table></li>");
			}
			$wgOut->addHTML('</ul>');
		}

		# We may later want to disable the regular page component
		# $wgOut->setPageTitleArray($this->mTitle->getTitleArray());
	}
	
	function viewTranslatedContent($setId) {
		global
			$wgOut, $wgLanguageNames;
		
		$translatedContents = $this->getTranslatedContents($setId);

		foreach($translatedContents as $language => $textId) {
			$wgOut->addHTML("<p><b><i>$wgLanguageNames[$language]</i></b></p>");
			$wgOut->addHTML(htmlspecialchars($this->getText($textId)));
		}
	}

	function getRelationTypeSuggest($definedMeaningId) {
		return getSuggest("new-relation-type-$definedMeaningId", 
                          "relation-type", 
                          "member_mid", "spelling");		
	}
	
	function getDefinedMeaningSuggest($definedMeaningId) {
		return getSuggest("new-relation-other-meaning-$definedMeaningId", 
                          "defined-meaning", 
                          "member_mid", "spelling");		
	}
	
	function getRelationTypes() {
		$relationtypes=array();
		$reltypecollections=$this->getReltypeCollections();
		$dbr =& wfGetDB( DB_SLAVE );	
		foreach($reltypecollections as $cname=>$cid) {
			$rel_res=$dbr->query("select member_mid from uw_collection_contents where collection_id=$cid and is_latest_set=1");
			while($rel_row=$dbr->fetchObject($rel_res)) {
				# fixme hardcoded English
				$rel_name=$this->getExpressionForMeaningId($rel_row->member_mid, 85);
				$relationtypes[$rel_row->member_mid]=$rel_name;
			}
		}
		return $relationtypes;
	}
	
	function getReltypeCollections() {
		$reltypecollections=array();
		$dbr =& wfGetDB ( DB_SLAVE );
		$col_res=$dbr->query("select collection_id,collection_mid from uw_collection_ns where collection_type='RELT' and is_latest=1");
		while($col_row=$dbr->fetchObject($col_res)) {
			# fixme hardcoded English
			$collection_name=$this->getExpressionForMeaningId($col_row->collection_mid,85);
			$reltypecollections[$collection_name]=$col_row->collection_id;
		}
		return $reltypecollections;
	
	}
	
	function getExpressionForMeaningId($mid, $langcode) {
		$dbr =& wfGetDB(DB_SLAVE);
		$sql="SELECT spelling from uw_syntrans,uw_expression_ns where defined_meaning_id=".$mid." and uw_expression_ns.expression_id=uw_syntrans.expression_id and uw_expression_ns.language_id=".$langcode." limit 1";
		$sp_res=$dbr->query($sql);
		$sp_row=$dbr->fetchObject($sp_res);
		return $sp_row->spelling;
	}
	
	function saveForm($sectionArguments) {
		global 
			$wgTitle;
		
		if (count($sectionArguments) == 0)
			$this->saveSpellingForm($wgTitle->getText());
		else {
			$expressionId = $sectionArguments[0];			
			$definedMeaningIds = $this->getDefinedMeaningIdsForSectionArguments($sectionArguments);
			$this->saveExpressionForm($expressionId, $definedMeaningIds);
		}
		
//		$dbr =& wfGetDB( DB_MASTER );
//
//		# Get entry record from GEMET namespace
//		$res=$dbr->query("SELECT * from uw_expression_ns WHERE spelling=BINARY ".$dbr->addQuotes($wgTitle->getText()));
//
//		while($row=$dbr->fetchObject($res)) {
//			$expressionId = $row->expression_id;
//			$definedMeaningIds = $this->getDefinedMeaningsForExpression($expressionId);
//			$synonymsAndTranslationIds = $this->getSynonymAndTranslationIds($definedMeaningIds, $expressionId);
//			$definedMeaningTexts = $this->getDefinedMeaningTexts($definedMeaningIds);
//			$definedMeaningRelations = $this->getDefinedMeaningRelations($definedMeaningIds);
//
//			foreach($definedMeaningIds as $definedMeaningId) {
//				$translatedContents = $this->getTranslatedContents($definedMeaningTexts[$definedMeaningId]);
//				
//				foreach($translatedContents as $languageId => $textId) {
//					$definition = trim($wgRequest->getText('definition-'.$textId));
//					   	 
//					if ($definition != '')
//						$this->setText($textId, $definition);
//				}
//
//				$this->addTranslatedDefinitionFromRequest($definedMeaningId, $definedMeaningTexts[$definedMeaningId], getRevisionForExpressionId($expressionId), array_keys($translatedContents));
//				$this->addSynonymsOrTranslationsFromRequest($definedMeaningId);
//				$this->addRelationFromRequest($definedMeaningId);
//			}
//		}
		
		Title::touchArray(array($wgTitle));
	}

	function saveSpellingForm($spelling) {
		$dbr =& wfGetDB(DB_SLAVE);
		$queryResult = $dbr->query("SELECT expression_id FROM uw_expression_ns WHERE spelling=BINARY ".$dbr->addQuotes($spelling));

		while ($expression = $dbr->fetchObject($queryResult)) {
			$expressionId = $expression->expression_id;
			$definedMeaningIds = $this->getDefinedMeaningsForExpression($expressionId);
			$this->saveExpressionForm($expressionId, $definedMeaningIds);			
		}
	}
	
	function saveExpressionForm($expressionId, $definedMeaningIds) {
		$synonymsAndTranslationIds = $this->getSynonymAndTranslationIds($definedMeaningIds, $expressionId);
		$definedMeaningTexts = $this->getDefinedMeaningTexts($definedMeaningIds);
		$definedMeaningRelations = $this->getDefinedMeaningRelations($definedMeaningIds);

		foreach($definedMeaningIds as $definedMeaningId) 
			$this->saveDefinedMeaningForm($expressionId, $definedMeaningId, $definedMeaningTexts[$definedMeaningId]);
	}
	
	function saveDefinedMeaningForm($expressionId, $definedMeaningId, $definedMeaningTextId) {
		global
			$wgRequest;
		
		$translatedContents = $this->getTranslatedContents($definedMeaningTextId);
		
		foreach($translatedContents as $languageId => $textId) {
			$definition = trim($wgRequest->getText('definition-'.$textId));
			   	 
			if ($definition != '')
				$this->setText($textId, $definition);
		}

		$this->addTranslatedDefinitionFromRequest($definedMeaningId, $definedMeaningTextId, getRevisionForExpressionId($expressionId), array_keys($translatedContents));
		$this->addSynonymsOrTranslationsFromRequest($definedMeaningId);
		$this->addRelationFromRequest($definedMeaningId);
	}

	function edit() {
		global 
			$wgOut, $wgTitle, $wgUser, $wgRequest;
		
		$sectionToEdit = $wgRequest->getText('section');
		
		if ($sectionToEdit != "")
			$sectionArguments = explode("-", $sectionToEdit);
		else
			$sectionArguments = array();
		
		if ($wgRequest->getText('save') != '')
			$this->saveForm($sectionArguments);
					
		$userlang = $wgUser->getOption('language');
		$skin = $wgUser->getSkin();

		$wgOut->addHTML("Your user interface language preference: <b>$userlang</b> - " . $skin->makeLink("Special:Preferences", "set your preferences"));
		$wgOut->addHTML('<form method="post" action="">');

		if (count($sectionArguments) == 0)
			$this->displaySpellingEditForm($wgTitle->getText());
		else {
			$definedMeaningIds = $this->getDefinedMeaningIdsForSectionArguments($sectionArguments);							
			$this->displayPartialEditForm($sectionArguments[0], $definedMeaningIds);
		}

		$wgOut->addHTML(getSubmitButton("save", "Save"));
		$wgOut->addHTML('</form>');
	}
	
	function getDefinedMeaningIdsForSectionArguments($sectionArguments) {
		if (count($sectionArguments) >= 2) 
			return array($sectionArguments[1]);
		else
			return $this->getDefinedMeaningsForExpression($sectionArguments[0]);
	}
	
	function displaySpellingEditForm($spelling) {
		global
			$wgOut;
		
		$dbr =& wfGetDB(DB_SLAVE);
		$queryResult = $dbr->query("SELECT expression_id, spelling, language_id FROM uw_expression_ns WHERE spelling=BINARY ".$dbr->addQuotes($spelling));

		while ($expression = $dbr->fetchObject($queryResult)) {
			$expressionId = $expression->expression_id;
			$definedMeaningIds = $this->getDefinedMeaningsForExpression($expressionId);
			$this->displayExpressionEditForm($expression->spelling, $expressionId, $expression->language_id, $definedMeaningIds);			
		}
	}
	
	function displayPartialEditForm($expressionId, $definedMeaningIds) {
		$dbr =& wfGetDB(DB_SLAVE);
		$queryResult = $dbr->query("SELECT spelling, language_id FROM uw_expression_ns WHERE expression_id=$expressionId");
		
		if ($expression = $dbr->fetchObject($queryResult))
			$this->displayExpressionEditForm($expression->spelling, $expressionId, $expression->language_id, $definedMeaningIds);
	}
	
	function displayExpressionEditForm($spelling, $expressionId, $languageId, $definedMeaningIds) {
		global
			$wgOut, $wgLanguageNames;
		
		$wgOut->addHTML("<h2><i>Spelling:</i>" . $spelling . " - <i>Language:</i> ".$wgLanguageNames[$languageId]."</h2>");

		$synonymAndTranslationTables = $this->getSynonymAndTranslationTables($definedMeaningIds, $expressionId);
		$definedMeaningTexts = $this->getDefinedMeaningTexts($definedMeaningIds);
		$definedMeaningRelations = $this->getDefinedMeaningRelations($definedMeaningIds);

		$wgOut->addHTML('<ul>');
		foreach ($definedMeaningIds as $definedMeaningId) {
			$wgOut->addHTML('<li>');			
			$this->displayDefinedMeaningEditForm($definedMeaningId, $synonymAndTranslationTables[$definedMeaningId], $definedMeaningTexts[$definedMeaningId]);
			$wgOut->addHTML('</li>');
		}
		$wgOut->addHTML('</ul>');
	}
	
	function displayDefinedMeaningEditForm($definedMeaningId, $synonymAndTranslationTable, $definedMeaningTextId) {
		global
			$wgOut, $wgLanguageNames;
		
		$wgOut->addHTML("<h3>Definition</h3>");
		$translatedContents = $this->getTranslatedContents($definedMeaningTextId);

		foreach($translatedContents as $languageId => $textId) {
			$wgOut->addHTML("<div><i>$wgLanguageNames[$languageId]</i></div>".
		    	            getTextArea("definition-$textId", $this->getText($textId)));
		}

		$wgOut->addHTML('<div><i>Translate into</i>: '.
						$this->getLanguageSelect("translated-definition-language-$definedMeaningId", array_keys($translatedContents)).'</div>'.
		                getTextArea("translated-definition-$definedMeaningId"));

		$wgOut->addHTML('<table border="0" cellpadding="5"><tr valign="top"><td>'); 
		$wgOut->addHTML('<h4>Translations and synonyms</h4>');
		$wgOut->addHTML(getTableAsHTML($synonymAndTranslationTable));
		$wgOut->addHTML($this->getAddTranslationsAndSynonymsFormFields($definedMeaningId));

		$wgOut->addHTML('</td><td>');

		$wgOut->addHTML($this->getAddRelationsFormFields($definedMeaningId));

		$wgOut->addHTML('</td></tr></table>');
	}
	
	function getDefinedMeaningsForExpression($expressionId) {
		$dbr =& wfGetDB(DB_SLAVE);
		$definedMeanings = array();
		$queryResult = $dbr->query("SELECT defined_meaning_id from uw_syntrans WHERE expression_id=$expressionId");
		
		while($definedMeaning = $dbr->fetchObject($queryResult)) 
			$definedMeanings[] = $definedMeaning->defined_meaning_id;
			
		return $definedMeanings;
	}
	
	function getDefinedMeaningTexts($definedMeaningIds) {
		$dbr =& wfGetDB(DB_SLAVE);
		$definedMeaningTexts = array();
	
		foreach($definedMeaningIds as $definedMeaningId) {
			$queryResult = $dbr->query("SELECT meaning_text_tcid from uw_defined_meaning WHERE defined_meaning_id=$definedMeaningId and is_latest_ver=1");
			
			while($dm_row=$dbr->fetchObject($queryResult)) 
				$definedMeaningTexts[$definedMeaningId] = $dm_row->meaning_text_tcid;
		}
		
		return $definedMeaningTexts;
	}
	
	function getAlternativeMeaningTexts($definedMeaningIds) {
		$dbr =& wfGetDB(DB_SLAVE);
		$alternativeMeaningTexts = array();
	
		foreach($definedMeaningIds as $definedMeaningId) {
			$queryResult = $dbr->query("select meaning_text_tcid from uw_alt_meaningtexts where meaning_mid=$definedMeaningId and is_latest_set=1");
			
			while($alternativeMeaning = $dbr->fetchObject($queryResult)) 
				$alternativeMeaningTexts[$definedMeaningId][] = $alternativeMeaning->meaning_text_tcid;
		}
		
		return $alternativeMeaningTexts;
	}
	
	function getSpellingsPerDefinedMeaningAndLanguage($definedMeaningIds, $synonymsAndTranslationIds) {
		$spellingsPerDefinedMeaningAndLanguage = array();	
	
		foreach($definedMeaningIds as $definedMeaningId) {
			$spellingsPerLanguage = array();
			
			if (array_key_exists($definedMeaningId, $synonymsAndTranslationIds)) 
				foreach($synonymsAndTranslationIds[$definedMeaningId] as $synonymOrTranslation) {
					$spellingAndLanguage = $this->getSpellingAndLanguageForExpression($synonymOrTranslation);
					
					foreach($spellingAndLanguage as $language => $spelling) 
						$spellingsPerLanguage[$language][] = $spelling;					
				}
			
			$spellingsPerDefinedMeaningAndLanguage[$definedMeaningId] = $spellingsPerLanguage;
		}
		
		return $spellingsPerDefinedMeaningAndLanguage;
	}
	
	function getSpellingAndLanguageForExpression($expressionId) {
		$dbr =& wfGetDB(DB_SLAVE);
		$queryResult = $dbr->query("SELECT language_id, spelling from uw_expression_ns WHERE expression_id=$expressionId");
		$spellingAndLanguage = array();
		
		while($expression = $dbr->fetchObject($queryResult)) 
			$spellingAndLanguage[$expression->language_id] = $expression->spelling;					
		
		return $spellingAndLanguage;
	}
	
	function getSynonymAndTranslationIds($definedMeaningIds, $skippedExpressionId) {
		$dbr =& wfGetDB(DB_SLAVE);
		$synonymAndTranslationIds = array();
		
		foreach($definedMeaningIds as $definedMeaningId) {
			$queryResult = $dbr->query("SELECT expression_id from uw_syntrans where defined_meaning_id=$definedMeaningId and expression_id!=$skippedExpressionId");
		
			while($synonymOrTranslation = $dbr->fetchObject($queryResult)) 
				$synonymAndTranslationIds[$definedMeaningId][] = $synonymOrTranslation->expression_id;
		}
			
		return $synonymAndTranslationIds;
	}
	
	function getSynonymAndTranslationTables($definedMeaningIds, $skippedExpressionId) {
		$dbr =& wfGetDB(DB_SLAVE);
		$result = array();
		$attributes = array(new Attribute("Language", "language"), new Attribute("Spelling", "spelling"), new Attribute("Identical meaning?", "boolean"));
		
		foreach($definedMeaningIds as $definedMeaningId) {
			$table = new ArrayTable($attributes);
			$queryResult = $dbr->query("SELECT expression_id, endemic_meaning from uw_syntrans where defined_meaning_id=$definedMeaningId and expression_id!=$skippedExpressionId");
		
			while($synonymOrTranslation = $dbr->fetchObject($queryResult)) {
				$spellingAndLanguage = $this->getSpellingAndLanguageForExpression($synonymOrTranslation->expression_id);
				
				foreach($spellingAndLanguage as $languageId => $spelling) 
					$table->addRow(array($languageId, $spelling, $synonymOrTranslation->endemic_meaning));
			}	
			
			$result[$definedMeaningId] = $table;
		}
			
		return $result;
	}
	
	function getDefinedMeaningRelations($definedMeaningIds) {
		$dbr =& wfGetDB(DB_SLAVE);
	    $definedMeaningRelations = array();
		
		foreach($definedMeaningIds as $definedMeaningId) {
			$relations = array();
			$queryResult = $dbr->query("SELECT * from uw_meaning_relations where meaning1_mid=$definedMeaningId and relationtype_mid!=0 and is_latest_set=1");
			
			while($definedMeaningRelation = $dbr->fetchObject($queryResult)) 
				$relations[$definedMeaningRelation->relationtype_mid][] = $definedMeaningRelation->meaning2_mid;
						
			$definedMeaningRelations[$definedMeaningId] = $relations;
		}
		
		return $definedMeaningRelations;
	}

	function getDefinedMeaningAttributes($definedMeaningIds) {
		$dbr =& wfGetDB(DB_SLAVE);
		$definedMeaningAttributes = array();
		
		foreach($definedMeaningIds as $definedMeaningId) {
			$attributes = array();
			$queryResult = $dbr->query("SELECT * from uw_meaning_relations where meaning1_mid=$definedMeaningId and relationtype_mid=0 and is_latest_set=1");
			
			while($attribute = $dbr->fetchObject($queryResult)) 
				$attributes[] = $attribute->meaning2_mid;
			
			$definedMeaningAttributes[$definedMeaningId] = $attributes;
		}

		return $definedMeaningAttributes;	
	}
	
	function getTranslatedContents($setId) {
		$dbr =& wfGetDB(DB_SLAVE);
		$translatedContents = array();

		$queryResult = $dbr->query("SELECT * from translated_content where set_id=$setId");
	
		while($translatedContent = $dbr->fetchObject($queryResult)) 
			$translatedContents[$translatedContent->language_id] = $translatedContent->text_id;
		
		return $translatedContents;
	}
	
	function getAddTranslationsAndSynonymsFormFields($definedMeaningId) {
		return '<h4>Add translation/synonym</h4>
				<table>
				<tr><th>Language</th><th>Spelling</th><th>Identical meaning?</th><th>Input rows</th></tr>
				<tr id="add-translation-synonym-'. $definedMeaningId .'" class="repeat">
					<td>'.$this->getLanguageSelect("language-$definedMeaningId").'</td>
					<td>'.getTextBox("spelling-$definedMeaningId") .'</td>
				    <td>'.getCheckBox("endemic-meaning-$definedMeaningId", true). '</td>
				    <td></td>		
				</tr>
				</table>';
	}
	
	function getAddRelationsFormFields($definedMeaningId) {
		return '<h4>Add relation</h4>
				<table>
					<tr><th>Relation type</th><th>Other defined meaning</th></tr>
					<tr><td>' .	$this->getRelationTypeSuggest($definedMeaningId) . '</td><td>' . $this->getDefinedMeaningSuggest($definedMeaningId) . '</td></tr>
				</table>';
	}
	
	function getTranslationIdsForDefinedMeaning($definedMeaningId) {
		$dbr =& wfGetDB(DB_SLAVE);
		$queryResult = $dbr->query("SELECT * from text where old_id=$textId");
	}
	
	function getLanguageOptions($languageIdsToExclude = array()) {
		global 
			$wgUser;
			
		$userLanguage = $wgUser->getOption('language');
		$idNameIndex = getLangNames($userLanguage);
		
		$result = array();
		
		foreach($idNameIndex as $id => $name) 
			if (!in_array($id, $languageIdsToExclude)) 
				$result[$id] = $name;
		
		return $result;
	}
	
	function getLanguageSelect($name, $languageIdsToExclude = array()) {
		global 
			$wgUser;
			
		$userLanguage = $wgUser->getOption('language');
		$userLanguageId = getLanguageIdForCode($userLanguage);

		return getSelect($name, $this->getLanguageOptions($languageIdsToExclude), $userLanguageId);
	}
	
	function getText($textId) {
		$dbr =& wfGetDB(DB_SLAVE);
		$queryResult = $dbr->query("SELECT old_text from text where old_id=$textId");

		if($text = $dbr->fetchObject($queryResult)) 
			return $text->old_text;
		else
			return "";
	}
	
	function setText($textId, $text) {
		$dbr = &wfGetDB(DB_MASTER);
		$text = $dbr->addQuotes($text);
		$sql = "UPDATE text SET old_text=$text WHERE old_id=$textId";	
		$dbr->query($sql);
	}
	
	function createText($text) {
		$dbr = &wfGetDB(DB_MASTER);
		$text = $dbr->addQuotes($text);
		$sql = "insert into text(old_text) values($text)";	
		$dbr->query($sql);
		
		return $dbr->insertId();
	}
	
	function addSynonymOrTranslation($spelling, $languageId, $definedMeaningId, $endemicMeaning) {
		$expression = findOrCreateExpression($spelling, $languageId);
		$expression->assureIsBoundToDefinedMeaning($definedMeaningId, $endemicMeaning);
	}
	
	function addSynonymOrTranslationFromRequest($definedMeaningId, $postFix) {
		global
			$wgRequest;
	
		if (array_key_exists('language-'. $postFix, $_POST)) {
			$languageId = $wgRequest->getInt('language-'. $postFix);
			$spelling = trim($wgRequest->getText('spelling-'. $postFix));
			$endemicMeaning = $wgRequest->getCheck('endemic-meaning-'.$postFix);
			
			if ($spelling != '')
				$this->addSynonymOrTranslation($spelling, $languageId, $definedMeaningId, $endemicMeaning);			
		} 
	}
	
	function addSynonymsOrTranslationsFromRequest($definedMeaningId) {
		global	
			$wgRequest;
			
		$this->addSynonymOrTranslationFromRequest($definedMeaningId, $definedMeaningId);
		
		for ($i = 2; $i <= $wgRequest->getInt('add-translation-synonym-'. $definedMeaningId . '-RC'); $i++) 
			$this->addSynonymOrTranslationFromRequest($definedMeaningId, $definedMeaningId . '-' . $i);
	}
	
	function createTranslatedContent($setId, $languageId, $textId, $revisionId) {
		$dbr = &wfGetDB(DB_MASTER);
		$sql = "insert into translated_content(set_id,language_id,text_id,first_set,revision_id) values($setId, $languageId, $textId, $setId, $revisionId)";	
		$dbr->query($sql);
		
		return $dbr->insertId();
	}
	
	function addTranslatedDefinition($setId, $languageId, $definition, $revisionId) {
		$textId = $this->createText($definition);
		$this->createTranslatedContent($setId, $languageId, $textId, $revisionId);
	}
	
	function addTranslatedDefinitionFromRequest($definedMeaningId, $setId, $revisionId, $languageIdsToExclude) {
		global
			$wgRequest;	
		
		$languageId = $wgRequest->getInt('translated-definition-language-'.$definedMeaningId);
		$definition = trim($wgRequest->getText('translated-definition-'.$definedMeaningId));

		if ($definition != '' && !in_array($languageId, $languageIdsToExclude)) 
			$this->addTranslatedDefinition($setId, $languageId, $definition, $revisionId);		
	}

	function getAttributeValues(){
		$atts=array();
		$attcollections=$this->getCollectionsByType('ATTR');
		$dbr =& wfGetDB( DB_SLAVE );	
		foreach($attcollections as $cname=>$cid) {
			$att_res=$dbr->query("select member_mid from uw_collection_contents where collection_id=$cid and is_latest_set=1");
			while($att_row=$dbr->fetchObject($att_res)) {
				# fixme hardcoded English
				$att_name=$this->getExpressionForMid($att_row->member_mid, 85);
				$atts[$att_row->member_mid]=$att_name;
			}
		}
		return $atts;
	}

	function getCollectionsByType($type) {
		$typecollections=array();
		$dbr =& wfGetDB ( DB_SLAVE );
		$col_res=$dbr->query("select collection_id,collection_mid from uw_collection_ns where collection_type=".$dbr->addQuotes($type)." and is_latest=1");
		while($col_row=$dbr->fetchObject($col_res)) {
			# fixme hardcoded English
			$collection_name=$this->getExpressionForMid($col_row->collection_mid,85);
			$typecollections[$collection_name]=$col_row->collection_id;
		}
		return $typecollections;
	
	}

	function getExpressionForMid($mid,$langcode) {
		$dbr =& wfGetDB(DB_SLAVE);
		$sql="SELECT spelling from uw_syntrans,uw_expression_ns where defined_meaning_id=".$mid." and uw_expression_ns.expression_id=uw_syntrans.expression_id and uw_expression_ns.language_id=".$langcode." limit 1";
		$sp_res=$dbr->query($sql);
		$sp_row=$dbr->fetchObject($sp_res);
		return $sp_row->spelling;
		#return $sql;
	}

	function addRelationFromRequest($definedMeaningId) {
		global
			$wgRequest;
		
		$relationTypeId = $wgRequest->getInt("new-relation-type-$definedMeaningId");
		$otherDefinedMeaningId = $wgRequest->getInt("new-relation-other-meaning-$definedMeaningId");
		  
		if ($relationTypeId != 0 && $otherDefinedMeaningId != 0)
			$this->addRelation($definedMeaningId, $relationTypeId, $otherDefinedMeaningId);
	}
	
	function getSetIdForDefinedMeaningRelations($definedMeaningId) {
		$dbr =& wfGetDB(DB_SLAVE);
		$sql = "SELECT set_id from uw_meaning_relations where meaning1_mid=$definedMeaningId and is_latest_set=1 limit 1";
		$queryResult = $dbr->query($sql);
				
		$setId = $dbr->fetchObject($queryResult)->set_id;
		
		if (!$setId) {
			$sql = "SELECT max(set_id) as max_id from uw_meaning_relations";
			$queryResult = $dbr->query($sql);
			$setId = $dbr->fetchObject($queryResult)->max_id + 1;
		}
		
		return $setId;		
	}
	
	function getLatestRevisionForDefinedMeaning($definedMeaningId) {
		$dbr =& wfGetDB(DB_SLAVE);
		$sql = "SELECT revision_id from uw_defined_meaning where defined_meaning_id=$definedMeaningId and is_latest_ver=1 limit 1";
		$queryResult = $dbr->query($sql);
		
		return $dbr->fetchObject($queryResult)->revision_id;
	}
	
	function relationExists($setId, $definedMeaning1Id, $relationTypeId, $definedMeaning2Id) {
		$dbr =& wfGetDB(DB_MASTER);
		$queryResult = $dbr->query("SELECT * FROM uw_meaning_relations WHERE set_id=$setId AND meaning1_mid=$definedMeaning1Id AND meaning2_mid=$definedMeaning2Id AND relationtype_mid=$relationTypeId AND is_latest_set=1");
		
		return $dbr->numRows($queryResult) > 0;
	}
	
	function addRelation($definedMeaning1Id, $relationTypeId, $definedMeaning2Id) {
		$setId = $this->getSetIdForDefinedMeaningRelations($definedMeaning1Id);
		$revisionId = $this->getLatestRevisionForDefinedMeaning($definedMeaning1Id);
		
		if (!$this->relationExists($setId, $definedMeaning1Id, $relationTypeId, $definedMeaning2Id)) {
			$dbr =& wfGetDB(DB_MASTER);
			$sql = "insert into uw_meaning_relations(set_id, meaning1_mid, meaning2_mid, relationtype_mid, is_latest_set, first_set, revision_id) " .
					"values($setId, $definedMeaning1Id, $definedMeaning2Id, $relationTypeId, 1, $setId, $revisionId)";
			$dbr->query($sql);
		}
	}
}

?>
