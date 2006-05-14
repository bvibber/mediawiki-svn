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
#   should be 1. Unsupported version on the server should drop or return
#   error.
# ALGO <algorithm>
#   "bzip2": Create a full bzip2 stream. Use data size as block size.
# HUGE <data>
#   Uncompressed input data; always the last packet. After this, wait
#   for response. You may issue multiple such requests as long as the
#   connection remains open.
# CLOS <no data>
#   Close the connection. (Optional?)
#
# Server sends back one of:
# SMAL <data>
#   Compressed output data.
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
