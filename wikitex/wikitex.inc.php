<?php

  /*
   * WikiTeX: expansible LaTeX module for MediaWiki
   * Copyright (C) 2004-6  Peter Danenberg
   *
   * WikiTeX is licensed under the Artistic License 2.0;  to
   * view a copy of this license, see COPYING or visit:
   *
   * http://dev.perl.org/perl6/rfc/346.html
   */

  /**
   * @mainpage
   * @author Peter Danenberg &lt;pcd at wikitex dot org&gt;
   * 
   * An expansible LaTeX (<em>et alia</em>) module for MediaWiki.&nbsp; Visit <a href="http://wikitex.org">wikitex.org</a>
   * for more information; or, to see WikiTeX in action: <a href="http://wikisophia.org">Wikisophia</a>.
   */

  /**
   * @file wikitex.inc.php
   * Customizable class-names.
   * 
   * @c wikitex.inc.php contains customizable class-names, which correspond to the key in each key-value
   * pair of @c wtClassHooks.
   */

  /**
   * Class names.
   * 
   * Set class names; which may be customized based on local language, fashion
   * or whim.  Henry V was a maker of these when he was courting Gallrix Kate.
   */
$wtClassHooks = array ('amsmath' => 'wtMath',
                       'chem' => 'wtXym',
                       'chess' => 'wtChess',
                       'feyn' => 'wtFeyn',
                       'go' => 'wtGo',
                       'greek' => 'wtGreek',
                       'graph' => 'wtGraph',
                       'music' => 'wtMusic',
                       'plot' => 'wtPlot',
                       'svg' => 'wtSVG',
                       'teng' => 'wtTeng',
                       'ipa' => 'wtTipa');

/**
 * Error messages.
 *
 * Placed here for easy clarification or translation.
 */
$wtErr = array('temp' => 'no template corresponds to said class.',
               'cache' => 'unable to change to temp directory.',
               'dir' => 'unable to create working directory.',
               'perm' => 'unable to set permissions on working directory.',
               'work' => 'unable to change to work directory.',
               'src' => 'unable to write contents in work directory.',
               'source' => 'source existeth nought or is illegible.',
               'data' => 'data existeth nought or is illegible.',
               'copy' => 'cannot copy data to the work directory.',
               'bash' => '<code>wikitex.sh</code> is not executable.');

define('WT_ERR', '<span class="errwikitex">WikiTeX: %s</span>');

/**
 * Initial properties.
 * 
 * Used to provide the initial palette upon which
 * an tag's properties will be superimpos&egrave;d.
 */
$wtDefaults = array();
?>
