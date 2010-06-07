<?php
/**
 * @author Yaron Koren
 */

if ( !defined( 'MEDIAWIKI' ) ) die();

/**
 * Class that holds information on a single internal object, including all
 * its properties.
 */
class SIOInternalObject {
	protected $mMainTitle;
	protected $mIndex;
	protected $mPropertyValuePairs;

	public function SIOInternalObject( $mainTitle, $index ) {
		$this->mMainTitle = $mainTitle;
		$this->mIndex = $index;
		$this->mPropertyValuePairs = array();
	}

	public function addPropertyAndValue( $propName, $value ) {
		$property = SMWPropertyValue::makeUserProperty( $propName );
		$dataValue = SMWDataValueFactory::newPropertyObjectValue( $property, $value );
		if ( $dataValue->isValid() ) {
			$this->mPropertyValuePairs[] = array( $property, $dataValue );
		} // else - show an error message?
	}

	public function getPropertyValuePairs() {
		return $this->mPropertyValuePairs;
	}

	public function getName() {
		return $this->mMainTitle->getDBkey() . '#' . $this->mIndex;
	}
}

/**
 * The SIOTitle and SIOInternalObjectValue exist for only one reason: in order
 * to be used by SIOSQLStore::createRDF(), to spoof Semantic MediaWiki's
 * RDF-exporting code into thinking that it's dealing with actual wiki pages.
 */
class SIOTitle {
	function __construct ($name, $namespace) {
		$this->mName = $name;
		$this->mNamespace = $namespace;
	}

	/**
	 * Based on functions in Title class
	 */
	function getPrefixedName() {
		$s = '';
		if ( 0 != $this->mNamespace ) {
			global $wgContLang;
			$s .= $wgContLang->getNsText( $this->mNamespace ) . ':';
		}
		$s .= $this->mName;
		return $s;
	}

	function getPrefixedURL() {
		$s = $this->getPrefixedName();
		return wfUrlencode( str_replace( ' ', '_', $s ) );
	}
}

class SIOInternalObjectValue extends SMWWikiPageValue {
	function __construct($name, $namespace) {
		$this->mSIOTitle = new SIOTitle( $name, $namespace );
	}
	function getExportData() {
		global $smwgNamespace;
		return new SMWExpData( new SMWExpResource( null ) );
	}

	function getTitle() {
		return $this->mSIOTitle;
	}

	function getWikiValue() {
		return $this->mSIOTitle->getPrefixedName();
	}
}

/**
 * Class for all database-related actions.
 * This class exists mostly because SMWSQLStore2's functions makeSMWPageID()
 * and makeSMWPropertyID(), which are needed for the DB access, are both
 * protected, and thus can't be accessed externally.
 */
class SIOSQLStore extends SMWSQLStore2 {
	static function getIDsForDeletion( $pageName, $namespace ) {
		$ids = array();

		$iw = '';
		$db = wfGetDB( DB_SLAVE );
		$res = $db->select( 'smw_ids', array( 'smw_id' ), 'smw_title LIKE ' . $db->addQuotes( $pageName . '#%' ) . ' AND ' . 'smw_namespace=' . $db->addQuotes( $namespace ) . ' AND smw_iw=' . $db->addQuotes( $iw ), 'SIO::getSMWPageObjectIDs', array() );
		while ( $row = $db->fetchObject( $res ) ) {
			$ids[] = $row->smw_id;
		}
		return $ids;
	}

