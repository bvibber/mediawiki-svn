<?php

# Private script called from incubator-transfer.php
$maintenance = '/home/wikipedia/common/php/maintenance';
require_once( "$maintenance/commandLine.inc" );


class IncubatorImport {
	var $pageList = array();

	function import( $prefix ) {
		global $wgUser;
		
		print "Importing...\n";

		$wgUser = User::newFromName( 'Incubator import' );
		if ( !$wgUser->getID() == 0 ) {
			$wgUser->addToDatabase();
		}

		$file = fopen( 'transfer.xml', 'r' );
		if ( !$file ) {
			print "transfer.xml is missing, aborting\n";
			return;
		}
		
		$source = new ImportStreamSource( $file );
		$importer = new WikiImporter( $source );
		$importer->setPageCallback( array( &$this, 'onPage' ) );
		$importer->doImport();

		# Fix internal links
		print "Fixing internal links...\n";

		$firstChar = $prefix[0];
		$lc = preg_quote( strtolower( $firstChar ), '/' );
		$uc = preg_quote( strtoupper( $firstChar ), '/' );
		$prefixRemainder = preg_quote( substr( $prefix, 1 ), '/' );
		$prefixRegex = "/\[\[(?:$lc|$uc)$prefixRemainder\/([^\]|]*)/";
		
		foreach ( $this->pageList as $title ) {
			$article = new Article( $title );
			$revision = Revision::newFromTitle( $title );
			$text = false;
			if ( $revision ) {
				$text = $revision->getText();
			}
			# Remove prefixes
			$newText = preg_replace( $prefixRegex, '[[$1', $text );
			# Fix unnecessary piped links
			$newText = preg_replace( '/\[\[\s*(.*?)\s*\|\s*\1\s*]]/', '[[$1]]', $newText );

			if ( $newText != $text ) {
				$article->doEdit( $newText, 'Fixing internal links' );
			}
		}

		# Move all the pages, removing the prefix
		print "Moving pages...\n";
		$errors = '';
		foreach ( $this->pageList as $source ) {
			if ( $source->getDBkey() == $prefix ) {
				$dest = Title::newMainPage();
			} else {
				$destDBkey = str_replace( "$prefix/", '', $source->getDBkey() );
				$dest = Title::makeTitleSafe( $source->getNamespace(), $destDBkey );
			}
			if ( !$dest ) {
				$errors .= $this->moveError( 'invalid title', $source, $dest );
			} elseif ( $dest->getArticleID() ) {
				$errors .= $this->moveError( 'destination exists', $source, $dest );
			} else {
				$err = $source->moveTo( $dest, false );
				if ( $err !== true ) {
					$errors .= $this->moveError( wfMsg( $err ), $source, $dest );
				}
			}
		}

		# Save the status page
		if ( $errors ) {
			$title = Title::makeTitleSafe( NS_PROJECT, 'Incubator_import_status' );
			$article = new Article( $title );
			$article->doEdit( "The following errors were encountered during incubator import:\n\n$errors", '' );
		}
	}

	function onPage( $page ) {
		$this->pageList[] = $page;
	}

	function moveError( $err, $source, $dest ) {
		return '* Unable to move [[' . $source->getPrefixedText() . ']] to [[' . $dest->getPrefixedText() . 
			"]]: $err.\n";
	}
}

$importer = new IncubatorImport;
$importer->import( ucfirst( $args[0] ) );

?>
