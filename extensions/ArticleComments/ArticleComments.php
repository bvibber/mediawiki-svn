<?php //{{MediaWikiExtension}}<source lang="php">
/*
 * ArticleComments.php - A MediaWiki extension for adding comment sections to articles.
 * @author Jim R. Wilson
 * @version 0.1
 * @copyright Copyright (C) 2007 Jim R. Wilson
 * @license The MIT License - http://www.opensource.org/licenses/mit-license.php 
 * -----------------------------------------------------------------------
 * Description:
 *     This is a MediaWiki (http://www.mediawiki.org/) extension which adds support
 *     for comment sections within article pages
 * Requirements:
 *     This extension is made to work with MediaWiki 1.6.x, 1.8.x or 1.9.x running against
 *     PHP 4.3.x, 5.x or higher.
 * Installation:
 *     1. Drop this script (ArticleComments.php) in $IP/extensions
 *         Note: $IP is your MediaWiki install dir.
 *     2. Enable the extension by adding this line to your LocalSettings.php:
 *            require_once('extensions/ArticleComments.php');
 * Usage:
 *     Once installed, you may utilize ArticleComments by adding the following flag in the article text:
 *         <comments />
 *     Note: Typically this would be placed at the end of the article text.
 * -----------------------------------------------------------------------
 * Copyright (c) 2007 Jim R. Wilson
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy 
 * of this software and associated documentation files (the "Software"), to deal 
 * in the Software without restriction, including without limitation the rights to 
 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of 
 * the Software, and to permit persons to whom the Software is furnished to do 
 * so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all 
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, 
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES 
 * OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND 
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT 
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, 
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING 
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR 
 * OTHER DEALINGS IN THE SOFTWARE. 
 * -----------------------------------------------------------------------
 */
 
# Confirm MW environment
if (!defined('MEDIAWIKI')) die();

# Credits
$wgExtensionCredits['other'][] = array(
    'name'=>'ArticleComments',
    'author'=>'Jim R. Wilson - wilson.jim.r &lt;at&gt; gmail.com',
    'url'=>'http://jimbojw.com/wiki/index.php?title=ArticleComments',
    'description'=>'Enables comment sections on article pages.',
    'version'=>'0.1'
);

# Add Extension Functions
$wgExtensionFunctions[] = 'wfArticleCommentsParserSetup';

# Sets up the ArticleComments Parser hook for <comments />
function wfArticleCommentsParserSetup() {
    global $wgParser;
    $wgParser->setHook( 'comments', 'wfArticleCommentsParserHook' );
}
function wfArticleCommentsParserHook( $text, $params = array(), &$parser ) {

    global $wgScript;

    $articleTitle = $parser->mTitle;

    # Build out the comment form.
    $content =  '<div id="commentForm">';
    $content .= '<form method="post" action="'.$wgScript.'?title=Special:ProcessComment">';
    $content .= '<input type="hidden" id="titleKey" name="titleKey" value="'.$articleTitle->getDBKey().'" />';
    $content .= '<input type="hidden" id="titleNS" name="titleNS" value="'.$articleTitle->getNamespace().'" />';
    $content .= '<p>'.wfMsgForContent('article-comments-name-field').'<br /><input type="text" id="commenterName" name="commenterName" /></p>';
    $content .= '<p>'.wfMsgForContent('article-comments-url-field').'<br /><input type="text" id="commenterURL" name="commenterURL" /></p>';
    $content .= '<p>'.wfMsgForContent('article-comments-comment-field').'<br /><textarea id="comment" name="comment" style="width:30em" rows="5"></textarea></p>';
    $content .= '<p><input id="submit" type="submit" value="'.wfMsgForContent('article-comments-submit-button').'" /></p>';
    $content .= '</form>';
    $content .= '</div>';

    # Inline JavaScript to make form behavior more rich (must degrade gracefully in JS-disabled browsers)
    $content .= '<script type="text/javascript">//<![CDATA['."\n";
    $content .= '(function(){'."\n";

    # Prefill the name field if the user is logged in.
    $content .= 'var prefillUserName = function(){'."\n";
    $content .= 'var ptu=document.getElementById("pt-userpage");'."\n";
    $content .= 'if (ptu) document.getElementById("commenterName").value=';
    $content .= 'ptu.getElementsByTagName("a")[0].innerHTML;'."\n";
    $content .= '};'."\n";
    $content .= 'if (window.addEventListener) window.addEventListener("load",prefillUserName,false);'."\n";
    $content .= 'else if (window.attachEvent) window.attachEvent("onload",prefillUserName);'."\n";

    # Hides the commentForm until the "Make a comment" link is clicked
    $content .= 'var cf=document.getElementById("commentForm");'."\n";
    $content .= 'cf.style.display="none";'."\n";
    $content .= 'var p=document.createElement("p");'."\n";
    $content .= 'p.innerHTML="<a href=\'javascript:void(0)\' onclick=\'';
    $content .= 'document.getElementById(\\"commentForm\\").style.display=\\"block\\";';
    $content .= 'this.style.display=\\"none\\";false';
    $content .= '\'>'.wfMsgForContent('article-comments-leave-comment-link').'</a>";'."\n";
    $content .= 'cf.parentNode.insertBefore(p,cf);'."\n";

    $content .= '})();';
    $content .= '//]]></script>';
    
    # Hide content from the Parser using base64 to avoid mangling.
    # Note: Content will be decoded after Tidy has finished it's processing of the page.
    return '<pre>@ENCODED@'.base64_encode($content).'@ENCODED@</pre>';
}

