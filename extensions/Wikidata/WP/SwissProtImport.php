<?php

require_once('XMLImport.php');
//require_once('ProgressBar.php');
//require_once('..\WiktionaryZ\Expression.php');

/*
 * Import Swiss-Prot from the XML file. Be sure to have started a transaction first!
 */
function importSwissProt($xmlFileName, $umlsCollectionId = 0, $goCollectionId = 0, $hugoCollectionId = 0, $EC2GoMapping = array(), $keyword2GoMapping = array()) {
	// Create mappings from EC numbers and SwissProt keywords to GO term meaning id's:	
	$EC2GoMeaningId = array();
	$keyword2GoMeaningId = array();

	if ($goCollectionId != 0) {
		$goCollection = getCollectionContents($goCollectionId);
	
//		foreach ($EC2GoMapping as $EC => $GO) {
//			if (array_key_exists($GO, $goCollection)) {
//				$goMeaningId = $goCollection[$GO];
//				$EC2GoMeaningId[$EC] = $goMeaningId;
//			}
//		}
//	
//		foreach ($keyword2GoMapping as $keyword => $GO) {
//			if (array_key_exists($GO, $goCollection)) {
//				$goMeaningId = $goCollection[$GO];
//				$keyword2GoMeaningId[$keyword] = $goMeaningId;
//			}
//		}
	}
	
	if($hugoCollectionId != 0) {
		$hugoCollection = getCollectionContents($hugoCollectionId);
	}

	// SwissProt import:
	$numberOfBytes = filesize($xmlFileName);
	initializeProgressBar($numberOfBytes, 5000000);
	$fileHandle = fopen($xmlFileName, "r");
	importEntriesFromXMLFile($fileHandle, $umlsCollectionId, $goCollection, $hugoCollection, $EC2GoMeaningId, $keyword2GoMeaningId);

	fclose($fileHandle);
}

function importEntriesFromXMLFile($fileHandle, $umlsCollectionId, $goCollection, $hugoCollection, $EC2GoMeaningIdMapping, $keyword2GoMeaningIdMapping) {
	$languageId = 85;
	$collectionId = bootstrapCollection("Swiss-Prot", $languageId, "");
	$classCollectionId = bootstrapCollection("Swiss-Prot classes", $languageId, "CLAS");
	$relationTypeCollectionId = bootstrapCollection("Swiss-Prot relation types", $languageId, "RELT");
	$textAttibuteCollectionId = bootstrapCollection("Swiss-Prot text attributes", $languageId, "TATT");
	$ECCollectionId = bootstrapCollection("Enzyme Commission numbers", $languageId, "");
	
	$xmlParser = new SwissProtXMLParser();
	$xmlParser->languageId = $languageId;
	$xmlParser->collectionId = $collectionId;
	$xmlParser->classCollectionId = $classCollectionId;
	$xmlParser->relationTypeCollectionId = $relationTypeCollectionId;
	$xmlParser->textAttibuteCollectionId = $textAttibuteCollectionId;
	$xmlParser->ECCollectionId = $ECCollectionId;
	$xmlParser->EC2GoMeaningIdMapping = $EC2GoMeaningIdMapping;
	$xmlParser->keyword2GoMeaningIdMapping = $keyword2GoMeaningIdMapping;
	$xmlParser->goCollection = $goCollection;
	$xmlParser->hugoCollection = $hugoCollection;
	
	// Find some UMLS concepts for cross references from SwissProt:
	if ($umlsCollectionId != 0) {
		$xmlParser->proteinConceptId = getCollectionMemberId($umlsCollectionId, "C0033684");
		$xmlParser->geneConceptId = getCollectionMemberId($umlsCollectionId, "C0017337");
		$xmlParser->organismConceptId = getCollectionMemberId($umlsCollectionId, "C0029235");
		$xmlParser->proteinFragmentConceptId = getCollectionMemberId($umlsCollectionId, "C1335533");
	}
	
	if ($goCollection) {
		$xmlParser->molecularFunctionConceptId = $goCollection["GO:0003674"];
		$xmlParser->biologicalProcessConceptId = $goCollection["GO:0008150"];
		$xmlParser->cellularComponentConceptId = $goCollection["GO:0005575"];
	}
	
	$xmlParser->initialize();
	
	parseXML($fileHandle, $xmlParser);
}

