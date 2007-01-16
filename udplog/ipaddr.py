# ipaddr.py -- handle IP addresses and set of IP addresses.
# Copyright (C) 1996-2005 Ingate Systems AB
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
# 
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# 
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

"""IP address manipulation.

This module is useful if you need to manipulate IP addresses or sets
of IP addresses.

The main classes are:

    ipaddr   -- a single IP address.
    netmask  -- a netmask.
    network  -- an IP address/netmask combination.  It is often, but
		not always, better to use the ip_set class instead.
    ip_set   -- a set of IP addresses, that may or may not be adjacent.

So, what can you do with this module?  As a simple example of the kind
of things this module can do, this code computes the set of all IP
addresses except 127.0.0.0/8 and prints it, expressed as a union of
network/netmask pairs.

    import ipaddr

    s = ipaddr.ip_set()
    s.add_network(ipaddr.network('127.0.0.0', '255.0.0.0',
                                 ipaddr.DEMAND_FILTER))
    for nw in s.complement().as_list_of_networks():
	print nw.ip_str() + '/' + nw.mask.netmask_bits_str

Errors are reported by raising an exception from the following
exception hierarcy:

Exception       # The standard Python base exception class.
 |
 +-- BadType    # Only raised if the programmer makes an error.
 +-- IpError    # Base class for errors that depend on the data.
      |
      +-- SetNotRepresentable
      +-- BrokenIpAddress
      |    |
      |    +-- PartNegative
      |    +-- PartOverflow
      |
      +-- BrokenNetmask
      |    |
      |    +-- NeedOneBit
      |    +-- NeedMoreBits
      |    +-- NeedLessBits
      |
      +-- BrokenNetwork
  	    |
  	    +-- EmptyIpAddress
  	    +-- EmptyNetmask
  	    +-- BrokenNetworkAddress
  	    +-- NetworkAddressClash
  	    +-- BroadcastAddressClash
  
BadType may be raised at any time if the programmer makes an error
(such as passing a dictionary to a function that expects a string).
SetNotRepresentable may be raised by ip_set.as_str_range().  All other
exceptions are raised from the constructors and helper functions only.

The following constants are present in this module:

    DEMAND_NONE		See class network.
    DEMAND_FILTER	See class network.
    DEMAND_NETWORK	See class network.
    DEMAND_INTERFACE	See class network.

    hostmask		A netmask object with all 32 bits set.
    complete_network	A network object representing all IP addresses.
    complete_set	An ip_set object representing all IP addresses.
    broadcast_network	A network object representing 255.255.255.255.
    broadcast_set	An ip_set object representing 255.255.255.255.
    
The as_ipaddr function can be used when you have an object that you
know is an ipaddr or network, and you want to get the ipaddr part.

All the other functions in this module are internal helper functions,
and they should not be used.

The internal representation used for IP addresses is currently a long
number.  That may change in the future, so where the internal
representation is visible, you should do nothing with it except
compare it to None.

This module was developed by Cendio Systems AB for use in the Fuego
Firewall.  Bug reports can be sent to Per Cederqvist <ceder@cendio.se>
who is currently acting as maintainer for this module.

Brief history:
    1997-03-11      Module created, and used internally.
    2000-03-09 1.0: First non-public beta release outside of Cendio Systems.
    2000-03-17 1.1: First public release under the GNU GPL license.

"""


import copy
import string
import types
import random

# The error messages are marked with a call to this function, so that
# they can easily be found and translated.
def _(s):
    return s

# The exception hierarchy.
class IpError(Exception):
    """Base class for errors that are cause by errors in input data.
    """
    def __str__(self):
	return self.format % self.args

class SetNotRepresentable(IpError):
    format = _("The set of IP addresses cannot be represented "
	       "as a single network range")

class BrokenIpAddress(IpError):
    format = _("Malformed IP address")

class PartNegative(BrokenIpAddress):
    format = _("A part of the IP address is negative")

class PartOverflow(BrokenIpAddress):
    format = _("A part of the IP address is greater than 255")

class BrokenNetmask(IpError):
    format = _("Malformed netmask")

class NeedOneBit(BrokenNetmask):
    format = _("At least one bit must be non-zero")

class NeedMoreBits(BrokenNetmask):
    format = _("At least %d bits must be non-zero")

class NeedLessBits(BrokenNetmask):
    format = _("At most %d bits may be non-zero")

class BrokenNetwork(IpError):
    """Base class for errors regarding network objects.
    """

class EmptyIpAddress(BrokenNetwork):
    format = _("IP address not entered")

class EmptyNetmask(BrokenNetwork):
    format = _("Netmask not entered")

class BrokenNetworkAddress(BrokenNetwork):
    format = _("%s is a malformed network address with the given netmask; "
	       "did you intend to write %s?")

class NetworkAddressClash(BrokenNetwork):
    format = _("The IP address clashes with the network address with "
	       "this setting of the netmask")

class BroadcastAddressClash(BrokenNetwork):
    format = _("The IP address clashes with the broadcast address with "
	       "this setting of the netmask")

class BadType(Exception):
    """An object of an unexpected type was passed to a function.
    """
    pass

# These constants are used with netmasks and networks to specify what
# the code expects.
#
#  DEMAND_NONE: netmask 0-32 (inclusive)
#  DEMAND_FILTER: netmask 0-32, the host part must be all zeroes
#  DEMAND_NETWORK: netmask 1-32, the host part must be all zeroes
#  DEMAND_INTERFACE: netmask 1-30, the host part must *not* be all zeroes
#  _DEMAND_P2P_INTERFACE: netmask 1-32