# Attach Hooks
$wgHooks['ParserAfterTidy'][] = 'wfProcessEncodedContent';

/**
 * Processes HTML comments with encoded content.
 * Usage: $wgHooks['OutputPageBeforeHTML'][] = 'wfProcessEncodedContent';
 * @param $out Handle to an OutputPage object (presumably $wgOut).
 * @param $test Article/Output text.
 */
function wfProcessEncodedContent($out, $text) {
    $text = preg_replace(
    '/<pre>@ENCODED@([0-9a-zA-Z\\+\\/]+=*)@ENCODED@<\\/pre>/e',
    'base64_decode("$1")',
    $text
    );
    return true;
}

# Sets up special page to handle comment submission
$wgExtensionFunctions[] = 'setupSpecialProcessComment';
function setupSpecialProcessComment() {
    global $IP, $wgMessageCache;
    require_once($IP . '/includes/SpecialPage.php');
    SpecialPage::addPage(new SpecialPage('ProcessComment', '', true, 'specialProcessComment', false));

    # Messages used in this extension
    $wgMessageCache->addMessage('article-comments-title-field', 'Title');
    $wgMessageCache->addMessage('article-comments-name-string', 'Name');
    $wgMessageCache->addMessage('article-comments-name-field', 'Name (required): ');
    $wgMessageCache->addMessage('article-comments-url-field', 'Website: ');
    $wgMessageCache->addMessage('article-comments-comment-string', 'Comment');
    $wgMessageCache->addMessage('article-comments-comment-field', 'Comment: ');
    $wgMessageCache->addMessage('article-comments-submit-button', 'Submit');
    $wgMessageCache->addMessage('article-comments-leave-comment-link', 'Leave a comment ...');
    $wgMessageCache->addMessage('article-comments-invalid-field', 'The $1 provided <nowiki>[$2]</nowiki> is invalid.');
    $wgMessageCache->addMessage('article-comments-required-field', '$1 field is required.');
    $wgMessageCache->addMessage('article-comments-submission-failed', 'Comment Submission Failed');
    $wgMessageCache->addMessage('article-comments-failure-reasons', 'Sorry, your comment submission failed for the following reason(s):');
    $wgMessageCache->addMessage('article-comments-no-comments', 'Sorry, the article &quot;[[$1]]&quot; is not accepting comments at this time.');
    $wgMessageCache->addMessage('article-comments-talk-page-starter', "<noinclude>Comments on [[$1]]\n<comments />\n----- __NOEDITSECTION__</noinclude>\n");
    $wgMessageCache->addMessage('article-comments-commenter-said', '$1 said ...');
    $wgMessageCache->addMessage('article-comments-summary', 'Comment provided by $1 - via ArticleComments extension');
    $wgMessageCache->addMessage('article-comments-submission-succeeded', 'Comment submission succeeded');
    $wgMessageCache->addMessage('article-comments-submission-success', 'You have successfully submitted a comment for [[$1]]');
    $wgMessageCache->addMessage('article-comments-submission-view-all', 'You may view all comments on that article [[$1|here]]');
    $wgMessageCache->addMessage('processcomment', 'Process Article Comment');
}

