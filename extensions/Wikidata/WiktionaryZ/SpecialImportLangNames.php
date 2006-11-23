<?php
	if (!defined('MEDIAWIKI')) die();

	$wgAvailableRights[] = 'languagenames';
	$wgGroupPermissions['bureaucrat']['languagenames'] = true;

	$wgExtensionFunctions[] = 'wfSpecialImportLangNames';

	function wfSpecialImportLangNames() {
		class SpecialImportLangNames extends SpecialPage {
			function SpecialImportLangNames() {
				SpecialPage::SpecialPage('ImportLangNames');
			}

			function execute($par) {
				global $wgOut, $wgUser;

				require_once('Transaction.php');

				$wgOut->setPageTitle('Import Language Names');

				if (!$wgUser->isAllowed('languagenames')) {
					$wgOut->addHTML('You do not have permission to import language names.');
					return false;
				}

				$dbr = &wfGetDB(DB_MASTER);

				/* Get collection ID for "ISO 639-3 codes" collection. */
				$sql = 'SELECT collection_id FROM uw_collection_ns' .
					' JOIN uw_defined_meaning ON defined_meaning_id = collection_mid' .
					' JOIN uw_expression_ns ON' .
					' uw_defined_meaning.expression_id = uw_expression_ns.expression_id' .
					' WHERE spelling LIKE "ISO 639-3 codes"' .
					' AND ' . getLatestTransactionRestriction('uw_collection_ns') .
					' LIMIT 1';
				$collection_id_res = $dbr->query($sql);
				$collection_id = $this->fetchResult($dbr->fetchRow($collection_id_res));

				/* Get defined meaning IDs and ISO codes for languages in collection. */
				$sql = 'SELECT member_mid,internal_member_id FROM uw_collection_contents' .
					' WHERE collection_id = ' . $collection_id .
					' AND ' . getLatestTransactionRestriction('uw_collection_contents');
				$lang_res = $dbr->query($sql);
				while ($lang_row = $dbr->fetchRow($lang_res)) {
					$iso_code = $lang_row['internal_member_id'];
					$dm_id = $lang_row['member_mid'];
		
					/*	Get the language ID for the current language. */
					$sql = 'SELECT language_id FROM language' .
						' WHERE iso639_3 LIKE ' . $dbr->addQuotes($iso_code) .
						' LIMIT 1';
					$lang_id_res = $dbr->query($sql);
					if ($dbr->numRows($lang_id_res))
						$wgOut->addHTML('Language names for "' . $iso_code . '" added. <br />');
					else {
						$wgOut->addHTML('<strong>No language entry for "' . $iso_code . '" found! </strong><br />');
						continue;
					}
					$lang_id = $this->fetchResult($dbr->fetchRow($lang_id_res));

					/*	Delete all language names that match current language ID. */
					$sql = 'DELETE FROM language_names' .
						' WHERE language_id = ' . $lang_id;
					$dbr->query($sql);

					/*	Get syntrans expressions for names of language and IDs for the languages the names are in. */
					$sql = 'SELECT spelling,language_id FROM uw_expression_ns' .
						' JOIN uw_syntrans' .
						' ON uw_expression_ns.expression_id = uw_syntrans.expression_id' .
						' WHERE defined_meaning_id = ' . $dm_id .
						' AND ' . getLatestTransactionRestriction('uw_expression_ns') .
						' AND ' . getLatestTransactionRestriction('uw_syntrans') .
						' GROUP BY language_id ORDER BY NULL';
					$syntrans_res = $dbr->query($sql);
					while ($syntrans_row = $dbr->fetchRow($syntrans_res)) {
						$sql = 'INSERT INTO language_names' .
							' (`language_id`,`name_language_id`,`language_name`)' .
							' VALUES(' . $lang_id . ', ' .
							$syntrans_row['language_id'] . ', ' .
							$dbr->addQuotes($syntrans_row['spelling']) . ')';
						$dbr->query($sql);
					}
				}
			}

			function fetchResult($row) {
				return $row[0];
			}

		}

		SpecialPage::addPage(new SpecialImportLangNames);
	}
?>