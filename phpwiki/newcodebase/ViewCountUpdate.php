<?
# See deferred.doc

class ViewCountUpdate {

	var $mPageID, $mCount;

	function ViewCountUpdate( $pageid, $count )
	{
		$this->mPageID = $pageid;
		$this->mCount = $count;
	}

	function doUpdate()
	{
		wfSetSQL( "cur", "cur_counter", $this->mCount,
		  "cur_id={$this->mPageID}" );
	}
}

?>
