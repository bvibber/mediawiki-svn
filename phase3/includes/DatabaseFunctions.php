<?

# Backwards compatibility wrapper for Database.php/LoadBalancer.php

include_once( "Database.php" );
include_once( "LoadBalancer.php" );

# Query the database
# $db: DB_READ  = -1    read from slave (or only server)
#      DB_WRITE = -2    write to master (or only server)
#      DB_LAST  = -3    whichever one was last used
#      0,1,2,...        query a database with a specific index
# Usually aborts on failure
# If errors are explicitly ignored, returns success
function wfQuery( $sql, $db, $fname = "" )
{
	global $wgLoadBalancer, $wgDBserver, $wgDBuser, $wgDBpassword, $wgDBname;
	global $wgDebugDumpSql, $wgBufferSQLResults, $wgIgnoreSQLErrors;
	
	if ( !is_numeric( $db ) ) {
		# Someone has tried to call this the old way
		$wgOut->fatalError( wfMsgNoDB( "wrong_wfQuery_params", $db, $sql ) );
	}
	$c =& wfGetDB( $db );
	return $c->query( $sql, $fname );
}

function &wfGetDB( $db )
{
	global $wgLoadBalancer, $wgDatabase;
	if ( $db == DB_READ ) {	
		$wgDatabase =& $wgLoadBalancer->getReader();
	} elseif ( $db == DB_WRITE ) {
		$wgDatabase =& $wgLoadBalancer->getWriter();
	} elseif ( $db != DB_LAST ) { # DB_LAST => do nothing
		$wgDatabase =& $wgLoadBalancer->getConnection( $db, true );
	}
	return $wgDatabase;
}
	
# Turns buffering of SQL result sets on (true) or off (false). Default is
# "on" and it should not be changed without good reasons. 
# Returns the previous state.

function wfBufferSQLResults( $newstate )
{
	$db =& wfGetDB( DB_LAST );
	return $db->setBufferResults( $newstate );
}

# Turns on (false) or off (true) the automatic generation and sending
# of a "we're sorry, but there has been a database error" page on
# database errors. Default is on (false). When turned off, the
# code should use wfLastErrno() and wfLastError() to handle the
# situation as appropriate.
# Returns the previous state.

function wfIgnoreSQLErrors( $newstate )
{
	$db =& wfGetDB( DB_LAST );
	return $db->setIgnoreErrors( $newstate );
}

function wfFreeResult( $res ) 
{ 
	$db =& wfGetDB( DB_LAST );
	$db->freeResult( $res ); 
}

function wfFetchObject( $res ) 
{ 
	$db =& wfGetDB( DB_LAST );
	return $db->fetchObject( $res ); 
}

function wfNumRows( $res ) 
{ 
	$db =& wfGetDB( DB_LAST );
	return $db->numRows( $res ); 
}

function wfNumFields( $res ) 
{ 
	$db =& wfGetDB( DB_LAST );
	return $db->numFields( $res ); 
}

function wfFieldName( $res, $n ) 
{ 
	$db =& wfGetDB( DB_LAST );
	return $db->fieldName( $res, $n ); 
}

function wfInsertId() 
{ 
	$db =& wfGetDB( DB_LAST );
	return $db->insertId(); 
}
function wfDataSeek( $res, $row ) 
{ 
	$db =& wfGetDB( DB_LAST );
	return $db->dataSeek( $res, $row ); 
}

function wfLastErrno()  
{ 
	$db =& wfGetDB( DB_LAST );
	return $db->lastErrno(); 
}

function wfLastError()  
{ 
	$db =& wfGetDB( DB_LAST );
	return $db->lastError(); 
}

function wfAffectedRows()
{ 
	$db =& wfGetDB( DB_LAST );
	return $db->affectedRows(); 
}

function wfLastDBquery()
{
	$db =& wfGetDB( DB_LAST );
	return $db->lastQuery();
}

function wfSetSQL( $table, $var, $value, $cond )
{
	$db =& wfGetDB( DB_WRITE );
	return $db->set( $table, $var, $value, $cond );
}

function wfGetSQL( $table, $var, $cond )
{
	$db =& wfGetDB( DB_READ );
	return $db->get( $table, $var, $cond );
}

function wfFieldExists( $table, $field )
{
	$db =& wfGetDB( DB_READ );
	return $db->fieldExists( $table, $field );
}

function wfIndexExists( $table, $index ) 
{
	$db =& wfGetDB( DB_READ );
	return $db->indexExists( $table, $index );
}

function wfInsertArray( $table, $array ) 
{
	$db =& wfGetDB( DB_WRITE );
	return $db->insertArray( $table, $array );
}

function wfGetArray( $table, $vars, $conds, $fname = "wfGetArray" )
{
	$db =& wfGetDB( DB_READ );
	return $db->getArray( $table, $vars, $conds, $fname );
}

?>
