<?php
if (!defined('MEDIAWIKI')) die();
/**
 * A Special Page extension to create concept-mappings
 * also provides a web-api. Minimal documentation is available by calling with &action=help, as a parameter
 * @addtogroup Extensions
 *
 * @author Erik Moeller <Eloquence@gmail.com>
 * @author Kim Bruning <kim@bruning.xs4all.nl>
 * @license public domain or GPL? (requesting info)
 */


$wgExtensionFunctions[] = 'wfSpecialConceptMapping';
$wgExtensionCredits['specialpage'][] = array(
	'name' => 'foo',
	'author' => 'Kim Bruning',
);

function wfSpecialConceptMapping() {
	# Add messages
	#require_once "$IP/includes/SpecialPage.php";

	require_once("Wikidata.php");
	require_once("WikiDataAPI.php");
	class SpecialConceptMapping extends SpecialPage {

		function SpecialConceptMapping() {
			SpecialPage::SpecialPage( 'ConceptMapping' );
		}

		function execute( $par ) {
			global $wgOut, $wgRequest, $wgTitle, $wgUser;
			$wgOut->setPageTitle("ConceptMapping");
			$action=$wgRequest->getText('action');
			if(!$action) {
				$this->ui();
			} elseif ($action=="insert"){
				$this->insert();
			} elseif ($action=="get"){
				$this->get();
			} elseif ($action=="list_sets"){
				$this->list_sets();
			} elseif ($action=="help"){
				$this->help();
			} elseif ($action=="get_associated"){
				$this->get_associated();
			} else {
				$wgOut->addWikiText(wfMsg('ow_conceptmapping_no_action_specified',$action));	
				$wgOut->addWikiText(wfMsg('ow_conceptmapping_help'));
			}
		}

		protected function ui() {
			global $wgOut;
			require_once("forms.php");
			
			$wgOut->addHTML(wfMsg('ow_conceptmapping_uitext'));
			$sets=wdGetDataSets();
			$options = array();
			$html="";
			foreach ($sets as $key=>$set) {
				$options[$set->fetchName()]=$this->getDm($set);
			}
			$wgOut->addHTML(getOptionPanel($options));
			#debug
			global $wgRequest;
			$mappings=array();
			foreach ($sets as $key=>$set) {
				$rq=$wgRequest->getText("set_".$key);
				$noerror=$wgRequest->getText("suppressWarnings");
				$rq=trim($rq);
				$dmData=new DefinedMeaningData();
				$dmData->setDataset($set);
				$dmData->setTitleText($rq); #is $rq a page title?
				if ($dmData->getId()==null) { #guess not
					$dmData->setId($rq); # maybe it's a defined meaning id?
				}
				$dmData->canonicalize();
				$id=null;
				$title=null;
				if ($dmData->exists()) {
					$id=$dmData->getId();
					$title=$dmData->getTitleText();
				}
				if(!$noerror) {
					$wgOut->addHTML("$key: $rq ($title)");
				}
				if ($id!=null) {
					$mappings[$key]=$id;
					if(!$noerror) {
						$wgOut->addHTML(' <span style="color:green">['.wfMsg('ow_OK').']</span>');
					}
				} else {
					if(!$noerror) {
						$wgOut->addHTML(' <span style="color:red">['.wfMsg('ow_not_present_or_malformed').']</span>');
					}
				}
				$wgOut->addHTML("<br>\n");	
			}
			if (sizeOf($mappings)>1) { 
				createConceptMapping($mappings);
				$wgOut->addHTML(wfMsg('ow_mapping_successful'));
			} else {
				$wgOut->addHTML(wfMsg('ow_mapping_unsuccessful'));
			}

		}

		protected function getDm($dataset) {
			global $wgRequest;
			$setname="set_".$dataset->getPrefix();
			$rq=$wgRequest->getText($setname);
			$html=getTextBox($setname, $rq);
			return $html;
		}

		
		protected function help() {
			global $wgOut;
			$wgOut->addWikiText("<h2>Help</h2>");
			$wgOut->addWikiText(wfMsg('ow_conceptmapping_help'));
		}
		
		protected function insert() {
			global 
				$wgRequest, $wgOut;
			
			# $wgRequest->getText( 'page' );
			$sets=wdGetDataSets();
			#$requests=$wgRequest->getValues();
			$wgOut->addWikiText("<h2>".wfMsg('ow_will_insert')."</h2>");
			$map=array();
			foreach ($sets as $key => $set) {
				$dc=$set->getPrefix();
				$dm_id=$wgRequest->getText($dc);
				$name=$set->fetchName();

				$dm_id_ui=$dm_id; # Only for teh purdy
				if ($dm_id_ui==null)
					$dm_id_ui="unset";  
				$wgOut->addWikiText("$name ->$dm_id_ui");
				$map[$dc]=$dm_id;
			#$dbr=&wfGetDB(DB_MASTER);
			}
			createConceptMapping($map);
		}

		protected function get() {
			global 
				$wgOut, $wgRequest;
			$concept_id=$wgRequest->getText("concept");
			$wgOut->addWikiText("<h2>".wfMsg('ow_contents_of_mapping')."</h2>");
			$map=readConceptMapping($concept_id);
			#$sets=wdGetDataSets();

			foreach ($map as $dc => $dm_id) {
				$wgOut->addWikiText("$dc -> $dm_id");
			}
		}

		protected function list_sets() {
			global $wgOut;
			$wgOut->addWikiText("<h2>".wfMsg('available contexts')."</h2>");
			$sets=wdGetDataSets();
			foreach ($sets as $key => $set) {
				$name=$set->fetchName();
				$wgOut->addWikiText("$key => $name");
			}
		}

		protected function get_associated() {
			global $wgOut, $wgRequest;
			$dm_id=$wgRequest->getText("dm");
			$dc=$wgRequest->getText("dc");
			$map=getAssociatedByConcept($dm_id, $dc);
			foreach ($map as $dc => $dm_id) {
				$wgOut->addWikiText("$dc -> $dm_id");
			}
		}

	}

	SpecialPage::addPage( new SpecialConceptMapping );
	
}

