<?
# UPLOAD FUNCTION
function doUpload () {
	global $removeFile , $xtitle , $removeFile , $Upload , $Upload_name , $no_copyright ;
	global $USERLOGGEDIN , $USERNAME ;

	if ( $USERLOGGEDIN != "YES" ) return "You are not logged in! You have to be logged in to upload a file. <a href=\"$PHP_SELF?action=login\">Log in</a> or return to the <a href=\"$PHP_SELF?no\">HomePage</a>" ;

	$rights = ",".getUserSetting ( $USERNAME , "user_rights" )."," ;
	if ( strstr ( $rights , ",is_editor," ) or strstr ( $rights , ",is_sysop" ) ) $isEditor = true ;
	else $isEditor = false ;
	if ( strstr ( $rights , ",is_sysop," ) or strstr ( $rights , ",is_sysop" ) ) $isSysop = true ;
	else $isSysop = false ;

	$xtitle = "File upload page";
	$ret = getStandardHeader() ;

	$message = "" ;

	if (isset($removeFile)) {
		if ( !$isSysop and !$isEditor ) return "You are neither an editor nor a sysop. Return to the <a href=\"$PHP_SELF?action=upload\">Upload page</a>" ;
		if (is_file("upload/$removeFile") ) unlink ("./upload/$removeFile");
		$message = "File <b>$removeFile</b> deleted!" ;
		unset ( $removeFile ) ;
	} else if (isset($Upload_name) or isset($Upload)) {
		if ( $no_copyright != "AFFIRMED" ) return "You need to affirm that the file is not violating copygights. Return to the <a href=\"$PHP_SELF?action=upload\">Upload page</a>" ;
		$Upload_name = ereg_replace(" ", "_", $Upload_name);
		$abc = split("\.", $Upload_name);

		$num = exec ("df");
		$readata = substr($num,(strpos($num, "%")-2),2);

		if ($readata > 96) {
			$ret .= "<body bgcolor=white>\n";
			$ret .= "<br><b>Sorry, we are almost out of disk space. We can't let you upload any files right now.</b>\n";
			return $ret ;
			}

		copy ( $Upload , "./upload/$Upload_name" ) ;
		system ("chmod 777 ./upload/$Upload_name");       
		$message = "File <b>$Upload_name</b> was successfully uploaded!" ;

/*		$ret .= "<script language=javascript>\n";
		$ret .= "  function winclose(name){\n";
		$ret .= "     str=\"Your file $Upload_name was successfully uploaded!\"\n";
		$ret .= "     alert(str);\n";
		$ret .= "  }\n";  
		$ret .= "</script>\n";
		$ret .= "<body bgcolor=white onload=\"winclose('$name')\">\n";*/

		unset ( $Upload_name ) ;
	}

	if ( $message != "" ) $ret .= "<font color=red>$message</font><br>\n" ;

	$ret .= "<h2>Instructions:</h2><ul>\n";
	$ret .= "<li><strong>Use this form to upload various files</strong></li>\n";
	$ret .= "<li>To replace a previously-uploaded file (e.g., a\n";
	$ret .= "new version of the article), simply re-upload the\n";
	$ret .= "same file. But first look below and make sure you\n";
	$ret .= "haven't changed the name.</li>\n";
	$ret .= "<li><strong>Here's how to upload your file. </strong>Click\n";
	$ret .= "&quot;Browse...&quot; to your find the file you\n";
	$ret .= "want to upload on your hard drive. This will open\n";
	$ret .= "a &quot;Choose file&quot; dialogue window.</li>\n";
	$ret .= "<li>When you've found the file, click &quot;Open.&quot;\n";
	$ret .= "This will select the file and close the &quot;Choose\n";
	$ret .= "file&quot; dialogue window.</li>\n";
	$ret .= "<li>Don't forget to check the copyright statement!</li>\n";
	$ret .= "<li>Then click &quot;Upload.&quot; The file will start uploading. This may take some time, if it's\n";
	$ret .= "a big file and you have a slow Internet connection.</li>\n";
	$ret .= "<li>A message will tell you when the file has successfully uploaded.</li>\n";
	$ret .= "<li>You can upload as many files you like. Please don't try to crash our server, ha ha.</li>\n";
	$ret .= "</ul>\n";

	$ret .= " <form enctype=\"multipart/form-data\" action=\"$PHP_SELF?action=upload\" method=post>\n";
	$ret .= " <input type=hidden name=max value=20096>\n";
	$ret .= " <input name=Upload type=\"file\"><br>\n";
	$ret .= " <input type=hidden name=update value=1>\n";
	$ret .= " <input type=hidden name=step value=$step>\n";
	$ret .= "<INPUT TYPE=checkbox NAME=\"no_copyright\" VALUE=\"AFFIRMED\">I hereby affirm that this file is <b>not copyrighted</b>, or that I own the copyright for this file and donate it to Wikipedia.<br>\n" ;
	$ret .= " <input type=submit value=UPLOAD>\n";
	$ret .= "</form>\n";

	if (is_dir("upload")) {
		$mydir = dir("upload");
			while ($entry = $mydir->read()) {
			if ($entry != "." and $entry != "..")
				$file = "yes";
			}
		$mydir->close();

		if ($file == "yes") {
			$ret .= "<h2>Previously-uploaded files:</h2>";
			$mydir = opendir("upload");
			$i = 0;
			$ret .= "<table border=1 width=\"100%\">\n";
			$ret .= "<tr><th>File</th><th>Size (byte)</th>";
			if ( $isSysop or $isEditor ) $ret .= "<th>File removal (editors and sysops only)</th>";"
			$ret .= </tr>\n" ;
			while ($entry = readdir($mydir)) {
				if ($entry != '.' && $entry != '..') {
					$ret .= "<tr><td align=center>" ;
					$ret .= "<a href=upload/$entry>$entry</a></td>";
					$ret .= "<td align=center>".filesize("upload/$entry")." bytes</td>";
					if ( $isSysop or $isEditor ) $ret .= "<td align=center><a href=\"$PHP_SELF?action=upload&removeFile=$entry\">Click here to remove $entry.</a></td>" ;
					$ret .= "</tr>" ;
					$i++;
				}
			}
		$ret .= "</table>\n";
		closedir($mydir);
		}
	}
	$ret .= getStandardFooter() ;
	return $ret ;
}
?>