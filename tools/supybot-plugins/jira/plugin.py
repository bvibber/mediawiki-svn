###

# Copyright (c) 2007, River Tarnell
# All rights reserved.
#
#
###

import supybot.utils as utils
from supybot.commands import *
import supybot.plugins as plugins
import supybot.ircutils as ircutils
import supybot.callbacks as callbacks

import SOAPpy

import time
from time import mktime, strptime

class jira(callbacks.Plugin):

    def __init__(self, irc):
        self.__parent = super(jira, self)
        self.__parent.__init__(irc)
        self.soap = SOAPpy.WSDL.Proxy(self.registryValue('URL') + '/rpc/soap/jirasoapservice-v2?wsdl')
        self.auth = self.soap.login(self.registryValue('username'), self.registryValue('password'))
        self.issuetypes = self.soap.getIssueTypes(self.auth)
        self.subtaskissuetypes = self.soap.getSubTaskIssueTypes(self.auth)
        self.statuses = self.soap.getStatuses(self.auth)
        self.resolutions = self.soap.getResolutions(self.auth)
        self.projects = self.soap.getProjects(self.auth)

    def bug(self, irc, msg, args, bug):
        """Get information about a bug"""

        try:
            issue = self.soap.getIssue(self.auth, bug)
        except Exception, value:
            irc.reply(value.faultstring)
            return

        for p in self.projects:
            if p['key'] == issue['project']:
                proj = p
                break

 #       proj = self.soap.getProjectByKey(self.auth, issue['project'])

        key = issue['key']
        type = None
        for t in self.issuetypes:
            if t['id'] == issue['type']:
                type = t['name']

        if type == None:
            for t in self.subtaskissuetypes:
                if t['id'] == issue['type']:
                    type = t['name']

        project = proj['name']
        reporter = issue['reporter']
        assignee = issue['assignee']
        statusnr = issue['status']
        for s in self.statuses:
            if statusnr == s['id']:
                status = s['name']
        
        resnr = issue['resolution']
        res = resnr
        for r in self.resolutions:  
            if resnr == r['id']:
                res = r['name']
        reply = "\002%s\002: %s; \002Project:\002 %s; \002Type:\002 %s; \002Status:\002 %s; " % (
                key, issue['summary'], project, type, status)

        if res != None:
            reply += "\002Resolution:\002 %s; " % res

        reply += "\002Reporter:\002 %s; \002Assignee:\002 %s; <%s/browse/%s>" % (
                reporter, assignee, self.registryValue('url'), key)
        irc.reply(reply)

    bug = wrap(bug, ['anything'])
    issue = bug

Class = jira 

# vim:set shiftwidth=4 tabstop=4 expandtab textwidth=79:
