# bgp.py
# Copyright (c) 2007 by Mark Bergsma <mark@nedworks.org>

"""
A (partial) implementation of the BGP 4 protocol (RFC4271).
"""

# System imports
import struct

# Zope imports
from zope.interface import implements, Interface

# Twisted imports
from twisted import copyright
from twisted.internet import reactor, protocol, base, interfaces, error, defer

# Constants
VERSION = 4
PORT = 179

HDR_LEN = 19
MAX_LEN = 4096

# BGP messages
MSG_OPEN = 1
MSG_UPDATE = 2
MSG_NOTIFICATION = 3
MSG_KEEPALIVE = 4

# BGP FSM states
ST_IDLE, ST_CONNECT, ST_ACTIVE, ST_OPENSENT, ST_OPENCONFIRM, ST_ESTABLISHED = range(6)

stateDescr = {
    ST_IDLE:        "IDLE",
    ST_CONNECT:     "CONNECT",
    ST_ACTIVE:      "ACTIVE",
    ST_OPENSENT:    "OPENSENT",
    ST_OPENCONFIRM: "OPENCONFIRM",
    ST_ESTABLISHED: "ESTABLISHED"
}

# Notification error codes
ERR_MSG_HDR = 1
ERR_MSG_OPEN = 2
ERR_MSG_UPDATE = 3
ERR_HOLD_TIMER_EXPIRED = 4
ERR_FSM = 5
ERR_CEASE = 6

# Notification suberror codes
ERR_MSG_HDR_CONN_NOT_SYNC = 1
ERR_MSG_HDR_BAD_MSG_LEN = 2
ERR_MSG_HDR_BAD_MSG_TYPE = 3

ERR_MSG_OPEN_UNSUP_VERSION = 1
ERR_MSG_OPEN_BAD_PEER_AS = 2
ERR_MSG_OPEN_BAD_BGP_ID = 3
ERR_MSG_OPEN_UNSUP_OPT_PARAM = 4
ERR_MSG_OPEN_UNACCPT_HOLD_TIME = 6

ERR_MSG_UPDATE_MALFORMED_ATTR_LIST = 1
ERR_MSG_UPDATE_UNRECOGNIZED_WELLKNOWN_ATTR = 2
ERR_MSG_UPDATE_MISSING_WELLKNOWN_ATTR = 3
ERR_MSG_UPDATE_ATTR_FLAGS = 4
ERR_MSG_UPDATE_ATTR_LEN = 5
ERR_MSG_UPDATE_INVALID_ORIGIN = 6
ERR_MSG_UPDATE_INVALID_NEXTHOP = 8
ERR_MSG_UPDATE_OPTIONAL_ATTR = 9
ERR_MSG_UPDATE_INVALID_NETWORK_FIELD = 10
ERR_MSG_UPDATE_MALFORMED_ASPATH = 11

# BGP attribute flags
ATTR_OPTIONAL = 1 << 7
ATTR_TRANSITIVE = 1 << 6
ATTR_PARTIAL = 1 << 5
ATTR_EXTENDED_LEN = 1 << 4

# BGP attribute types
ATTR_TYPE_ORIGIN = 1
ATTR_TYPE_AS_PATH = 2
ATTR_TYPE_NEXT_HOP = 3
ATTR_TYPE_MULTI_EXIT_DISC = 4
ATTR_TYPE_LOCAL_PREF = 5
ATTR_TYPE_ATOMIC_AGGREGATE = 6
ATTR_TYPE_AGGREGATOR = 7
ATTR_TYPE_COMMUNITY = 8

ATTR_TYPE_INT_LAST_UPDATE = 256 + 1

# Exception classes

class BGPException(Exception):
    def __init__(self, protocol=None):
        self.protocol = protocol

class NotificationSent(BGPException):
    def __init__(self, protocol, error, suberror, data):
        BGPException.__init__(self, protocol)
        
        self.error = error
        self.suberror = suberror
        self.data = data

class BadMessageLength(BGPException):
    pass

class AttributeException(BGPException):
    def __init__(self, suberror, data=''):
        BGPException.__init__(self)
        
        self.error = ERR_MSG_UPDATE
        self.suberror = suberror
        self.data = data

# Interfaces

class IBGPPeering(Interface):
    """
    Interface for notifications from the BGP protocol / FSM
    """
    
    def notificationSent(self, protocol, error, suberror, data):
        """
        Called when a BGP Notification message was sent.
        """
    
    def connectionClosed(self, protocol):
        """
        Called when the BGP connection has been closed (in error or not).
        """
    
    def completeInit(self, protocol):
        """
        Called when BGP resources should be initialized.
        """
        
    def sessionEstablished(self, protocol):
        """
        Called when the BGP session has reached the Established state
        """
    
    def connectRetryEvent(self, protocol):
        """
        Called when the connect-retry timer expires. A new connection should
        be initiated.
        """

class IPPrefix(object):
    """Class that represents an IP prefix"""
    
    def __init__(self, ipprefix):
        self.prefix = None # packed ip string
        
        if type(ipprefix) is tuple:
            prefix, self.prefixlen = ipprefix
            if type(prefix) is str:
                # tuple (ipstr, prefixlen)
                self.prefix = prefix
            elif type(prefix) is int:
                # tuple (ipint, prefixlen)
                self.prefix = struct.pack('!I', prefix)
            else:
                # Assume prefix is a sequence of octets
                self.prefix = "".join(map(chr, prefix))
        elif type(ipprefix) is str:
            # textual form
            prefix, prefixlen = ipprefix.split('/')
            self.prefix = "".join([chr(int(o)) for o in prefix.split('.')])
            self.prefixlen = int(prefixlen)
            # TODO: IPv6
        else:
            raise ValueError
    
    def __repr__(self):
        return repr(str(self))
        # TODO: IPv6
    
    def __str__(self):
        prefix = self.prefix +  ('\0\0\0\0'[:4-len(self.prefix)])
        return ".".join([str(ord(o)) for o in prefix]) + '/%d' % self.prefixlen
    
    def __eq__(self, other):
        # FIXME: masked ips
        return self.prefixlen == other.prefixlen and self.prefix == other.prefix
    
    def __ne__(self, other):
        return not self.__eq__(other)
    
    def __lt__(self, other):
        return self.prefix < other.prefix or \
            (self.prefix == other.prefix and self.prefixlen < other.prefixlen)
    
    def __le__(self, other):
        return self.__lt__(other) or self.__eq__(other)
    
    def __gt__(self, other):
        return self.prefix > other.prefix or \
            (self.prefix == other.prefix and self.prefixlen > other.prefixlen)
    
    def __ge__(self, other):
        return self.__gt__(other) or self.__eq__(other)
    
    def __hash__(self):
        return hash(self.prefix) ^ hash(self.prefixlen)
    
    def __len__(self):
        return self.prefixlen

    def ipToInt(self):
        return reduce(lambda x, y: x * 256 + y, map(ord, self.prefix))

    def netmask(self):
        return ~( (1 << (len(self.prefix)*8 - self.prefixlen)) - 1)

    def mask(self, prefixlen, shorten=False):
        # DEBUG
        assert len(self.prefix) == 4
        
        masklen = len(self.prefix) * 8 - prefixlen
        self.prefix = struct.pack('!I', self.ipToInt() >> masklen << masklen)
        if shorten: self.prefixlen = prefixlen
        return self

