<?php

class WikitexCache
{
  /**
   * Vide infra, putCache(), for justification of md4.
   */
  public static $HASH = 'md4';

  /**
   * Directories deep to place cache.
   */
  public static $HASH_DEPTH = 2;

  /**
   * Length of hash segments to use for
   * $HASH_DEPTH-deep directories.
   */
  public static $HASH_SPLICE = 1;

  public static $UMASK = 0022;
  public static $MOD = 0700;

  /**
   * Cache-dir plus file prefix as needed
   * to reference the file system.
   */
  protected $filepath = NULL;

  /**
   * Cache-dir plus file prefix as needed
   * to reference URIs.
   */
  protected $webpath = NULL;

  /**
   * The source from which to compute hash (i.e.,
   * uncompiled source).
   */
  protected $content = NULL;

  /**
   * The digested source, serves as filename.
   */
  protected $hash = NULL;

  /**
   * The compiled content to be stored in the cache
   * file; consequence: hash cannot be computed from
   * cache's contents.
   */
  protected $data = NULL;

  public function WikitexCache($content) {
    $this->content = $content;
    $this->hash = self::hash($content);
    $ext = NULL;
    for ($i = 0; $i < self::$HASH_DEPTH; $i += self::$HASH_SPLICE) {
      $hashchar = substr($this->hash, $i, self::$HASH_SPLICE);
      $ext = "{$ext}/{$hashchar}";
    }
    $cache_filepath = WikitexConstants::getCacheFilepath();
    $this->filepath = "{$cache_filepath}{$ext}";
    $cache_webpath = WikitexConstants::getCacheWebpath();
    $this->webpath = "{$cache_webpath}{$ext}";
  }

  public static function hash($content) {
    return hash(WikitexCache::$HASH, $content);
  }

  public function getFilename($mime) {
    $suffix = WikitexConstants::$MIME_TO_EXT[$mime];
    return "{$this->filepath}/{$this->hash}.{$suffix}";
  }

  public function getWebname($mime) {
    $suffix = WikitexConstants::$MIME_TO_EXT[$mime];
    return "{$this->webpath}/{$this->hash}.{$suffix}";
  }

  public function getCache($mime) {
    $filename = $this->getFilename($mime);
    if (file_exists($filename)) {
      $cached = ($mime == WikitexConstants::$MIMES['error'])
        ? sprintf('%s %s', WikitexConstants::$SAWS['cached_error'],
                  file_get_contents($filename))
        : $this->getWebname($mime);
      return $cached;
    } else {
      return NULL;
    }
  }

  /**
   * Opting for $BASE/[m]/[n]/mnxxx..., where mnxxx... is the md5 hash
   * of the content.  md5 was chosen over md4 because, though the latter
   * leads slightly in speed, the former is almost ubiquitous.
   */
  public function putCache($data, $type) {
    umask(self::$UMASK);
    if (!file_exists($this->filepath)) {
      if (!mkdir($this->filepath, self::$MOD, TRUE)) {
        // $this->filepath gives away too much info for public consumption;
        // the logs should disclose more for the need-to-knowers.
        throw new WikitexDirectoryError('CACHE_DIR');
      }
    }
    $filename = $this->getFilename($type);
    if (!file_put_contents($filename, (string) $data)) {
      // $filename gives away too much for the nought-need-to-knowers.
      throw new WikitexFileError('CACHE_FILE');
    }
  }
}

