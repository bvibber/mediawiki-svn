<?
# See skin.doc

class SkinStarTrek extends Skin {

	function initPage()
	{
		global $wgOut, $wgStyleSheetPath;

		$wgOut->addLink( "stylesheet", "",
		  "$wgStyleSheetPath/startrek.css" );
	}
	# Just inherit everything else
}

?>
