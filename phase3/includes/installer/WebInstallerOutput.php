<?php

/**
 * Output class modelled on OutputPage.
 *
 * I've opted to use a distinct class rather than derive from OutputPage here in 
 * the interests of separation of concerns: if we used a subclass, there would be 
 * quite a lot of things you could do in OutputPage that would break the installer, 
 * that wouldn't be immediately obvious. 
 */
class WebInstallerOutput {
	var $parent;
	var $contents = '';
	var $headerDone = false;
	var $redirectTarget;
	var $debug = true;

	function __construct( $parent ) {
		$this->parent = $parent;
	}

	function addHTML( $html ) {
		$this->contents .= $html;
		$this->flush();
	}

	function addWikiText( $text ) {
		$this->addHTML( $this->parent->parse( $text ) );
	}

	function addHTMLNoFlush( $html ) {
		$this->contents .= $html;
	}

	function redirect( $url ) {
		$this->redirectTarget = $url;
	}

	function output() {
		$this->flush();
		$this->outputFooter();
	}

	function flush() {
		if ( !$this->headerDone ) {
			$this->outputHeader();
		}
		if ( !$this->redirectTarget && strlen( $this->contents ) ) {
			echo $this->contents;
			flush();
			$this->contents = '';
		}
	}

	function outputHeader() {
		global $wgVersion;
		$this->headerDone = true;
		$dbTypes = $this->parent->getDBTypes();

		$this->parent->request->response()->header("Content-Type: text/html; charset=utf-8");
		if ( $this->redirectTarget ) {
			$this->parent->request->response()->header( 'Location: '.$this->redirectTarget );
			return;
		}

		
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">
<head>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	<title>MediaWiki <?php echo( htmlspecialchars( $wgVersion ) ); ?> Installation</title>
	<style type="text/css">

		@import "../skins/monobook/main.css";

		.env-check {
			font-size: 90%;
			margin: 1em 0 1em 2.5em;
		}

		.config-section {
			margin-top: 2em;
		}

		.config-label {
			clear: left;
			font-weight: bold;
			width: 10em;
			float: left;
			text-align: right;
			padding-right: 1em;
			padding-top: .2em;
		}

		.config-input {
			clear: left;
			zoom: 100%; /* IE hack */
		}

		.config-page-wrapper {
			padding: 0.5em;
		}

		.config-page-list {
			float: right;
			width: 12em;
			border: 1px solid #aaa;
			padding: 0.5em;
			margin: 0.5em;
		}

		.config-page {
			padding: 0.5em 2em 0.5em 2em;
			/* 15em right margin to leave space for 12em page list */
			margin: 0.5em 15em 0.5em 0.5em;
			border: 1px solid #aaa;
		}

		.config-submit {
			clear: left;
			text-align: center;
			padding: 1em;
		}

		.config-submit input {
			margin-left: 0.5em;
			margin-right: 0.5em;
		}

		.config-page-disabled {
			color: #aaa;
		}

		.config-info-left {
			margin: 0.5em;
			float: left;
			width: 35px;
		}

		.config-info-right {
			margin: 0.5em;
			float: left;
			width: 30em;
		}
		
		.config-page-current {
			font-weight: bold;
		}

		.config-desc {
			clear: left;
			margin: 0 0 2em 12em;
			padding-top: 1em;
			font-size: 85%;
		}

		.config-message {
			display: list-item;
			line-height: 1.5em;
			list-style-image: url(../skins/common/images/bullet.gif);
			list-style-type: square;
		}

		.config-input-text {
			width: 20em;
			margin-right: 1em;
		}

		.config-input-check {
			margin-left: 10em;
		}

		.error {
			color: red;
			background-color: #fff;
			font-weight: bold;
			left: 1em;
			font-size: 100%;
		}

		.config-error-top {
			background-color: #FFF0F0;
			border: 2px solid red;
			font-size: 110%;
			font-weight: bold;
			padding: 1em 1.5em;
			margin: 2em 0 1em;
		}

		.config-settings-block {
			list-style-type: none;
			list-style-image: none;
			float: left;
			margin: 0;
			padding: 0;
		}

		.btn-install {
			font-weight: bold;
			font-size: 110%;
			padding: .2em .3em;
		}

		.license {
			clear: both;
			font-size: 85%;
			padding-top: 3em;
		}
		
		.success-message {
			font-weight: bold;
			font-size: 110%;
			color: green;
		}
		.success-box {
			font-size: 130%;
		}

	</style>
	<script type="text/javascript">
	<!--
<?php
		echo "var dbTypes = " . Xml::encodeJsVar( $dbTypes ) . "\n";
?>
	function hideAllDBs() {
		for ( var i = 0; i < dbTypes.length; i++ ) {
			elt = document.getElementById( 'DB_wrapper_' + dbTypes[i] );
			if ( elt ) elt.style.display = 'none';
		}
	}
	function showDBArea(type) {
		hideAllDBs();
		var div = document.getElementById('DB_wrapper_' + type);
		if (div) div.style.display = 'block';
	}
	function resetDBArea() {
		for ( var i = 0; i < dbTypes.length; i++ ) {
			input = document.getElementById('DBType_' + dbTypes[i]);
			if ( input && input.checked ) {
				showDBArea( dbTypes[i] );
				return;
			}
		}
	}
	function disableControlArray( sourceID, targetIDs ) {
		var source = document.getElementById( sourceID );
		var disabled = source.checked ? '1' : '';
		if ( !source ) {
			return;
		}
		for ( var i = 0; i < targetIDs.length; i++ ) {
			var elt = document.getElementById( targetIDs[i] );
			if ( elt ) elt.disabled = disabled;
		}
	}
	// -->
	</script>
</head>

<body>
<div id="globalWrapper">
<div id="column-content">
<div id="content">
<div id="bodyContent">

<h1>MediaWiki <?php print htmlspecialchars( $wgVersion ); ?> Installation</h1>
<?php
	}

