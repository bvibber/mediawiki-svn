#! /usr/bin/env python
#
# Count number of edits from a user.
# This source code is in the public domain.
# $Id$

execfile("/home/kate/degree.cf", globals());

import sys
#sys.path.append('/home/kate/pylib64/lib64/python2.3/site-packages')
sys.path.append('/home/kate/pylib/lib/python2.2/site-packages')
import MySQLdb

import cgi
#import cgitb; cgitb.enable()
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

print "Content-Type: text/html; charset=UTF-8"
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
    try:
	return namespaces[ns]
    except KeyError:
        return "Unknown namespace %d" % ns

def editcount(user):
	ns = dict()
	total = 0
	db = MySQLdb.connect(db=dbname, host=dbserver, user=dbuser, passwd=dbpassword)
	c = db.cursor()
        c.execute("SELECT page_namespace, COUNT(*) FROM user, revision, page WHERE "
                "user_name=%s AND rev_user = user_id AND rev_page = page_id "
                "GROUP BY page_namespace", user);
        
        print "<table style='border: solid 1px black' cellspacing='0' cellpadding='3'>"
        print "<tr><th>Namespace</th><th>Edits</th></tr>"
        t = c.fetchone()
        while t != None:
            print "<tr><td style='border-right: solid 1px black'>%s</td><td style='text-align: right'>%d</td></tr>" % (ns2name(t[0]), t[1])
            total += t[1]
            t = c.fetchone()
        print "</table>"
        print "<hr/>Total edits for <strong>%s</strong>: %d<br/>" % (cgi.escape(user), total)
	return

if f.has_key('user'):
	print "<div>"
	print "<br/>"
        s = f['user'].value.replace('_', ' ')
        s = s[0].upper() + s[1:]
	editcount(s)
	c = db.cursor()
	c.execute("SELECT COUNT(DISTINCT page_id) FROM user, revision, page WHERE "
		 "user_name=%s AND rev_user = user_id AND rev_page = page_id", user);
	t = c.fetchone()
	print "%s has edited a total of <strong>%s</strong> distinct pages.<br/></p>" % (cgi.escape(user), t[0])
	print "</div>"

print """
<hr/>
<p><a href="count_edits_14">MW 1.4 version</a></p>
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