/**
* Special page for comment processing.
*/
function specialProcessComment() {

    global $wgOut, $wgContLang, $wgParser, $wgUser;

    # Check whether user is allowed to add a comment
    # $wgUser->getBlockedStatus()

    # Retrieve submitted values
    $titleKey = $_POST['titleKey'];
    $titleNS = intval($_POST['titleNS']);
    $commenterName = $_POST['commenterName'];
    $commenterURL = $_POST['commenterURL'];
    $comment = $_POST['comment'];

    # Perform validation checks on supplied fields
    $ac = 'article-comments-';
    $messages = array();
    if (!$titleKey) $messages[] = wfMsgForContent(
        $ac.'invalid-field', wfMsgForContent($ac.'title-field'), $titleKey
    );
    if (!$commenterName) $messages[] = wfMsgForContent(
        $ac.'required-field', wfMsgForContent($ac.'name-string'));
    if (!$comment) $messages[] = wfMsgForContent(
        $ac.'required-field', wfMsgForContent($ac.'comment-string'));
    if (!empty($messages)) {
        $wgOut->setPageTitle(wfMsgForContent($ac.'submission-failed'));
        $wikiText = "<div class='errorbox'>";
        $wikiText .= wfMsgForContent($ac.'failure-reasons')."\n\n";
        foreach ($messages as $message) {
            $wikiText .= "* $message\n";
        }
        $wgOut->addWikiText($wikiText . "</div>");
        return;
    }

    # Setup title and talkTitle object
    $title = Title::newFromDBkey($titleKey);
    $title->mNamespace = $titleNS - ($titleNS % 2);
    $article = new Article($title);

    $talkTitle = Title::newFromDBkey($titleKey);
    $talkTitle->mNamespace = $titleNS + 1 - ($titleNS % 2);
    $talkArticle = new Article($talkTitle);

    # Retrieve article content
    $articleContent = '';
    if ( $article->exists() ) {
        $articleContent = $article->getContent();
    }

    # Retrieve existing talk content
    $talkContent = '';
    if ( $talkTitle->exists() ) {
        $talkContent = $talkArticle->getContent();
    }
    
    # Check whether the article or its talk page contains a <comments /> flag
    if (
        strpos($articleContent, '<comments />')===false 
        && strpos($talkContent, '<comments />')===false
    ) {
        $wgOut->setPageTitle(wfMsgForContent($ac.'submission-failed'));
        $wgOut->addWikiText(
            "<div class='errorbox'>".
            wfMsgForContent($ac.'no-comments', $title->getPrefixedText()).
            "</div>"
        );
        return;
    }

    # Initialize the talk page's content.
    if ( $talkContent == '' ) {
        $talkContent = wfMsgForContent($ac.'talk-page-starter', $title->getPrefixedText() );
    }
    
    # Determine signature components
    $d = $wgContLang->timeanddate( date( 'YmdHis' ), false, false) . ' (' . date( 'T' ) . ')';
    if ($commenterURL) $sigText = "[$commenterURL $commenterName]";
    else if ($wgUser->isLoggedIn()) $sigText = $wgParser->getUserSig( $wgUser );
    else $sigText = $commenterName;
 
    # Append most recent comment
    $talkContent .= "\n== ".wfMsgForContent($ac.'commenter-said', $commenterName)." ==\n\n";
    $talkContent .= "<div class='commentBlock'>\n";
    $talkContent .= $comment."\n\n";
    $talkContent .= "--$sigText $d\n";
    $talkContent .= "</div>";

    # Update article
    $summary = wfMsgForContent($ac.'summary', $commenterName);
    if (method_exists($talkArticle, 'doEdit')) {
        $talkArticle->doEdit($talkContent, $summary);
    } else {
        $method = ($talkArticle->exists() ? 'updateArticle' : 'insertNewArticle' );
        $talkArticle->$method($talkContent, $summary, false, false);
        return;
    }

    $wgOut->setPageTitle(wfMsgForContent($ac.'submission-succeeded'));
    $wgOut->addWikiText(wfMsgForContent($ac.'submission-success', $title->getPrefixedText()));
    $wgOut->addWikiText(wfMsgForContent($ac.'submission-view-all', $talkTitle->getPrefixedText()));
}

//</source>
?>