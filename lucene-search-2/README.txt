
Lucene-search 2.1: search extension for MediaWiki

== Requirements ==

 - Java 5 +
 - MediaWiki 1.13 with MWSearch extension 
 - Apache Ant 1.6 (for building from source)

== Installation ==

A single-host, single-wiki configuration can be generated as follows.

First make sure LuceneSearch.jar is present. If building from sources, 
run ant to make it:

ant

To generate configuration files, run:

./configure <path to mediawiki root directory>

This script will examine your MediaWiki installation, and generate
configuration files to match your installation. If everything went
without exception, build indexes:

./build

This will build search, highlight and spellcheck indexes from xml
database dump. For small wikis, just put this script into daily
cron and installation is done. 

For larger wikis, install OAIRepository MediaWiki extension and 
after building the initial index use incremental updater:

./update

This will fetch latest updates from your wiki, and update various
indexes with search, page links and spell check data. Put this into 
daily cron to keep the indexes up-to-date. 

== Running ==

Once the indexes have been built, run the daemon:

./lsearchd

The deamon will listen on port 8123 for incoming search requests 
from MediaWiki, and on port 8321 for incoming incremental updates
for the index. 

== Further notes ==

For more complex configuration instructions and troubleshooting please
visit:

  http://www.mediawiki.org/wiki/Extension:Lucene-search




