<?

function wfSpecialUpload()
{
	global $wpUpload, $wpReUpload, $deletefile;
	global $wgSavedFile;

	if ( isset( $wpUpload ) ) {
		processUpload();
	} else {
		if ( isset( $wpReUpload ) ) {
			# TODO: consider removing the previous file here
		}
		mainUploadForm( "" );
	}
}

function processUpload()
{
	global $wgUser, $wgOut, $wpUploadAffirm, $wpUploadFile;
	global $wpUploadDescription, $wpIgnoreWarning;
	global $HTTP_POST_FILES, $wgUploadDirectory;
	global $wpUploadSaveName, $wpUploadTempName, $wpUploadSize;
	global $wgSavedFile;

	if ( 1 != $wpUploadAffirm ) {
		mainUploadForm( WfMsg( "noaffirmation" ) );
		return;
	}
	if ( ! $wpUploadTempName ) {
		$wpUploadTempName = $HTTP_POST_FILES['wpUploadFile']['tmp_name'];
	}
	if ( ! $wpUploadSize ) {
		$wpUploadSize = $HTTP_POST_FILES['wpUploadFile']['size'];
	}
	if ( ! $wpUploadSaveName ) {
		$oname = $HTTP_POST_FILES['wpUploadFile']['name'];
		$basename = strrchr( $oname, "/" );
		if ( false === $basename ) { $basename = $oname; }
		else ( $basename = substr( $basename, 1 ) );

		$ext = strrchr( $basename, "." );
		if ( false === $ext ) { $ext = ""; }
		else { $ext = substr( $ext, 1 ); }

		if ( "" == $ext ) { $xl = 0; } else { $xl = strlen( $ext ) + 1; }
		$partname = substr( $basename, 0, strlen( $basename ) - $xl );

		if ( strlen( $partname ) < 3 ) {
			mainUploadForm( WfMsg( "minlength" ) );
			return;
		}
		$nt = Title::newFromText( $basename );
		$wpUploadSaveName = $nt->getDBkey();
		if ( preg_match( "/^[a-z]/", $basename ) ) {
			$wpUploadSaveName = strtolower( $wpUploadSaveName{0} ) .
			  substr( $wpUploadSaveName, 1 );
		}
		$oldumask = umask(0);
		$dest = $wgUploadDirectory . "/" . $wpUploadSaveName{0};
		if ( ! is_dir( $dest ) ) { mkdir( $dest, 0777 ); }
		$dest .= "/" . substr( $wpUploadSaveName, 0, 2 );
		if ( ! is_dir( $dest ) ) { mkdir( $dest, 0777 ); }
		umask( $oldumask );

		$wgSavedFile = "{$dest}/{$wpUploadSaveName}";
		move_uploaded_file( $wpUploadTempName, $wgSavedFile );

		if ( ( ! $wpIgnoreWarning ) &&
		  ( 0 != strcmp( $basename , $wpUploadSaveName ) ) ) {
			$warn = str_replace( "$1", $wpUploadSaveName, wfMsg( "badfilename" ) );
			return uploadWarning( $warn );
		}
		$extensions = array( "png", "jpg", "jpeg", "gif" ); 
		if ( ( ! $wpIgnoreWarning ) &&
		  ( ! in_array( strtolower( $ext ), $extensions ) ) ) {
			$warn = str_replace( "$1", $ext, wfMsg( "badfiletype" ) );
			return uploadWarning( $warn );
		}
		if ( ( ! $wpIgnoreWarning ) && ( $wpUploadSize > 100000 ) ) {
			return uploadWarning( WfMsg( "largefile" ) );
			return;
		}
	}
	$conn = wfGetDB();
	$sql = "SELECT img_name FROM images WHERE img_name={$wpUploadSaveName}";
	wfDebug( "Upl: $sql\n" );

	$res = mysql_query( $sql, $conn );
	if ( ( false === $res ) || ( 0 == mysql_num_rows( $res ) ) ) {
		$sql = "INSERT INTO images (img_name,img_size,img_timestamp," .
		  "img_description,img_user,img_user_text) VALUES (" .
		  "'{$wpUploadSaveName}',{$wpUploadSize},'" . date( "YmdHis" ) . "','" .
		  wfStrencode( $wpUploadDescription ) . "', '" . $wgUser->getID() .
		  "', '" . wfStrencode( $wgUser->getName() ) . "')";
	} else {
		$sql = "UPDATE images SET img_size={$wpUploadSize}," .
		  "img_timestamp=img_timestamp,img_user='" . $wgUser->getID() . "'," .
		  "img_user_text='" . wfStrencode( $wgUser->getName() ) . "', '" .
		  "img_description='{$wpUploadDescription}' " .
		  "WHERE img_name='{$wpUploadSaveName}'";
	}
	$conn = wfGetDB();
	wfDebug( "Upl: $sql\n" );
	$res = mysql_query( $sql, $conn );

	$wgOut->addHTML( "<h2>" . wfMsg( "successfulupload" ) . "</h2>\n" );
	$text = str_replace( "$1", $wpUploadSaveName, wfMsg( "fileuploaded" ) );
	$wgOut->addHTML( "<p>{$text}\n" );
	$wgOut->returnToMain();
}