class SwissProtXMLParser extends BaseXMLParser {
	public $languageId;
	public $collectionId;
	public $classCollectionId;
	public $relationTypeCollectionId;
	public $textAttibuteCollectionId;
	public $ECCollectionId;
	public $EC2GoMeaningIdMapping;
	public $keyword2GoMeaningIdMapping;
	public $goCollection;
	public $hugoCollection;
	public $numberOfEntries = 0;
	
	public $proteins = array();
	public $species = array();
	public $genes = array();
	public $organismSpecificGenes = array();
	public $attributes = array();
	public $ECNumbers = array();
	
	public $funcionalDomains = array();
	public $proteinComponents = array();
		
	public $proteinConceptId = 0;
	public $proteinFragmentConceptId = 0;
	public $organismSpecificProteinConceptId = 0;
	public $organismSpecificGeneConceptId = 0;
	public $geneConceptId = 0;
	public $organismConceptId = 0;
	public $functionalDomainConceptId = 0;
	public $proteinComponentConceptId = 0;
	public $biologicalProcessConceptId = 0;
	public $molecularFunctionConceptId = 0;
	public $cellularComponentConceptId = 0;
//	public $keywordConceptId = 0;
	public $includesConceptId = 0;
	public $containsConceptId = 0;
	public $textAttributeConceptId = 0;
	public $enzymeCommissionNumberConceptId = 0;
	public $activityConceptId = 0;
	
	protected function bootstrapDefinedMeaning($spelling, $definition) {
		$expression = $this->getOrCreateExpression($spelling);
		$definedMeaningId = createNewDefinedMeaning($expression->id, $this->languageId, $definition);

		return $definedMeaningId;
	}
	
	protected function bootstrapConceptIds() {
		if ($this->proteinConceptId == 0)
			$this->proteinConceptId = $this->bootstrapDefinedMeaning("protein", "protein");

		if ($this->proteinFragmentConceptId == 0)
			$this->proteinFragmentConceptId = $this->bootstrapDefinedMeaning("protein fragment", "protein fragment");
		
		if ($this->organismSpecificProteinConceptId == 0)
			$this->organismSpecificProteinConceptId = $this->bootstrapDefinedMeaning("organism specific protein", "organism specific protein");

		if ($this->organismSpecificGeneConceptId == 0)
			$this->organismSpecificGeneConceptId = $this->bootstrapDefinedMeaning("organism specific gene", "organism specific gene");
		
		if ($this->geneConceptId == 0)
			$this->geneConceptId = $this->bootstrapDefinedMeaning("gene", "gene");
		
		if ($this->organismConceptId == 0)
			$this->organismConceptId = $this->bootstrapDefinedMeaning("organism", "organism");

		if ($this->functionalDomainConceptId == 0)
			$this->functionalDomainConceptId = $this->bootstrapDefinedMeaning("functional domain", "functional domain");
		
		if ($this->proteinComponentConceptId == 0)
			$this->proteinComponentConceptId = $this->bootstrapDefinedMeaning("protein component", "protein component");
			
		if ($this->biologicalProcessConceptId == 0)
			$this->biologicalProcessConceptId = $this->bootstrapDefinedMeaning("biological process", "biological process");		

		if ($this->molecularFunctionConceptId == 0)
			$this->molecularFunctionConceptId = $this->bootstrapDefinedMeaning("molecular function", "molecular function");		

		if ($this->cellularComponentConceptId == 0)
			$this->cellularComponentConceptId = $this->bootstrapDefinedMeaning("cellular component", "cellular component");		
		
//		if ($this->keywordConceptId == 0)
//			$this->keywordConceptId = $this->bootstrapDefinedMeaning("keyword", "keyword");
		
		if ($this->includesConceptId == 0)
			$this->includesConceptId = $this->bootstrapDefinedMeaning("includes", "includes");
		
		if ($this->containsConceptId == 0)
			$this->containsConceptId = $this->bootstrapDefinedMeaning("contains", "contains");
		
		if ($this->enzymeCommissionNumberConceptId == 0)
			$this->enzymeCommissionNumberConceptId = $this->bootstrapDefinedMeaning("enzyme commission number", "enzyme commission number");

		if ($this->textAttributeConceptId == 0)
			$this->textAttributeConceptId = $this->bootstrapDefinedMeaning("text attribute", "text attribute");

		if ($this->activityConceptId == 0)
			$this->activityConceptId = $this->bootstrapDefinedMeaning("activity", "activity");
	}
	
