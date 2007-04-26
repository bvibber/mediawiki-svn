  Lucene Search 2.0: extension for MediaWiki
  ==========================================

Requirements:

 - Java 5.0
 - Lucene 2.0dev (modified Lucene 2.0)
 - MediaWiki 1.9 with MWSearch extension
 
 Optionally:
 - Rsync (for distributed architecture)
 - Apache XMLRPC 3.0 (for XMLRPC interface)
 - Apache Ant 1.6 (for building from source, etc)

Setup:

 - Edit mwsearch-global.conf and make it available at some URL
 - At each host: 
 	* properly setup hostname (otherwise JavaVM gets confused)
 	* make and set permissions of local directory for indexes
 	* edit mwsearch.conf:
 	 	+ MWConfig.global to point to URL of mwsearch-global.conf
 		+ Localization.url to point to URL pattern of latest 
 		  message files from MediaWiki
   	* setup rsync daemon (see rsyncd.conf-example)
  	* setup log4j logging subsystem (see mwsearch.log4j-example)
 	
Running:

 - start rsync daemon (if distributed architecture)
 - "./run.sh <hostname>" or "ant run" (setup hostname in file "hostname")
 
Features: 

 - distributed architecture, indexes can be either single file (single), 
   split between main namespace and rest (mainsplit) or split into some 
   number of subindexes (split). Indexer makes periodic snapshots of
   index, and searchers check for this snapshots to update their local
   copy.
   
 - wiki syntax parser, articles are parsed for basic wiki syntax and are 
   stripped of accents. Localization for wiki syntax can be read from 
   MediaWiki message files. Categories are extracted and put into 
   separate field. Additionaly, template names (but not parameters), 
   table parameters, image parameters (except caption) are not indexed.
   
 - query parser, faster search query parsing, enables prefixes for namespaces,
   e.g. 'help:editing pages'. Prefixes are localized within MediaWiki. Can
   do category searches e.g. 'smoked category:cheeses'. Rewrites all of these
   so that stemmed present are present but add less to document score. 
   
 - (hopefully) robust architecture, with threads pinging hosts that are down,
   and search daemons trying alternatives if host holding part of the 
   index is down.
