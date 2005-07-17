<?
require_once("/etc/logwood-fe.conf");
require_once("/etc/logwood-fe/lw-support.php");
require_once("$graphloc/class_graphs.php");

if (!isset($_REQUEST['site'])) {
	echo "no site\n";
	exit;
}
$site = $_REQUEST['site'];

$data = array();

$db = new lw_db($site);
$hours = $db->edits_by_hour();

foreach ($hours as $hour => $count) {
	$data[$hour] = array("label" => (int)$hour, "value" => (int)$count);
}
$db->close();

$g = new Graph_Bar(500,300);
$g->load_data($data);
$g->set_title("Visit rates by hour for " . htmlspecialchars($site));
$g->titlefont = "Vera.ttf";
$g->titlesize = 10;
$g->labelfont = "Vera.ttf";
$g->labelsize = 9;
$g->smallfont = "Vera.ttf";
$g->smallsize = 7;
$g->xtitle = "Hour";
$g->ytitle = "Visits";
$g->bordersize = 0;
$g->bgcolor = new Color(0xFA, 0xFA, 0xFA);
$g->draw();
$g->output();
$g->destroy();
?>
