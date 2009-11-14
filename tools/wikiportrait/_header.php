<?php
	include 'inc/common.php';
?>
<!doctype html>
<html>
	<head>
		<!--
			Wikiportrait v<?php echo $wikiportrait_version; ?>
			Programmed by [[User:Husky]] / Hay Kranen
			Released under the terms of the GNU Public License, version 2 or later
			Concept by the Werkgroep Vrije Media (Workgroup Free Media) of the Dutch Chapter
			of the Wikimedia Foundation: http://nl.wikimedia.org
		-->
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link rel="stylesheet" type="text/css" href="style.css" />
    	<!--[if lt IE 7]>
            <link rel="stylesheet" href="style_ie6.css" type="text/css" media="screen" />
    	<![endif]-->
		<style type="text/css">
            #wrapper {
                /* we need to set this here because it might change per language */
                background: url('images/logo_<?php echo $GLOBALS['settings']['language']; ?>.png') top left no-repeat;
            }
	 	</style>

        <script type="text/javascript" src="javascript/jquery.js"></script>
        <script type="text/javascript" src="javascript/lib_formvalidation.js"></script>
		<script type="text/javascript" src="javascript/lib_js.js"></script>
		<script type="text/javascript">
			var GE_WIZARD='<?php echo GE_WIZARD; // this is a quick hack to prevent having to write a parser for the uploadforms :) ?>';
			var GE_URL='<?php echo GE_URL; ?>';

			var messages = {};
			<?php
			     $values = array("EMPTY_VALUE", "INVALID_EMAIL", "INVALID_EMAIL_PROVIDER", "DISCLAIMER_NOT_AGREED", "INVALID_FILETYPE", "WAIT_FOR_UPLOAD");
			     foreach($values as $value) {
			         echo "messages.$value = '".addslashes(___($value))."';\n";
			     }
		    ?>
		</script>
    	<title>
    	   <?php if(GE_DEV_MODE > 0) echo "TEST MODE - "; ?>
    	   <?php __('TITLE'); ?>
	   </title>
	</head>
	<body>
		<div id="wrapper">
			<div id="header">
				<h1>
				    <a href="<?php echo GE_HOME; ?>">
                        <?php if(GE_DEV_MODE > 0) echo "TEST MODE - "; ?>
    				    <?php __('TITLE'); ?>
			        </a>
		        </h1>
			</div>
