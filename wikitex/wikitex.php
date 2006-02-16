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
   * @file wikitex.php
   * @brief Main driver.
   * 
   * @c wikitex.php is the main driver for WikiTeX; and is where the extensible functions are located.
   */
include 'wikitex.inc.php';

/**
 * Register WikiTeX with @c wgParser.
 */
$wgExtensionFunctions[] = 'wtRegister';

require_once('Setup.php');

/**
 * Register extension hooks.
 * 
 * Register each key in <code> @link wikitex.inc.php wtClassHooks @endlink </code> with @c wgParser as pointing to
 * the function in whose corresponding value.
 * @return void
 */
function wtRegister()
{
  global $wtClassHooks, $wgParser;

  foreach ($wtClassHooks as $class => $hook) {
    $wgParser->setHook($class, $hook);
  }
}

/**
 * Substitute parameters.
 * 
 * Substitute the parameters in the given template,
 * corresponding above all to @c 'value' (the content),
 * as well as any properties that have been provided with the
 * tag.<p>Upshot: a tag of the form &lt;class value="content"&gt;&lt;/class&gt;
 * sufficeth.
 * @param partiendum the string to be parsed.
 * @param partientes an array of parsing key-value pairs.
 * @param partitum the predigested template-string.
 * @return the parsed content.
 */
function wtSub($partiendum, $partientes, $partitum)
{
  foreach($partientes as $token => $substitution) {
    $partiendum = strtr($partiendum, array(sprintf($partitum, strtoupper($token)) => $substitution));
  }

  return $partiendum;
}
                
/**
 * Return error.
 *
 * Returns error whose language is specified in @c wikitex.inc.php.
 */
function wtErr($class) {
  global $wtErr;

  return sprintf(WT_ERR, $wtErr[$class]);
}

/**
 * Render WikiTeX.
 * 
 * Receive raw text in, serialize to hash-encoded temp file, funnel to bash and receive tag anew.
 * @param reddendum string to be rendered; containing the tag's content received from @c wgParser.
 * @param class string containing the tag's type; typically the @e verbatim element.
 * @param props array containing the properties associated with content's tag.
 * @return an @c HTML string containing a reference to rendered image (plus apposite extras, cf. music's midi),
 * and an anchor to its source.
 */
function wtRender($reddendum, $class, $props)
{
  global $IP, $wgScriptPath, $wtDefaults;

  define('WT_PATH', "$IP/extensions/wikitex");
  define('WT_TEMPLATE', WT_PATH . '/template/%s.*');
  define('WT_DIR', WT_PATH . '/tmp/');
  define('WT_URI', "$wgScriptPath/extensions/wikitex/tmp/");
  define('WT_BASH', WT_PATH . '/wikitex.sh %s %s %s');
  define('WT_REND', '%%%s%%');
  define('WT_CACHE', '%s.cache');
  define('WT_SOURCE', 'src');
  define('WT_DATA', 'data');

  // token => value substitution in template
  $subs = array();

  // content, whether from source file, cache, or template substitution and processing;
  // filename of pre-processed content;
  // MD5 hash of content
  $content = $cache = $hash = '';

  // try reading from source
  if (!empty($props[WT_SOURCE])) {
    $file = Image::newFromName($props[WT_SOURCE]);
    if ($file->exists() && is_readable($file->imagePath)) {
      $content = file_get_contents($file->imagePath);
    } else {
      return wtErr('source');
    }
  }

  // read from source failed
  if (empty($content)) {
    // check class template against glob: "template/<class>.*"
    if (!($template = file_get_contents(current(glob(sprintf(WT_TEMPLATE, $class)))))) {
      return wtErr('temp');
    }
                        
    // ad hoc substitution (superseded by value property)
    $subs['value'] = $reddendum;

    // superimpose properties [unto defaults] unto substitutions
    $subs = array_merge($subs, (empty($wtDefaults[$class]) ? $props : array_merge($wtDefaults[$class], $props)));;

    // token substitution; where each value of $subs substituted in $template: %VALUE%, %TEMPO%, etc.
    $content = wtSub($template, $subs, WT_REND);
  }

  // derive the outfile hash
  $hash = md5($content);

  // change to cache directory
  if (!chdir(WT_DIR)) {
    return wtErr('cache');
  }

  // check cache
  if (is_readable($cache = sprintf(WT_CACHE, $hash))) {
    return file_get_contents($cache);
  }

  // create working directory
  if (!is_dir($hash) && !mkdir($hash)) {
    return wtErr('dir');
  }      

  // allow wikitex to write to dir
  if (!chmod($hash, 0777)) {
    return wtErr('perm');
  }      

  // change to working directory
  if (!chdir($hash)) {
    return wtErr('work');
  }

  // source
  if (!file_put_contents($hash, $content)) {
    return wtErr('src');
  }

  // make supplemental data available
  if (!empty($props[WT_DATA])) {
    $file = Image::newFromName($props[WT_DATA]);
    if ($file->exists() && is_readable($file->imagePath)) {
      if (!copy($file->imagePath, './' . $props[WT_DATA])) {
        return wtErr('copy');
      }
    } else {
      return wtErr('data');
    }
  }

  // invoke handler
  if (!is_executable(substr(WT_BASH, 0, strpos(WT_BASH, ' ')))) {
    return wtErr('bash');
  } else {
    return trim(shell_exec(sprintf(WT_BASH, $hash, $class, WT_URI)));
  }
}

