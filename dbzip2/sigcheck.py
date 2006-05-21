import struct
import sys

from BitShifter import bitShift

def findBzTrail(data):
	"""Look for a bzip2 stream trail marker in order to locate the end of
	the compressed data block.
	
	Returns a tuple with the number of bits left at the end of the last
	byte of the block data, the contents bits (right-aligned), and the
	stream's CRC32."""

	# Stream trailer is 80 bits, but may not be byte-aligned.
	assert len(data) > 11
	
	for offset in range(0, 8):
		if offset:
			trail = data[-11:]
			(overflow, shifted) = bitShift(data[-11:], offset)
		else:
			overflow = 0
			shifted = data[-10:]
		if shifted[0:6] == "\x17\x72\x45\x38\x50\x90":
			# BBBttttt t....$
			# \./ <- 'offset' is the count, 'overflow' contains the bits
			crc = struct.unpack(">L", shifted[6:10])[0]
			return (offset, overflow, crc)
	
	raise Exception("No bzip2 trail found in block.")

if __name__ == "__main__":
	for filename in sys.argv[1:]:
		f = file(filename)
		
		f.seek(-20, 2)
		trail = f.read(20)
		
		try:
			(offset, overflow, crc) = findBzTrail(trail)
			print "%s: trail offset %d bits, CRC 0x%08x" % (filename, offset, crc)
		except:
			print "%s: no bzip2 trail signature" % filename