DEMAND_NONE = 1
DEMAND_FILTER = 2
DEMAND_NETWORK = 3
DEMAND_INTERFACE = 4
_DEMAND_P2P_INTERFACE = 5

def bits_to_intrep(bits):
    """Convert BITS to the internal representation.

    BITS should be a number in the range 0-32 (inclusive).

    """
    assert 0 <= bits and bits <= 32

    return pow(2L, 32) - pow(2L, 32-bits)


def intrep_with_bit_set(bit):
    """Return an internal representation with bit BIT set.

    BIT should be a number in the range 1-32, where bit 1 is the
    leftmost.  Examples:

      intrep_with_bit_set(1) --> the internal representation of 128.0.0.0
      intrep_with_bit_set(32) --> the internal representation of 0.0.0.1
    """
    assert 0 < bit and bit <= 32

    return pow(2L, 32-bit)


__ONES = {0:0, 128:1, 192:2, 224:3,
	  240:4, 248:5, 252:6, 254:7}

def tuple_to_bits(mask):
    """Convert MASK to bits.

    MASK should be a tuple of four integers in the range 0-255 (inclusive).

    Raises BrokenNetmask if MASK is not a valid netmask.
    """

    if mask == None:
	return None
    else:
	(a, b, c, d) = mask

	if a == 255 and b == 255 and c == 255 and d == 255:
	    return 32

	try:
	    if a == 255 and b == 255 and c == 255:
		return 24 + __ONES[d]
	    elif a == 255 and b == 255  and d == 0:
		return 16 + __ONES[c]
	    elif a == 255 and c == 0  and d == 0:
		return 8 + __ONES[b]
	    elif b == 0 and c == 0  and d == 0:
		return __ONES[a]
	except KeyError:
	    pass

	raise BrokenNetmask()


def intrep_to_dotted_decimal(t):
    """Convert T to dotted-decimal notation.

    T should be the internal representation used py ipaddr.py.
    """

    return (str(int(t>>24)) + '.' + str(int((t>>16) & 255))
	    + '.' + str(int((t>>8) & 255)) + '.' + str(int(t & 255)))


def as_ipaddr(nwip):
    """Return the IP address object of NWIP.

    NWIP may be an ipaddr object, which is returned unchanged,
    or a network object, in which case the ipaddr part of it is
    returned.
    """

    if isinstance(nwip, ipaddr):
	return nwip
    elif isinstance(nwip, network):
	return nwip.ip
    else:
	raise BadType('Expected a network or ipaddr object', nwip)


class ipaddr(object):
    """Handle IP addresses.

    Sample use:

        ip1 = ipaddr('12.3.5.1')
	ip2 = ipaddr(201524481L)
	print ip1.ip_str()
	>>> '12.3.5.1'
	print ip1.intrep
	>>> 201524481L
	print ip2.ip_str()
	>>> '12.3.5.1'
	print ip2.intrep
	>>> 201524481L

    An ipaddr object can have two states: empty or good.
    The status can be examined like this:

	if ip.intrep == None:
	    handle_empty(ip.user_input())
	else:
	    handle_good(ip)

    All other members should only be used in the good state.  The
    value stored in the intrep member should only be compared against
    None.  The type and value of it is an internal detail that may
    change in the future.

    """

    def __init__(self, ip):
	"""Create an ipaddr from IP (a string, integer or ipaddr).

	The empty string or None may be given; it is handled as the
	empty IP address.
	"""

	if type(ip) == types.StringType:
	    self.__user_input = ip
	    ip = ip.strip()
	else:
	    self.__user_input = None

	# Check if we can extract an IP string.
	try:
	    ip = ip.ip_str()
	except:
	    pass

	# The empty IP address?
	if ip == '' or ip == None:
	    self.__ip_str = ''
	    self.intrep = None
	    if self.__user_input == None:
		self.__user_input = ''
	    return

	if type(ip) == types.StringType:
	    # Convert a string.
	    try:
		[a, b, c, d] = map(int, ip.split('.'))
	    except:
		raise BrokenIpAddress()

	    if a < 0 or b < 0 or c < 0 or d < 0:
		raise PartNegative()

	    if a > 255 or b > 255 or c > 255 or d > 255:
		raise PartOverflow()

	    self.intrep = (long(a) << 24) + (b << 16) + (c << 8) + d
	else:
	    self.intrep = long(ip)

	self.__ip_str = None

    def ip_str(self):
	return str(self)

    def host_str(self):
	"""Return IP address or host name as a string.

	This implementation always returns an IP address.
	"""
	return str(self)

    def user_input(self):
	if self.__user_input == None:
	    # Generate a string.
	    self.__user_input = self.ip_str()
	return self.__user_input

    def compare(self, other):
	"""Compare this IP address with OTHER.

	Returns -1, 0 or 1 if this IP address is less than, equal to,
	or greater than OTHER (which should be an ipaddr object).
	"""
	if self.intrep == other.intrep:
	    return 0
	if self.intrep < other.intrep:
	    return -1
	else:
	    return 1

    def __cmp__(self, other):
	if not isinstance(other, ipaddr):
	    return NotImplemented
	return self.compare(other)

    def __str__(self):
	if self.__ip_str == None:
	    self.__ip_str = intrep_to_dotted_decimal(self.intrep)
	return self.__ip_str

    def __repr__(self):
	if self.intrep is None:
	    return "ipaddr.ipaddr('')"
	else:
	    return "ipaddr.ipaddr('%s')" % self.ip_str()

    def __eq__(self, other):
        """Compare self with other.

           Both the IP address represented by this object and the
           return value of user_input() must be equal.
        """

        if not isinstance(other, ipaddr):
            return NotImplemented

        if self.__user_input is None and other.__user_input is not None:
            self.user_input()
        elif self.__user_input is not None and other.__user_input is None:
            other.user_input()

        return (self.intrep == other.intrep
                and self.__user_input == other.__user_input)

    def __ne__(self, other):
        return not self == other

    def __hash__(self):
	return hash((self.__class__, self.intrep))


