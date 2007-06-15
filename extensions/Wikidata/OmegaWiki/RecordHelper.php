<?php

class RecordHelperFactory {
	function __construct() {
		# ---
	}

	function getRecordHelper($record) {
		$type=$record->type();
		if (empty($type))
			return null;
		switch($type) {
			case "expression":
				echo "express!";
				break;
			case "synonyms-translations":
				echo "sinner!";
				break;
		}

	}
}
?>
