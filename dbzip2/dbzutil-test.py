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

class ReadblockTest(unittest.TestCase):
	def testReadblock(self):
		cases = [
			("abcdef", ["abcdef"]),
			("abbcccddd", ["abbcccddd"]),
			("abbcccdddeeeefffff", ["abbcccdddeeeefffff"]),
			("a"*259, ["a"*259]),
			("a"*259+"b", ["a"*259+"b"]),
			("a"*260+"b", ["a"*260+"b"]),
			("a"*900000+"b", ["a"*900000+"b"]),
			("aaabbb"*150000, ["aaabbb"*149997, "aaabbb"*3]),
			("aaaabbbb"*90000, ["aaaabbbb"*89998 + "aaaa", "bbbbaaaabbbb"]),
			("aaabbb"*149996 + "abcdefghijklmnopqrstuvwxyz",
				["aaabbb"*149996 + "abcde", "fghijklmnopqrstuvwxyz"]),
			];
		for (input, blocks) in cases:
			chunks = []
			def callback(chunk):
				chunks.append(chunk)
			stream = StringIO.StringIO(input)
			dbzutil.readblock(stream, callback)
			
			blockSizes = map(len, blocks)
			chunkSizes = map(len, chunks)
			self.assertEqual(blockSizes, chunkSizes)
			self.assertEqual(blocks, chunks)

if __name__ == "__main__":
	unittest.main()
