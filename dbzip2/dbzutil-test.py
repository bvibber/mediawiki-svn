import StringIO
import unittest

import dbzutil

class WriterTest(unittest.TestCase):
	def testStuff(self):
		buffer = StringIO.StringIO()
		stream = dbzutil.Bitstream(buffer)
		stream.write("abc")
		stream.write("\x80", 1)
		stream.write("xyz", 24)
		stream.flush()
		self.assertEqual(buffer.getvalue(), "abc\xbc\x3c\xbd\x00")
		# 1011 1100  0011 1100  1011 1101  0
		
if __name__ == "__main__":
	unittest.main()
