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
   * Include those constants responsible for error' gestalt and
   * class->function mapping.
   */
include_once('WikitexConstants.php');

/**
 * HTML element corresponding to errorous output (previously rendered of LaTeX: whoops!)
 */
define('WIKITEX_ERROR', '<span class="errwikitex">WikiTeX: %s</span>');

/**
 * Absolute path to the base extension.
 */
define('WIKITEX_PATH', "$IP/extensions/wikitex");

/**
 * Absolute path to the templates' directory.
 */
define('WIKITEX_TEMPLATE', WIKITEX_PATH . '/template/%s.*');

/**
 * Absolute path to the cache directory.
 */
define('WIKITEX_DIR', WIKITEX_PATH . '/tmp/');

/**
 * Uniform resource identifier associated with the
 * cache directory.
 */
define('WIKITEX_URI', "$wgScriptPath/extensions/wikitex/tmp/");

/**
 * The anticipated form of the bash invocation,
 * with triple argument: hash, module, outpath.
 */
define('WIKITEX_BASH', WIKITEX_PATH . '/wikitex.sh %s %s %s');

/**
 * Template for token substitution in template files.
 */
define('WIKITEX_TOKEN', '%%%s%%');

/**
 * Template for cache files.
 */
define('WIKITEX_CACHE', '%s.cache');

/**
 * Tag-property which invokes source-file or
 * -page.
 */
define('WIKITEX_SOURCE', 'src');

/**
 * Tag-property which invokes apposite data
 * (usurps not source).
 */
define('WIKITEX_DATA', 'data');

/**
 * Capital WikiTeX functionality.
 *
 * Provides translational functionality from class to method;
 * mediates between PHP and shell interface.
 * 
 * @package Wikitex
 * @author Peter Danenberg <pcd@wikitex.org>
 * @copyright Copyright (C) 2004-6  Peter Danenberg
 * @version %VERSION%
 * @link http://wikitex.org
 * @since WikiTeX 1.1-beta2
 */
class Wikitex {

  /**
   * Placeholder for facultative token
   * substitutions and directives
   * particular to classes.
   * @var array
   */
  protected static $defaults = array();

  /**
   * Register parser hooks.
   *
   * Iterate through the array of in-article classes,
   * associating them with a function pointer within
   * Parser.
   * @param Parser $parser Parser object received from MediaWiki.
   * @return void
   * @access public
   * @static
   */
  public static function registerHooks($parser) {
    foreach (Wikitex_Constants::getHooks() as $class => $hook) {
      $parser->setHook($class, array(__CLASS__, $hook));
    }
  }

  /**
   * Substitute string tokens.
   *
   * Using the {@link WIKITEX_TOKEN} template, substitute
   * the tokens within the source material.
   * @param string $partiendum the source material (Latin, partiendum: "to be parsed").
   * @param array $partientes array mapping token to substitution (Latin, partientes: "parsing things").
   * @return void
   * @static
   * @access public
   */
  public static function substituteTokens($partiendum, array $partientes)
  {
    foreach($partientes as $token => $substitution) {
      $partiendum = strtr($partiendum, array(sprintf(WIKITEX_TOKEN, strtoupper($token)) => $substitution));
    }
    return $partiendum;
  }

  /**
   * Constructs an HTML error according
   * to the error template.
   * @param string the class under whose auspices the error
   * arose.
   * @return string the so-gestalted error.
   * @see WIKITEX_ERROR
   */
  public static function error($error) {
    $errors = Wikitex_Constants::getErrors();
    return sprintf(WIKITEX_ERROR, $errors[$error]);
  }

