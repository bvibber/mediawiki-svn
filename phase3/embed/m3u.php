<?
//generates a m3u file so that vlc buffers clips.
if(isset($_GET['media_url'])){
	$media_url=$_GET['media_url'];
}else{
	//@@TODO media_url = media not found .ogg
	$media_url='';
}
//how much to buffer (for now 5 seconds):  
$cache_len = 5000;


//set out the m3u file as so that VLC will know to handle it: 
header('Content-Type: application/x-videolan-vlc');
?>#EXTM3U

#EXTVLCOPT:http-caching=<?=$cache_len?>
#
# METAVID playlist file:
#
<?=$media_url?>