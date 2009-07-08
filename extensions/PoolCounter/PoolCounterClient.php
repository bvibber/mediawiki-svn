<?php

/**
 * MediaWiki client for the pool counter daemon poolcounter.py.
 */

/**
 * Configuration array for the connection manager. 
 * Use $wgPoolCounterConf to configure the pools.
 */
$wgPoolCountClientConf = array(
	/**
	 * Array of hostnames, or hostname:port. The default port is 7531.
	 */
	'servers' => array( '127.0.0.1' ),

	/**
	 * Connect timeout
	 */
	'timeout' => 0.1,
);

/**
 * Sample pool configuration:
 *   $wgPoolCounterConf = array( 'Article::view' => array( 
 *     'class' => 'PoolCounter_Client',
 *     'waitTimeout' => 15, // wait timeout in seconds
 *     'maxThreads' => 5, // maximum number of threads in each pool
 *   ) );
 */

$wgAutoloadClasses['PoolCounter_ConnectionManager'] 
	= $wgAutoloadClasses['PoolCounter_Client'] 
	= dirname(__FILE__).'/PoolCounterClient_body.php';
$wgExtensionMessagesFiles['PoolCounterClient'] = dirname(__FILE__).'/PoolCounterClient.i18n.php';