class netmask(object):
    """Handle netmasks.

    Sample use:

	# Five ways to initialize a netmask.
        nm1 = netmask('255.255.128.0', DEMAND_NONE)
	nm2 = netmask([255, 255, 128, 0], DEMAND_NONE)
	nm3 = netmask('17', DEMAND_NONE)
	nm4 = netmask(17, DEMAND_NONE)
	nm5 = netmask(4294967040L, DEMAND_NONE)
	print nm1.netmask_str()
	>>> '255.255.128.0'
	print nm1.intrep
	>>> 4294934528L
	print repr(nm1.netmask_bits)
	>>> 17
	print repr(nm1.netmask_bits_str)
	>>> '17'

    A netmask can have two states: empty or good.  The state
    can be examined like this:

	if m.intrep == None:
	    handle_empty(m.user_input())
	else:
	    handle_good(m)

    All other members should be used only in the good state.

    """

    def __check_range(self, bits, minbits, maxbits):
	if bits < minbits:
	    if minbits == 1:
		raise NeedOneBit()
	    else:
		raise NeedMoreBits(minbits)
	elif bits > maxbits:
	    raise NeedLessBits(maxbits)


    def __set_from_bits(self, bits, minbits, maxbits):
	self.__check_range(bits, minbits, maxbits)
	self.intrep = bits_to_intrep(bits)
	self.netmask_bits = bits


    def __set_from_tuple(self, tpl, minbits, maxbits):
	bits = tuple_to_bits(tpl)
	self.__check_range(bits, minbits, maxbits)
	self.intrep = bits_to_intrep(bits)
	self.netmask_bits = bits

    DEMANDS = {DEMAND_NONE:(0,32),
	       DEMAND_FILTER:(0,32),
	       DEMAND_NETWORK:(1,32),
	       DEMAND_INTERFACE:(1,30),
	       _DEMAND_P2P_INTERFACE:(1,32)}

    def __init__(self, mask, demand = DEMAND_NONE):
	"""Create a netmask from MASK (a string, tuple or number) and DEMAND.

	The empty string or None may be given; it is handled as the
	empty netmask.

	See class network for a description of the DEMAND parameter.
	"""

	(minbits, maxbits) = self.DEMANDS[demand]
	self.demand = demand

	if type(mask) == types.StringType:
	    self.__user_input = mask
	    mask = mask.strip()
	else:
	    self.__user_input = None

	# Check if we can extract a netmask string.
	try:
	    mask = mask.netmask_str()
	except:
	    pass

	if mask == '' or mask == None:
	    # Handle empty netmasks.
	    self.__netmask_str = ''
	    self.intrep = None
	    self.netmask_bits_str = ''
	    self.netmask_bits = None
	    if self.__user_input == None:
		self.__user_input = ''
	    return

	# Decode the MASK argument and set self.netmask_bits
	# and self.intrep.

	if type(mask) == types.StringType:
	    # Is this a string containing a single number?
	    try:
		bits = int(mask)
	    except (OverflowError, ValueError):
		bits = None

	    if bits != None:
		# This is a string containing a single number.
		self.__set_from_bits(bits, minbits, maxbits)
	    else:
		# Interpret the netmask as a dotted four-tuple.
		try:
		    [a, b, c, d] = map(int, mask.split('.'))
		except:
		    raise BrokenNetmask()

		self.__set_from_tuple((a, b, c, d), minbits, maxbits)

	elif type(mask) == types.IntType:
	    # This is a number, representing the number of bits in the mask.
	    self.__set_from_bits(mask, minbits, maxbits)
	elif type(mask) == types.LongType:
	    # This is a 32-bit bitmask.
	    a = (mask & 0xff000000L) >> 24
	    b = (mask & 0x00ff0000) >> 16
	    c = (mask & 0x0000ff00) >> 8
	    d = (mask & 0x000000ff)
	    self.__set_from_tuple((a, b, c, d), minbits, maxbits)
	else:
	    # This is a tuple or list.
	    if len(mask) != 4:
		raise BadType('Wrong len of tuple/list')

	    (a, b, c, d) = (mask[0], mask[1], mask[2], mask[3])

	    self.__set_from_tuple((a, b, c, d), minbits, maxbits)

	self.__netmask_str = None
	self.netmask_bits_str = repr(self.netmask_bits)

    def netmask_str(self):
	return str(self)

    def user_input(self):
	if self.__user_input == None:
	    # Generate a string.
	    self.__user_input = self.netmask_str()
	return self.__user_input

    def __str__(self):
	if self.__netmask_str == None:
	    self.__netmask_str = intrep_to_dotted_decimal(self.intrep)
	return self.__netmask_str

    def __repr__(self):
        if self.intrep is None:
            return "ipaddr.netmask('')"
        else:
            return "ipaddr.netmask(%d, %d)" % (self.netmask_bits, self.demand)

    def __eq__(self, other):

        if not isinstance(other, netmask):
            return NotImplemented

        if self.__user_input is None and other.__user_input is not None:
            self.user_input()
        elif self.__user_input is not None and other.__user_input is None:
            other.user_input()

        return (self.intrep == other.intrep
                and self.__user_input == other.__user_input
                and self.demand == other.demand)

    def __ne__(self, other):
        return not self == other

    def __cmp__(self, other):
	if not isinstance(other, netmask):
	    return NotImplemented
	# Note that largest netmask is smallest net
	return cmp(other.netmask_bits, self.netmask_bits)

    def __hash__(self):
	return hash((self.__class__, self.intrep))
        


