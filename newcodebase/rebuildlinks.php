<?

# Database conversion (from May 2002 format).  Assumes that
# the "buildtables.sql" script has been run to create the new
# empty tables, and that the old tables have been read in from
# a database dump, renamed "old_*".

include_once( "Setup.php" );
set_time_limit(0);

$wgDBname			= "wikidb";
$wgDBuser			= "wikiadmin";
$wgDBpassword		= "adminpass";
$wgUploadDirectory	= "/usr/local/apache/htdocs/upload";

rebuildLinkTables();

print "Done.\n";
exit();

########## End of script, beginning of functions.

# Empty and rebuild the "links" and "brokenlinks" tables.
# This can be done at any time for the new database, and
# probably should be done periodically (you should lock
# the wiki while it is running as well).
#
function rebuildLinkTables()
{
	global $wgLinkCache;
	$count = 0;

	print "Rebuilding link tables.\n";

	$sql = "LOCK TABLES cur READ, links WRITE, " .
	  "brokenlinks WRITE, imagelinks WRITE";
	wfQuery( $sql );

	$sql = "DELETE FROM links";
	wfQuery( $sql );

	$sql = "DELETE FROM brokenlinks";
	wfQuery( $sql );

	$sql = "DELETE FROM imagelinks";
	wfQuery( $sql );

	$sql = "SELECT cur_id,cur_namespace,cur_title,cur_text FROM cur";
	$res = wfQuery( $sql );

	while ( $row = mysql_fetch_object( $res ) ) {
		$id = $row->cur_id;
		$ns = Namespace::getName( $row->cur_namespace );
		if ( "" == $ns ) {
			$title = $row->cur_title;
		} else {
			$title = "$ns:{$row->cur_title}";
		}
		$text = $row->cur_text;

		$wgLinkCache = new LinkCache();
		getInternalLinks( $title, $text );

		$sql = "";
		$a = $wgLinkCache->getImageLinks();
		if ( 0 != count ( $a ) ) {
			$sql = "INSERT INTO imagelinks (il_from,il_to) VALUES ";
			$first = true;
			foreach( $a as $iname => $val ) {
				if ( ! $first ) { $sql .= ","; }
				$first = false;

				$sql .= "('" . wfStrencode( $title ) . "','" .
				  wfStrencode( $iname ) . "')";
			}
		}
		if ( "" != $sql ) { wfQuery( $sql ); }

		$sql = "";
		$a = $wgLinkCache->getGoodLinks();
		if ( 0 != count( $a ) ) {
			$sql = "INSERT INTO links (l_from,l_to) VALUES ";
			$first = true;
			foreach( $a as $lt => $lid ) {
				if ( ! $first ) { $sql .= ","; }
				$first = false;

				$sql .= "('" . wfStrencode( $title ) . "',$lid)";
			}
		}
		if ( "" != $sql ) { wfQuery( $sql ); }

		$sql = "";
		$a = $wgLinkCache->getBadLinks();
		if ( 0 != count ( $a ) ) {
			$sql = "INSERT INTO brokenlinks (bl_from,bl_to) VALUES ";
			$first = true;
			foreach( $a as $blt ) {
				if ( ! $first ) { $sql .= ","; }
				$first = false;

				$sql .= "($id,'" . wfStrencode( $blt ) . "')";
			}
		}
		if ( "" != $sql ) { wfQuery( $sql ); }

		if ( ( ++$count % 1000 ) == 0 ) {
			print "$count articles processed.\n";
		}
	}
	print "$count articles processed.\n";
	mysql_free_result( $res );

	$sql = "UNLOCK TABLES";
	wfQuery( $sql );
}

function getInternalLinks ( $title, $text )
{
	global $wgLinkCache, $wgLang;

	$text = preg_replace( "/<\\s*nowiki\\s*>.*<\\/\\s*nowiki\\s*>/i",
	  "", $text );

	$a = explode( "[[", " " . $text );
	$s = array_shift( $a );
	$s = substr( $s, 1 );

	$tc = Title::legalChars();
	foreach ( $a as $line ) {
		$e1 = "/^([{$tc}]+)\\|([^]]+)]]/sD";
		$e2 = "/^([{$tc}]+)]]/sD";

		if ( preg_match( $e1, $line, $m )
		  || preg_match( $e2, $line, $m ) ) {
			$link = $m[1];
		} else {
			continue;
		}
		if ( preg_match( "/^([a-z]+):(.*)$$/", $link,  $m ) ) {
			$pre = strtolower( $m[1] );
			$suf = $m[2];
			if ( "image" == $pre || "media" == $pre ) {
				$nt = Title::newFromText( $suf );
				$t = $nt->getDBkey();
				$wgLinkCache->addImageLink( $t );
				continue;
			} else {
				$l = $wgLang->getLanguageName( $pre );
				if ( "" != $l ) {
					continue; # Language link
				}
			}
		}
		$nt = Title::newFromText( $link );
		$ft = $nt->getPrefixedDBkey();
		$id = getArticleID( $nt->getNamespace(), $nt->getDBkey(), $ft );

		if ( 0 == $id ) { $wgLinkCache->addBadLink( $ft ); }
		else { $wgLinkCache->addGoodLink( $id, $ft ); }
	}
}

# To rebuild link tables faster, we want to cache article ID
# lookups across all pages, not just per-page as in live code.
#

function getArticleID( $namespace, $title, $fulltitle )
{
	global $wgFullCache;

	if ( ! array_key_exists( $fulltitle, $wgFullCache ) ) {
		$sql = "SELECT cur_id FROM cur WHERE (cur_namespace=" .
		  "{$namespace} AND cur_title='" . wfStrencode( $title ) . "')";
		$res = wfQuery( $sql );

		if ( 0 == wfNumRows( $res ) ) { $id = 0; }
		else {
			$s = wfFetchObject( $res );
			$id = $s->cur_id;
		}
		$wgFullCache[$fulltitle] = $id;
	}
	return $wgFullCache[$fulltitle];
}

?>
