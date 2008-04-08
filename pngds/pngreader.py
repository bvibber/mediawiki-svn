"""
	PNGReader - Copyright 2008 - Bryan Tong Minh
	Licensed under the GNU Public License v2 or above
"""

import struct
import zlib
from cStringIO import StringIO

COLOR_GRAY, COLOR_RGB, COLOR_PALETTE = (0, 2, 3)
COLOR_GRAYA, COLOR_RGBA = (4, 6)
COLOR_DEPTH = {
	COLOR_GRAY: 1,
	COLOR_RGB: 3,
	COLOR_PALETTE: 1,
	COLOR_GRAYA: 2,
	COLOR_RGBA: 4,
}

COMPRESS_DEFLATE = 0
COMPRESS_FLAG_DEFLATE = 8

FILTER_METHOD_BASIC_ADAPTIVE = 0

FILTER_NONE, FILTER_SUB, FILTER_UP = (0, 1, 2)
FILTER_AVERAGE, FILTER_PAETH = (3, 4)

class PNGReader(object):
	def __init__(self, stream, out = None):
		self.stream = stream
		self.previous_scanline = None
		self.scanline = []
		self.expect_filter = True
		self.chunks = []
		
		if out:
			self.out = out
		else:
			self.out = StringIO()
		
	def parse(self):
		header = self.stream.read(8)
		if header != '\x89PNG\r\n\x1a\n':
			raise ValueError('Invalid header', header)
		
		while self.read_chunk():
			pass
		self.out.close()
			
	def read_chunk(self):
		length = struct.unpack('!L', self.stream.read(4))[0]
		chunk_type = self.stream.read(4)
		ancillary = ord(chunk_type[0]) & 32
		private = ord(chunk_type[1]) & 32
		
		self.chunks.append(chunk_type)
		
		if chunk_type == 'IHDR':
			self.read_header(length)
		elif chunk_type == 'PLTE':
			self.read_palette(length)
		elif chunk_type == 'IDAT':
			self.read_data(length)
		elif ancillary:
			self.stream.read(length)
		elif chunk_type != 'IEND':
			raise ValueError('Unrecognized critical chunk', chunk_type)
			
		crc = self.stream.read(4)
		
		if chunk_type == 'IEND':
			return False
		else:
			return True
	
	def read_header(self, length):
		self.width, self.height = struct.unpack('!LL', self.stream.read(8))
		self.bitdepth, self.colortype = struct.unpack('!BB', self.stream.read(2))
		
		self.compression, self.filter_method, self.interlace = struct.unpack('!BBB', self.stream.read(3))
		
		if self.compression != COMPRESS_DEFLATE:
			raise ValueError('Unknown compressor', self.compression)
		if self.filter_method != FILTER_METHOD_BASIC_ADAPTIVE:
			raise ValueError('Unknown filter method', self.filtermethod)
		if self.interlace:
			raise ValueError('Interlacing is unsupported')
			
		self.decompress = zlib.decompressobj()
		
		self.bytedepth = self.bitdepth / 8
		if self.bitdepth % 8: self.bytedepth += 1
		self.bpp = COLOR_DEPTH[self.colortype] * self.bytedepth
		
		self.previous_scanline = [0] * self.bpp * self.width
		
	def read_palette(self, length):
		self.palette = []
		for i in xrange(length / 3):
			self.palette.append(struct.unpack('!BBB', self.stream.read(3)))
		
	def read_data(self, length):
		left = length
		while left:
			size = min(4096, left)
			data = self.stream.read(size)
			left -= size
			
			self.defilter(self.decompress.decompress(data))
			
	def defilter(self, data):
		for byte in data:
			byte = ord(byte)
			if self.expect_filter:
				self.filter = byte
				self.expect_filter = False
				continue
			
			if self.filter == FILTER_NONE:
				pass
			elif self.filter == FILTER_SUB:
				i = len(self.scanline) - self.bpp
				if i >= 0: byte += self.scanline[i]
			elif self.filter == FILTER_UP:
				i = len(self.scanline)
				byte += self.previous_scanline[i]
			elif self.filter == FILTER_AVERAGE:
				i = len(self.scanline) - self.bpp
				j = len(self.scanline)
				if i >= 0:
					byte += (self.scanline[i] + 
						self.previous_scanline[j]) / 2
				else:
					byte += self.previous_scanline[j] / 2
			elif self.filter == FILTER_PAETH:
				i = len(self.scanline) - self.bpp
				j = len(self.scanline)
				b = self.previous_scanline[j]
				if i >= 0:
					a = self.scanline[i]
					c = self.previous_scanline[i]
				else:
					a = c = 0
				p = a + b - c
				pa = abs(p - a)
				pb = abs(p - b)
				pc = abs(p - c)
				if pa <= pb and pa <= pc: byte += a
				elif pb <= pc: byte += b
				else: byte += c
			else:
				raise ValueError('Unknown filter', self.filter)
					
			byte %= 256
			self.scanline.append(byte)
			
			if (len(self.scanline) % self.bpp == 0) and \
					(len(self.scanline) / self.bpp) == self.width:
				self.previous_scanline = self.scanline
				self.completed_scanline()
				self.scanline = []
				self.expect_filter = filter
		
	def read_scanline(self):
		scanline = self.scanline[:]
		while scanline:
			yield [''.join((chr(scanline.pop(0)) for i in xrange(self.bytedepth))) 
				for j in xrange(COLOR_DEPTH[self.colortype])]
	
	def completed_scanline(self):
		# Override this in a subclass
		for pixel in self.read_scanline():
			self.out.write(''.join(pixel))


def test(filename):
	reader = PNGReader(open(filename, 'rb'))
	try:
		reader.parse()
		reader.scanline = reader.previous_scanline = None
		print reader.__dict__
	finally:
		return reader