<?php
/**
 * @author Yaron Koren
 */

if (!defined('MEDIAWIKI')) die();

/**
 * Class that holds information on a single internal object, including all
 * its properties.
 */
class SIOInternalObject {
	var $main_title;
	var $index;
	var $property_value_pairs;

	public function SIOInternalObject($main_title, $index) {
		$this->main_title = $main_title;
		$this->index = $index;
		$this->property_value_pairs = array();
	}

	public function addPropertyAndValue($prop_name, $value) {
		$property = SMWPropertyValue::makeUserProperty($prop_name);
		$data_value = SMWDataValueFactory::newPropertyObjectValue($property, $value);
		if ($data_value->isValid()) {
			$this->property_value_pairs[] = array($property, $data_value);
		} // else - show an error message?
	}

	public function getName() {
		return $this->main_title->getDBkey() . '#' . $this->index;
	}
}

/**
 * Class for all database-related actions.
 * This class exists mostly because SMWSQLStore2's functions makeSMWPageID()
 * and makeSMWPropertyID(), which are needed for the DB access, are both
 * protected, and thus can't be accessed externally.
 */
class SIOSQLStore extends SMWSQLStore2 {
	function deletePageObjects($page_name, $namespace) {
		$ids = array();

		$iw = '';
		$db =& wfGetDB( DB_SLAVE );
		$res = $db->select('smw_ids', array('smw_id'), 'smw_title LIKE ' . $db->addQuotes($page_name . '#%') . ' AND ' . 'smw_namespace=' . $db->addQuotes($namespace) . ' AND smw_iw=' . $db->addQuotes($iw), 'SIO::getSMWPageObjectIDs', array());
		while ($row = $db->fetchObject($res)) {
			$ids[] = $row->smw_id;
		}
		foreach ($ids as $id) {
			$db->delete('smw_rels2', array('s_id' => $id), 'SIO::updateData::Rels2');
			$db->delete('smw_rels2', array('o_id' => $id), 'SIO::updateData::Rels2');
			$db->delete('smw_atts2', array('s_id' => $id), 'SMW::deleteSubject::Atts2');
		}
	}

	function storeAllInfo($main_page_name, $namespace, $internal_object) {
		$main_page_id = $this->makeSMWPageID($main_page_name, $namespace, '');
		$io_id = $this->makeSMWPageID($internal_object->getName(), $namespace, '');
		$up_rels2 = array();
		$up_atts2 = array();
		// set all the properties pointing from this internal object
		foreach ($internal_object->property_value_pairs as $property_value_pair) {
			list($property, $value) = $property_value_pair;
			$mode = SMWSQLStore2::getStorageMode($property->getPropertyTypeID());
			switch ($mode) {
			case SMW_SQL2_RELS2:
				$up_rels2[] = array(
					's_id' => $io_id,
					'p_id' => $this->makeSMWPropertyID($property),
					'o_id' => $this->makeSMWPageID($value->getDBkey(), $value->getNamespace(), $value->getInterwiki())
				);
				break;
			case SMW_SQL2_ATTS2:
				$keys = $value->getDBkeys();
				$up_atts2[] = array(
					's_id' => $io_id,
					'p_id' => $this->makeSMWPropertyID($property),
					'value_unit' => $value->getUnit(),
					'value_xsd' => $keys[0],
					'value_num' => $value->getNumericValue()
				);
				break;
			}
		}

		// now save everything to the database
		$db =& wfGetDB( DB_MASTER );
		if (count($up_rels2) > 0) {
			$db->insert( 'smw_rels2', $up_rels2, 'SMW::updateRel2Data');
		}
		if (count($up_atts2) > 0) {
			$db->insert( 'smw_atts2', $up_atts2, 'SMW::updateAtt2Data');
		}
	}
}

/**
 * Class for hook functions for creating and storing information
 */
class SIOHandler {

	static $cur_page_name = '';
	static $cur_page_namespace = 0;
	static $internal_object_index = 1;
	static $internal_objects = array();

	public static function doSetInternal(&$parser) {
		$main_page_name = $parser->getTitle()->getDBKey();
		$main_page_namespace = $parser->getTitle()->getNamespace();
		if ($main_page_name == self::$cur_page_name &&
			$main_page_namespace == self::$cur_page_namespace) {
			self::$internal_object_index++;
		} else {
			self::$cur_page_name = $main_page_name;
			self::$cur_page_namespace = $main_page_namespace;
			self::$internal_object_index = 1;
		}
		$cur_object_num = self::$internal_object_index;
		$params = func_get_args();
		array_shift( $params ); // we already know the $parser...
		$internal_object = new SIOInternalObject($parser->getTitle(), $cur_object_num);
		$obj_to_page_prop_name = array_shift( $params );
		$internal_object->addPropertyAndValue($obj_to_page_prop_name, $parser->getTitle());
		foreach ( $params as $param ) {
			$parts = explode("=", trim($param));
			if (count($parts)==2) {
				$key = $parts[0];
				$value = $parts[1];
				$internal_object->addPropertyAndValue($key, $value);
			}
		}
		self::$internal_objects[] = $internal_object;
	}

	public static function updateData($subject) {
		$sio_sql_store = new SIOSQLStore();
		// Find all "pages" in the SMW IDs table that are internal
		// objects for this page, and delete their properties from
		// the SMW tables.
		// Then save the current contents of the $internal_objects
		// array.
		$page_name = $subject->getDBKey();
		$namespace = $subject->getNamespace();
		$sio_sql_store->deletePageObjects($page_name, $namespace);
		foreach (self::$internal_objects as $internal_object) {
			$sio_sql_store->storeAllInfo($page_name, $namespace, $internal_object);
		}
		self::$internal_objects = array();
		return true;
	}

}
