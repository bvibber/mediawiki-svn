<?php

require_once('XMLImport.php');

function importEntriesFromXMLFile($fileHandle) {
//	$selectLanguageId = 'SELECT language_id FROM language_names WHERE language_name ="English"';
//	$queryResult = $dbr->query($selectLanguageId);
//	if ($languageIdObject = $dbr->fetchObject($queryResult)){
//		$languageId = $languageIdObject->language_id;
//	}
	
	$languageId = 85;
	$collectionId = bootstrapCollection("Swiss-Prot", $languageId, "");
	$classCollectionId = bootstrapCollection("Swiss-Prot classes", $languageId, "ATTR");
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
	
	$xmlParser->addClass("Protein");
	$xmlParser->addClass("Protein fragment");
	$xmlParser->addClass("Organism");
	$xmlParser->addClass("Organism specific protein");
	$xmlParser->addClass("Gene");
	$xmlParser->addClass("Organism specific gene");
	$xmlParser->addClass("Text attribute");
	$xmlParser->addClass("Enzyme Commission number");
	
	parseXML($fileHandle, $xmlParser);
}

class SwissProtXMLParser extends BaseXMLParser {
	public $languageId;
	public $collectionId;
	public $classCollectionId;
	public $relationTypeCollectionId;
	public $textAttibuteCollectionId;
	public $ECCollectionId;
	public $numberOfEntries = 0;
	
	public $classes = array();
	public $relationTypes = array();
	public $proteins = array();
	public $species = array();
	public $genes = array();
	public $attributes = array();
	public $ECNumbers = array();
	
	public function addClass($name) {
		if (array_key_exists($name, $this->classes)) {
			$definedMeaningId = $this->classes[$name];
		}
		else {
			$definedMeaningId = $this->addExpressionAsDefinedMeaning($name, $name, $name, $this->classCollectionId);
			$this->classes[$name] = $definedMeaningId;
		}
		return $definedMeaningId;		
	}
	
	public function startElement($parser, $name, $attributes) {
		if (count($this->stack)== 0){
			$handler = new UniProtXMLElementHandler();
			$handler->name = $name;
			$handler->importer = $this;
			$handler->setAttributes($attributes);
			$this->stack[] = $handler;						
		}
		else
			BaseXMLParser::startElement($parser, $name, $attributes);
	}
	
	public function import($entry){
		$proteinMeaningId = $this->addProtein($entry->protein);

		if ($entry->gene != "")
			$geneMeaningId = $this->addGene($entry->gene, $entry->geneSynonyms);
		else 
			$geneMeaningId = -1;
		
		$organismSpeciesMeaningId = $this->addOrganism($entry->organism, $entry->organismTranslations);
		
		$entryMeaningId = $this->addEntry($entry, $proteinMeaningId, $geneMeaningId, $organismSpeciesMeaningId);

		$this->numberOfEntries += 1;
		echo "$this->numberOfEntries\n";
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
			addRelation($definedMeaningId, 0, $this->classes["Protein fragment"]);
		else
			addRelation($definedMeaningId, 0, $this->classes["Protein"]);			
		
		return $definedMeaningId;
	}
	
	public function addGene($name, $synonyms) {
		if (array_key_exists($name, $this->genes)) {
			$definedMeaningId = $this->genes[$name];
		}
		else {
			$definedMeaningId = $this->addExpressionAsDefinedMeaning($name, $name, $name, $this->collectionId);
			$this->genes[$name] = $definedMeaningId;
		}
		
		addRelation($definedMeaningId, 0, $this->classes["Gene"]);
		
		foreach ($synonyms as $key => $synonym) {
			addSynonymOrTranslation($synonym, $this->languageId, $definedMeaningId, true);
		}
		
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
		
		addRelation($definedMeaningId, 0, $this->classes["Organism"]);
		
		foreach ($translations as $key => $translation) {
			addSynonymOrTranslation($translation, $this->languageId, $definedMeaningId, true);
		}
		
		return $definedMeaningId;		
	}
	
