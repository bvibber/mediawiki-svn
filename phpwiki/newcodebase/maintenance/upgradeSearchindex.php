<?
# Break fulltext search index out to separate table from cur
# This is being done mainly to allow us to use InnoDB tables
# for the main db while keeping the MyISAM fulltext index for
# search.

# 2002-12-16 Brion VIBBER <brion@pobox.com>

include_once( "Setup.php" );

$wgTitle = Title::newFromText( "Search index breakout script" );
set_time_limit(0);

$wgDBuser			= "wikiadmin";
$wgDBpassword		= $wgDBadminpassword;

echo "Creating searchindex table...\n";
$sql = "DROP TABLE IF EXISTS searchindex";
wfQuery( $sql );
$sql = "CREATE TABLE searchindex (
  si_page int(8) unsigned NOT NULL,
  si_title varchar(255) NOT NULL default '',
  si_text mediumtext NOT NULL default '',
  UNIQUE KEY (si_page)
) TYPE=MyISAM PACK_KEYS=1";
wfQuery( $sql );

echo "Copying data into new table...\n";
# Now, convert!
$sql = "INSERT into searchindex (si_page,si_title,si_text) SELECT cur_id,cur_ind_title,cur_ind_text FROM cur";
$res = wfQuery( $sql );


echo "Creating fulltext index...\n";
# Add index
	$sql = "ALTER TABLE searchindex
  ADD FULLTEXT si_title (si_title),
  ADD FULLTEXT si_text (si_text)";
	wfQuery( $sql );

echo "Dropping index columns from cur table.\n";
# Drop old columns
	$sql = "ALTER TABLE cur
  DROP COLUMN cur_ind_title,
  DROP COLUMN cur_ind_text";
	wfQuery( $sql );

?>
