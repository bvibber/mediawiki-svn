#!/usr/bin/python

import sys
from SpyTools import *

pybotdir = sys.argv[1]
lang = sys.argv[2]

sys.path.append(pybotdir)
sys.path.append(pybotdir+"/families")

import wikipedia_family

family = wikipedia_family.Family()

for m in family.__dict__:
  if m == "interwiki_putfirst":
    continue

  d = family.__dict__[m]

  if not isinstance(d, dict):
    continue

  if not lang in d:
    continue

  v = d[lang]

  if v is None:
    continue

  if isinstance(v, list) or isinstance(v, tuple):
    v = "|".join(v)

  v = v.replace(" ", "_");
  print "%s=%s" % (m, escape(v))
    