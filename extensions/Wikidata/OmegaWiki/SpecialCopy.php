<?php
if (!defined('MEDIAWIKI')) die();
/**
 * A Special Page extension to copy defined meanings between datasets.
 * 
 * Copied over from SpecialConceptMapping.
 * User Interface temporarily retained (but currently flawed)
 * Web API will be implemented
 * Minimal documentation is available by calling with &action=help, as a parameter
 * @addtogroup Extensions
 *
 * @author Erik Moeller <Eloquence@gmail.com>	(Possibly some remaining code)
 * @author Kim Bruning <kim@bruning.xs4all.nl>
 # @author Alan Smithee <Alan.Smithee@brown.paper.bag> (if code quality improves, may yet claim)
 * @license GPLv2 or later.
 */


$wgExtensionFunctions[] = 'wfSpecialCopy';
$wgExtensionCredits['specialpage'][] = array(
	'name' => 'SpecialCopy',
	'author' => 'Alan Smithee',
);

function wfSpecialCopy() {
	# Add messages
	#require_once "$IP/includes/SpecialPage.php";

        global $wgMessageCache;
        $wgMessageCache->addMessages(array('Copy'=>'Wikidata: Copy'),'en');
                
	require_once("Wikidata.php");
	require_once("WikiDataAPI.php");
	require_once("Utilities.php");
	require_once("DefinedMeaningModel.php");
	require_once("Copy.php");
	class SpecialCopy extends SpecialPage {

		function SpecialCopy() {
			SpecialPage::SpecialPage( 'Copy' );
		}
		function execute( $par ) {
			global $wgOut, $wgRequest, $wgTitle, $wgUser, $wdTermDBDataSet;
			$wgOut->setPageTitle("Special:Copy");

			if(!$wgUser->isAllowed('wikidata-copy')) {
				$wgOut->addHTML(wfMsgSci("Permission_denied"));
				return false;
			}

			$action=$wgRequest->getText('action');
			if(!$action) {
				$this->ui();
			} elseif ($action=="copy") {
				$this->copy_by_param();
			} elseif ($action=="list") {
				$this->list_sets();
			} elseif ($action=="help"){
				$this->help();
			} else {
				$wgOut->addWikiText(wfMsgSc("copy_no_action_specified",$action));	
				$wgOut->addWikiText(wfMsgSc("copy_help"));
			}
		}

		protected function ui() {

			global $wgOut, $wgRequest, $wgUser;
			$lang=$wgUser->getOption("language");
			require_once("forms.php");			
			$wgOut->addHTML(wfMsgSc("conceptmapping_uitext"));
			$sets=wdGetDataSets();
			$options = array();
			$html="";
			$mappings=array();
			$rq=array();

			foreach ($sets as $key=>$setObject) {
				$set=$setObject->getPrefix();
				$rq[$set]=$wgRequest->getText("set_".$set);
				$rq[$set]=trim($rq[$set]);
				$rq[$set]=(int)$rq[$set];
				if($rq[$set]) {
					$dmModel=new DefinedMeaningModel($rq[$set],null,$setObject);
					$defaultSel=$dmModel->getSyntransByLanguageCode($lang);
					$options[$setObject->fetchName()]=getSuggest("set_$set", 'defined-meaning',array(), $rq[$set], $defaultSel, array(0), $setObject);
				} else {
					$options[$setObject->fetchName()]=getSuggest("set_$set", 'defined-meaning', array(), null, null, array(0), $setObject);
				}

			}
			$wgOut->addHTML(getOptionPanel($options));
			$noerror=$wgRequest->getText("suppressWarnings");

			foreach ($sets as $key=>$setObject) {
				$set=$setObject->getPrefix();
				if(!$rq[$set]) {
					$wgOut->addHTML(' <span style="color:yellow">['.wfMsgSc("dm_not_present").']</span>');
				} else  {
					$dmModel=new DefinedMeaningModel($rq[$set],null,$setObject);
					$dmModel->checkExistence();
					if ($dmModel->exists()) {
						$id=$dmModel->getId();
						$title=$dmModel->getTitleText();
					} else {
						$id=null;
						$title=null;
					}
					if(!$noerror) {
						$wgOut->addHTML("$key: ".$rq[$set]." ($title)");
					}
					if ($id!=null) {
						$mappings[$key]=$id;
						if(!$noerror) {
							$wgOut->addHTML(' <span style="color:green">['.wfMsgSc("dm_OK").']</span>');
						}
					} else {
						if(!$noerror) {
							$wgOut->addHTML(' <span style="color:red">['.wfMsgSc("dm_not_found").']</span>');
						}
					}
				}
				$wgOut->addHTML("<br>\n");	
			}
			if (sizeOf($mappings)>1) { 
				createConceptMapping($mappings);
				$wgOut->addHTML(wfMsgSc("mapping_successful"));
			} else {
				$wgOut->addHTML(wfMsgSc("mapping_unsuccessful"));
			}

		}

		protected function help() {
			global $wgOut;
			$wgOut->addWikiText("<h2>Help</h2>");
			$wgOut->addWikiText(wfMsgSc("copy_help"));
		}
		
		/**read in and partially validate parameters,
		 * then call _doCopy()
		 */
		protected function copy_by_param() {
			global 
				$wgRequest, $wgOut;
			
			$dmid_dirty=$wgRequest->getText("dmid");
			$dc1_dirty=$wgRequest->getText("dc1");
			$dc2_dirty=$wgRequest->getText("dc2");	

			$abort=false; 	# check all input before aborting

			if (is_null($dmid_dirty)) {
				$wgOut->addWikiText(wfMsgSc("please_provide_dmid"));
				$abort=true;
			}
			if (is_null($dc1_dirty)) {
				$wgOut->addWikiText(wfMsgSc("please_provide_dc1"));
				$abort=true;
			}
			if (is_null($dc2_dirty)) {
				$wgOut->addWikiText(wfMsgSc("please_provide_dc2"));
				$abort=true;
			}

			if ($abort)
				return;

			#seems ok so far, let's try and copy.
			$success=$this->_doCopy($dmid_dirty, $dc1_dirty, $dc2_dirty);
			if ($success)
				$wgOut->addWikiText(wfMsgSc("copy_successful"));
			else
				$wgOut->addWikiText(wfMsgSc("copy_unsuccessful"));
		}


		protected function _doCopy($dmid_dirty, $dc1_dirty, $dc2_dirty) {
			global 
				$wgCommunityEditPermission, $wgOut, $wgUser, $wgCommunity_dc;
			$dmid=mysql_real_escape_string($dmid_dirty);
			$dc1=mysql_real_escape_string($dc1_dirty);
			$dc2=mysql_real_escape_string($dc2_dirty);

			if (!($wgUser->isAllowed($wgCommunityEditPermission)) or $dc2!=$wgCommunity_dc) {
				$wgOut->addHTML(wfMsgSc("Permission_denied"));
				return false; #"houston, we have a problem"
			}
			CopyTools::newCopyTransaction($dc1, $dc2);
			$dmc=new DefinedMeaningCopier($dmid, $dc1, $dc2); #sorry, not a [[delorean]]
			$dmc->dup(); 

			if ($dmc->already_there() ) {
				$wgOut->addHTML(wfMsgSc("already_there"));
				return false;
			}
			# Do we need this here? Or is there already transaction
			# management on speial pages. :-/
			# mysql_query("COMMIT");	# force commit where no autocommit
						# full mysql transactions mihgt	be a good plan
			return true; # seems everything went ok.
	
		}
	}
	SpecialPage::addPage( new SpecialCopy );
	
}

