import socket
import struct

#
# Packet format:
# [atom] 4-byte ASCII token identifier
# [len ] 32-bit big-endian integer with size of remaining data. Must be <2GB
# [data] variable length depending on len. if zero, not present.
#
# Client sends:
# COMP <version>
#   Requests remote compression. Version is 32-bit big-endian integer,
#   should be 2. Unsupported version on the server should drop or return
#   error.
# ALGO <algorithm>
#   "bzip2": Create a full bzip2 stream. Default block size is 900k.
# BLOK <size in 100k>
#   Optional; "1" through "9" to select bzip2 block size.
# HUGE <data>
#   Uncompressed input data. For each HUGE atom sent by the client, the
#   server will return a SMAL atom containing compressed data. You may
#   issue multiple such requests as long as the connection remains open;
#   the last set ALGO and BLOK settings continue to apply.
#   Multiple requests may be pipelined if the sides support it, but this
#   is not required; both clients and servers may block on each request.
# SMAL <data>
#   Compressed input data. For each SMAL atom sent by the client, the
#   server will return a HUGE atom containing uncompressed data. You may
#   issue multiple such requests, and can interleave SMAL and HUGE requests.
# CLOS <no data>
#   Close the connection. (Optional?)
#
# Server sends back one of:
# SMAL <data>
#   Compressed output data.
# HUGE <data>
#   Uncompressed output data.
# -or-
# EROR <error string>
#   Some error condition which can be reported gracefully.
#

class Connection(object):
	def __init__(self, socket):
		self.stream = socket.makefile("rw")
	
	def close(self):
		self.stream.close()
		self.stream = None
	
	def send(self, atom, data=""):
		assert len(atom) == 4
		
		header = struct.pack(">4sl", atom, len(data))
		assert len(header) == 8
		#header = "%4s%08xd" % (atom, len(data))
		#assert len(header) == 12
		
		self.stream.write(header)
		if len(data):
			self.stream.write(data)
		self.stream.flush()
	
	def receive(self):
		header = self.stream.read(8)
		#print "Read: '%s'" % header
		
		if header == "":
			# Connection closed
			return (None, None)
		
		assert len(header) == 8
		
		(atom, size) = struct.unpack(">4sl", header)
		#atom = header[0:4]
		#size = int(header[4:12], 16)
		assert len(atom) == 4
		assert size >= 0
		
		if size > 0:
			data = self.stream.read(size)
		else:
			data = ""
		assert len(data) == size
		
		return (atom, data)
	
	def isOpen(self):
		return self.stream is not None
