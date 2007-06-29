<?php

require_once('Wikidata.php');
require_once('OmegaWikiRecordSets.php');
require_once('OmegaWikiEditors.php');
require_once('DefinedMeaningModel.php');

class DefinedMeaning extends DefaultWikidataApplication {
	public function view() {
		global
			$wgOut, $wgTitle, $wgRequest;

		$this->showDataSetPanel=false;
		parent::view();

		$view_as=$wgRequest->getText('view_as');

		if ($view_as=="raw") {
			$wgOut->disable();
			
			header("Content-Type: text/plain; charset=UTF-8");
			echo($this->raw());
			return;
		}
		$this->outputViewHeader();

		// Obtain ID from title of the form "DefinedMeaning:Foo (1234)" for database lookup
		$definedMeaningId = $this->getDefinedMeaningIdFromTitle($wgTitle->getText());
		
		$wgOut->addHTML($this->getConceptPanel());

		$dmModel=new DefinedMeaningModel(
				$definedMeaningId, 
				$this->filterLanguageId,
				$this->possiblySynonymousRelationTypeId, 
				$this->viewQueryTransactionInformation);

		$wgOut->addHTML(
			getDefinedMeaningEditor(
				$this->filterLanguageId, $this->possiblySynonymousRelationTypeId, false, $this->shouldShowAuthorities)->view(
				$this->getIdStack($definedMeaningId), 
				$dmModel->getRecord()
			)
		);
		$this->outputViewFooter();
	}
	
	public function edit() {
		global
			$wgOut, $wgTitle;

		if(!parent::edit()) return false;

		$definedMeaningId = $this->getDefinedMeaningIdFromTitle($wgTitle->getText());

		$this->outputEditHeader();
		$dmModel=new DefinedMeaningModel(
			$definedMeaningId, 
			$this->filterLanguageId, 
			$this->possiblySynonymousRelationTypeId, 
			new QueryLatestTransactionInformation()
		);
		$wgOut->addHTML(
			getDefinedMeaningEditor($this->filterLanguageId, $this->possiblySynonymousRelationTypeId, false, false)->edit(
				$this->getIdStack($definedMeaningId), 
				$dmModel->getRecord()
			)
		);
		$this->outputEditFooter();
	}
	
	public function history() {
		global
			$wgOut, $wgTitle;

		parent::history();

		$definedMeaningId = $this->getDefinedMeaningIdFromTitle($wgTitle->getText());
		$dmModel=new DefinedMeaningModel(
					$definedMeaningId, 
					$this->filterLanguageId, 
					$this->possiblySynonymousRelationTypeId, 
					$this->queryTransactionInformation
				);
		$wgOut->addHTML(
			getDefinedMeaningEditor($this->filterLanguageId, $this->possiblySynonymousRelationTypeId, $this->showRecordLifeSpan, false)->view(
				new IdStack("defined-meaning"), 
				$dmModel->getRecord()
			)
		);
		
		$wgOut->addHTML(DefaultEditor::getExpansionCss());
		$wgOut->addHTML("<script language='javascript'><!--\nexpandEditors();\n--></script>");
	}

	/**
		@return Basic CSV data dump
	*/
	public function raw() {
		global 
			$wgTitle;
	
		$definedMeaningId = $this->getDefinedMeaningIdFromTitle($wgTitle->getText());
		$dmModel=new DefinedMeaningModel(
			$definedMeaningId, 
			$this->filterLanguageId,
			$this->possiblySynonymousRelationTypeId, 
			$this->viewQueryTransactionInformation
		);
		$record=$dmModel->getRecord();
		$record->finish("DefinedMeaning");
		return $record;
	}

	protected function save($referenceTransaction) {
		global
			$wgTitle;

		parent::save($referenceTransaction);
		$definedMeaningId = $this->getDefinedMeaningIdFromTitle($wgTitle->getText());
		
		$dmModel=new DefinedMeaningModel(
			$definedMeaningId, 
			$this->filterLanguageId,
			$this->possiblySynonymousRelationTypeId, 
			$referenceTransaction
		);

		$definedMeaningId = $this->getDefinedMeaningIdFromTitle($wgTitle->getText());
		getDefinedMeaningEditor($this->filterLanguageId, $this->possiblySynonymousRelationTypeId, false, false)->save(
			$this->getIdStack($definedMeaningId), 
			$dmModel->getRecord()
		);
	
	}
	
	protected function getIdStack($definedMeaningId) {
		global
			$definedMeaningIdAttribute;
			
		$definedMeaningIdStructure = new Structure($definedMeaningIdAttribute);
		$definedMeaningIdRecord = new ArrayRecord($definedMeaningIdStructure, $definedMeaningIdStructure);
		$definedMeaningIdRecord->setAttributeValue($definedMeaningIdAttribute, $definedMeaningId);	
		
		$idStack = new IdStack("defined-meaning");
		$idStack->pushKey($definedMeaningIdRecord);
		
		return $idStack;
	}
	
	/** @deprecated, use DefinedMeaningData.setTitle instead */
	protected function getDefinedMeaningIdFromTitle($title) {
		// get id from title: DefinedMeaning:expression (id)
		$bracketPosition = strrpos($title, "(");
		$definedMeaningId = substr($title, $bracketPosition + 1, strlen($title) - $bracketPosition - 2);
		return $definedMeaningId;
	}	
	
	public function getTitle() {
		global
			$wgTitle, $wgDefinedMeaningPageTitlePrefix;
	
		if ($wgDefinedMeaningPageTitlePrefix != "")
			$prefix = $wgDefinedMeaningPageTitlePrefix . ": ";
		else
			$prefix	= "";
					
		return $prefix . definedMeaningExpression($this->getDefinedMeaningIdFromTitle($wgTitle->getText()));
	}

	public function getDefinedMeaningId() {
		global 
			$wgTitle;
		return $this->getDefinedMeaningIdFromTitle($wgTitle->getText());
	}
	/** cut and paste getDataSetPanel with mods for Collection instead
	 * Potential refactor candidate!
	*/
	protected function getConceptPanel() {
		global $wgTitle, $wgUser;
		$active=True; # wrong place, but hey
		$dm=$this->getDefinedMeaningId();
		$dc=wdGetDataSetContext();
		$ow_conceptpanel="Concept Panel";

		$html="<div class=\"dataset-panel\">";;
		$html.="<table border=\"0\"><tr><th class=\"dataset-panel-heading\">$ow_conceptpanel</th></tr>";
		$sk=$wgUser->getSkin();
		$meanings=getDefinedMeaningDataAssociatedByConcept($dm,$dc);
		foreach ($meanings as $dm) {
			$dataset=$dm->getDataset();
			$active=($dataset->getPrefix()==$dc->getPrefix());
			$name=$dataset->fetchName();
			#$name="woo";
			$prefix=$dataset->getPrefix();

			$class= $active ? 'dataset-panel-active' : 'dataset-panel-inactive';
			$slot = $active ? "$name" : $sk->makeLinkObj($dm->getTitle(),$name,"dataset=$prefix");
			$html.="<tr><td class=\"$class\">$slot</td></tr>";
		}
		$cmtitle=Title::newFromText("Special:ConceptMapping");
		$titleText=$wgTitle->getPrefixedURL();
		$cmlink=$sk->makeLinkObj($cmtitle,"<small>".wfMsg("add_concept_link")."</small>","set_$dc=$titleText&suppressWarnings=true");
		$html.="<tr><td>$cmlink</td></tr>\n";
		$html.="</table>\n";
		$html.="</div>\n";
		#$html="bla\n";
		return $html;
	}
}

