#! /usr/bin/env python
# vim:ts=4 sw=4 et:
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
<style type="text/css">
table.edittable {
    border: solid 1px #6666aa;
    border-collapse: collapse;
    margin-left: auto;
    margin-right: auto;
}
td, th {
    padding: 0.1em 0.3em 0.1em 0.3em;
    text-align: center;
}
th {
    vertical-align: top;
}
table.edittable tr {
    border-bottom: solid 1px #6666aa;
}
table.edittable th {    
    border-right: solid 1px #6666aa;
}
td.nedits {
    text-align: right;
}
td.nsname {
    border-right: solid 1px #6666aa;
    text-align: left;
}
table.edittable tr.nsn {
    border-bottom: 0px;
}
</style>
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

extra_ns = {
    'enwiki': {
        100: 'Portal',
        101: 'Portal talk'
    }
}

def ns2name(ns, wiki):
    try:
        return namespaces[ns]
    except KeyError:
        try:
            return "%s: %s" % (wiki, extra_ns[wiki][ns])
        except KeyError:
            return "Unknown namespace %d" % ns

def fmttime(t):
    return "%s-%s-%s %s:%s:%s" % (t[0:4], t[4:6], t[6:8], t[8:10], t[10:12], t[12:14])

def editcount(user):
    ns = dict()
    total = 0
    db = MySQLdb.connect(db=dbname, host=dbserver, user=dbuser, passwd=dbpassword)
    c = db.cursor()
    c.execute("SELECT COUNT(DISTINCT page_id) FROM user, revision, page WHERE "
              "user_name=%s AND rev_user = user_id AND rev_page = page_id", user);
    t = c.fetchone()
    distinct = t[0]
    c = db.cursor()
    c.execute("SELECT MIN(rev_timestamp) FROM user, revision, page WHERE "
              "user_name=%s AND rev_user = user_id AND rev_page = page_id", user)
    t = c.fetchone()
    firstedit = t[0]
    c = db.cursor()
    c.execute("SELECT page_namespace, COUNT(*) FROM user, revision, page WHERE "
              "user_name=%s AND rev_user = user_id AND rev_page = page_id "
              "GROUP BY page_namespace", user);
    edits = {}    
    t = c.fetchone()
    while t != None:
        total += t[1]
        edits[t[0]] = t[1]
        t = c.fetchone()
    print """
<table class="edittable">
<tr><th>Username</th><td colspan='2'>%s</td></tr>
<tr><th>Total edits</th><td colspan='2'>%s</td></tr>
<tr><th>Distinct pages edited</th><td colspan='2'>%s</td></tr>
<tr><th>Edits/page (avg)</th><td colspan='2'>%0.02f</td></tr>
<tr><th>First edit</th><td colspan='2'>%s</td></tr>
<tr>
<th rowspan='%s'>Edits by namespace</th>
<th>Namespace</th><th>Edits</th>
</tr>
""" % (cgi.escape(user), total, distinct, (float(total) / float(distinct)), fmttime(firstedit), len(edits) + 1)
    for nsn in edits.keys():
        print "<tr class='nsn'><td class='nsname'>%s</td><td class='nedits'>%d</td></tr>" \
                % (ns2name(nsn, dbname), edits[nsn])
    print """
</table>
    """
    return

if f.has_key('user'):
	print "<div>"
	print "<br/>"
    s = f['user'].value.replace('_', ' ')
    s = s[0].upper() + s[1:]
	editcount(s)
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
