#!/usr/bin/python

import sys, urllib2
import json
from SpyTools import *

lang = sys.argv[1]

opener = urllib2.build_opener()
opener.addheaders = [('User-agent', 'BrightByte/WikiWord/WikipediaInterwikiSpy <wikiword@brightbyte.de>')]

u = "http://%s.wikipedia.org/w/api.php?action=query&meta=siteinfo&siprop=interwikimap&format=json" % lang
js = opener.open(u).read();

data = json.read(js)

interwikis = {}

for e in data["query"]["interwikimap"]:
  prefix = escape(e["prefix"])
  url = escape(e["url"])
  
  lang= e.get("language")
  
  if lang: 
  		lang = escape(lang)
		print "%s=%s %s" % (prefix, url, lang)
  else:
  		print "%s=%s" % (prefix, url)

