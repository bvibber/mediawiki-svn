<?php
ini_set("display_errors", 1);

require_once('/etc/logwood-fe.conf');

$dbh = mysql_connect($server, $username, $password);
mysql_select_db($database, $dbh);

mysql_query("DELETE FROM topurls", $dbh);
mysql_query("DELETE FROM topagents", $dbh);
mysql_query("DELETE FROM toprefs", $dbh);

$res = mysql_query("SELECT si_id FROM sites", $dbh);
while ($row = mysql_fetch_assoc($res)) {
	$site = $row["si_id"];
	mysql_query("
		INSERT INTO topurls(tu_site, tu_url, tu_count)
			SELECT si_id, ur_id, uc_count
			FROM sites, url_id, url_count
			WHERE si_id=$site AND ur_site=si_id AND uc_url_id=ur_id
			ORDER BY uc_count DESC LIMIT 500
		", $dbh);

	mysql_query("
		INSERT INTO topagents(ta_site, ta_agent, ta_count) 
			SELECT si_id, ag_id, ac_count 
			FROM sites, agent_ids, agent_count 
			WHERE si_id=$site AND ag_site=si_id AND ac_id=ag_id 
			ORDER BY ac_count DESC LIMIT 500
		", $dbh);

	mysql_query("
		INSERT INTO toprefs(tr_site, tr_ref, tr_count)
			SELECT si_id, ref_ids.ref_id, ref_count
			FROM sites, ref_ids, ref_count
			WHERE si_id=$site AND ref_site=si_id AND ref_count.ref_id=ref_ids.ref_id
			ORDER BY ref_count DESC LIMIT 500
		", $dbh);
}
mysql_free_result($res);

mysql_close($dbh);
?>
