  Lucene Search extension for MediaWiki
  =====================================

Note, that this is very much under developement, and is not likely to be
useful right now except for people wishing to help finish it.

To use:

  1. Compile the sources using Eclipse, or download the binary
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
* Jakarta Lucene: http://jakarta.apache.org/lucene/
* BerkeleyDB Java Edition: http://www.sleepycat.com/
* MySQL Connector/J: http://www.mysql.com/products/connector/j/
* MediaWiki 1.4 or 1.5

Should now work with Java 1.4 as well as 1.5.
Testing w/ GCJ will be done later...

Right now the build process is a bit spotty and requires things to be at odd
paths. This will probably get cleaned up at some point.

Todo:
* Close sockets cleanly on errors
* Clean up build process
* GCJ native build

Send feedback to wikitech-l@mail.wikimedia.org or kate.turner@gmail.com.
