<?

// This script fills the tables 'linked' and 'unlinked' with recalculated
// values from the article text in the 'cur' table. Since this operation
// takes quite some time, it divides the the work in smaller batches so you don't get
// a http server time-out.

// The size of each batch is determined by $size, and you can
// adapt it to your situation. After every batch a link is presented that you can
// click to process the next $size records of table cur. In the URL you can see how
// far you already are. If all records are processed the script will not present a linke
// and say "Ready!!"

    include_once ( "./wikiSettings.php" ) ;
    include_once ( "./basicFunctions.php" ) ;
    include_once ( "./databaseFunctions.php" ) ;
    include_once ( "./wikiTitle.php" ) ;
    include_once ( "./wikiPage.php" ) ;

    $size = 1000;

    //establish user connection
    $connection = mysql_pconnect($wikiThisDBserver , $wikiThisDBuser , $wikiThisDBpassword )
        or die("Could not get connection to database server.") ;
    //open up database
    mysql_select_db ($wikiSQLServer , $connection)
        or die("Could not select database: $wikiSQLServer");

    if ( !isset ( $offset ) ) $offset = 0;
    if ( !isset ( $all ) ) {
        $sql = "SELECT COUNT(*) AS allPages FROM cur " ;
        $result = mysql_query ( $sql , $connection ) ;
        $row = mysql_fetch_object ( $result ) ;
        $all = $row->allPages;
    }

    if ( $offset <= $all ) {

        $thisPage = new wikiPage ;

        $sql1 = "SELECT cur_title FROM cur LIMIT $offset, $size ;" ;
        $result = mysql_query ( $sql1 , $connection ) ;
        while ( $row = mysql_fetch_object ( $result ) ) {
            $thisPage->load ( $row->cur_title );
            
            $linkedLinks = array () ;
            $unlinkedLinks = array () ;
            
            $thisPage->parseContents ( $thisPage->contents , true ) ; # Calling with savingMode flag set, so only internal Links are parsed
    
            # store linked links in linked table
            $sql = "DELETE FROM linked WHERE linked_from = \"$thisPage->secureTitle\" ;" ;
            $r = mysql_query ( $sql , $connection ) ;
            $linkTitle = new wikiTitle ;
            foreach ( array_keys ( $linkedLinks ) as $linked_link ) { 
                $linkTitle->title = $linked_link ;
                $linkTitle->makeSecureTitle () ;
                $secureLinkTitle = $linkTitle->secureTitle ;
                if ( $secureLinkTitle ) {
                    $sql = "INSERT INTO linked (linked_from, linked_to) VALUES ( \"$thisPage->secureTitle\" , \"$secureLinkTitle\" ) ;" ;
                    $r = mysql_query ( $sql , $connection ) ;
                }
            }
    
            # store unlinked links in unlinked table
            $sql = "DELETE FROM unlinked WHERE unlinked_from = \"$thisPage->secureTitle\" ;" ;
            $r = mysql_query ( $sql , $connection ) ;
            $linkTitle = new wikiTitle ;        
            foreach ( array_keys ( $unlinkedLinks ) as $unlinked_link ) {
                $linkTitle->title = $unlinked_link ;
                $linkTitle->makeSecureTitle () ;
                $secureLinkTitle = $linkTitle->secureTitle ;
                if ( secureLinkTitle ) {
                    $sql = "INSERT INTO unlinked (unlinked_from, unlinked_to) VALUES ( \"$thisPage->secureTitle\" , \"$secureLinkTitle\" ) ;" ;
                    $r = mysql_query ( $sql , $connection ) ;
                }
            }
        }
    
        mysql_close ( $connection ) ;
    
        $next = $offset + $size ;
        echo "<html><UL><LI>Number of processed records: $next <LI> Total number of records: $all <LI> <a href=rebuildLinks.php?offset=$next&all=$all>Click here to process the next $size records. </a></UL></html>" ;
    } else
        echo "<html><h2>Ready!!</h2></html>" ;
    
?>