	public function initialize() {
		$this->bootstrapConceptIds();
		
		// Add concepts to classes
		addDefinedMeaningToCollectionIfNotPresent($this->proteinConceptId, $this->classCollectionId, "protein");
		addDefinedMeaningToCollectionIfNotPresent($this->proteinFragmentConceptId, $this->classCollectionId, "protein fragment");
		addDefinedMeaningToCollectionIfNotPresent($this->geneConceptId, $this->classCollectionId, "gene");
		addDefinedMeaningToCollectionIfNotPresent($this->organismConceptId, $this->classCollectionId, "organism");
		addDefinedMeaningToCollectionIfNotPresent($this->functionalDomainConceptId, $this->classCollectionId, "functional domain");
		addDefinedMeaningToCollectionIfNotPresent($this->proteinComponentConceptId, $this->classCollectionId, "protein component");
		addDefinedMeaningToCollectionIfNotPresent($this->organismSpecificProteinConceptId, $this->classCollectionId, "organism specific protein");
		addDefinedMeaningToCollectionIfNotPresent($this->organismSpecificGeneConceptId, $this->classCollectionId, "organism specific gene");		
		addDefinedMeaningToCollectionIfNotPresent($this->textAttributeConceptId, $this->classCollectionId, "text attribute");
		addDefinedMeaningToCollectionIfNotPresent($this->enzymeCommissionNumberConceptId, $this->classCollectionId, "enzyme commission number");
		
		// Add concepts to relation types
		addDefinedMeaningToCollectionIfNotPresent($this->proteinConceptId, $this->relationTypeCollectionId, "protein");
		addDefinedMeaningToCollectionIfNotPresent($this->geneConceptId, $this->relationTypeCollectionId, "gene");
		addDefinedMeaningToCollectionIfNotPresent($this->organismSpecificGeneConceptId, $this->relationTypeCollectionId, "organism specific gene");		
		addDefinedMeaningToCollectionIfNotPresent($this->organismConceptId, $this->relationTypeCollectionId, "organism");
		addDefinedMeaningToCollectionIfNotPresent($this->activityConceptId, $this->relationTypeCollectionId, "activity");
		addDefinedMeaningToCollectionIfNotPresent($this->biologicalProcessConceptId, $this->relationTypeCollectionId, "biological process");
		addDefinedMeaningToCollectionIfNotPresent($this->molecularFunctionConceptId, $this->relationTypeCollectionId, "molecular function");
		addDefinedMeaningToCollectionIfNotPresent($this->cellularComponentConceptId, $this->relationTypeCollectionId, "cellular component");		
//		addDefinedMeaningToCollectionIfNotPresent($this->keywordConceptId, $this->relationTypeCollectionId, "keyword");
		addDefinedMeaningToCollectionIfNotPresent($this->includesConceptId, $this->relationTypeCollectionId, "includes");
		addDefinedMeaningToCollectionIfNotPresent($this->containsConceptId, $this->relationTypeCollectionId, "contains");
	}
	
	public function startElement($parser, $name, $attributes) {
		global
			$numberOfBytes;

		if (count($this->stack) == 0){
			$handler = new UniProtXMLElementHandler();
			$handler->name = $name;
			$handler->importer = $this;
			$handler->setAttributes($attributes);
			$this->stack[] = $handler;						
		}
		else {
			if (count($this->stack) == 1) {
				$currentByteIndex = xml_get_current_byte_index($parser);
				setProgressBarPosition($currentByteIndex);
			}
			
			BaseXMLParser::startElement($parser, $name, $attributes);
		}
	}
	