function uploadWarning( $warning )
{
	global $wgOut, $wgUser, $wgUploadDirectory;
	global $wpUpload, $wpReUpload, $wpUploadAffirm, $wpUploadFile;
	global $wpUploadDescription, $wpIgnoreWarning;
	global $wpUploadSaveName, $wpUploadTempName, $wpUploadSize;

	$sub = wfMsg( "uploadwarning" );
	$wgOut->addHTML( "<h2>{$sub}</h2>\n" );
	$wgOut->addHTML( "<h4><font color=red>{$warning}</font></h4>\n" );

	$save = wfMsg( "savefile" );
	$reupload = wfMsg( "reupload" );
	$iw = wfMsg( "ignorewarning" );
	$reup = wfMsg( "reuploaddesc" );
	$action = wfLocalLink( "Special:Upload" );

	$wgOut->addHTML( "
<form method=post enctype='multipart/form-data' action='{$action}'>
<input type=hidden name='wpUploadAffirm' value='1'>
<input type=hidden name='wpIgnoreWarning' value='1'>
<input type=hidden name='wpUploadDescription' value='$wpUploadDescription'>
<input type=hidden name='wpUploadSaveName' value='$wpUploadSaveName'>
<input type=hidden name='wpUploadTempName' value='$wpUploadTempName'>
<input type=hidden name='wpUploadSize' value='$wpUploadSize'>
<table border=0><tr>
<tr><td align=right>
<input tabindex=2 type=submit name='wpUpload' value='{$save}'>
</td><td align=left>{$iw}</td></tr>
<tr><td align=right>
<input tabindex=2 type=submit name='wpReUpload' value='{$reupload}'>
</td><td align=left>{$reup}</td></tr></table></form>\n" );
}

function mainUploadForm( $msg )
{
	global $wgOut, $wgUser, $wgUploadDirectory;
	global $wpUpload, $wpUploadAffirm, $wpUploadFile;
	global $wpUploadDescription, $wpIgnoreWarning;

	if ( "" != $msg ) {
		$sub = wfMsg( "uploaderror" );
		$wgOut->addHTML( "<h2>{$sub}</h2>\n" .
		  "<h4><font color=red>{$msg}</font></h4>\n" );
	} else {
		$sub = wfMsg( "uploadfile" );
		$wgOut->addHTML( "<h2>{$sub}</h2>\n" );
	}
	$wgOut->addHTML( "<p>" . wfMsg( "uploadtext" ) );
	$sk = $wgUser->getSkin();

	$link = $sk->makeKnownLink( "Wikipedia:Upload log",
	  wfMsg( "uploadlog" ) );
	$ult = str_replace( "$1", $link, wfMsg( "uploadlogtext" ) );
	$wgOut->addHTML( "\n<p>{$ult}\n" );

	$fn = wfMsg( "filename" );
	$fd = wfMsg( "filedesc" );
	$ulb = wfMsg( "upload" );

	$clink = $sk->makeKnownLink( wfMsg( "copyrightpage" ),
	  wfMsg( "copyrightpagename" ) );
	$ca = str_replace( "$1", $clink, wfMsg( "affirmation" ) );
	$iw = wfMsg( "ignorewarning" );

	$action = wfLocalLink( "Special:Upload" );
	$wgOut->addHTML( "
<form method=post enctype='multipart/form-data' action='{$action}'>
<table border=0><tr>
<td align=right>{$fn}:</td><td align=left>
<input tabindex=1 type='file' name='wpUploadFile' value='{$wpUploadFile}' size=40>
</td></tr><tr>
<td align=right>{$fd}:</td><td align=left>
<input tabindex=2 type=text name='wpUploadDescription' value='{$wpUploadDescription}' size=40>
</td></tr><tr>
<td align=right>
<input tabindex=3 type=checkbox name='wpUploadAffirm' value='1'>
</td><td align=left>{$ca}</td></tr>
<tr><td>&nbsp;</td><td align=left>
<input tabindex=5 type=submit name='wpUpload' value='{$ulb}'>
</td></tr></table></form>\n" );
}

?>
