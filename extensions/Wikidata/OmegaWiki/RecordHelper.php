<?php

class RecordHelperFactory {

	public static function getRecordHelper($record) {
		$type=$record->getStructure()->getStructureType();
		if (empty($type))
			return null;
		switch($type) {
			case "definition":
				return new DefinitionHelper($record);
			case "translated-text":
				return new TranslatedTextHelper($record);
			case "object-attributes":
				return new ObjectAttributesHelper($record);
			case "syntrans-id":
				return new SynonymsTranslationsHelper($record);
				break;
			case "expression":
				return new ExpressionHelper($record);
				break;
			case "relations":
				return new RelationsHelper($record);
				break;
			case "relation-type":
				return new RelationTypeHelper($record);
				break;
			case "other-defined-meaning":
				return new OtherDefinedMeaningHelper($record);
				break;
			case "reciprocal-relations":
				return new ReciprocalRelationsHelper($record);
				break;
			case "collection-membership":
				return new CollectionMembershipHelper($record);
				break;
			case "collection-meaning":
				return new CollectionMeaningHelper($record);
				break;
			case "goto-source":
				return new GotoSourceHelper($record);
				break;
			case "defined-meaning-attributes":
				return new DefinedMeaningAttributesHelper($record);
				break;
			case "defined-meaning-id":
				// Relax
				break;
			case "defined-meaning":
				// ...
				break;
			case "language":
				break;
			default :
				# Need to do more checks here
#				throw new Exception("Record with unknown type: '$type'");			
				break;
		}

	}
}

abstract class Helper {
	protected $record;
	protected $saved;
	public function __construct($record) {
		$this->record=$record;	
	}
	
	public function isSaved() {
		return $this->saved;
	}

	public function setSaved($saved) {
		$this->saved=$saved;
	}
	
	public function getRecord() {
		return $record;
	}

	# should probably be abstract. Making non-abstract
	# to save me from tearing my hair out while worrying about
	# initial implementation
	#public abstract function getSaveSQL($dc="uw");
	public function getSaveSQL($dc="uw") {
		$dc=wdGetDataSetContext($dc);
	}

	public function save() {
		$sql=$this->getSaveSQL();
		$dbr = &wfGetDB(DB_MASTER);
		$dbr->query($sql);
	}

}

class DefinitionHelper extends Helper {
	public function __construct($record) {
		Helper::__construct($record);
	}
	
	
}

class TranslatedTextHelper extends Helper {
	public function __construct($record) {
		Helper::__construct($record);
	}
}

class ObjectAttributesHelper extends Helper {
	public function __construct($record) {
		Helper::__construct($record);
	}
}

class SynonymsTranslationsHelper extends Helper {
	public function __construct($record) {
		Helper::__construct($record);
	}
}

class ExpressionHelper extends Helper {
	public function __construct($record) {
		Helper::__construct($record);
		#echo $record;
	}
	
	// Actually going to use save for now (kind of hack)
	public function getSaveSQL($dc="uw") {
		$dc=wdGetDataSetContext($dc);
	}

	public function save() {
		
	}
}


class RelationsHelper extends Helper {
	public function __construct($record) {
		Helper::__construct($record);
	}
}

class RelationTypeHelper extends Helper {
	public function __construct($record) {
		Helper::__construct($record);
	}
}	

class OtherDefinedMeaningHelper extends Helper {
	public function __construct($record) {
		Helper::__construct($record);
	}
}

class ReciprocalRelationsHelper extends Helper {
	public function __construct($record) {
		Helper::__construct($record);
	}
}

class CollectionMembershipHelper extends Helper {
	public function __construct($record) {
		Helper::__construct($record);
	}
}

class CollectionMeaningHelper extends Helper {
	public function __construct($record) {
		Helper::__construct($record);
	}
}

class GotoSourceHelper extends Helper {
	public function __construct($record) {
		Helper::__construct($record);
	}
}

class DefinedMeaningAttributesHelper extends Helper {
	public function __construct($record) {
		Helper::__construct($record);
	}
}

