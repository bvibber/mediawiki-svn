<?php
/**
 * Lets the user import a CSV file to turn into wiki pages
 *
 * @author Yaron Koren
 */

if (!defined('MEDIAWIKI')) die();

class DTPage  {
	var $mName;
	var $mTemplates;
	var $mFreeText;

	public function DTPage() {
		$this->mTemplates = array();
	}

	function setName($name) {
		$this->mName = $name;
	}

	function getName() {
		return $this->mName;
	}

	function addTemplateField($template_name, $field_name, $value) {
		if (! array_key_exists($template_name, $this->mTemplates)) {
			$this->mTemplates[$template_name] = array();
		}
		$this->mTemplates[$template_name][$field_name] = $value;
	}

	function setFreeText($free_text) {
		$this->mFreeText = $free_text;
	}

	function createText() {
		$text = "";
		foreach ($this->mTemplates as $template_name => $fields) {
			$text .= '{{' . $template_name . "\n";
			foreach ($fields as $field_name => $val) {
				$text .= "|$field_name=$val\n";
			}
			$text .= '}}' . "\n";
		}
		$text .= $this->mFreeText;
		return $text;
	}
}

class DTImportCSV extends SpecialPage {

	/**
	 * Constructor
	 */
	public function DTImportCSV() {
		global $wgLanguageCode;
		SpecialPage::SpecialPage('ImportCSV');
		dtfInitContentLanguage($wgLanguageCode);
		wfLoadExtensionMessages('DataTransfer');
	}

	function execute($query) {
		global $wgUser, $wgOut, $wgRequest;
		$this->setHeaders();

		if ( ! $wgUser->isAllowed('importxml') ) {
			global $wgOut;
			$wgOut->permissionRequired('importxml');
			return;
		}

		if ($wgRequest->getCheck('import_file')) {
			$text = "<p>" . wfMsg('dt_import_importing') . "</p>\n";
			$source = ImportStreamSource::newFromUpload( "csv_file" );
			$pages = array();
			$error_msg = self::getCSVData($source->mHandle, $pages);
			if (! is_null($error_msg))
				$text .= $error_msg;
			else
				$text .= self::modifyPages($pages);
		} else {
			$select_file_label = wfMsg('dt_import_selectfile', 'CSV');
			$import_button = wfMsg('import-interwiki-submit');
			$text =<<<END
	<p>$select_file_label</p>
	<form enctype="multipart/form-data" action="" method="post">
	<p><input type="file" name="csv_file" size="25" /></p>
	<p><input type="Submit" name="import_file" value="$import_button"></p>
	</form>

END;
		}

		$wgOut->addHTML($text);
	}


	static function getCSVData($csv_file, &$pages) {
		$table = array();
		while ($line = fgetcsv($csv_file)) {
			array_push($table, $line);
		}
		fclose($csv_file);
		// check header line to make sure every term is in the
		// correct format
		$title_label =  wfMsgForContent('dt_xml_title');
		$free_text_label =  wfMsgForContent('dt_xml_freetext');
		foreach ($table[0] as $i => $header_val) {
			if ($header_val !== $title_label && $header_val !== $free_text_label &&
				! preg_match('/^[^\[\]]+\[[^\[\]]+]$/', $header_val)) {
				$error_msg = wfMsg('dt_importcsv_badheader', $i, $header_val, $title_label, $free_text_label);
				return $error_msg;
			}
		}
		foreach ($table as $i => $line) {
			if ($i == 0) continue;
			$page = new DTPage();
			foreach ($line as $j => $val) {
				if ($val == '') continue;
				if ($table[0][$j] == $title_label) {
					$page->setName($val);
				} elseif ($table[0][$j] == $free_text_label) {
					$page->setFreeText($val);
				} else {
					list($template_name, $field_name) = explode('[', str_replace(']', '', $table[0][$j]));
					$page->addTemplateField($template_name, $field_name, $val);
				}
			}
			$pages[] = $page;
		}
	}

	function modifyPages($pages) {
		$text = "";
		$jobs = array();
		$job_params = array();
		global $wgUser;
		$job_params['user_id'] = $wgUser->getId();
		$job_params['edit_summary'] = wfMsgForContent('dt_import_editsummary', 'CSV');
		foreach ($pages as $page) {
			$title = Title::newFromText($page->getName());
			$job_params['text'] = $page->createText();
			$jobs[] = new DTImportJob( $title, $job_params );
		}
		Job::batchInsert( $jobs );
		$text .= wfMsg('dt_import_success', count($jobs), 'CSV');
		return $text;
	}

}
