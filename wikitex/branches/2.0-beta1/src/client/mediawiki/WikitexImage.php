<?php
  /**
   * WikiTeX: expansible LaTeX module for MediaWiki
   * Copyright(C) 2004-7 Peter Danenberg
   * See doc/COPYING for details.
   */

class WikitexImage
{
  const ELEMENT = '<a href="%s"><img src="%s" /></a>';

  /**
   * URI to the source behind the image.
   */
  protected $source = NULL;

  /**
   * URI to the image itself.
   */
  protected $image = NULL;

  public function WikitexImage($source, $image) {
    $this->source = $source;
    $this->image = $image;
  }

  public function __toString() {
    return sprintf(self::ELEMENT, $this->source, $this->image);
  }
}

?>
