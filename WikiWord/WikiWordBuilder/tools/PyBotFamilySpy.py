#!/usr/bin/python

import sys

def escape(text):

  if isinstance(text, str):
    text = text.decode("UTF-8")

  text = text.replace( u'\n', u'\\n' );
  text = text.replace( u'\r', u'\\r' );
  text = text.replace( u'\t', u'\\t' );

  s = ""

  for ch in text:
    n = ord(ch)
    hi = n / 256
    lo = n % 256

    if hi == 0:
	if lo<32 or lo>127:
	  s += '\\u00%0x' % lo
	else: 
	  s += ch;
    else:
	s += '\\u%0x%0x' % (hi, lo)

  return s;


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

  print "%s=%s" % (m, escape(v))
    