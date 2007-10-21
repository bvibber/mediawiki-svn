#! /usr/bin/env python

import xmlrpclib
import os, sys, time, socket

rpc = 'http://confluence.ts.wikimedia.org/rpc/xmlrpc'

loginfile = os.getenv("HOME") + "/.addlog"

h = open(loginfile, "r")
username = h.readline()[:-1]
password = h.readline()[:-1]
h.close()

c = xmlrpclib.Server(rpc)
auth = c.confluence1.login(username, password)
log = c.confluence1.getPage(auth, "tech", "Maintenance log")

lines = log['content'].split("\n")
newlines = []

# Look for the start of the log
for l in lines:
	newlines.append(l)
	if l == "BEGIN LOG":
		# insert the new entry
		newlines.append("* %s: %s: *%s*: %s" % (time.strftime("%Y-%m-%d %H:%M"), os.getlogin(), socket.gethostname(), " " .join(sys.argv[1:])))
	

log["content"] = "\n".join(newlines)
c.confluence1.storePage(auth, log)
