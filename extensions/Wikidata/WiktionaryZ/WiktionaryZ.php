<?php

require_once('Expression.php');
require_once('forms.php');

/**
 * Renders a content page from WiktionaryZ based on the GEMET database.
 * @package MediaWiki
 */
class WiktionaryZ {
	/* TODOs:
		use $dbr->select() instead of $dbr->query() wherever possible; it lets MediaWiki handle additional
		table prefixes and such.
	*/
	protected $sectionToEdit = 0;
	protected $currentSection = 0;
	protected $inSection = false;
	protected $inSectionLevel = 0;
	
	function initializeSections($sectionToEdit) {
		$this->currentSection = 0;
		$this->inSection = $sectionToEdit == 0;
		$this->inSectionLevel = 0;
		$this->sectionToEdit = $sectionToEdit;
	}
	
	function addSection($level) {
		if ($this->sectionToEdit != 0) {
			$this->currentSection++;
			
			if ($this->currentSection == $this->sectionToEdit) {
				$this->inSection = true;
				$this->inSectionLevel = $level;	
			}
			else if ($level == $this->inSectionLevel) 
				$this->inSection = false;
		}
			
		return $this->inSection;
	}
	
	function view() {
		global $wgOut, $wgTitle, $wgUser;
		$userlang=$wgUser->getOption('language');

		# $w is the variable used to store generated wikitext
		$wgOut->addWikiText("Your user interface language preference: '''".$userlang."''' - [[Special:Preferences|set your preferences]]");

		# Get language names, preferably in UI language
		$langdefs=$this->getLangNames($userlang);

		$dbr =& wfGetDB( DB_MASTER );

		# Get entry record from GEMET namespace
		$res=$dbr->query("SELECT * from uw_expression_ns WHERE spelling=BINARY ".$dbr->addQuotes($wgTitle->getText()));

		while($row=$dbr->fetchObject($res)) {
			$tcids=array();
			$dms=array();
			$syntrans=array();
			$oids=array();
			$rels=array();

			$wgOut->addWikiText("\n== ''Spelling: ''" . $row->spelling . " - ''Language:'' ".$langdefs[$row->language_id]." ==\n");

			# Get meanings via Expression ID
			$st_res=$dbr->query("SELECT defined_meaning_id from uw_syntrans WHERE expression_id=".$row->expression_id);
			while($st_row=$dbr->fetchObject($st_res)) {
				$dms[]=$st_row->defined_meaning_id;
				# Get synonyms and translations for each
				$li_res=$dbr->query("SELECT expression_id from uw_syntrans where defined_meaning_id=".$st_row->defined_meaning_id." and expression_id!=".$row->expression_id);
				while($li_row=$dbr->fetchObject($li_res)) {
					$syntrans[$st_row->defined_meaning_id][]=$li_row->expression_id;
				}

			}

			# Get meaning text IDs
			foreach($dms as $mid) {
				$dm_res=$dbr->query("SELECT meaning_text_tcid from uw_defined_meaning WHERE defined_meaning_id=".$mid." and is_latest_ver=1");
				while($dm_row=$dbr->fetchObject($dm_res)) {
					$tcids[$mid][]=$dm_row->meaning_text_tcid;
				}
				$alt_res=$dbr->query("select meaning_text_tcid from uw_alt_meaningtexts where meaning_mid=".$mid." and is_latest_set=1");
				while($alt_row=$dbr->fetchObject($alt_res)) {
					$tcids[$mid][]=$alt_row->meaning_text_tcid;
				}
				
				$transl=array();
				if(array_key_exists($mid,$syntrans)) {
					foreach($syntrans[$mid] as $liid) {
						$sp_res=$dbr->query("SELECT * from uw_expression_ns WHERE expression_id=".$liid);
						while($sp_row=$dbr->fetchObject($sp_res)) {
							$transl[$sp_row->language_id][]=$sp_row->spelling;					
						}
					}
				}
				$translmid[$mid]=$transl;
			}

				
			# Get relations
			$meaning_rels=array();
			foreach($dms as $mid) {
				$rels=array();
				$rt_res=$dbr->query("SELECT * from uw_meaning_relations where meaning1_mid=".$mid." and relationtype_mid!=0 and is_latest_set=1");
				while($rt_row=$dbr->fetchObject($rt_res)) {
					$rels[$rt_row->relationtype_mid][]=$rt_row->meaning2_mid;
				}
				$meaning_rels[$mid]=$rels;

			}
			
			# Get attributes
			$attrib_rels=array();
			foreach($dms as $mid) {
				$atts=array();
				$att_res=$dbr->query("SELECT * from uw_meaning_relations where meaning1_mid=".$mid." and relationtype_mid=0 and is_latest_set=1");
				while($att_row=$dbr->fetchObject($att_res)) {
					$atts[]=$att_row->meaning2_mid;
				}
				$attrib_rels[$mid]=$atts;
			}
			
			$typenames=$this->getRelationTypes();
			$attnames=$this->getAttributeValues();

			foreach($dms as $mid) {
				$oids=array();
				$wgOut->addWikiText("\n\n===Definition===\n");
				foreach($tcids[$mid] as $tc) {
					$tc_res=$dbr->query("SELECT * from translated_content where set_id=".$tc);
					while($tc_row=$dbr->fetchObject($tc_res)) {
						$oids[$tc_row->language_id][]=$tc_row->text_id;
					}
				}

				foreach($oids as $lang=>$oid) {
					foreach($oid as $oid_d) {
						$wgOut->addWikiText("\n\n'''''$langdefs[$lang]'''''\n");
						$t_res=$dbr->query("SELECT * from text where old_id=".$oid_d);
						while($t_row=$dbr->fetchObject($t_res)) {
							$wgOut->addHTML(htmlspecialchars($t_row->old_text));
						}
					}
				}
				# Get spellings of translations and synonyms
				$wgOut->addHTML("<table border='0' cellpadding='5'><tr valign='top'><td width='20%'>");
				$wgOut->addWikiText("\n'''Translations and Synonyms'''\n");
				foreach($translmid[$mid] as $lang=>$splist) {
					foreach($splist as $spl) {
						if(!empty($spl)) {
							$wgOut->addWikiText("* ''".$langdefs[$lang]."'': [[WiktionaryZ:$spl|$spl]]\n");
						}
					}
				}
	
				# Relations
				$wgOut->addHTML("</td><td>");
				$wgOut->addWikiText("\n'''Relations:'''\n");

				$rels=$meaning_rels[$mid];
				foreach($rels as $type=>$rellist) {
					$wgOut->addWikiText("\n$typenames[$type]:\n");
					foreach($rellist as $rel) {
						$rs_res=$dbr->query("SELECT expression_id from uw_defined_meaning where defined_meaning_id=".$rel." LIMIT 1");
						$rs_row=$dbr->fetchObject($rs_res);
						if($rs_row->expression_id) {
							$li_res=$dbr->query("SELECT spelling from uw_expression_ns where expression_id=".$rs_row->expression_id);
							$li_row=$dbr->fetchObject($li_res);
							$wgOut->addWikiText("* [[WiktionaryZ:".$li_row->spelling."|"."$li_row->spelling]]\n");
						}
					}
				}
				$wgOut->addWikiText("\n\n'''Attributes:'''\n");
				$atts=$attrib_rels[$mid];
				foreach($atts as $att) {
					$wgOut->addWikiText("* [[WiktionaryZ:".$attnames[$att]."|".$attnames[$att]."]]\n");
				}
				
				$wgOut->addHTML("</td></tr></table>");
			}
		}

		# We may later want to disable the regular page component
		# $wgOut->setPageTitleArray($this->mTitle->getTitleArray());
	}

