#!/usr/bin/python

##############################################################################
# UDP to XMPP relay server for XMLRC
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

import sys, os, os.path, traceback
import ConfigParser, optparse
import select, socket, urllib
import xmpp, xmpp.simplexml # using the xmpppy library <http://xmpppy.sourceforge.net/>, GPL

from xml.parsers.expat import ExpatError

LOG_MUTE = 0
LOG_QUIET = 10
LOG_VERBOSE = 20
LOG_DEBUG = 30

################################################################################
class WikiInfo(object):
    def __init__( self, config ):
	self.config = config

    def get_wikis( self ):
	return self.config.sections()

    def get_wiki_property( self, wiki, prop ):
	if not self.config.has_section( wiki ): 
	    return None

	if not self.config.has_option( wiki, prop ): 
	    return None

	return self.config.get( wiki, prop )

    def get_wiki_url( self, wiki, prop, suffix ):
	u = self.get_wiki_property( wiki, prop )
	if u is None:
	    u = self.get_wiki_base_url( wiki )
	    if not u is None:
		u = u + suffix

	return u

    def get_wiki_base_url( self, wiki ):
	return self.get_wiki_property( wiki, 'base-url' )

    def get_wiki_page_url( self, wiki ):
	return self.get_wiki_url( wiki, 'page-url', 'index.php/$1' )

    def get_wiki_script_url( self, wiki ):
	return self.get_wiki_url( wiki, 'script-url', 'index.php' )

    def get_wiki_api_url( self, wiki ):
	return self.get_wiki_url( wiki, 'api-url', 'api.php' )

    def get_wiki_channel_type( self, wiki ):
	return self.get_wiki_property( wiki, 'channel-type' )

    def get_wiki_channel_spec( self, wiki ):
	return self.get_wiki_property( wiki, 'channel' )

