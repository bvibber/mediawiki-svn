<?php
  /**
   * WikiTeX: expansible LaTeX module for MediaWiki
   * Copyright(C) 2004-7 Peter Danenberg
   * See doc/COPYING for details.
   */

class WikitexConstants
{
  public static $CONFIG_DIR = 'config';
  public static $DEFAULT_CONFIG = 'default.php';
  public static $LOCAL_CONFIG = 'local.php';

  public static $SAWS =
    array('cached_error' => '<span class="error">[Cached]</span>');

  /**
   * Relative path from wikitex's root in the file system
   * to the cache directory.
   */
  public static $CACHE_FILEPATH = 'cache';

  /**
   * Relative path from MediaWiki's web-URI to the cache-URI.
   */
  public static $CACHE_WEBPATH = 'extensions/wikitex/cache';

  /**
   * Mappeth MIME-types to extensions.
   */
  public static $MIME_TO_EXT =
    array(
          'application/x-latex' => 'tex',
          'application/x-wikitex-error' => 'error',
          'audio/midi' => 'midi',
          'image/png' => 'png',
          );

  /**
   * Convenient handles to MIMEs.
   */
  public static $MIMES =
    array(
          'error' => 'application/x-wikitex-error',
          'latex' => 'application/x-latex',
          'midi' => 'audio/midi',
          'png' => 'image/png',
          );

  public static $ERRORS =
    array(
          'generic' => array('<span class="error">WikiTeX hath failed, milord: %s. (<em>Pecca fortiter!</em>)</span>',
                             0),
          'blank' => array('We received a blank document from the webserver',
                           1),
          'curl' => array('Curl reported error %d: %s', 2),
          'directory' => array('Can\'t create directory `%s\'', 3),
          'file' => array('Can\'t write to file `%s\'', 4),
          'fault' => array('The RPC-server reported fault %d: &ldquo;%s&rdquo;', 4),
          );

  public static $AGENT = 'WikiTeX-WikiMedia';

  public static $HOOKS =
    array('amsmath' => 'math');

  /**
   * Local to remote wethod mapping.
   */
  public static $METHODS =
    array('math' => 'math');

  public static $CLASSES =
    array(
          'Wikitex' => 'Wikitex.php',
          'WikitexCache' => 'WikitexCache.php',
          'WikitexConfig' => 'WikitexConfig.php',
          'WikitexConstants' => 'WikitexConstants.php',
          'WikitexCurlError' => 'errors/WikitexCurlError.php',
          'WikitexDirectoryError' => 'errors/WikitexDirectoryError.php',
          'WikitexError' => 'errors/WikitexError.php',
          'WikitexFileError' => 'errors/WikitexFileError.php',
          'WikitexImage' => 'WikitexImage.php',
          'WikitexRequest' => 'WikitexRequest.php',
          'WikitexRPCFaultError' => 'errors/WikitexRPCFaultError.php',
          'WikitexRPCResponseError' => 'errors/WikitexRPCResponseError.php',
          );

  public static function getDir() {
    return dirname(__FILE__);
  }

  public static function getCacheFilepath() {
    return sprintf('%s/%s', self::getDir(), self::$CACHE_FILEPATH);
  }

  public static function getCacheWebpath() {
    global $wgScriptPath;
    return sprintf('%s/%s', $wgScriptPath, self::$CACHE_WEBPATH);
  }

  public static function getClasses() {
    $dir = self::getDir();
    $classes = array();
    foreach (self::$CLASSES as $class => $file) {
        $classes[$class] = "$dir/$file";
      }
    return $classes;
  }
  
  public static function getConfig($config) {
    return sprintf('%s/%s/%s', dirname(__FILE__), self::$CONFIG_DIR, $config);
  }

  public static function getDefaultConfig() {
    return self::getConfig(self::$DEFAULT_CONFIG);
  }
  
  public static function getLocalConfig() {
    return self::getConfig(self::$LOCAL_CONFIG);
  }
}

?>
