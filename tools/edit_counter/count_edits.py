#! /usr/bin/env python
#
# $Header$
#
# MediaWiki user edit counter.
# This code is in the public domain.

execfile("/home/kate/degree.cf", globals());

import sys
import MySQLdb

import cgi
f = cgi.FieldStorage()

def getdblist():
	try:
		f = file("/home/wikipedia/common/all.dblist", "r")
		i = []
		while True:
			s = f.readline()
			if s == '':
				return i
			s = s[:-1]
			i.append(s)
		return i
	except:
		return []
	return []

if f.has_key('dbname') and f['dbname'].value in getdblist():
	dbname = f['dbname'].value

print "Content-Type: text/html; charset=iso-8859-1"
print
print """
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head><title>editcount</title>
</head>
<body>"""

namespaces = {
	0: 'Articles',
	1: 'Talk',
	2: 'User',
	3: 'User talk',
	4: 'Wikipedia',
	5: 'Wikipedia talk',
	6: 'Image',
	7: 'Image talk',
	8: 'MediaWiki',
	9: 'MediaWiki talk',
	10: 'Template',
	11: 'Template talk',
	12: 'Help',
	13: 'Help talk',
	14: 'Category',
	15: 'Category talk'
}

def ns2name(ns):
	return namespaces[ns]

def editcount(user, nsb):
	ns = dict()
	total = 0
	db = MySQLdb.connect(db=dbname, host=dbserver, user=dbuser, passwd=dbpassword)
	c = db.cursor()
	c.execute("SELECT user_id FROM user WHERE user_name=%s", user)
	t = c.fetchone()
	if t == None:
		print "<strong>user '" + cgi.escape(user) + "' does not exist</strong><br/>"
		return
	uid = t[0]
	if nsb:
		for i in range(0, 16):
			c.execute("SELECT COUNT(*) FROM cur WHERE cur_user=%s AND cur_namespace=%s", (uid, i))
			t = c.fetchone()
			ns[i] = t[0]
			c.execute("SELECT COUNT(*) FROM old WHERE old_user=%s AND old_namespace=%s", (uid, i))
			t = c.fetchone()
			ns[i] += t[0]
		print """
<table cellspacing="0">
<tr><th style="border-bottom: solid 1px black; border-right: solid 1px black">namespace</th><th style="border-bottom: solid 1px black">edits</th></tr>
"""
		for nsn in ns.keys():
			print "<tr><td style='border-right: solid 1px black'>%s</td><td style='text-align: right'>%d</td></tr>" % (ns2name(nsn), ns[nsn])
			total += ns[nsn]
		print "</table>"
	else:
		c.execute("SELECT COUNT(*) FROM cur WHERE cur_user=%s", uid)
		t = c.fetchone()
		total = int(t[0])
		c.execute("SELECT COUNT(*) FROM old WHERE old_user=%s", uid)
		t = c.fetchone()
		total += t[0]
	print cgi.escape(user) + " has a total of %d edits<br/>" % total
	return

if f.has_key('user'):
	print "<div>"
	print "<br/>"
	nsb = False
	# too slow
	#if f.has_key('nsb'):
	#	nsb = True
	editcount(f['user'].value, nsb)
	print "</div>"

print """
<hr/>
<form action="count_edits" method="get">
user name: <input type="text" name="user"/>
<select name="dbname">
"""

dblist = getdblist()
dblist.sort()
for db in dblist:
	selected = ''
	if db == dbname:
		selected=' selected="selected"'
	print '<option value="%s"%s>%s</option>' % (db, selected,db)
print """
</select>
<input type="submit" value="go" />
<!--<input type="checkbox" name="nsb"/>breakdown by namespace (slow)-->
<br/>
<p>
<strong>warning:</strong> <em>editcountitis can be fatal</em>
</p>
<hr/>
kate's tools:
<strong>user edit counter</strong>
| <a href="six_degrees">six degrees of wikipedia</a>
</form>
</body>
</html>
"""