class IPv4IP(IPPrefix):
    """Class that represents a single non-prefix IPv4 IP."""
    
    def __init__(self, ip):
        if type(ip) is str and len(ip) > 4:
            super(IPv4IP, self).__init__(ip + '/32')
        else:
            super(IPv4IP, self).__init__((ip, 32))

    def __str__(self):
        return ".".join([str(ord(o)) for o in self.prefix])

class Attribute(object):
    """Base class for all BGP attribute classes"""
    
    typeToClass = {}
    name = 'Attribute'
    
    def __init__(self, attrTuple=None):
        super(Attribute, self).__init__()
        
        self.tuple = attrTuple
        
        if attrTuple is None:            
            self.optional = 0
            self.transitive = 0
            self.partial = 0
            self.extendedLength = 0
            
            self.value = None
            self.typeCode = 0
        else:
            flags, typeCode, value = attrTuple
            self.optional = (flags & ATTR_OPTIONAL != 0)
            self.transitive = (flags & ATTR_TRANSITIVE != 0)
            self.partial = (flags & ATTR_PARTIAL != 0)
            self.extendedLength = (flags & ATTR_EXTENDED_LEN != 0)
            
            self.value = value
            self.typeCode = typeCode
            
            if typeCode not in self.typeToClass:
                if self.optional and self.transitive:
                    # Unrecognized optional, transitive attribute, set partial bit
                    self.partial = 1
                elif not self.optional:
                    raise AttributeException(ERR_MSG_UPDATE_UNRECOGNIZED_WELLKNOWN_ATTR, attrTuple)
            
        self.type = self.__class__
    
    def __eq__(self, other):
        return self is other or \
            (type(self) is type(other) and self.flags == other.flags and self.value == other.value)
    
    def __ne__(self, other):
        return not self.__eq__(other)
    
    def __repr__(self):
        return repr(self.tuple)
    
    def __str__(self):
        return str(self.value)
    
    def flagsStr(self):
        """Returns a string with characters symbolizing the flags
        set to True"""
        
        s = ''
        for c, f in [('O', self.optional), ('T', self.transitive),
                     ('P', self.partial), ('E', self.extendedLength)]:
            if f: s += c
        return s            
    
    @classmethod
    def fromTuple(cls, attrTuple):
        """Instantiates an Attribute inheritant of the right type for a 
        given attribute tuple.
        """

        return cls.typeToClass.get(attrTuple[1], cls)(attrTuple)

class OriginAttribute(Attribute):
    name = 'Origin'
    
    ORIGIN_IGP = 0
    ORIGIN_EGP = 1
    ORIGIN_INCOMPLETE = 2

    def __init__(self, attrTuple):
        super(OriginAttribute, self).__init__(attrTuple)
        
        value = attrTuple[2]
        
        if self.optional or not self.transitive:
            raise AttributeException(ERR_MSG_UPDATE_ATTR_FLAGS, attrTuple)
        if len(value) != 1:
            raise AttributeException(ERR_MSG_UPDATE_ATTR_LEN, attrTuple)
        if ord(value) not in (self.ORIGIN_IGP, self.ORIGIN_EGP, self.ORIGIN_INCOMPLETE):
            raise AttributeException(ERR_MSG_UPDATE_INVALID_ORIGIN, attrTuple)
        
        self.value = ord(value)
            
class ASPathAttribute(Attribute):
    name = 'AS Path'
    
    def __init__(self, attrTuple):
        super(ASPathAttribute, self).__init__(attrTuple)

        value = attrTuple[2]

        if self.optional or not self.transitive:
            raise AttributeException(ERR_MSG_UPDATE_ATTR_FLAGS, attrTuple)
        if len(value) == 0:
            raise AttributeException(ERR_MSG_UPDATE_ATTR_LEN, attrTuple)

        self.value = []
        postfix = value        
        try:
            # Loop over all path segments
            while len(postfix) > 0:
                type, length = struct.unpack('!BB', postfix[:2])
                asPath = list(struct.unpack('!%dH' % length, postfix[2:2+length*2]))
                
                postfix = postfix[2+length*2:]
                self.value.append( (type, asPath) )
        except:
            raise AttributeException(ERR_MSG_UPDATE_MALFORMED_ASPATH)  
        
class NextHopAttribute(Attribute):
    name = 'Next Hop'

    def __init__(self, attrTuple):
        super(NextHopAttribute, self).__init__(attrTuple)
        
        value = attrTuple[2]
        
        if self.optional or not self.transitive:
            raise AttributeException(ERR_MSG_UPDATE_ATTR_FLAGS, attrTuple)
        if len(value) != 4:
            raise AttributeException(ERR_MSG_UPDATE_ATTR_LEN, attrTuple)
        if value in (0, 2**32-1):
            raise AttributeException(ERR_MSG_UPDATE_INVALID_NEXTHOP, attrTuple)
        
        self.value = IPv4IP(value)
    
class MEDAttribute(Attribute):
    name = 'MED'

    def __init__(self, attrTuple):
        super(MEDAttribute, self).__init__(attrTuple)
        
        value = attrTuple[2]
        
        if not self.optional or self.transitive:
            raise AttributeException(ERR_MSG_UPDATE_ATTR_FLAGS, attrTuple)
        if len(value) != 4:
            raise AttributeException(ERR_MSG_UPDATE_ATTR_LEN, attrTuple)
        
        self.value = struct.unpack('!I', value)[0]
        
class LocalPrefAttribute(Attribute):
    name = 'Local Pref'
    
    def __init__(self, attrTuple):
        super(LocalPrefAttribute, self).__init__(attrTuple)
        
        value = attrTuple[2]
        
        if not self.optional or self.transitive:
            raise AttributeException(ERR_MSG_UPDATE_ATTR_FLAGS, attrTuple)
        if len(value) != 4:
            raise AttributeException(ERR_MSG_UPDATE_ATTR_LEN, attrTuple)
        
        self.value = struct.unpack('!I', value)[0]
    
