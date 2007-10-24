#! /usr/bin/env python

import time, os, sys, tempfile
import SOAPpy
from getopt import getopt
#from JiraSoapServiceService_services_types import *
#from JiraSoapServiceService_services import *
#import JiraSoapServiceService_services_types

loginfile = "%s/.jirarc" % os.getenv("HOME")

jusername = ""
jpassword = ""
jurl = ""

usage = """usage: 
     jira get <issue>
     jira resolve [-c] <issue>
     jira close [-c] <issue>
     jira reopen [-c] <issue>
     jira comment <issue>

  -c:   leave a comment with this action

  To authenticate to JIRA, create a file called $HOME/.jirarc with the
  contents:

      username <user>
      password <pass>
      url <URL to JIRA>
"""

(opts, args) = getopt(sys.argv[1:], "h")

for o in opts:
	if o[0] == "-h":
		print usage
		sys.exit(1)

try:
	h = open(loginfile, "r")
except Exception, value:
	print "can't open %s: %s" % (loginfile, value)
	sys.exit(1)

while True:
	line = h.readline()
	if line == "":
		break

	if line[0] == '#':
		continue

	line = line[:-1]

	(opt, value) = line.split(' ', 1)
	if opt == "username":
		jusername = value
	elif opt == "password":
		jpassword = value
	elif opt == "url":
		jurl = value
	else:
		print "invalid option '%s' in %s" % (opt, loginfile)
		sys.exit(1)

if jusername == "":
	print "username not set in %s" % loginfile
	sys.exit(1)

if jpassword == "":
	print "password not set in %s" % loginfile
	sys.exit(1)

if jurl == "":
	print "URL not set in %s" % loginfile
	sys.exit(1)

try:
	#loc = JiraSoapServiceServiceLocator()
	#jira = loc.getJiraSoapService()
        jira = SOAPpy.WSDL.Proxy(jurl + '/rpc/soap/jirasoapservice-v2?wsdl')
except Exception, value:
	print "can't create SOAP object: %s" % value
	sys.exit(1)

try:
	#loginreq = loginRequest()
	##loginreq._in0 = jusername
	#loginreq._in1 = jpassword
	#auth = jira.login(loginreq)
	auth = jira.login(jusername, jpassword)
except Exception, value:
	print "can't log in: %s" % value
	sys.exit(1)

if len(sys.argv) < 2:
	print usage
	sys.exit(1)

def c_get(jira, auth, args):
	key = args[0]

	print ""

	try:
		issue = jira.getIssue(auth, key)
	except Exception, value:
		print "can't retrieve issue %s: %s" % (key, value.faultstring)
		return 1

	reporterusername = issue['reporter']
	assigneeusername = issue['assignee']

	if assigneeusername != None:
		assigneeuser = jira.getUser(auth, assigneeusername)
	reporteruser = jira.getUser(auth, reporterusername)

	statuses = jira.getStatuses(auth)
	resolutions = jira.getResolutions(auth)
	types = jira.getIssueTypes(auth)
	priorities = jira.getPriorities(auth)

	project = jira.getProjectByKey(auth, issue['project'])
	projectname = project['name']

	for s in statuses:
		if s['id'] == issue['status']:
			status = s['name']
			break

	type = None
	for t in types:
		if t['id'] == issue['type']:
			type = t['name']
			break

	for p in priorities:
		if p['id'] == issue['priority']:
			priority = p['name']
			break

	if type == None:
		subtypes = jira.getSubTaskIssueTypes(auth)
		for t in subtypes:
			if t['id'] == issue['type']:
				type = t['name']
				break
	
	if issue['resolution'] == None:
		resolution = None
	else:
		for r in resolutions:
			if r['id'] == issue['resolution']:
				resolution = r['name']
				break
		
	comments = jira.getComments(auth, issue['key'])

	print "Summary:            [%s] %s" % (issue['key'], issue['summary'])
	#print "Created:            %s" % time.strftime("%Y-%m-%d", issue['created'])
	print "Created:            %04d-%02d-%02d %02d:%02d:%02d" % (issue['created'][0], issue['created'][1],
		issue['created'][2], issue['created'][3], 
		issue['created'][4], issue['created'][5])
	print "Updated:            %04d-%02d-%02d %02d:%02d:%02d" % (issue['updated'][0], issue['updated'][1],
		issue['updated'][2], issue['updated'][3], 
		issue['updated'][4], issue['updated'][5])
	if issue['duedate'] != None:
		print "Due date:           %d-%d-%d" % (issue['duedate'][0], issue['duedate'][1], issue['duedate'][2])
	print "Project:            %s" % projectname

	if len(issue['components']) > 0:
		print "Component(s):       %s" % (", ".join(c['name'] for c in issue['components']))

	if len(issue['affectsVersions']) > 0:
		print "Affects version(s): %s" % (", ".join(v['name'] for v in issue['affectsVersions']))

	if len(issue['fixVersions']) > 0:
		print "Fix version(s):     %s" % (", ".join(v['name'] for v in issue['fixVersions']))

	print "Type:               %s" % type
	print "Priority:           %s" % priority
	print "Status:             %s" % status
	print "Votes:              %s" % issue['votes']
	print "Reporter:           %s <%s>" % (reporteruser['fullname'], reporteruser['email'])
	if assigneeusername != None:
		print "Assignee:           %s <%s>" % (assigneeuser['fullname'], assigneeuser['email'])
	else:
		print "Assignee:           Unassigned"	
	if resolution != None:
		print "Resolution:         %s" % resolution

	if issue['environment'] != None:
		print "\nEnvironment:\s  %s\n" % "\n  ".join(issue['environment'].split("\n"))

	if issue['description'] == None:
		print "\nNo description."
	else:
		print "\nDescription:\n  %s\n" % "\n  ".join(issue['description'].split("\n"))

	if len(comments) == 0:
		print "\nNo comments."
	else:
		i = 1
		for c in comments:
			print "Comment #%s from %s:\n  %s\n" % (i, c['author'], "\n  ".join(c['body'].split("\n")))
			i += 1

	return 0