	public function import($entry){
		$proteinMeaningId = $this->addProtein($entry->protein);

		$organismSpeciesMeaningId = $this->addOrganism($entry->organism, $entry->organismTranslations);

		if ($entry->gene != "") {
			$geneMeaningId = $this->addGene($entry->gene);
			$organismSpecificGene = $this->addOrgansimSpecificGene($organismSpeciesMeaningId, $geneMeaningId, $entry->organism, $entry->gene, $entry->geneSynonyms, $entry->HGNCReference);			
		}
		else 
			$organismSpecificGene = -1;
		
		$entryMeaningId = $this->addEntry($entry, $proteinMeaningId, $organismSpecificGene, $organismSpeciesMeaningId);

		$this->numberOfEntries += 1;
	}
	
	public function addProtein($protein){
		if (array_key_exists($protein->name, $this->proteins)) {
			$definedMeaningId = $this->proteins[$protein->name];
		}
		else {
			$definedMeaningId = $this->addExpressionAsDefinedMeaning($protein->name, $protein->name, $protein->name, $this->collectionId);
			$this->proteins[$protein->name] = $definedMeaningId;
		}
		
		if($protein->fragment) 
			addClassMembership($definedMeaningId, $this->proteinFragmentConceptId);
		else
			addClassMembership($definedMeaningId, $this->proteinConceptId);			
		
		return $definedMeaningId;
	}
	
	public function addFunctionalDomain($domain) {
		if (array_key_exists($domain->name, $this->funcionalDomains)) {
			$definedMeaningId = $this->funcionalDomains[$domain->name];
		}
		else {
			$definedMeaningId = $this->addExpressionAsDefinedMeaning($domain->name, "Functional domain " . $domain->name, $domain->name, $this->collectionId);
			$this->funcionalDomains[$domain->name] = $definedMeaningId;
		}
		
		addClassMembership($definedMeaningId, $this->functionalDomainConceptId);

		return $definedMeaningId;
	}
	
	public function addContainedProtein($component) {
		if (array_key_exists($component->name, $this->proteinComponents)) {
			$definedMeaningId = $this->proteinComponents[$component->name];
		}
		else {
			$definedMeaningId = $this->addExpressionAsDefinedMeaning($component->name, "Protein component " . $component->name, $component->name, $this->collectionId);
			$this->proteinComponents[$component->name] = $definedMeaningId;
		}
		
		addClassMembership($definedMeaningId, $this->proteinComponentConceptId);

		return $definedMeaningId;
	}
	
	public function addGene($name) {
		if (array_key_exists($name, $this->genes)) {
			$definedMeaningId = $this->genes[$name];
		}
		else {
			$definedMeaningId = $this->addExpressionAsDefinedMeaning($name, $name, $name, $this->collectionId);
			$this->genes[$name] = $definedMeaningId;
		}
		
		addClassMembership($definedMeaningId, $this->geneConceptId);
		
		return $definedMeaningId;		
	}
	
	public function addOrganism($name, $translations) {
		if (array_key_exists($name, $this->species)) {
			$definedMeaningId = $this->species[$name];
		}
		else {
			$definedMeaningId = $this->addExpressionAsDefinedMeaning($name, $name, $name, $this->collectionId);
			$this->species[$name] = $definedMeaningId;
		}
		
		addClassMembership($definedMeaningId, $this->organismConceptId);
		
		foreach ($translations as $key => $translation) {
			addSynonymOrTranslation($translation, $this->languageId, $definedMeaningId, true);
		}
		
		return $definedMeaningId;		
	}
	
