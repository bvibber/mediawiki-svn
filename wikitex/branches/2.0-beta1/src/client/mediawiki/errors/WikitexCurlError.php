<?php
  /**
   * WikiTeX: expansible LaTeX module for MediaWiki
   * Copyright(C) 2004-7 Peter Danenberg
   * See doc/COPYING for details.
   */

class WikitexCurlError extends WikitexError
{
  public function WikitexCurlError($curl_strerr, $curl_errno) {
    list($strerror, $errno) = WikitexConstants::$ERRORS['curl'];
    parent::__construct(sprintf($strerror, $curl_errno, $curl_strerr), $errno);
  }
}

?>
