<?php
if ( ! defined( 'MEDIAWIKI' ) )
    die();

/**#@+
 *  Provides a way of importing properly licensed photos from flickr
 * 
 * @addtogroup Extensions
 *
 * @link http://www.mediawiki.org/wiki/Extension:ImportFreeImages Documentation
 *
 *
 * @author Travis Derouin <travis@wikihow.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

$wgExtensionFunctions[] = 'wfImportFreeImages';
$wgIFI_FlickrAPIKey = '';
$wgIFI_CreditsTemplate = 'flickr'; // use this to format the image content with some key parameters
$wgIFI_GetOriginal = true; // import the original version of the photo
$wgIFI_PromptForFilename = true;  // prompt the user through javascript for the destination filename

$wgIFI_ResultsPerPage = 20;
$wgIFI_ResultsPerRow = 4;
// see the flickr api page for more information on these params
// for licnese info http://www.flickr.com/services/api/flickr.photos.licenses.getInfo.html
// default 4 is CC Attribution License
$wgIFI_FlickrLicense = "4,5";
$wgIFI_FlickrSort = "interestingness-desc";
$wgIFI_FlickrSearchBy = "tags"; // Can be tags or text. See http://www.flickr.com/services/api/flickr.photos.search.html
$wgIFI_AppendRandomNumber = true; /// append random # to destination filename
$wgIFI_ThumbType = "t"; // s for square t for thumbnail

require_once("SpecialPage.php");

$wgExtensionCredits['other'][] = array(
    'name' => 'ImportFreeImages',
    'author' => 'Travis Derouin',
    'description' => 'Provides a way of importing properly licensed photos from flickr.',
    'url' => 'http://www.mediawiki.org/wiki/Extension:ImportFreeImages',
);

function wfImportFreeImages() {

	SpecialPage::AddPage( new SpecialPage( 'ImportFreeImages' ) );
	require_once( dirname( __FILE__ ) . '/ImportFreeImages.i18n.php' );
	global $wgMessageCache;
	foreach( efImportFreeImagesMessages() as $lang => $messages ) {
		$wgMessageCache->addMessages( $messages, $lang );
	}
}

// I wish I didn't have to copy paste most of 

function wfIIF_uploadWarning($u) {
        global $wgOut;
        global $wgUseCopyrightUpload;

        $u->mSessionKey = $u->stashSession();
        if( !$u->mSessionKey ) {
            # Couldn't save file; an error has been displayed so let's go.
            return;
        }

        $wgOut->addHTML( "<h2>" . wfMsgHtml( 'uploadwarning' ) . "</h2>\n" );
        $wgOut->addHTML( "<ul class='warning'>{$warning}</ul><br />\n" );

        $save = wfMsgHtml( 'savefile' );
        $reupload = wfMsgHtml( 'reupload' );
        $iw = wfMsgWikiHtml( 'ignorewarning' );
        $reup = wfMsgWikiHtml( 'reuploaddesc' );
        $titleObj = Title::makeTitle( NS_SPECIAL, 'Upload' );
        $action = $titleObj->escapeLocalURL( 'action=submit' );
        if ( $wgUseCopyrightUpload )
        {
            $copyright =  "
    <input type='hidden' name='wpUploadCopyStatus' value=\"" . htmlspecialchars( $u->mUploadCopyStatus ) . "\" />
    <input type='hidden' name='wpUploadSource' value=\"" . htmlspecialchars( $u->mUploadSource ) . "\" />
    ";
        } else {
            $copyright = "";
        }

        $wgOut->addHTML( "
    <form id='uploadwarning' method='post' enctype='multipart/form-data' action='$action'>
        <input type='hidden' name='wpIgnoreWarning' value='1' />
        <input type='hidden' name='wpSessionKey' value=\"" . htmlspecialchars( $u->mSessionKey ) . "\" />
        <input type='hidden' name='wpUploadDescription' value=\"" . htmlspecialchars( $u->mUploadDescription ) . "\" />
        <input type='hidden' name='wpLicense' value=\"" . htmlspecialchars( $u->mLicense ) . "\" />
        <input type='hidden' name='wpDestFile' value=\"" . htmlspecialchars( $u->mDestFile ) . "\" />
        <input type='hidden' name='wpWatchu' value=\"" . htmlspecialchars( intval( $u->mWatchu ) ) . "\" />
    {$copyright}
    <table border='0'>
        <tr>
            <tr>
                <td align='right'>
                    <input tabindex='2' type='submit' name='wpUpload' value=\"$save\" />
                </td>
                <td align='left'>$iw</td>
            </tr>
        </tr>
    </table></form>\n" . wfMsg('importfreeimages_returntoform',  $_SERVER["HTTP_REFERER"]) );
//  $_SERVER["HTTP_REFERER"]; -- javascript.back wasn't working for some reason... hmph.

}

function wfSpecialImportFreeImages( $par )
{
	global $wgUser, $wgOut, $wgScriptPath, $wgRequest, $wgLang, $wgIFI_FlickrAPIKey, $wgTmpDirectory;
	global $wgIFI_ResultsPerPage, $wgIFI_FlickrSort, $wgIFI_FlickrLicense, $wgIFI_ResultsPerRow, $wgIFI_CreditsTemplate;
	global $wgIFI_GetOriginal, $wgIFI_PromptForFilename, $wgIFI_AppendRandomNumber, $wgIFI_FlickrSearchBy, $wgIFI_ThumbType;
	require_once("phpFlickr-2.0.0/phpFlickr.php");
	
	$fname = "wfSpecialImportFreeImages";
	$importPage = Title::makeTitle(NS_SPECIAL, "ImportFreeImages");

    if( $wgUser->isAnon() ) {
        $wgOut->errorpage( 'uploadnologin', 'uploadnologintext' );
        return;
     } 

	if (empty($wgIFI_FlickrAPIKey)) {
		// error - need to set $wgIFI_FlickrAPIKey to use this extension
		$wgOut->errorpage('error', 'importfreeimages_noapikey');
		return;
	}	
	$q = '';	
	if (isset($_GET['q']) && !$wgRequest->wasPosted() ) {
		$q = $_GET['q'];
	}


	$import = '';
	if ($wgRequest->wasPosted() && isset($_POST['url'])) {
		$import = $_POST['url'];
        if (!preg_match('/^http:\/\/farm[0-9]+.static.flickr.com/', $import)) {
            $wgOut->errorpage('error', 'importfreeimages_invalidurl');           
		 	return;
        }
	
       	$f = new phpFlickr($wgIFI_FlickrAPIKey);
		
		if ($wgIFI_GetOriginal) {
			// get URL of original :1
	
			$sizes = $f->photos_getSizes($_POST['id']);
			$original = '';
			foreach ($sizes as $size) {
				if ($size['label'] == 'Original') {
					$original = $size['source'];
					$import = $size['source'];
				} else if ($size['label'] == 'Large') {
					$large = $size['source'];
				}
			}
			//somtimes Large is returned but no Original!
			if ($original == '' && $large != '') 
				$import = $large; 
		}

		// store the contents of the file
		$pageContents = file_get_contents($import); 	
		$name =$wgTmpDirectory . "/flickr-" . rand(0,999999);
		$r = fopen($name, "w");
		$size = fwrite ( $r, $pageContents);	
		fclose($r);
		chmod( $name, 0777 );
		$info = $f->photos_getInfo($_POST['id']);
	
		if (!empty($wgIFI_CreditsTemplate)) {
			$caption = "{{" . $wgIFI_CreditsTemplate . $info['license'] . "|{$_POST['id']}|" . urldecode($_POST['owner']) . "|" . urldecode($_POST['name']). "}}";
		} else {
			$caption = wfMsg('importfreeimages_filefromflickr', $_POST['t'], "http://www.flickr.com/people/" . urlencode($_POST['owner']) . " " . $_POST['name']) . " <nowiki>$import</nowiki>. {{CC by 2.0}} ";
		}
		$caption = trim($caption);
		$t = $_POST['ititle'];

		// handle duplicate filenames
		$i = strrpos($import, "/");
		if ($i !== false) {
			$import = substr($import, $i + 1);
		}

		// pretty dumb way to make sure we're not overwriting previously uploaded images
		$c = 0;
		$nt =& Title::makeTitle( NS_IMAGE, $import);
		$fname = $import;
		while( $nt->getArticleID() && $c < 20) {
			$fname = $c . "_" . $import;
			$nt =& Title::makeTitle( NS_IMAGE, $fname);
			$c++;
		}
		$import = $fname;

/*
		$arr = array ( "size" => $size, "tempname" => $name, 
				"caption" => $caption,
				"url" => $import, "title" => $_POST['t'] );
*/
		$filename = urldecode($_POST['ititle']) . ($wgIFI_AppendRandomNumber ? "-" . rand(0, 9999) : "") . ".jpg";
		$filename = str_replace("?", "", $filename);
		$filename = str_replace(":", "", $filename);
		$filename = preg_replace('/ [ ]*/', ' ', $filename);

		if (!class_exists("UploadForm")) 
			require_once('includes/SpecialUpload.php');
		$u = new UploadForm($wgRequest);
        $u->mUploadTempName = $name;
        $u->mUploadSize     = $size; 
		$u->mUploadDescription = $caption;
		$u->mRemoveTempFile = true;
		$u->mIgnoreWarning =  true;
        $u->mOname = $filename;
		$t = Title::newFromText($filename, NS_IMAGE);
		if ($t->getArticleID() > 0) {
			$sk = $wgUser->getSkin();
           	$dlink = $sk->makeKnownLinkObj( $t );
            $warning .= '<li>'.wfMsgHtml( 'fileexists', $dlink ).'</li>';
			
			// use our own upload warning as we dont have a 'reupload' feature
			wfIIF_uploadWarning	($u);
			return;
		} else {
			$u->execute();
		}
	}


	$wgOut->addHTML(wfMsg ('importfreeimages_description') . "<br/><br/>
		<form method=GET action='" . $importPage->getFullURL() . "'>".wfMsg('search').
		": <input type=text name=q value='" . htmlspecialchars($q) . "'><input type=submit value=".wfMsg('search')."></form>");

	if ($q != '') { 
		$page = $_GET['p'];
		if ($page == '') $page = 1;
        	$f = new phpFlickr($wgIFI_FlickrAPIKey);
        	$q = $_GET['q'];
		// TODO: get the right licenses
        	$photos = $f->photos_search(array(
				"$wgIFI_FlickrSearchBy"=>"$q", "tag_mode"=>"any", 
				"page" => $page, 
				"per_page" => $wgIFI_ResultsPerPage, "license" => $wgIFI_FlickrLicense, 
				"sort" => $wgIFI_FlickrSort  ));

		$i = 0;
		if ($photos == null || !is_array($photos) || sizeof($photos) == 0 || !isset($photos['photo']) ) {
			$wgOut->addHTML(wfMsg("importfreeimages_nophotosfound",$q));
			return;
		}
		$sk = $wgUser->getSkin();

		$wgOut->addHTML("
			<table cellpadding=4>
			<form method='POST' name='uploadphotoform' action='" . $importPage->getFullURL() . "'>
				<input type=hidden name='url' value=''>
				<input type=hidden name='id' value=''>
				<input type=hidden name='action' value='submit'>
				<input type=hidden name='owner' value=''>
				<input type=hidden name='name' value=''>
				<input type=hidden name='ititle' value=''>

	<script type=\"text/javascript\">

		function s2 (url, id, owner, name, ititle) {
			document.uploadphotoform.url.value = url;
			document.uploadphotoform.id.value = id;
			document.uploadphotoform.owner.value = owner;
			document.uploadphotoform.name.value = name;
			document.uploadphotoform.ititle.value = ititle;
			if (" . ($wgIFI_PromptForFilename ? "true" : "false") . ") {
				ititle = ititle.replace(/\+/g, ' ');
				document.uploadphotoform.ititle.value = prompt('" . wfMsg('importfreeimages_promptuserforfilename') . "', unescape(ititle));
				if (document.uploadphotoform.ititle.value == '') {
					document.uploadphotoform.ititle.value = ititle;
				}
			}
			document.uploadphotoform.submit();
		}

	</script>
 
			");
        	foreach ($photos['photo'] as $photo) {
			if ($i % $wgIFI_ResultsPerRow == 0) $wgOut->addHTML("<tr>");
                	$owner = $f->people_getInfo($photo['owner']);
                	$wgOut->addHTML( "<td align=center style='padding-top: 15px; border-bottom: 1px solid #ccc;'><font size=-2><a href='http://www.flickr.com/photos/" . $photo['owner'] . "/" . $photo['id'] . "/'>" );
                	$wgOut->addHTML( $photo['title'] );
                	$wgOut->addHTML( "</a><br/>".wfMsg('importfreeimages_owner').": " );
                	$wgOut->addHTML( "<a href='http://www.flickr.com/people/" . $photo['owner'] . "/'>") ;
                	$wgOut->addHTML( $owner['username'] );
                	$wgOut->addHTML( "</a><br/>" );
                	//$wgOut->addHTML( "<img  src=http://static.flickr.com/" . $photo['server'] . "/" . $photo['id'] . "_" . $photo['secret'] . "." . "jpg>" );
					$url="http://farm{$photo['farm']}.static.flickr.com/{$photo['server']}/{$photo['id']}_{$photo['secret']}.jpg";
                    $wgOut->addHTML( "<img src=\"http://farm{$photo['farm']}.static.flickr.com/{$photo['server']}/{$photo['id']}_{$photo['secret']}_{$wgIFI_ThumbType}.jpg\">" );     

			$wgOut->addHTML( "<br/>(<a href='#' onclick=\"s2('$url', '{$photo['id']}','{$photo['owner']}', '" 
						. urlencode($owner['username']  ) . "', '" . urlencode($photo['title']) . "');\">" . 
								wfMsg('importfreeimages_importthis') . "</a>)\n" );
			$wgOut->addHTML("</td>");
			if ($i % $wgIFI_ResultsPerRow == ($wgIFI_ResultsPerRow - 1) ) $wgOut->addHTML("</tr>");
			$i++;
		}
		$wgOut->addHTML("</form></table>");
		$page = $page + 1;

		$wgOut->addHTML("<br/>" .  $sk->makeLinkObj($importPage, wfMsg('importfreeimages_next', $wgIFI_ResultsPerPage), "p=$page&q=" . urlencode($q) ) );
                //print_r($photo);
	}
}
