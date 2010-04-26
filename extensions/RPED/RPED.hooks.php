<?php
if ( !defined( 'MEDIAWIKI' ) ) {
	echo <<<EOT
	To install the RPED extension, put the following line in LocalSettings.php:
	require_once( "\$IP/extensions/RPED/RPED.php" );
EOT;
	exit( 1 );
}

class RPEDHooks {
	public static function RPEDCreateTable() {
		global $wgExtNewTables;
		
		$wgExtNewTables[] = array(
			'rped_page',
			dirname( __FILE__ ) . '/rpedtable.sql'
		);
		return true;
	}

	public static function wikipediaLink( $skin, $target, &$text,
		&$customAttribs, &$query, &$options, &$ret
	) {
		global $wgLocalStyle, $wgRemoteStyle, $wgPureWikiDeletionInEffect, $wgTitle, $wgRequest;
		wfLoadExtensionMessages('RPED');
		if ( $wgTitle->getNamespace () == -1 ) {
			return true;
		}

		if ( isset( $query['action'] ) && $query['action'] == 'history' ) {
			return true;
		}

		if ( $wgRequest->getText( 'action' ) == 'history' ) {
			return true;
		}

		$itIsBlank = false;

		// Return immediately if we know it's existent on the local wiki
		if ( in_array( 'known', $options ) ) {
			if ( !isset( $query['action'] ) && !isset( $query['curid'] ) ) {
				$customAttribs['style'] = $wgLocalStyle;
			}

			if ( !isset( $wgPureWikiDeletionInEffect ) || $wgPureWikiDeletionInEffect != true ) {
				return true;
			}

			$dbr = wfGetDB( DB_SLAVE );

			/*$myRevision=Revision::loadFromTitle($dbr,$target);
			if ($myRevision->getRawText()!=''){*/
			$id = $target->getArticleID();
			$result = $dbr->selectRow(
				'blanked_page',
				'blank_page_id',
				array( "blank_page_id" => $id )
			);

			if ( !$result ) {
				return true;
			}

			$itIsBlank = true;
		}

		// If it doesn't exist on the local wiki, then see if it exists on the
		// remote wiki (Wikipedia)
		if ( in_array( 'broken', $options ) || $itIsBlank == true ) {
			$title = $target->getPrefixedText ();

			for ( $thiscount = 0; $thiscount < strlen( $title ); $thiscount++ ) {
				if ( substr( $title, $thiscount, 1 ) == ' ' ) {
					$title = substr_replace( $title, '_', $thiscount, 1 );
				}
			}
			$dbr = wfGetDB( DB_SLAVE );
			$result = $dbr->selectRow(
				'rped_page',
				'rped_page_id',
				array( "rped_page_title" => $title )
			);

			if ( !$result ) {
				return true;
			} else {
				$title = htmlentities( $title );
				$url = wfMsgExt( 'rped-wikipedia-url','parsemag') . $title;

				// The page that we'll link to
				$text = '<a href="' . $url . '">' . $text . '</a>';

				if ( $wgRemoteStyle != '' ) {
					$customAttribs['style'] = $wgRemoteStyle;
				}
			}
		}
		return true;
	}
}
