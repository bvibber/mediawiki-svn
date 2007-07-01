<?php

class RecordHelperFactory {

	public static function getRecordHelper($record) {
		$type=$record->getType();
		if (empty($type))
			return null;
		echo "HELPING $type .... ";
		switch($type) {
			case "definition":
				return new DefinitionHelper($record);
			case "translated-text":
				return new TranslatedTextHelper($record);
			case "object-attributes":
				return new ObjectAttributesHelper($record);
			case "synonyms-translations":
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
			default :
				echo "IGIVEUP ($type) I Give Up! \n";
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

	//public abstract function getSQL();
	//public abstract function save();

}

class DefinitionHelper extends Helper {
	public function __construct($record) {
		echo "DefinitionHelper\n";
		Helper::__construct($record);
	}
	
	public function save() {
		/*what to do here eh?*/
	}
	

}

class TranslatedTextHelper extends Helper {
	public function __construct($record) {
		echo "TranslatedTextHelper\n";
		Helper::__construct($record);
	}
	
	public function save() {
		/*what to do here eh?*/
	}
	

}

class ObjectAttributesHelper extends Helper {
	public function __construct($record) {
		echo "ObjectAttributesHelper\n";
		Helper::__construct($record);
	}
	
	public function save() {
		/*what to do here eh?*/
	}
	

}

class SynonymsTranslationsHelper extends Helper {
	public function __construct($record) {
		echo "SynonymsTranslationsHelper\n";
		Helper::__construct($record);
	}
	
	public function save() {
		/*what to do here eh?*/
	}
	

}

class ExpressionHelper extends Helper {
	public function __construct($record) {
		echo "ExpressionHelper\n";
		Helper::__construct($record);
		echo $record;
	}
	
	public function getSQL() {

	}

	public function save() {
		/*what to do here eh?*/
	}
	

}


class RelationsHelper extends Helper {
	public function __construct($record) {
		echo "RelationsHelper\n";
		Helper::__construct($record);
	}
	
	public function save() {
		/*what to do here eh?*/
	}
	

}

class RelationTypeHelper extends Helper {
	public function __construct($record) {
		echo "RelationTypeHelper\n";
		Helper::__construct($record);
	}
	
	public function save() {
		/*what to do here eh?*/
	}
}

class OtherDefinedMeaningHelper extends Helper {
	public function __construct($record) {
		echo "OtherDefinedMeaningHelper\n";
		Helper::__construct($record);
	}
	
	public function save() {
		/*what to do here eh?*/
	}
	

}

class ReciprocalRelationsHelper extends Helper {
	public function __construct($record) {
		echo "ReciprocalRelationsHelper\n";
		Helper::__construct($record);
	}
	
	public function save() {
		/*what to do here eh?*/
	}
	

}

class CollectionMembershipHelper extends Helper {
	public function __construct($record) {
		echo "CollectionMembershipHelper\n";
		Helper::__construct($record);
	}
	
	public function save() {
		/*what to do here eh?*/
	}
	

}

class CollectionMeaningHelper extends Helper {
	public function __construct($record) {
		echo "CollectionMeaningHelper\n";
		Helper::__construct($record);
	}
	
	public function save() {
		/*what to do here eh?*/
	}
	

}

class GotoSourceHelper extends Helper {
	public function __construct($record) {
		echo "GotoSourceHelper\n";
		Helper::__construct($record);
	}
	
	public function save() {
		/*what to do here eh?*/
	}
	

}

class DefinedMeaningAttributesHelper extends Helper {
	public function __construct($record) {
		echo "DefinedMeaningAttributesHelper\n";
		Helper::__construct($record);
	}
	
	public function save() {
		/*what to do here eh?*/
	}
	

}

