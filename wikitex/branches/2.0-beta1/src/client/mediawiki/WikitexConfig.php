<?php
  /**
   * WikiTeX: expansible LaTeX module for MediaWiki
   * Copyright(C) 2004-7 Peter Danenberg
   * See doc/COPYING for details.
   */

$LOCAL = WikitexConstants::getLocalConfig();
$DEFAULT = WikitexConstants::getDefaultConfig();

if (file_exists($LOCAL)) {
  require_once $LOCAL;
 } else {
  require_once $DEFAULT;
 }

?>
