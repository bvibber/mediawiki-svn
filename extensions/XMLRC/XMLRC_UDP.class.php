<?php
if (!defined('MEDIAWIKI')) {
	echo "XMLRC extension";
	exit(1);
}

class XMLRC_UDP extends XMLRC_Transport {
  function __construct( $config ) {
    $this->socket = null;

    $this->address = isset( $config['address'] ) ? $config['address'] : '127.0.0.1';
    $this->port = isset( $config['port'] ) ? $config['port'] : 4455;
  }

  public function connect() {
    if ( $this->socket ) return;

    $this->socket = socket_create( AF_INET, SOCK_DGRAM, SOL_UDP );
    if ( !$this->socket ) wfDebugLog("XMLRC", "failed to create UDP socket\n");
    else wfDebugLog("XMLRC", "created UDP socket\n");
  }

  public function disconnect() {
    if ( !$this->socket ) return;

    socket_close( $this->socket );
    $this->socket = null;
    wfDebugLog("XMLRC", "closed UDP socket\n");
  }

  public function send( $xml ) {
    $do_disconnect = !$this->socket;
    $this->connect();

    $ok = socket_sendto( $this->socket, $xml, strlen($xml), 0, $this->address, $this->port );
    if ( $ok ) wfDebugLog("XMLRC", "sent UDP packet to {$this->address}:{$this->port}\n");
    else wfDebugLog("XMLRC", "failed to send UDP packet to {$this->address}:{$this->port}\n");

    if ( $do_disconnect ) $this->disconnect();
  }
}
