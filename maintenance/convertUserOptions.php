<?php
/**
 * Do each user sequentially, since accounts can't be deleted
 * @ingroup Maintenance
 */

require_once( "Maintenance.php" );

class ConvertUserOptions extends Maintenance {

	private $mConversionCount = 0;

	public function __construct() {
		parent::__construct();
		$this->mDescription = "Convert user options from old to new system";
	}
	
	public function execute() {
		$this->output( "Beginning batch conversion of user options.\n" );
		$id = 0;
		$dbw = wfGetDB( DB_MASTER );

		while ($id !== null) {
			$idCond = 'user_id>'.$dbw->addQuotes( $id );
			$optCond = "user_options!=".$dbw->addQuotes( '' ); // For compatibility
			$res = $dbw->select( 'user', '*',
					array( $optCond, $idCond ), __METHOD__,
					array( 'LIMIT' => 50, 'FOR UPDATE' ) );
			$id = $this->convertOptionBatch( $res, $dbw );
			$dbw->commit();
	
			wfWaitForSlaves( 1 );
	
			if ($id)
				$this->output( "--Converted to ID $id\n" );
		}
		$this->output( "Conversion done. Converted " . $this->mConversionCount . " user records.\n" );
	}

	function convertOptionBatch( $res, $dbw ) {
		$id = null;
		while ($row = $dbw->fetchObject( $res ) ) {
			$this->mConversionCount++;
	
			$u = User::newFromRow( $row );
	
			$u->saveSettings();
			$id = $row->user_id;
		}
	
		return $id;
	}
}

$maintClass = "ConvertUserOptions";
require_once( DO_MAINTENANCE );
