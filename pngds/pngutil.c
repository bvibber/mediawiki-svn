#include <stdlib.h>
#include <stdio.h>
#include <string.h>

#include "zlib.h"
#include "pngutil.h"
#include "pngcmd.h"

// TODO: support other architectures
#define SWAP_BYTES(x) ((((x) & 0x000000FF) << 24) | (((x) & 0x0000FF00) << 8) | (((x) & 0x00FF0000) >> 8) | ((x) >> 24))

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

uint32_t png_read_int(FILE *stream, uint32_t *crc)
{
	uint32_t result = 0;
	png_fread(&result, 4, stream, crc);
	return SWAP_BYTES(result);
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
	return size;
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

