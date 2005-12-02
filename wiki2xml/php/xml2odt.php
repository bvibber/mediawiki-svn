<?php

class element {
	var $name = '';
	var $attrs = array ();
	var $children = array ();
	function getSourceAttrs () {
		return "" ;
	}

	function sub_parse(& $tree, $tag = '', $attr = '') {
		$ret = '';

		$attr2 = $this->getSourceAttrs();
		if ($attr != '' AND $attr2 != '')
			$attr .= ' ';
		$attr .= $attr2;

		if ($tag != '') {
			$ret .= '<'.$tag;
			if ($attr != '')
				$ret .= ' '.$attr;
			$ret .= '>';
		}

		foreach ($this->children as $key => $child) {
			if (is_string($child)) {
				$ret .= $child;
			} elseif ($child->name != 'ATTRS') {
				$ret .= $child->makeXHTML($tree);
			}
		}
		if ($tag != '')
			$ret .= '</'.$tag.">\n";
		return $ret;
	}


	function parse ( &$tree ) {
		$ret = '';
		$n = $this->name; # Shortcut

		$ret .= $this->sub_parse ( $tree ) . "\n" ;
		return $ret ;
	}
}

include_once ( "xml2tree.php" ) ;


//_______________________________________________________________

$infile = "Biology.xml" ;
$xml = @file_get_contents ( $infile ) ;

print htmlentities ( $xml ) . "<hr>" ;

$x2t = new xml2php ;
$tree = $x2t->scanString ( $xml ) ;

$odt = new xml2odt ;
$odt->parse ( $tree ) ;

?>
