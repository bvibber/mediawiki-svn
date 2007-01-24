<?php
  /**
   * WikiTeX: expansible LaTeX module for MediaWiki
   * Copyright(C) 2004-7 Peter Danenberg
   * See doc/COPYING for details.
   */

  /**
   * In case of blank or invalid documents for the RPC-server.
   */
class WikitexRPCResponseError extends WikitexError
{
  public function WikitexRPCResponseError() {
    list($strerror, $errno) = WikitexConstants::$ERRORS['blank'];
    parent::__construct($strerror, $errno);
  }
}

?>
  