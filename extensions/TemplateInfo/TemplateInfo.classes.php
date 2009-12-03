<?php
/**
 * Classes for TemplateInfo extension
 *
 * @file
 * @ingroup Extensions
 */

class TemplateInfo {

	static $tab = "&nbsp;&nbsp;&nbsp;&nbsp;";

	/* Functions */

	public static function validateXML( $xml, &$error_msg ) {
		$xmlDTD =<<<END
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE template [
<!ELEMENT template (description?,params?,data*)>
<!ELEMENT templateinfo (param|group)*>
<!ELEMENT param (label?,description?,options?,type?,data*)>
<!ATTLIST param id ID #REQUIRED>
<!ELEMENT group (label?,description?,param*,data*)>
<!ELEMENT label (#PCDATA|msg)*>
<!ELEMENT description (#PCDATA|msg)*>
<!ELEMENT options (option*)>
<!ELEMENT option (#PCDATA|msg)*>
<!ELEMENT type (field*)>
<!ATTLIST type name CDATA #REQUIRED>
<!ELEMENT field EMPTY>
<!ATTLIST field name CDATA #REQUIRED>
<!ATTLIST field value CDATA #REQUIRED>
<!ELEMENT msg (#PCDATA)>
<!ATTLIST msg lang CDATA #REQUIRED>
<!ELEMENT data (field*)>
<!ATTLIST data app CDATA #REQUIRED>
]>

END;
		// we are using the SimpleXML library to do the XML validation
		// for now - this may change later
		// hide parsing warnings
		libxml_use_internal_errors(true);
		$xml_success = simplexml_load_string($xmlDTD . $xml);
		$errors = libxml_get_errors();
		$error_msg = $errors[0]->message;
		return $xml_success;
	}

	static function parseTemplateInfo($template_info_xml) {
		$text = "<p>Template description:</p>\n";
		$text .= "<table>\n";
		foreach ($template_info_xml->children() as $tag => $child) {
			if ($tag == 'group') {
				$text .= self::parseParamGroup($child);
			} elseif ($tag == 'param') {
				$text .= self::parseParam($child);
			}
		}
		$text .= "</table>\n";
		return $text;
	}

	static function parseParamGroup($param_group_xml) {
		$text = "<tr>\n";
		$text .= "<td colspan=\"2\" style=\"background: #bbaa88\">";
		$id = $param_group_xml->attributes()->id;
		$text .= "&nbsp;Group: <strong>$id</strong></td>";
		$text .= "</tr>\n";
		foreach ($param_group_xml->children() as $child) {
			$text .= self::parseParam($child);
		}
		return $text;
	}

	static function parseParam($param_xml) {
		$id = $param_xml->attributes()->id;
		$text = "<tr><td colspan=\"2\" style=\"background: #d3c2a0\">" . self::$tab . "Parameter: <strong>$id</strong></td></tr>\n";
		foreach ($param_xml->children() as $tag_name => $child) {
			if ($tag_name == 'label') {
				$text .= self::parseParamLabel($child);
			} elseif ($tag_name == 'description') {
				$text .= self::parseParamDescription($child);
			} elseif ($tag_name == 'options') {
				$text .= self::parseParamOptions($child);
			} elseif ($tag_name == 'data') {
				$text .= self::parseParamData($child);
			} elseif ($tag_name == 'type') {
				$text .= self::parseParamType($child);
			}
		}
		return $text;
	}

	static function parseParamLabel($param_label_xml) {
		if (count($param_label_xml->children()) == 0) {
			$text .= "<tr><td colspan=\"2\" style=\"background: #eeddbb\">" . self::$tab . self::$tab . "Label: $param_label_xml</td></tr>\n";
		} else {
			$text .= "<tr><td colspan=\"2\" style=\"background: #eeddbb\">" . self::$tab . self::$tab . "Label</td></tr>\n";
			foreach ($param_label_xml->children() as $child) {
				$text .= self::parseMsg($child);
			}
		}
		return $text;
	}

	static function parseParamDescription($param_desc_xml) {
		if (count($param_desc_xml->children()) == 0) {
			$text = "<tr><td colspan=\"2\" style=\"background: #eeddbb\">" . self::$tab . self::$tab . "Description: $param_desc_xml</td></tr>\n";
		} else {
			$text = "<tr><td colspan=\"2\" style=\"background: #eeddbb\">" . self::$tab . self::$tab . "Description</td></tr>\n";
			foreach ($param_desc_xml->children() as $child) {
				$text .= self::parseMsg($child);
			}
		}
		return $text;
	}

	static function parseParamType($param_type_xml) {
		$name = $param_type_xml->attributes()->name;
		$text = "<tr><td colspan=\"2\" style=\"background: #eeddbb\">" . self::$tab . self::$tab . "Type: $name</td></tr>\n";
		return $text;
	}

	static function parseParamOptions($param_options_xml) {
		$text = "<tr><td colspan=\"2\" style=\"background: #ffff77\">" . self::$tab . self::$tab . "Options</td></tr>\n";
		foreach ($param_options_xml->children() as $child) {
			$text .= self::parseParamOption($child);
		}
		return $text;
	}

	static function parseParamOption($param_option_xml) {
		$name = $param_option_xml->attributes()->name;
		$text = "<tr><td colspan=\"2\" style=\"background: #ffff99\">" . self::$tab . self::$tab . self::$tab . "Option: <strong>$name</strong></td></tr>\n";
		if (count($param_option_xml->children()) == 0) {
			$text .= "<tr><td colspan=\"2\" style=\"background: #ffffbb\">" . self::$tab . self::$tab . self::$tab . self::$tab . "$param_option_xml</td></tr>\n";
		} else {
			foreach ($param_option_xml->children() as $child) {
				$text .= self::parseOptionMsg($child);
			}
		}
		return $text;
	}

	static function parseMsg($msg_xml) {
		$language = $msg_xml->attributes()->language;
		$text = "<tr><td style=\"background: #ffeecc\">" . self::$tab . self::$tab . self::$tab . "$language</td><td style=\"background: white\">$msg_xml</td></tr>\n";
		return $text;
	}

	static function parseOptionMsg($msg_xml) {
		$language = $msg_xml->attributes()->language;
		$text = "<tr><td style=\"background: #ffffbb\">" . self::$tab . self::$tab . self::$tab . self::$tab . "$language</td><td style=\"background: white\">$msg_xml</td></tr>\n";
		return $text;
	}

	static function parseParamData($param_data_xml) {
		$app = $param_data_xml->attributes()->app;
		$text = "<tr><td colspan=\"2\" style=\"background: #77dd77\">" . self::$tab . self::$tab . "Data for app: <strong>$app</strong></td></tr>\n";
		foreach ($param_data_xml->children() as $child) {
			$text .= self::parseField($child);
		}
		return $text;
	}

	static function parseField($field_xml) {
		$name = $field_xml->attributes()->name;
		$text = "<tr><td style=\"background: #99ff99\">" . self::$tab . self::$tab . self::$tab . "$name</td><td style=\"background: white\">$field_xml</td></tr>\n";
		return $text;
	}

}