	public function addOrgansimSpecificGene($organismSpeciesMeaningId, $geneMeaningId, $organismName, $geneName, $synonyms, $hgncReference) {
		$key = $geneMeaningId . "-" . $organismSpeciesMeaningId;
		$description = $geneName . " in " . $organismName;
		if (array_key_exists($key, $this->organismSpecificGenes)) {
			$definedMeaningId = $this->organismSpecificGenes[$key];
		}
		else {
			if(!($this->hugoCollection && ($hgncReference != 0) && ($definedMeaningId = $this->hugoCollection[$hgncReference]))){
				$definedMeaningId = $this->addExpressionAsDefinedMeaning($description, $description, $geneName, $this->collectionId);
			}
			addSynonymOrTranslation($geneName, $this->languageId, $definedMeaningId, true);
			$this->organismSpecificGenes[$key] = $definedMeaningId;			
		}			
		
		addClassMembership($definedMeaningId, $this->organismSpecificGeneConceptId);
		
		//add relation between specific gene and organism 
		addRelation($definedMeaningId, $this->organismConceptId, $organismSpeciesMeaningId);
		
		//add relation between specific gene and gene
		addRelation($definedMeaningId, $this->geneConceptId, $geneMeaningId);
		
		foreach ($synonyms as $key => $synonym) {
			addSynonymOrTranslation($synonym, $this->languageId, $definedMeaningId, true);
		}
		

		
		return $definedMeaningId;	
	}
	
	public function addEntry($entry, $proteinMeaningId, $organismSpecificGene, $organismSpeciesMeaningId) {
		// change name to make sure it works in wiki-urls:
		$swissProtExpression = str_replace('_', '-', $entry->name);
		
		$entryDefinition = $entry->protein->name . ' in ' . $entry->organism;
		
		// add the expression as defined meaning:
		$expression = $this->getOrCreateExpression($entryDefinition);
		$definedMeaningId = createNewDefinedMeaning($expression->id, $this->languageId, $entryDefinition);
		addDefinedMeaningToCollection($definedMeaningId, $this->collectionId, $entry->accession);
		
		// Add entry synonyms: protein name, Swiss-Prot entry name and species specific protein synonyms		
		addSynonymOrTranslation($entry->protein->name, $this->languageId, $definedMeaningId, true);
		addSynonymOrTranslation($swissProtExpression, $this->languageId, $definedMeaningId, true);
		
		foreach ($entry->protein->synonyms as $key => $synonym) 
			addSynonymOrTranslation($synonym, $this->languageId, $definedMeaningId, true);

		// set the class of the entry:
		addClassMembership($definedMeaningId, $this->organismSpecificProteinConceptId);

		// set the protein of the swiss prot entry and relate the protein to the entry:		
		addRelation($definedMeaningId, $this->proteinConceptId, $proteinMeaningId);

		// set the gene of the swiss prot entry and relate the gene to the entry:
		if($organismSpecificGene >= 0) {
			//add realtion between entry and gene			
			addRelation($definedMeaningId, $this->organismSpecificGeneConceptId, $organismSpecificGene);
		}
		
		// set the species of the swiss prot entry and relate the species to the entry:		
		addRelation($definedMeaningId, $this->organismConceptId, $organismSpeciesMeaningId);
		
		// add the comment fields as text attributes:
		foreach ($entry->comments as $key => $comment) {
			$attributeMeaningId = $this->getOrCreateAttributeMeaningId($comment->type);
			$textValue = $comment->text;
			
			if ($comment->status != "") 
				$textValue .= " (" . $comment->status . ")";
				
			addDefinedMeaningTextAttributeValue($definedMeaningId, $attributeMeaningId, $this->languageId, $textValue);
		}		
		
		// add EC number:
		if($entry->EC != ""){
			$ECNumberMeaningId = $this->getOrCreateECNumberMeaningId($entry->EC);
			addRelation($definedMeaningId, $this->activityConceptId, $ECNumberMeaningId);
		}
		
		// add keywords:
//		foreach ($entry->keywords as $key => $keyword) {
//			if (array_key_exists($keyword, $this->keyword2GoMeaningIdMapping)) {
//				$goMeaningId = $this->keyword2GoMeaningIdMapping[$keyword];
//				addRelation($definedMeaningId, $this->keywordConceptId, $goMeaningId);
//			}
//		}
		
		if($this->goCollection) {
			foreach ($entry->GOReference as $key => $goReference) {
				$relationConcept = 0;
				switch($goReference->type) {
				case("biological process"):
					$relationConcept = $this->biologicalProcessConceptId;
					break;
				case("molecular function"):
					$relationConcept = $this->molecularFunctionConceptId;
					break;
				case("cellular component"):
					$relationConcept = $this->cellularComponentConceptId;
					break;
				}
				
				if($relationConcept && ($goConcept = $this->goCollection[$goReference->goCode])) {
					addRelation($definedMeaningId, $relationConcept, $goConcept);					
				}
			}			
		}
		
		
 		// Add 'included' functional domains:
		foreach ($entry->protein->domains as $key => $domain) {
			$domainMeaningId = $this->addFunctionalDomain($domain);

			foreach ($domain->synonyms as $domainKey => $synonym) 
				addSynonymOrTranslation($synonym, $this->languageId, $domainMeaningId, true);
			
			addRelation($definedMeaningId, $this->includesConceptId, $domainMeaningId);
		}
		
 		// Add 'contained' proteins:
		foreach ($entry->protein->components as $key => $component) {
			$componentMeaningId = $this->addContainedProtein($component);
			
			foreach ($component->synonyms as $componentKey => $synonym) 
				addSynonymOrTranslation($synonym, $this->languageId, $componentMeaningId, true);
			
			addRelation($definedMeaningId, $this->containsConceptId, $componentMeaningId);
		}
		
		return $definedMeaningId;		
	}
	
