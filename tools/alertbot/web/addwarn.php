<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Wikimedia emergency problem report</title>
</head>

<body>
<?PHP
require("data.inc.php");
$problem=$_REQUEST["problem"];
$project=$_REQUEST["project"];
$language=$_REQUEST["language"];
$level=$_REQUEST["affected"];
$problem_other=$_REQUEST["problem-other"];
$banlist=file("/tmp/ab_bans.txt");

//prechecks for sanity
foreach($banlist as $ban) //check if user is banned
	if($_SERVER['REMOTE_ADDR']==trim($ban))
		$msg.="You are banned from using this service.";
if(array_search($project,array_keys($acceptable_projects))===FALSE)
	$msg.="Please choose a valid project.<br />\r\n";
if(array_search($problem,array_keys($acceptable_problems))===FALSE)
	$msg.="Please choose a valid problem.<br />\r\n";
if(array_search($level,array_keys($acceptable_levels))===FALSE)
	$msg.="Please choose how many people are affected.<br />\r\n";
if(!array_search($language,$acceptable_languages)===FALSE && $language!="")
	$msg.="Please choose a valid language.<br />\r\n";
if($project=="OT")
	$msg.="Unfortunately, your problem can not be solved by Wikimedia. Please contact the site's administrator.<br />\r\n";
if($project=="ow")
	$project="other";
if($level=="justme")
	$msg.="Please check back with other users before re-submitting so that we can be sure this is a bug on our side.<br />Remember this is only for emergencies.<br />\r\n";
if(strlen($problem_other)>100) //shouldn't happen, but we must check
	$msg.="Problem text too long, please shorten it.<br />\r\n";


if($msg=="") {//no errors, we can proceed
	if($project=="com")				//commons & bugzilla: get rid of the leading .
		$project="commons";
	elseif($project=="bugzilla")
		$project="bugzilla";
	else
		$project=$language.".".$project;

	$sms=$level_correspond[$level].": ".$project." / ".$problem_correspond[$problem];
	if($problem=="other" || $problem=="parts")
		$sms.=$problem_other;
	$fp=fopen("/tmp/ab_msg.txt","a");
	fwrite($fp,$sms.chr(250).$_SERVER['REMOTE_ADDR']."\r\n");
	fclose($fp);
	$msg.="Thanks for your submission.<br />\r\nMessage sent: $sms<br />\r\n";
}
$msg.="<a href='warning.php'>Back</a> to main page.";
?>
<?=$msg?>
</body>
</html>