<?

// This script fills the tables 'linked' and 'unlinked' with recalculated
// values from the article text in the 'cur' table. Since this operation
// takes quite some time, so I have cut the work in smaller pieces so you don't get
// a http server time-out.

// The size of each chunk is determined by $size, and you can
// adapt it to your situation. After every chunk a link is presented that you can
// click to process the next $size records of table cur. In the URL you can see how
// far you already are. Note that there is no stopping condition built in, so you have
// to check yourself if all records have been processed by looking if $offset is larger
// than the number of records in the table cur.

// Also note that there is key defined for
// the tables 'linked' and 'link' (because MySQL doesn't allow it for such large columns) so
// if you process accidentally the same chunk from table cur twice, you will end up
// with duplicates in your tables. This should be avoided because it might lead to
// incorrect behavior of the system in the future.

    include_once ( "./wikiSettings.php" ) ;
    include_once ( "./basicFunctions.php" ) ;
    include_once ( "./databaseFunctions.php" ) ;
    include_once ( "./wikiTitle.php" ) ;

    $size = 100;
    if ( !isset ( $offset ) ) $offset = 0;
    
    //establish user connection
    $connection = mysql_pconnect($wikiThisDBserver , $wikiThisDBuser , $wikiThisDBpassword )
        or die("Could not get connection to database server.") ;
    //open up database
    mysql_select_db ($wikiSQLServer , $connection)
        or die("Could not select database: $wikiSQLServer");
  
    #$sql = "select cur_title, cur_linked_links, cur_unlinked_links from cur limit $offset, $size ;" ;
    $sql = "select cur_title, cur_text from cur limit $offset, $size ;" ;
    $result = mysql_query ( $sql , $connection ) ;
   
    $linkTitle = new wikiTitle ;    # needed for creating secure titles

    while ( $row = mysql_fetch_object ( $result ) ) {
    	# Grab all links from the page
	$links = array();
	preg_replace( '/\[\[([^\|\]]+)(\||\]\])/e' , "\$links[ucfirst(\"\$1\")]++" , $row->cur_text ) ;
    
        foreach ( $links as $link => $throwaway ) {
            $linkTitle->title = $link ;
            $linkTitle->makeSecureTitle () ;
            $secLinkTitle = $linkTitle->secureTitle ;
            if ($secLinkTitle) { # links that don't have a corresponding secure name are not stored
	    	if($linkTitle->doesTopicExist()) $un = "" ; else $un = "un" ;
		$secLinkTitle = strtr ( $secLinkTitle , array ( "\\" => "\\\\" , "\"" => "\\\"" ) ) ;
                $sql1 = "INSERT INTO ${un}linked ( ${un}linked_from, ${un}linked_to ) VALUES ( \"$row->cur_title\", \"$secLinkTitle\" ) " ;
                mysql_query ( $sql1 , $connection ) ;
		#echo "$sql1\n";
            }
        }
    }

    mysql_close ( $connection ) ;

    $next = $offset + $size ;
    echo "<html><a href=updLinks.php?offset=$next>Click here for next batch </a></html>" ;
    
?>
