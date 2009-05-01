<?php
// sample embed page (this could be plain html its a php script so that we can grab its location)
do_testing_page();

function do_testing_page(){
	$mv_path = 'http://' . $_SERVER['SERVER_NAME'] . substr( $_SERVER['REQUEST_URI'], 0, strrpos( $_SERVER['REQUEST_URI'], '/' ) ) . '/';
	$mv_path = str_replace( 'example_usage/', '', $mv_path );
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<title>sample mv embed</title>
 	<script type="text/javascript" src="../mv_embed.js?urid=<?php echo time()?>&debug=true"></script>
</head>
<body>
<h3>testing embed</h3>
  <table border="1" cellpadding="6" width="600">
	    <tr>
	      <td valign="top">
	      	<video  id="embed_vid" 
thumbnail="http://metavid.org/wiki/index.php?action=ajax&rs=mv_frame_server&stream_id=501&t=0:01:32&amp;size=400x300" 
roe="http://metavid.org/wiki/index.php?title=Special:MvExportStream&stream_name=House_proceeding_01-28-08&feed_format=roe&t=0:01:32/0:03:20" 
style="width:400px;height:300px" 
controls="true" embed_link="true" >	
	<source type="video/x-flv" src="http://mvbox2.cse.ucsc.edu/mvFlvServer.php/house_proceeding_01-28-08.flv?t=0:01:32/0:03:20"></source>
	<source type="video/ogg" src="http://metavidstorage01.ucsc.edu/media/house_proceeding_01-28-08.ogg?t=0:01:32/0:03:20"></source>
</video>
</td>
	      <td valign="top"><b>Test embed</b><br />
	      </td>
	    </tr>	    
  </table>
	<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />&nbsp;
  </body>
</html>
<?
}
?>