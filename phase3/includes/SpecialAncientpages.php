<?php

require_once( "QueryPage.php" );

class AncientPagesPage extends QueryPage {

	function getName() {
		return "Ancientpages";
	}

	function isExpensive() {
		return true;
#		return parent::isExpensive() ;
	}

	function getSQL() {
		global $wgIsMySQL;
		$use_index=$wgIsMySQL?"USE INDEX (cur_timestamp)":"";
		return
			"SELECT 'Ancientpages' as type,
					cur_namespace as namespace,
			        cur_title as title,
			        UNIX_TIMESTAMP(cur_timestamp) as value
			FROM cur $use_index
			WHERE cur_namespace=0 AND cur_is_redirect=0";
	}
	
	function sortDescending() {
		return false;
	}

	function formatResult( $skin, $result ) {
		global $wgLang;

		$d = $wgLang->timeanddate( wfUnix2Timestamp( $result->value ), true );
		$link = $skin->makeKnownLink( $result->title, "" );
		return "{$link} ({$d})";
	}
}

function wfSpecialAncientpages()
{
	global $wgOut;
#	# disabling Special:Ancientpages, for which the queries take forever on big wikis - jeronim 2004-05-31
#	$wgOut->addWikiText( wfMsg( "disabled" ) );
#	return;
#	######
	

	list( $limit, $offset ) = wfCheckLimits();

	$app = new AncientPagesPage();

	$app->doQuery( $offset, $limit );
}

?>
