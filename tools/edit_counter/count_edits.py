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
import os
import cgi
import sha
import time

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
    /*margin-left: auto;
    margin-right: auto;*/
    float: left;
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
<body>
<h2>edit count news!!</h2>
<p>the edit counter was broken for a few days.  apparently this caused quite a
bit of concern, so in future, it'll print a nice error message instead of
that nasty blank page.
</p>
"""

def thishour():
    n = time.gmtime()
    return n[3]
def lasthour():
    n = thishour()
    if n > 0:
        return n -1
    return 24
def mktoken(name, this):
    ip = os.getenv("REMOTE_ADDR")
    ip = ".".join(ip.split('.')[:2])
    hash = sha.new("%s%s%s%s" % (secret, ip, this, name)).hexdigest()
    return hash
def checkhash(name):
    try:
        theirhash = f['hash'].value
    except KeyError:
        return False
    return (theirhash == mktoken(name, thishour())) or (theirhash == mktoken(name, lasthour()))

namespaces = {
	0: 'Articles',
	1: 'Talk',
	2: 'User',
	3: 'User talk',
	4: 'Project *',
	5: 'Project talk *',
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

def confirmcount(user):
    if checkhash(user):
        return True
    print """
<form action="count_edits" method="get">
<p>(Note: some people (particularly AOL users) were having problems with the
"new" version not working.  that should be fixed now, please tell me if it's
still broken.)</p>
<p>
users are reminded (in a nice way, of course) that number of edits is only a
very small part of a user's Wikipedia contributions.  any number of factors -
the ability to work with other users, write good articles, etc. - are far
more important than this single number, and you can't find out about those
from a user's edit count.
</p>
<p>
if you're here because you're about to vote on an RfA, consider instead
<em>not</em> looking at their edit count, but instead their contributions.
there's now an "Earliest" link there that you can use to find their first
edit, if you're worried about how long they've been around.  have a look at
a few diffs, see how good their edits are, how well they conduct themselves
on talk pages, and so on.  maybe edit count isn't that important, after all.
</p>
<p>
if you're checking your <em>own</em> edit count&hellip; well, do you really care? ;-)
</p>
<input type="hidden" name="hash" value="%s" />
<input type="hidden" name="user" value="%s" />
<input type="hidden" name="dbname" value="%s" />
<input type="submit" value="i understand, show me the edit count" />
<p>
(PS: some people seem to dislike this warning.  well, i am sorry about that,
but perhaps you can see it from my point of view.</p>
<p>i wrote the edit counter
because, sometimes, it <em>is</em> interesting to view someone's edit count,
and the old method (using Special:Contributions) was a real pain.  unfortunately,
what i didn't foresee is that an easy way to count people's edit would lead
to edit counts becoming quite a bit more important in the general scheme
of things.</p>
<p>
this makes me quite sad, but i don't want to remove the edit counter; like i say,
it does have legitimite uses, and since MW 1.5, the Special:Contributions method
doesn't work at all. (unless you're really, really patient).  but i did want
to make people stop and think about how useful an edit count really is.</p>
<p>did i
manage that?  i don't know... people who agree with me have said they like the
warning, but people who disagree just focus their attention on disliking the
warnings, rather than reconsidering edits.  still, even if i can annoy people into
using the edit counter less, that's a minor victory ;-)
</p>
<p>and lastly... yes, this is just my personal opinion.  but then, it is my 
edit counter :-)</p>
</form>
""" % (mktoken(user, thishour()), cgi.escape(user), cgi.escape(dbname));
    return False

def editcount(user):
    if not confirmcount(user):
        return
    ns = dict()
    total = 0
    try:
        db = MySQLdb.connect(db=dbname, host=dbserver, user=dbuser, passwd=dbpassword)
    except:
        print """
        <p><strong>onoes!!</strong> some kind of database error occured... please try again later</p>
        """;
        return
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
    if total == 0:
        print """<p><strong>this user does not seem to have any edits, or they don't exist</strong></p>""";
        return

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
<br clear='all'/>
<p>* 'Project' and 'Project talk' refer to the project namespace: Wikipedia, Wiktionary, Meta, etc.</p>
    """
    return

if f.has_key('user'):
    print "<div>"
    print "<br/>"
    s = f['user'].value.replace('_', ' ')
    s = s[0].upper() + s[1:]
    editcount(s)
    print "</div>"

if f.has_key('user0'):
    for j in range(0, 15):
        key = "user%d" % j
        if not f.has_key(key):
            break
        editcount(f[key].value.replace('_', ' '))

print """
<br clear="all"/>
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
<hr>
<p style='font-size: smaller'>
Aggregated data on user edits for all users of Wikimedia projects is made available to the general public under
the terms of the <a href="http://wikimediafoundation.org/wiki/Privacy_policy#User_data">Wikimedia
Foundation privacy policy</a>.   No data is available via the "edit counter" which is not already available
via the "user contributions" pages on the wiki itself, or as part of the periodic database dumps.
If you do not wish your edit data to be made publically available, you should not make any edits or contributions
to Wikimedia projects.  Requests to remove user data from the "edit counter" will be ignored.
</p>
</form>
</body>
</html>
"""
