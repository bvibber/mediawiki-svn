<?php
class RefSearch extends SpecialPage {
	function __construct() {
		parent::__construct( 'RefSearch','edit',true,false,'default',false );
		wfLoadExtensionMessages('RefHelper');
	}
 
	function execute( $par ) {
		global $wgRequest, $wgOut;
 
		$this->setHeaders();
 
		# Get request data from, e.g.
		$action = $wgRequest->getText('action');

		$query = htmlentities($wgRequest->getText('query'));
		$query = str_replace(" ","+",$query);
		$reqfilled = strlen($query);
 
		# Output
		$wgOut->addHTML( "<fieldset>\n" );
	$wgOut->addHTML( "<legend>Search PubMed for References</legend>\n");
		$wgOut->addHTML( '<form enctype="multipart/form-data" method="post" action="/w/index.php?title=Special:RefSearch&amp;action=submit" id="mw-create-ref-form"><input name="action" value="submit" type="hidden"/><table id="mw-import-table"><tbody>'."\n");

		$wgOut->addHTML( "<tr>\n");
		$wgOut->addHTML( "\t<td class='mw-input'><input name='query' size='50' value='$query' type='text' id='inp_articletitle'/></td>\n");
		$wgOut->addHTML( "<td class=\"mw-submit\"><input value=\"Search\" type=\"submit\"></td>\n");
		$wgOut->addHTML( "\n");

		$wgOut->addHTML( '				</tr></tbody></table><input name="editToken" value="014149335353561d3d9dc6e1273e037a+\" type="hidden"></form>'."\n" );

		if( $action=="submit" || $reqfilled ) {
			$ch = curl_init("http://eutils.ncbi.nlm.nih.gov/entrez/eutils/esearch.fcgi?term=$query&tool=mediawiki_refhelper");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$result = curl_exec($ch);
			curl_close($ch);

			$num = preg_match_all("|<Id>(\d+)</Id>|", $result, $matches );
			$wgOut->addHTML("<table>\n");
			for( $i = 0; $i < $num; $i++ ) {
				$pmid = $matches[1][$i];
				$result = self::query_pmid($pmid);

				$author = array_shift($result["authors"]);
				if( isset($author) ) $author = $author[1];
				$title = $result["title"];
				$query = $result["query_url"];
				$year = $result["year"];
				if( count($result["AU"]) > 1 )	$etal = " et al.";
				else								$etal = "";
					

				$wgOut->addHTML("<tr>\n");
				$wgOut->addHTML("\t<td>\n");
				$wgOut->addHTML("$author $etal ($year) \"$title\"");
				$wgOut->addHTML("\t</td>\n");
				$wgOut->addHTML("\t<td>\n");
				$wgOut->addHTML( "<form enctype='multipart/form-data' method='post' action='/w/index.php?title=Special:RefHelper&amp;action=submit&amp;pmid=$pmid' id='mw-create-ref-form$i'><input value=\"Create\" type=\"submit\"/></form> <a href='$query'>(debug)</a>");
				$wgOut->addHTML("\t</td>\n");
				$wgOut->addHTML("</tr>\n");
			}
			$wgOut->addHTML("</table>\n");
		}
		$wgOut->addHTML( "</fieldset>\n");
	}
	static function parse_medline( $text, $field ) {
		$field = strtoupper($field);
		$num = preg_match_all("|\n$field\s*- (.*)(?>\n      (.*))*|", $text, $matches, PREG_SET_ORDER );
		$ret = array();
		for( $i = 0; $i < $num; $i++ )
		{
			array_shift($matches[$i]);
			$ret[] = implode( " ", $matches[$i] );
		}
		return $ret;
	}
	static function query_pmid( $pmid ) {
		$ret = array();
		$ret["query_url"]	= "http://eutils.ncbi.nlm.nih.gov/entrez/eutils/efetch.fcgi?db=pubmed&report=medline&mode=text&id=$pmid&email=jonwilliford@gmail.com&tool=mediawiki_refhelper";
		$ch = curl_init( $ret["query_url"] );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);
		curl_close($ch);

		$ret["title"] 	= array_shift( self::parse_medline( $result, "TI" ));
		$ret["journal"]	= array_shift( self::parse_medline( $result, "TA" ));
		$ret["year"] 	= substr( array_shift( self::parse_medline( $result, "DP" )), 0, 4 );
		$ret["volume"] 	= array_shift( self::parse_medline( $result, "VI" ));
		$ret["issue"] 	= array_shift( self::parse_medline( $result, "IP" ));
		$ret["pages"] 	= array_shift( self::parse_medline( $result, "PG" ));

		$ret["firstlasts"] = self::parse_medline( $result, "FAU" );
		$ret["AU"] = self::parse_medline( $result, "AU" );

		$ret["authors"] = array();
		/*if( isset( $ret["firstlasts"] ) )
		{
			for( $i = 0; $i < count( $ret["firstlasts"] ); $i++ ) {
	
				$auth = $ret["firstlasts"][$i];
	
				if( preg_match("|(.+), (.+)|", $auth, $matches ) ) {
	
					// index 0 for first name, index 1 for surname
					$ret["authors"][$i][1] = $matches[1];
					$ret["authors"][$i][0] = $matches[2];
				}
				else {
					$ret["authors"][$i] = array(0=>"",1=>$auth);
				}
			}
		}
		else*/
		{
			for( $i = 0; $i < count( $ret["AU"] ); $i++ ) {
	
				$auth = $ret["AU"][$i];
	
				if( preg_match("|^(.+) (.+)$|", $auth, $matches ) ) {
	
					// index 0 for first name, index 1 for surname
					$ret["authors"][$i][1] = $matches[1];
					$ret["authors"][$i][0] = $matches[2];
				}
				else {
					$ret["authors"][$i] = array(0=>"",1=>$auth);
				}
			}
		}

		return $ret;
	}
}

