  Lucene Search extension for MediaWiki
  =====================================

Note, that this is very much under developement, and is not likely to be
useful right now except for people wishing to help finish it.

It _only_ works with MediaWiki 1.5.  Anything else WILL NOT WORK, so don't try it.
Depending on the 1.5 release schedule it may be backported to 1.4.

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

Still to do: incremental search updates.

Send feedback to wikitech-l@mail.wikimedia.org or kate.turner@gmail.com.
