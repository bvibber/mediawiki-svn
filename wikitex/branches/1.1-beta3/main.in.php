<?php
  /**
   * WikiTeX: expansible LaTeX module for MediaWiki
   *
   * PHP version 5
   * 
   * WikiTeX is licensed under the Artistic License 2.0;  to
   * view a copy of this license, see COPYING or visit:
   *
   * http://dev.perl.org/perl6/rfc/346.html
   *
   * @package Wikitex
   * @author Peter Danenberg <pcd at wikitex dot org>
   * @copyright Copyright &copy; 2004-6  Peter Danenberg
   * @version %VERSION%
   * @link http://wikitex.org
   * @since WikiTeX 1.1-beta2
   */

  /**
   * Include main functional class (which in turn includes
   * constants).
   */
include_once('Wikitex.php');

$wgExtensionFunctions[] = 'wikitexRegisterHooks';

/**
 * Register callbacks.
 *
 * Register callback function within the MediaWiki parser.
 * @return void
 */
function wikitexRegisterHooks() {
  global $wgParser;
  Wikitex::registerHooks($wgParser);
}

?>
