<?php
if(isset ($_REQUEST['load'])){
	handleLoad();
}
else if(isset ($_REQUEST['submit'])){
	handleSubmit();
}

function handleLoad(){
	$cc_id = randomCookie();
	
	if(!isset($_COOKIE['cc-cookie'])){
		$cc_id = $_REQUEST['cc-cookie'];
	}
	else{
		//set cookie to expire in maybe about a year or so... 300 days
		setcookie('cc-cookie', $cc_id, time() + (60 * 60 * 24 * 300), '/', "payments.wikimedia.org");
	}
	
	//if not exist insert into DB
	//via REQUEST['utm_src']
}

function handleSubmit(){
	$cc_id = "NULL";
	if(isset($_COOKIE['cc-cookie'])){
		$cc_id = $_COOKIE['cc-cookie'];
	}
	
	//donations++
}

function randomCookie(){
	return md5(mt_rand() + time());
}