<?php
/*
cortado_embed.php
all file checks and conditions should be checked prior to loading this page.
this page serves as a wrapper for the cortado java applet
*/
//load the http GETS:

//default to null media in not provided:
$media_url = (isset($_GET['media_url']))?$_GET['media_url']:die('no media url provided');
//default duration to 30 seconds if not provided. (ideally cortado would read this from the video file)
$duration = (isset($_GET['duration']))?$_GET['duration']:0;
//id (set to random if none provided)
$id = (isset($_GET['id']))?$_GET['id']:'vid_'.rand('1000000');

//default to video:
$stream_type = (isset($_GET['stream_type']))?$_GET['stream_type']:'video';
if($stream_type=='video'){
	$audio=$video='true';
	$height = (isset($_GET['height']))?$_GET['height']:240;
}
if($stream_type=='audio'){
	$audio='true';
	$video='false';
	$height = (isset($_GET['height']))?$_GET['height']:20;
}
$width = (isset($_GET['width']))?$_GET['width']:320;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>cortado_embed</title>
<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
-->
</style></head>
<body>
	<applet id="<?=$id?>" code="com.fluendo.player.Cortado.class" archive="cortado-ovt-stripped-0.2.2.1.jar" width="<?=$width?>" height="<?=$height?>">
		<param name="url" value="<?=$media_url?>" />
		<param name="local" value="false"/>
		<param name="keepaspect" value="true" />
		<param name="video" value="<?=$audio?>" />
		<param name="audio" value="<?=$video?>" />
		<param name="seekable" value="true" />
		<? if($duration!=0){
			?>
			<param name="duration" value="<?=$duration?>" />
			<?
		 } ?>
		<param name="bufferSize" value="200" />
	</applet>
</body>
</html>
