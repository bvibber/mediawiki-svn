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
		$wgUser;

	$optionAttribute = $_GET['option-attribute'];
	$attributeObject = $_GET['attribute-object'];
	$lang_code = $wgUser->getOption('language');

	$dbr =& wfGetDB(DB_SLAVE);
	$sql = 'SELECT language_id' .
			' FROM uw_syntrans' .
			' JOIN uw_expression_ns ON uw_expression_ns.expression_id = uw_syntrans.expression_id' .
			' WHERE uw_syntrans.syntrans_sid = ' . $attributeObject .
			' AND ' . getLatestTransactionRestriction('uw_syntrans') .
			' AND ' . getLatestTransactionRestriction('uw_expression_ns');
	$lang_res = $dbr->query($sql);
	$objectLanguage = $dbr->fetchObject($lang_res)->language_id;

	$sql = 'SELECT uw_option_attribute_options.option_id,uw_option_attribute_options.option_mid' .
			' FROM uw_option_attribute_options' .
			' JOIN uw_class_attributes ON uw_class_attributes.object_id = uw_option_attribute_options.attribute_id' .
			' WHERE uw_class_attributes.attribute_mid = ' . $optionAttribute .
			' AND (uw_option_attribute_options.language_id = ' . $objectLanguage .
			' OR uw_option_attribute_options.language_id = 0)' .
			' AND ' . getLatestTransactionRestriction('uw_option_attribute_options') .
			' AND ' . getLatestTransactionRestriction('uw_class_attributes');
	$options_res = $dbr->query($sql);

	$optionsString = '';
	while ($options_row = $dbr->fetchObject($options_res)) {
		/* Use a simpler query if the user's language is English. */
		if ($lang_code == 'en' || !($lang_id = getLanguageIdForCode($lang_code))) {
			$sql = 'SELECT uw_expression_ns.spelling' .
					' FROM uw_syntrans' .
					' JOIN uw_expression_ns ON uw_expression_ns.expression_id = uw_syntrans.expression_id' .
					' WHERE uw_syntrans.defined_meaning_id = ' . $options_row->option_mid .
					' AND uw_expression_ns.language_id = ' . getLanguageIdForCode('en') .
					' AND ' . getLatestTransactionRestriction('uw_syntrans') .
					' AND ' . getLatestTransactionRestriction('uw_expression_ns');
		}
		/* Fall back on English in cases where an option name is not present in the
			user's preferred language. */
		else {
			/* XXX: This setup is really hacked together. It NEEDS to be improved. */
			$sql = 'SELECT uw_expression_ns.spelling' .
					' FROM uw_syntrans' .
					' JOIN uw_expression_ns ON uw_expression_ns.expression_id = uw_syntrans.expression_id' .
					' WHERE uw_syntrans.defined_meaning_id = ' . $options_row->option_mid .
					' AND uw_expression_ns.language_id = ' . $lang_id .
					' AND ' . getLatestTransactionRestriction('uw_syntrans') .
					' AND ' . getLatestTransactionRestriction('uw_expression_ns');
			$res = $dbr->query($sql);
			if (!$dbr->fetchObject($res)->spelling)
				$sql = 'SELECT uw_expression_ns.spelling' .
						' FROM uw_syntrans' .
						' JOIN uw_expression_ns ON uw_expression_ns.expression_id = uw_syntrans.expression_id' .
						' WHERE uw_syntrans.defined_meaning_id = ' . $options_row->option_mid .
						' AND uw_expression_ns.language_id = ' . getLanguageIdForCode('en') .
						' AND ' . getLatestTransactionRestriction('uw_syntrans') .
						' AND ' . getLatestTransactionRestriction('uw_expression_ns');
		}

		$spelling_res = $dbr->query($sql);
		$spelling_row = $dbr->fetchObject($spelling_res);
		if ($optionsString != '')
			$optionsString .= "\n";
		$optionsString .= $options_row->option_id . ';' . $spelling_row->spelling;
	}

	return $optionsString;
}

?>
