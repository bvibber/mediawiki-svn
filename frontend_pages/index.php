<?php
/* 	minninalist homepage 
*	setup environment vars: */
$wgScriptPath = '/mvw-exp';
$wgScript = '/mvw-exp/index.php';
$wgServer = '';
$skin_path=$wgScriptPath.'/skins/mvpcf';
$mvextension_path = $wgScriptPath.'/extensions/MetavidWiki';

//just redirect people to the main wiki page (we don't need front_end pages anymore) 
header('Location: '.$wgScript);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>metavid.org - an open video archive of us congress</title>
	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
	<meta name="author" content="" />
	<meta name="keywords" content="" />
	<meta name="description" content="" />
	<meta name="robots" content="all" />
	<link rel="stylesheet" href="<?php echo $skin_path?>/style.css" type="text/css" media="screen" />
	<!--[if lt IE 7]>
	<link rel="stylesheet" href="<?php echo $skin_path?>/ie_styles.css" type="text/css" media="screen" />
	<![endif]-->	
	<script type= "text/javascript">/*<![CDATA[*/
var wgScriptPath = "<?php echo $wgScriptPath?>";
var wgScript = "<?php echo $wgScript ?>";
var wgServer = "<?php echo $wgServer ?>";
/*]]>*/</script>
<script type="text/javascript" src="<?php echo $mvextension_path?>/skins/mv_embed/mv_embed.js"></script>	
<script type="text/javascript" src="<?php echo $mvextension_path?>/skins/mv_embed/jquery/jquery-1.2.1.js"></script>
<script type="text/javascript" src="<?php echo $mvextension_path?>/skins/mv_embed/jquery/plugins/jquery.autocomplete.js"></script>
<script type="text/javascript" src="<?php echo $mvextension_path?>/skins/mv_embed/jquery/plugins/jquery.hoverIntent.js"></script>

<script type="text/javascript" src="<?php echo $mvextension_path?>/skins/mv_allpages.js"></script>
</head>
<body id="frontPage">
	<div id="metaLogin"><a href="#">login / create account</a></div>
	<div id="frontPageTop">
		<div id="searchSplash">
			<div class="logo"><a href="#"><img src="<?php echo $skin_path?>/images/logo.png" alt="Metavid" /></a></div>
			<p class="tagline">The Open Video archive of the US Congress</p>			
			<div class="form_search_row">
				<input type="text" class="searchField" name="search[field]" id="search_field" />
				<button class="grey_button" type="submit"><span>&nbsp;&nbsp; Video Search &nbsp;&nbsp;</span></button>
				<a href="#" class="advanced_search_tag">advanced search</a>
			</div>
			
			<div id="suggestions" style="display:none">
				<div id="suggestionsTop"></div>

				<div id="suggestionsInner" class="suggestionsBox">					
					
				</div><!--suggestionsInner-->
				<div id="suggestionsBot"></div>
			</div><!--suggestions-->
		</div><!--searchSplash-->
	</div><!--frontPageTop-->

	
	<div id="frontPageContent">
		<h2>Today's Popular Searches</h2>
		<ul class="popularSearches">
			<li><a href="#">Barack Obama</a></li>
			<li><a href="#">Health Care</a></li>
			<li><a href="#">Gas Tax</a></li>
			<li><a href="#">Hillary Clinton</a></li>

			<li><a href="#">John McCain</a></li>
			<li><a href="#">Iraq War</a></li>
			<li class="last_li"><a href="#">Appropriations</a></li>
		</ul>
		
		<h2>Today's Popular Clips</h2>
		<ul class="popularClips">
			<li>

				<img src="<?php echo $skin_path ?>/images/img1.jpg" alt="Clip Image" />
				<span class="title"><a href="#">Sen. Barack Obama (D-IL)</a></span>
				<span class="description">Senate Floor - June 3, 2008</span>
				<span class="keywords">keywords: <a href="#">war</a>, <a href="#">iraq</a>, <a href="#">budget</a></span>
			</li>

			
			<li>
				<img src="<?php echo $skin_path ?>/images/img1.jpg" alt="Clip Image" />
				<span class="title"><a href="#">Sen. Barack Obama (D-IL)</a></span>
				<span class="description">Senate Floor - June 3, 2008</span>
				<span class="keywords">keywords: <a href="#">war</a>, <a href="#">iraq</a>, <a href="#">budget</a></span>

			</li>
			
			<li>
				<img src="<?php echo $skin_path ?>/images/img1.jpg" alt="Clip Image" />
				<span class="title"><a href="#">Sen. Barack Obama (D-IL)</a></span>
				<span class="description">Senate Floor - June 3, 2008</span>
				<span class="keywords">keywords: <a href="#">war</a>, <a href="#">iraq</a>, <a href="#">budget</a></span>

			</li>
			
			<li class="last_li">
				<img src="<?php echo $skin_path ?>/images/img1.jpg" alt="Clip Image" />
				<span class="title"><a href="#">Sen. Barack Obama (D-IL)</a></span>
				<span class="description">Senate Floor - June 3, 2008</span>
				<span class="keywords">keywords: <a href="#">war</a>, <a href="#">iraq</a>, <a href="#">budget</a></span>

			</li>
		</ul>
	</div>	 
	<!--frontPageContent-->
	
	<div id="footer">
		<h5>Sitemap</h5>
		<ul class="footerLinks">
			<li><a href="#">Home</a></li>
			<li><a href="#">New Video Streams</a></li>

			<li><a href="#">Advanced Search</a></li>
		</ul>
		<ul class="footerLinks">
			<li><a href="#">Help</a></li>
			<li><a href="#">Video Introduction</a></li>
			<li><a href="#">FAQ</a></li>
		</ul>

		<ul class="footerLinks">
			<li><a href="#">Home</a></li>
			<li><a href="#">New Video Streams</a></li>
			<li><a href="#">Advanced Search</a></li>
		</ul>
		<ul class="footerLinks">
			<li><a href="#">About MetaVid</a></li>

			<li><a href="#">MetaVid Blog</a></li>
			<li><a href="#">MetaVid Software</a></li>
		</ul>
	</div>
	
	<p id="copyInfo">MetaVid is a non-profit project of <a href="#">UC Santa Cruz</a> and the <a href="#">Sunlight Foundation</a>.</p>

</body>
</html>