	function getStorageSQL( $mainPageName, $namespace, $internalObject ) {
		$mainPageID = $this->makeSMWPageID( $mainPageName, $namespace, '' );
		$ioID = $this->makeSMWPageID( $internalObject->getName(), $namespace, '' );
		$upRels2 = array();
		$upAtts2 = array();
		$upText2 = array();
		$upCoords = array();
		// set all the properties pointing from this internal object
		foreach ( $internalObject->getPropertyValuePairs() as $propertyValuePair ) {
			list( $property, $value ) = $propertyValuePair;
			// handling changed in SMW 1.5
			if ( method_exists( 'SMWSQLStore2', 'findPropertyTableID' ) ) {
				$tableid = SMWSQLStore2::findPropertyTableID( $property );
				$isRelation = ( $tableid == 'smw_rels2' );
				$isAttribute = ( $tableid == 'smw_atts2' );
				$isText = ( $tableid == 'smw_text2' );
				// new with SMW 1.5.1 / SM 0.6
				$isCoords = ( $tableid == 'smw_coords' );
			} else {
				$mode = SMWSQLStore2::getStorageMode( $property->getPropertyTypeID() );
				$isRelation = ( $mode == SMW_SQL2_RELS2 );
				$isAttribute = ( $mode == SMW_SQL2_ATTS2 );
				$isText = ( $mode == SMW_SQL2_TEXT2 );
				$isCoords = false;
			}
			if ( $isRelation ) {
				$upRels2[] = array(
					's_id' => $ioID,
					'p_id' => $this->makeSMWPropertyID( $property ),
					'o_id' => $this->makeSMWPageID( $value->getDBkey(), $value->getNamespace(), $value->getInterwiki() )
				);
			} elseif ( $isAttribute ) {
				$keys = $value->getDBkeys();
				if ( method_exists( $value, 'getValueKey' ) ) {
					$valueNum = $value->getValueKey();
				} else {
					$valueNum = $value->getNumericValue();
				}
				$upAtts2[] = array(
					's_id' => $ioID,
					'p_id' => $this->makeSMWPropertyID( $property ),
					'value_unit' => $value->getUnit(),
					'value_xsd' => $keys[0],
					'value_num' => $valueNum
				);
			} elseif ( $isText ) {
				$keys = $value->getDBkeys();
				$upText2[] = array(
					's_id' => $ioID,
					'p_id' => $this->makeSMWPropertyID( $property ),
					'value_blob' => $keys[0]
				);
			} elseif ( $isCoords ) {
				$keys = $value->getDBkeys();
				$upCoords[] = array(
					's_id' => $ioID,
					'p_id' => $this->makeSMWPropertyID( $property ),
					'lat' => $keys[0],
					'lon' => $keys[1],
				);
			}
		}
		return array( $upRels2, $upAtts2, $upText2, $upCoords );
	}

	static function createRDF( $title, $rdfDataArray ) {
		$pageName = $title->getText();
		$namespace = $title->getNamespace();

		// go through all SIOs for the current page, create RDF for
		// each one, and add it to the general array
		$iw = '';
		$db = wfGetDB( DB_SLAVE );
		$res = $db->select( 'smw_ids', array( 'smw_id', 'smw_namespace', 'smw_title' ), 'smw_title LIKE ' . $db->addQuotes( $pageName . '#%' ) . ' AND ' . 'smw_namespace=' . $db->addQuotes( $namespace ) . ' AND smw_iw=' . $db->addQuotes( $iw ), 'SIO::getSMWPageObjectIDs', array() );
		while ( $row = $db->fetchObject( $res ) ) {
			$value = new SIOInternalObjectValue( $row->smw_title, $row->smw_namespace );
			$semdata = new SMWSemanticData( $value, false );
			$propertyTables = SMWSQLStore2::getPropertyTables();
			foreach ( $propertyTables as $tableName => $propertyTable ) {
				$data = smwfGetStore()->fetchSemanticData( $row->smw_id, null, $propertyTable );
				foreach ( $data as $d ) {
					$semdata->addPropertyStubValue( reset( $d ), end( $d ) );
				}
			}
			$rdfDataArray[] = SMWExporter::makeExportData( $semdata, null );
		}
		return true;
	}
}

/**
 * Class for hook functions for creating and storing information
 */
class SIOHandler {

	static $mCurPageName = '';
	static $mCurPageNamespace = 0;
	static $mInternalObjectIndex = 1;
	static $mInternalObjects = array();

	public static function clearState( &$parser ) {
		self::$mCurPageName = '';
		self::$mCurPageNamespace = 0;
		self::$mInternalObjectIndex = 1;
		return true;
	}

	public static function doSetInternal( &$parser ) {
		$mainPageName = $parser->getTitle()->getDBKey();
		$mainPageNamespace = $parser->getTitle()->getNamespace();
		if ( $mainPageName == self::$mCurPageName &&
			$mainPageNamespace == self::$mCurPageNamespace ) {
			self::$mInternalObjectIndex++;
		} else {
			self::$mCurPageName = $mainPageName;
			self::$mCurPageNamespace = $mainPageNamespace;
			self::$mInternalObjectIndex = 1;
		}
		$curObjectNum = self::$mInternalObjectIndex;
		$params = func_get_args();
		array_shift( $params ); // we already know the $parser...
		$internalObject = new SIOInternalObject( $parser->getTitle(), $curObjectNum );
		$objToPagePropName = array_shift( $params );
		$mainPageName = $parser->getTitle()->getText();
		if ( ( $nsText = $parser->getTitle()->getNsText() ) != '' ) {
			$mainPageName = $nsText . ':' . $mainPageName;
		}
		$internalObject->addPropertyAndValue( $objToPagePropName, $mainPageName );
		foreach ( $params as $param ) {
			$parts = explode( "=", trim( $param ), 2 );
			if ( count( $parts ) == 2 ) {
				$key = $parts[0];
				$value = $parts[1];
				// if the property name ends with '#list', it's
				// a comma-delimited group of values
				if ( substr( $key, - 5 ) == '#list' ) {
					$key = substr( $key, 0, strlen( $key ) - 5 );
					$listValues = explode( ',', $value );
					foreach ( $listValues as $listValue ) {
						$internalObject->addPropertyAndValue( $key, trim( $listValue ) );
					}
				} else {
					$internalObject->addPropertyAndValue( $key, $value );
				}
			}
		}
		self::$mInternalObjects[] = $internalObject;
	}