hostmask = netmask(32, DEMAND_NONE)
	

class network(object):
    """Designate a network or host.

    The constructor takes four arguments: the IP address part, the
    netmask part, an optional demand parameter and an optional peer
    parameter.  See class ipaddr and class netmask for a description
    of the first two arguments.  The demand argument can be one of the
    following constants:

    DEMAND_NONE
        No special demands.
    DEMAND_FILTER
        The host part must be all zeroes.
    DEMAND_NETWORK
        The netmask must be 1-32
	The host part must be all zeroes.
    DEMAND_INTERFACE
        The netmask must be 1-30
	The host part must *not* be all zeroes (the network address)
        or all ones (the broadcast address).

    The peer argument is either None or something that can be converted
    to an ipaddr object.

    The following members exist and are set by the constructor:

      ip.user_input()		# a caching function
      ip.ip_str()		# a caching function
      ip.intrep
      mask.user_input()		# a caching function
      mask.netmask_str()	# a caching function
      mask.intrep
      mask.netmask_bits
      mask.netmask_bits_str
      ip_str()			# an alias for ip.ip_str()
      netmask_str()		# an alias for mask.netmask_str()
      network_str()		# a caching function
      network_intrep
      broadcast_str()		# a caching function
      broadcast_intrep
      host_part_str()		# a caching function
      host_part_intrep
      interval_str()		# a caching function

    """

    def __init__(self, ip, mask, demand = DEMAND_NONE, peer = None):
	if peer != None:
	    peer = ipaddr(peer)
	    if demand == DEMAND_INTERFACE:
		demand = _DEMAND_P2P_INTERFACE

	self.ip = ipaddr(ip)
	self.mask = netmask(mask, demand)
	self.peer = peer

	if self.ip.intrep == None:
	    raise EmptyIpAddress()

	if self.mask.intrep == None:
	    raise EmptyNetmask()

	self._precompute()

    def _precompute(self):
	self.__lower_str = None
	self.__upper_str = None

	self.network_intrep = self.ip.intrep & self.mask.intrep
	self.broadcast_intrep = (self.network_intrep |
				(pow(2L, 32)-1-self.mask.intrep))
	self.host_part_intrep = self.ip.intrep - self.network_intrep

	self.__network_str = None
	self.__broadcast_str = None
	self.__host_part_str = None
	self.__interval_str = None

	demand = self.mask.demand

	if demand == DEMAND_NONE or demand == _DEMAND_P2P_INTERFACE:
	    pass
	elif demand == DEMAND_FILTER or demand == DEMAND_NETWORK:
	    if self.host_part_intrep != 0L:
		raise BrokenNetworkAddress(self.ip_str(), self.network_str())
	elif demand == DEMAND_INTERFACE:
	    if self.host_part_intrep == 0L:
		raise NetworkAddressClash()
	    elif self.broadcast_intrep == self.ip.intrep:
		raise BroadcastAddressClash()
	else:
	    raise BadType('Bad value for the demand parameter', demand)

    def network_str(self):
	if self.__network_str == None:
	    self.__network_str = intrep_to_dotted_decimal(self.network_intrep)
	return self.__network_str

    def broadcast_str(self):
	if self.__broadcast_str == None:
	    self.__broadcast_str = intrep_to_dotted_decimal(
		self.broadcast_intrep)
	return self.__broadcast_str

    def host_part_str(self):
	if self.__host_part_str == None:
	    self.__host_part_str = intrep_to_dotted_decimal(
		self.host_part_intrep)
	return self.__host_part_str

    def interval_str(self):
	if self.__interval_str == None:
	    if self.mask.netmask_bits == 32:
		self.__interval_str = self.network_str()
	    else:
		self.__interval_str = (self.network_str() + ' - ' +
				       self.broadcast_str())
	return self.__interval_str

    def overlaps(self, other):
	"""Returns true if the network overlaps with OTHER.

	OTHER must be a network object or an ipaddr object.  If it
	is empty this method will always return false.

	"""

	if self.network_intrep == None:
	    return False

	if isinstance(other, ipaddr):

	    if other.intrep == None:
		return False

	    return (self.mask.intrep & other.intrep) == self.network_intrep
	else:
	    if other.network_intrep == None:
		return False

	    mask = self.mask.intrep & other.mask.intrep
	    return (mask & self.ip.intrep) == (mask & other.ip.intrep)

    def intersection(self, other):
	"""Return the intersection of the network and OTHER.

	The return value is a network object with DEMAND_FILTER.  If
	the intersection is empty this method will return None.

	OTHER must be a network object or an ipaddr object.  The
	intersection will be empty if it is empty.
	"""

	if self.network_intrep == None:
	    return None

	if isinstance(other, ipaddr):

	    if other.intrep == None:
		return None

	    prefix_mask = self.mask.intrep
	    short_net = self.network_intrep
	    long_ip = other.intrep
	    result = self.__class__(other.intrep, 32, DEMAND_FILTER)
	else:
	    if other.network_intrep == None:
		return None
	    
	    if self.mask.netmask_bits < other.mask.netmask_bits:
		prefix_mask = self.mask.intrep
		short_net = self.network_intrep
		long_ip = other.network_intrep
		result = self.__class__(other.network_intrep,
					other.mask.netmask_bits,
					DEMAND_FILTER)
	    else:
		prefix_mask = other.mask.intrep
		short_net = other.network_intrep
		long_ip = self.network_intrep
		result = self.__class__(self.network_intrep,
					self.mask.netmask_bits,
					DEMAND_FILTER)

	if (long_ip & prefix_mask) != (short_net & prefix_mask):
	    return None

	return result

    def is_subset(self, nwip):
	"""Return true if NWIP is a subset of this network.

	NWIP must be a network object or an ipaddr object.
	"""

	if not self.overlaps(nwip):
	    return False

	if isinstance(nwip, ipaddr):
	    return True

	return nwip.mask.netmask_bits >= self.mask.netmask_bits

    def is_same_set(self, nwip):
	"""Return true if NWIP contains the same set as this network.

	NWIP must be a network object or an ipaddr object.
	"""

	if isinstance(nwip, ipaddr):
	    return (self.mask.netmask_bits == 32
		    and self.ip.intrep == nwip.intrep)
	else:
	    return (self.mask.netmask_bits == nwip.mask.netmask_bits
		    and self.network_intrep == nwip.network_intrep)

    def subtract(self, nwip):
	"""Create a list of new network objects by subtracting NWIP from self.

	The result consists of networks that together span all
	IP addresses that are present in self, except those that are
	present in NWIP.  (The result may be empty or contain several
	disjoint network objects.)

	Don't use this!  This method is slow.  The ip_set class can do
	this kind of things in a more efficient way.
	"""

	if not self.overlaps(nwip):
	    # No overlap at all, so NWIP cannot affect the result.
	    return [self]

	if isinstance(nwip, ipaddr):
	    bits = 32
	    intrep = nwip.intrep
	else:
	    assert isinstance(nwip, network)
	    bits = nwip.mask.netmask_bits
	    intrep = nwip.ip.intrep
	nets = []
	while bits > self.mask.netmask_bits:
	    nets.append(self.__class__(compute_neighbor(intrep, bits),
				       bits, DEMAND_FILTER))
	    bits = bits - 1
	return nets

    def subtract_nwips(self, nwips):
	"""Create a list of new network objects by subtracting NWIPS.

	The result consists of networks that together span all
	IP addresses that are present in self, except those that are
	present in NWIPS.  (The result may be empty or contain
	several disjoint network objects.)  NWIPS should be a list
	of network or ipaddr objects.

	Don't use this!  This method is slow.  The ip_set class can do
	this kind of things in a more efficient way.
	"""

	subtracted = [self]
	for s in nwips:
	    # precondition<A>: SUBTRACTED is a list of networks
	    tmp = []
	    for nw in subtracted:
		tmp = tmp + nw.subtract(s)
	    subtracted = tmp
	    # postcondition: SUBTRACTED is a list of networks that
	    # spans all IP addresses that were present in
	    # precondition<A>, except those that are present in S.

	return subtracted

    def __compute_lower_upper(self):
	if self.__lower_str != None:
	    return
	assert self.network_intrep != None and self.broadcast_intrep != None

	self.__lower_str = intrep_to_dotted_decimal(self.network_intrep + 1)
	self.__upper_str = intrep_to_dotted_decimal(self.broadcast_intrep - 1)

    def lower_host(self):
	self.__compute_lower_upper()
	return self.__lower_str

    def upper_host(self):
	self.__compute_lower_upper()
	return self.__upper_str

    def ip_str(self):
	return self.ip.ip_str()

    def netmask_str(self):
        return self.mask.netmask_str()

    def prefix_str(self):
	return '%s/%d' % (self.ip_str(), self.mask.netmask_bits)

    def network_prefix_str(self):
	return '%s/%d' % (intrep_to_dotted_decimal(self.network_intrep),
                          self.mask.netmask_bits)

    def size(self):
        """Return the total number of addresses in the network.
        """
        return self.broadcast_intrep - self.network_intrep + 1

    def __str__(self):
	return "%s/%d" % (self.ip_str(), self.mask.netmask_bits)

    def __repr__(self):
	return "ipaddr.network('%s', %d, %d)" % \
	       (self.ip_str(), self.mask.netmask_bits, self.mask.demand)

    def __eq__(self, other):
        if not isinstance(other, network):
            return NotImplemented

        return self.ip == other.ip and self.mask == other.mask

    def __ne__(self, other):
        return not self == other

    def __cmp__(self, other):
	if not isinstance(other, network):
	    return NotImplemented
	# Note that largest netmask is smallest net
	return (cmp(other.mask.netmask_bits, self.mask.netmask_bits)
		or cmp(self.ip.intrep, other.ip.intrep))

    def __hash__(self):
	return hash((self.__class__, self.ip.intrep, self.mask.intrep))


