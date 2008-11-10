<?PHP
	$admins=" ";                 // IRC nicks of the sysops
	$cellphones=array();
	$cellphones[]="49900666666"; // International number without the +
	$channel="#test-1234";       // IRC channel where the bot is.
	$nick="yaab";                // bot nickname
	$password=" ";               // nickserv password
	$bulksms_from="abc@example.com"; // bulksms.co.uk "From" email address
	$bulksms_auth=" ";           // bulksms.co.uk auth password
	$url="http://example.com/warning.php" // URL to the Web UI
	$ttw=5*60;                   // seconds between warning display in IRC and SMS sending
	$smsinterval=20*60           //seconds between sending of SMS, to avoid flood

?>