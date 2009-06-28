<?php
/**
 * Send purge requests for listed pages to squid
 *
 * @file
 * @ingroup Maintenance
 */

require_once( "Maintenance.php" );

class PurgeList extends Maintenance {
	public function __construct() {
		parent::__construct();
		$this->mDescription = "Send purge requests for listed pages to squid";
	}

	public function execute() {
		$stdin = $this->getStdin();
		$urls = array();

		while( !feof( $stdin ) ) {
			$page = trim( fgets( $stdin ) );
			if ( substr( $page, 0, 7 ) == 'http://' ) {
				$urls[] = $page;
			} elseif( $page !== '' ) {
				$title = Title::newFromText( $page );
				if( $title ) {
					$url = $title->getFullUrl();
					$this->output( "$url\n" );
					$urls[] = $url;
					if( isset( $options['purge'] ) ) {
						$title->invalidateCache();
					}
				} else {
					$this->output( "(Invalid title '$page')\n" );
				}
			}
		}

		$this->output( "Purging " . count( $urls ) . " urls...\n" );
		$u = new SquidUpdate( $urls );
		$u->doUpdate();

		$this->output( "Done!\n" );
	}
}

