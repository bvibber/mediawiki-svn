<?php
include("Setup.php");
header("Content-type: text/xml; charset=utf-8");
echo "<?xml version=\"1.0\"?>"
?>

<rdf:RDF
xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
xmlns="http://my.netscape.com/rdf/simple/0.9/">

<channel>
<title><?php echo iconv($wgInputEncoding, "utf-8", wfMsg("sitetitle") ) ?></title>
<link><?php echo $wgServer ?></link>
<description><?php echo iconv($wgInputEncoding, "utf-8", wfMsg("sitesubtitle") ) ?></description>
</channel>

<?php
#<image>
#<title>Wikipedia</title>
#<url>...</url>
#<link>http://wikipedia.org/</link>
#</image>

if($style == 'new') {
	# 10 newest articles
$sql = "SELECT cur_title FROM cur
WHERE cur_is_new=1 AND cur_namespace=0 AND cur_is_redirect=0
AND LENGTH(cur_text) > 75
ORDER BY cur_timestamp DESC LIMIT 10";
} else {
	# 10 most recently edit articles that aren't frickin tiny
$sql = "SELECT cur_title FROM cur
WHERE cur_namespace=0 AND cur_is_redirect=0 AND cur_minor_edit=0
AND LENGTH(cur_text) > 150
ORDER BY cur_timestamp DESC LIMIT 10";
}
$res = wfQuery( $sql );

while( $row = wfFetchObject( $res ) ) {
	$title = htmlspecialchars(
		iconv($wgInputEncoding, "utf-8",
		str_replace( "_", " ", $row->cur_title ) ) );
	$url = wfLocalUrl( wfUrlencode( $row->cur_title ) );
	echo "
<item>
<title>{$title}</title>
<link>{$url}</link>
</item>
";
}

#<textinput>
#<title>Search Wikipedia</title>
#<description>Search Wikipedia articles</description>
#<name>query</name>
#<link>http://www.wikipedia.org/w/wiki.phtml?search=</link>
#</textinput>
?>

</rdf:RDF>