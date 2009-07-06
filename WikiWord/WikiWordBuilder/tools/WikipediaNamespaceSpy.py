#!/usr/bin/python

import sys, urllib2
import json

lang = sys.argv[1]

opener = urllib2.build_opener()
opener.addheaders = [('User-agent', 'BrightByte/WikiWord/WikipediaNamespaceSpy <wikiword@brightbyte.de>')]

u = "http://%s.wikipedia.org/w/api.php?action=query&meta=siteinfo&siprop=namespaces|namespacealiases&format=json" % lang
js = opener.open(u).read();

data = json.read(js)

ns = {}

for n in data["query"]["namespaces"]:
  r = data["query"]["namespaces"][n]

  n = int(n)

  if not n in ns:
      ns[n] = [];

  ns[n].append(r["*"])

  #if "canonical" in r and r["canonical"] != r["*"]:
  #  ns[n].append(r["canonical"])

for r in data["query"]["namespacealiases"]:
  n=r["id"]
  n = int(n)

  if not n in ns:
      ns[n] = [];

  ns[n].append(r["*"])

for n in ns:
  names = "|".join(ns[n])
  names = names.replace(" ", "_")
  print "%d=%s" % (n, names)

