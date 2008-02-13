<?php
/*  Comment pages for main namespace pages
**  Originally designed for Wikinews
**  By [[User:Zachary]]
**  Released under the GPL
**/

require_once( 'CommentPages.i18n.php' );

$wgExtensionFunctions[] = 'wfCommentPages';
$wgExtensionCredits['other'][] = array(
    'name'        => '[http://www.mediawiki.org/wiki/User:Zachary/CommentPages CommentPages]',
    'author'      => '[http://en.wikinews.org/wiki/User:Zachary Zachary Hauri]',
    'description' => 'Comment pages for main namespace',
);

function wfCommentPages ()
{
    # Add messages
    global $wgMessageCache, $wgCommentsPagesMessages;
    foreach( $wgCommentsPagesMessages as $key => $value ) {
        $wgMessageCache->addMessages( $wgCommentsPagesMessages[$key], $key );
    }

    global $wgHooks;
    $wgHooks['SkinTemplateTabs'][]  = 'wfCommentPagesSkinTemplateTabs';
}

function wfCommentPagesSkinTemplateTabs ( &$skin, &$content_actions )
{
    global $wgContLang, $wgCommentPagesNS;
    $pagename = $skin->mTitle->getText();
    $namespace = $skin->mTitle->getNamespace();
    $class = '';
    $page = '';
    if ($namespace == 0 || $namespace == 1) {
        $comments = Title::newFromText($wgContLang->getNSText($wgCommentPagesNS).':'.$pagename);
        $newcontent_actions = array();
        if (!$comments->exists()) {
            $class = 'new';
            $query = 'action=edit';
            if (wfMsg('commenttab-preload') != '') {
                $query .= '&preload='.wfMsg('commenttab-preload');
            }
            if (wfMsg('commenttab-editintro') != '') {
                $query .= '&editintro='.wfMsg('commenttab-editintro');
            }
        } else {
            $class = '';
        }
        foreach ($content_actions as $key => $value) {
            // Insert the comment tab before the edit link
            if ($key == 'edit') {
                $newcontent_actions['comments'] = array(
                    'class' => $class,
                    'text'  => wfMsg('nstab-comments'),
                    'href'  => $comments->getFullURL($query),
                );
            }
            $newcontent_actions[$key] = $value;
        }
        $content_actions = $newcontent_actions;
    } elseif ($skin->mTitle->getNamespace() == $wgCommentPagesNS) {
        $main = Title::newFromText($pagename);
        $talk = $main->getTalkPage();
        $newcontent_actions = array();
        if (!$main->exists()) {
            $class = 'new';
            $query = 'action=edit';
        } else {
            $class = '';
            $query = '';
        }
        $newcontent_actions['article'] = array(
            'class' => $class,
            'text'  => wfMsg( 'nstab-main' ),
            'href'  => $main->getFullURL($query),
        );
        if (!$talk->exists()) {
            $class = 'new';
            $query = 'action=edit';
        } else {
            $class = '';
            $query = '';
        }
        $newcontent_actions['talk'] = array(
            'class' => $class,
            'text'  => wfMsg( 'talk' ),
            'href'  => $talk->getFullURL($query),
        );
        foreach ($content_actions as $key => $value) {
            if ($key != 'talk') {
                $newcontent_actions[$key] = $value;
            }
        }
        $content_actions = $newcontent_actions;
    }
    return true;
}

