<?

# Database conversion (from May 2002 format).  Assumes that
# the "buildtables.sql" script has been run to create the new
# empty tables, and that the old tables have been read in from
# a database dump, renamed "old_*".

include_once( "Setup.php" );
set_time_limit(0);

$wgDBname			= "wikidb";
$wgDBuser			= "wikiadmin";
$wgDBpassword		= "oberon";
$wgImageDirectory	= "/rfs/backups/lee/wikiimages";
$wgUploadDirectory	= "/rfs/upload";

$wgFullCache		= array();

# convertUserTable();
# convertOldTable();
# convertCurTable();
# convertImageDirectories();

rebuildLinkTables();

print "Done.\n";
exit();

########## End of script, beginning of functions.

function convertUserTable()
{
	$count = 0;
	print "Converting USER table.\n";

	$sql = "LOCK TABLES old_user READ, user WRITE";
	$newres = wfQuery( $sql );

	$sql = "SELECT user_id,user_name,user_rights,user_password," .
	  "user_email,user_options,user_watch FROM old_user";
	$oldres = wfQuery( $sql );

	$sql = "DELETE FROM user";
	$newres = wfQuery( $sql );

	$sql = "";
	while ( $row = mysql_fetch_object( $oldres ) ) {
		if ( 0 == ( $count % 10 ) ) {
			if ( 0 != $count ) { $newres = wfQuery( $sql ); }

			$sql = "INSERT INTO user (user_id,user_name,user_rights," .
			  "user_password,user_newpassword,user_email,user_options," .
			  "user_watch) VALUES ";
		} else {
			$sql .= ",";
		}
		$ops = addslashes( fixUserOptions( $row->user_options ) );
		$name = addslashes( fixUserName( $row->user_name ) );
		$rights = addslashes( fixUserRights( $row->user_rights ) );
		$email = addslashes( $row->user_email );
		$pwd = addslashes( md5( $row->user_password ) );
		$watch = addslashes( $row->user_watch );

		if ( "" == $name ) continue; # Don't convert illegal names

		$sql .= "({$row->user_id},'{$name}','{$rights}','{$pwd}',''," .
		  "'{$email}','{$ops}','{$watch}')";

		if ( ( ++$count % 1000 ) == 0 ) {
			print "$count user records processed.\n";
		}
	}
	if ( $sql ) { $newres = wfQuery( $sql ); }

	print "$count user records processed.\n";
	mysql_free_result( $oldres );

	$sql = "UNLOCK TABLES";
	$newres = wfQuery( $sql );
}

# Convert May 2002 version of database into new format.
#
function convertCurTable()
{
	$count = 0;
	print "Converting CUR table.\n";

	$sql = "LOCK TABLES old_cur READ, cur WRITE";
	$newres = wfQuery( $sql );

	$sql = "SELECT cur_id,cur_title,cur_text,cur_comment,cur_user," .
	  "cur_timestamp,cur_minor_edit,cur_restrictions," .
	  "cur_counter,cur_ind_title,cur_user_text FROM old_cur";
	$oldres = wfQuery( $sql );

	$sql = "DELETE FROM cur";
	$newres = wfQuery( $sql );

	$sql = "";
	while ( $row = mysql_fetch_object( $oldres ) ) {
		$nt = Title::newFromDBkey( $row->cur_title );
		$title = addslashes( $nt->getDBkey() );
		$ns = $nt->getNamespace();
		$text = addslashes( convertMediaLinks( $row->cur_text ) );

		$ititle = addslashes( indexTitle( $nt->getText() ) );
		$itext = addslashes( indexText( $text, $ititle ) );

		$com = addslashes( $row->cur_comment );
		$cr = addslashes( $row->cur_restrictions );
		$cut = addslashes( $row->cur_user_text );
		if ( "" == $cut ) { $cut = "Unknown"; }

		if ( 2 == $row->cur_minor_edit ) { $isnew = 1; }
		else { $isnew = 0; }
		if ( 0 != $row->cur_minor_edit ) { $isme = 1; }
		else { $isme = 0; }

		# $counter = $row->cur_counter;
		# if ( ! $counter ) { $counter = 0; }

		if ( preg_match( "/^#redirect /i", $text ) ) {
			$redir = 1;
			$text = fixRedirect( $text );
		} else { $redir = 0; }

		$sql = "INSERT INTO cur (cur_id,cur_namespace," .
		  "cur_title,cur_text,cur_comment,cur_user," .
		  "cur_timestamp,cur_minor_edit,cur_is_new," .
		  "cur_restrictions,cur_counter,cur_ind_title," .
		  "cur_ind_text,cur_is_redirect,cur_user_text) VALUES ";
		$sql .= "({$row->cur_id},{$ns},'{$title}','{$text}'," .
		  "'{$com}',{$row->cur_user},'{$row->cur_timestamp}'," .
		  "{$isme},{$isnew},'{$cr}',0,'{$ititle}','{$itext}'," .
		  "{$redir},'{$cut}')";
		wfQuery( $sql );

		if ( ( ++$count % 1000 ) == 0 ) {
			print "$count article records processed.\n";
		}
	}
	print "$count article records processed.\n";
	mysql_free_result( $oldres );

	$sql = "UNLOCK TABLES";
	$newres = wfQuery( $sql );
}