	public function getOrCreateAttributeMeaningId($attribute) {
		if (array_key_exists($attribute, $this->attributes)) {
			$definedMeaningId = $this->attributes[$attribute];
		}
		else {
			$definedMeaningId = $this->addExpressionAsDefinedMeaning($attribute, $attribute, $attribute, $this->textAttibuteCollectionId);
			addClassMembership($definedMeaningId, $this->textAttributeConceptId);
			$this->attributes[$attribute] = $definedMeaningId;
		}
		return $definedMeaningId;		
	}
	
	public function getOrCreateECNumberMeaningId($EC) {
		if (array_key_exists($EC, $this->ECNumbers)) {
			$definedMeaningId = $this->ECNumbers[$EC];
		}
		elseif (array_key_exists($EC, $this->EC2GoMeaningIdMapping)) {
			$definedMeaningId = $this->EC2GoMeaningIdMapping[$EC];
			$this->ECNumbers[$EC] = $definedMeaningId;
			$expression = $this->getOrCreateExpression($EC);
			$expression->assureIsBoundToDefinedMeaning($definedMeaningId, true);
		}
		else {
			$definedMeaningId = $this->addExpressionAsDefinedMeaning($EC, $EC, $EC, $this->ECCollectionId);
			addClassMembership($definedMeaningId, $this->enzymeCommissionNumberConceptId);
			$this->ECNumbers[$EC] = $definedMeaningId;
		}
		return $definedMeaningId;		
	}
	
	public function getOrCreateExpression($spelling) {
		$expression = findExpression($spelling, $this->languageId);
		if (!$expression) {
			$expression = createExpression($spelling, $this->languageId);
		}
		return $expression;		
	}
	
	public function addExpressionAsDefinedMeaning($spelling, $definition, $internalIdentifier, $collectionId) {
		$expression = $this->getOrCreateExpression($spelling);
		$definedMeaningId = createNewDefinedMeaning($expression->id, $this->languageId, $definition);
		addDefinedMeaningToCollection($definedMeaningId, $collectionId, $internalIdentifier);
		return $definedMeaningId;
	}
} 

class UniProtXMLElementHandler extends DefaultXMLElementHandler {
	public $importer;
	
	public function getHandlerForNewElement($name) {
		if($name=="ENTRY") {
			$result = new EntryXMLElementHandler();
		}
		else {
			$result = DefaultXMLElementHandler::getHandlerForNewElement($name);
		}
		$result->name = $name;
		return $result;
	}
	
	public function notify($childHandler) {
		if (is_a($childHandler, EntryXMLElementHandler)) {
			$this->importer->import($childHandler->entry);
		}
	}
}

class EntryXMLElementHandler extends DefaultXMLElementHandler {
	public $entry;

	public function __construct() {
		$this->entry = new SwissProtEntry();	
	}
	
