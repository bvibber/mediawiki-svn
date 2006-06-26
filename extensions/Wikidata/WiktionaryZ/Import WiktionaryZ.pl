use WiktionaryZ;
use POSIX qw(strftime);

my $startTime = time;

# Example usage to import UMLS into an existing WiktionaryZ database:
# use WiktionaryZ;
# my $importer=new WiktionaryZ('wikidatadb','root','MyPass');
# $importer->setSourceDB('umls');
# $importer->importUMLS();

my $importer=new WiktionaryZ('wikidata_icpc','root','');
$importer->setSourceDB('umls');
#$importer->setSourceDB('swissprot');
$importer->importUMLS();


my $endTime = time;
print "\n";
print "Import started at: " . (strftime "%H:%M:%S", localtime($startTime)) . "\n";
print "Import ended at:   " . (strftime "%H:%M:%S", localtime($endTime)) . "\n";
print "Elapsed time:      " . (strftime "%H:%M:%S", gmtime($endTime - $startTime)) . "\n";

exit 0;