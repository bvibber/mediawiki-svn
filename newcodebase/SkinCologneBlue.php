<?
# See skin.doc

class SkinCologneBlue extends Skin {

	function initPage()
	{
		global $wgOut, $wgStyleSheetPath;

		$wgOut->addLink( "stylesheet", "",
		  "$wgStyleSheetPath/cologneblue.css" );
	}

}

?>