class Relay(object):
    def __init__( self, wiki_info, console_encoding = 'utf-8' ):
	self.console_encoding = console_encoding
	self.channels = {}
	self.loglevel = LOG_VERBOSE
	self.wiki_info = wiki_info;

    def warn(self, message):
	if self.loglevel >= LOG_QUIET:
	    sys.stderr.write( "WARNING: %s\n" % ( message.encode( self.console_encoding ) ) )

    def info(self, message):
	if self.loglevel >= LOG_VERBOSE:
	    sys.stderr.write( "INFO: %s\n" % ( message.encode( self.console_encoding ) ) )

    def debug(self, message):
	if self.loglevel >= LOG_DEBUG:
	    sys.stderr.write( "DEBUG: %s\n" % ( message.encode( self.console_encoding ) ) )

    def get_all_channels(self):
	return self.channels.values()

    def get_channel( self, name ):
	return self.channels.get( name )

    def add_channel( self, name, channel ):
	self.channels[ name ] = channel
	self.debug("added channel " + name)

    def create_channels( self, names, factories ):
	for wiki in names:
	    t = self.wiki_info.get_wiki_channel_type( wiki )
	    x = self.wiki_info.get_wiki_channel_spec( wiki )

	    f = factories[ t ]
	    channel = f( x )

	    channel.join() #FIXME: error detection / recovery!

	    self.add_channel( wiki, channel )

    def broadcast_message( self, message, xml = None ):
        targets = self.get_all_channels()

	for t in targets:
	    t.send_message( message, xml = xml )

    def process_command(self, line):
	args = line.split()
	command = args[0][1:]
	args = args[1:]

	self.debug( "processing command: %s" % command )

        if ( command == 'quit' ):
    	    self.online = False
        #elif ( command.startswith( '/' ) ):
    	#    self.broadcast_message( line[1:] )
        elif ( command == 'send' ):
    	    self.broadcast_message( ' '.join(args) )
        elif ( command == 'debug' ):
    	    self.loglevel = LOG_DEBUG
        elif ( command == 'verbose' ):
    	    self.loglevel = LOG_VERBOSE
        elif ( command == 'quiet' ):
    	    self.loglevel = LOG_QUIET
	else:
	    self.warn( "unknwon command: %s" % command )

    def get_rc_text( self, rc ):
	rc_id = rc.getAttr( 'rcid' )
	rc_type = rc.getAttr( 'type' )

	if rc_id is None or rc_type is None:
	    return False

	rc_title = rc.getAttr( 'title' )
	rc_user = rc.getAttr( 'user' )
	rc_comment = rc.getAttr( 'comment' )
	rc_timestamp = rc.getAttr( 'timestamp' )

	rc_logtype = rc.getAttr( 'logtype' )
	rc_logaction = rc.getAttr( 'logaction' )

	rc_oldlen = rc.getAttr( 'oldlen' )
	rc_newlen = rc.getAttr( 'newlen' )
	rc_revid = rc.getAttr( 'revid' )
	rc_old_revid = rc.getAttr( 'old_revid' )

	rc_anon = rc.getAttr( 'anon' ) is not None
	rc_bot = rc.getAttr( 'bot' ) is not None
	rc_minor = rc.getAttr( 'minor' ) is not None

	if not rc_oldlen is None: rc_oldlen = int( rc_oldlen )
	if not rc_newlen is None: rc_newlen = int( rc_newlen )
	if not rc_revid is None: rc_revid = int( rc_revid )
	if not rc_old_revid is None: rc_old_revid = int( rc_old_revid )
	if not rc_id is None: rc_id = int( rc_id )

	if rc_comment is None: rc_comment = ''

	target = None
	target_params = {}

	if rc_type == 'log':
	    target = "Special:Log/" + rc_logtype;
	    target_params['page'] = rc_title
	else:
	    target = rc_title;

	    if rc_revid is not None:
		if rc_type != 'new':
		    target_params['diff'] = rc_revid
		    target_params['rcid'] = rc_id

		    if rc_old_revid is not None:
			target_params['oldid'] = rc_old_revid
		else:
		    target_params['oldid'] = rc_revid

	url = self.get_wiki_url( wikiid, target, **target_params );
	if not url: url = ''

	if rc_oldlen is not None and rc_newlen is not None and rc_type != 'log':
		d = ( rc_newlen - rc_oldlen )
		if d >= 0:
			szdiff = '(+%d)' % d
		else: 
			szdiff = '(%d)' % d
	else:
		szdiff = ''

	flag = ''

	if rc_type == 'log':
		targetText = rc_title
		flag = rc_logaction
	else:
		flag = ''
		
		if rc_type == 'new':
		    flag += 'N';

		if rc_minor:
		    flag += 'M';

		if rc_bot:
		    flag += 'B';

		if rc_anon:
		    flag += 'A';

		# TODO: flag as ! for patrolling events, once we can receive them

	if target is None or rc_user is None:
	    return False

	fullString = "[[%s]] %s %s * %s * %s %s" % ( target, flag, url, rc_user, szdiff, rc_comment );

	return fullString;

    def get_wiki_url( self, wikiid, pagename, **params ):
	if type(pagename) == unicode:
	    pagename = pagename.encode('utf-8')

	if len(params) > 0 or pagename is None:
	    u = self.wiki_info.get_wiki_script_url( wikiid )
	    print "script url for %s: %s" % (wikiid, u)
	    if not u: return False

	    if not pagename is None:
		params[ 'title' ] = pagename

	    for k, v in params.items():
		if type(v) == unicode:
		    params[k] = v.encode('utf-8')

	    return u + "?" + urllib.urlencode( params )
	else:
	    u = self.wiki_info.get_wiki_page_url( wikiid )
	    print "page url for %s: %s" % (wikiid, u)
	    if not u: return False

	    return u.replace( '$1', urllib.quote( pagename ) )

    def relay_rc_message( self, rc ):
	w = rc.getAttr('wikiid')
	
	if not w:
	    self.warn( "missing attribute wikiid, can't determin channel. discarding message.")
	    return False

	t = self.get_channel( w )
	
	if not t:
	    self.warn( "no channel found for %s, discarding message " % w )
	    return False

	else:
	    m = self.get_rc_text( rc )

	    if m is None or m == False:
		self.warn( "insufficient information in RC packet (rcid: %s), discarding message" % rc.getAttr('rcid') )
	    else:
		return t.send_message( m, rc )

    def service_loop( self, *connections ):
	socketlist = {}
	for con in connections:
	    socketlist[ con.get_socket() ] = con

	self.online = 1

	while self.online:
	    (in_socks , out_socks, err_socks) = select.select(socketlist.keys(),[],socketlist.keys(),1)

	    for sock in in_socks:
		con = socketlist[ sock ]

		if con:
		    try:
			con.process()
		    except Exception, e:
			error_type, error_value, trbk = sys.exc_info()
			self.warn( "Error while processing! %s" % "  ".join( traceback.format_exception( error_type, error_value, trbk ) ) )
			# TODO: detect when we should kill the loop because a connection failed
		else:
		    raise Exception( "Unknown socket: %s" % repr(sock) )

	    for sock in err_socks:
		    raise Exception( "Error in socket: %s" % repr(sock) )

	self.info("service loop terminated, disconnecting")

	for con in connections:
	    con.close()

	self.info("done.")