	# Falls back to English if no language name translations available for chosen languages
	function getLangNames($code) {
		$id=$this->getLanguageIdForCode($code);
		if(!$id) $id=$this->getLanguageIdForCode('en');
		$names=$this->getLanguageNamesForId($id);
		if(empty($names)) {
			$id=$this->getLanguageIdForCode('en');
			$names=$this->getLanguageNamesForId($id);
		}
		return $names;
	}
	
	function getLanguageIdForCode($code) {
		$dbr =& wfGetDB( DB_SLAVE );
		$id_res=$dbr->query("select language_id from language where wikimedia_key='".$code."'");
		$id_row=$dbr->fetchObject($id_res);
		return $id_row->language_id;
	}
	
	function getLanguageNamesForId($id) {
		$dbr =& wfGetDB( DB_SLAVE );
		$langs=array();
		$lang_res=$dbr->query("select language_names.language_id,language_names.language_name,language.wikimedia_key from language,language_names where language_names.name_language_id=".$id." and language.language_id=language_names.name_language_id");
		while($lang_row=$dbr->fetchObject($lang_res)) {
			$langs[$lang_row->language_id]=$lang_row->language_name;
		}
		return $langs;
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
	
	function checkForm() {
		return true;
	}	

	function saveForm() {
		global 
			$wgTitle, $wgUser, $wgRequest, $wgOut;
		
		$userlang = $wgUser->getOption('language');

		# Get language names, preferably in UI language
		$langdefs=$this->getLangNames($userlang);

		$this->initializeSections($wgRequest->getInt('section'));
		$dbr =& wfGetDB( DB_MASTER );

		# Get entry record from GEMET namespace
		$res=$dbr->query("SELECT * from uw_expression_ns WHERE spelling=BINARY ".$dbr->addQuotes($wgTitle->getText()));

		while($row=$dbr->fetchObject($res)) {
			$this->addSection(1);
			$expressionId = $row->expression_id;
			$definedMeaningIds = $this->getDefinedMeaningsForExpression($expressionId);
			$synonymsAndTranslationIds = $this->getSynonymAndTranslationIds($definedMeaningIds, $expressionId);
			$definedMeaningTexts = $this->getDefinedMeaningTexts($definedMeaningIds);
			$definedMeaningRelations = $this->getDefinedMeaningRelations($definedMeaningIds);

			foreach($definedMeaningRelations as $definedMeaningId => $relations) {
				if ($this->addSection(2)) {
					$translatedContents = $this->getTranslatedContents($definedMeaningTexts[$definedMeaningId]);
					
					foreach($translatedContents as $languageId => $textId) {
						$definition = trim($wgRequest->getText('definition-'.$textId));
						   	 
						if ($definition != '')
							$this->setText($textId, $definition);
					}
	
					$this->addTranslatedDefinitionFromRequest($definedMeaningId, $definedMeaningTexts[$definedMeaningId], getRevisionForExpressionId($expressionId), array_keys($translatedContents));
					$this->addSynonymsOrTranslationsFromRequest($definedMeaningId);
					$this->addRelationFromRequest($definedMeaningId);
				}				
			}
		}
		
		Title::touchArray(array($wgTitle));
	}

	function edit() {
		global 
			$wgOut, $wgTitle, $wgUser, $wgRequest;
		
		if ($wgRequest->getText('save') != '')
			$this->saveForm();
					
		$this->initializeSections($wgRequest->getInt('section'));

		$userlang = $wgUser->getOption('language');

		# $w is the variable used to store generated wikitext
		$wgOut->addWikiText("Your user interface language preference: '''$userlang''' - [[Special:Preferences|set your preferences]]");
		$wgOut->addHTML('<form method="post" action="">');

		# Get language names, preferably in UI language
		$langdefs=$this->getLangNames($userlang);

		$dbr =& wfGetDB(DB_MASTER);

		# Get entry record from GEMET namespace
		$queryResult = $dbr->query("SELECT * from uw_expression_ns WHERE spelling=BINARY ".$dbr->addQuotes($wgTitle->getText()));
		
		while($row = $dbr->fetchObject($queryResult)) {
			if ($this->addSection(1))
				$wgOut->addHTML("<h2> <i>Spelling: </i>" . $row->spelling . " - <i>Language:</i> ".$langdefs[$row->language_id]." </h2>");

			$expressionId = $row->expression_id;
			$definedMeaningIds = $this->getDefinedMeaningsForExpression($expressionId);
			$synonymsAndTranslationIds = $this->getSynonymAndTranslationIds($definedMeaningIds, $expressionId);
			$spellingsPerDefinedMeaningAndLanguage = $this->getSpellingsPerDefinedMeaningAndLanguage($definedMeaningIds, $synonymsAndTranslationIds);
			$definedMeaningTexts = $this->getDefinedMeaningTexts($definedMeaningIds);
			$definedMeaningRelations = $this->getDefinedMeaningRelations($definedMeaningIds);

			foreach ($definedMeaningRelations as $definedMeaningId => $relations) {
				if ($this->addSection(2)) {
					$wgOut->addHTML('<table border="0" cellpadding="5"><tr valign="top"><td width="20%">'); 
					$wgOut->addHTML('<b>Translations and synonyms</b>');
						
					foreach($spellingsPerDefinedMeaningAndLanguage[$definedMeaningId] as $languageId => $spellings) {
						$languageName = $langdefs[$languageId];
						
						foreach($spellings as $spelling) 
							if(!empty($spelling)) 
								$wgOut->addWikiText("* ''$languageName'': [[WiktionaryZ:$spelling|$spelling]]\n");
					}
		
					$wgOut->addHTML('</td><td>');
	
					$wgOut->addHTML("<div><b>Definition</b></div>");
					$translatedContents = $this->getTranslatedContents($definedMeaningTexts[$definedMeaningId]);
	
					foreach($translatedContents as $languageId => $textId) {
						$wgOut->addHTML("<div><i>$langdefs[$languageId]</i></div>".
						                getTextArea("definition-$textId", $this->getText($textId)));
					}
					
					$wgOut->addHTML('<div><i>Translate into</i>: '.
									$this->getLanguageSelect("translated-definition-language-$definedMeaningId", array_keys($translatedContents)).'</div>'.
					                getTextArea("translated-definition-$definedMeaningId"));
	
					$wgOut->addHTML('</td></tr></table>'. $this->getAddTranslationsAndSynonymsFormFields($definedMeaningId));
					$wgOut->addHTML($this->getAddRelationsFormFields($definedMeaningId));
				}
			}
		}

		$wgOut->addHTML(getSubmitButton("save", "Save"));
		$wgOut->addHTML('</form>');
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
	
	function getSpellingsPerDefinedMeaningAndLanguage($definedMeaningIds, $synonymsAndTranslationIds) {
		$dbr =& wfGetDB(DB_SLAVE);
		$spellingsPerDefinedMeaningAndLanguage = array();	
	
		foreach($definedMeaningIds as $definedMeaningId) {
			$spellingsPerLanguage = array();
			
			if (array_key_exists($definedMeaningId, $synonymsAndTranslationIds)) 
				foreach($synonymsAndTranslationIds[$definedMeaningId] as $synonymOrTranslation) {
					$queryResult = $dbr->query("SELECT * from uw_expression_ns WHERE expression_id=$synonymOrTranslation");
					
					while($expression = $dbr->fetchObject($queryResult)) 
						$spellingsPerLanguage[$expression->language_id][] = $expression->spelling;					
				}
			
			$spellingsPerDefinedMeaningAndLanguage[$definedMeaningId] = $spellingsPerLanguage;
		}
		
		return $spellingsPerDefinedMeaningAndLanguage;
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
	
	function getDefinedMeaningRelations($definedMeaningIds) {
		$dbr =& wfGetDB(DB_SLAVE);
	    $definedMeaningRelations = array();
		
		foreach($definedMeaningIds as $definedMeaningId) {
			$relations = array();
			$queryResult = $dbr->query("SELECT * from uw_meaning_relations where meaning1_mid=$definedMeaningId and is_latest_set=1");
			
			while($definedMeaningRelation = $dbr->fetchObject($queryResult)) 
				$relations[$definedMeaningRelation->relationtype_mid][]=$definedMeaningRelation->meaning2_mid;
						
			$definedMeaningRelations[$definedMeaningId] = $relations;
		}
		
		return $definedMeaningRelations;
	}
	
	function getTranslatedContents($setId) {
		$dbr =& wfGetDB(DB_SLAVE);
		$queryResult = $dbr->query("SELECT * from translated_content where set_id=$setId");
		$translatedContents = array();
		
		while($translatedContent = $dbr->fetchObject($queryResult)) 
			$translatedContents[$translatedContent->language_id] = $translatedContent->text_id;
		
		return $translatedContents;
	}
	
	function getAddTranslationsAndSynonymsFormFields($definedMeaningId) {
		return '<h3>Add translation/synonym</h3>
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
		return '<h3>Add relation</h3>
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
		$idNameIndex = $this->getLangNames($userLanguage);
		
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
		$userLanguageId = $this->getLanguageIdForCode($userLanguage);

		return getSelect($name, $this->getLanguageOptions($languageIdsToExclude), $userLanguageId);
	}
	
	function getText($textId) {
		$dbr =& wfGetDB(DB_SLAVE);
		$queryResult = $dbr->query("SELECT * from text where old_id=$textId");

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
	
	function addRelation($definedMeaning1Id, $relationTypeId, $definedMeaning2Id) {
		$setId = $this->getSetIdForDefinedMeaningRelations($definedMeaning1Id);
		$revisionId = $this->getLatestRevisionForDefinedMeaning($definedMeaning1Id);
		
		$dbr =& wfGetDB(DB_MASTER);
		$sql = "insert into uw_meaning_relations(set_id, meaning1_mid, meaning2_mid, relationtype_mid, is_latest_set, first_set, revision_id) " .
				"values($setId, $definedMeaning1Id, $definedMeaning2Id, $relationTypeId, 1, $setId, $revisionId)";
		$dbr->query($sql);
	}
}

?>