	public static function updateData( $subject ) {
		$sioSQLStore = new SIOSQLStore();
		// Find all "pages" in the SMW IDs table that are internal
		// objects for this page, and delete their properties from
		// the SMW tables.
		// Then save the current contents of the $mInternalObjects
		// array.
		$pageName = $subject->getDBKey();
		$namespace = $subject->getNamespace();
		$idsForDeletion = SIOSQLStore::getIDsForDeletion( $pageName, $namespace );

		$allRels2Inserts = array();
		$allAtts2Inserts = array();
		$allText2Inserts = array();
		$allCoordsInserts = array();
		foreach ( self::$mInternalObjects as $internalObject ) {
			list( $upRels2, $upAtts2, $upText2, $upCoords ) = $sioSQLStore->getStorageSQL( $pageName, $namespace, $internalObject );
			$allRels2Inserts = array_merge( $allRels2Inserts, $upRels2 );
			$allAtts2Inserts = array_merge( $allAtts2Inserts, $upAtts2 );
			$allText2Inserts = array_merge( $allText2Inserts, $upText2 );
			$allCoordsInserts = array_merge( $allCoordsInserts, $upCoords );
		}

		// now save everything to the database, in a single transaction
		$db = wfGetDB( DB_MASTER );
		$db->begin( 'SIO::updatePageData' );
		if ( count( $idsForDeletion ) > 0 ) {
			$idsString = '(' . implode ( ', ', $idsForDeletion ) . ')';
			$db->delete( 'smw_rels2', array( "(s_id IN $idsString) OR (o_id IN $idsString)" ), 'SIO::deleteRels2Data' );
			$db->delete( 'smw_atts2', array( "s_id IN $idsString" ), 'SIO::deleteAtts2Data' );
			$db->delete( 'smw_text2', array( "s_id IN $idsString" ), 'SIO::deleteText2Data' );
			$db->delete( 'sm_coords', array( "s_id IN $idsString" ), 'SIO::deleteCoordsData' );
		}

		if ( count( $allRels2Inserts ) > 0 ) {
			$db->insert( 'smw_rels2', $allRels2Inserts, 'SIO::updateRels2Data' );
		}
		if ( count( $allAtts2Inserts ) > 0 ) {
			$db->insert( 'smw_atts2', $allAtts2Inserts, 'SIO::updateAtts2Data' );
		}
		if ( count( $allText2Inserts ) > 0 ) {
			$db->insert( 'smw_text2', $allText2Inserts, 'SIO::updateText2Data' );
		}
		if ( count( $allCoordsInserts ) > 0 ) {
			$db->insert( 'sm_coords', $allCoordsInserts, 'SIO::updateCoordsData' );
		}
		// end transaction
		$db->commit( 'SIO::updatePageData' );
		self::$mInternalObjects = array();
		return true;
	}

	/**
	 * Takes a set of SMW "update jobs", and keeps only the unique, actual
	 * titles among them - this is useful if there are any internal objects
	 * among the group; a set of names like "Page name#1", "Page name#2"
	 * etc. should be turned into just "Page name".
	 */
	static function handleUpdatingOfInternalObjects( &$jobs ) {
		$uniqueTitles = array();
		foreach ( $jobs as $i => $job ) {
			$title = Title::makeTitleSafe( $job->title->getNamespace(), $job->title->getText() );
			$id = $title->getArticleID();
			$uniqueTitles[$id] = $title;
		}
		$jobs = array();
		foreach ( $uniqueTitles as $id => $title ) {
			$jobs[] = new SMWUpdateJob( $title );
		}
		return true;
	}

	/**
	 * Takes a set of SMW "update jobs" generated by refresh data and removes
	 * any job with a fragment (in other words a job trying to update a SIO object)
	 * We aren't guaranteed that all the jobs related to a single page using SIO
	 * will be in a single one of these batches so we remove everything updating
	 * a SIO object instead of filtering them down to unique titles.
	 */
	 static function handleRefreshingOfInternalObjects( &$jobs ) {
	 	$allJobs = $jobs;
	 	$jobs = array();
	 	foreach ( $allJobs as $job ) {
	 		if ( strpos( $job->title->getText(), '#' ) === false )
	 			$jobs[] = $job;
	 	}
		return true;
	}
}
