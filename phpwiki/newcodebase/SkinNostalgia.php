<?
# See skin.doc

class SkinNostalgia extends Skin {

	function initPage()
	{
		global $wgOut, $wgStyleSheetPath;

		$wgOut->addLink( "stylesheet", "",
		  "$wgStyleSheetPath/nostalgia.css" );
	}
	# Just inherit everything else
}

?>
