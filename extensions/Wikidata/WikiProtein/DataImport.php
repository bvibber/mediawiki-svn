<?php
	define('MEDIAWIKI', true );
	require_once("../../../LocalSettings.php");
	require_once("../WiktionaryZ/Expression.php");
	require_once("Setup.php");
	require_once('SwissProtImport.php');

 	$beginTime = time();
	$fileHandle = fopen("C:\Documents and Settings\Karsten Uil\Mijn documenten\Charta\Clients\KnewCo\WikiProtein\uniprot_sprot.dat\uniprot_sprot.dat", "r");

	$selectLanguageId = 'SELECT language_id FROM language_names WHERE language_name ="English"';
	$dbr =& wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query($selectLanguageId);
	
	if ($languageIdObject = $dbr->fetchObject($queryResult)){
		$languageId = $languageIdObject->language_id;
	}

	$collectionId = bootstrapCollection("Swiss-Prot", $languageId);
		
//	getPrefixAnalysis($fileHandle);
//	while (!feof($fileHandle)) {
	for ($i = 1; $i <= 10; $i++)  {
			$entry = new SwissProtImportEntry;
			$entry->import($fileHandle);

			$identifier = $entry->getIdentifier();
			$descriptions = $entry->getDescriptions();
			
			$expression = findOrCreateExpression($identifier, $languageId);
			$definedMeaningId = addDefinedMeaning($expression->id, $expression->revisionId);
			$expression->assureIsBoundToDefinedMeaning($definedMeaningId, true);
			
			addDefinedMeaningDefinition($definedMeaningId, $expression->revisionId, $languageId, $descriptions);
			addDefinedMeaningToCollection($definedMeaningId, $collectionId, $identifier, $expression->revisionId);
	}
	fclose($fileHandle);
	
	$endTime = time();
	echo "Time elapsed: " . ($endTime - $beginTime); 
	
	function getPrefixAnalysis($fileHandle){
		$prefixArray=array();
			
		while (!feof($fileHandle)) {
		    $buffer = fgets($fileHandle);
		    $buffer = rtrim($buffer,"\n");
		    $currentPrefix = substr($buffer, 0, 2);
				
				if ($currentPrefix != ""){
		 	    if (!array_key_exists($currentPrefix, $prefixArray)) {
		 	    	$prefixArray[$currentPrefix]=1;	
		 	    }
		 	    else {
		 	    	$prefixArray[$currentPrefix]+=1;
		 	    }
				} 
	 	 }
		
		echo "Number of prefixes: " . count($prefixArray) . "\n";
		foreach ($prefixArray as $prefix => $value) {
			echo $prefix . ": $value\n";
		}
	}
?>
