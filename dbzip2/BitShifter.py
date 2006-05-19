import array
import unittest

def bitShift(data, nbits):
	"""Shift a byte array over N bits. Returns a tuple with the overflowed
	bits from the front followed by a byte string of the rest, zero-padded
	at the end."""
	
	if nbits == 0:
		return (0, data)
	if nbits > 7:
		raise Exception("Silly user, you're shifting more than 7 bits!")
	
	out = array.array("B", data)
	last = len(data) - 1
	shift = 8 - nbits
	
	overflow = (out[0] >> shift)
	
	for i in xrange(0, last):
		a = (out[i] << nbits) & 0xff
		b = (out[i+1] >> shift)
		out[i] = a + b
	
	out[last] = (out[last] << nbits) & 0xff
	
	return (overflow, out.tostring())


class BitShiftTest(unittest.TestCase):
	def testShifting(self):
		"""Shifts should work!"""
		cases = [
			#            0110 0001  0110 0010  0110 0011  0110 0100
			(0, "abcd", (0x00, "abcd")),
			
			#        0 / 1100 0010  1100 0100  1100 0110  1100 100.
			(1, "abcd", (0x00, "\xc2\xc4\xc6\xc8")),
			
			#       01 / 1000 0101  1000 1001  1000 1101  1001 00..
			(2, "abcd", (0x01, "\x85\x89\x8d\x90")),
			
			#      011 / 0000 1011  0001 0011  0001 1011  0010 0...
			(3, "abcd", (0x03, "\x0b\x13\x1b\x20")),
			
			#     0110 / 0001 0110  0010 0110  0011 0110  0100 ....
			(4, "abcd", (0x06, "\x16\x26\x36\x40")),
			
			#   0 1100 / 0010 1100  0100 1100  0110 1100  100. ....
			(5, "abcd", (0x0c, "\x2c\x4c\x6c\x80")),
			
			#  01 1000 / 0101 1000  1001 1000  1101 1001  00.. ....
			(6, "abcd", (0x18, "\x58\x98\xd9\x00")),
			
			# 011 0000 / 1011 0001  0011 0001  1011 0010  0... ....
			(7, "abcd", (0x30, "\xb1\x31\xb2\x00"))
		]
		for (nbits, data, (overflow, bytes)) in cases:
			(outOverflow, outBytes) = bitShift(data, nbits)
			self.assertEqual(overflow, outOverflow)
			self.assertEqual(bytes, outBytes)


if __name__ == "__main__":
	unittest.main()