################################################################################
class Connection(object):
    def __init__( self, relay ):
	self.relay = relay

    def warn(self, message):
	self.relay.warn( message )

    def info(self, message):
	self.relay.info( message )

    def debug(self, message):
	self.relay.debug( message )

class XmppConnection (Connection):
    def __init__( self, relay, message_encoding = 'utf-8' ):
        super( XmppConnection, self ).__init__( relay )
	self.message_encoding = message_encoding
	self.jid = None

    def process( self ):
	self.jabber.Process(1)

	if not self.jabber.isConnected(): 
	    self.warn("connection lost, reconnecting...")
	    
	    if self.jabber.reconnectAndReauth():
		self.warn("re-connect successful.")
		self.on_connect()

    def close( self ):
	# self.jabber.disconnect() #wha??
	# XXX: leave chat rooms, etc?

	sock = self.get_socket()
	if sock: sock.close()

	self.debug("closed xmpp socket")

    def make_jabber_channel( self, jid ):
	return JabberChannel( self, jid )

    def make_muc_channel( self, room_jid, room_nick = None ):
	if type(room_jid) != object:
	    room_jid = xmpp.protocol.JID( room_jid )

	if not room_nick:
	    room_nick = room_jid.getResource()

	if not room_nick:
	    room_nick = self.jid.getNode()

	return MucChannel( self, room_jid, room_nick )

    def process_message(self, con, message):
        if (message.getError()):
            self.warn("received %s error from <%s>: %s" % (message.getType(), message.getError(), message.getFrom() ))
	elif message.getBody():
	    self.debug("discarding %s message from <%s>: %s" % (message.getType(), message.getFrom(), message.getBody().strip() ))

    def register_handlers(self):
        self.jabber.RegisterHandler( 'message', self.process_message )

    def guess_local_resource(self):
	resource = "%s-%d" % ( socket.gethostname(), os.getpid() ) 
	
	return resource;

    def connect( self, jid, password ):

	if type( jid ) != object:
	    jid = xmpp.protocol.JID( jid )

	if jid.getResource() is None:
	    jid = xmpp.protocol.JID( host= jid.getHost(), node= jid.getNode(), resource = self.guess_local_resource() )

	self.jabber = xmpp.Client(jid.getDomain(),debug=[])
        con= self.jabber.connect()

        if not con:
            self.warn( 'could not connect to %s!' % jid.getDomain() )
            return False

        self.debug( 'connected with %s' % con )

        auth= self.jabber.auth( jid.getNode(), password, resource= jid.getResource() )

        if not auth:
            self.warn( 'could not authenticate as %s!' % jid )
            return False

        self.debug('authenticated using %s as %s' % ( auth, jid ) )

        self.register_handlers()

	self.jid = jid;
        self.info( 'connected as %s' % ( jid ) )

	self.on_connect()

        return con

    def on_connect( self ):
        self.jabber.sendInitPresence(self)
        self.roster = self.jabber.getRoster()

    def get_socket( self ):
	return self.jabber.Connection._sock

