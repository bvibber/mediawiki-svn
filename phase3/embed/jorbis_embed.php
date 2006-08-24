<?
$media_url = (isset($_GET['media_url']))?$_GET['media_url']:die('no media url provided');
?>
<html>
    <head>
      <TITLE>JOrbisPlayer</TITLE>
    </head>

    <BODY bgcolor=#FFFFFF link="#0000FF" text="#000000" vlink="#0000FF" 
          alink="#FF0000" topmargin="0" bottommargin="0" leftmargin="0" 
          rightmargin="0" marginheight="0" marginwidth="0">

<?
//stoped
if(!isset($_GET['state']))$_GET['state']='play';
if($_GET['state']=='play'){
		?>
		<OBJECT classid="clsid:8AD9C840-044E-11D1-B3E9-00805F499D93"
        width="1" height="1" align="left"
        codebase="http://java.sun.com/products/plugin/1.3/jinstall-13-win32.cab#Version=1,3,0,0">
  <PARAM NAME="java_codebase" VALUE="http://www.jcraft.com/jorbis/player/">
  <PARAM NAME="java_code" VALUE="JOrbisPlayer.class">
  <PARAM NAME="archive" VALUE="JOrbisPlayer-0.0.16.2-rsa.jar">
  <PARAM NAME="jorbis.player.play.0" VALUE="<?=$media_url?>">
  <PARAM NAME="jorbis.player.icestats" VALUE="no">
  <PARAM NAME="jorbis.player.playonstartup" VALUE="yes">
  <PARAM NAME="type" VALUE="application/x-java-applet;version=1.3">
  <COMMENT>
  
    <EMBED type="application/x-java-applet;version=1.3"
           width="1" height="1"
           java_codebase="http://www.jcraft.com/jorbis/player/"
           java_code="JOrbisPlayer.class"
           archive="JOrbisPlayer-0.0.16.2-rsa.jar"
           jorbis.player.play.0="<?=$media_url?>"
           jorbis.player.icestats="no"
           jorbis.player.playonstartup="yes"
           pluginspage="http://java.sun.com/products/plugin/1.3/plugin-install.html">
    <NOEMBED>
  </COMMENT>
    No J2SE plugin support.
    </NOEMBED>
    </EMBED>
</OBJECT>
<a href="<?=$_SERVER['REQUEST_URI'].'&state=stop'?>">Stop</a>
<?
}else if($_GET['state']=='stop'){
	?>
		<a href="<?=$_SERVER['PHP_SELF'].'?media_url='.$_GET['media_url']?>">Play</a>
	<?
}

?>

</body>
</html>
<?
$media_url = (isset($_GET['media_url']))?$_GET['media_url']:die('no media url provided');
?>
<html>
    <head>
      <TITLE>JOrbisPlayer</TITLE>
    </head>

    <BODY bgcolor=#FFFFFF link="#0000FF" text="#000000" vlink="#0000FF" 
          alink="#FF0000" topmargin="0" bottommargin="0" leftmargin="0" 
          rightmargin="0" marginheight="0" marginwidth="0">

<?
//stoped
if(!isset($_GET['state']))$_GET['state']='play';


if($_GET['state']=='play'){
		?>
		<OBJECT classid="clsid:8AD9C840-044E-11D1-B3E9-00805F499D93"
        width="1" height="1" align="left"
        codebase="http://java.sun.com/products/plugin/1.3/jinstall-13-win32.cab#Version=1,3,0,0">
  <PARAM NAME="java_codebase" VALUE="http://www.jcraft.com/jorbis/player/">
  <PARAM NAME="java_code" VALUE="JOrbisPlayer.class">
  <PARAM NAME="archive" VALUE="JOrbisPlayer-0.0.16.2-rsa.jar">
  <PARAM NAME="jorbis.player.play.0" VALUE="<?=$media_url?>">
  <PARAM NAME="jorbis.player.icestats" VALUE="no">
  <PARAM NAME="jorbis.player.playonstartup" VALUE="yes">
  <PARAM NAME="type" VALUE="application/x-java-applet;version=1.3">
  <COMMENT>
  
    <EMBED type="application/x-java-applet;version=1.3"
           width="1" height="1"
           java_codebase="http://www.jcraft.com/jorbis/player/"
           java_code="JOrbisPlayer.class"
           archive="JOrbisPlayer-0.0.16.2-rsa.jar"
           jorbis.player.play.0="<?=$media_url?>"
           jorbis.player.icestats="no"
           jorbis.player.playonstartup="yes"
           pluginspage="http://java.sun.com/products/plugin/1.3/plugin-install.html">
    <NOEMBED>
  </COMMENT>
    No J2SE plugin support.
    </NOEMBED>
    </EMBED>
</OBJECT>
<a href="<?=$_SERVER['REQUEST_URI'].'&state=stop'?>">Stop</a>
<?
}else if($_GET['state']=='stop'){
	?>
		<a href="<?=$_SERVER['PHP_SELF'].'?media_url='.$_GET['media_url']?>">Play</a>
	<?
}

?>

</body>
</html>
