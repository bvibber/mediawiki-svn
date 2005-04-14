  Lucene Search extension for MediaWiki
  =====================================

Note: This is at best beta-quality code.  Be aware that it may contain bugs
and not work as intended. If you want to use it, please report any issues 
to the authors (see end of file).

To use:

  1. Compile the sources using Eclipse or Ant, or download the binary
     distribution [not yet available].
  2. Build the initial search index:
  
       java -Djdbc.drivers=com.mysql.jdbc.Driver org.wikimedia.lsearch.MWSearch 
               -rebuild jdbc:mysql://192.168.0.160/searchdb
  
  3. Copy mwsearch.conf.example to mwsearch.conf and modify as appropriate.
  
  4. Start the MWDaemon:
  
       java org.wikimedia.lsearch.MWDaemon
  
  5. Install the LuceneSearch extension from the `extensions' module in your
     MediaWiki.  Make sure $wgDisableInternalSearch is enabled.

Requirements:
* Jakarta Lucene: http://lucene.apache.org/
* BerkeleyDB Java Edition: http://www.sleepycat.com/
  (only required if you use TitlePrefixMatcher).
* MySQL Connector/J: http://www.mysql.com/products/connector/j/
* MediaWiki 1.4 or 1.5

Should now work with Java 1.4 as well as 1.5.
Testing w/GCJ has been done, and except for the prefix matcher, it appears to work.
(BerkeleyDB does not work under GCJ at the moment, and the matcher requires it).

GCJ 4.0 is required for the native build; a snapshot from 2005-04-07 is known to
build and work more or less correctly.

Todo:
* Close sockets cleanly on errors
* Clean up build process

Send feedback to wikitech-l@mail.wikimedia.org.
