<?
require_once("/etc/logwood-fe.conf");
require_once("$graphloc/class_graphs.php");
if (!isset($_REQUEST['site'])) {
	echo "no site\n";
	exit;
}
$dbh = mysql_connect($server, $username, $password);
mysql_select_db($database, $dbh);
$site = mysql_real_escape_string($_REQUEST['site'], $dbh);
$hours = mysql_query("
        SELECT hr_hour, SUM(hr_count) AS total FROM sites,hours
        WHERE si_name='$site' AND hr_site=si_id
        GROUP BY hr_hour ORDER BY hr_hour ASC
        ");

$data = array();

for ($i = 0; $i < 24; ++$i)
	$data[$i] = array("label" => $i, "value" => 0);

while ($hour = mysql_fetch_assoc($hours)) {
	$data[$hour["hr_hour"]] = array("label" => $hour["hr_hour"], "value" => $hour["total"]);
}
mysql_free_result($hours);
mysql_close($dbh);

$g = new Graph_Bar(500,300);
$g->load_data($data);
$g->set_title("Visits by hour");
$g->titlefont = "Vera.ttf";
$g->titlesize = 10;
$g->labelfont = "Vera.ttf";
$g->labelsize = 9;
$g->smallfont = "Vera.ttf";
$g->smallsize = 7;
$g->xtitle = "Hour";
$g->ytitle = "Visits";
$g->bordersize = 0;
$g->draw();
$g->output();
$g->destroy();
?>
