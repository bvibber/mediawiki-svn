#!/usr/bin/python

import sys, urllib2
import json, re
from SpyTools import *

lang = sys.argv[1]

opener = urllib2.build_opener()
opener.addheaders = [('User-agent', 'BrightByte/WikiWord/WikipediaDisambiguationSpy <wikiword@brightbyte.de>')]

u = "http://%s.wikipedia.org/w/api.php?action=query&prop=links&plnamespace=10&pllimit=50&titles=MediaWiki:Disambiguationspage&format=json" % lang
js = opener.open(u).read();

data = json.read(js)

disambig = []

pid = data["query"]["pages"].keys()[0]
page = data["query"]["pages"][pid]

if not "links" in page:
  sys.exit()

links = page["links"]

for link in links:
  n = link["title"]
  n = re.sub("^[^:]*:", "", n)
  disambig.append(n)

for d in disambig:
  d = d.replace(" ", "_")
  d = escape(d)
  print d

