<?php

class RecordHelperFactory {

	public static function getRecordHelper($record) {
		$type=$record->getType();
		if (empty($type))
			return null;
		echo "HELPING $type .... ";
		switch($type) {
			case "definition":
				return new DefinitionHelper();
			case "translated-text":
				return new TranslatedTextHelper();
			case "object-attributes":
				return new ObjectAttributesHelper();
			case "synonyms-translations":
				return new SynonymsTranslationsHelper();
				break;
			case "expression":
				return new ExpressionHelper();
				break;
			case "relations":
				return new RelationsHelper();
				break;
			case "relation-type":
				return new RelationTypeHelper();
				break;
			case "other-defined-meaning":
				return new OtherDefinedMeaningHelper();
				break;
			case "reciprocal-relations":
				return new ReciprocalRelationsHelper();
				break;
			case "collection-membership":
				return new CollectionMembershipHelper();
				break;
			case "collection-meaning":
				return new CollectionMeaningHelper();
			case "goto-source":
				return new GotoSourceHelper();
			case "defined-meaning-attributes":
				return new DefinedMeaningAttributesHelper();
			default :
				echo "IGIVEUP ($type) I Give Up! \n";
				break;
		}

	}
}

abstract class Helper {
	public function __construct() {
	}
	
	public abstract function save();

}

class DefinitionHelper extends Helper {
	public function __construct() {
		echo "DefinitionHelper\n";
		Helper::__construct();
	}
	
	public function save() {
		/*what to do here eh?*/
	}
	

}

class TranslatedTextHelper extends Helper {
	public function __construct() {
		echo "TranslatedTextHelper\n";
		Helper::__construct();
	}
	
	public function save() {
		/*what to do here eh?*/
	}
	

}

class ObjectAttributesHelper extends Helper {
	public function __construct() {
		echo "ObjectAttributesHelper\n";
		Helper::__construct();
	}
	
	public function save() {
		/*what to do here eh?*/
	}
	

}

class SynonymsTranslationsHelper extends Helper {
	public function __construct() {
		echo "SynonymsTranslationsHelper\n";
		Helper::__construct();
	}
	
	public function save() {
		/*what to do here eh?*/
	}
	

}

class ExpressionHelper extends Helper {
	public function __construct() {
		echo "ExpressionHelper\n";
		Helper::__construct();
	}
	
	public function save() {
		/*what to do here eh?*/
	}
	

}


class RelationsHelper extends Helper {
	public function __construct() {
		echo "RelationsHelper\n";
		Helper::__construct();
	}
	
	public function save() {
		/*what to do here eh?*/
	}
	

}

class RelationTypeHelper extends Helper {
	public function __construct() {
		echo "RelationTypeHelper\n";
		Helper::__construct();
	}
	
	public function save() {
		/*what to do here eh?*/
	}
}

class OtherDefinedMeaningHelper extends Helper {
	public function __construct() {
		echo "OtherDefinedMeaningHelper\n";
		Helper::__construct();
	}
	
	public function save() {
		/*what to do here eh?*/
	}
	

}

class ReciprocalRelationsHelper extends Helper {
	public function __construct() {
		echo "ReciprocalRelationsHelper\n";
		Helper::__construct();
	}
	
	public function save() {
		/*what to do here eh?*/
	}
	

}

class CollectionMembershipHelper extends Helper {
	public function __construct() {
		echo "CollectionMembershipHelper\n";
		Helper::__construct();
	}
	
	public function save() {
		/*what to do here eh?*/
	}
	

}

class CollectionMeaningHelper extends Helper {
	public function __construct() {
		echo "CollectionMeaningHelper\n";
		Helper::__construct();
	}
	
	public function save() {
		/*what to do here eh?*/
	}
	

}

class GotoSourceHelper extends Helper {
	public function __construct() {
		echo "GotoSourceHelper\n";
		Helper::__construct();
	}
	
	public function save() {
		/*what to do here eh?*/
	}
	

}

class DefinedMeaningAttributesHelper extends Helper {
	public function __construct() {
		echo "DefinedMeaningAttributesHelper\n";
		Helper::__construct();
	}
	
	public function save() {
		/*what to do here eh?*/
	}
	

}
?>