	function outputFooter() {
?>
	<div class="license">
	<hr/>
	<p>This program is free software; you can redistribute it and/or modify
	 it under the terms of the GNU General Public License as published by
	 the Free Software Foundation; either version 2 of the License, or
	 (at your option) any later version.</p>

	 <p>This program is distributed in the hope that it will be useful,
	 but WITHOUT ANY WARRANTY; without even the implied warranty of
	 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	 GNU General Public License for more details.</p>

	 <p>You should have received <a href="../COPYING">a copy of the GNU General Public License</a>
	 along with this program; if not, write to the Free Software
	 Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
	 or <a href="http://www.gnu.org/copyleft/gpl.html">read it online</a></p>
	</div>

</div></div></div>


<div id="column-one">
	<div class="portlet" id="p-logo">
	  <a style="background-image: url(../skins/common/images/mediawiki.png);"
	    href="http://www.mediawiki.org/"
	    title="Main Page"></a>
	</div>
	<script type="text/javascript"> if (window.isMSIE55) fixalpha(); </script>
	<div class='portlet'><div class='pBody'>
		<ul>
			<li><strong><a href="http://www.mediawiki.org/">MediaWiki home</a></strong></li>
			<li><a href="../README">Readme</a></li>
			<li><a href="../RELEASE-NOTES">Release notes</a></li>
			<li><a href="../docs/">Documentation</a></li>
			<li><a href="http://www.mediawiki.org/wiki/Help:Contents">User's Guide</a></li>
			<li><a href="http://www.mediawiki.org/wiki/Manual:Contents">Administrator's Guide</a></li>
			<li><a href="http://www.mediawiki.org/wiki/Manual:FAQ">FAQ</a></li>
		</ul>
		<p style="font-size:90%;margin-top:1em">MediaWiki is Copyright © 2001-2008 by Magnus Manske, Brion Vibber,
		 Lee Daniel Crocker, Tim Starling, Erik Möller, Gabriel Wicke, Ævar Arnfjörð Bjarmason, Niklas Laxström,
		 Domas Mituzas, Rob Church, Yuri Astrakhan, Aryeh Gregor, Aaron Schulz and others.</p>
	</div></div>
</div>

</div>

</body>
</html>
<?php
	}
}