# Convert May 2002 version of database into new format.
#
function convertOldTable()
{
	$count = 0;
	print "Converting OLD table.\n";

	$sql = "LOCK TABLES old_old READ, old WRITE";
	$newres = wfQuery( $sql );

	$sql = "SELECT old_id,old_title,old_text,old_comment,old_user," .
	  "old_timestamp,old_minor_edit,old_user_text FROM old_old";
	$oldres = wfQuery( $sql );

	$sql = "DELETE FROM old";
	$newres = wfQuery( $sql );

	while ( $row = mysql_fetch_object( $oldres ) ) {
		$nt = Title::newFromDBkey( $row->old_title );
		$title = addslashes( $nt->getDBkey() );
		$ns = $nt->getNamespace();
		$text = addslashes( convertMediaLinks( $row->old_text ) );

		$com = addslashes( $row->old_comment );
		$cut = addslashes( $row->old_user_text );
		if ( "" == $cut ) { $cut = "Unknown"; }

		if ( 0 != $row->old_minor_edit ) { $isme = 1; }
		else { $isme = 0; }

		if ( preg_match( "/^#redirect /i", $text ) ) {
			$redir = 1;
			$text = fixRedirect( $text );
		} else { $redir = 0; }

		$sql = "INSERT INTO old (old_id,old_namespace,old_title," .
		  "old_text,old_comment,old_user," .
		  "old_timestamp,old_minor_edit,old_user_text) VALUES ";
		$sql .= "({$row->old_id},{$ns},'{$title}','{$text}'," .
		  "'{$com}',{$row->old_user},'{$row->old_timestamp}'," .
		  "{$isme},'{$cut}')";
		wfQuery( $sql );

		if ( ( ++$count % 1000 ) == 0 ) {
			print "$count history records processed.\n";
		}
	}
	print "$count history records processed.\n";
	mysql_free_result( $oldres );

	$sql = "UNLOCK TABLES";
	$newres = wfQuery( $sql );
}