class AtomicAggregateAttribute(Attribute):
    name = 'Atomic Aggregate'
    
    def __init__(self, attrTuple):
        super(AtomicAggregateAttribute, self).__init__(attrTuple)
        
        if self.optional:
            raise AttributeException(ERR_MSG_UPDATE_ATTR_FLAGS, attrTuple)
        if len(attrTuple[2]) != 0:
            raise AttributeException(ERR_MSG_UPDATE_ATTR_LEN, attrTuple)

class AggregatorAttribute(Attribute):
    name = 'Aggregator'

    def __init__(self, attrTuple):
        super(AggregatorAttribute, self).__init__(attrTuple)
        
        value = attrTuple[2]

        if not self.optional or not self.transitive:
            raise AttributeException(ERR_MSG_UPDATE_ATTR_FLAGS, attrTuple)
        if len(value) != 6:
            raise AttributeException(ERR_MSG_UPDATE_ATTR_LEN, attrTuple)
        
        asn = struct.unpack('!H', value[:2])[0]
        aggregator = IPv4IP(value[2:]) # TODO: IPv6
        self.value = (asn, aggregator)

class CommunityAttribute(Attribute):
    name = 'Community'
    
    def __init__(self, attrTuple):
        super(CommunityAttribute, self).__init__(attrTuple)
        
        value = attrTuple[2]
        
        if not self.optional or not self.transitive:
            raise AttributeException(ERR_MSG_UPDATE_ATTR_FLAGS, attrTuple)
        if len(value) % 4 != 0:
            raise AttributeException(ERR_MSG_UPDATE_ATTR_LEN, attrTuple)
        
        length = len(value) / 4
        self.value = list(struct.unpack('!%dI' % length, value))
    
    def __str__(self):
        return str(["%d:%d" % (c / 2**16, c % 2**16) for c in self.value])

class LastUpdateIntAttribute(Attribute):
    name = 'Last Update'
    
    def __init__(self, attrTuple):
        super(LastUpdateIntAttribute, self).__init__(attrTuple)
        
        self.value = attrTuple[2]
    
Attribute.typeToClass = {
    ATTR_TYPE_ORIGIN:            OriginAttribute,
    ATTR_TYPE_AS_PATH:           ASPathAttribute,
    ATTR_TYPE_NEXT_HOP:          NextHopAttribute,
    ATTR_TYPE_MULTI_EXIT_DISC:   MEDAttribute,
    ATTR_TYPE_LOCAL_PREF:        LocalPrefAttribute,
    ATTR_TYPE_ATOMIC_AGGREGATE:  AtomicAggregateAttribute,
    ATTR_TYPE_AGGREGATOR:        AggregatorAttribute,
    ATTR_TYPE_COMMUNITY:         CommunityAttribute,
    
    ATTR_TYPE_INT_LAST_UPDATE:   LastUpdateIntAttribute
}

class AttributeSet(set):
    """Class that contains a single set of attributes attached to a list of NLRIs"""
    
    def __init__(self, attributes):
        """Expects a sequence of either unparsed attribute tuples, or parsed
        Attribute inheritants.
        """

        self.origin, self.asPath, self.nextHop = None, None, None

        for a in attributes:
            if type(a) is tuple:
                attr = Attribute.fromTuple(a)
            elif isinstance(a, Attribute):
                attr = a
    
            self.add(attr)
        
        # Check whether all mandatory wellknown attributes are present
        for attr, typeCode in [(self.origin, ATTR_TYPE_ORIGIN),
                               (self.asPath, ATTR_TYPE_AS_PATH),
                               (self.nextHop, ATTR_TYPE_NEXT_HOP)]:
            if attr is None:
                raise AttributeError(ERR_MSG_UPDATE_MISSING_WELLKNOWN_ATTR, (0, typeCode, None))
    
    def add(self, attr):
        """Adds attribute attr to the set, raises KeyError if already present"""
        
        try:
            super(AttributeSet, self).add(attr)
            
            # Add direct references for the mandatory wellknown attributes
            if type(attr) is OriginAttribute:
                self.origin = attr
            elif type(attr) is ASPathAttribute:
                self.asPath = attr
            elif type(attr) is NextHopAttribute:
                self.nextHop = attr
        except KeyError:
            # Attribute was already present
            raise AttributeError(ERR_MSG_UPDATE_MALFORMED_ATTR_LIST)

    # FIXME: check/implement other set methods