class CommandConnection (Connection):
    def __init__( self, relay, socket ):
        super( CommandConnection, self ).__init__( relay )
	self.socket = socket

    def close( self ):
	if self.socket != sys.stdin:
	    self.socket.close()
	    self.debug("closed command socket")
	else:
	    self.debug("not closing stdin")

    def process(self):
	msg = self.socket.readline().strip()

	if (msg.startswith('/')):
	    self.process_command( msg )
	#else:
	#    self.relay.broadcast_message( msg )

    def process_command(self, command):
        self.relay.process_command( command )

    def get_socket( self ):
	return self.socket

class UdpConnection (Connection):
    def __init__( self, relay, buffer_size = 8192 ):
        super( UdpConnection, self ).__init__( relay )
	self.buffer_size = buffer_size
	self.socket = None

    def close( self ):
	self.socket.close()
	self.debug("closed UDP socket")

    def process(self):
	packet = self.socket.recvfrom( self.buffer_size )

	body = packet[0]
	addr = packet[1] #TODO: optionally filter...

	self.debug( "received packet from %s:%s" % addr )
	self.process_rc_packet( body )

    def process_rc_packet(self, data):
	try:
	    dom = xmpp.simplexml.XML2Node( data )
	    self.debug( "parsed rc packet" )

	    if dom.getName() != "rc":
		self.warn( "expected <rc> element, found <%s>; sklipping unknown XML" % dom.getName() )
		return False

	except ExpatError, e:
	    self.warn( "failed to parse RC XML: " + e.args[0] + "; data: " + data[:128].strip() )
	    return False
	
	#TODO: optionally filter...
	self.relay.relay_rc_message( dom )

    def connect( self, port, interface = '0.0.0.0' ):
	self.socket = socket.socket( socket.AF_INET, socket.SOCK_DGRAM )
	self.socket.setsockopt( socket.SOL_SOCKET, socket.SO_REUSEADDR, 1 )
	self.socket.setblocking( 0 )

	self.debug( "binding to UDP %s:%d" % (interface, port) )
	self.socket.bind( (interface, port) )

	if not self.socket.fileno():
	    self.warn( "failed to bind to UDP %s:%d" % (interface, port) )
	    return False

	self.info( "listening to UDP %s:%d" % (interface, port) )
	return True

    def get_socket( self ):
	return self.socket

##################################################################################

class Channel(object):
    def __init__( self, connection ):
	self.connection = connection

    def join(self):
	pass

class JabberChannel (Channel):
    def __init__( self, connection, jid ):
	super( JabberChannel, self ).__init__( connection )

	if type( jid ) != object:
	    jid = xmpp.protocol.JID( jid )

	self.connection = connection
        self.jid = jid
	self.message_type = 'chat'

    def compose_message( self, message, xml = None, mtype = None ):
	if type( message ) == unicode:
	    message = message.encode( self.connection.message_encoding )

	if type( message ) == str:
	    if mtype is None:
		mtype = self.message_type

	    message = xmpp.protocol.Message( self.jid, body= message, typ= mtype )

	    if xml:
		message.addChild( node = xml )
	else:
	    if xml:
		raise Exception("Message already composed, can't attach XML!")

	    if mtype is not None and mtype != message.getType():
		raise Exception("Message already composed with incompatible type! ( %s != %s )" % (mtype, message.getType()) )

	return message

    def send_message( self, message, xml = None, mtype = None ):
	message = self.compose_message( message, mtype = mtype, xml = xml )

        return self.connection.jabber.send( message )

