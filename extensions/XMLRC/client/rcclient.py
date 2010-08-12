#!/usr/bin/python

##############################################################################
# XMPP client for XMLRC
# 
# 
#  Copyright (c) 2010, Wikimedia Deutschland; Author: Daniel Kinzler
#  All rights reserved.
# 
#    This program is free software: you can redistribute it and/or modify
#    it under the terms of the GNU General Public License as published by
#    the Free Software Foundation, either version 3 of the License, or
#    (at your option) any later version.
#
#    This program is distributed in the hope that it will be useful,
#    but WITHOUT ANY WARRANTY; without even the implied warranty of
#    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#    GNU General Public License for more details.
#
#    You should have received a copy of the GNU General Public License
#    along with this program.  If not, see <http://www.gnu.org/licenses/>.
##############################################################################

import sys, os, os.path, traceback, datetime
import ConfigParser, optparse
import select, xmpp # using the xmpppy library <http://xmpppy.sourceforge.net/>, GPL


LOG_MUTE = 0
LOG_QUIET = 10
LOG_VERBOSE = 20
LOG_DEBUG = 30

##################################################################################
class RecentChange(object):
    """ Represence a RecentChanges-Record. Properties of a change can be accessed
	using item syntax (e.g. rc['revid']) or attribute syntax (e.g. rc.revid). """

    flags = set( ( 'anon', 'bot', 'minor' ) )
    numerics = set( ( 'rcid', 'pageid', 'revid', 'old_revid', 'newlen', 'oldlen', 'ns' ) )
    times = set( ( 'timestamp' ) )

    def __init__(self, dom):
	self.dom = dom

    def get_property(self, prop):
	a = self.dom.getAttr(prop)

	if prop in RecentChange.flags:
	    if a is None or a is False:
		return False
	    else:
		return True
	elif a is None:
	    a = self.dom.getTag(prop)
	    # TODO: wrap for conversion. known tags: <tags>, <param>, <block>
	elif a in RecentChange.numerics:
	    a = int( a )
	elif a in RecentChange.times:
	    a = datetime.strptime( a, '%Y-%m-%dT%H:%M:%S%Z' ) # 2010-10-12T08:57:03Z

        return a

    def __getitem__(self, prop):
        return self.get_property(prop)

    def __getattr__(self, prop):
        return self.get_property(prop)

class RCHandler(object):
    """ Base class for RC hanlders. Instances compatible with this class
	can be registered with RCClient.add_handler(). Their process(rc)
	method will then be called for every change record received. """

    def process(self, rc):
	pass

class RCEcho(RCHandler):
    props = ( 'rcid', 'timestamp', 'type', 'ns', 'title', 'pageid', 'revid', 'old_revid', 
	      'user', 'oldlen', 'newlen', 'comment', 'logid', 'logtype', 'logaction' )

    def process(self, rc):
	for p in RCEcho.props:
	    self.print_prop(rc, p)

    def print_prop(self, rc, prop):
	v = rc[prop]
	if v is None: v = ''

	print "%s: %s" % (prop, v) # XXX: check encoding crap

##################################################################################

