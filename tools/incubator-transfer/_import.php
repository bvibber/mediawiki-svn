<?php

# Private script called from incubator-transfer.php
$maintenance = '/home/wikipedia/common/php/maintenance';
require_once( "$maintenance/commandLine.inc" );


class IncubatorImport {
	var $pageList = array();

	function import( $prefix, $moveOnly ) {
		global $wgUser;
		
		$wgUser = User::newFromName( 'Incubator import' );
		if ( !$wgUser->getID() ) {
			$wgUser->addToDatabase();
		}
		
		if ( $moveOnly ) {
			$this->pageList = array_map( 'trim', file( 'transfer.lst' ) );
		} else {
			print "Importing...\n";

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
			
			foreach ( $this->pageList as $titleText ) {
				$title = Title::newFromText( $titleText );
				$article = new Article( $title );
				$revision = Revision::newFromTitle( $title );
				$text = false;
				if ( $revision ) {
					$text = $revision->getText();
				}
				# Remove prefixes
				$newText = preg_replace( $prefixRegex, '[[$1', $text );
				# Fix unnecessary piped links
				if ( $newText != $text ) {
					$newText = preg_replace( '/\[\[\s*(.*?)\s*\|\s*\1\s*]]/', '[[$1]]', $newText );
				}

				if ( $newText != $text ) {
					$article->doEdit( $newText, 'Fixing internal links' );
				}
				wfWaitForSlaves( 5 );
			}
		}

		# Move all the pages, removing the prefix
		print "Moving pages...\n";
		$errors = '';
		foreach ( $this->pageList as $sourceText ) {
			$source = Title::newFromText( $sourceText );
			if ( $source->getDBkey() == $prefix ) {
				$dest = Title::newMainPage();
			} else {
				$destDBkey = str_replace( "$prefix/", '', $source->getDBkey() );
				$dest = Title::makeTitleSafe( $source->getNamespace(), $destDBkey );
			}
			if ( !$dest ) {
				$errors .= "* Unable to move \"$sourceText\" to \"$destDBkey\": invalid title\n";
			} elseif ( $dest->getArticleID() ) {
				$errors .= $this->moveError( 'destination exists', $source, $dest );
			} else {
				try {
					$err = $source->moveTo( $dest, false );
				} catch ( Exception $e ) {
					$errors .= $this->moveError( "exception encountered: " . get_class( $e ) . ': ' . $e->getMessage(), $source, $dest );
					$err = true;
				}
				if ( $err !== true ) {
					$errors .= $this->moveError( wfMsg( $err ), $source, $dest );
				}
			}
			wfWaitForSlaves( 5 );
		}

		# Save the status page
		print "Saving status page...\n";
		$title = $wgUser->getUserPage();
		$article = new Article( $title );
		$text = "This script is operated by Tim Starling.\n\n";
		if ( $errors ) {
			$text .= "The following errors were encountered during incubator import:\n\n$errors";
		} else {
			$text .= "Incubator import was successful.";
		}
		$article->doEdit( $text, '' );
		print "Import done.\n";
	}

	function onPage( $page ) {
		wfWaitForSlaves( 5 );
		$this->pageList[] = $page;
	}

	function moveError( $err, $source, $dest ) {
		return '* Unable to move [[' . $source->getPrefixedText() . ']] to [[' . $dest->getPrefixedText() . 
			"]]: $err.\n";
	}
}

$importer = new IncubatorImport;
$importer->import( ucfirst( $args[0] ), isset( $options['move-only'] ) );

?>
