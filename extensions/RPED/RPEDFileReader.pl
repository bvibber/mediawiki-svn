# RPEDFileReader.pl by Tisane, http://www.mediawiki.org/wiki/User:Tisane
#
# This script is free software that is available under the terms of the Creative Commons
# Attribution 3.0 license and the current version of the GNU General Public License.
#
# The purpose of this script is to read a text file (specifically, the list of page titles from
# Wikipedia's data dump) and add each page title to a database table.
 
use strict;

my $base_module_dir = (-d '/home2/rpedorg/perl' ? '/home2/rpedorg/perl' : ( getpwuid($>) )[7] . '/perl/');
    unshift @INC, map { $base_module_dir . $_ } @INC;

#use Mysql;
use DBI;
 
my $sql_login = 'rpedorg';
my $sql_pass = 'Ab123456!';
my $db_name = 'rpedorg_libertapedia';
my $db_host = 'localhost'; # or remote mysql server name
my $numArgs = $#ARGV + 1;
my $offset=0;
my $titleMatch='';
if ($numArgs>1){
	if ($ARGV[0] eq '-line' || $ARGV[0] eq '-l'){
		$offset=$ARGV[1];
	}
	if ($ARGV[0] eq '-title' || $ARGV[0] eq '-t'){
		$titleMatch=$ARGV[1];
	}
}

# if left blank, this defaults to localhost 
 
# PERL MYSQL CONNECT()
my $conn_string = "DBI:mysql:$db_name";
if ($db_host) { $conn_string .= ":$db_host"; }
my $dbh = DBI->connect("$conn_string",$sql_login,$sql_pass); 
  
my $filename='enwiki-20100312-all-titles-in-ns0';
open(MYDATA, $filename) or 
	die("Error: cannot open file '".$filename."'\n");
my $line;
my $lnum = 1;
my $titleMatched=0;
if ($titleMatch eq ''){
	$titleMatched=1;
}
while( $line = <MYDATA> ){
	chomp($line);
	if ($titleMatched==0){
		if ($titleMatch eq $line){
			$titleMatched=1;
		}
	}
	if ($lnum>=$offset && $titleMatched==1){
		$line=$dbh->quote("$line");
		my $sql="INSERT INTO rped_page (rped_page_title) VALUES ($line)";
		$dbh->prepare($sql);
		$dbh->do($sql);
		if ($lnum%100==0){	
			print "$lnum: $line\n";
		}
	}
	$lnum++;
}
 
close MYDATA;