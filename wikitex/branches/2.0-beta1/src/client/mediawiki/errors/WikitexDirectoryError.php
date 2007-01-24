<?php
  /**
   * WikiTeX: expansible LaTeX module for MediaWiki
   * Copyright(C) 2004-7 Peter Danenberg
   * See doc/COPYING for details.
   */

class WikitexDirectoryError extends WikitexError
{
  public function WikitexDirectoryError($dir) {
    list($strerror, $errno) = WikitexConstants::$ERRORS['directory'];
    parent::__construct(sprintf($strerror, $dir), $errno);
  }
}

?>
  