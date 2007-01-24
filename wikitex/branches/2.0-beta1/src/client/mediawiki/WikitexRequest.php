<?php
  /**
   * WikiTeX: expansible LaTeX module for MediaWiki
   * Copyright(C) 2004-7 Peter Danenberg
   * See doc/COPYING for details.
   */

class WikitexRequest
{
  protected $action = NULL;
  protected $content = NULL;
  protected $author = NULL;

  public function WikitexRequest($action, $content, $author=NULL) {
    $this->action = $action;
    $this->content = $content;
    if (empty($author)) {
      $author = $_SERVER['REMOTE_ADDR'];
    }
    $this->author = $author;
  }

  /**
   * An ad-hoc call to check errore caching.  Can be generalized
   * by adding error to the list of interesting MIMEs, and giving
   * it precedence, perhaps, over content-MIMEs?
   *
   * Should error have precedence?
   * 
   * @param cache the instantiatied cache object.
   * @return A mime -> error mapping in the case of cached error
   * (and $DEBUG) or NULL.
   */
  public function checkCachedError(WikitexCache $cache) {
    if (!WikitexConfig::$DEBUG &&
        $cached = $cache->getCache(WikitexConstants::$MIMES['error'])) {
      return array(WikitexConstants::$MIMES['error'] => $cached);
    }
    return NULL;
  }

  /**
   * Configure curl (headers, etc.) Placeholder for basic/key-based
   * auth switching.
   * @return The so-configured request
   */
  public function getCurlRequest() {
    xmlrpc_set_type($this->content, 'base64');
    xmlrpc_set_type($this->author, 'base64');
    $data = xmlrpc_encode_request($this->action,
                                  array('content' => $this->content,
                                        'author' => $this->author));
    $request = curl_init(WikitexConfig::$URL);
    $url = parse_url(WikitexConfig::$URL);
    $port = (isset($url['port']))
      // chose {$ over ${ for continuity with {$a->a}
      ? ":{$url['port']}"
      : NULL;
    $host = "{$url['host']}{$port}";
    $agent = WikitexConstants::$AGENT;
    $length = strlen($data);
    $username = WikitexConfig::$USERNAME;
    $password = WikitexConfig::$PASSWORD;
    $options = array(
                     CURLOPT_POST => TRUE,
                     CURLOPT_HEADER => FALSE,
                     CURLOPT_HTTPHEADER =>
                     array("Host: {$host}",
                           "User-Agent: {$agent}",
                           "Content-Type: text/xml", // Mandated
                           "Content-Length: {$length}"),
                     CURLOPT_RETURNTRANSFER => TRUE,
                     CURLOPT_POSTFIELDS => $data,
                     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_0,
                     CURLOPT_FAILONERROR => TRUE,
                     CURLOPT_USERPWD => "{$username}:{$password}",
                     );
    curl_setopt_array($request, $options);
    return $request;
  }

  /**
   * Dig up caches or befray the server. Client registers MIME-types of
   * interest; these are systematically checked against the cache
   * and atomically assembled into a document. If one mime existeth nought,
   * we befray the server. Cached errors are checked first of all,
   * and pre&euml;mpt other MIMEs unless WikitexConfig::$DEBUG is
   * TRUE.
   *
   * @param mimes the MIME-types that interest us, and make up a whole
   * document.
   * @return A dictionary of MIME -> content mappings.
   */
  public function render(array $mimes) {
    $cache = new WikitexCache($this->content);
    if ($cached = $this->checkCachedError($cache)) {
      return $cached;
    }
    $media = NULL;
    foreach ($mimes as $mime) {
      if ($content = $cache->getCache($mime)) {
        $media[$mime] = $content;
      } else {
        $media = NULL;
        break;
      }
    }
    if ($media) {
      return $media;
    }
    $request = $this->getCurlRequest();
    $response = curl_exec($request);
    $errno = curl_errno($request);
    if ($errno) {
      $cacheable = new WikitexCurlError(curl_error($request), $errno);
      $cache->putCache($cacheable, WikitexConstants::$MIMES['error']);
      throw $cacheable;
    } else {
      $document = xmlrpc_decode($response);
      if (empty($document)) {
        $cacheable = new WikitexRPCResponseError();
        $cache->putCache($cacheable, WikitexConstants::$MIMES['error']);
        throw $cacheable;
      } else if (xmlrpc_is_fault($document)) {
        $cacheable = new WikitexRPCFaultError($document);
        $cache->putCache($cacheable, WikitexConstants::$MIMES['error']);
        throw $cacheable;
      }
      foreach ($document as $mime => $content) {
        $cache->putCache($content->scalar, $mime);
        $media[$mime] = $cache->getCache($mime);
      }
      return $media;
    }
  }
}
?>
