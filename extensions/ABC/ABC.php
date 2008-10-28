<?php
/* Copyright (c) 2008 River Tarnell <river@loreley.flyingparchment.org.uk>. */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */
/* $Id$ */

if (!defined('MEDIAWIKI'))
	die();
	
# The on-disk path where ABC files and rendered images / PDFs
# will be placed.
# Example: $abcPath = "/var/www/wiki/abc";
$abcPath = false; 

# The HTTP path where the above directory is found.
# Example: $abcURL = "/wiki/abc";
$abcURL = false;

# Path to the abcm2ps executable.
$abcm2ps = "/usr/bin/abcm2ps";

# Path to the ps2pdf executable.
$abcps2pdf = "/usr/bin/ps2pdf14";

# Path to the abc2midi executable.
$abc2midi = "/usr/bin/abc2midi";

$wgExtensionCredits['parserhooks'][] = array(
	'name' => 'ABC',
	'author' => 'River Tarnell',
	'url' => 'http://www.mediawiki.org/wiki/Extension:ABC',
	'description' => 'Adds <tt>&lt;abc&gt;</tt> tag to format ABC music',
	'descriptionmsg' => 'abcdesc'
);
$wgExtensionMessagesFiles['ABC'] =  dirname(__FILE__) . '/ABC.i18n.php';

if (defined('MW_SUPPORTS_PARSERFIRSTCALLINIT')) {
	$wgHooks['ParserFirstCallInit'][] = 'efABCInit';
} else {
	$wgExtensionFunctions[] = 'efABCParserInit';
}

function
efABCInit() {
global	$wgParser;
	wfLoadExtensionMessages('ABC');
	$wgParser->setHook( 'abc', 'efABCRender' );
	return true;
}
 
function
efABCRender($input, $args, $parser) {
global	$abcPath, $abcURL;
	if ($abcPath == false || $abcURL == false)
		return 'Error: $abcPath and $abcURL must be set to use the ABC extension.';

	/*
	 * To avoid re-rendering the same tunes on every view,
	 * use the hash of the ABC content for the filename.
	 * This has the added benefit that rendering the same tune
	 * on different pages will only cause one rendering.
	 */
	$float = "left";
	if (isset($args['float']) && $args['float'] == 'right')
		$float = "right";
		
	$abc = preg_replace("/^\n+/", "", $input);
	$hash = sha1($input);
	$error = "";
	
	/*
	 * Try to extract the title from the ABC.  This is used as the
	 * alt text for the image.
	 */
	$title = "Unknown song";
	if (preg_match("/^T:\s*(.*)$/m", $input, $matches))
		$title = $matches[1];
	
	if (!abcCreateABC($abc, $hash, $error))
		return str_replace("\n", "<br />", htmlspecialchars($error));
	if (!abcCreatePS($abc, $hash, $error))
		return str_replace("\n", "<br />", htmlspecialchars($error));
	if (!abcCreatePNG($abc, $hash, $error))
		return str_replace("\n", "<br />", htmlspecialchars($error));
	if (!abcCreatePDF($abc, $hash, $error))
		return str_replace("\n", "<br />", htmlspecialchars($error));
	if (!abcCreateMIDI($abc, $hash, $error))
		return str_replace("\n", "<br />", htmlspecialchars($error));
	
	/*
	 * Succeeded to create all the output formats, return the
	 * output.  We produce an image from the PNG, and include
	 * links to the ABC and PS.
	 */
	$e_title = htmlspecialchars($title);
	$e_imgpath = htmlspecialchars("$abcURL/$hash.png");
	$e_abcpath = htmlspecialchars("$abcURL/$hash.abc");
	$e_pspath = htmlspecialchars("$abcURL/$hash.ps");
	$e_pdfpath = htmlspecialchars("$abcURL/$hash.pdf");
	$e_midipath = htmlspecialchars("$abcURL/$hash.mid");
	
	$e_abclink = "<a href=\"$e_abcpath\">" . wfMsg('abcabc') . "</a>";
	$e_pslink = "<a href=\"$e_pspath\">" . wfMsg('abcps') . "</a>";
	$e_pdflink = "<a href=\"$e_pdfpath\">" . wfMsg('abcpdf') . "</a>";
	$e_midilink = "<a href=\"$e_midipath\">" . wfMsg('abcmidi') . "</a>";

	$e_dllinks = wfMsg('abcdownload') . " " .
		join(" " . wfMsg('abcsep') . " ", 
			array($e_abclink, $e_pslink, $e_pdflink, $e_midilink));
			
	$output = <<<EOF
<div style="float: $float; border: solid 1px #aaaaaa; margin: 0.2em;" class="abc-music">
	<img src="$e_imgpath" alt="$e_title" />
	<div style="text-align: center">
		$e_dllinks
	</div>
</div>
EOF;
	return $output;
}