function convertImageDirectories()
{
	global $wgImageDirectory, $wgUploadDirectory;
	$count = 0;

	print "Moving image files.\n";
	$dir = opendir( $wgImageDirectory );
	while ( false !== ( $oname = readdir( $dir ) ) ) {
		if ( "." == $oname{0} ) continue;

		$nt = Title::newFromText( $oname );
		$nname = $nt->getDBkey();

		$exts = array( "png", "gif", "jpg", "jpeg" );
		$ext = strrchr( $nname, "." );
		if ( false === $ext ) { $ext = ""; }
		else { $ext = strtolower( substr( $ext, 1 ) ); }
		if ( ! in_array( $ext, $exts ) ) {
			print "Skipping \"{$oname}\"\n";
			continue;
		}
		$oldumask = umask(0);
		$hash = md5( $nname );
		$dest = $wgUploadDirectory . "/" . $hash{0};
		if ( ! is_dir( $dest ) ) { mkdir( $dest, 0777 ); }
		$dest .= "/" . substr( $hash, 0, 2 );
		if ( ! is_dir( $dest ) ) { mkdir( $dest, 0777 ); }
		umask( $oldumask );

		print "{$wgImageDirectory}/{$oname} => {$dest}/{$nname}\n";

		if ( copy( "{$wgImageDirectory}/{$oname}", "{$dest}/{$nname}" ) ) {
			++$count;

			$sql = "DELETE FROM image WHERE img_name='" .
			  addslashes( $nname ) . "'";
			$res = wfQuery( $sql );

			$sql = "INSERT INTO image (img_name,img_timestamp,img_user," .
			  "img_user_text,img_size,img_description) VALUES ('" .
			  addslashes( $nname ) . "','" .
			  date( "YmdHis" ) . "',0,'(Automated conversion)','" .
			  filesize( "{$dest}/{$nname}" ) . "','')";
			$res = wfQuery( $sql );
		}
	}
	print "{$count} images moved.\n";
}

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
# Utility functions for the above.
#
function convertMediaLinks( $text )
{
	$text = preg_replace(
	  "/(^|[^[])http:\/\/(www.|)wikipedia.com\/upload\/" .
	  "([a-zA-Z0-9_:.~\%\-]+)\.(png|PNG|jpg|JPG|jpeg|JPEG|gif|GIF)/",
	  "\\1[[image:\\3.\\4]]", $text );
	$text = preg_replace(
	  "/(^|[^[])http:\/\/(www.|)wikipedia.com\/images\/uploads\/" .
	  "([a-zA-Z0-9_:.~\%\-]+)\.(png|PNG|jpg|JPG|jpeg|JPEG|gif|GIF)/",
	  "\\1[[image:\\3.\\4]]", $text );

	$text = preg_replace(
	  "/(^|[^[])http:\/\/(www.|)wikipedia.com\/upload\/" .
	  "([a-zA-Z0-9_:.~\%\-]+)/", "\\1[[media:\\3]]", $text );
	$text = preg_replace(
	  "/(^|[^[])http:\/\/(www.|)wikipedia.com\/images\/uploads\/" .
	  "([a-zA-Z0-9_:.~\%\-]+)/", "\\1[[media:\\3]]", $text );

	return $text;
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

function fixRedirect( $text )
{
	$tc = "[&;%\\-,.\\(\\)' _0-9A-Za-z\\/:\\xA0-\\xff]";
	$re = "#redirect";
	if ( preg_match( "/^{$re}\\s*\\[{$tc}+\\]/i", $text ) ) {
		$text = preg_replace( "/^({$re})\\s*\\[\\s*({$tc}+)\\]/i",
		  "\\1 [[\\2]]", $text, 1 );
	} else if ( preg_match( "/^{$re}\\s+{$tc}+/i", $text ) ) {
		$text = preg_replace( "/^({$re})\\s+({$tc}+)/i",
		  "\\1 [[\\2]]", $text, 1 );
	}
	return $text;
}

function fixUserOptions( $in )
{
	$s = urldecode( $in );
	$a = explode( "\n", $s );

	foreach ( $a as $l ) {
		if ( preg_match( "/^([A-Za-z0-9_]+)=(.*)/", $l, $m ) ) {
			$ops[$m[1]] = $m[2];
		}
	}
	$nops = array();

	$q = strtolower( $ops["quickBar"] );
	if ( $q == "none" ) { $q = 0; }
	else if ( $q == "left" ) { $q = 1; }
	else { $q = 2; }
	$nops["quickbar"] = $q;

	if ( $ops["markupNewTopics"] == "inverse" ) {
		$nops["highlightbroken"] = 1;
	}
	$sk = substr( strtolower( $ops["skin"] ), 0, 4 );
	if ( "star" == $sk ) { $sk = 0; }
	else if ( "nost" == $sk ) { $sk = 1; }
	else if ( "colo" == $sk ) { $sk = 2; }
	else { $sk = 0; }
	$nops["skin"] = $sk;

	$u = strtolower( $ops["underlineLinks"] );
	if ( "yes" == $u || "on" == $u ) { $nops["underline"] = 1; }
	else { $nops["underline"] = 0; }

	$j = strtolower( $ops["justify"] );
	if ( "yes" == $j || "on" == $j ) { $nops["justify"] = 1; }
	$n = strtolower( $ops["numberHeadings"] );
	if ( "yes" == $n || "on" == $n ) { $nops["numberheadings"] = 1; }
	$h = strtolower( $ops["hideMinor"] );
	if ( "yes" == $h || "on" == $h ) { $nops["hideminor"] = 1; }
	$r = strtolower( $ops["rememberPassword"] );
	if ( "yes" == $r || "on" == $r ) { $nops["rememberpassword"] = 1; }
	$s = strtolower( $ops["showHover"] );
	if ( "yes" == $s || "on" == $s ) { $nops["hover"] = 1; }

	$c = $ops["cols"];
	if ( $c < 20 || c > 200 ) { $nops["cols"] = 80; }
	else { $nops["cols"] = $c; }
	$r = $ops["rows"];
	if ( $r < 5 || $r > 100 ) { $nops["rows"] = 20; }
	else { $nops["rows"] = $r; }
	$r = $ops["resultsPerPage"];
	if ( $r < 3 || $r > 500 ) { $nops["searchlimit"] = 20; }
	else { $nops["searchlimit"] = $r; }
	$r = $ops["viewRecentChanges"];
	if ( $r < 10 || $r > 1000 ) { $nops["rclimit"] = 50; }
	else { $nops["rclimit"] = $r; }
	$nops["rcdays"] = 3;

	$a = array();
	foreach ( $nops as $oname => $oval ) {
		array_push( $a, "$oname=$oval" );
	}
	$s = implode( "\n", $a );
	return $s;
}

function fixUserRights( $in )
{
	$a = explode( ",", $in );
	$b = array();
	foreach ( $a as $r ) {
		if ( "is_developer" == strtolower( trim( $r ) ) ) {
			array_push( $b, "developer" );
		} else if ( "is_sysop" == strtolower( trim( $r ) ) ) {
			array_push( $b, "sysop" );
		}
	}
	$out = implode( ",", $b );
	return $out;
}

function fixUserName( $in )
{
	$lc = "-,.()' _0-9A-Za-z\\/:\\xA0-\\xFF";
	$out = preg_replace( "/[^{$lc}]/", "", $in );
	$out = ucfirst( trim( str_replace( "_", " ", $out ) ) );
	return $out;
}

function indexTitle( $in )
{
	$lc = "A-Za-z_'0-9&#;\\x90-\\xFF\\-";
	$t = preg_replace( "/[^{$lc}]+/", " ", $in );
	$t = preg_replace( "/\\b[{$lc}][{$lc}]\\b/", " ", $t );
	$t = preg_replace( "/\\b[{$lc}]\\b/", " ", $t );
	$t = preg_replace( "/\\s+/", " ", $t );
	return $t;
}

function indexText( $text, $ititle )
{
	$lc = "A-Za-z_'0-9&#;\\x90-\\xFF\\-";
	$titlewords = array();
	$words = explode( " ", strtolower( trim( $ititle ) ) );
	foreach ( $words as $w ) { $titlewords[$w] = 1; }

	$text = preg_replace( "/<\\/?\\s*[A-Za-z][A-Za-z0-9]*\\s*([^>]*?)>/",
	  " ", strtolower( $this->mText ) ); # Strip HTML markup
	$text = preg_replace( "/(^|\\n)\\s*==\\s+([^\\n]+)\\s+==\\s/sD",
	  "\\2 \\2 \\2 ", $text ); # Emphasize headings

	# Strip external URLs
	$uc = "A-Za-z0-9_\\/:.,~%\\-+&;#?!=()@\\xA0-\\xFF";
	$protos = "http|https|ftp|mailto|news|gopher";
	$pat = "/(^|[^\\[])({$protos}):[{$uc}]+([^{$uc}]|$)/";
	$text = preg_replace( $pat, "\\1 \\3", $text );

	$p1 = "/(^|[^\\[])\\[({$protos}):[{$uc}]+]/";
	$p2 = "/(^|[^\\[])\\[({$protos}):[{$uc}]+\\s+([^\\]]+)]/";
	$text = preg_replace( $p1, "\\1 ", $text );
	$text = preg_replace( $p2, "\\1 \\3 ", $text );

	# Internal image links
	$pat2 = "/\\[\\[image:([{$uc}]+)\\.(png|jpg|jpeg)([^{$uc}])/i";
	$text = preg_replace( $pat2, " \\1 \\3", $text );

	$text = preg_replace( "/([^{$lc}])([{$lc}]+)]]([a-z]+)/",
	  "\\1\\2 \\2\\3", $text ); # Handle [[game]]s

	# Strip all remaining non-search characters
	$text = preg_replace( "/[^{$lc}]+/", " ", $text );

	# Handle 's, s'
	$text = preg_replace( "/([{$lc}]+)'s /", "\\1 \\1's ", $text );
	$text = preg_replace( "/([{$lc}]+)s' /", "\\1s ", $text );

	# Strip 1- and 2-letter words
	$text = preg_replace( "/(^|[^{$lc}])[{$lc}][{$lc}]([^{$lc}]|$)/",
	  "\\1 \\2", $text );
	$text = preg_replace( "/(^|[^{$lc}])[{$lc}]([^{$lc}]|$)/",
	  "\\1 \\2", $text );

	# Strip wiki '' and '''
	$text = preg_replace( "/''[']*/", " ", $text );

	# Remove title words: those have already been found
	foreach ( $titlewords as $w => $val ) {
		$text = str_replace( $w, " ", $text );
	}
	$text = preg_replace( "/\\s+/", " ", $text );
	return $text;
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
