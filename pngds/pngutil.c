#include <stdlib.h>
#include <stdio.h>
#include <string.h>

#include "zlib.h"
#include "pngutil.h"

void png_die(char *msg, void *data)
{
	if (strcmp(msg, "critical_chunk") == 0)
		fprintf(stderr, "%s: %.4s\n", msg, data);
	else if (strcmp(msg, "unknown_filter") == 0)
		fprintf(stderr, "%s: %i\n", msg, (int)(*((unsigned char*)data)));
	else
		fprintf(stderr, "%s\n", msg);
	exit(1);
}

void png_read_int(u_int32_t *ptr, FILE *stream, u_int32_t *crc)
{
	signed char i;
	*ptr = 0;
	for (i = 24; i >= 0; i -= 8)
	{
		unsigned char buf = 0;
		png_fread(&buf, 1, stream, crc);
		*ptr |= (((u_int32_t)buf) << i);
	}
}

unsigned int png_fread(void *ptr, unsigned int size, 
	FILE *stream, u_int32_t *crc)
{
	if (feof(stream))
		png_die("unexpected_eof", stream);
	if (fread(ptr, 1, size, stream) != size ||
			ferror(stream))
		png_die("read_error", stream);
#ifndef NO_CRC
	if (crc != NULL)
		*crc = crc32(*crc, ptr, size);
#endif
	return size;
}

