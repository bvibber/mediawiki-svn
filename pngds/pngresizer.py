import math

from pngreader import *

class PNGResizer(PNGReader):
	def __init__(self, stream, width, height, out = None):
		PNGReader.__init__(self, stream)
		self.resize_width = width
		self.resize_height = height
		
		self.current_scanline = 0
		self.written_lines = 0
		self.scanlines = []
		self.last_line = ''
		
		if out:
			self.out = out
		else:
			self.out = StringIO()
	
	def read_chunk(self):
		has_data = PNGReader.read_chunk(self)
		if not has_data:
			while self.written_lines < self.resize_height:
				self.out.write(self.last_line)
				self.written_lines += 1
		return has_data
	
	def read_header(self, length):
		PNGReader.read_header(self, length)
		
		self.fx = float(self.width) / self.resize_width
		self.fy = float(self.height) / self.resize_height
		if self.fx < 1.0 or self.fy < 1.0:
			raise ValueError('Upsampling unsupported')
			
	def completed_scanline(self):
		pixels = tuple(self.read_scanline())
		
		current_scanline = []
		for i in xrange(self.resize_width):
			start = int(math.ceil(self.fx * i))
			end = int(math.ceil(self.fx * (i + 1)))
			divisor = end - start
			
			new_pixel = [0] * COLOR_DEPTH[self.colortype]
			for j in xrange(len(new_pixel)):
				for k in xrange(end - start):
					new_pixel[j] += ord(pixels[start + k][j]) / divisor
				new_pixel[j] = chr(new_pixel[j])
			
			#current_scanline.append(''.join(new_pixel))
			current_scanline.extend(new_pixel)
			
		self.scanlines.append(current_scanline)
		self.current_scanline += 1
		if (self.current_scanline / self.fy) > (self.written_lines +  1):
			line = [0] * len(current_scanline)
			for i in xrange(len(line)):
				for j in xrange(len(self.scanlines)):
					line[i] += ord(self.scanlines[j][i]) / len(self.scanlines)
				line[i] = chr(line[i])
			self.last_line = ''.join(line)
			
			self.out.write(self.last_line)
			self.scanlines = []
			self.written_lines += 1
		
def test(filename):
	reader = PNGResizer(open(filename, 'rb'), 32, 32)
	reader.parse()
	reader.scanline = reader.previous_scanline = None
	print reader.__dict__
	return reader
