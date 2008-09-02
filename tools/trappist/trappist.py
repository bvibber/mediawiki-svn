#! /usr/bin/python

import sys, getopt, re, socket, threading
from ircbot import SingleServerIRCBot

# Import PySNMP modules
from pysnmp import asn1, v1, v2c
from pysnmp import role

ircchan = '#channel'
ircnick = 'wmtrappist'
ircserver = 'irc.freenode.net'

class EchoBot(SingleServerIRCBot):
    def __init__(self, chans, nickname, server):
        print "*** Connecting to IRC server %s..." % server
        SingleServerIRCBot.__init__(self, [(server, 6667)], nickname, "Trappist")
        self.chans = chans
 
    def on_nicknameinuse(self, c, e):
        c.nick(c.get_nickname() + "_")
 
    def on_welcome(self, c, e):
        print "*** Connected"
        for chan in self.chans:
            c.join(chan)

bot = EchoBot([ircchan], ircnick, ircserver)

class EchoThread(threading.Thread):
    def __init__(self):
        threading.Thread.__init__(self)
        self.abot = bot

    def run(self):
        bot.start()

et = EchoThread()
et.start()

# Initialize help messages
options =           'Options:\n'
options = options + '  -p       port to listen for requests from managers. Default is 8162.\n'
usage = 'Usage: %s [options] [local-interface] [community]\n' % sys.argv[0]
usage = usage + options
    
# Initialize defaults
port = 8162
iface = '0.0.0.0'
community = None

# Parse possible options
try:
    (opts, args) = getopt.getopt(sys.argv[1:], 'hp:',\
                                 ['help', 'port='])
except getopt.error, why:
    print 'getopt error: %s\n%s' % (why, usage)
    sys.exit(-1)

try:
    for opt in opts:
        if opt[0] == '-h' or opt[0] == '--help':
            print usage
            sys.exit(0)
        
        if opt[0] == '-p' or opt[0] == '--port':
            port = int(opt[1])

except ValueError, why:
    print 'Bad parameter \'%s\' for option %s: %s\n%s' \
          % (opt[1], opt[0], why, usage)
    sys.exit(-1)

# Parse optional arguments
if len(args) > 0:
    iface = args[0]
if len(args) > 1:
    community = args[1]
    
# Create SNMP agent object
server = role.agent(ifaces=[(iface, port)])

def addircmsg(s):
    print s
    try:
        bot.connection.notice(ircchan, s)
    except:
        pass


def hostname(ip):
    try:
        r = socket.gethostbyaddr(ip)
        if r == None:
            return ip
        return r[0]
    except:
        return ip

# Listen for SNMP messages from remote SNMP managers
while 1:
    # Receive a request message
    (question, src) = server.receive()

    # Decode request of any version
    (req, rest) = v2c.decode(question)

    # Decode BER encoded Object IDs.
    oids = map(lambda x: x[0], map(asn1.OBJECTID().decode, \
                                   req['encoded_oids']))

    # Decode BER encoded values associated with Object IDs.
    vals = map(lambda x: x[0](), map(asn1.decode, req['encoded_vals']))
    
    # Print it out
    print 'SNMP message from: ' + str(src)
    print 'Version: ' + str(req['version']+1) + ', type: ' + str(req['tag'])
    if req['version'] == 0:
        print 'Enterprise OID: ' + str(req['enterprise'])
        print 'Trap agent: ' + str(req['agent_addr'])
        for t in v1.GENERIC_TRAP_TYPES.keys():
            if req['generic_trap'] == v1.GENERIC_TRAP_TYPES[t]:
                print 'Generic trap: %s (%d)' % (t, req['generic_trap'])
                break
        else:
            print 'Generic trap: ' + str(req['generic_trap'])
        print 'Specific trap: ' + str(req['specific_trap'])
        print 'Time stamp (uptime): ' + str(req['time_stamp'])

    for (oid, val) in map(None, oids, vals):
        print oid + ' ---> ' + str(val)

    # Verify community name if needed
    if community is not None and req['community'] != community:
        print 'WARNING: UNMATCHED COMMUNITY NAME: ' + str(community)
        continue

    soid = str(oid)
    sip = src[0]
    if soid == '.1.3.6.1.4.1.1991.1.1.2.1.44.0':
        if req['specific_trap'] == 66: # BGP peer down
            m = re.search(r'^\s*BGP Peer ([0-9a-fA-F:.]+) DOWN \((.*)\)\s*$', str(val))
            if m != None:
                addircmsg("[%s] BGP peer %s <%s>, session is now down because <%s>" % (
                            hostname(sip), m.group(1), hostname(m.group(1)),
                            m.group(2)))
        elif req['specific_trap'] == 65: # BGP peer up
            m = re.search(r'^\s*BGP Peer ([0-9a-fA-F:.]+) UP (\(.*\))\s*$', str(val))
            if m != None:
                addircmsg("[%s] BGP peer %s <%s>, session is now up" % (
                            hostname(sip), m.group(1), hostname(m.group(1))))
                