class FSM(object):
    class BGPTimer(object):
        """
        Timer class with a slightly different Timer interface than the
        Twisted DelayedCall interface
        """
        
        def __init__(self, callable):
            self.delayedCall = None
            self.callable = callable
        
        def cancel(self):
            """Cancels the timer if it was running, does nothing otherwise"""
            
            try:
                self.delayedCall.cancel()
            except (AttributeError, error.AlreadyCancelled):
                pass
                
        def reset(self, secondsFromNow):
            """Resets an already running timer, or starts it if it wasn't running."""
            
            try:
                self.delayedCall.reset(secondsFromNow)
            except (AttributeError, error.AlreadyCalled, error.AlreadyCancelled):
                self.delayedCall = reactor.callLater(secondsFromNow, self.callable)
        
        def active(self):
            """Returns True if the timer was running, False otherwise."""
            
            try:
                return self.delayedCall.active()
            except AttributeError:
                return False
    
    protocol = None
    
    state = ST_IDLE
    
    largeHoldTime = 4*60
    sendNotificationWithoutOpen = True    # No bullshit
    
    def __init__(self, bgpPeering=None, protocol=None):
        self.bgpPeering = bgpPeering
        self.protocol = protocol

        self.connectRetryCounter = 0
        self.connectRetryTime = 30
        self.connectRetryTimer = FSM.BGPTimer(self.connectRetryTimeEvent)
        self.holdTime = 3 * 60
        self.holdTimer = FSM.BGPTimer(self.holdTimeEvent)
        self.keepAliveTime = self.holdTime / 3
        self.keepAliveTimer = FSM.BGPTimer(self.keepAliveEvent)
    
        self.allowAutomaticStart = False
        self.allowAutomaticStop = False
        self.delayOpen = False
        self.delayOpenTime = 30
        self.delayOpenTimer = FSM.BGPTimer(self.delayOpenEvent)

    def manualStart(self):
        """
        Should be called when a BGP ManualStart event (event 1) is requested.
        Note that a protocol instance does not yet exist at this point,
        so this method requires some support from BGPPeering.manualStart().
        """
        
        if self.state == ST_IDLE:
            self.connectRetryCounter = 0
            self.connectRetryTimer.reset(self.connectRetryTime)

    def manualStop(self):
        """Should be called when a BGP ManualStop event (event 2) is requested."""
        
        if self.state != ST_IDLE:
            self.protocol.sendNotification(ERR_CEASE, 0)
            # Stop all timers
            for timer in (self.connectRetryTimer, self.holdTimer, self.keepAliveTimer,
                          self.delayOpenTimer):
                timer.cancel()
            if self.bgpPeering is not None: self.bgpPeering.releaseResources()
            self._errorClose()
            self.connectRetryCounter = 0
            raise NotificationSent(self.protocol, ERR_CEASE, 0)
            
            self.state = ST_IDLE

    def connectionMade(self):
        """Should be called when a TCP connection has successfully been
        established with the peer. (events 16, 17)
        """
        
        if self.state in (ST_CONNECT, ST_ACTIVE):
            # State Connect, Event 16 or 17
            if self.delayOpen:
                self.connectRetryTimer.cancel()
                self.delayOpenTimer.reset(self.delayOpenTime)
            else:
                self.connectRetryTimer.cancel()
                if self.bgpPeering: self.bgpPeering.completeInit(self.protocol)
                self.protocol.sendOpen()
                self.holdTimer.reset(self.largeHoldTime)
                self.state = ST_OPENSENT
    
    def connectionFailed(self):
        """Should be called when the associated TCP connection failed, or
        was lost. (event 18)"""
               
        if self.state == ST_CONNECT:
            # State Connect, event 18
            if self.delayOpenTimer.active():
                 self.connectRetryTimer.reset(self.connectRetryTime)
                 self.delayOpenTimer.cancel()
                 self.state = ST_ACTIVE
            else:
                self.connectRetryTimer.cancel()
                self._closeConnection()
                if self.bgpPeering: self.bgpPeering.releaseResources(self.protocol)
                self.state = ST_IDLE
                if self.bgpPeering: self.bgpPeering.connectionClosed(self.protocol)
        elif self.state == ST_ACTIVE:
            # State Active, event 18
            self.connectRetryTimer.reset(self.connectRetryTime)
            self.delayOpenTimer.cancel()
            if self.bgpPeering: self.bgpPeering.releaseResources(self.protocol)
            self.connectRetryCounter += 1
            # TODO: osc damping
            self.state = ST_IDLE
        elif self.state == ST_OPENSENT:
            # State OpenSent, event 18
            if self.bgpPeering: self.bgpPeering.releaseResources(self.protocol)
            self._closeConnection()
            self.connectRetryTimer.reset(self.connectRetryTime)
            self.state = ST_ACTIVE
            if self.bgpPeering: self.bgpPeering.connectionClosed(self.protocol)
        elif self.state in (ST_OPENCONFIRM, ST_ESTABLISHED):
            self._errorClose()


    def openReceived(self):
        """Should be called when a BGP Open message was received from
        the peer. (events 19, 20)
        """
        
        if self.state in (ST_CONNECT, ST_ACTIVE):
            if self.delayOpenTimer.active():    
                # State Connect, event 20
                self.connectRetryTimer.cancel()
                if self.bgpPeering: self.bgpPeering.completeInit(self.protocol)
                self.delayOpenTimer.cancel()
                self.protocol.sendOpen()
                self.protocol.sendKeepAlive()
                if self.holdTime != 0:
                    self.KeepAliveTimer.reset(self.keepAliveTime)
                    self.holdTimer.reset(self.holdTimer)
                else:    # holdTime == 0
                    self.keepAliveTimer.cancel()
                    self.holdTimer.cancel()
                    
                self.state = ST_OPENCONFIRM
            else:
                # State Connect, event 19
                self._errorClose()
                
        elif self.state == ST_OPENSENT:
            # State OpenSent, events 19, 20
            self.delayOpenTimer.cancel()
            self.connectRetryTimer.cancel()
            self.protocol.sendKeepAlive()
            if self.holdTime > 0:
                self.keepAliveTimer.reset(self.keepAliveTime)
                self.holdTimer.reset(self.holdTime)
            self.state = ST_OPENCONFIRM
        
        elif self.state == ST_OPENCONFIRM:
            # State OpenConfirm, events 19, 20
            # DEBUG
            print "Running collision detection"
            
            # Perform collision detection
            self.protocol.collisionDetect()
        
        elif self.state == ST_ESTABLISHED:
            # State Established, event 19 or 20
            self.protocol.sendNotification(ERR_FSM, 0)
            self._errorClose()
            raise NotificationSent(self.protocol, ERR_FSM, 0)

    def headerError(self, suberror, data=''):
        """
        Should be called when an invalid BGP message header was received.
        (event 21)
        """
        
        self.protocol.sendNotification(ERR_MSG_HDR, suberror, data)
        # Note: RFC4271 states that we should send ERR_FSM in the
        # Established state, which contradicts earlier statements.
        self._errorClose()
        raise NotificationSent(self.protocol, ERR_MSG_HDR, suberror, data)
    
    def openMessageError(self, suberror, data=''):
        """
        Should be called when an invalid BGP Open message was received.
        (event 22)
        """

        self.protocol.sendNotification(ERR_MSG_OPEN, suberror, data)
        # Note: RFC4271 states that we should send ERR_FSM in the
        # Established state, which contradicts earlier statements.
        self._errorClose()
        raise NotificationSent(self.protocol, ERR_MSG_OPEN, suberror, data)
    
    def keepAliveReceived(self):
        """Should be called when a BGP KeepAlive packet was received
        from the peer. (event 26)
        """
        
        if self.state == ST_OPENCONFIRM:
            # State OpenSent, event 26
            self.holdTimer.reset(self.holdTime)
            self.state = ST_ESTABLISHED
            self.protocol.deferred.callback(self.protocol)
        elif self.state == ST_ESTABLISHED:
            # State Established, event 26
            self.holdTimer.reset(self.holdTime)
        elif self.state in (ST_CONNECT, ST_ACTIVE):
            # States Connect, Active, event 26
            self._errorClose()

    def versionError(self):
        """Should be called when a BGP Notification Open Version Error
        message was received from the peer. (event 24)
        """
        
        if self.state in (ST_OPENSENT, ST_OPENCONFIRM):
            # State OpenSent, event 24
            self.connectRetryTimer.cancel()
            if self.bgpPeering: self.bgpPeering.releaseResources(self.protocol)
            self._closeConnection()
            self.state = ST_IDLE          
        elif self.state in (ST_CONNECT, ST_ACTIVE):
            # State Connect, event 24
            self._errorClose()

    def notificationReceived(self, error, suberror):
        """Should be called when a BGP Notification message was
        received from the peer. (events 24, 25)
        """
        
        if error == ERR_MSG_OPEN and suberror == 1:
            # Event 24
            self.versionError()
        else:
            if self.state != ST_IDLE:
                # State != Idle, events 24, 25
                self._errorClose()          
    
    def updateReceived(self, update):
        """Called when a valid BGP Update message was received. (event 27)"""
        
        if self.state == ST_ESTABLISHED:
            # State Established, event 27
            if self.holdTime != 0:
                self.holdTimer.reset(self.holdTime)
            
            self.bgpPeering.update(update)
        elif self.state in (ST_ACTIVE, ST_CONNECT):
            # States Active, Connect, event 27
            self._errorClose()
        elif self.state in (ST_OPENSENT, ST_OPENCONFIRM):
            # States OpenSent, OpenConfirm, event 27
            self.protocol.sendNotification(ERR_FSM, 0)
            self._errorClose()
            raise NotificationSent(self.protocol, ERR_FSM, 0)        
        
    def updateError(self, suberror, data=''):
        """Called when an invalid BGP Update message was received. (event 28)"""

        if self.state == ST_ESTABLISHED:
            # State Established, event 28
            self.protocol.sendNotification(ERR_MSG_UPDATE, suberror, data)
            self._errorClose()
            raise NotificationSent(self.protocol, ERR_MSG_UPDATE, suberror, data)
        elif self.state in (ST_ACTIVE, ST_CONNECT):
            # States Active, Connect, event 28
            self._errorClose()
        elif self.state in (ST_OPENSENT, ST_OPENCONFIRM):
            # States OpenSent, OpenConfirm, event 28
            self.protocol.sendNotification(self.protocol, ERR_FSM, 0)
            self._errorClose()
            raise NotificationSent(self.protocol, ERR_FSM, 0)   

    def openCollisionDump(self):
        """Called when the collision detection algorithm determined
        that the associated connection should be dumped.
        (event 23)
        """
        
        # DEBUG
        print "Collided, closing."

        if self.state == ST_IDLE:
            return
        elif self.state in (ST_OPENSENT, ST_OPENCONFIRM, ST_ESTABLISHED):
            self.protocol.sendNotification(ERR_CEASE, 0)
            
        self._errorClose()
        raise NotificationSent(self.protocol, ERR_CEASE, 0)

    def delayOpenEvent(self):
        """Called when the DelayOpenTimer expires. (event 12)"""
        
        assert(self.delayOpen)
        
        # DEBUG
        print "Delay Open event"
        
        if self.state == ST_CONNECT:
            # State Connect, event 12
            self.protocol.sendOpen()
            self.holdTimer.reset(self.largeHoldTime)
            self.state = ST_OPENSENT
        elif self.state == ST_ACTIVE:
            # State Active, event 12
            self.connectRetryTimer.cancel()
            self.delayOpenTimer.cancel()
            if self.bgpPeering: self.bgpPeering.completeInit(self.protocol)
            self.sendOpen()
            self.holdTimer.reset(self.largeHoldTime)
            self.state = ST_OPENSENT
        elif self.state != ST_IDLE:
            # State OpenSent, OpenConfirm, Established, event 12
            self.protocol.sendNotification(ERR_FSM, 0)
            self._errorClose()
            raise NotificationSent(self.protocol, ERR_FSM, 0)
    
    def keepAliveEvent(self):
        """Called when the KeepAliveTimer expires. (event 11)"""
        
        # DEBUG
        print "KeepAlive event"
        
        if self.state in (ST_OPENCONFIRM, ST_ESTABLISHED):
            # State OpenConfirm, Established, event 11
            self.protocol.sendKeepAlive()
            if self.holdTime > 0:
                self.keepAliveTimer.reset(self.keepAliveTime)
        elif self.state in (ST_CONNECT, ST_ACTIVE):
            self._errorClose()
                
    def holdTimeEvent(self):
        """Called when the HoldTimer expires. (event 10)"""
    
        if self.state in (ST_OPENSENT, ST_OPENCONFIRM, ST_ESTABLISHED):
            # States OpenSent, OpenConfirm, Established, event 10
            self.protocol.sendNotification(ERR_HOLD_TIMER_EXPIRED, 0)
            self.connectRetryTimer.cancel()
            self._errorClose()
            self.connectRetryCounter += 1
            # TODO: peer osc damping
            self.state = ST_IDLE
            
            #self.protocol.deferred.errback(HoldTimerExpired(self.protocol))
        elif self.state in (ST_CONNECT, ST_ACTIVE):
            self._errorClose()
    
    def connectRetryTimeEvent(self):
        """Called when the ConnectRetryTimer expires. (event 9)"""
        
        if self.state in (ST_CONNECT, ST_ACTIVE):
            # State Connect, event 9
            self._closeConnection()
            self.connectRetryTimer.reset(self.connectRetryTime)
            self.delayOpenTimer.cancel()
            # Initiate TCP connection
            if self.bgpPeering: self.bgpPeering.connectRetryEvent(self.protocol)
        elif self.state != ST_IDLE:
            # State OpenSent, OpenConfirm, Established, event 12
            self.protocol.sendNotification(ERR_FSM, 0)
            self._errorClose()
            raise NotificationSent(self.protocol, ERR_FSM, 0)
    
    def _errorClose(self):
        """Internal method that closes a connection and returns the state
        to IDLE.
        """

        # Stop the timers
        for timer in (self.connectRetryTimer, self.delayOpenTimer, self.holdTimer,
            self.keepAliveTimer):
            timer.cancel()

        # Release BGP resources (routes, etc)
        if self.bgpPeering: self.bgpPeering.releaseResources(self.protocol)
        
        self._closeConnection()
        
        self.connectRetryCounter += 1
        self.state = ST_IDLE
        
        if self.bgpPeering: self.bgpPeering.connectionClosed(self.protocol)
    
    def _closeConnection(self):
        """Internal method that close the connection if a valid BGP protocol
        instance exists.
        """
        
        if self.protocol is not None:
            self.protocol.closeConnection()
    

