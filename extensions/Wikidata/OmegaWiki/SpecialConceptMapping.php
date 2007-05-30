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

define('help_text',
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
				$wgOut->addWikiText("Apologies, I don't know how to '$action'.");	
				$wgOut->addWikiText(help_text);
			}
		}

		protected function ui() {
			global $wgOut;
			require_once("forms.php");
			
			$wgOut->addHTML("
					<p>Concept Mapping allows you to identify
					which defined meaning in one dataset is identical
					to defined meanings in other datasets.</p>\n
					<p>Please enter or cut and paste the defined 
					meanings (with id), or simply the defined meaning ids
					which are identical.</p>\n
					<p> For example, you could paste <code>DefinedMeaning:Boat (7774)</code>
					or simply type <code>7774</code>.</p>\n");
			
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
				
				$wgOut->addHTML("$key: $rq ($title)");
				if ($id!=null) {
					$mappings[$key]=$id;
					$wgOut->addHTML(' <span style="color:green">[OK]</span>');
				} else {
					$wgOut->addHTML(' <span style="color:red">[not present or malformed]</span>');
				}
				$wgOut->addHTML("<br>\n");	
			}
			if (sizeOf($mappings)>1) { 
				createConceptMapping($mappings);
				$wgOut->addHTML("Mapped all fields marked with [OK]<br>\n");
			} else {
				$wgOut->addHTML("Need to have at least two defined meanings before I can link them");
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
