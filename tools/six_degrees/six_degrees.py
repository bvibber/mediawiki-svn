#! /usr/bin/python
#
# $Header$
#
# Links path finder CGI client.
# This source is in the public domain.

import sys
import cgi
import os

print "Content-Type: text/html; charset=iso-8859-1"
print

form = cgi.FieldStorage()

fromval = ''
toval = ''

def safe(s):
	return cgi.escape(s).replace('"', "&dquot;").replace("'", "&quot;")

if form.has_key('from'):
	fromval = safe(form['from'].value)
if form.has_key('to'):
	toval = safe(form['to'].value)

print """
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title>six degrees of wikipedia</title>
<style type="text/css">
body {
	background-color: white;
	padding-right: 0px;
	margin-right: 0px;
}
input { border: solid 1px blue; background: white; color: black; }
div.error {
	display: inline;
	border: solid 1px #ff8888;
}
span.error {
	padding-left: 1em;
	padding-right: 1em;
	background-color: #ffdcdc;
	font-weight: bold;
	border-right: solid 1px #ff8888;
}
span.errtext {
	padding-left: 1em;
	padding-right: 1em;
}
div.result {
	border: solid 1px #0000ff;
	width: 50%;
	margin-left: auto; margin-right: auto;
	text-align: center;
}
div.answer {
	width: 100%;
	text-align: center;
	border-bottom: solid 1px #0000ff;
	background-color: #ddddff;
}
span.art {
}
a {
	color: #0000C0;
}
a:hover {
	text-decoration: underline;
}
</style>
</head>
<body>
<div style="text-align: right; padding: 0px; margin: 0px"><img src="/~kate/6deg.png" alt="" /><br/></div>
<div style="text-align: center">
<i>
<a href="http://en.wikipedia.org/wiki/Iterative_deepening_depth-first_search">iterative deepening</a>
<a href="http://en.wikipedia.org/Depth-first_search">depth first search</a>
<a href="http://en.wikipedia.org/Shortest_path_problem">shortest path</a>
query tool for <a href="http://en.wikipedia.org/wiki/Main_Page">Wikipedia</a>...</i><br/>
<i>six degrees</i> finds the shortest path between any two Wikipedia articles in the
main namespace using wiki links
</div>
<div style="padding-top: 35px;">
<form method="post" action="six_degrees">
<center>
<strong>find path...</strong>
from: <input type="text" name="from" value=\"""" + fromval + """\"/>
to: <input type="text" name="to" value=\"""" + toval + """\" />
<input type="submit" value="go" />
</center>
</form>
"""

def mklink(art):
	a2 = art.replace('_', ' ')
	return "<a href=\"http://en.wikipedia.org/wiki/" + safe(art) + "\">" + safe(a2) + "</a>"

def chop(s):
	if s[-1:] == '\n':
		return s[:-1]
	else:
		return s

def getrecent():
	try:
		f = file("/home/kate/rcq", "r")
	except IOError:
		return ''
	i = []
	while True:
		s,t,d = f.readline(), f.readline(), f.readline()
		s = safe(chop(s))
		t = safe(chop(t))
		
		if '' in [s,t,d]:
			break
		i = i + [[s,t,d]]
	i.reverse()
	s = ''
	for t in i[:10]:
		s = s + '<li>' + mklink(t[0]) + ' -> ' + mklink(t[1]) + ' (' + t[2] + ' hops)'
	return s

footer = """
<div style="width: 50%; margin-left: auto; margin-right: auto; border-top: solid 1px black; margin-top: 3em">
<div style="text-align: center">
<strong>hints:</strong>
</div>
<ul>
<li>redirects are searched as well as articles</li>
<li>using a redirect as the target will generally produce an inferior result</li>
<li>article names are case sensitive except for the first letter, which is always capital</li>
</ul>
</div>
</div>
"""

rc = getrecent()
if rc != '':
	footer = footer + """
<div style="width: 50%; margin-left: auto; margin-right: auto; border-top: solid 1px black; margin-top: 3em">
<div style="text-align: center">
<strong>recent queries:</strong>
<ul>"""
	footer = footer + rc
	footer = footer + "</ul></div>"

footer = footer + """
<hr/>
kate's tools: 
 <a href="count_edits">user edit counter</a>
| <strong>six degrees of wikipedia</strong>
| <a href="mailto:keturner@livejournal.com">send feedback...</a>
</div></body></html>"""

def error(text):
	print "<center><div class='error'><span class='error'>error:</span><span class='errtext'>%s</span></div></center>" % cgi.escape(text)
	print footer
	sys.exit()

if not (form.has_key('from') and form.has_key('to')):
	print footer
	sys.exit()

def cfirst(s):
	return s[0].upper() + s[1:]
srcname,targname = cfirst(form['from'].value.replace(' ', '_')), cfirst(form['to'].value.replace(' ', '_'))

if len(srcname) > 255:
	error("source article %s does not exist" % srcname)
if len(targname) > 255:
	error("target article %s does not exist" % targname)
def findlink(src, dst):
	(sin, sout) = os.popen2("/home/kate/linksc")
	sin.write(src + '\n')
	sin.write(dst + '\n')
	sin.flush()
	i = sout.readline()
	if i == "ERROR\n":
		err = sout.readline()
		if err == "NO_FROM\n":
			error("source article %s does not exist" % src)
		elif err == "NO_TO\n":
			error("target article %s does not exist" % dst)
		elif err == "NO_CONNECT\n":
			error("could not contact links server")
		else:
			error("unknown error")
	i = []
	while True:
		nid = sout.readline()
		if nid == '':
			return i
		i = i + [nid]

def mklink(art):
	a2 = art.replace('_', ' ')
	return "<a href=\"http://en.wikipedia.org/wiki/" + safe(art) + "\">" + cgi.escape(a2) + "</a>"

def writelog(src, targ, degs):
	try:
		f = file("/home/kate/rcq", "a")
		f.write(src + '\n' + targ + '\n' + str(degs) + '\n')
	except IOError:
		return
	f.close()

answer = findlink(srcname, targname)
if answer != []:
	writelog(srcname, targname, len(answer))
	print "<div class='result'>"
	print "<div class='answer'>%d degrees of separation</div>" % len(answer)
	print "<span class='art'>%s</span><br/>" % mklink(answer[0]),
	for i in answer[1:]:
		print "<span class='art'>%s</span><br/>" % mklink(i),
	#print "<span class='art'>%s</span>" % mklink(targname)
	print "</div>"
	print footer
	sys.exit()
error("no route found after %d degrees..." % 10)
