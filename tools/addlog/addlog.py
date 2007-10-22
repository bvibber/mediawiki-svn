#! /usr/bin/env python

import xmlrpclib
import os, sys, time, socket
from getopt import getopt

rpc = 'http://confluence.ts.wikimedia.org/rpc/xmlrpc'

loginfile = os.getenv("HOME") + "/.addlog"

servername = socket.gethostname()
(opts, args) = getopt(sys.argv[1:], "hs:")

for v in opts:
	if v[0] == '-s':
		servername = v[1]
	elif v[0] == '-h':
		print "usage: addlog [-s server] [-h] <log text ...>"
		sys.exit(0)

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
		newlines.append("* %s: %s: *%s*: %s" % (time.strftime("%Y-%m-%d %H:%M"), os.getlogin(), servername, " " .join(args)))
		gotnew = True

if gotnew == False:
	print "Couldn't find where to insert log entry!"
	print "Page text:"
	for l in lines:
		print "[%s]" % l
	sys.exit(1)

log["content"] = "\n".join(newlines)
r = c.confluence1.storePage(auth, log)
