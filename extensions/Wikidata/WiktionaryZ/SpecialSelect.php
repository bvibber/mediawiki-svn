<?php

if (!defined('MEDIAWIKI')) die();

$wgExtensionFunctions[] = 'wfSpecialSelect';

function wfSpecialSelect() {
	class SpecialSelect extends SpecialPage {
		function SpecialSelect() {
			SpecialPage::SpecialPage('Select');
		}

		function execute( $par ) {
			require_once('languages.php');
			require_once('Transaction.php');
			global
				$wgOut,	$IP;

			$wgOut->disable();

			echo getSelectOptions();
		}
	}

	SpecialPage::addPage(new SpecialSelect());
}

function getSelectOptions() {
	global
		$idAttribute;

	$optionAttribute = $_GET['option-attribute'];
	$attributeObject = $_GET['attribute-object'];

	$dbr =& wfGetDB(DB_SLAVE);

	$sql = getSQLForOptions($optionAttribute, $attributeObject);
	$result = $dbr->query($sql);

	$optionString = '';
	while ($row = $dbr->fetchObject($result)) {
		if ($optionString != '')
			$optionString .= "\n";
		$optionString .= $row->row_id . ';' . $row->spelling;
	}
	return $optionString;
}

/* Return SQL query string for fetching language names. */
function getSQLForOptions($optionAttribute, $attributeObject) {
	global
		$wgUser;

	$lang_code = $wgUser->getOption('language');

	$dbr =& wfGetDB(DB_SLAVE);
	$sql = 'SELECT language_id' .
			' FROM uw_syntrans' .
			' JOIN uw_expression_ns ON uw_expression_ns.expression_id = uw_syntrans.expression_id' .
			' WHERE uw_syntrans.syntrans_sid = ' . $attributeObject .
			' AND ' . getLatestTransactionRestriction('uw_syntrans') .
			' AND ' . getLatestTransactionRestriction('uw_expression_ns');
	$result = $dbr->query($sql);
	$objectLanguage = $dbr->fetchObject($result)->language_id;

	/* Use a simpler query if the user's language is English. */
	if ($lang_code == 'en' || !($lang_id = getLanguageIdForCode($lang_code)))
		return 'SELECT option_id AS row_id,spelling' .
			' FROM uw_option_attribute_options' .
			' JOIN uw_syntrans ON uw_syntrans.defined_meaning_id = uw_option_attribute_options.option_mid' .
			' JOIN uw_expression_ns ON uw_expression_ns.expression_id = uw_syntrans.expression_id' .
			' JOIN uw_class_attributes ON uw_class_attributes.object_id = uw_option_attribute_options.attribute_id' .
			' WHERE uw_expression_ns.language_id = ' . getLanguageIdForCode('en') .
			' AND uw_class_attributes.attribute_mid = ' . $optionAttribute .
			' AND (uw_option_attribute_options.language_id = ' . $objectLanguage .
			' OR uw_option_attribute_options.language_id = 0)' .
			' AND ' . getLatestTransactionRestriction('uw_option_attribute_options') .
			' AND ' . getLatestTransactionRestriction('uw_syntrans') .
			' AND ' . getLatestTransactionRestriction('uw_expression_ns') .
			' GROUP BY option_id';
	/* Fall back on English in cases where an option name is not present in the
		user's preferred language. */
	else
		return 'SELECT option_id AS row_id,COALESCE(uwens1.spelling,uwens2.spelling) AS spelling' .
			' FROM uw_option_attribute_options' .
			' JOIN uw_syntrans ON uw_syntrans.defined_meaning_id = uw_option_atribute_options.option_mid' .
			' LEFT JOIN uw_expression_ns AS uwens1 ON uwens1.expression_id = uw_syntrans.expression_id AND uwens1.language_id = ' . $lang_id .
			' JOIN uw_expression_ns AS uwens2 ON uwens2.expression_id = uw_syntrans.expression_id AND uwens2.language_id = ' . getLanguageIdForCode('en') .
			' AND ' . getLatestTransactionRestriction('uw_option_attribute_options') .
			' AND ' . getLatestTransactionRestriction('uw_syntrans') .
			' AND ' . getLatestTransactionRestriction('uwens1') .
			' AND ' . getLatestTransactionRestriction('uwens2') .
			' GROUP BY option_id';
}

?>
