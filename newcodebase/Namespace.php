<?
# This is a utility class with only static functions
# for dealing with namespaces. Everything is hardcoded,
# and will have to be internationalized.
#
# Note "Special" is treated, well, specially, and doesn't
# really fit into the system--it has to be tested for
# independently.
#
$wgNamespaceNamesEn = array(
	0 => "", 1 => "Talk", 2 => "User", 3 => "User_talk",
	4 => "Wikipedia", 5 => "Wikipedia_talk"
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

	function isTalk( $index )
	{
		if ( 1 == $index || 3 == $index || 5 == $index ) {
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
