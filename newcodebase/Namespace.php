<?
# This is a utility class with only static functions
# for dealing with namespaces.  The actual text of the
# names is in Language.php, but the namespaces have
# special behaviors based on their index--those are
# all hard-coded here.
#

$wgNamespaceNamesEn = array(
	0 => "", 1 => "Talk", 2 => "User", 3 => "User_talk",
	4 => "Wikipedia", 5 => "Wikipedia_talk", 6 => "Image",
	7 => "Image_talk"
);

class Namespace {

	function getName( $index )
	{
		global $wgNamespaceNamesEn;
		if ( -1 == $index ) { return "Special"; }
		else { return $wgNamespaceNamesEn[$index]; }
	}

	function getIndex( $name )
	{
		global $wgNamespaceNamesEn;

		foreach ( $wgNamespaceNamesEn as $i => $n ) {
			if ( 0 == strcmp( $n, $name ) ) {
				return $i;
			}
		}
		return -1;
	}

	function getSpecialName() { return Namespace::getName( -1 ); }
	function getUserIndex() { return 2; }
	function getUserName() { return Namespace::getName( 2 ); }
	function getWikipediaIndex() { return 4; }
	function getWikipediaName() { return Namespace::getName( 4 ); }
	function getImageIndex() { return 6; }
	function getImageName() { return Namespace::getName( 6 ); }

	function isMovable( $index )
	{
		if ( $index < 0 || $index > 5 ) { return false; }
		return true;
	}

	function isTalk( $index )
	{
		if ( 1 == $index || 3 == $index || 5 == $index || 7 == $index ) {
			return true;
		}
		return false;
	}

	# Get the talk namespace corresponding to the given index
	#
	function getTalk( $index )
	{
		if ( Namespace::isTalk( $index ) ) {
			return $index;
		} else {
			return $index + 1;
		}
	}

	function getSubject( $index )
	{
		if ( Namespace::isTalk( $index ) ) {
			return $index - 1;
		} else {
			return $index;
		}
	}
}

?>
