<?php

if ( !defined( 'MEDIAWIKI' ) ) {
	die( "Not a valid entry point\n" );
}

$wgExtensionCredits['other'][] = array(
	'path' => __FILE__,
	'name' => 'Cassandra',
	'version' => 0.1,
	'author' => 'Max Semenik',
	'url' => 'http://www.mediawiki.org/wiki/Extension:Cassandra',
	'description' => 'Allows to store revision text in [http://cassandra.apache.org/ Apache Cassandra] database.',
	//'descriptionmsg' => 'cassandra-desc',
);

$wgAutoloadClasses['ExternalStoreCassandra'] = $wgAutoloadClasses['MWCassandraException']
	= dirname( __FILE__ ) . '/Cassandra_body.php';

if ( is_array( $wgExternalStores ) ) {
	$wgExternalStores[] = 'cassandra';
} else {
	$wgExternalStores = array( 'cassandra' );
}

/**
 * Extension settings
 */

// Directory where Thrift bindings for PHP reside
$wgThriftRoot = '/usr/share/php/Thrift';

// Port used for communicating with Cassandra. Must match <ThriftPort>
// in Cassandra's storage-conf.xml
$wgCassandraPort = 9160;

// String prepended to saved key names, can be used to distinct between
// different wikis, etc. Does not affect the already saved revisions.
$wgCassandraKeyPrefix = $wgDBname;

/**
 * Read and write consistencies, see http://wiki.apache.org/cassandra/API#ConsistencyLevel
 * for details.
 * Avoid using cassandra_ConsistencyLevel here to prevent large parts
 * of Cassandra and Thrift from being loaded on every request. Shouldn't
 * matter much for real-world setups with byte code cache though.
 */
$wgCassandraReadConsistency = 1;  // cassandra_ConsistencyLevel::ONE
$wgCassandraWriteConsistency = 1; // cassandra_ConsistencyLevel::ONE

// Column family to be used for storing data
$wgCassandraColumnFamily = 'Standard1';