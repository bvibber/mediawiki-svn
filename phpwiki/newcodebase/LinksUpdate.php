<?
# See deferred.doc

class LinksUpdate {

	/* private */ var $mId, $mTitle;

	function LinksUpdate( $id, $title )
	{
		$this->mId = $id;
		$this->mTitle = $title;
	}

	function doUpdate()
	{
		global $wgLinkCache;
		$fname = "LinksUpdate::doUpdate";
		$t = wfStrencode( $this->mTitle );

		$sql = "DELETE FROM links WHERE l_from='{$t}'";
		wfQuery( $sql, $fname );

		$a = $wgLinkCache->getGoodLinks();
		$sql = "";
		if ( 0 != count( $a ) ) {
			$sql = "INSERT INTO links (l_from,l_to) VALUES ";
			$first = true;
			foreach( $a as $lt => $lid ) {
				if ( ! $first ) { $sql .= ","; }
				$first = false;

				$sql .= "('{$t}',{$lid})";
			}
		}
		if ( "" != $sql ) { wfQuery( $sql, $fname ); }

		$sql = "DELETE FROM brokenlinks WHERE bl_from={$this->mId}";
		wfQuery( $sql, $fname );

		$a = $wgLinkCache->getBadLinks();
		$sql = "";
		if ( 0 != count ( $a ) ) {
			$sql = "INSERT INTO brokenlinks (bl_from,bl_to) VALUES ";
			$first = true;
			foreach( $a as $blt ) {
				$blt = wfStrencode( $blt );
				if ( ! $first ) { $sql .= ","; }
				$first = false;

				$sql .= "({$this->mId},'{$blt}')";
			}
		}
		if ( "" != $sql ) { wfQuery( $sql, $fname ); }

		$sql = "DELETE FROM imagelinks WHERE il_from='{$t}'";
		wfQuery( $sql, $fname );

		$a = $wgLinkCache->getImageLinks();
		$sql = "";
		if ( 0 != count ( $a ) ) {
			$sql = "INSERT INTO imagelinks (il_from,il_to) VALUES ";
			$first = true;
			foreach( $a as $iname => $val ) {
				$iname = wfStrencode( $iname );
				if ( ! $first ) { $sql .= ","; }
				$first = false;

				$sql .= "('{$t}','{$iname}')";
			}
		}
		if ( "" != $sql ) { wfQuery( $sql, $fname ); }

		$sql = "SELECT bl_from FROM brokenlinks WHERE bl_to='{$t}'";
		$res = wfQuery( $sql, $fname );
		if ( 0 == wfNumRows( $res ) ) { return; }

		$sql = "INSERT INTO links (l_from,l_to) VALUES ";
		$first = true;
		while ( $row = wfFetchObject( $res ) ) {
			if ( ! $first ) { $sql .= ","; }
			$first = false;
			$nl = wfStrencode( Article::nameOf( $row->bl_from ) );

			$sql .= "('{$nl}',{$this->mId})";
		}
		wfQuery( $sql, $fname );

		$sql = "DELETE FROM brokenlinks WHERE bl_to='{$t}'";
		wfQuery( $sql, $fname );
	}
}

?>
