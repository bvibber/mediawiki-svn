<?

# Rebuild the fulltext search indexes. This may take a while
# depending on the database size and server configuration.

include_once( "Setup.php" );
include_once( "SearchUpdate.php" );
set_time_limit(0);

$wgDBuser			= "wikiadmin";
$wgDBpassword		= $wgDBadminpassword;

#echo "Dropping index...\n";
$sql = "ALTER TABLE DROP INDEX cur_ind_title, DROP INDEX cur_ind_text";
#$res = wfQuery($sql);

$sql = "SELECT COUNT(*) AS count FROM cur";
$res = wfQuery($sql);
$s = wfFetchObject($res);
echo "Rebuilding index fields for {$s->count} pages...\n";
$n = 0;

$sql = "SELECT cur_id, cur_namespace, cur_title, cur_text FROM cur";
$res = wfQuery($sql);
while( $s = wfFetchObject($res)) {
	$t = wfStrencode( Title::indexTitle( $s->cur_namespace,
		str_replace("_", " ", $s->cur_title ) ) );
	$sql2 = "UPDATE cur SET cur_ind_title='{$t}' WHERE cur_id={$s->cur_id}";
	$res2 = wfQuery( $sql2 );
	$u = new SearchUpdate( $s->cur_id, $s->cur_title, $s->cur_text );
	$u->doUpdate();
	if ( ( (++$n) % 500) == 0) {
		echo "$n\n";
	}
}
wfFreeResult( $res );

#echo "Rebuild the index...\n";
$sql = "ALTER TABLE ADD FULLTEXT cur_ind_title (cur_ind_title),
  ADD FULLTEXT cur_ind_text (cur_ind_text)";
#$res = wfQuery($sql);

print "Done.\n";
exit();

?>
