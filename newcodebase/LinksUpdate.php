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

		$conn = wfGetDB();
		$sql = "DELETE FROM links WHERE l_from='" .
		  wfStrencode( $this->mTitle ) . "'";
		wfQuery( $sql, $conn, $fname );

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
			$res2 = wfQuery( $sql, $conn, $fname );
		}

		$conn = wfGetDB();
		$sql = "DELETE FROM brokenlinks WHERE bl_from={$this->mId}";
		wfQuery( $sql, $conn, $fname );

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
			$res2 = wfQuery( $sql, $conn, $fname );
		}

		$conn = wfGetDB();
		$sql = "DELETE FROM imagelinks WHERE il_from='" .
		  wfStrencode( $this->mTitle ) . "'";
		wfQuery( $sql, $conn, $fname );

		$a = $wgLinkCache->getImageLinks();
		$sql = "";
		if ( 0 != count ( $a ) ) {
			$sql = "INSERT INTO imagelinks (il_from,il_to) VALUES ";
			$first = true;
			foreach( $a as $iname => $val ) {
				if ( ! $first ) { $sql .= ","; }
				$first = false;

				$sql .= "('" . wfStrencode( $this->mTitle ) . "','" .
				  wfStrencode( $iname ) . "')";
			}
		}
		if ( "" != $sql ) {
			$conn = wfGetDB();
			$res2 = wfQuery( $sql, $conn, $fname );
		}
	}
}

?>
