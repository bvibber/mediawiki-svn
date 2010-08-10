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

import sys, os, time, select, socket
import xmpp # using the xmpppy library <http://xmpppy.sourceforge.net/>, GPL
import simplexml # using simplexml library <http://pypi.python.org/pypi/simplexml/0.6.1>, GPL

RC_EDIT= 0
RC_NEW= 1
RC_MOVE= 2
RC_LOG= 3
RC_MOVE_OVER_REDIRECT= 4

################################################################################
class Relay:
    def __init__( self, console_encoding = 'utf-8' ):
	self.console_encoding = console_encoding
	self.channels = {}

    def warn(self, message):
	sys.stderr.write( "WARNING: %s\n" % ( message.encode( self.console_encoding ) ) )

    def info(self, message):
	sys.stderr.write( "INFO: %s\n" % ( message.encode( self.console_encoding ) ) )

    def debug(self, message):
	sys.stderr.write( "DEBUG: %s\n" % ( message.encode( self.console_encoding ) ) )

    def get_all_channels(self):
	return self.targets.values()

    def get_channel( self, name ):
	return self.channels.get( name )

    def add_channel( self, name, channel ):
	return self.channels[ name ] = channel

    def broadcast_message( self, message, xml = None ):
        targets = self.get_all_channels()

	for t in targets:
	    t.send_message( message, xml = xml )

    def process_command(self, command):
        if ( command == 'quit' ):
    	    self.online = False
        elif ( command.startswith( '/' ) ):
    	    self.broadcast_message( command )

    def get_rc_text( self, rc ):
	for k, v in rc.items():
	    locals[k] = v

	if rc_type == RC_LOG:
	    target = "Special:Log/" + rc_log_type;
	    url = ''
	else:
	    target = title;

	    if rc_type == RC_NEW:
		url = "oldid=" + rc_this_oldid
	    else:
		url = "diff=" + rc_this_oldid + "&oldid=" + rc_last_oldid
		url += "&rcid=" + rc_id

	url = self.get_wiki_url( wikiid, target );

	if locals.get('oldlen') && locals.get('newlen') ) {
		szdiff = newlen - oldlen;
		if szdiff >= 0:
			szdiff = '+' + szdiff
		
		szdiff = '(' + $szdiff + ')' 
	else:
		szdiff = ''

	if rc_type == RC_LOG:
		targetText = title
		flag = logaction
	else:
		flag = ''
		
		if type == 'new':
		    flage += 'N';

		if minor:
		    flage += 'M';

		if bot:
		    flage += 'B';

		if anon:
		    flage += 'A';

	fullString = "%s %s %s * %s * %s %s" % ( title, flag, url, user, szdiff, comment );

	return $fullString;

    def get_wiki_url( self, wikiid, page ):
	raise Exception("oops!") !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

    def relay_rc_message( self, rc ):
	w = rc['wikiid']
	t = self.get_channel( w )
	
	if not t:
	    self.warn( "no channel found for %s, discarding message " % s )
	    return False
	else:
	    m = self.get_rc_text( rc )
	    return t.send_message( m, rc )

    def service_loop( self, connections* ):
	socketlist = {}
	for con in connections:
	    socketlist[ con.get_socket() ] = con

	self.online = 1

	while self.online:
	    (i , o, e) = select.select(socketlist.keys(),[],[],1)

	    for sock in i:
		con = socketlist[ sock ]
		if con:
		    con.process()
		else:
		    raise Exception("Unknown socket: %s" % repr(sock))

	    # FIXME: error recovery (especially when send failed)

	    for sock in e:
		    raise Exception("Error in socket: %s" % repr(sock))

	self.info("service loop terminated, disconnecting")

	for con in connections:
	    con.close()

	self.info("done.")

################################################################################
class Connection:
    def __init__( self, relay ):
	self.relay = relay

    def warn(self, message):
	self.relay.warn( message )

    def info(self, message):
	self.relay.info( message )

    def debug(self, message):
	self.relay.debug( message )

class XmppConnection (Connection):
    def __init__( self, relay, jabber, message_encoding = 'utf-8' ):
        super( XmppConnection, self ).__init__( relay )
        self.jabber = jabber
	self.message_encoding = message_encoding

    def process( self ):
	self.jabber.Process(1)

	if not self.jabber.isConnected(): 
	    self.warn("connection lost, reconnecting...")
	    
	    if self.jabber.reconnectAndReauth():
		self.warn("re-connect successful.")
		self.on_connect()

    def close( self ):
	self.jabber.disconnect()

    def make_jabber_channel( self, jid ):
	return JabberChannel( self, jid )

    def make_muc_channel( self, jid, nick ):
	return MucChannel( self, jid, nick )

    def process_message(self, con, message):
        if (message.getError()):
            self.warn("received %s error from <%s>: %s\n" % (message.getType(), message.getError(), message.getFrom() ))
	elif message.getBody():
	    self.debug("discarding %s message from <%s>: %s\n" % (message.getType(), message.getFrom(), message.getBody() ))

    def register_handlers(self):
        self.jabber.RegisterHandler( 'message', self.process_message )

    def connect( self, jid, password ):
        con= self.jabber.connect()

        if not con:
            self.warn( 'could not connect!' )
            return False

        self.debug( 'connected with %s' % con )

        auth= self.jabber.auth( jid.getNode(), password, resource= jid.getResource() )

        if not auth:
            self.warn( 'could not authenticate as %s!' %s jid )
            return False

        self.debug('authenticated using %s as %s' % ( auth, jid ) )

        self.register_handlers()

        self.info( 'connected %s' % ( jid ) )

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

    def process(self):
	msg = self.socket.readline().sub('^\\s+|\\s+$', '')

	if (msg.startswith('/')):
	    self.process_command( msg )
	else:
	    self.relay.broadcast_message( msg )

    def process_command(self, command):
        self.relay.process_command( command )

    def get_socket( self ):
	return self.socket

class UdpConnection (Connection):
    def __init__( self, relay, buffsize = 8192 ):
        super( UdpConnection, self ).__init__( relay )
	self.buffsize = buffsize
	self.socket = None

    def close( self ):
	self.socket.close()

    def process(self):
	msg = socket.recvfrom( self.buffsize )

	self.process_rc_packet( msg )

    def process_rc_packet(self, data):
	dom = simplexml.simplexml( data )
	self.relay.relay_rc_message( dom.item[0] )

	#FIXME: error recovery (xml parser error, etc)

    def connect( self, port, host = '0.0.0.0' ):
	self.socket = socket.socket( socket.AF_INET, socket.SOCK_DGRAM )
	self.socket.setsockopt( socket.SOL_SOCKET, socket.SO_REUSEADDR, 1 )
	self.socket.bind( (host, port) )

    def get_socket( self ):
	return self.socket

##################################################################################

class Channel:
    def __init__( self, connection ):
	self.connection = connection

class JabberChannel (Channel):
    def __init__( self, connection, jid ):
	super( JabberChannel, self ).__init__( connection )
	self.connection = connection
        self.jid = jid
	self.message_type = 'chat'

    def compose_message( self, message, xml = None, mtype = None ):
	if type( message ) == unicode:
	    message = message.encode( self.message_encoding )

	if type( message ) == str:
	    if mtype is None:
		mtype = self.message_type

	    message = xmpp.protocol.Message( jid, body= message, type= mtype )

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
	super( MucChannel, self ).__init__( connection, jid )
        self.nick = room_nick
	self.message_type = 'groupchat'

    def join_muc(self):
	# use our own desired nickname as resource part of the group's JID
	jid = self.jid.__str__( wresource= 0 ) + "/" + self.nick; 

	#create presence stanza
	join = xmpp.Presence( to= jid )

	#announce full MUC support
	join.addChild( name = 'x', namespace = 'http://jabber.org/protocol/muc' ) 

	self.connection.jabber.send( join )

	self.info('joined room %s' % room)

	return True

##################################################################################

if __name__ == '__main__':

    if len(sys.argv) < 4:
        print "Syntax: ytalk tojis myjid mypass"
        sys.exit(0)
    
    tojid = sys.argv[1]
    myjid = sys.argv[2]
    mypass = sys.argv[3]
    group = None
    type = 'chat'

    jid=xmpp.protocol.JID(myjid)

    if tojid.startswith('#'):
	tojid = tojid[1:]
	group = tojid  + "/" + jid.getNode()
	type = 'groupchat'
    
    cl=xmpp.Client(jid.getDomain(),debug=[])
    
    bot=Bot(cl,tojid,type)

    if not bot.xmpp_connect():
        sys.stderr.write("Could not connect to server, or password mismatch!\n")
        sys.exit(1)

    if group and not bot.xmpp_join(group):
        sys.stderr.write("Could not join group "+group+"!\n")
        sys.exit(1)