/**
 * Chess.
 * 
 * <a href="http://www.ctan.org/tex-archive/macros/latex/contrib/skak">Skak</a> by Torben Hoffmann.
 * @param content string containing the tag's content, received from @c wgParser.
 * @param parms array of parameters corresponding to the tag's properties.
 * @return string containing the processed tag, referencing novel
 * content.
 */
function wtChess($content, $parms)
{
  return wtRender($content, 'chess', $parms);
}

/**
 * Feynman diagrams.
 * 
 * <a href="http://www.tex.ac.uk/cgi-bin/texfaq2html?label=drawFeyn">Feynman</a> by Michael Levine
 * @param content string containing the tag's content, received from @c wgParser.
 * @param parms array of parameters corresponding to the tag's properties.
 * @return string containing the processed tag, referencing novel
 * content.
 */
function wtFeyn($content, $parms)
{
  return wtRender($content, 'feyn', $parms);
}

/**
 * Go.
 * 
 * <a href="http://match.stanford.edu/bump/go.html">sgf2dg</a> by Daniel Bump and Reid Augustin.
 * @param content string containing the tag's content, received from @c wgParser.
 * @param parms array of parameters corresponding to the tag's properties.
 * @return string containing the processed tag, referencing novel
 * content.
 */
function wtGo($content, $parms)
{
  return wtRender($content, 'go', $parms);
}

/**
 * Graphviz.
 * 
 * <a href="http://www.graphviz.org/">Graphviz</a> by Lefty Koutsofios, <em>et al.\ </em>
 * @param content string containing the tag's content, received from @c wgParser.
 * @param parms array of parameters corresponding to the tag's properties.
 * @return string containing the processed tag, referencing novel
 * content.
 */
function wtGraph($content, $parms)
{
  return wtRender($content, 'graph', $parms);
}

/**
 * Ibycus Greek.
 * 
 * <a href="http://www.ctan.org/tex-archive/language/greek/ibygrk/?action=/tex-archive/language/greek/">Ibycus</a>
 * by Pierre MacKay.
 * @param content string containing the tag's content, received from @c wgParser.
 * @param parms array of parameters corresponding to the tag's properties.
 * @return string containing the processed tag, referencing novel
 * content.
 */
function wtGreek($content, $parms)
{
  return wtRender($content, 'greek', $parms);
}

/**
 * American Mathematical Society.
 * 
 * <a href="http://www.ams.org/tex/amslatex.html">AMS-LaTeX</a>, including commutative diagrams.
 * @param content string containing the tag's content, received from @c wgParser.
 * @param parms array of parameters corresponding to the tag's properties.
 * @return string containing the processed tag, referencing novel
 * content.
 */
function wtMath($content, $parms)
{
  return wtRender($content, 'amsmath', $parms);
}

/**
 * Lilypond.
 * 
 * <a href="http://www.lilypond.org/web/">Lilypond</a> with midi output.
 * @param content string containing the tag's content, received from @c wgParser.
 * @param parms array of parameters corresponding to the tag's properties.
 * @return string containing the processed tag, referencing novel
 * content.
 */
function wtMusic($content, $parms)
{
  global $wtDefaults;

  $wtDefaults['music'] = array('tempo' => '80');
  return wtRender($content, 'music', $parms);
}

/**
 * Gnuplot.
 * 
 * <a href="http://www.gnuplot.info">Gnuplot</a> by Hans-Bernhard Broeker, <em>et al.\ </em>
 * @param content string containing the tag's content, received from @c wgParser.
 * @param parms array of parameters corresponding to the tag's properties.
 * @return string containing the processed tag, referencing novel
 * content.
 */
function wtPlot($content, $parms)
{
  return wtRender($content, 'plot', $parms);
}

/**
 * ImageMagick's @c SVG.
 * 
 * <a href="http://www.imagemagick.org/">ImageMagick's</a> light @c SVG conversion.
 * @param content string containing the tag's content, received from @c wgParser.
 * @param parms array of parameters corresponding to the tag's properties.
 * @return string containing the processed tag, referencing novel
 * content.
 */
function wtSVG($content, $parms)
{
  global $wtDefaults;

  $wtDefaults['svg'] = array('width' => '800',
                             'height' => '600');
  return wtRender($content, 'svg', $parms);
}

/**
 * Tengwar.
 * 
 * <a href="http://tolklang.quettar.org/fonts/">TengTeX</a> by Ivan Derzhanski.
 * @param content string containing the tag's content, received from @c wgParser.
 * @param parms array of parameters corresponding to the tag's properties.
 * @return string containing the processed tag, referencing novel
 * content.
 */
function wtTeng($content, $parms)
{
  return wtRender($content, 'teng', $parms);
}

/**
 * International Phonetic Alphabet.
 * 
 * <a href="http://www.l.u-tokyo.ac.jp/~fkr/tipa/">TIPA</a> International Phonetic Symbols.
 * @param content string containing the tag's content, received from @c wgParser.
 * @param parms array of parameters corresponding to the tag's properties.
 * @return string containing the processed tag, referencing novel
 * content.
 */
function wtTipa($content, $parms)
{
  return wtRender($content, 'tipa', $parms);
}

/**
 * XyMTeX.
 * 
 * <a href="http://imt.chem.kit.ac.jp/fujita/fujitas3/xymtex/indexe.html">XyMTeX</a> by Shinsaku Fujita.
 * @param content string containing the tag's content, received from @c wgParser.
 * @param parms array of parameters corresponding to the tag's properties.
 * @return string containing the processed tag, referencing novel
 * content.
 */
function wtXym($content, $parms)
{
  return wtRender($content, 'chem', $parms);
}

?>
