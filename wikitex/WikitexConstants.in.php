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
   * @author Peter Danenberg <pcd@wikitex.org>
   * @copyright Copyright (C) 2004-6  Peter Danenberg
   * @version %VERSION%
   * @link http://wikitex.org
   * @since WikiTeX 1.1-beta2
   */

  /**
   * WikiTeX Constants.
   *
   * Provides centralized error messages and class to method mapping.
   * 
   * @package Wikitex
   * @author Peter Danenberg <pcd@wikitex.org>
   * @copyright Copyright (C) 2004-6  Peter Danenberg
   * @version %VERSION%
   * @link http://wikitex.org
   * @since WikiTeX 1.1-beta2
   */
class Wikitex_Constants
{
  /**
   * Class to function mapping.
   *
   * Ideal topos for tranlation: maps in-article class names to names of functions
   * within class {@link Wikitex}.
   * 
   * @access protected
   * @static
   * @var array
   */
  protected static $classHooks = array('amsmath' => 'amsmath',
                                       'chem' => 'chem',
                                       'chess' => 'chess',
                                       'feyn' => 'feyn',
                                       'go' => 'go',
                                       'greek' => 'greek',
                                       'graph' => 'graph',
                                       'music' => 'music',
                                       'plot' => 'plot',
                                       'svg' => 'svg',
                                       'teng' => 'teng',
                                       'ipa' => 'ipa');

  /**
   * Error messages.
   *
   * Simulated constant array with error messages.
   *
   * @access protected
   * @static
   * @var array
   */
  protected static $errors = array('temp' => 'no template corresponds to said class.',
                                   'cache' => 'unable to change to temp directory.',
                                   'dir' => 'unable to create working directory.',
                                   'perm' => 'unable to set permissions on working directory.',
                                   'work' => 'unable to change to work directory.',
                                   'src' => 'unable to write contents in work directory.',
                                   'source' => 'source existeth nought or is illegible.',
                                   'copy' => 'cannot copy data to the work directory.',
                                   'bash' => '<code>wikitex.sh</code> is not executable.');

  /**
   * Get class to function mapping.
   *
   * Maps in-article class name to methods within {@link Wikitex}.
   * @access public
   * @static
   * @return array array containing class to function mapping.
   */
  public static function getHooks() {
    return self::$classHooks;
  }

  /**
   * Get error messages.
   *
   * Maps error type to message; simulates constant array.
   * @static
   * @access public
   * @return array containing closs to error mapping.
   */
  public static function getErrors() {
    return self::$errors;
  }
}

?>
