#! /usr/bin/env python

import xmlrpclib
import os, sys, time, socket
from getopt import getopt

trivial = False

rpc = 'http://confluence.ts.wikimedia.org/rpc/xmlrpc'

loginfile = os.getenv("HOME") + "/.addlog"

servername = socket.gethostname()
loguser = os.getlogin()
(opts, args) = getopt(sys.argv[1:], "hs:u:t")

for v in opts:
	if v[0] == '-s':
		servername = v[1]
	elif v[0] == '-h':
		print "usage: addlog [-ht] [-s <server>] [-u <user>] <log text ...>"
		print "    -s <server>      specify which server this entry applies to"
		print "                     (default: current server)"
		print "    -u <user>        select the username for this log entry"
		print "                     (default: current user)"
		print "    -t               mark this entry as trivial (will show as"
		print "                     grey in the log)"
		print "    -h               this text"
		sys.exit(0)
	elif v[0] == '-t':
		trivial = True
	elif v[0] == '-u':
		loguser = v[1]

h = open(loginfile, "r")
username = h.readline()[:-1]
password = h.readline()[:-1]
h.close()

c = xmlrpclib.Server(rpc)
auth = c.confluence1.login(username, password)
log = c.confluence1.getPage(auth, "tech", "Maintenance log")


lines = log['content'].replace("\r\n", "\n").split("\n")
newlines = []

# Look for the start of the log
gotnew = False
for l in lines:
	newlines.append(l)
	if l == "BEGIN LOG":
		# insert the new entry
		template = "* %s: %s: *%s*: %s"
		if trivial == True:
			template = "* {color:grey}%s: %s: *%s*: %s{color}"
		newlines.append(template % (time.strftime("%Y-%m-%d %H:%M"), loguser, servername, " " .join(args)))
		gotnew = True

if gotnew == False:
	print "Couldn't find where to insert log entry!"
	print "Page text:"
	for l in lines:
		print "[%s]" % l
	sys.exit(1)

log["content"] = "\n".join(newlines)
r = c.confluence1.storePage(auth, log)
