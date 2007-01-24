<?php
  /**
   * WikiTeX: expansible LaTeX module for MediaWiki
   * Copyright(C) 2004-7 Peter Danenberg
   * See doc/COPYING for details.
   */

  /**
   * In case of faults thrown from the RPC-server.
   */
class WikitexRPCFaultError extends WikitexError
{
  const STRING = 'faultString';
  const CODE = 'faultCode';

  /**
   * @param document The parsed rpc-response.
   */
  public function WikitexRPCFaultError($document) {
    list($strerror, $errno) = WikitexConstants::$ERRORS['fault'];
    $fault_strerror = htmlentities($document[self::STRING]);
    $fault_errno = $document[self::CODE];
    parent::__construct(sprintf($strerror, $fault_errno, $fault_strerror), $errno);
  }
}

?>
