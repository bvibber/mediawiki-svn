import struct
import sys

from BitShifter import bitShift

for filename in sys.argv[1:]:
	f = file(filename)
	
	# Stream trailer is 80 bits, but may not be byte-aligned.
	f.seek(-11, 2)
	trail = f.read(11)
	
	found = False
	
	for offset in range(0, 8):
		(overflow, shifted) = bitShift(trail, offset)
		if shifted[0:6] == "\x17\x72\x45\x38\x50\x90":
			found = True
			crc = struct.unpack(">L", shifted[6:10])[0]
			print "%s: trail offset %d bits, CRC 0x%08x" % (filename, offset, crc)
			break
	
	if not found:
		print "%s: no bzip2 trail signature" % filename