class BGP(protocol.Protocol):
    """Protocol class for BGP 4"""
       
    def __init__(self):
        self.deferred = defer.Deferred()
        self.fsm = None
    
        self.disconnected = False
        self.receiveBuffer = ''    

    def connectionMade(self):
        """
        Starts the initial negotiation of the protocol
        """
        
        # Set transport socket options
        self.transport.setTcpNoDelay(True)
        
        # DEBUG
        print "Connection established"
        
        try:
            self.fsm.connectionMade()
        except NotificationSent, e:
            self.deferred.errback(e)

    def connectionLost(self, reason):
        """Called when the associated connection was lost."""
        
        # Don't do anything if we closed the connection explicitly ourselves
        if self.disconnected: return
        
        # DEBUG
        print "Connection lost"
        
        try:
            self.fsm.connectionFailed()
        except NotificationSent, e:
            self.deferred.errback(e)

    def dataReceived(self, data):
        """Appends newly received data to the receive buffer, and
        then attempts to parse as many BGP messages as possible.
        """
        
        # Buffer possibly incomplete data first
        self.receiveBuffer += data
        
        # Attempt to parse as many messages as possible
        while(self.parseBuffer()): pass
    
    def closeConnection(self):
        """Close the connection"""
        
        if self.transport.connected:
            self.transport.loseConnection()
            self.disconnected = True
        
    def sendOpen(self):
        """Sends a BGP Open message to the peer"""
        
        # DEBUG
        print "Sending Open"
        
        self.transport.write(self.constructOpen())
    
    def sendKeepAlive(self):
        """Sends a BGP KeepAlive message to the peer"""
               
        self.transport.write(self.constructKeepAlive())
    
    def sendNotification(self, error, suberror, data=''):
        """Sends a BGP Notification message to the peer
        """
       
        self.transport.write(self.constructNotification(error, suberror, data))
    
    def constructHeader(self, message, type):
        """Prepends the mandatory header to a constructed BGP message"""
        
        return struct.pack('!16sHB',
                           chr(255)*16,
                           len(message)+19,
                           type) + message                           
    
    def constructOpen(self):
        """Constructs a BGP Open message"""
        
        msg = struct.pack('!BHHIB',
                          VERSION,
                          self.factory.myASN,
                          self.fsm.holdTime,
                          self.factory.bgpId,
                          0)
        
        # TODO: support optional parameters
        
        return self.constructHeader(msg, MSG_OPEN)
    
    def constructKeepAlive(self):
        """Constructs a BGP KeepAlive message"""
        
        return self.constructHeader('', MSG_KEEPALIVE)
    
    def constructNotification(self, error, suberror=0, data=''):
        """Constructs a BGP Notification message"""
        
        msg = struct.pack('!BB', error, suberror) + data
        return self.constructHeader(msg, MSG_NOTIFICATION)

    def parseBuffer(self):
        """Parse received data in receiveBuffer"""
        
        buf = self.receiveBuffer
        
        if len(buf) < HDR_LEN:
            # Every BGP message is at least 19 octets. Maybe the rest
            # hasn't arrived yet.
            return False
        
        # Check whether the first 16 octets of the buffer consist of
        # the BGP marker (all bits one)
        if buf[:16] != chr(255)*16:
            self.fsm.headerError(ERR_MSG_HDR_CONN_NOT_SYNC)
        
        # Parse the header
        try:
            marker, length, type = struct.unpack('!16sHB', buf[:HDR_LEN])
        except:
            self.fsm.headerError(ERR_MSG_HDR_CONN_NOT_SYNC)
        
        # Check the length of the message
        if length < HDR_LEN or length > MAX_LEN:
            self.fsm.headerError(ERR_MSG_HDR_BAD_MSG_LEN, struct.pack('!H', length))
        
        # Check whether the entire message is already available
        if len(buf) < length: return False
               
        message = buf[HDR_LEN:length]
        try:
            try:
                if type == MSG_OPEN:
                    self.openReceived(*self.parseOpen(message))
                elif type == MSG_UPDATE:
                    self.updateReceived(*self.parseUpdate(message))
                elif type == MSG_KEEPALIVE:
                    self.parseKeepAlive(message)
                    self.keepAliveReceived()
                elif type == MSG_NOTIFICATION:
                    self.notificationReceived(*self.parseNotification(message))
                else:    # Unknown message type
                    self.fsm.headerError(ERR_MSG_HDR_BAD_MSG_TYPE, chr(type))
            except BadMessageLength:
                self.fsm.headerError(ERR_MSG_HDR_BAD_MSG_LEN, struct.pack('!H', length))
        except NotificationSent, e:
            self.deferred.errback(e)
        
        # Message successfully processed, jump to next message
        self.receiveBuffer = self.receiveBuffer[length:]
        return True 
    
    def parseOpen(self, message):
        """Parses a BGP Open message"""
                            
        try:
            peerVersion, peerASN, peerHoldTime, peerBgpId, paramLen = struct.unpack('!BHHIB', message[:10])
        except:
            raise BadMessageLength(self)
        
        # Check whether these values are acceptable
        
        if peerVersion != VERSION:
            self.fsm.openMessageError(ERR_MSG_OPEN_UNSUP_VERSION, 
                                      struct.pack('!B', VERSION))
        
        if peerASN in (0, 2**16-1):
            self.fsm.openMessageError(ERR_MSG_OPEN_BAD_PEER_AS)
        
        # Hold Time is negotiated and/or rejected later
        
        if peerBgpId in (0, 2**32-1, self.bgpPeering.bgpId):
            self.fsm.openMessageError(ERR_MSG_OPEN_BAD_BGP_ID)
        
        # TODO: optional parameters
        
        return peerVersion, peerASN, peerHoldTime, peerBgpId
    
    def parseUpdate(self, message):
        """Parses a BGP Update message"""
        
        try:
            withdrawnLen = struct.unpack('!H', message[:2])[0]
            withdrawnPrefixesData = message[2:withdrawnLen+2]            
            attrLen = struct.unpack('!H', message[withdrawnLen+2:withdrawnLen+4])[0]
            attributesData = message[withdrawnLen+4:withdrawnLen+4+attrLen]
            nlriData = message[withdrawnLen+4+attrLen:]
            
            withdrawnPrefixes = BGP.parseEncodedPrefixList(withdrawnPrefixesData)
            attributes = BGP.parseEncodedAttributes(attributesData)
            nlri = BGP.parseEncodedPrefixList(nlriData)
        except BGPException, e:
            if (e.error, e.suberror) == (ERR_MSG_UPDATE, ERR_MSG_UPDATE_INVALID_NETWORK_FIELD):
                self.fsm.updateError(e.suberror)
            else:
                raise
        except:
            # RFC4271 dictates that we send ERR_MSG_UPDATE Malformed Attribute List
            # in this case
            self.fsm.updateError(ERR_MSG_UPDATE_MALFORMED_ATTR_LIST)

        return withdrawnPrefixes, attributes, nlri
    
    def parseKeepAlive(self, message):
        """Parses a BGP KeepAlive message"""
        
        # KeepAlive body must be empty
        if len(message) != 0: raise BadMessageLength(self)
    
    def parseNotification(self, message):
        """Parses a BGP Notification message"""
        
        try:
            error, suberror = struct.unpack('!BB', message[:2])
        except:
            raise BadMessageLength(self)
        
        return error, suberror, message[2:]
    
    def openReceived(self, version, ASN, holdTime, bgpId):
        """Called when a BGP Open message was received."""
        
        # DEBUG
        print "OPEN: version:", version, "ASN:", ASN, "hold time:", \
            holdTime, "id:", bgpId
            
        self.peerId = bgpId
        self.bgpPeering.setPeerId(bgpId)
        
        # Perform collision detection
        self.collisionDetect()
        
        self.negotiateHoldTime(holdTime)                
        self.fsm.openReceived()
        
        # DEBUG
        print "State is now:", stateDescr[self.fsm.state]
    
    def updateReceived(self, withdrawnPrefixes, attributes, nlri):
        """Called when a BGP Update message was received."""
        
        if len(nlri) > 0:
            try:
                attrSet = AttributeSet(attributes)
            except AttributeException, e:
                if e.suberror in (ERR_MSG_UPDATE_UNRECOGNIZED_WELLKNOWN_ATTR,
                                  ERR_MSG_UPDATE_MISSING_WELLKNOWN_ATTR):
                    # e.data is a typecode
                    self.fsm.updateError(e.suberror, chr(e.data))
                else:
                    # e.data is an attribute tuple
                    self.fsm.updateError(e.suberror, self.encodeAttribute(e.data))
        else:
            attrSet = set()
            
        self.fsm.updateReceived((withdrawnPrefixes, attrSet, nlri))

    def keepAliveReceived(self):
        """Called when a BGP KeepAlive message was received.
        """
        
        assert self.fsm.holdTimer.active()
        
        # DEBUG
        print "KEEPALIVE"
        
        self.fsm.keepAliveReceived()
        
        # DEBUG
        print "State is now:", stateDescr[self.fsm.state]

    def notificationReceived(self, error, suberror, data=''):
        """Called when a BGP Notification message was received.
        """
        
        # DEBUG
        print "NOTIFICATION:", error, suberror
        
        self.fsm.notificationReceived(error, suberror)

    def negotiateHoldTime(self, holdTime):
        """Negotiates the hold time"""
        
        self.fsm.holdTime = min(self.fsm.holdTime, holdTime)
        if self.fsm.holdTime != 0 and self.fsm.holdTime < 3:
            self.fsm.openMessageError(ERR_MSG_OPEN_UNACCPT_HOLD_TIME)
        
        # Derived times
        self.fsm.keepAliveTime = self.fsm.holdTime / 3
        
        # DEBUG
        print "hold time:", self.fsm.holdTime, "keepalive time:", self.fsm.keepAliveTime

    def collisionDetect(self):
        """Performs collision detection. Outsources to factory class BGPPeering."""
        
        return self.bgpPeering.collisionDetect(self)
    
    def isOutgoing(self):
        """Returns True when this protocol represents an outgoing connection,
        and False otherwise."""
        
        return (self.transport.getPeer().port == PORT)

    @staticmethod
    def parseEncodedPrefixList(data):
        """Parses an RFC4271 encoded blob of BGP prefixes into a list"""
        
        prefixes = []
        postfix = data
        while len(postfix) > 0:
            prefixLen = ord(postfix[0])
            if prefixLen > 32:
                raise BGPError(ERR_MSG_UPDATE, ERR_MSG_UPDATE_INVALID_NETWORK_FIELD)
            
            octetLen, remainder = prefixLen / 8, prefixLen % 8
            if remainder > 0:
                # prefix length doesn't fall on octet boundary
                octetLen += 1
            
            prefixData = map(ord, postfix[1:octetLen+1])
            # Zero the remaining bits in the last octet if it didn't fall
            # on an octet boundary
            if remainder > 0:
                prefixData[-1] = prefixData[-1] & (255 << (8-remainder))
                
            prefixes.append(IPPrefix((prefixData, prefixLen)))
            
            # Next prefix
            postfix = postfix[octetLen+1:]
        
        return prefixes
    
    @staticmethod
    def parseEncodedAttributes(data):
        """Parses an RFC4271 encoded blob of BGP prefixes into a list"""
        
        attributes = []
        postfix = data
        while len(postfix) > 0:
            flags, typeCode = struct.unpack('!BB', postfix[:2])
            
            if flags & ATTR_EXTENDED_LEN:
                attrLen = struct.unpack('!H', postfix[2:4])[0]
                value = postfix[4:4+attrLen]
                postfix = postfix[4+attrLen:]    # Next attribute
            else:    # standard 1-octet length
                attrLen = ord(postfix[2])
                value = postfix[3:3+attrLen]
                postfix = postfix[3+attrLen:]    # Next attribute
            
            attribute = (flags, typeCode, value)
            attributes.append(attribute)
                    
        return attributes
    
    @staticmethod
    def encodeAttribute(attrTuple):
        """Encodes a single attribute"""
        
        flags, typeCode, value = attrTuple
        if flags & ATTR_EXTENDED_LEN:
            fmtString = '!BBH'
        else:
            fmtString = '!BBB'
        
        return struct.pack(fmtString, flags, typeCode, len(value)) + value

