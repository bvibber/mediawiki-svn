  Lucene Search 2: extension for MediaWiki
  ==========================================

Requirements:

 - Java 5.0
 - Lucene 2.0dev (modified Lucene 2.0)
 - MediaWiki 1.9 with MWSearch extension
 
 Optionally:
 - Rsync (for distributed architecture)
 - Apache XMLRPC 3.0 (for XMLRPC interface)
 - Apache Ant 1.6 (for building from source, etc)

Installing:

 - Up-to-date instructions and troubleshooting can be found at: 

   http://www.mediawiki.org/wiki/Extension:LuceneSearch
 	
Running:

 - start rsync daemon (if distributed architecture)
 - "./lsearchd" or "ant run" (setup hostname in file "hostname")
 
Features: 

 - distributed architecture, indexes can be either single file (single), 
   split between main namespace and rest (mainsplit) or split into some 
   number of subindexes (split). Indexer makes periodic snapshots of
   index, and searchers check for this snapshots to update their local
   copy.
   
 - incremental updater using oai interface. Periodically checks wikis
   for new updates, and enqueues them on the indexer.
   
 - wiki syntax parser, articles are parsed for basic wiki syntax and are 
   stripped of accents. Localization for wiki syntax can be read from 
   MediaWiki message files. Categories are extracted and put into 
   separate field. Additionaly, template names (but not parameters), 
   table parameters, image parameters (except caption) are not indexed.
   
 - query parser, faster search query parsing, enables prefixes for namespaces,
   e.g. 'help:editing pages'. Prefixes are localized within MediaWiki. Rewrites 
   all of these so that stemmed present are present but add less to document score. 
   
 - (hopefully) robust architecture, with threads pinging hosts that are down,
   and search daemons trying alternatives if host holding part of the 
   index is down.