	public function addEntry($entry, $proteinMeaningId, $geneMeaningId, $organismSpeciesMeaningId) {
//		change name to make sure it works in wiki-urls:
		$swissProtExpression = str_replace('_', '-', $entry->name);
		$entryExpression = $entry->protein->name . ' in ' . $entry->organism;
		
//		add the expression as defined meaning:
		$expression = $this->getOrCreateExpression($entryExpression);
		$definedMeaningId = createNewDefinedMeaning($expression->id, $this->languageId, $entryExpression);
		addDefinedMeaningToCollection($definedMeaningId, $this->collectionId, $entry->accession);
		
//		Add entry synonyms: Swiss-Prot entry name and species specific protein synonyms		
		addSynonymOrTranslation($swissProtExpression, $this->languageId, $definedMeaningId, true);
		
		foreach ($entry->protein->synonyms as $key => $synonym) 
			addSynonymOrTranslation($synonym, $this->languageId, $definedMeaningId, true);

//		set the class of the entry:
		addRelation($definedMeaningId, 0, $this->classes["Organism specific protein"]);

//		set the protein of the swiss prot entry and relate the protein to the entry:		
		addRelation($definedMeaningId, $this->getOrCreateRelationTypeMeaningId("protein"), $proteinMeaningId);
		addRelation($proteinMeaningId, $this->getOrCreateRelationTypeMeaningId("referenced by"), $definedMeaningId);

//		set the gene of the swiss prot entry and relate the gene to the entry:
		if($geneMeaningId >= 0) {
			addRelation($definedMeaningId, $this->getOrCreateRelationTypeMeaningId("gene"), $geneMeaningId);
			addRelation($geneMeaningId, $this->getOrCreateRelationTypeMeaningId("referenced by"), $definedMeaningId);
		}
		
//		set the species of the swiss prot entry and relate the species to the entry:		
		addRelation($definedMeaningId, $this->getOrCreateRelationTypeMeaningId("organism"), $organismSpeciesMeaningId);
		addRelation($organismSpeciesMeaningId, $this->getOrCreateRelationTypeMeaningId("referenced by"), $definedMeaningId);
		
//		add the comment fields as text attributes:
		foreach ($entry->comments as $key => $comment) {
			$attributeMeaningId = $this->getOrCreateAttributeMeaningId($comment->type);
			$textValue = $comment->text;
			
			if ($comment->status != "") 
				$textValue .= " (" . $comment->status . ")";
				
			addDefinedMeaningTextAttributeValue($definedMeaningId, $attributeMeaningId, $this->languageId, $textValue);
		}		
		
//		add EC number:
		if($entry->EC != ""){
			$ECNumberMeaningId = $this->getOrCreateECNumberMeaningId($entry->EC);
			addRelation($definedMeaningId, $this->getOrCreateRelationTypeMeaningId("EC number"), $ECNumberMeaningId);
			addRelation($ECNumberMeaningId, $this->getOrCreateRelationTypeMeaningId("referenced by"), $definedMeaningId);
		}
		
// 		Add protein includes relations
		foreach ($entry->protein->domains as $key => $domain) {
			$domainMeaningId = $this->addProtein($domain);
			addRelation($definedMeaningId, $this->getOrCreateRelationTypeMeaningId("includes"), $domainMeaningId);
			addRelation($domainMeaningId, $this->getOrCreateRelationTypeMeaningId("included in"), $definedMeaningId);
		}
		
// 		Add protein includes relations
		foreach ($entry->protein->components as $key => $component) {
			$componentMeaningId = $this->addProtein($component);
			addRelation($definedMeaningId, $this->getOrCreateRelationTypeMeaningId("contains"), $componentMeaningId);
			addRelation($componentMeaningId, $this->getOrCreateRelationTypeMeaningId("contained in"), $definedMeaningId);
		}
		
		return $definedMeaningId;		
	}
	