def c_search(jira, auth, args):
	terms = " ".join(args)
	
	results = jira.getIssuesFromTextSearch(auth, terms)
	if len(results) == 0:
		print "No results."
		return 0

	maxkey = max(len(i['key']) for i in results)
	maxsumm = max(len(i['summary']) for i in results)

	print "%-*s    Summary" % (maxkey, "Key")
	print '=' * (maxkey + 4 + maxsumm)

	for issue in results:
		print "%-*s    %s" % (maxkey, issue['key'], issue['summary'])

	return 0

def getStatusId(jira, auth, name):
	statuses = jira.getStatuses(auth)
	for s in statuses:
		if s['name'] == name:
			return s['id']

def getActionId(jira, auth, key, action):
	actions = jira.getAvailableActions(auth, key)
	for a in actions:
		if action == a['name']:
			return a['id']

class RemoteFieldValue:
	pass

def runEditor(desc):
	(fd, fname) = tempfile.mkstemp()
	f = os.fdopen(fd, "w")
	f.write("JIRA: %s\n" % desc)
	f.close()
	
	editor = os.getenv("EDITOR")
	if editor == None:
		editor = "vi"
	os.system("%s %s" % (editor, fname))
	f = file(fname, "r")
	text = ''
	while True:
		l = f.readline()
		if l == "":
			break

		l = l[:-1]
		if l[0:6] == 'JIRA: ':
			continue

		text += "%s\n" % l

	return text

class RemoteComment:
	pass

def c_comment(jira, auth, args):
	text = runEditor("Type your comment below:")
	comment = RemoteComment()
	comment.body = text
	jira.addComment(auth, args[0], comment)
	return 0

def doWorkflow(jira, auth, args, action):
	(opts, args) = getopt(args, "c")
	comment = None
	for o in opts:
		if o[0] == "-c":
			comment = RemoteComment()
			comment.body = runEditor("Type your comment below:")

	key = args[0]
	actid = getActionId(jira, auth, key, action)
	jira.progressWorkflowAction(auth, key, actid, [])

	if comment != None:
		jira.addComment(auth, args[0], comment)
		
	return 0

commands = {
	'get': c_get,
	'search': c_search,
	'comment': c_comment,
	'reopen': lambda jira, auth, args: doWorkflow(jira, auth, args, 'Reopen Issue'),
	'resolve': lambda jira, auth, args: doWorkflow(jira, auth, args, 'Resolve Issue'),
	'close': lambda jira, auth, args: doWorkflow(jira, auth, args, 'Close Issue'),
}

if not commands.has_key(args[0]):
	print "unknown command: %s" % args[0]
	print usage
	sys.exit(1)

sys.exit(commands[args[0]](jira, auth, args[1:]))
