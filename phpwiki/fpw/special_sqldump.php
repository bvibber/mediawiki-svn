<?
# Creates an SQL dump

function sqldump () {
	global $vpage ;
	$vpage->special ( "MYSQLDUMP" ) ;
	$target = "./upload/mysqldump.sql" ;

	set_time_limit ( 30000 ) ; # Enough time for this script...

	$out = "" ;
	$c = getDBconnection () ;

	# Listing tables
	if ( isset ( $tables) ) $tables = explode ( "," , $tables ) ; # Tables passed as a parameter
	else { # Scanning all tables
		$tables = array () ;
		$sql = "SHOW TABLES" ;
		$r = mysql_query ( $sql , $c ) ;
		while ( $s = mysql_fetch_row ( $r ) ) array_push ( $tables , $s[0] ) ;
		}

	# Listing fields and collecting structure
	$f = array () ;
	$structure = "# SQL database dump created ".date("l, Fj, Y (H:i:s)",time())."\n\n" ; # Field/table creations
	foreach ( $tables AS $x ) {
		$f[$x] = array () ;
		$sql = "DESCRIBE $x" ;
		$r = mysql_query ( $sql , $c ) ;
		$structure .= "DROP TABLE $x ;\n" ;
		$structure .= "CREATE TABLE $x (\n" ;
		while ( $s = mysql_fetch_row ( $r ) ) {
			array_push ( $f[$x] , $s[0] ) ;
			$structure .= "$s[0] $s[1]" ;
			if ( strtolower($s[2]) != "yes" ) $structure .= " NOT NULL" ;
			if ( $s[4] != "" ) $structure .= " DEFAULT $s[4]" ;
			if ( $s[5] != "" ) $structure .= " $s[5]" ;
			$structure .= ",\n" ;
			}

		$sql = "SHOW INDEX FROM $x" ;
		$r = mysql_query ( $sql , $c ) ;
		while ( $s = mysql_fetch_row ( $r ) ) {
			$app = "KEY" ;
			if ( strtolower ( $s[2] ) == "primary" ) $app = "PRIMARY $app ($s[4])" ;
			else {
				$app .= " $s[2] ($s[4])" ;
				if ( $s[1] == "0" ) $app = "UNIQUE $app" ;
				}
			$app .= ",\n" ;
			$structure .= $app ;
			}
	
		$structure .= ") TYPE=MyISAM ;\n\n" ;
		}

	# Removing all tables except cur
	$tables = array ( "cur" ) ;

	# Writing contents
	$file = fopen ( $target , "wb" ) ;
	fwrite ( $file , $structure."\n\n".$out ) ;

	foreach ( $tables as $x ) {
		$sql = "SELECT * FROM $x" ;
		$r = mysql_query ( $sql , $c ) ;
		while ( $s = mysql_fetch_object ( $r ) ) {
			$out = "" ;
			$out .= "INSERT INTO $x (" ;
			$out .= implode ( ", " , $f[$x] ) ;
			$out .= ") VALUES (" ;
			$a = array () ;
			foreach ( $f[$x] AS $z )
				array_push ( $a , "\"".$s->$z."\"" ) ;
			$out .= implode ( ", " , $a ) ;
			$out .= ") ;\n" ;
			fwrite ( $file , $out ) ;
			}
		}

	fclose ( $file ) ;
	mysql_close ( $c ) ;

	# Zipping
	@chmod ( "$target.gz" , 0777 ) ;
	@unlink ( "$target.gz" ) ;
	system ( "gzip $target" ) ;
	$target = "$target.gz" ;

	$body = "A database dump of the tables <i>" ;
	$body .= implode ( ", " , $tables ) ;
	$body .= "</i> has been created <a href=\"$target\">here</a> (~" ;
	$body .= round ( filesize ( $target ) / 1024 ) ." KB)." ;

	return "<nowiki>$body</nowiki>" ;
	}

?>