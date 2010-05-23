#include <stdlib.h>
#include <stdio.h>
#include <string.h>
#include <errno.h>

#include "zlib.h"
#include "pngutil.h"
#include "pngcmd.h"

void png_die(char *msg, void *data)
{
	if (strcmp(msg, "unknown_filter") == 0)
		fprintf(stderr, "%s: %i\n", msg, (int)(*((unsigned char*)data)));
	else
		fprintf(stderr, "%s\n", msg);
	exit(1);
}

void png_die_type(char *msg, char *type)
{
	fprintf(stderr, "%s: %.4s\n", msg, type);
	exit(1);
}

uint32_t png_read_int(FILE *stream, uint32_t *crc)
{
	unsigned char bytes[4];
	png_fread(bytes, 4, stream, crc);
	return (((((bytes[0] << 8) | bytes[1]) << 8) | bytes[2]) << 8) | bytes[3];
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

void png_open_streams(const struct pngopts *opts, FILE **in, FILE **out)
{
	if (!opts->stdin && (opts->input_filename == NULL))
		pngcmd_die("input unspecified", NULL);
	if (!opts->stdout && (opts->output_filename == NULL))
		pngcmd_die("output unspecified", NULL);
	
	if (opts->stdin) {
		*in = stdin;
	} else {
		*in = fopen(opts->input_filename, "rb");
		if (!*in) {
			fprintf(stderr, "Couldn't open input filename '%s' (%s)\n", opts->input_filename, strerror(errno));
			exit(2);
		}		
	}
	
	if (opts->stdout) {
		*out = stdout;
	} else {
		*out = fopen(opts->output_filename, "wb");
		if (!*out) {
			fprintf(stderr, "Couldn't open output filename '%s' (%s)\n", opts->output_filename, strerror(errno));
			exit(2);
		}	
	}
}

/* pngds code doesn't check malloc() return value, so we exit here if malloc returned NULL
 * In case you wanted to profile memory usage, note that the program also calls the real
 * malloc via calloc() and zlib.
 */
void* xmalloc(size_t size) {
	void* v = malloc(size);
	if (!v) {
		fprintf(stderr, "Couldn't reserve %zu bytes\n", size);
		exit(12);
	}
	return v;
}
