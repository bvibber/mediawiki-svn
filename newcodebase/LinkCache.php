<?
# Cache for article titles and ids linked from one source

class LinkCache {

	/* private */ var $mGoodLinks, $mBadLinks, $mActive;
	/* private */ var $mImageLinks;

	function LinkCache()
	{
		$this->mActive = true;
		$this->mGoodLinks = array();
		$this->mBadLinks = array();
		$this->mImageLinks = array();
	}

	function getGoodLinkID( $title )
	{
		if ( array_key_exists( $title, $this->mGoodLinks ) ) {
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
		if ( $this->mActive ) {
			$this->mGoodLinks[$title] = $id;
		}
	}

	function addBadLink( $title )
	{
		if ( $this->mActive && ( ! $this->isBadLink( $title ) ) ) {
			array_push( $this->mBadLinks, $title );
		}
	}

	function addImageLink( $title )
	{
		if ( $this->mActive ) {
			$this->mImageLinks[$title] = 1;
		}
	}

	function clearBadLink( $title )
	{
		$index = array_search( $title, $this->mBadLinks );
		if ( isset( $index ) ) {
			unset( $this->mBadLinks[$index] );
		}
	}

	function suspend() { $this->mActive = false; }
	function resume() { $this->mActive = true; }
	function getGoodLinks() { return $this->mGoodLinks; }
	function getBadLinks() { return $this->mBadLinks; }
	function getImageLinks() { return $this->mImageLinks; }

	function addLink( $title )
	{
		if ( $this->isBadLink( $title ) ) { return 0; }
		$id = $this->getGoodLinkID( $title );
		if ( 0 != $id ) { return $id; }

		$nt = Title::newFromDBkey( $title );
		$ns = $nt->getNamespace();
		$t = $nt->getDBkey();

		$conn = wfGetDB();
		$sql = "SELECT cur_id FROM cur WHERE (cur_namespace=" .
		  "{$ns} AND cur_title='" . wfStrencode( $t ) . "')";
		$res = wfQuery( $sql, $conn, "LinkCache::addLink" );

		if ( 0 == mysql_num_rows( $res ) ) {
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
