<?php
if (!defined('MEDIAWIKI')) die();
/**
 * A Special Page extension to add languages, runnable by users with the 'addlanguage' right.
 * @addtogroup Extensions
 *
 * @author Erik Moeller <Eloquence@gmail.com>
 * @author Kim Bruning <kim@bruning.xs4all.nl>
 * @license public domain
 */


$wgExtensionFunctions[] = 'wfSpecialConceptMapping';
$wgExtensionCredits['specialpage'][] = array(
	'name' => 'foo',
	'author' => 'Kim Bruning',
);

define(help_text,
	"<p>possible actions: <ul>
	<li>&action=insert&<data_context_prefix>=<defined_id>&...  insert a mapping</li>
	<li>&action=get&concept=<concept_id>  read a mapping back</li>
	<li>&action=list_sets  return a list of possible data context prefixes and what they refer to.</li>
	<li>&action=get_associated&dm=<defined_meaning_id>&dc=<dataset_context_prefix> for one defined meaning in a concept, return all others</li>
	<li>&action=help   Show helpful help.</li>
	</ul></p>"); #brief help message




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
				$wgOut->addWikiText("You didn't set an action. Now kicking back and being lazy.<br>");
				$wgOut->addWikiText(help_text);
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
				$wgOut->addWikiText("Apologies, I don't know how to '$action'.");	
				$wgOut->addWikiText(help_text);
			}
		}

		protected function help() {
			global $wgOut;
			$wgOut->addWikiText("<h2>Help</h2>");
			$wgOut->addWikiText(help_text);
		}
		
		protected function insert() {
			global 
				$wgRequest, $wgOut;
			
			# $wgRequest->getText( 'page' );
			$sets=wdGetDataSets();
			#$requests=$wgRequest->getValues();
			$wgOut->addWikiText("<h2>Will insert the following:</h2>");
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
			$wgOut->addWikiText("<h2>Contents of mapping:</h2>");
			$map=readConceptMapping($concept_id);
			#$sets=wdGetDataSets();

			foreach ($map as $dc => $dm_id) {
				$wgOut->addWikiText("$dc -> $dm_id");
			}
		}

		protected function list_sets() {
			global $wgOut;
			$wgOut->addWikiText("<h2>available contexts</h2>");
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
?>
