<?PHP

global $wgHooks;
$wgHooks['ParserBeforeTidy'][] = 'beforeTidyHook' ;

$wgExtensionFunctions[] = 'registerWikiwygExtension';
$wgExtensionCredits['other'][] = array(
    'name' => 'MediaWikiWyg',
    'author' => 'http://svn.wikiwyg.net/code/trunk/wikiwyg/AUTHORS',
    'version' => 0.10,
    'url' => 'http://www.wikiwyg.net',
    'description' => 'Mediawiki integration of the Wikiwyg WYSIWYG wiki editor'
);

function registerWikiwygExtension() {
    global $wgOut,$wgSkin,$jsdir,$cssdir;
    global $wgWikiwygPath;
    global $wgServer,$wgWikiwygJsPath,$wgWikiwygCssPath,$wgWikiwygImagePath;

    if (! isset($wgWikiwygPath)) {
        $wgWikiwygPath = "$wgServer/wikiwyg";
    }
    if (! isset($wgWikiwygJsPath)) {
        $wgWikiwygJsPath = "$wgWikiwygPath/share/MediaWiki";
    }
    if (! isset($wgWikiwygCssPath)) {
        $wgWikiwygCssPath = "$wgWikiwygPath/share/MediaWiki/css";
    }
    if (! isset($wgWikiwygImagePath)) {
        $wgWikiwygImagePath = "$wgWikiwygPath/share/MediaWiki/images";
    }
    $wgOut->addScript("<style type=\"text/css\" media=\"screen,projection\">/*<![CDATA[*/ @import \"$wgWikiwygCssPath/MediaWikiwyg.css\"; /*]]>*/</style>\n");
    $wgOut->addScript("
<script type=\"text/javascript\">
    if (typeof(Wikiwyg) == 'undefined') Wikiwyg = function() {};
    Wikiwyg.mediawiki_source_path = \"$wgWikiwygPath\";
</script>
");
    $wgOut->addScript("<script type=\"text/javascript\" src=\"$wgWikiwygJsPath/MediaWikiWyg.js\"></script>\n");
}

function beforeTidyHook($parser,$text) {
    $blocks = preg_split(
        '/(<div class="editsection".*?<\/div>)/i',
        $text, -1, PREG_SPLIT_DELIM_CAPTURE
    );

    $i = 0;
		
    $full = array_shift($blocks);
    foreach ($blocks as $block) {
        # This is an edit link
        if (preg_match('/<div class="editsection".*?<\/div>/i', $block)) {
            $i++;
	    $full .= "<span class='wikiwyg_edit' id=\"wikiwyg_edit_{$i}\">
$block
</span>
";
        }
        # This is a section body
        else {
            if ($i == 0) {
                die("Wrong order!");
            }
            $full .= "
<div class=\"wikiwyg_section\" id=\"wikiwyg_section_{$i}\">
$block
</div>
<iframe class='wikiwyg_iframe'
        id=\"wikiwyg_iframe_{$i}\"
        height='0' width='0' 
        frameborder='0'>
</iframe>
";
        }
    }
    $text = $full;
}

# Not a valid entry point, skip unless MEDIAWIKI is defined
if (defined('MEDIAWIKI')) {
$wgExtensionFunctions[] = 'wfEZParser';

$wgAvailableRights[] = 'ezparser';

$wgGroupPermissions['ezparser']['ezparser'] = true;

function wfEZParser() {
global $IP;
require_once( $IP.'/includes/SpecialPage.php' );

#class EZParser extends UnlistedSpecialPage
class EZParser extends SpecialPage
{
	function EZParser() {
#		UnlistedSpecialPage::UnlistedSpecialPage('EZParser');
		SpecialPage::SpecialPage('EZParser');
	}

	function execute( $par ) {
		global $wgRequest, $wgOut, $wgTitle, $wgUser;
		
		if (!in_array( 'ezparser', $wgUser->getRights() ) ) {
			$wgOut->setArticleRelated( false );
			$wgOut->setRobotpolicy( 'noindex,follow' );
			$wgOut->errorpage( 'nosuchspecialpage', 'nospecialpagetext' );
			return;
		}

		$this->setHeaders();

		$text = $wgRequest->getText( 'text' );

		if ( $text ) {
			$this->parseText( $text );
		}
		else{
		  $this->addForm();
		}
	}

	function parseText($text){
	  #still need to make it actually parse the input.
	  global $wgOut, $wgUser, $wgTitle, $wgParser, $wgAllowDiffPreview, $wgEnableDiffPreviewPreference;
$parserOptions = ParserOptions::newFromUser( $wgUser );
	  $parserOptions->setEditSection( false );
	  $output = $wgParser->parse( $text, $wgTitle, $parserOptions );
	  $wgOut->setArticleBodyOnly( true );

# Here we filter the output. If there's a secion header in the beginning,
# we'll have an empty wikiwyg_section_0 div, and we do not want it.
# So we strip the empty div out.

          $goodHTML = str_replace("<div class=\"wikiwyg_section_0\">\n<p><!-- before block -->\n</p><p><br />\n</p><p><!-- After block -->\n</p>\n</div><iframe class=\"wikiwyg_iframe\" id=\"wikiwyg_iframe_0\" height='0' width='0' frameborder='0'></iframe>", "", $output->mText);

          $wgOut->addHTML($goodHTML);

	}

	function addForm(){
		global $wgOut, $wgTitle;

		$action = $wgTitle->escapeLocalUrl();

		$wgOut->addHTML( <<<EOF
<form name="ezparser" action="$action" method=post>
<textarea name="text">
enter wikitext here
</textarea>
<input type="submit" name="submit" value="OK" />
</form>
EOF
		);
	}
}

global $wgMessageCache;
SpecialPage::addPage( new EZParser );
$wgMessageCache->addMessage( 'ezparser', 'Simple parser test' );

}
} # End if(defined MEDIAWIKI)

?>