class ip_set(object):
    def __init__(self, nwip=None):
	"""Create an ip_set.

	If the optional argument NWIP is supplied, the set is
	initialized to it, otherwise the created set will be empty.
	NWIP must be a network or ipaddr object.
	"""

	# [[0L, 3L], [5L, 7L]] means 0.0.0.0/29 \ 0.0.0.4/32
	self.__set = []

	if nwip != None:
	    self.append(nwip)

    def subtract_set(self, other):
	"""Remove all IP addresses in OTHER from this.

	OTHER should be an ip_set object.
	"""

	self.subtract_list(other.__set)

    def subtract_list(self, other):
	# Don't use this method directly, unless you are the test suite.
	ix = 0
	iy = 0
	while ix < len(self.__set) and iy < len(other):
	    if self.__set[ix][1] < other[iy][0]:
		# The entire range survived.
		ix = ix + 1
	    elif self.__set[ix][0] > other[iy][1]:
		# The entire other range is unused, so discard it.
		iy = iy + 1
	    elif self.__set[ix][0] >= other[iy][0]:
		if self.__set[ix][1] <= other[iy][1]:
		    # The entire range is subtracted.
		    del self.__set[ix]
		else:
		    # The start of the range is subtracted, but
		    # the rest of the range may survive.  (As a matter
		    # of fact, at least one number *will* survive,
		    # since there should be a gap between other[iy][1]
		    # and other[iy+1][0], but we don't use that fact.)
		    self.__set[ix][0] = other[iy][1] + 1
		    iy = iy + 1
	    else:
		# The first part of the range survives.
		end = self.__set[ix][1]
		assert self.__set[ix][1] >= other[iy][0]
		self.__set[ix][1] = other[iy][0] - 1
		ix = ix + 1
		if end > other[iy][1]:
		    # The part that extends past the subtractor may survive.
		    self.__set[ix:ix] = [[other[iy][1] + 1, end]]
		# Retain the subtractor -- it may still kill some
		# other range.

    def subtract_ips(self, ips):
	"""Remove all IP addresses in IPS from this.

	IPS should be a list of ipaddr objects.
	"""

	for ip in ips:
	    self.subtract_list([[ip.intrep, ip.intrep]])

    def subtract_network(self, nw):
	"""Subtract NW from this.

	NW should be a network object.
	"""
	self.subtract_list([[nw.network_intrep, nw.broadcast_intrep]])

    def subtract_range(self, lo_ip, hi_ip):
	"""Subtract the range of IPs specified by LO_IP and HI_IP from this.

	LO_IP and HI_IP should be ipaddr objects.  They specify a
	range of IP addresses.  Both LO_IP and HI_IP are included in the
	range.
	"""

	assert lo_ip.intrep != None
	assert hi_ip.intrep != None
	assert lo_ip.intrep <= hi_ip.intrep
	self.subtract_list([[lo_ip.intrep, hi_ip.intrep]])

    def add_set(self, other):
	"""Add all IP addresses in OTHER to this.

	OTHER should be an ip_set object.
	"""

	self.add_list(copy.deepcopy(other.__set))

    def add_list(self, other):
	# Don't use this method directly, unless you are the test suite.
	ix = 0
	iy = 0
	res = []
	while ix < len(self.__set) or iy < len(other):
	    # Remove the first range
	    if ix < len(self.__set):
		if iy < len(other):
		    if self.__set[ix][0] < other[iy][0]:
			rng = self.__set[ix]
			ix = ix + 1
		    else:
			rng = other[iy]
			iy = iy + 1
		else:
		    rng = self.__set[ix]
		    ix = ix + 1
	    else:
		rng = other[iy]
		iy = iy + 1

	    # Join this range to the list we already have collected.
	    if len(res) == 0:
		# This is the first element.
		res.append(rng)
	    elif rng[0] <= res[-1][1] + 1:
		# This extends (or is consumed by) the last range.
		res[-1][1] = max(res[-1][1], rng[1])
	    else:
		# There is a gap between the previous range and this one.
		res.append(rng)

	self.__set = res

    def append(self, nwip):
	"""Add NWIP to this.

	NWIP should be a network object or ipaddr object.
	"""

	if isinstance(nwip, network):
	    self.add_network(nwip)
	else:
	    self.add_ipaddr(nwip)

    def add_ipaddr(self, ip):
	"""Add IP to this.

	IP should be an ipaddr object.
	"""

	assert ip.intrep != None
	self.add_list([[ip.intrep, ip.intrep]])

    def add_network(self, nw):
	"""Add NW to this.

	NW should be a network object.
	"""
	self.add_list([[nw.network_intrep, nw.broadcast_intrep]])

    def add_range(self, lo_ip, hi_ip):
	"""Add the range of IP addresses specified by LO_IP and HI_IP to this.

	LO_IP and HI_IP should be ipaddr objects.  They specify a
	range of IP addresses.  Both LO_IP and HI_IP are included in the
	range.
	"""

	assert lo_ip.intrep != None
	assert hi_ip.intrep != None
	assert lo_ip.intrep <= hi_ip.intrep
	self.add_list([[lo_ip.intrep, hi_ip.intrep]])

    def complement(self):
	"""Return everything not contained in this ip_set.

	The return value is a new ip_set.  This is not modified.
	"""

	pre = -1L
	lst = []
	for [lo, hi] in self.__set:
	    if lo != 0:
		lst.append([pre+1, lo-1])
	    pre = hi
	if pre < pow(2L, 32) - 1:
	    lst.append([pre+1, pow(2L, 32) - 1])
	res = self.__class__()
	res.add_list(lst)
	return res

    def intersection(self, other):
	"""Return the intersection of this and OTHER.

	The return value is a new ip_set.  This is not modified.
	OTHER should be an ip_set, network or ipaddr object.
	"""

	res = []
	ix = 0
	iy = 0
	x = copy.deepcopy(self.__set)

	if isinstance(other, ip_set):
	    y = copy.deepcopy(other.__set)
	elif isinstance(other, network):
	    y = [[other.network_intrep, other.broadcast_intrep]]
	elif isinstance(other, ipaddr):
	    y = [[other.intrep, other.intrep]]
	else:
	    raise BadType('expected an ip_set, network or ipaddr argument')

	while ix < len(x) and iy < len(y):
	    if x[ix][1] < y[iy][0]:
		# The first entry on x doesn't overlap with anything on y.
		ix = ix + 1
	    elif x[ix][0] > y[iy][1]:
		# The first entry on y doesn't overlap with anything on x.
		iy = iy + 1
	    else:
		# Some overlap exists.

		# Trim away any leading edges.
		if x[ix][0] < y[iy][0]:
		    # x starts before y
		    x[ix][0] = y[iy][0]
		elif x[ix][0] > y[iy][0]:
		    # y starts before x
		    y[iy][0] = x[ix][0]

		# The ranges start at the same point (at least after
		# the trimming).
		if x[ix][1] == y[iy][1]:
		    # The ranges are equal.
		    res.append(x[ix])
		    ix = ix + 1
		    iy = iy + 1
		elif x[ix][1] < y[iy][1]:
		    # x is the smaller range
		    res.append(x[ix])
		    ix = ix + 1
		else:
		    # y is the smaller range
		    res.append(y[iy])
		    iy = iy + 1

	result = self.__class__()
	result.add_list(res)
	return result


    def as_list_of_networks(self):
	"""Return this set as a list of networks.

	The returned value is a list of network objects, that are
	created with DEMAND_FILTER.  This method may be expensive, so
	it should only be used when necessary.
	"""

	bm = []
	for [a, b] in self.__set:

	    lomask = 1L
	    lobit = 1L
	    himask = pow(2L, 32)-2
	    bits = 32
	    while a <= b:
		if a & lomask != 0L:
		    bm.append((bits, a))
		    a = a + lobit
		elif b & lomask != lomask:
		    bm.append((bits, b & himask))
		    b = b - lobit
		else:
		    lomask = (lomask << 1) | 1
		    lobit = lobit << 1
		    himask = himask ^ lobit
		    bits = bits - 1
		    assert(bits >= 0)
	bm.sort()
	res = []
	for (mask, ip) in bm:
	    res.append(network(ip, mask, DEMAND_FILTER))
	return res

    def as_network(self):
	"""Return this set as a network.

	Raises SetNotRepresentable if the set cannot be represented as a
	single network, or if it is the empty set.
	"""
	res = self.as_list_of_networks()
	if len(res) != 1:
	    raise SetNotRepresentable()
	return res[0]

    def as_list_of_ranges(self):
	"""Return the set of IP addresses as a list of ranges.

	Each range is a list of two long numbers.  Sample return
	value: [[1L, 3L], [0x7f000001L, 0x7f000001L]], meaning
	the set 0.0.0.1, 0.0.0.2, 0.0.0.3, 127.0.0.1.
	"""

	# This method is currently very cheap, since this is the
	# current internal representation.

	return self.__set

    def as_list_of_str_ranges(self):
	"""Return the set of IP addresses as a list of ranges 
	converted to strings..

	Each range is a list of two strings.  Sample return
	value: [["0.0.0.1", "0.0.0.2"], ["0.0.0.3", "127.0.0.1"]].

	"""
	res = []
	for s in self.__set:
	    res.append([intrep_to_dotted_decimal(s[0]),
			intrep_to_dotted_decimal(s[1])])

	return res

    def as_str_range(self):
	"""Return the set as a string, such as "1.2.3.4-1.2.3.8".

	The returned value always has the form a.b.c.d-e.f.g.h.
	Raises SetNotRepresentable if the set cannot be represented as a
	single interval, or if it is the empty set.
	"""
	if len(self.__set) != 1:
	    raise SetNotRepresentable()
	return '%s-%s' % (intrep_to_dotted_decimal(self.__set[0][0]),
			  intrep_to_dotted_decimal(self.__set[0][1]))

    def contains(self, ip):
	"""Return true if IP is contained in the set.

	IP should be an ipaddr object.  The empty ipaddr is never contained.
	"""

	if ip.intrep == None:
	    return False

	for [lo, hi] in self.__set:
	    if lo <= ip.intrep <= hi:
		return True
	return False

    def __contains__(self, ip):
        """Return true if IP is contained in the set.

        Return False if IP isn't an ipaddr object.
        """
        if type(ip) == type(ipaddr) and isinstance(ip, ipaddr):
            return self.contains(ip)
        else:
            return False

    def overlaps(self, nwip):
	"""Return true if NWIP overlaps the set of IP addresses.

	NWIP may be an ipaddr, network or ip_set object.
	"""

	if isinstance(nwip, ipaddr):
	    return self.contains(nwip)
	elif isinstance(nwip, ip_set):
	    # This could be optimized -- we don't really need
	    # to compute the intersection.
	    return not self.intersection(nwip).is_empty()
	elif isinstance(nwip, network):
	    wanted_low = nwip.network_intrep
	    wanted_high = nwip.broadcast_intrep
	    if wanted_low == None or wanted_high == None:
		return False
	    for [lo, hi] in self.__set:
		if lo > wanted_high:
		    # We are past the interresting interval.
		    return False
		if lo >= wanted_low or hi >= wanted_low:
		    return True
	    return False
	else:
	    raise BadType('Expected an ipaddr, ip_set or network instance')

    def is_empty(self):
	"""Return true if this ip_set is empty.
	"""

	return len(self.__set) == 0

    def any_ip(self):
	"""Return one of the IP addresses contained in ip_set.

	This method may only be called if the set is non-empty.  You
	can use the is_empty method to test for emptiness.

	This picks an IP address from the set and returns it as an
	ipaddr object.  Given the same set of IP addresses, this
	method will always return the same IP address, but which IP
	address it chooses is explicitly undocumented and may change
	if the underlying implementation of ip_set ever changes.
	"""

	assert not self.is_empty()
	return ipaddr(self.__set[0][0])

    def size(self):
        """Return the total number of addresses in the network.
        """

        size = 0
        for (low, high) in self.__set:
            size = size + high - low + 1

        return size
    
    def random_ip(self):
	"""Return a random IP address contained in ip_set.

	This method may only be called if the set is non-empty.  You
	can use the is_empty method to test for emptiness.

	This picks an IP address from the set using the random module
	and returns it as an ipaddr object.
        """

	assert not self.is_empty()

        size = self.size()
        index = random.randrange(0, size)
        for (low, high) in self.__set:
            size_this = high - low + 1
            if index < size_this:
                # Time to choose from this part.
                return ipaddr(low+index)
            else:
                # It belongs to some other set.
                index = index - size_this

        # We should never get here!
        raise ValueError()

    def __str__(self):
	res = []
	for rng in self.__set:
	    if rng[0] == rng[1]:
		res.append(intrep_to_dotted_decimal(rng[0]))
	    else:
		res.append('%s-%s' % (intrep_to_dotted_decimal(rng[0]),
				      intrep_to_dotted_decimal(rng[1])))
	return ', '.join(res)

    def __eq__(self, other):
        if not isinstance(other, ip_set):
            return NotImplemented

        return self.__set == other.__set

    def __ne__(self, other):
        return not self == other

    def __hash__(self):
	return hash((self.__class__, tuple(map(tuple, self.__set))))

    def __len__(self):
	"""Return the number of ranges in the ip_set.
	"""
	return len(self.__set)


