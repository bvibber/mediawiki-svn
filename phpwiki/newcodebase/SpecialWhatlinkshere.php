<?

function wfSpecialWhatlinkshere()
{
	global $wgUser, $wgOut, $target;
	$fname = "wfSpecialWhatlinkshere";

	if ( "" == $target ) {
		$wgOut->errorpage( "notargettitle", "notargettext" );
		return;
	}
	$nt = Title::newFromURL( wfCleanQueryVar( $target ) );
	$wgOut->setPagetitle( $nt->getPrefixedText() );
	$wgOut->setSubtitle( wfMsg( "linklistsub" ) );

	$id = $nt->getArticleID();
	$sk = $wgUser->getSkin();

	if ( 0 == $id ) {
		$sql = "SELECT bl_from FROM brokenlinks WHERE bl_to='" .
		  wfStrencode( $nt->getPrefixedDBkey() ) . "'";
		$res = wfQuery( $sql, $fname );

		if ( 0 == wfNumRows( $res ) ) {
			$wgOut->addHTML( wfMsg( "nolinkshere" ) );
		} else {
			$wgOut->addHTML( wfMsg( "linkshere" ) );
			$wgOut->addHTML( "\n<ul>" );

			while ( $row = wfFetchObject( $res ) ) {
				$n = Article::nameOf( $row->bl_from );
				$link = $sk->makeKnownLink( $n, "" );
				$wgOut->addHTML( "<li>{$link}</li>\n" );
			}
			$wgOut->addHTML( "</ul>\n" );
			wfFreeResult( $res );
		}
	} else {
		$sql = "SELECT l_from FROM links WHERE l_to={$id}";
		$res = wfQuery( $sql, $fname );

		if ( 0 == wfNumRows( $res ) ) {
			$wgOut->addHTML( wfMsg( "nolinkshere" ) );
		} else {
			$wgOut->addHTML( wfMsg( "linkshere" ) );
			$wgOut->addHTML( "\n<ul>" );

			while ( $row = wfFetchObject( $res ) ) {
				$link = $sk->makeKnownLink( $row->l_from, "" );
				$wgOut->addHTML( "<li>{$link}</li>\n" );
			}
			$wgOut->addHTML( "</ul>\n" );
			wfFreeResult( $res );
		}
	}
}

?>
