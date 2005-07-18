<?php
ini_set("display_errors", 1);

require_once('/etc/logwood-fe.conf');

$db = new mysqli($server, $username, $password, $database);

$stmt = $db->prepare("DELETE FROM topurls");
$stmt->execute();
$stmt = $db->prepare("DELETE FROM topagents");
$stmt->execute();
$stmt = $db->prepare("DELETE FROM toprefs");
$stmt->execute();

$insert_topurls = $db->prepare("
	INSERT INTO topurls(tu_site, tu_url, tu_count)
		SELECT si_id, ur_id, uc_count
		FROM sites, url_id, url_count
		WHERE si_id=? AND ur_site=si_id AND uc_url_id=ur_id
		ORDER BY uc_count DESC LIMIT 500
	");
$qallurls = $db->prepare("
	SELECT si_name, ur_path, uc_count
	FROM sites, url_id, url_count
	WHERE si_id=? AND ur_site=si_id AND uc_url_id=ur_id
	ORDER BY uc_count DESC
	");
$insert_topagents = $db->prepare("
	INSERT INTO topagents(ta_site, ta_agent, ta_count) 
		SELECT si_id, ag_id, ac_count 
		FROM sites, agent_ids, agent_count 
		WHERE si_id=? AND ag_site=si_id AND ac_id=ag_id 
		ORDER BY ac_count DESC LIMIT 500
	");

$insert_toprefs = $db->prepare("
	INSERT INTO toprefs(tr_site, tr_ref, tr_count)
		SELECT si_id, ref_ids.ref_id, ref_count
		FROM sites, ref_ids, ref_count
		WHERE si_id=? AND ref_site=si_id AND ref_count.ref_id=ref_ids.ref_id
		ORDER BY ref_count DESC LIMIT 500
	");

$si_id = $si_name = false;
$qsites = $db->prepare("SELECT si_id, si_name FROM sites");
$qsites->bind_result($si_id, $si_name);
$qsites->execute();
$qsites->store_result();
while ($qsites->fetch()) {
	$insert_topurls->bind_param("i", $si_id);
	$insert_topurls->execute();

	$insert_topagents->bind_param("i", $si_id);
	$insert_topagents->execute();

	$insert_toprefs->bind_param("i", $si_id);
	$insert_toprefs->execute();

	$date = date('Y-m');
	$fdate = date('Y-m-d H:i:s');
	@mkdir("$lwbase/archive");
	@mkdir("$lwbase/archive/$si_name");
	@mkdir("$lwbase/archive/$si_name/$date");
	$dir = "$lwbase/archive/$si_name/$date";
	$fh = fopen("$dir/$date.$si_name.all_urls.txt", "w");
	fwrite($fh, "# This is a list of all URLs for $si_name\n");	
	fwrite($fh, "# It was last updated on $fdate.\n");
	
	$a_name = $a_path = $a_count = false;
	$qallurls->bind_param("i", $si_id);
	$qallurls->bind_result($a_name, $a_path, $a_count);
	$qallurls->execute();
	while ($qallurls->fetch())
		fwrite($fh, "$a_count\t$a_name\t" . 
			str_replace("\n", "%0A", 	
			str_replace("\r", "%0D",
			str_replace("%", "%25",
				urldecode($a_path)))) . "\n");
	fclose($fh);
	$qallurls->free_result();
}
$qsites->close();
$db->close();
?>