def ip_set_from_list(lst):
    """Create an ip_set containing a list of addresses/networks.
    """
    set = ip_set()
    for l in lst:
	set.append(l)
    return set

complete_network = network(0L, 0, DEMAND_FILTER)
complete_set = ip_set(complete_network)
broadcast_network = network('255.255.255.255', 32, DEMAND_FILTER)
broadcast_set = ip_set(broadcast_network)
loopback_network = network('127.0.0.1', 8, DEMAND_INTERFACE)
loopback_set = ip_set(loopback_network)
# All RFC1918 and link local networks.
private_set = ip_set_from_list([ network('10.0.0.0', 8),
				 network('172.16.0.0', 12),
				 network('192.168.0.0', 16),
				 network('169.254.0.0', 16) ])

def compute_neighbor(intrep, bits):
    xor_mask = intrep_with_bit_set(bits)
    and_mask = bits_to_intrep(bits)
    return (intrep ^ xor_mask) & and_mask


if __name__ == '__main__':
    # Test/demo code.  With no arguments, this will print a page
    # of data that can be useful when trying to interpret an
    # ipnumber/netmask pair.  With two arguments, it will print some
    # information about the IP address and netmask that was entered.

    import sys
    if len(sys.argv) == 1:
	print "Netmasks"
	print "========"
	for i in range(0, 17):
	    if i != 16:
		print '%2d' % i,
		print '%-13s' % netmask(i, DEMAND_NONE).netmask_str(),
	    else:
		print ' ' * 16,
	    print i + 16, '%-16s' % netmask(i + 16, DEMAND_NONE).netmask_str()
	print "\n"
	print "IP intervals"
	print "============"
	for i in range(9):
	    for j in range(0, 4):
		print '%2d' % (8*j + i),
	    print '%3d' % (netmask(i, DEMAND_NONE).intrep >> 24),
	    x = 0
	    need_break = 0
	    if i < 8:
		for j in range(0, 256, pow(2, 8-i)):
		    if need_break:
			print
			print ' ' * 15,
			need_break = 0
		    print '%3d-%-3d' % (j, j + pow(2, 8-i)-1),
		    x = x + 1
		    if x % 8 == 0:
			need_break = 1
	    else:
		print '0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13...',
	    print
	sys.exit(0)

    if len(sys.argv) != 3:
	sys.stderr.write("Usage: python ipaddr.py IP_ADDRESS NETMASK\n")
	sys.exit(1)
    nw = network(sys.argv[1], sys.argv[2], DEMAND_NONE)
    print nw
    print "IP address:       ", nw.ip.ip_str()
    print "Netmask:          ", nw.mask.netmask_str(),
    print " (/" + nw.mask.netmask_bits_str + ")"
    print "Network address:  ", nw.network_str()
    print "Broadcast address:", nw.broadcast_str()