class MucChannel (JabberChannel):
    def __init__( self, connection, room_jid, room_nick ):
	if type( room_jid ) != object:
	    room_jid = xmpp.protocol.JID( room_jid )

	super( MucChannel, self ).__init__( connection, room_jid.getStripped() )

        self.nick = room_nick
	self.message_type = 'groupchat'

    def join(self):
	# use our own desired nickname as resource part of the group's JID
	jid = self.jid.getStripped() + "/" + self.nick; 

	#create presence stanza
	join = xmpp.Presence( to= jid )

	#announce full MUC support
	join.addChild( name = 'x', namespace = 'http://jabber.org/protocol/muc' ) 

	self.connection.jabber.send( join )

	self.connection.info( 'joined room %s' % self.jid.getStripped() )

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

    option_parser.add_option("--wiki-info", dest="wiki_info_file", 
				help="read wiki info from FILE", metavar="FILE")

    option_parser.add_option("--quiet", action="store_const", dest="loglevel", const=LOG_QUIET, default=LOG_VERBOSE, 
				help="suppress informational messages, only print warnings and errors")

    option_parser.add_option("--debug", action="store_const", dest="loglevel", const=LOG_DEBUG, 
				help="print debug messages")

    (options, args) = option_parser.parse_args()

    # find config file........
    if options.config_file:
	cfg = options.config_file #take it from --config
    else:
        cfg = extdir + "/../../udp2xmpp.ini" #installation root

	if not os.path.exists( cfg ):
		cfg = extdir + "/../../phase3/udp2xmpp.ini" #installation root in dev environment

	if not os.path.exists( cfg ):
		cfg = bindir + "/udp2xmpp.ini" #extension dir

    # define config defaults........
    config = ConfigParser.SafeConfigParser()

    config.add_section( 'UDP' )
    config.set( 'UDP', 'buffer-size', '8192' )
    config.set( 'UDP', 'port', '4455' )
    config.set( 'UDP', 'interface', '0.0.0.0' )

    config.add_section( 'XMPP' )
    config.set( 'XMPP', 'message-encoding', 'utf-8' )

    # read config file........
    if not config.read( cfg ):
	sys.stderr.write( "failed to read config from %s\n" % cfg )
	sys.exit(2)


    # find wiki info file........
    wikis = None

    if options.wiki_info_file:
	w = options.wiki_info_file #take it from --wiki-info

    elif config.has_option( 'udp2xmpp', 'wiki-info-section' ):
	# if the config specifies a wiki-info section, there's only one wiki
	# with the wiki id equal to that section name. The wiki's properties
	# are then take from that section in the config file, no extra wiki 
	# info file is needed.

	wikiid = config.get( 'udp2xmpp', 'wiki-info-section' ) 
	info = config.options( wikiid )

	wikis = ConfigParser.SafeConfigParser()
	wikis.add_section( wikiid )

	for k in info:
	    v = config.get( wikiid, k )
	    wikis.set( wikiid, k, v )

    elif config.has_option( 'udp2xmpp', 'wiki-info-file' ):
	w = config.get( 'udp2xmpp', 'wiki-info-file' ) # config file says where to find the wiki info file

    else:
        w = extdir + "/../../udp2xmpp-wikis.ini" #installation root

	if not os.path.exists( cfg ):
		w = extdir + "/../../phase3/udp2xmpp-wikis.ini" #installation root in dev environment

	if not os.path.exists( cfg ):
		w = bindir + "/udp2xmpp-wikis.ini" #extension dir

    # load wiki info file, if no wiki info is yet known
    if not wikis:
	wikis = ConfigParser.SafeConfigParser()
	if not wikis.read( w ):
	    sys.stderr.write( "failed to read wiki info from %s\n" % w )
	    sys.exit(2)

    # create wiki info wrapper and relay instance
    wiki_info = WikiInfo( wikis )
    relay = Relay( wiki_info )

    relay.loglevel = options.loglevel

    # create connections............
    commands_con = CommandConnection( relay, sys.stdin )
    udp_con = UdpConnection( relay, buffer_size = config.getint( 'UDP', 'buffer-size' ) )    
    xmpp_con = XmppConnection( relay, message_encoding = config.get( 'XMPP', 'message-encoding' ) )

    # -- DO STUFF -----------------------------------------------------------------------------------

    # connect................
    if not xmpp_con.connect( jid = config.get( 'XMPP', 'jid' ), password = config.get( 'XMPP', 'password' ) ):
	sys.exit(1)

    if not udp_con.connect( port = config.getint( 'UDP', 'port' ), interface = config.get( 'UDP', 'interface' ) ):
	sys.exit(1)

    # create channels................
    # note: Need to be connected to do this. Some channels need to be joined.
    relay.create_channels( wiki_info.get_wikis(), {
			      'jabber': xmpp_con.make_jabber_channel,
			      'muc': xmpp_con.make_muc_channel,
			  } )
    
    # run relay loop................
    relay.service_loop( commands_con, udp_con, xmpp_con )

    print "done."
