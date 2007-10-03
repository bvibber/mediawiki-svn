<?php
	if (!defined('MEDIAWIKI')) die();

	require_once("WikiDataAPI.php"); // for bootstrapCollection
	require_once("Utilities.php"); 
	
	$wgAvailableRights[] = 'importtsv';
	$wgGroupPermissions['bureaucrat']['importtsv'] = true;
	$wgExtensionFunctions[] = 'wfSpecialImportTSV';

	function wfSpecialImportTSV() {
	        global $wgMessageCache;
                $wgMessageCache->addMessages(array('importtsv'=>'Wikidata: Import TSV'),'en');
                        
		class SpecialImportTSV extends SpecialPage {
			
			function SpecialImportTSV() {
				SpecialPage::SpecialPage('ImportTSV');
			}

			function execute($par) {

				global $wgOut, $wgUser, $wgRequest;

				if (!$wgUser->isAllowed('importtsv')) {
					$wgOut->addHTML('You do not have permission to do a tsv import.');
					return false;
				}
				
				$dbr =& wfGetDB(DB_MASTER);
				$dc = wdGetDataSetcontext();
				
				if ($wgRequest->getFileName('tsvfile')) {
					// process tsv
					
					require_once('WikiDataAPI.php');
					require_once('Transaction.php');
					$file = fopen($wgRequest->getFileTempname('tsvfile'),'r');
					$columns = fgetcsv($file, 1024, "\t");
					
					// somehow testing for $columns[0] fails sometimes. Byte Order Mark?
//					if (!$columns || count($columns) <= 2 || $columns[1] != "defining expression") {
//						$wgOut->addHTML("<p>This does not appear to be a valid tsv file.</p>");
//						return;
//					}
					
					// TODO verify column names
					// TODO verify the max line length
					
					startNewTransaction($wgUser->getID(), wfGetIP(), "Bulk import via SpecialImportTSV", $dc);
					
					$row = "";
					$line = 1; // actually 2, 1 was the header, but increased at the start of while
					$definitions = 0; // definitions added
					$translations = 0; // translations added
							
					while($row = fgetcsv($file, 8196, "\t")) { 
						$line++; 
						if (count($row) != count($columns)) {
							$wgOut->addHTML("Skipped line $line: incorrect column count.<br/>");
							continue;
						}
						
						$dmid = $row[0];
						$exp = $row[1];
						
						// find the defined meaning record
						$qry = "SELECT dm.meaning_text_tcid, exp.spelling, dm.remove_transaction_id ";
						$qry .= "FROM {$dc}_defined_meaning dm INNER JOIN {$dc}_expression exp ON dm.expression_id=exp.expression_id ";
						$qry .= "WHERE dm.defined_meaning_id=$dmid";
						
						$dmResult = $dbr->query($qry);
						$dmRecord = null;
						// perfomr some tests
						if ($dmRecord = $dbr->fetchRow($dmResult)) {
							if ($dmRecord['spelling'] != $exp) {
								$wgOut->addHTML("Skipped line $line: defined meaning id $dmid does not match defining expression. Should be '$exp', found '{$dmRecord['spelling']}'.<br/>");
								continue;
							}
							if ($dmRecord['remove_transaction_id']) {
								$wgOut->addHTML("Skipped line $line: defined meaning has been removed.<br/>");
								continue;
							}
						}
						else {
							$wgOut->addHTML("Skipped line $line: incorrect defined meaning id $dmid.<br/>");
							continue; 
						}
						
						
						// all is well. Get the translated content id
						$tcid = $dmRecord['meaning_text_tcid'];
						
						
						for ($columnIndex = 2; $columnIndex < count($columns); $columnIndex++) {
							
							$columnValue = $row[$columnIndex];
							if (!$columnValue) {
								continue;
							}
						
							$columnName = $columns[$columnIndex];
							$langCode = substr($columnName, strrpos($columnName, '_')+1);
							$langId = getLanguageIdForIso639_3($langCode);
							if (strpos($columnName, 'definition') === 0) {
								if (!translatedTextExists($tcid, $langId)) {
									addTranslatedText($tcid, $langId, $columnValue);
									$wgOut->addHTML("Added definition for $exp ($dmid) in $langCode: $columnValue.<br/>");
									$definitions++;
								}
							}
							if (strpos($columnName, 'translation') === 0) {
								$spellings = explode('|', $columnValue);
								foreach ($spellings as $spelling) {
									$spelling = trim($spelling);
									$expression = findExpression($spelling, $langId);
									if (!$expression) { // expression does not exist
										$expression = createExpression($spelling, $langId);
										$expression->bindToDefinedMeaning($dmid, 1);

										// not nescesary to check page exists, createPage does that.
										$title = getPageTitle($spelling);
										createPage(16, $title);

										$wgOut->addHTML("Added translation for $exp ($dmid) in $langCode: $spelling. Also added a new page.<br/>");
										$translations++;
									} 
									else { // expression exists, but may not be bound to this defined meaning.
										if (!$expression->isBoundToDefinedMeaning($dmid)) {
											$expression->bindToDefinedMeaning($dmid, 1);

											$wgOut->addHTML("Added translation for $exp ($dmid) in $langCode: $spelling.<br/>");
											$translations++;
										}
									}
								}
							}
						}
					}
					
					
					if ($definitions == 0 && $translations == 0) {
						$wgOut->addHTML("<br/>Nothing added.<br/>");
					}
					else {
						$wgOut->addHTML("<br/>Added $definitions definitions and $translations translations.<br/>");
					}
						
				}
				else {
					// render the page
					$wgOut->setPageTitle('Import a collection from tsv (tab separated values)');
//					$wgOut->addHTML('<p>Export a collection to a tab delimited text format that you can import in OpenOffice.org, Excel or other spreadsheet software.<br/>');
//					$wgOut->addHTML('The collection is fixed right now, do not change it. In the languages text box, enter a comma separated list of ');
//					$wgOut->addHTML('ISO 639-3 languages codes. Start with the languages that you will be translating from (pick as many as you like) and ');
//					$wgOut->addHTML('finish with the ones you\'ll be translating to.</p>');
					
					$wgOut->addHTML(getOptionPanelForFileUpload(
						array(
							'TSV File' => getFileField('tsvfile')
						),
						'',array('upload' => 'Upload')
					));
				}

			}
			
			
			/* HELPER METHODS START HERE */
			
			function getLanguage($columnName) {
				
			}
			
		}

		SpecialPage::addPage(new SpecialImportTSV);
	}

