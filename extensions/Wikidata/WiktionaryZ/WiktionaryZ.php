<?php

require_once('wikidata.php');
require_once('Expression.php');
require_once('forms.php');
require_once('relation.php');
require_once('type.php');
require_once('languages.php');

function getLatestRevisionForDefinedMeaning($definedMeaningId) {
	$dbr =& wfGetDB(DB_SLAVE);
	$sql = "SELECT revision_id from uw_defined_meaning where defined_meaning_id=$definedMeaningId and is_latest_ver=1 limit 1";
	$queryResult = $dbr->query($sql);
	
	return $dbr->fetchObject($queryResult)->revision_id;
}
	
function relationExists($setId, $definedMeaning1Id, $relationTypeId, $definedMeaning2Id) {
	$dbr =& wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT * FROM uw_meaning_relations WHERE set_id=$setId AND meaning1_mid=$definedMeaning1Id AND meaning2_mid=$definedMeaning2Id AND relationtype_mid=$relationTypeId AND is_latest_set=1");
	
	return $dbr->numRows($queryResult) > 0;
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

function addRelation($definedMeaning1Id, $relationTypeId, $definedMeaning2Id) {
	$setId = getSetIdForDefinedMeaningRelations($definedMeaning1Id);
	$revisionId = getLatestRevisionForDefinedMeaning($definedMeaning1Id);
	
	if (!relationExists($setId, $definedMeaning1Id, $relationTypeId, $definedMeaning2Id)) {
		$dbr =& wfGetDB(DB_MASTER);
		$sql = "insert into uw_meaning_relations(set_id, meaning1_mid, meaning2_mid, relationtype_mid, is_latest_set, first_set, revision_id) " .
				"values($setId, $definedMeaning1Id, $definedMeaning2Id, $relationTypeId, 1, $setId, $revisionId)";
		$dbr->query($sql);
	}
}


class SynonymTranslationController implements PageElementController {
	protected $definedMeaningId;
	
	public function __construct($definedMeaningId) {
		$this->definedMeaningId = $definedMeaningId;
	}
	
	public function add($values) {
		$languageId = $values[0];		
		$spelling = $values[1];
		$endemicMeaning = $values[2];
		
		if ($spelling != '') {
			$expression = findOrCreateExpression($spelling, $languageId);
			$expression->assureIsBoundToDefinedMeaning($this->definedMeaningId, $endemicMeaning);
		}
	}
}

class DefinedMeaningRelationController implements PageElementController {
	protected $definedMeaningId;
	
	public function __construct($definedMeaningId) {
		$this->definedMeaningId = $definedMeaningId;
	}
	
	public function add($values) {
		$relationTypeId = $values[0];
		$otherDefinedMeaningId = $values[1];
		  
		if ($relationTypeId != 0 && $otherDefinedMeaningId != 0)
			addRelation($this->definedMeaningId, $relationTypeId, $otherDefinedMeaningId);
	}	
}

class DefinedMeaningAttributeController implements PageElementController {
	protected $definedMeaningId;
	
	public function __construct($definedMeaningId) {
		$this->definedMeaningId = $definedMeaningId;
	}
	
	public function add($values) {
		$attributeId = $values[0];
		  
		if ($attributeId != 0)
			addRelation($this->definedMeaningId, 0, $attributeId);
	}	
}

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

		$res=$dbr->query("SELECT * from uw_expression_ns WHERE spelling=BINARY ".$dbr->addQuotes($wgTitle->getText()));

		while($row=$dbr->fetchObject($res)) {
			$expressionId = $row->expression_id;
			$definedMeaningIds = $this->getDefinedMeaningsForExpression($expressionId);
			$definedMeaningTexts = $this->getDefinedMeaningTexts($definedMeaningIds);
			$alternativeMeaningTexts = $this->getAlternativeMeaningTexts($definedMeaningIds);

			$wgOut->addHTML($skin->editSectionLink($wgTitle, $expressionId));
			$wgOut->addHTML("<h2><i>Spelling</i>: $row->spelling - <i>Language:</i> ".$wgLanguageNames[$row->language_id]. "</h2>");

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
 				
 				$wgOut->addHTML('<div class="wiki-data-blocks">');
 					$pageElements = $this->getDefinedMeaningPageElements($definedMeaningId, $expressionId);
 					
 					foreach($pageElements as $pageElement)
 						$this->displayPageElement($pageElement);
				$wgOut->addHTML('</div>');

				$wgOut->addHTML('<div class="clear-float"/>');
				$wgOut->addHTML('</li>');
			}
			$wgOut->addHTML('</ul>');
		}

		# We may later want to disable the regular page component
		# $wgOut->setPageTitleArray($this->mTitle->getTitleArray());
	}
	
	function displayPageElement($pageElement) {
		addWikiDataBlock($pageElement->getCaption(), getRelationAsHTML($pageElement->getRelationModel()));
	}
	
	function editPageElement($pageElement) {
		$inputRow = array();
		$addId = "add-".$pageElement->getId();
		
		foreach($pageElement->getRelationModel()->getAttributes() as $attribute) 
			$inputRow[] = getInputFieldForType($addId . "-" . $attribute->id, $attribute->type, "");
		
		addWikiDataBlock($pageElement->getCaption(), getRelationAsEditHTML($pageElement->getRelationModel(), $addId,
														$inputRow, $pageElement->repeatInput()));
	}
	
	function addRowForPageElement($pageElement, $postFix) {
		$addId = "add-".$pageElement->getId();
		$attributes = $pageElement->getRelationModel()->getAttributes();
		
		if (array_key_exists($addId . "-" . $attributes[0]->id . $postFix, $_POST)) {
			$values = array();
			
			foreach($attributes as $attribute)
				$values[] = getFieldValueForType($addId . "-". $attribute->id . $postFix, $attribute->type);
			
			$pageElement->getController()->add($values);
		}
	}
	
	function savePageElement($pageElement) {
		global
			$wgRequest;
		
		if ($pageElement->getController()) {
			$addId = "add-" . $pageElement->getId();
			$inputRow = array();
			$rowCount = $wgRequest->getInt($addId . '-RC');
		
			$this->addRowForPageElement($pageElement, "");
				
			for ($i = 2; $i <= $rowCount; $i++) 
				$this->addRowForPageElement($pageElement, '-' . $i);
		}				
	}
	
	function getSynonymsAndTranslationsPageElement($definedMeaningId, $expressionId) {
		return new DefaultPageElement("synonym-translation-$definedMeaningId", "Translations and synonyms", 
										$this->getSynonymAndTranslationRelation($definedMeaningId, $expressionId), 
										false, 
										true,
										new SynonymTranslationController($definedMeaningId));
	}
	
	function getDefinedMeaningRelationsPageElement($definedMeaningId) {
		return new DefaultPageElement("defined-meaning-relation-$definedMeaningId", "Relations", 
										$this->getDefinedMeaningRelationsRelation($definedMeaningId), 
										false, 
										false,
										new DefinedMeaningRelationController($definedMeaningId));
	}
	
	function getDefinedMeaningAttributesPageElement($definedMeaningId) {
		return new DefaultPageElement("defined-meaning-attribute-$definedMeaningId", "Attributes", 
										$this->getDefinedMeaningAttributesRelation($definedMeaningId), 
										false, 
										false,
										new DefinedMeaningAttributeController($definedMeaningId));
	}
	
	function getDefinedMeaningPageElements($definedMeaningId, $expressionId) {
		return array($this->getSynonymsAndTranslationsPageElement($definedMeaningId, $expressionId), 
						$this->getDefinedMeaningRelationsPageElement($definedMeaningId),
						$this->getDefinedMeaningAttributesPageElement($definedMeaningId));
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

		if (count($translatedContents))
			$this->addTranslatedDefinitionFromRequest($definedMeaningId, $definedMeaningTextId, getRevisionForExpressionId($expressionId), array_keys($translatedContents));
			
		$pageElements = $this->getDefinedMeaningPageElements($definedMeaningId, $expressionId);
		
		foreach($pageElements as $pageElement)
			$this->savePageElement($pageElement);	
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

		$definedMeaningTexts = $this->getDefinedMeaningTexts($definedMeaningIds);

		$wgOut->addHTML('<ul>');
		foreach ($definedMeaningIds as $definedMeaningId) {
			$wgOut->addHTML('<li>');			
			$this->displayDefinedMeaningEditForm($definedMeaningId, $expressionId, $this->getSynonymAndTranslationRelation($definedMeaningId, $expressionId), $definedMeaningTexts[$definedMeaningId]);
			$wgOut->addHTML('</li>');
		}
		$wgOut->addHTML('</ul>');
	}
	
	function displayDefinedMeaningEditForm($definedMeaningId, $expressionId, $synonymAndTranslationTable, $definedMeaningTextId) {
		global
			$wgOut, $wgLanguageNames;
		
		$wgOut->addHTML("<h3>Definition</h3>");
		$translatedContents = $this->getTranslatedContents($definedMeaningTextId);

		foreach($translatedContents as $languageId => $textId) {
			$wgOut->addHTML("<div><i>$wgLanguageNames[$languageId]</i></div>".
		    	            getTextArea("definition-$textId", $this->getText($textId)));
		}

		if (count($translatedContents) > 0) {
			$wgOut->addHTML('<div><i>Translate into</i>: '.
							getLanguageSelect("translated-definition-language-$definedMeaningId", array_keys($translatedContents)).'</div>'.
		        	        getTextArea("translated-definition-$definedMeaningId"));
		}

	 	$wgOut->addHTML('<div class="wiki-data-blocks">');
	 	$pageElements = $this->getDefinedMeaningPageElements($definedMeaningId, $expressionId);	
	 	
	 	foreach($pageElements as $pageElement) 
	 		$this->editPageElement($pageElement);
	 	
		$wgOut->addHTML('</div>');
		
		$wgOut->addHTML('<div class="clear-float"/>');
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
	
	function getSynonymAndTranslationRelation($definedMeaningId, $skippedExpressionId) {
		$dbr =& wfGetDB(DB_SLAVE);
		$attributes = array(new Attribute("language", "Language", "language"), new Attribute("spelling", "Spelling", "spelling"), new Attribute("endemic_meaning", "Identical meaning?", "boolean"));
		$relation = new ArrayRelation($attributes);
		$queryResult = $dbr->query("SELECT expression_id, endemic_meaning from uw_syntrans where defined_meaning_id=$definedMeaningId and expression_id!=$skippedExpressionId");
	
		while($synonymOrTranslation = $dbr->fetchObject($queryResult)) {
			$spellingAndLanguage = $this->getSpellingAndLanguageForExpression($synonymOrTranslation->expression_id);
			
			foreach($spellingAndLanguage as $languageId => $spelling) 
				$relation->addTuple(array($languageId, $spelling, $synonymOrTranslation->endemic_meaning));
		}	
		
		return $relation;
	}
	
	function getDefinedMeaningRelationsRelation($definedMeaningId) {
		$attributes = array(new Attribute("relation-type", "Relation type", "relation-type"), new Attribute("other-defined_meaning", "Other defined meaning", "defined-meaning"));
		$relation = new ArrayRelation($attributes);
		
		$dbr =& wfGetDB(DB_SLAVE);
		$queryResult = $dbr->query("SELECT relationtype_mid, meaning2_mid from uw_meaning_relations where meaning1_mid=$definedMeaningId and relationtype_mid!=0 and is_latest_set=1 ORDER BY relationtype_mid");
			
		while($definedMeaningRelation = $dbr->fetchObject($queryResult))
			$relation->addTuple(array($this->getExpressionForMeaningId($definedMeaningRelation->relationtype_mid, 85), 
										$this->getExpressionForMeaningId($definedMeaningRelation->meaning2_mid, 85))); 
		
		return $relation;
	}
	
	function getDefinedMeaningAttributesRelation($definedMeaningId) {
		$attributes = array(new Attribute("attribute", "Attribute", "attribute"));
		$relation = new ArrayRelation($attributes);
		
		$dbr =& wfGetDB(DB_SLAVE);
		$queryResult = $dbr->query("SELECT relationtype_mid, meaning2_mid from uw_meaning_relations where meaning1_mid=$definedMeaningId and relationtype_mid=0 and is_latest_set=1");
			
		while($attribute = $dbr->fetchObject($queryResult))
			$relation->addTuple(array($this->getExpressionForMeaningId($attribute->meaning2_mid, 85))); 
		
		return $relation;
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
	
	function getTranslationIdsForDefinedMeaning($definedMeaningId) {
		$dbr =& wfGetDB(DB_SLAVE);
		$queryResult = $dbr->query("SELECT * from text where old_id=$textId");
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
				$att_name=$this->getExpressionForMeaningId($att_row->member_mid, 85);
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
			$collection_name=$this->getExpressionForMeaningId($col_row->collection_mid,85);
			$typecollections[$collection_name]=$col_row->collection_id;
		}
		return $typecollections;
	
	}

	function getExpressionForMeaningId($mid, $langcode) {
//		$dbr =& wfGetDB(DB_SLAVE);
//		$sql="SELECT spelling from uw_syntrans,uw_expression_ns where defined_meaning_id=".$mid." and uw_expression_ns.expression_id=uw_syntrans.expression_id and uw_expression_ns.language_id=".$langcode." limit 1";
//		$sp_res=$dbr->query($sql);
//		$sp_row=$dbr->fetchObject($sp_res);
//		return $sp_row->spelling;
		$expressions = $this->getExpressionsForDefinedMeaningIds(array($mid)); 
		return $expressions[$mid];
	}
	
	# Fixme, the following function only returns English expressions
	# Should be expressions in the language of preference, with an appropriate fallback scheme
	function getExpressionsForDefinedMeaningIds($definedMeaningIds) {
		$dbr =& wfGetDB(DB_SLAVE);
		$queryResult = $dbr->query("SELECT defined_meaning_id, spelling from uw_syntrans, uw_expression_ns where defined_meaning_id in (". implode(",", $definedMeaningIds) . ") and uw_expression_ns.expression_id=uw_syntrans.expression_id and uw_expression_ns.language_id=85 and uw_syntrans.endemic_meaning=1");
		$expressions = array();
		
		while ($expression = $dbr->fetchObject($queryResult)) 
			if (!array_key_exists($expression->defined_meaning_id, $expressions))
				$expressions[$expression->defined_meaning_id] = $expression->spelling;
		
		return $expressions;
	}
}

?>
