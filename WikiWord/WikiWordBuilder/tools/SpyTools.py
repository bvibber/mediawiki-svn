#!/usr/bin/python

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
	  s += '\\u00%02x' % lo
	else: 
	  s += ch;
    else:
	s += '\\u%02x%02x' % (hi, lo)

  return s;
