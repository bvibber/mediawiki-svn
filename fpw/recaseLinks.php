<?php
/*
recaseLinks.php -- Renames articles in wiki newly converted from old
                   UseMod format to fit case conventions.

Most of the non-English wikipedias have been running in full capitalization
mode, so articles Get Named Things Like This With All Kinds Of Things
Capitalized That Shouldn't Be Capitalized At All. That's ugly, so this script
will go through a newly converted database, check which case format was most
often used in links, and rename pages to the preferred title.

Additionally, redirects are created from the Old Caps Title and any other
link forms that used to work, to avoid gratuitously breaking links.

NOTE: Run this *after* importing the converted wiki into the database.
Also, be sure to run rebuildLinks.php after this to get the linked/unlinked
tables up to date again.

OTHER NOTE: The Polish wiki is already on a (mostly) sane capitalization
system, this script is just for the other languages.

2002-05-21 <brion@pobox.com>

*/
 
    include_once ( "./wikiSettings.php" ) ;
    include_once ( "./basicFunctions.php" ) ;
    include_once ( "./databaseFunctions.php" ) ;
    include_once ( "./wikiTitle.php" ) ;
    include_once ( "./wikiUser.php" ) ;
    include_once ( "./wikiPage.php" ) ;

# Don't run from a web browser!
if ( isset ( $ENV["SERVER_NAME"] ) ) die ( "Don't run this script via the web." ) ;

    set_time_limit ( 0 ) ;


# Slight encoding hack for Esperanto pedia charset issues
$user = new wikiUser;
$user->options["encoding"] = 1 ;
$user->name = "Conversion script" ;

# Translated out of old usemod wiki...
function FreeToNormal ( $id , $FreeUpper = true ) {
  # If necessary, work on pre-charset conversion values
  global $wikiRecodeInput , $wikiRecodeOutput ;
  $id = $wikiRecodeOutput ( $id ) ;

  $id = str_replace ( " ", "_", $id ) ;
  $id = ucfirst($id);
  if (strstr($id, '_') != false) {  # Quick check for any space/underscores
    $id = preg_replace ( '/__+/' , "_" , $id ) ;
    $id = preg_replace ( '/^_/' , "", $id ) ;
    $id = preg_replace ( '/_$/' , "", $id ) ;
    #if ($UseSubpage) {
      $id = preg_replace ( '|_/|', "/" , $id ) ;
      $id = preg_replace ( '|/_|', "/" , $id ) ;
    #}
  }
  if ($FreeUpper) {
    # Note that letters after ' are *not* capitalized
    if (preg_match ( '|[-_.,\(\)/][a-z]|' , $id ) ) { # Quick check for non-canon
      $id = preg_replace ( '|([-_.,\(\)/])([a-z])|e' , '"$1" . strtoupper("$2")' , $id ) ;
    }
  }
  return $wikiRecodeInput ( $id ) ;
}

global $wikiMoveRedirectMessage ;

$links = array () ;
$connection = getDBconnection () ;

$arbitrarylimit = 100 ;
$randomcount = 0 ;

function blarg ( $un ) {
	global $links , $connection ;
	global $arbitrarylimit, $randomcount ;
	
	$sql = "SELECT ${un}linked_to as lt from ${un}linked order by lt" ;
	$result = mysql_query ( $sql , $connection ) ;
	if ( $result == 0 ) die ("SQL error: " . mysql_error()) ;
	
	$row = mysql_fetch_object ( $result );

	# Count them up!
	$linkform = "" ;
	while ( $row = mysql_fetch_object ( $result ) ) {
		#if ( $randomcount++ > $arbitrarylimit ) break ; #FIXME
		
		if ( $linkform != $row->lt ) {
			$linkform = $row->lt ;
			$oldcase = FreeToNormal ( $linkform ) ;
			$linkform = ucfirstIntl ( $linkform ) ; # First letter always caps
			if ( ! isset ( $links[$oldcase] ) ) $links[$oldcase] = array () ;
			echo "\n$oldcase <- $linkform" ;
			}
		$x = $links[$oldcase];
		if ( count ( $x ) ) {
			$y = $x[$linkform] ;
			if ( $y ) $y++; else $y = 1 ;
			$x[$linkform] = $y ;
		} else
			$x = array ( $linkform => 1 ) ;
		$links[$oldcase] = $x ;
		
		#$links[$oldcase][$linkform]++ ;
		echo "." ;
		}
	mysql_free_result ( $result ) ;
	}
echo "\n\nChecking linked table..." ;
blarg ( "" ) ;
echo "\n\nChecking unlinked table..." ;
blarg ( "un" ) ;

# For each title, find the most frequent form and rename the article
# to use that form, ?leaving redirects for the others?
echo "\n\nAwright, let's convert some titles!\n" ;
foreach ( $links as $oldcase => $linkforms ) {
	# Check that article by this name really exists...
	#echo "GARRG oldcase is ".gettype($oldcase). " ($oldcase) ; linkforms is ".gettype($linkforms)." ($linkforms)\n" ;
	
	$t = new wikiPage ;
	$t->setTitle ( $oldcase ) ;
	if ( !$t->doesTopicExist() ) {
		#echo " (skipping nonexistent topic $oldcase) " ;
		continue ;
		}
	
	# We want to use the most frequently linked-to form as the title
	$maxcount = 0 ; $maxform = $oldcase ;
	foreach ( $linkforms as $linkform => $count ) {
		if ( $count > $maxcount ) {
			$maxcount = $count ;
			$maxform = $linkform ;
			}
		}
	if ( $maxform != $oldcase ) {
		echo "\nRenaming $oldcase to $maxform...\n" ;
		# Most frequent form was different - rename the article
		$sql = "UPDATE cur SET cur_title=\"$maxform\",cur_timestamp=cur_timestamp WHERE cur_title=\"$oldcase\"";
		#echo "$sql\n" ;
		if ( mysql_query ( $sql , $connection ) == 0 ) echo "\nMYSQL ERROR: " . mysql_error () . "\n";
		$sql = "UPDATE old SET old_title=\"$maxform\",old_timestamp=old_timestamp WHERE old_title=\"$oldcase\"";
		#echo "$sql\n" ;
		if ( mysql_query ( $sql , $connection ) == 0 ) echo "\nMYSQL ERROR: " . mysql_error () . "\n";
		
		# Add old case to redirect list for external links; bookmarks; etc
		$links[$oldcase][$oldcase]++ ;
		} else {
		echo " (don't need to rename $oldcase) " ;
		}
	
	# Make redirects where necessary
	foreach ( $linkforms as $linkform => $count ) {
		if ( $linkform != $maxform ) {
			# And make redirect
			$t = new wikiPage ;
                	$t->setTitle ( $linkform ) ;
			if (! $t->doesTopicExist() ) {
				echo "\nMaking redirect from $linkfrom to $maxform...\n" ;
        	        	$t->ensureExistence () ;
                		$t->setEntry ( "#REDIRECT [[$maxform]]" ,
					str_replace ( "$1" , "$maxform" , $wikiMoveRedirectMessage ) ,
					0 , $wikiConversionScript , 1 ) ;
				}
			}
		}
	}

?>
