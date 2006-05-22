import StringIO
import sys
import unittest

from BitShifter import bitShift

# Hideously inefficient bitstream writer for proof of concept
# Much too slow for real use. :)

class BitWriter(object):
	def __init__(self, stream):
		self._stream = stream
		self._bitBuffer = 0 # remaining bits, at bottom (00000bbb)
		self._bitCount  = 0 # number of bits stored in buffer
	
	def writeBytes(self, data):
		"""Output a series of bytes"""
		if self._bitCount:
			shift = 8 - self._bitCount
			(overflow, shifted) = bitShift(data, shift)
			
			# Write out the first byte
			byte = (self._bitBuffer << shift) + overflow
			self._stream.write(chr(byte))
			
			# Write out the main body
			self._stream.write(shifted[:-1])
			
			# Save the final bits for later
			# bbb00000 -> 00000bbb [bitcount=3]
			newBits = ord(shifted[-1]) >> shift
			assert newBits >= 0 and newBits <= (2 << self._bitCount)
			self._bitBuffer = newBits
		else:
			# Already byte-aligned, convenient!
			self._stream.write(data)
	
	def writeBits(self, bits, count):
		"""Write out a sub-byte bit sequence."""
		assert count >= 0 and count < 8
		assert bits >= 0 and bits <= (2 << count)
		
		# 'b' = self._bitBuffer, stored from last time:
		#   00000bbb
		# 'B' = bits, new stuff
		#   000000BB
		# out: newBits=
		#   000bbbBB

		newBits = (self._bitBuffer << count) + bits
		newCount = self._bitCount + count
		
		# 000000bb bbBBBBBB <- could be two bytes
		#   bbbbBBBB <- write
		#   000000BB <- keep
		if newCount >= 8:
			# Take a byte off the top and write it out
			remainder = newCount % 8
			byte = newBits >> remainder
			self._stream.write(chr(byte))
			
			mask = 0xff >> (8 - remainder)
			newBits = newBits & mask
			newCount = remainder
		
		assert newCount >= 0 and newCount < 8
		assert newBits >= 0 and newBits <= (2 << newCount)
		
		self._bitBuffer = newBits
		self._bitCount = newCount
		
	
	def flush(self):
		"""Kaboom"""
		if self._bitCount:
			self._stream.write(chr(self._bitBuffer << (8 - self._bitCount)))
			self._bitCount = 0
			self._bitBuffer = 0
		self._stream.flush()

class WriterTest(unittest.TestCase):
	def testStuff(self):
		buffer = StringIO.StringIO()
		stream = BitWriter(buffer)
		stream.writeBytes("abc")
		stream.writeBits(0x01, 1)
		stream.writeBytes("xyz")
		stream.flush()
		self.assertEqual(buffer.getvalue(), "abc\xbc\x3c\xbd\x00")
		# 1011 1100  0011 1100  1011 1101  0
		
if __name__ == "__main__":
	unittest.main()
