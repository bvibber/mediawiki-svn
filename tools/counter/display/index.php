<html>
<head>
<title>Group counter -- experimental</title>
<meta name="robots" content="noindex,nofollow">
</head>
<body>
<h1>Group counter -- experimental</h1>
<?php
ini_set('display_errors', 0);

require './dbconfig.php';

if (STATS_DB_DEBUG) {
	ini_set('display_errors', 1);
	error_reporting(E_ALL);
}

function debugError($message) {
	if (STATS_DB_DEBUG) {
		$error = mysql_error();
		if ($error) {
			$message .= ' - ' . $error;
		}
		print "<p>" . htmlspecialchars($message) . "</p>";
	}
	return false;
}

function doit() {
	$conn = mysql_connect(STATS_DB_HOST, STATS_DB_USER, STATS_DB_PASS);
	if ($conn === false) {
		return debugError('Failed to connect.');
	}
	$ok = mysql_select_db(STATS_DB);
	if (!$ok) {
		return debugError('Failed to select database.');
	}

	$res = mysql_query('SELECT hc_site, hc_page, sum(hc_count) AS hc_count FROM hit_counter GROUP BY hc_site, hc_page ORDER BY hc_site, hc_page');
	if ($res === false) {
		return debugError('Query failed.');
	}
	print "<table border=\"1\">\n";
	print "<tr><th>Hits to date</th><th>Site</th><th>Page</th></tr>\n";
	while ($row = mysql_fetch_object($res)) {
		$url = 'http://' . $row->hc_site . '/wiki/' .
			urlencode(str_replace(' ', '_', $row->hc_page));
	
		$encCount = htmlspecialchars(number_format($row->hc_count));
		$encSite = htmlspecialchars($row->hc_site);
		$encPage = htmlspecialchars($row->hc_page);
		$encUrl = htmlspecialchars($url);
	
		print "<tr><td>$encCount</td><td>$encSite</td><td><a href=\"$encUrl\">$encPage</a></td></tr>\n";
	}
	print "</table>\n";
	return true;
}

if (!doit()) {
	print "<p>Error retrieving stats from database.</p>\n";
}

?>
</body>
</html>