	public function getHandlerForNewElement($name) {
		switch($name) {
			case "PROTEIN": 
				$result = new ProteinXMLElementHandler();
				break;
			case "GENE":
				$result = new GeneXMLElementHandler();
				break;
			case "ORGANISM":
				$result = new OrganismXMLElementHandler();
				break;
			case "COMMENT":
				$result = new CommentXMLElementHandler();
				break;
			case "DBREFERENCE":
				$result = new dbReferenceXMLElement();
				break;
			default:
				$result = DefaultXMLElementHandler::getHandlerForNewElement($name);
				break;
		}
		
		$result->name = $name;
		
		return $result;
	}
	
	public function notify($childHandler) {
		if (is_a($childHandler, ProteinXMLElementHandler)) {
			$this->entry->protein = $childHandler->protein;		
		}
		elseif (is_a($childHandler, GeneXMLElementHandler)) {
//			$this->entry->gene = $childHandler->gene;
//			$this->entry->geneSynonyms = $childHandler->synonyms;
			list($this->entry->gene, $this->entry->geneSynonyms) = $childHandler->getResult();
		}
		elseif (is_a($childHandler, OrganismXMLElementHandler)) {
			$this->entry->organism = $childHandler->organism;
			$this->entry->organismTranslations = $childHandler->translations;		
		}
		elseif (is_a($childHandler, CommentXMLElementHandler)) {
			if ($childHandler->comment != "")
				$this->entry->comments[] = $childHandler->comment;
		}
		elseif (is_a($childHandler, dbReferenceXMLElement)) {
			if ($childHandler->type == "EC") {
				$this->entry->EC = $childHandler->id;
			}
			if ($childHandler->type == "GO") {
				$this->entry->GOReference[] = new GOReference($childHandler->id, $childHandler->property["term"]);
			}
			if ($childHandler->type == "HGNC") {
				$position = strpos($childHandler->id, ":");
				$this->entry->HGNCReference = substr($childHandler->id, $position + 1);
			}			
		}
		elseif($childHandler->name == "ACCESSION") {
			if ($this->entry->accession == "") {
				$this->entry->accession = $childHandler->data;
			} 
			else {
				$this->entry->secundaryAccessions[] = $childHandler->data;
			}
		}
		elseif($childHandler->name == "NAME") {
			$this->entry->name = $childHandler->data;
		}															 
		elseif($childHandler->name == "KEYWORD") {
			$this->entry->keywords[] = $childHandler->attributes["ID"];
		}
	}	
}

class ProteinXMLElementHandler extends DefaultXMLElementHandler {
	public $protein;
	
	public function __construct() {
		$this->protein = new Protein();
	}
	
	public function setAttributes($attributes) {
		DefaultXMLElementHandler::setAttributes($attributes);
		$this->protein->fragment = (array_key_exists("TYPE", $this->attributes) && ($this->attributes["TYPE"] == "fragment" || $this->attributes["TYPE"] == "fragments"));
	} 
	
	public function getHandlerForNewElement($name) {
		if ($name == "NAME") 
			$result = new NameXMLElementHandler();
		else 
			$result = new ProteinXMLElementHandler();
		
		$result->name = $name;
		return $result;
	}
	
	public function notify($childHandler) {
		if (is_a($childHandler, NameXMLElementHandler)) {
			$proteinName = $childHandler->data;
			if ($this->protein->name == "") 
				$this->protein->name = $proteinName;
			else 
				$this->protein->synonyms[]=$proteinName;
		}
		elseif ($childHandler->name == "DOMAIN")
			$this->protein->domains[] = $childHandler->protein;					
		elseif ($childHandler->name == "COMPONENT")
			$this->protein->components[] = $childHandler->protein;					
	}
}

class NameXMLElementHandler extends DefaultXMLElementHandler {
	public $name;
	
	public function close() {
		$this->name = $this->data;
	}
}

class GeneNameXMLElementHandler extends DefaultXMLElementHandler {
	public $name = "";
	public $type = "";

	public function setAttributes($attributes) {
		$this->type = $attributes["TYPE"];	
	}

	public function processData($data) {
		$this->name .= $data;
	}
}