class RCClient(object):
    def __init__( self, console_encoding = 'utf-8' ):
	self.console_encoding = console_encoding
	self.handlers = []
	self.loglevel = LOG_VERBOSE

	self.xmpp = None
	self.jid = None

        self.group = None
        self.nick = None

    def warn(self, message):
	if self.loglevel >= LOG_QUIET:
	    sys.stderr.write( "WARNING: %s\n" % ( message.encode( self.console_encoding ) ) )

    def info(self, message):
	if self.loglevel >= LOG_VERBOSE:
	    sys.stderr.write( "INFO: %s\n" % ( message.encode( self.console_encoding ) ) )

    def debug(self, message):
	if self.loglevel >= LOG_DEBUG:
	    sys.stderr.write( "DEBUG: %s\n" % ( message.encode( self.console_encoding ) ) )

    def service_loop( self ):
	sockets = ( self.xmpp.Connection._sock, )

	self.online = 1

	while self.online:
	    (in_socks , out_socks, err_socks) = select.select(sockets, [], sockets, 1)

	    for sock in in_socks:
		try:
		    self.xmpp.Process(1)

		    if not self.xmpp.isConnected(): 
			self.warn("connection lost, reconnecting...")
			
			if self.xmpp.reconnectAndReauth():
			    self.warn("re-connect successful.")
			    self.on_connect()

		except Exception, e:
		    error_type, error_value, trbk = sys.exc_info()
		    self.warn( "Error while processing! %s" % "  ".join( traceback.format_exception( error_type, error_value, trbk ) ) )
		    # TODO: detect when we should kill the loop because a connection failed

	    for sock in err_socks:
		    raise Exception( "Error in socket: %s" % repr(sock) )

	self.info("service loop terminated, disconnecting")

	for sock in sockets:
	    con.close()

	self.info("done.")

    def process_message(self, con, message):
        if (message.getError()):
            self.warn("received %s error from <%s>: %s" % (message.getType(), message.getError(), message.getFrom() ))
	elif message.getBody():
	    rc_dom = message.T.rc
	    if rc_dom:
		self.debug("RC %s message from <%s>: %s" % (message.getType(), message.getFrom(), message.getBody().strip() ))
		rc = RecentChange( rc_dom )
		self.dispatch_rc( rc )
	    else:
		self.info("plain %s message from <%s>: %s" % (message.getType(), message.getFrom(), message.getBody().strip() ))

    def dispatch_rc(self, rc):
	for h in self.handlers:
	    h.process( rc )

    def add_handler(self, handler):
	self.handlers.append( handler )

    def remove_handler(self, handler):
	self.handlers.remove( handler )

    def guess_local_resource(self):
	resource = "%s-%d" % ( socket.gethostname(), os.getpid() ) 
	
	return resource;

    def connect( self, jid, password ):

	if type( jid ) != object:
	    jid = xmpp.protocol.JID( jid )

	if jid.getResource() is None:
	    jid = xmpp.protocol.JID( host= jid.getHost(), node= jid.getNode(), resource = self.guess_local_resource() )

	self.xmpp = xmpp.Client(jid.getDomain(),debug=[])
        con= self.xmpp.connect()

        if not con:
            self.warn( 'could not connect to %s!' % jid.getDomain() )
            return False

        self.debug( 'connected with %s' % con )

        auth= self.xmpp.auth( jid.getNode(), password, resource= jid.getResource() )

        if not auth:
            self.warn( 'could not authenticate as %s!' % jid )
            return False

        self.debug('authenticated using %s as %s' % ( auth, jid ) )

        self.xmpp.RegisterHandler( 'message', self.process_message )

	self.jid = jid;
        self.info( 'connected as %s' % ( jid ) )

	self.on_connect()

        return con

    def on_connect( self ):
        self.xmpp.sendInitPresence(self)
        self.roster = self.xmpp.getRoster()

	if self.group:
		self.join( self.group )

    def join(self, group, nick = None):
	if not nick:
	    nick = self.jid.getNode()

	if type( group ) != object:
	    group = xmpp.protocol.JID( group )

	# use our own desired nickname as resource part of the group's JID
	gjid = group.getStripped() + "/" + nick; 

	#create presence stanza
	join = xmpp.Presence( to= gjid )

	#announce full MUC support
	join.addChild( name = 'x', namespace = 'http://jabber.org/protocol/muc' ) 

	self.xmpp.send( join )

	self.info( 'joined room %s' % self.jid.getStripped() )

	self.group = group
	self.nick = nick

	return True

##################################################################################

if __name__ == '__main__':

    # find the location of this script
    bindir=  os.path.dirname( os.path.realpath( sys.argv[0] ) )
    extdir=  os.path.dirname( bindir )

    # set up command line options........
    option_parser = optparse.OptionParser()
    option_parser.add_option("--config", dest="config_file", 
				help="read config from FILE", metavar="FILE")

    option_parser.add_option("--quiet", action="store_const", dest="loglevel", const=LOG_QUIET, default=LOG_VERBOSE, 
				help="suppress informational messages, only print warnings and errors")

    option_parser.add_option("--debug", action="store_const", dest="loglevel", const=LOG_DEBUG, 
				help="print debug messages")

    option_parser.add_option("--group", "--muc", dest="group", default=None,
				help="join MUC chat group")

    option_parser.add_option("--nick", dest="nick", metavar="NICKNAME", default=None,
				help="use NICKNAME in the MUC group")

    (options, args) = option_parser.parse_args()

    # find config file........
    if options.config_file:
	cfg = options.config_file #take it from --config
    else:
        cfg = extdir + "/../../rcclient.ini" #installation root

	if not os.path.exists( cfg ):
		cfg = extdir + "/../../phase3/rcclient.ini" #installation root in dev environment

	if not os.path.exists( cfg ):
		cfg = bindir + "/rcclient.ini" #extension dir

    # define config defaults........
    config = ConfigParser.SafeConfigParser()

    config.add_section( 'XMPP' )
    config.set( 'XMPP', 'group', '' )
    config.set( 'XMPP', 'nick', '' )

    # read config file........
    if not config.read( cfg ):
	sys.stderr.write( "failed to read config from %s\n" % cfg )
	sys.exit(2)

    jid = config.get( 'XMPP', 'jid' )
    password = config.get( 'XMPP', 'password' )

    if options.group is None:
	group = config.get( 'XMPP', 'group' )
    else:
	group = options.group

    if options.nick is None:
	nick = config.get( 'XMPP', 'nick' )
    else:
	nick = options.nick

    if group == '': group = None
    if nick == '': nick = None

    # create rc client instance
    client = RCClient( )
    client.loglevel = options.loglevel

    # -- DO STUFF -----------------------------------------------------------------------------------

    # connect................
    if not client.connect( jid = jid, password = password ):
	sys.exit(1)

    if group:
	client.join( group, nick )

    # run relay loop................
    client.service_loop( )

    print "done."
    