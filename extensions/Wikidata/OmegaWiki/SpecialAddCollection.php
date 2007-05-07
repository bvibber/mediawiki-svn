<?php
	if (!defined('MEDIAWIKI')) die();

	$wgAvailableRights[] = 'addcollection';
	$wgGroupPermissions['bureaucrat']['addcollection'] = true;

	$wgExtensionFunctions[] = 'wfSpecialAddCollection';

	function wfSpecialAddCollection() {
		class SpecialAddCollection extends SpecialPage {
			function SpecialAddCollection() {
				SpecialPage::SpecialPage('AddCollection');
			}

			function execute($par) {
				global $wgOut, $wgUser, $wgRequest;

				$wgOut->setPageTitle('Add Collection');

				if (!$wgUser->isAllowed('addcollection')) {
					$wgOut->addHTML('You do not have permission to add a collection.');
					return false;
				}

				$dbr = &wfGetDB(DB_MASTER);

				if ($wgRequest->getText('collection')) {
					require_once('WikiDataAPI.php');
					require_once('Transaction.php');

					$collectionName = $wgRequest->getText('collection');
					startNewTransaction($wgUser->getID(), wfGetIP(), 'Add collection ' . $collectionName);
					bootstrapCollection($collectionName,$wgRequest->getText('language'),$wgRequest->getText('type'));
					$wgOut->addHTML('<strong>Collection ' . $collectionName . ' added.</strong><br />');	
				}

				$wgOut->addHTML(getOptionPanel(
					array(
						'Collection name' => getTextBox('collection'),
						'Language of name' => getSuggest('language','language'),
						'Collection type' => getSelect('type',array('' => 'None','RELT' => 'RELT','LEVL' => 'LEVL','CLAS' => 'CLAS'))
					),
					'',array('create' => 'Create')
				));
			}
		}

		SpecialPage::addPage(new SpecialAddCollection);
	}
?>
