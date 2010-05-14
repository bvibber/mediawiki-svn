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
	'description' => 'Allows to store revision text in [http://incubator.apache.org/cassandra/ Apache Cassandra] database.',
	//'descriptionmsg' => 'cassandra-desc',
);

$wgAutoloadClasses['ExternalStoreCassandra'] = dirname( __FILE__ ) . '/Cassandra_body.php';

if ( is_array( $wgExternalStores ) ) {
	$wgExternalStores[] = 'cassandra';
} else {
	$wgExternalStores = array( 'cassandra' );
}

/**
 * Extension settings
 */

// Directory where Thrift for PHP resides.
$wgThriftRoot = '/usr/share/php/Thrift';
$wgThriftPort = 9160;
$wgCassandraKeyPrefix = '';

/**
 * Read and write consistencies, see http://wiki.apache.org/cassandra/API#ConsistencyLevel
 * for details.
 * Avoid using cassandra_ConsistencyLevel here to prevent large parts
 * of Cassandra and Thrift from being loaded on every request. Shouldn't
 * matter for real-world setups with byte code cache though.
 */
$wgCassandraReadConsistency = 1;  // cassandra_ConsistencyLevel::ONE
$wgCassandraWriteConsistency = 1; // cassandra_ConsistencyLevel::ONE

$wgCassandraColumnFamily = 'Standard1';