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

class Relay:
    def __init__( self, jabber, message_type = 'message', console_encoding = 'utf-8', message_encoding = 'utf-8', udp_buffsize = 8192 ):
        self.jabber = jabber
	self.message_type = message_type
	self.console_encoding = console_encoding
	self.message_encoding = message_encoding
	self.udp_buffsize = udp_buffsize

	self.targets = {}

	self.udp_socket = None

    def register_handlers(self):
        self.jabber.RegisterHandler( 'message', self.process_message )

    def warn(self, message):
	sys.stderr.write( "WARNING: %s\n" % ( message.encode( self.console_encoding ) ) )

    def info(self, message):
	sys.stderr.write( "INFO: %s\n" % ( message.encode( self.console_encoding ) ) )

    def debug(self, message):
	sys.stderr.write( "DEBUG: %s\n" % ( message.encode( self.console_encoding ) ) )

    def get_all_targets(self):
	return self.targets.keys()

    def get_target( self, stream_name ):
	return self.get( stream_name )

    def add_target( self, stream_name, target_jid ):
	return self.targets[ stream_name ] = target_jid

    def process_message(self, con, message):
        type = message.getType()
        fromjid = message.getFrom().getStripped()

        if (message.getError()):
            self.warn("received %s error from <%s>: %s\n" % (type, message.getError(), message.getFrom() ))
	elif message.getBody():
	    self.debug("discarding %s message from <%s>: %s\n" % (type, message.getFrom(), message.getBody() ))

    def process_rc_packet(self, data):
	dom = simplexml.simplexml( data )
	
	s = self.get_stream_name( dom )
	t = self.get_target( s )
	
	if not t:
	    self.warn( "no target addres known for %s, discarding message " % s )
	else:
	    m = self.get_rc_text( dom )
	    self.send_message_to( m, t, dom )

    def compose_message( self, message, xml = None, mtype = None ):
	if type( message ) == unicode:
	    message = message.encode( self.message_encoding )

	if type( message ) == str:
	    if mtype is None:
		mtype = self.message_type

	    message = xmpp.protocol.Message( to, body= message, type= mtype )

	    if xml:
		message.addChild( node = xml )
	else:
	    if xml:
		raise Exception("Message already composed, can't attach XML!")

	    if mtype is not None and mtype != message.getType():
		raise Exception("Message already composed with incompatible type! ( %s != %s )" % (mtype, message.getType()) )

	return message

    def send_message_to( self, message, to, xml = None, mtype = None ):
	message = self.compose_message( message, mtype = mtype, xml = xml )

        return self.jabber.send( message )

    def broadcast_message( self, message, xml = None, mtype = None ):
	message = self.compose_message( message, mtype = mtype, xml = xml )
        targets = self.get_all_targets()

	for t in targets:
	    self.send_message_to( message, t )

    def process_command(self, command):
        if ( command == 'quit' ):
    	    self.online = False
        elif ( command.startswith( '/' ) ):
    	    self.broadcast_message( command )

    def udp_connect( self, port, host = '0.0.0.0' ):
	self.udp_socket = socket.socket( socket.AF_INET, socket.SOCK_DGRAM )
	self.udp_socket.setsockopt( socket.SOL_SOCKET, socket.SO_REUSEADDR, 1 )
	self.udp_socket.bind( (host, port) )

    def xmpp_connect( self, jid, password ):
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

	self.on_xmpp_connect()

        return con

    def on_xmpp_connect( self ):
        self.jabber.sendInitPresence(self)
        self.roster = self.jabber.getRoster()

    def service_loop( self ):
	udp_socket = self.udp_socket
	xml_socket = self.jabber.Connection._sock
	stdin_socket = sys.stdin

	self.online = 1

	while self.online:
	    (i , o, e) = select.select(socketlist.keys(),[],[],1)
	    for sock in i:
		if sock == xmpp_socket:
		    self.jabber.Process(1)

		    if not cl.isConnected(): 
			self.warn("connection lost, reconnecting...")
			self.jabber.reconnectAndReauth()
			self.warn("re-connect successful.")
			self.on_xmpp_connect()

		elif sock == udp_socket:
		    msg = socket.recvfrom( self.udp_buffsize )

		    bot.process_rc_packet( msg )

		elif sock == stdio_socket:
		    msg = sys.stdin.readline().rstrip('\r\n')

		    if (msg.startswith('/')):
			bot.process_command( msg )
		    else:
			bot.broadcast_message( msg )

		else:
		    raise Exception("Unknown socket: %s" % repr(sock))

	   # FIXME: error recovery (especially when send failed)

	    for sock in e:
		    raise Exception("Error in socket: %s" % repr(sock))

	self.info("service loop terminated, disconnecting")
	self.jabber.disconnect()
	self.udp_socket.close()
	self.info("done.")

class ChatRelay ( Relay ):
    def __init__( self, jabber, args** ):
	super( ChatRelay, self ).__init__( jabber, **args )


class GroupChatRelay (ChatRelay):
    def __init__( self, jabber, group_nick, args** ):
	super( ChatRelay, self ).__init__( jabber, room_jid, **args )

	self.group_nick = group_nick;

    def add_target( self, stream_name, target_jid ):
	super(ChatRelay, self).add_target(stream_name, target_jid)
	self.join_muc( target_jid, self.group_nick )

    def join_muc(self, room, nick):
	# use our own desired nickname as resource part of the group's JID
	jid = room.__str__( wresource= 0 ) + "/" + nick; 

	#create presence stanza
	join = xmpp.Presence( to= jid )

	#announce full MUC support
	join.addChild( name = 'x', namespace = 'http://jabber.org/protocol/muc' ) 

	self.jabber.send( join )

	self.info('joined room %s' % room)

	return True

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