  /**
   * Render content.
   * 
   * The carnality or meat of WikiTeX; takes the
   * to-be-rendered-content ($reddendum),
   * which it renders according to $class
   * and dispatches with the addition of said $properties.
   * 
   * @param string $class the class under whose auspices we
   * do peripatetically render (the students of Aristotle).
   * @param string $reddendum the content to be rendered (Latin, reddendum: "to be rendered").
   * @param array $properties an array consisting of token -> value
   * substitutions in the template, or idio-directives (source, say, or data
   * fetching).
   * @return string the thus rendered content (or apposite error).
   * @static
   * @access public
   */
  public static function renderContent($reddendum, $class, $properties)
  {
    global $IP, $wgScriptPath;

    // token => value substitution in template
    $substitutions = array();

    // content, whether from source file, cache, or template substitution and processing;
    // filename of pre-processed content;
    // MD5 hash of content
    $template = $content = $cache = $hash = '';

    // try reading from source
    if (!empty($properties[WIKITEX_SOURCE])) {
      if (!self::fetchContent($properties[WIKITEX_SOURCE], $content, false)) {
        return $content;        // an error occurred
      }
    }

    // read from source failed
    if (empty($content)) {
      // check class template against glob: "template/<class>.*"
      if (!($template = file_get_contents(current(glob(sprintf(WIKITEX_TEMPLATE, $class)))))) {
        return self::error('temp');
      }
                        
      // ad hoc substitution (superseded by value property)
      $substitutions['value'] = $reddendum;

      // superimpose properties [unto defaults] unto substitutions
      $substitutions = array_merge($substitutions, (empty(self::$defaults[$class]) ? $properties : array_merge(self::$defaults[$class], $properties)));;

      // token substitution; where each value of $subs substituted in $template: %VALUE%, %TEMPO%, etc.
      $content = self::substituteTokens($template, $substitutions);
    }

    // derive the outfile hash
    $hash = md5($content);

    // change to cache directory
    if (!chdir(WIKITEX_DIR)) {
      return self::error('cache');
    }

    // check cache
    if (is_readable($cache = sprintf(WIKITEX_CACHE, $hash))) {
      return file_get_contents($cache);
    }

    // create working directory
    if (!is_dir($hash) && !mkdir($hash)) {
      return self::error('dir');
    }      

    // allow wikitex to write to dir
    if (!chmod($hash, 0777)) {
      return self::error('perm');
    }      

    // change to working directory
    if (!chdir($hash)) {
      return self::error('work');
    }

    // source
    if (!file_put_contents($hash, $content)) {
      return self::error('src');
    }

    // make supplemental data available
    if (!empty($properties[WIKITEX_DATA])) {
      if (!self::fetchContent($properties[WIKITEX_DATA], $content, true)) { // clobbers content in case of error
        return $content;        // an error occurred
      }
    }

    // invoke handler
    if (!is_executable(substr(WIKITEX_BASH, 0, strpos(WIKITEX_BASH, ' ')))) {
      return self::error('bash');
    } else {
      return trim(shell_exec(sprintf(WIKITEX_BASH, $hash, $class, WIKITEX_URI)));
    }
  }

  /**
   * Fetch external content.
   *
   * Fetch external content given title; try uploaded files first, and if that
   * fails: fall back on article fetchment.
   * 
   * @param string $title the name of the resource (underscores or spaces).
   * @param string &$content the fetched content or (in case of error) an error message.
   * @param bool $copy whether to copy the file to the working directory (leaves
   * $copy unmolested except in case of error).
   * @return bool whether an error took place (in which case $content will contain
   * the error message).
   * @access public
   * @static
   * @since 1.1-beta3
   */
  public static function fetchContent($title, &$content, $copy = false) {
    $file = Image::newFromName($title);
    if ($file->exists() && is_readable($file->imagePath)) {
      if ($copy) {
        if (!copy($file->imagePath, './' . $file->getName())) {
          $content = self::error('copy');
          return false;
        }
      } else {                  // Not copying
        $content = file_get_contents($file->imagePath);
      }
    } else {                 // File is illegible; try reading article
      if (($article = Revision::newFromTitle(Title::newFromText($title))) != null) {
        $text = $article->getText();
        if ($copy) {
          file_put_contents('./' . escapeshellcmd($title), $text);;
        } else {
          $content = $text;
        }
      } else {    // Neither source is legible, nor article available.
        $content = self::error('source');
        return false;
      }
    }
    return true;
  }

  /**
   * Chess.
   * 
   * {@link http://www.ctan.org/tex-archive/macros/latex/contrib/skak Skak} by Torben Hoffmann.
   * @param string $content containing the tag's content, received from wgParser.
   * @param array $parms of parameters corresponding to the tag's properties.
   * @return string containing the processed tag, referencing novel
   * content.
   */
  public static function chess($content, array $parms)
  {
    return self::renderContent($content, __FUNCTION__, $parms);
  }

  /**
   * Feynman diagrams.
   * 
   * {@link http://www.tex.ac.uk/cgi-bin/texfaq2html?label=drawFeyn Feynman} by Michael Levine
   * @param string $content containing the tag's content, received from wgParser.
   * @param array $parms of parameters corresponding to the tag's properties.
   * @return string containing the processed tag, referencing novel
   * content.
   */
  public static function feyn($content, array $parms)
  {
    return self::renderContent($content, __FUNCTION__, $parms);
  }

  /**
   * Go.
   * 
   * {@link http://match.stanford.edu/bump/go.html sgf2dg} by Daniel Bump and Reid Augustin.
   * @param string $content containing the tag's content, received from wgParser.
   * @param array $parms of parameters corresponding to the tag's properties.
   * @return string containing the processed tag, referencing novel
   * content.
   */
  public static function go($content, array $parms)
  {
    return self::renderContent($content, __FUNCTION__, $parms);
  }