class BGPFactory(protocol.Factory):
    """Base factory for creating BGP protocol instances"""
    
    protocol = BGP
    FSM = FSM
    
    myASN = None
    bgpId = None
    
    def buildProtocol(self, addr):
        """Builds a BGPProtocol instance"""
        
        assert self.myASN is not None and self.bgpId is not None
        
        return protocol.Factory.buildProtocol(self, addr)
    
    def startedConnecting(self, connector):
        # DEBUG
        print "Started connecting", connector    
    
    def clientConnectionLost(self, connector, reason):
        # DEBUG
        print "Client connection lost", connector, reason        

class BGPServerFactory(BGPFactory):
    """Class managing the server (listening) side of the BGP
    protocol. Hands over the factory work to a specific BGPPeering
    (factory) instance.
    """
    
    def __init__(self, peers):
        self.peers = peers
    
    def buildProtocol(self, addr):
        """Builds a BGPProtocol instance by finding an appropriate
        BGPPeering factory instance to hand over to.
        """
        
        # DEBUG
        print "Connection received from", addr.host
        
        try:
            bgpPeering = self.peers[addr.host]
        except KeyError:
            # This peer is unknown. Reject the incoming connection.
            return None
        
        return bgpPeering.takeServerConnection(addr)        
        

class BGPPeering(BGPFactory):
    """Class managing a BGP session with a peer"""

    implements(IBGPPeering, interfaces.IPushProducer)

    def __init__(self):
        self.peerAddr = None
        self.peerId = None
        self.fsm = BGPFactory.FSM(self)
        self.inConnections = []
        self.outConnections = []
        self.estabProtocol = None    # reference to the BGPProtocol instance in ESTAB state
        self.consumers = set()
       
    def buildProtocol(self, addr):
        """Builds a BGP protocol instance"""
        
        p = BGPFactory.buildProtocol(self, addr)
        if p is not None:
            self._initProtocol(p, addr)
            self.outConnections.append(p)
            
        return p
    
    def takeServerConnection(self, addr):
        """Builds a BGP protocol instance for a server connection"""
        
        p = BGPFactory.buildProtocol(self, addr)
        if p is not None:
            self._initProtocol(p, addr)
            self.inConnections.append(p)
            
        return p
        
    def _initProtocol(self, protocol, addr):    
        """Initializes a BGPProtocol instance"""

        protocol.bgpPeering = self
        
        # Hand over the FSM
        protocol.fsm = self.fsm
        protocol.fsm.protocol = protocol
        
        # Create a new fsm for internal use for now
        self.fsm = BGPFactory.FSM(self)
        self.fsm.state = protocol.fsm.state
        
        if addr.port == PORT:
            protocol.fsm.state = ST_CONNECT
        else:
            protocol.fsm.state = ST_ACTIVE
        
        # Set up callback and error handlers
        protocol.deferred.addCallbacks(self.sessionEstablished, self.protocolError)

    def clientConnectionFailed(self, connector, reason):
        """Called when the outgoing connection failed."""
        
        # DEBUG
        print "Client connection failed", connector, reason

        # There is no protocol instance yet at this point.
        # Catch a possible NotificationException
        try:
            self.fsm.connectionFailed()
        except NotificationSent, e:
            # TODO: error handling
            pass

    def manualStart(self):
        """BGP ManualStart event (event 1)"""
        
        if self.fsm.state == ST_IDLE:
            self.fsm.manualStart()        
            # Create outbound connection
            self.connect()
            self.fsm.state = ST_CONNECT
    
    def manualStop(self):
        """BGP ManualStop event (event 2)"""
        
        for c in inConnections + outConnections:
            # Catch a possible NotificationSent exception
            try:
                c.fsm.manualStop()
            except NotificationSent, e:
                c.deferred.errback(e)
    
    def releaseResources(self, protocol):
        """
        Called by FSM when BGP resources (routes etc.) should be released
        prior to ending a session.
        """
        
        print "Releasing resources"
    
    def connectionClosed(self, protocol):
        """
        Called by FSM when the BGP connection has been closed.
        """
        
        print "Connection closed", protocol
        
        if protocol is None:
            # Protocol did not exist yet, connection never succeeded
            # No further cleanup needed.
            return
        
        # Remove the protocol
        if protocol.isOutgoing():
            self.outConnections.remove(protocol)
        else:
            self.inConnections.remove(protocol)
        
        if protocol is self.estabProtocol:
            self.estabProtocol = None
            # self.fsm should still be valid and set to ST_IDLE
            assert self.fsm.state == ST_IDLE
    
    def completeInit(self, protocol):
        """
        Called by FSM when BGP resources should be initialized.
        """
    
    def sessionEstablished(self, protocol):
        """Called when the BGP session was established"""
        
        # The One True protocol
        self.estabProtocol = protocol
        self.fsm = protocol.fsm
        
        # Create a new deferred for later possible errors
        protocol.deferred = defer.Deferred()
        protocol.deferred.addErrback(self.protocolError)
        
        # Kill off all other possibly running protocols
        for p in self.inConnections + self.outConnections:
            if p != protocol:
                p.openCollisionDump()
                
    def connectRetryEvent(self, protocol):
        """Called by FSM when we should reattempt to connect."""
        
        self.connect()
                    
    def protocolError(self, failure):
        failure.trap(BGPException)
        
        print "BGP exception", failure
        
        e = failure.check(NotificationSent)
        try:
            # Raise the original exception
            failure.raiseException()
        except NotificationSent, e:
            if (e.error, e.suberror) == (ERR_MSG_UPDATE, ERR_MSG_UPDATE_ATTR_FLAGS):
                print "exception on flags:", BGP.parseEncodedAttributes(e.data)
            else:
                print e.error, e.suberror, e.data   
        
        # FIXME: error handling
        
    def setPeerId(self, bgpId):
        """
        Should be called when an Open message was received from a peer.
        Sets the BGP identifier of the peer if it wasn't set yet. If the
        new peer id is unequal to the existing one, CEASE all connections.
        """
        
        if self.peerId is None:
            self.peerId = bgpId
        elif self.peerId != bgpId:
            # Ouch, schizophrenia. The BGP id of the peer is unequal to
            # the ids of current and/or previous sessions. Close all
            # connections.
            self.peerId = None
            for c in inConnections + outConnections:
                try:
                    c.fsm.openCollisionDump()
                except NotificationSent, e:
                    c.deferred.errback(e)
    
    def collisionDetect(self, protocol):
        """
        Runs the collision detection algorithm as defined in the RFC.
        Returns True if the requesting protocol has to CEASE
        """

        # Construct a list of other connections to examine
        openConfirmConnections = [c
             for c
             in self.inConnections + self.outConnections
             if c != protocol and c.fsm.state in (ST_OPENCONFIRM, ST_ESTABLISHED)]
        
        # We need at least 1 other connections to have a collision
        if len(openConfirmConnections) < 1:
            return False

        # A collision exists at this point.
        
        # If one of the other connections is already in ESTABLISHED state,
        # it wins
        if ST_ESTABLISHED in [c.fsm.state for c in openConfirmConnections]:
            protocol.fsm.openCollisionDump()
            return True

        # Break the tie
        assert self.bgpId != protocol.peerId
        if self.bgpId < protocol.peerId:
            dumpList = outConnections
        elif self.bgpId > protocol.peerId:
            dumpList = inConnections

        for c in dumpList:
            try:
                c.fsm.openCollisionDump()
            except NotificationSent, e:
                c.deferred.errback(e)

        return (protocol in dumpList)
    
    def connect(self):
        """Initiates a TCP connection to the peer. Should only be called from
        BGPPeering or FSM, otherwise use manualStart() instead.
        """
        
        # DEBUG
        print "(Re)connect to", self.peerAddr
        
        if self.fsm.state != ST_ESTABLISHED:        
            reactor.connectTCP(self.peerAddr, PORT, self)
            return True
        else:
            return False
    
    def pauseProducing(self):
        """IPushProducer method - noop"""
        pass

    def resumeProducing(self):
        """IPushProducer method - noop"""
        pass
    
    def registerConsumer(self, consumer):
        """Subscription interface to BGP update messages"""
        
        consumer.registerProducer(self, streaming=True)
        self.consumers.add(consumer)
    
    def unregisterConsumer(self, consumer):
        """Unsubscription interface to BGP update messages"""
        
        consumer.unregisterProducer()
        self.consumers.remove(consumer)
    
    def update(self, update):
        """Called by FSM when a BGP Update message is received."""
        
        # Write to all BGPPeering consumers
        for consumer in self.consumers:
            consumer.write(update)
