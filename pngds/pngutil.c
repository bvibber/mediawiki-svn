#include <stdlib.h>
#include <stdio.h>
#include <string.h>

#include "zlib.h"
#include "pngutil.h"
#include "pngcmd.h"

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

void png_read_int(uint32_t *ptr, FILE *stream, uint32_t *crc)
{
	signed char i;
	*ptr = 0;
	for (i = 24; i >= 0; i -= 8)
	{
		unsigned char buf = 0;
		png_fread(&buf, 1, stream, crc);
		*ptr |= (((uint32_t)buf) << i);
	}
}

unsigned int png_fread(void *ptr, unsigned int size, 
	FILE *stream, uint32_t *crc)
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

unsigned int png_fwrite(void *ptr, unsigned int size,
	FILE *stream, uint32_t *crc)
{
	if (size == 0) return 0;
	
	if (fwrite(ptr, 1, size, stream) != size ||
			ferror(stream))
		png_die("write_error", stream);
	if (crc != NULL)
		*crc = crc32(*crc, ptr, size);
}
void png_write_int(uint32_t value, FILE *stream, uint32_t *crc)
{
	signed char i;
	for (i = 3; i >= 0; i--)
		png_fwrite((char*)(&value) + i, 1, stream, crc);
}

void png_open_streams(void **opts, FILE **in, FILE **out)
{
	if (!*((char*)opts[PNGOPT_STDIN]) && opts[PNGOPT_IN] == NULL)
		pngcmd_die("input unspecified", NULL);
	if (!*((char*)opts[PNGOPT_STDOUT]) && opts[PNGOPT_OUT] == NULL)
		pngcmd_die("output unspecified", NULL);
	
	if (*((char*)opts[PNGOPT_STDIN]))
		*in = stdin;
	else
		*in = fopen((char*)opts[PNGOPT_IN], "rb");
	
	if (*((char*)opts[PNGOPT_STDOUT]))
		*out = stdout;
	else
		*out = fopen((char*)opts[PNGOPT_OUT], "wb");
}

