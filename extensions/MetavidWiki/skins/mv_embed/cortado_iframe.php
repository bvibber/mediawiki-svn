<?php
/*
cortado_embed.php
all file checks and conditions should be checked prior to loading this page.
this page serves as a wrapper for the cortado java applet
*/
// load the http GETS:

$video = '';
$error = '';
if ( !function_exists( 'filter_input' ) ) {
	error_out( 'you version of php lacks <b>filter_input()</b> function</br>' );
}
// default to null media in not provided:
$media_url = filter_input( INPUT_GET, 'media_url', FILTER_SANITIZE_URL );
if ( is_null( $media_url ) || $media_url === false || $media_url == '' ) {
	error_out( 'not valid or missing media url' );
}
// default duration to 30 seconds if not provided. (ideally cortado would read this from the video file)
// $duration = (isset($_GET['duration']))?$_GET['duration']:0;
$duration = filter_input( INPUT_GET, 'duration', FILTER_SANITIZE_NUMBER_INT );
if ( is_null( $duration ) || $duration === false ) {
	$duration = 0;
}

// id (set to random if none provided)
$id = filter_input( INPUT_GET, 'id', FILTER_SANITIZE_STRING );
if ( is_null( $id ) || $id === false ) {
	$id = 'vid_' . rand( 0, 10000000 );
}

$width = filter_input( INPUT_GET, 'width', FILTER_SANITIZE_NUMBER_INT );
if ( is_null( $width ) || $width === false ) {
	$width = 320;
}
$height = filter_input( INPUT_GET, 'height', FILTER_SANITIZE_NUMBER_INT );
// default to video:
$stream_type = ( isset( $_GET['stream_type'] ) ) ? $_GET['stream_type']:'video';
if ( $stream_type == 'video' ) {
	$audio = $video = 'true';
	if ( is_null( $height ) || $height === false )
		$height = 240;
}
if ( $stream_type == 'audio' ) {
	$audio = 'true';
	$video = 'false';
	if ( is_null( $height ) || $height === false )
		$height = 20;
}
// set the parent domain if provided:
$parent_domain =  filter_input( INPUT_GET, 'parent_domain', FILTER_SANITIZE_STRING );

// everything good output page:
output_page();

function error_out( $error = '' ) {
	output_page( $error );
	exit();
}
function output_page( $error = '' ) {
	global $id, $media_url, $audio, $video, $duration, $width, $height, $parent_domain;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>cortado_embed</title>
	<? if ( $parent_domain ) { ?>
	<script type="text/javascript">
		window.DOMAIN = '<?php echo $parent_domain?>';
	</script>
	<? } ?>
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
	<? if ( $error == '' ) { ?>
		<applet id="<?php echo $id?>" code="com.fluendo.player.Cortado.class" archive="cortado-ovt-stripped_r34336.jar" width="<?php echo $width?>" height="<?php echo $height?>">
			<param name="url" value="<?php echo $media_url?>" />
			<param name="local" value="false"/>
			<param name="keepaspect" value="true" />
			<param name="video" value="<?php echo $audio?>" />
			<param name="audio" value="<?php echo $video?>" />
			<param name="seekable" value="true" />
			<? if ( $duration != 0 ) {
				?>
				<param name="duration" value="<?php echo $duration?>" />
				<?
			 } ?>
			<param name="bufferSize" value="200" />
		</applet>
	<? } else { ?>
		<b>Error:</b> <?php echo $error?>
	<?
	}
	?>
	</body>
	</html>
<?
}
