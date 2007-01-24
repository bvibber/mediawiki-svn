<?php
  /**
   * WikiTeX: expansible LaTeX module for MediaWiki
   * Copyright(C) 2004-7 Peter Danenberg
   * See doc/COPYING for details.
   */

class WikitexError extends Exception
{
  const CODE = 0;
  const MESSAGE = '%d, %s';

  protected $message = NULL;
  protected $code = self::CODE;

  public function WikitexError($message, $code = self::CODE) {
    $this->message = $message;
    $this->code = $code;
  }

  public function __toString() {
    list($strerror, $errno) = WikitexConstants::$ERRORS['generic'];
    $message = $this->message;
    if ($this->code) {
      $message = sprintf(self::MESSAGE, $this->code, $message);
    }
    return sprintf($strerror, $message);
  }
}

?>
