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

		$conn = wfGetDB();
		$sql = "DELETE FROM links WHERE l_from='{$this->mTitle}'";
		wfDebug( "LU:1: $sql\n" );
		mysql_query( $sql, $conn );

		$a = $wgLinkCache->getGoodLinks();
		$sql = "";
		if ( 0 != count( $a ) ) {
			$sql = "INSERT INTO links (l_from,l_to) VALUES ";
			$first = true;
			foreach( $a as $lt => $lid ) {
				if ( ! $first ) { $sql .= ","; }
				$first = false;

				$sql .= "('" . wfStrencode( $this->mTitle ) . "',$lid)";
			}
		}
		if ( "" != $sql ) {
			$conn = wfGetDB();
			wfDebug( "LU:2: $sql\n" );
			$res2 = mysql_query( $sql, $conn );
		}
		$conn = wfGetDB();
		$sql = "DELETE FROM brokenlinks WHERE bl_from={$this->mId}";
		wfDebug( "LU:3: $sql\n" );
		mysql_query( $sql, $conn );

		$a = $wgLinkCache->getBadLinks();
		$sql = "";
		if ( 0 != count ( $a ) ) {
			$sql = "INSERT INTO brokenlinks (bl_from,bl_to) VALUES ";
			$first = true;
			foreach( $a as $blt ) {
				if ( ! $first ) { $sql .= ","; }
				$first = false;

				$sql .= "($this->mId,'" . wfStrencode( $blt ) . "')";
			}
		}
		if ( "" != $sql ) {
			$conn = wfGetDB();
			wfDebug( "LU:4: $sql\n" );
			$res2 = mysql_query( $sql, $conn );
		}
	}
}

?>