class GeneXMLElementHandler extends DefaultXMLElementHandler {
	public $primaryNames = array();
	public $synonyms = array();
	public $orderedLoci = array();
	public $ORFs = array();
	
	public function getHandlerForNewElement($name) {
		if ($name == "NAME")
			return new GeneNameXMLElementHandler();
		else
			return parent::getHandlerForNewElement($name);
	}
	
	public function notify($childHandler) {
		// We expect a GeneNameXMLElementHandler
		
		switch ($childHandler->type) { 
			case "primary": 
				$this->primaryNames[] = $childHandler->name;
				break;
			case "synonym":
				$this->synonyms[] = $childHandler->name;
				break;
			case "ordered locus":
				$this->orderedLoci[] = $childHandler->name;
				break;
			case "ORF":
				$this->ORFs[] = $childHandler->name;
				break;
		}	
	}
	
	public function getResult() {
		// Primary name is not always present for a gene, fall back to a synonym, ORF or ordered locus
		$name = "";
		$synonyms = array();
		
		foreach($this->primaryNames as $primaryName) 
			if ($name == "")
				$name = $primaryName;
			else
				$synonyms[] = $primaryName;
		
		foreach($this->synonyms as $synonym) 
			if ($name == "")
				$name = $synonym;
			else
				$synonyms[] = $synonym;
		
		foreach($this->ORFs as $ORF) 
			if ($name == "")
				$name = $ORF;
			else
				$synonyms[] = $ORF;
				
		foreach($this->orderedLoci as $orderedLocus) 
			if ($name == "")
				$name = $orderedLocus;
			else
				$synonyms[] = $orderedLocus;

		return array($name, $synonyms);
	}
}

class OrganismXMLElementHandler extends DefaultXMLElementHandler {
	public $organism = "";
	public $translations = array();
	
	public function notify($childHandler) {
		if ($childHandler->name == "NAME") {
			if($this->organism == "") 
				$this->organism = $childHandler->data; 
			else 
				$this->translations[] = $childHandler->data;	
		}
	}
}

class CommentXMLElementHandler extends DefaultXMLElementHandler {
	public $comment;
	
	public function __construct() {
		$this->comment = new Comment();
	}
	
	public function setAttributes($attributes) {
		DefaultXMLElementHandler::setAttributes($attributes);
		$this->comment->type = $attributes["TYPE"];
		
		if (array_key_exists("STATUS", $attributes))
			$this->comment->status = $attributes["STATUS"];
	} 
	
	public function notify($childHandler) {
		if ($childHandler->name = "TEXT")
			$this->comment->text = $childHandler->data;
	}
}

class dbReferenceXMLElement extends DefaultXMLElementHandler {
	public $type;
	public $id;
	public $property = array();
	
	public function setAttributes($attributes) {
		DefaultXMLElementHandler::setAttributes($attributes);
		
		$this->type = $attributes["TYPE"];
		$this->id = $attributes["ID"];
	} 
	
	public function notify($childHandler) {
		$this->property[$childHandler->attributes["TYPE"]] = $childHandler->attributes["VALUE"];
	}	
}

class SwissProtEntry {
	public $name = "";
	public $accession = "";
	public $secundaryAccessions = array();
	public $protein;
	public $EC = "";
	public $gene = "";
	public $geneSynonyms = array();
	public $organism = "";
	public $organismTranslations = array();
	public $comments = array();
	public $keywords = array();
	public $GOReference = array();
	public $HGNCReference;
}

class Comment {
	public $type = "";
	public $text = "";
	public $status = "";
}

class Protein {
	public $name = "";
	public $fragment = false;
	public $synonyms = array();
	public $domains = array();
	public $components = array();
}

class GOReference {
	public $type;
	public $goCode;
	
	public function __construct($goCode, $term) {
		$this->goCode = $goCode;
		$typeAbbreviation = substr($term, 0, 1);
		switch($typeAbbreviation) {
		case("P"):
			$this->type = "biological process";
			break;
		case("F"):
			$this->type = "molecular function";
			break;
		case("C"):
			$this->type = "cellular component";
			break;
		}
	}
}

?>
