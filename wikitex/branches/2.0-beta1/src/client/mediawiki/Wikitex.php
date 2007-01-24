<?php
  /**
   * WikiTeX: expansible LaTeX module for MediaWiki
   * Copyright(C) 2004-7 Peter Danenberg
   * See doc/COPYING for details.
   */

class Wikitex {
  public static function registerHooks($parser) {
    foreach (WikitexConstants::$HOOKS as $action => $hook) {
      $parser->setHook($action, array(__CLASS__, $hook));
    }
  }

  /**
   * Superclass for all latex-based renderers.
   *
   * Is itself inaccessible from the wiki; must be accessed through its
   * subclasses.
   *
   * @param content the twixt-tag-content.
   * @param parms a dictionary of attribute-value pairs.
   * @param action the remoted render-method to be invoked (probably identical
   * to its local name).
   * @return The rendered content
   */
  public static function latex($content, array $parms, $action=__FUNCTION__) {
    $IMG_MIME = WikitexConstants::$MIMES['png'];
    $SRC_MIME = WikitexConstants::$MIMES['latex'];
    $ERR_MIME = WikitexConstants::$MIMES['error'];

    $request = new WikitexRequest($action, $content);

    try {
      $redditum = $request->render(array($IMG_MIME, $SRC_MIME));
      if (!empty($redditum[$ERR_MIME])) {
        return $redditum[$ERR_MIME];
      }
      return (string) new WikitexImage($redditum[$SRC_MIME],
                                       $redditum[$IMG_MIME]);
    } catch (Exception $e) {
      return (string) new WikitexError($e->getMessage(), $e->getCode());
    }
    return (string) new WikitexError('Bizzarly, execution continued <em>ins Nichts</em>: this shouldn\'t happen');
  }

  public static function math($content, array $parms) {
    return self::latex($content, $parms, WikitexConstants::$METHODS[__FUNCTION__]);
  }
}

?>
