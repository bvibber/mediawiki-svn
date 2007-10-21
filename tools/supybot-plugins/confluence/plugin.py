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

class confluence(callbacks.Plugin):
    def __init__(self, irc):
        self.__parent = super(confluence, self)
        self.__parent.__init__(irc)

    def confluence(self, irc, msg, args, terms):
        """Search confluence"""

        soap = SOAPpy.WSDL.Proxy(self.registryValue('URL') + '/rpc/soap-axis/confluenceservice-v1?wsdl')
        results = soap.search("", terms, 5);

        if len(results) == 0:
            irc.reply("no results for %s" % terms)
            return

        r = ''
        for s in results:
            if len(r) > 0:
                r += "; "
            r += "%s <%s>" % (s['title'], s['url'])

        irc.reply(r)

    confluence = wrap(confluence, ['text'])

Class = confluence

# vim:set shiftwidth=4 tabstop=4 expandtab textwidth=79:
