<?
# Cache for article titles and ids linked from one source

class LinkCache {

	/* private */ var $mGoodLinks, $mBadLinks;

	function LinkCache()
	{
		$this->mGoodLinks = $this->mBadLinks = array();
	}

	function getGoodLinkID( $title )
	{
		if ( key_exists( $title, $this->mGoodLinks ) ) {
			return $this->mGoodLinks[$title];
		} else {
			return 0;
		}
	}

	function isBadLink( $title )
	{
		return in_array( $title, $this->mBadLinks );
	}

	function addGoodLink( $id, $title )
	{
		$this->mGoodLinks[$title] = $id;
	}

	function addBadLink( $title )
	{
		if ( ! $this->isBadLink( $title ) ) {
			array_push( $this->mBadLinks, $title );
		}
	}

	function getGoodLinks() { return $this->mGoodLinks; }
	function getBadLinks() { return $this->mBadLinks; }

	function addLink( $title )
	{
		if ( $this->isBadLink( $title ) ) { return 0; }
		$id = $this->getGoodLinkID( $title );
		if ( 0 != $id ) { return $id; }

		$nt = Title::newFromDBKey( $title );
		$ns = $nt->getNamespace();
		$t = $nt->getDBKey();

		$conn = wfGetDB();
		$sql = "SELECT cur_id FROM cur WHERE (cur_namespace=" .
		  "{$ns} AND cur_title='{$t}')";
		# wfDebug( "Title: 1: $sql\n" );
		$res = mysql_query( $sql, $conn );

		if ( ( false === $res ) || 0 == mysql_num_rows( $res ) ) {
			$id = 0;
		} else {
			$s = mysql_fetch_object( $res );
			$id = $s->cur_id;
			mysql_free_result( $res );
		}
		if ( 0 == $id ) { $this->addBadLink( $title ); }
		else { $this->addGoodLink( $id, $title ); }
		return $id;
	}
}

?>
