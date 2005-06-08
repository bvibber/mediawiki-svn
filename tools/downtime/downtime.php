<?php
# $Header$

header('HTTP/1.x 503 Service Unavailable');
$rootpath = '/downtime/';
$untilhour = 21;
$statpath = "${rootpath}stat/";
$scriptpath = "${rootpath}downtime.php";

include "language-support.php";

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<title>Wikimedia site maintenance</title>  
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style type="text/css">
body {
	font-family: serif;
	background: url(<?php echo $statpath ?>Headbg.jpg) #FAFAFA no-repeat top left;
	margin: 0px;
	padding: 0px;
	width: 100%;
}

h1, h2, h3 {
	font-family: sans-serif;
	font-variant: small-caps;
}

h1 {
	margin:0px;
}

/*h2 {
	font-size: 200%;
}*/
h2 {
 font-size: 150%;border-bottom: 1px #535353 dashed; font-weight: bold; 
 margin-top:25px;margin-bottom:0px;line-height:200%;
}

li {
	list-style-type: square;
	list-style-image: url("<?php echo $statpath ?>bullet.gif");
}

#firstsection {
	margin-left: 340px;
}

#copyright {
        text-align: right;
        font-size: small;
        padding-top: 80px;
	font-family: sans-serif;
	color: white;
}

p:first-letter {
	font-size: 150%;
}

#header {
	width: 100%; height: 124px;
}

#languages {
	margin: 1em;
	margin-top: -25px;
	float: right;
}

#body {
	padding-right: 4em;
	padding-left: 4em;
	background: url(<?php echo $statpath ?>Logoparabg1.jpg) no-repeat 0px 0px;
}

#footer {
	width: 100%;
	height: 109px; 
	background: url(<?php echo $statpath ?>Footbg.jpg) no-repeat 0px 0px; 
	clear: left;
}
#footer p {margin:0px}
#wikilogo {
	position: absolute; 
	top: 20px; 
	left: 30px;
}

a {
	color: #36b; text-decoration: none;
}

a.dark {
	color: #ddf;
	text-decoration: underline;
}

a:hover {text-decoration:underline;}
.plainlinks p, .plainlinks a {vertical-align:middle;}
img {border:0px;}
</style>	

</head>

<body>
	<div id="header">
		<h1><img id="wikilogo" src="<?php echo $statpath ?>Wikilogo.png" alt="Wikipedia" width="230" height="31" /></h1>	
	</div> 
	<div id="languages">
		<?php echo langbar() ?>
	</div>
	<div id="body">
	<div id="firstsection">

		<h2><?php echo langet("maintitle") ?></h2>
		<p><?php echo langet("mainp1") ?></p>
		<p><?php echo langet("mainp2") ?></p>

		<ul>
<?php if ($lang == "da") { ?>
			<li><a href="http://www.netleksikon.dk">www.netleksikon.dk</a> (Dansk)</li>
<?php } ?>
<?php if ($lang == "pl") { ?>
			<li><a href="http://pl.efactory.pl/allpl.php">efactory.pl</a> (Polski)</li>
<?php } ?>
			<li><a href="http://www.answers.com/">Answers.com</a></li>
			<li><a href="http://www.fact-index.com/">fact-index.com</a></li>
		</ul>

		<p><?php echo langet("mainp3") ?></p>
		<p><?php echo langet("mainp4") ?></p>
	</div>

	<h2><?php echo langet("abouttitle")?></h2>
	<p><?php echo langet("about")?></p>

<?php if ($lang != "da") { ?>
	<h2><?php echo langet("projectstitle")?></h2>
	<p><?php echo langet("projects")?></p>
<?php } ?>

	</div>

	<div id="footer">
		<div id="copyright">
<a class="dark" href="http://validator.w3.org/check?uri=referer">Valid XHTML 1.0</a>.
<?php echo langet("credit") ?>
		</div>
	</div>
</body>
</html>
