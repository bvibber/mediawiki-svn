<?

function wfSpecialRandompage()
{
	global $wgOut, $wgTitle, $wgArticle;

	$sql = "SELECT cur_id FROM cur WHERE (cur_namespace=0 " .
	  "AND cur_is_redirect=0)";
	$res = wfQuery( $sql, "wfSpecialRandompage" );

	$n = wfNumRows( $res ) - 1;
	wfSeedRandom();
	wfDataSeek( $res, mt_rand( 0, $n ) );

	$s = wfFetchObject( $res );
	$newid = $s->cur_id;

	$wgArticle = Article::newFromID( $newid );
	$wgOut->setArticleFlag( true );
	$wgArticle->view();
}

?>
