<?php
if(isset ($_REQUEST['load'])){
	handleLoad();
}
else if(isset ($_REQUEST['submit'])){
	handleSubmit();
}

function handleLoad(){
	$cc_id = randomCookie();
	
	if(isset($_COOKIE['cc-cookie'])){
		$cc_id = $_COOKIE['cc-cookie'];
	}
	else{
		//set cookie to expire in maybe about a year or so... 300 days
		setcookie('cc-cookie', $cc_id, time() + (60 * 60 * 24 * 300), '/', "payments.wikimedia.org");
	}
	
	$cc_id = addslashes($cc_id);
	$utm_src = isset($_REQUEST['utm_src']) ? 
			   addslashes($_REQUEST['utm_src']) : "unknown";
	$sql = "INSERT IGNORE INTO cc-track (cookie_id, utm_src, contribs) VALUES ('$cc_id','$utm_src', 0)";
}

function handleSubmit(){
	$cc_id = "NULL";
	if(isset($_COOKIE['cc-cookie'])){
		$cc_id = $_COOKIE['cc-cookie'];
	}
	
	$cc_id = addslashes($cc_id);
	$sql = "UPDATE cc-track SET contribs=contribs+1 WHERE cookie_id = '$cc_id'";
}

function randomCookie(){
	return md5(mt_rand() + time());
}