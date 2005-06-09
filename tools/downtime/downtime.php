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
	<title><?= langet("maintitle") ?></title>  
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style type="text/css">
/* E-mail brion@pobox.com if you see this text rendered at the top of the page. */

body {
	font-family: serif;
	background: url(<?= $statpath ?>Headbg.jpg) #FAFAFA no-repeat top left;
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

h2 {
 font-size: 150%;border-bottom: 1px #535353 dashed; font-weight: bold; 
 margin-top:25px;margin-bottom:0px;line-height:200%;
}

li {
	list-style-type: square;
	list-style-image: url("<?= $statpath ?>bullet.gif");
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
	background: url(<?= $statpath ?>Logoparabg1.jpg) no-repeat 0px 0px;
}

#footer {
	width: 100%;
	height: 109px; 
	background: url(<?= $statpath ?>Footbg.jpg) no-repeat 0px 0px; 
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
		<h1><img id="wikilogo" src="<?= $statpath ?>Wikilogo.png" alt="Wikipedia" width="230" height="31" /></h1>	
	</div> 
	<div id="languages">
		<?= langbar() ?>
	</div>
	<div id="body">
	<div id="firstsection">

		<h2><?= langet("maintitle") ?></h2>
		<p><?= langet("mainp1") ?></p>
		<p><?= langet("mainp2") ?></p>

		<ul>
<? if ($lang == "da") { ?>
			<li><a href="http://www.netleksikon.dk">www.netleksikon.dk</a> (Dansk)</li>
<? } else if ($lang == "pl") { ?>
			<li><a href="http://pl.efactory.pl/allpl.php">efactory.pl</a> (Polski)</li>
<? } ?>
			<li><a href="http://www.answers.com/">Answers.com</a></li>
			<li><a href="http://www.fact-index.com/">fact-index.com</a></li>
		</ul>

		<p><?= langet("mainp3") ?></p>
		<p><?= langet("mainp4") ?></p>
	</div>

	<h2><?= langet("abouttitle") ?></h2>
	<p><?= langet("about")?></p>

<? if ($lang != "da") { ?>
	<h2><?= langet("projectstitle") ?></h2>
	<p><?= langet("projects") ?></p>
<? } ?>

	</div>

	<div id="footer">
		<div id="copyright">
<a class="dark" href="http://validator.w3.org/check?uri=referer">Valid XHTML 1.0</a>.
<?= langet("credit") ?>
		</div>
	</div>
</body>
</html>