  /**
   * Graphviz.
   * 
   * {@link http://www.graphviz.org/ Graphviz} by Lefty Koutsofios, et al.
   * @param string $content containing the tag's content, received from wgParser.
   * @param array $parms of parameters corresponding to the tag's properties.
   * @return string containing the processed tag, referencing novel
   * content.
   */
  public static function graph($content, array $parms)
  {
    switch ($parms['layout']) {
    case 'neato':
      $layout = $parms['layout'];
      break;
    case 'fdp':
      $layout = $parms['layout'];
      break;
    case 'twopi':
      $layout = $parms['layout'];
      break;
    case 'circo':
      $layout = $parms['layout'];
      break;
    default:
      $layout = 'graph';
    }
    return self::renderContent($content, $layout, $parms);
  }

  /**
   * Ibycus Greek.
   * 
   * {@link http://www.ctan.org/tex-archive/language/greek/ibygrk/?action=/tex-archive/language/greek/ Ibycus}
   * by Pierre MacKay.
   * @param string $content containing the tag's content, received from wgParser.
   * @param array $parms array of parameters corresponding to the tag's properties.
   * @return string containing the processed tag, referencing novel
   * content.
   */
  public static function greek($content, array $parms)
  {
    return self::renderContent($content, __FUNCTION__, $parms);
  }

  /**
   * American Mathematical Society.
   * 
   * {@link http://www.ams.org/tex/amslatex.html AMS-LaTeX}, including commutative diagrams.
   * @param string $content containing the tag's content, received from wgParser.
   * @param array $parms of parameters corresponding to the tag's properties.
   * @return string containing the processed tag, referencing novel
   * content.
   */
  public static function amsmath($content, array $parms)
  {
    return self::renderContent($content, __FUNCTION__, $parms);
  }

  /**
   * Lilypond.
   * 
   * {@link http://www.lilypond.org/web/ Lilypond} with midi output.
   * @param string $content containing the tag's content, received from wgParser.
   * @param array $parms of parameters corresponding to the tag's properties.
   * @return string containing the processed tag, referencing novel
   * content.
   */
  public static function music($content, array $parms)
  {
    self::$defaults[__FUNCTION__] = array('tempo' => '80');
    return self::renderContent($content, __FUNCTION__, $parms);
  }

  /**
   * Gnuplot.
   * 
   * {@link http://www.gnuplot.info Gnuplot} by Hans-Bernhard Broeker, <em>et al.\ </em>
   * @param string $content containing the tag's content, received from wgParser.
   * @param array $parms of parameters corresponding to the tag's properties.
   * @return string containing the processed tag, referencing novel
   * content.
   */
  public static function plot($content, array $parms)
  {
    return self::renderContent($content, __FUNCTION__, $parms);
  }

  /**
   * ImageMagick's SVG.
   * 
   * {@link http://www.imagemagick.org/ ImageMagick's} light SVG conversion.
   * @param string $content containing the tag's content, received from wgParser.
   * @param array $parms of parameters corresponding to the tag's properties.
   * @return string containing the processed tag, referencing novel
   * content.
   */
  public static function svg($content, array $parms)
  {
    self::$defaults[__FUNCTION__] = array('width' => '800',
                                          'height' => '600');
    return self::renderContent($content, __FUNCTION__, $parms);
  }

  /**
   * Tengwar.
   * 
   * {@link http://tolklang.quettar.org/fonts/ TengTeX} by Ivan Derzhanski.
   * @param string $content containing the tag's content, received from wgParser.
   * @param array $parms of parameters corresponding to the tag's properties.
   * @return string containing the processed tag, referencing novel
   * content.
   */
  public static function teng($content, array $parms)
  {
    return self::renderContent($content, __FUNCTION__, $parms);
  }

  /**
   * International Phonetic Alphabet.
   * 
   * {@link http://www.l.u-tokyo.ac.jp/~fkr/tipa/ TIPA} International Phonetic Symbols.
   * @param string $content containing the tag's content, received from wgParser.
   * @param array $parms of parameters corresponding to the tag's properties.
   * @return string containing the processed tag, referencing novel
   * content.
   */
  public static function ipa($content, array $parms)
  {
    return self::renderContent($content, __FUNCTION__, $parms);
  }

  /**
   * XyMTeX.
   * 
   * {@link http://imt.chem.kit.ac.jp/fujita/fujitas3/xymtex/indexe.html XyMTeX} by Shinsaku Fujita.
   * @param string $content containing the tag's content, received from wgParser.
   * @param array $parms of parameters corresponding to the tag's properties.
   * @return string containing the processed tag, referencing novel
   * content.
   */
  public static function chem($content, array $parms)
  {
    return self::renderContent($content, __FUNCTION__, $parms);
  }
}
?>
