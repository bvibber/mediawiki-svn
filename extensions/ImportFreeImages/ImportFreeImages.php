<?php
if ( ! defined( 'MEDIAWIKI' ) )
    die();

/**#@+
 *  Provides a way of importing properly licensed photos from flickr
 * 
 * @package MediaWiki
 * @subpackage Extensions
 *
 * @link http://www.mediawiki.org/wiki/Extension:ImportFreeImages Documentation
 *
 *
 * @author Travis Derouin <travis@wikihow.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

$wgExtensionFunctions[] = 'wfImportFreeImages';
$wgIFI_FlickrAPIKey  = '';
$wgIFI_CreditsTemplate = 'flickr'; // use this to format the image content with some key parameters

$wgIFI_ResultsPerPage = 20;
$wgIFI_ResultsPerRow = 5;
// see the flickr api page for more information on these params
// for licnese info http://www.flickr.com/services/api/flickr.photos.licenses.getInfo.html
// default 4 is CC Attribution License
$wgIFI_FlickrLicense = 4;
$wgIFI_FlickrSort = "interestingness-desc";
require_once("SpecialPage.php");




$wgExtensionCredits['other'][] = array(
    'name' => 'ImportFreeImages',
    'author' => 'Travis Derouin',
    'description' => 'Provides a way of importing properly licensed photos from flickr.',
    'url' => 'http://www.mediawiki.org/wiki/Extension:ImportFreeImages',
);


function wfImportFreeImages() {


	SpecialPage::AddPage(new SpecialPage('ImportFreeImages'));

    global $wgMessageCache;
     $wgMessageCache->addMessages(     array(
			'importfreeimages' => 'Import Free Images',
			'importfreeimages_description' => 'This page allows you to search properly licensed photos from flickr and import them into your wiki.',	
			'importfreeimages_noapikey' => 'You have not configured your Flickr API Key. To do so, please obtain a API key from  [http://www.flickr.com/services/api/misc.api_keys.html here] and set wgFlickrAPIKey in ImportFreeImages.php.',
			'importfreeimages_nophotosfound' => 'No photos were found for your search criteria \'$1\', please try again.',
			'importfreeimages_owner' => 'Author',
			'importfreeimages_importthis' => 'import this',
			'importfreeimages_next' => 'Next $1',
			'importfreeimages_filefromflickr' => '$1 by user <b>[$2]</b> from flickr. Original URL',
        )
    );


}


function wfSpecialImportFreeImages( $par )
{
	global $wgUser, $wgOut, $wgScriptPath, $wgRequest, $wgLang, $wgIFI_FlickrAPIKey, $wgTmpDirectory;
	global $wgIFI_ResultsPerPage, $wgIFI_FlickrSort, $wgIFI_FlickrLicense, $wgIFI_ResultsPerRow, $wgIFI_CreditsTemplate;
	
	$fname = "wfSpecialImportFreeImages";
	$importPage = Title::makeTitle(NS_SPECIAL, "ImportFreeImages");

    if( $wgUser->isAnon() ) {
        $wgOut->showErrorPage( 'uploadnologin', 'uploadnologintext' );
        return;
     } 

	if (empty($wgIFI_FlickrAPIKey)) {
		// error - need to set $wgIFI_FlickrAPIKey to use this extension
		$wgOut->showErrorPage('error', 'importfreeimages_noapikey');
		return;
	}	
	$q = '';	
	if (isset($_GET['q']) && !$wgRequest->wasPosted() ) {
		$q = $_GET['q'];
	}

	$wgOut->addHTML(wfMsg ('importfreeimages_description') . "<br/><br/>
		<form method=GET action='" . $importPage->getFullURL() . "'>".wfMsg('search').
		": <input type=text name=q value='" . htmlspecialchars($q) . "'><input type=submit value=".wfMsg('search')."></form>");

	$import = '';
	if ($wgRequest->wasPosted() && isset($_POST['url'])) {
		$import = $_POST['url'];
		if (strpos($import, "http://static.flickr.com/") !== 0) {
			// avoid hack attempts
			 echo "not supported.";
			exit;
		}
		$pageContents = file_get_contents($import); 	
		$name =$wgTmpDirectory . "/flickr-" . rand(0,999999);
		$r = fopen($name, "w");
		$size = fwrite ( $r, $pageContents);	
		fclose($r);
		chmod( $name, 0777 );
		if (!empty($wgIFI_CreditsTemplate)) {
			$caption = "{{" . $wgIFI_CreditsTemplate . "|{$_POST['id']}|" . urldecode($_POST['owner']) . "|" . urldecode($_POST['name']). "}}";
		} else {
			$caption = wfMsg('importfreeimages_filefromflickr', $_POST['t'], "http://www.flickr.com/people/" . urlencode($_POST['owner']) . " " . $_POST['name']) . " <nowiki>$import</nowiki>. {{CC by 2.0}} ";
		}
		$caption = trim($caption);
		$t = $_POST['title'];

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
		$u = new UploadForm($wgRequest);
        $u->mUploadTempName = $name;
        $u->mUploadSize     = $size; 
		$u->mUploadDescription = $caption;
		$u->mRemoveTempFile = true;
		$u->mIgnoreWarning =  true;
        $u->mOname = urldecode($_POST['title']) . ".jpg";

		$u->execute();
	}
	if ($q != '') { 
		$page = $_GET['p'];
		if ($page == '') $page = 1;
        	require_once("phpFlickr-2.0.0/phpFlickr.php");
        	$f = new phpFlickr($wgIFI_FlickrAPIKey);
        	$q = $_GET['q'];
		// TODO: get the right licenses
        	$photos = $f->photos_search(array(
				"tags"=>"$q", "tag_mode"=>"any", 
				"page" => $page, 
				"per_page" => $wgIFI_ResultsPerPage, "license" => $wgIFI_FlickrLicense, 
				"sort" => $wgIFI_FlickrSort  ));

		$i = 0;
		if ($photos == null || !is_array($photos) || sizeof($photos) == 0 || !isset($photos['photo']) ) {
			$wgOut->addHTML(wfMsg("importfreeimages_nophotosfound",$q));
			return;
		}
		$sk = $wgUser->getSkin();

		$wgOut->addHTML("<table cellpadding=4>
			<form method='POST' name='uploadphotoform' action='" . $importPage->getFullURL() . "'>
				<input type=hidden name='url' value=''>
				<input type=hidden name='id' value=''>
				<input type=hidden name='action' value='submit'>
				<input type=hidden name='owner' value=''>
				<input type=hidden name='name' value=''>
				<input type=hidden name='title' value=''>

	<script type=\"text/javascript\">

		function s2 (url, id, owner, name, title) {
			document.uploadphotoform.url.value = url;
			document.uploadphotoform.id.value = id;
			document.uploadphotoform.owner.value = owner;
			document.uploadphotoform.name.value = name;
			document.uploadphotoform.title.value = title;
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
                	$url="http://static.flickr.com/" . $photo['server'] . "/" . $photo['id'] . "_" . $photo['secret'] . "." . "jpg";
                	$wgOut->addHTML( "<img src=http://static.flickr.com/" . $photo['server'] . "/" . $photo['id'] . "_" . $photo['secret'] . "_s." . "jpg>" );
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
?>