	public function getOrCreateAttributeMeaningId($attribute) {
		if (array_key_exists($attribute, $this->attributes)) {
			$definedMeaningId = $this->attributes[$attribute];
		}
		else {
			$definedMeaningId = $this->addExpressionAsDefinedMeaning($attribute, $attribute, $attribute, $this->textAttibuteCollectionId);
			addRelation($definedMeaningId, 0, $this->classes["Text attribute"]);
			$this->attributes[$attribute] = $definedMeaningId;
		}
		return $definedMeaningId;		
	}
	
	public function getOrCreateECNumberMeaningId($EC) {
		if (array_key_exists($EC, $this->ECNumbers)) {
			$definedMeaningId = $this->ECNumbers[$EC];
		}
		else {
			$definedMeaningId = $this->addExpressionAsDefinedMeaning($EC, $EC, $EC, $this->ECCollectionId);
			addRelation($definedMeaningId, 0, $this->classes["Enzyme Commission number"]);
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

	public function getOrCreateRelationTypeMeaningId($spelling) {
		if (array_key_exists($spelling, $this->relationTypes)){
			$relationTypeMeaningId = $this->relationTypes[$spelling];
		}
		else {
			$relationTypeMeaningId = $this->addExpressionAsDefinedMeaning($spelling, $spelling, $spelling, $this->relationTypeCollectionId);
			$this->relationTypes[$spelling] = $relationTypeMeaningId;
		}
		return $relationTypeMeaningId;
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
		elseif($childHandler->name == "DBREFERENCE" && array_key_exists("TYPE", $childHandler->attributes) && $childHandler->attributes["TYPE"] == "EC"){
			$this->entry->EC = $childHandler->attributes["ID"];
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
	
//	public function __construct($nameSynonymsString) {
//		$openingBracketPosition = strpos($nameSynonymsString, "(");
//		if ($openingBracketPosition === false) {
//			$this->name = trim($nameSynonymsString);
//		}
//		else {
//			$this->name = trim(substr($nameSynonymsString, 0, $openingBracketPosition));
//			$nameSynonymsString = substr($nameSynonymsString, $openingBracketPosition, strlen($nameSynonymsString)-$openingBracketPosition);
//			$openingBracketPosition = strpos($nameSynonymsString, "(");
//		}
//		while($openingBracketPosition !== false) {
//			$closingBracketPosition = strpos($nameSynonymsString, ")");
//			$this->synonyms[]= trim(substr($nameSynonymsString, $openingBracketPosition+1, $closingBracketPosition - $openingBracketPosition-1));
//			$nameSynonymsString = substr($nameSynonymsString, $closingBracketPosition+1, strlen($nameSynonymsString)-$closingBracketPosition);
//			$openingBracketPosition = strpos($nameSynonymsString, "(");	
//		}	
//	}
}

//class SwissProtImportEntry {
//	public $attributes;
//	
//	public function __construct() {
//		$this->attributes = array();
//	}
//	
//	public function import($fileHandle){
//		$line = fgets($fileHandle);
//		while((!feof($fileHandle)) and (getPrefix($line) != "//")){
//			$attribute = $this->getEntryAttribute($line);
//			$line = $attribute->import($line, $fileHandle);
//		} 
//	}
//	
//	public function echoEntry(){
//		foreach ($this->attributes as $prefix => $attribute) {
//			foreach ($attribute->lines as $line) {
//				echo $line;					
//			}
//		}
//	}
//	
//	private function getEntryAttribute($line){
//    $line = rtrim($line,"\n");
//		$prefix = getPrefix($line);
//    if (!array_key_exists($prefix, $this->attributes)) {
//    	$this->attributes[$prefix]=new SwissProtEntryAttribute($prefix);	
//    }
//    return $this->attributes[$prefix]; 	    
//	}
//	
//	public function getIdentifier(){
//		$idAttribute = $this->attributes["ID"];
//		if ($idAttribute) {
//			$line = $idAttribute->lines[0];
//			$line = stripPrefix($line);
//			$endIdentifierPosition = strpos($line, " ");
//			return substr($line, 0, $endIdentifierPosition);				
//		}
//	}
//	
//	public function getDescriptionAttribute(){
//		$descriptionAttibute = $this->attributes["DE"];
//		if ($descriptionAttibute) {
//			return new DescriptionSwissProtEntryAttribute($descriptionAttibute);
//		}
//	}
//}
//
//function getPrefix($line) {
//	return substr($line, 0, 2);			
//}
//
//function stripPrefix($line) {
//	$line = substr($line, 2, strlen($line)-2);		
//	return ltrim($line);
//}
//
//function joinLinesWithoutPrefixes($attribute) {
//	$result = "";
//	foreach ($attribute->lines as $line) {
//		$unPrefixedLine = stripPrefix($line);
//		$unPrefixedLine = rtrim($unPrefixedLine,"\n");
//		$result .= $unPrefixedLine;  
//	}
//	return $result;
//}
//
//class SwissProtEntryAttribute {
//	public $prefix;
//	public $lines;
//
//	public function __construct($prefix) {
//		$this->prefix = $prefix;
//		$this->lines = array();
//	}
//	
//	public function import($firstLine, $fileHandle){
//		$this->lines[]=$firstLine;
//		$line = fgets($fileHandle);
//					
//		while ( (!feof($fileHandle)) and ( (getPrefix($line) == $this->prefix) or (getPrefix($line) == "  ") )){
//			$this->lines[]=$line;								
//			$line = fgets($fileHandle);
//		}
//		return $line; 
//	}
//}
//
//class DescriptionSwissProtEntryAttribute {
//	private $attribute;
//	private $includesString;
//	private $containsString;
//	public $protein;
//	public $containsProteins;
//	public $includesProteins;
//	public $isFragment;
//	
//	public function __construct($attribute) {
//		$this->attribute = $attribute;
//		$this->parse();
//	}
//	
//	public function parse() {
//		$fullDescription = rtrim(joinLinesWithoutPrefixes($this->attribute), ".");
//		$fragmentPosition = strpos($fullDescription, "(Fragment");
//					
//		if ($this->isFragment = ($fragmentPosition!==false)) {
//			$fullDescription = rtrim(substr($fullDescription, 0, $fragmentPosition));
//		}
//				
//		$startContainsPosition = strpos($fullDescription, "[Contains:");
//		$startIncludesPosition = strpos($fullDescription, "[Includes:");
//		
//		if (($startContainsPosition!==false) or ($startIncludesPosition!==false)) {
//			$endProteinPosition = max($startContainsPosition, $startIncludesPosition);
//			$this->getContainsAndIncludesStrings($fullDescription, $startContainsPosition, $startIncludesPosition);
//		}
//		else {
//			$endProteinPosition = strlen($fullDescription);
//		}  
//		$proteinString = rtrim(substr($fullDescription, 0, $endProteinPosition));
//		$this->protein = new Protein($proteinString);
//	}
//	
//	private function getContainsAndIncludesStrings($fullDescription, $startContainsPosition, $startIncludesPosition) {
//			if ($startContainsPosition===false) {
//				$startContainsPosition = strlen($fullDescription);
//			}
//			if ($startIncludesPosition===false) {
//				$startIncludesPosition = strlen($fullDescription);
//			}
//			
//			if ($startContainsPosition > $startIncludesPosition) {
//				$endIncludesPosition = strpos($fullDescription, "]");
//				$endContainsPosition = strpos($fullDescription, "]", $endIncludesPosition+1);
//			}
//			else {
//				$endContainsPosition = strpos($fullDescription, "]");
//				$endIncludesPosition = strpos($fullDescription, "]", $endContainsPosition+1);
//			}
//			$this->includesString = trim(substr($fullDescription, $startIncludesPosition + 10, $endIncludesPosition-$startIncludesPosition-10));
//			$this->containsString = trim(substr($fullDescription, $startContainsPosition + 10, $endContainsPosition-$startContainsPosition-10));
//	}
//}

?>