function
abcCreateABC($abc, $hash, &$error)
{
global	$abcPath;
	if (!@file_exists($abcPath)) {
		$error = "Error: $abcPath does not exist.";
		return false;
	}
	
	$filename = "$abcPath/$hash.abc";
	if (($f = @fopen($filename, "w")) === false) {
		$last = error_get_last();
		$msg = $last['msg'];
		$error = "Error: cannot create $filename: $msg";
		return false;
	}
	
	if (@fwrite($f, $abc) === false) {
		@unlink($filename);
		$last = error_get_last();
		$msg = $last['msg'];
		$error = "Error: cannot write to $filename: $msg";
		return false;
	}
       
	if (@fclose($f) === false) {
		@unlink($filename);
		$last = error_get_last();
		$msg = $last['msg'];
		$error = "Error: cannot write to $filename: $msg";
		return false;
	}

	return true;
}

function
abcCreatePS($abc, $hash, &$error)
{
global	$abcm2ps, $abcPath;
	if (!@file_exists($abcm2ps)) {
		$error = "Error: $abcm2ps not found.";
		return false;
	}
	
	$input = "$abcPath/$hash.abc";
	$output = "$abcPath/$hash.ps";
	
	$cmd = "$abcm2ps -E $input -O $abcPath/$hash";
	@exec($cmd, $cmd_out, $ret);
	if ($ret != 0 || !@file_exists("$abcPath/{$hash}001.eps")) {
		$error = "Error: $abcm2ps failed to convert input (ret: $ret).\n";
		$error .= "Output: " . join("\n", $cmd_out);
		return false;
	}
	
	if (@rename("$abcPath/{$hash}001.eps", "$abcPath/{$hash}.ps") === false) {
		$error = "Error: cannot rename output file.";
		return false;
	}
	
	return true;
}

function
abcCreatePDF($abc, $hash, &$error)
{
global	$abcps2pdf, $abcPath;
	if (!@file_exists($abcps2pdf)) {
		$error = "Error: $abcps2pdf not found.";
		return false;
	}
	
	$input = "$abcPath/$hash.ps";
	$output = "$abcPath/$hash.pdf";
	
	$cmd = "$abcps2pdf $input $output";
	@exec($cmd, $cmd_out, $ret);
	if ($ret != 0 || !@file_exists("$abcPath/$hash.pdf")) {
		$error = "Error: $abcps2pdf failed to convert input (ret: $ret).\n";
		$error .= "Output: " . join("\n", $cmd_out);
		return false;
	}
	
	return true;
}

function
abcCreatePNG($abc, $hash, &$error)
{
global	$wgImageMagickConvertCommand, $abcPath;
	if (!$wgImageMagickConvertCommand) {
		$error = "Error: ImageMagick not enabled.";
		return false;
	}
	
	$input = "$abcPath/$hash.ps";
	$output = "$abcPath/$hash.png";
	
	$cmd = "$wgImageMagickConvertCommand $input $output";
	@exec($cmd, $cmd_out, $ret);
	if ($ret != 0 || !@file_exists($output)) {
		$error = "Error: ImageMagick failed to convert input [$output] (ret: $ret).";
		$error .= "Output: " . join("\n", $cmd_out);
		return false;
	}

	return true;
}

function
abcCreateMIDI($abc, $hash, &$error)
{
global	$abc2midi, $abcPath;
	if (!$abc2midi) {
		$error = "Error: $abc2midi not found.";
		return false;
	}
	
	$input = "$abcPath/$hash.abc";
	$output = "$abcPath/$hash.mid";
	
	$cmd = "$abc2midi $input -o $output";
	@exec($cmd, $cmd_out, $ret);
	if ($ret != 0 || !@file_exists($output)) {
		$error = "Error: $abc2midi failed to convert input [$output] (ret: $ret).";
		$error .= "Output: " . join("\n", $cmd_out);
		return false;
	}

	return true;
}
