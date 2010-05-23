#ifndef	_PNGUTIL_H
#define _PNGUTIL_H	1
#ifdef _MSC_VER
typedef unsigned __int32  uint32_t;
#else
#include <stdint.h>
#endif

struct pngopts;

void* xmalloc(size_t size);
void png_die(char *msg, void *data);
void png_die_type(char *msg, char *data);
uint32_t png_read_int(FILE *stream, uint32_t *crc);
unsigned int png_fread(void *ptr, unsigned int size, FILE *stream, uint32_t *crc);
void png_open_streams(const struct pngopts *opts, FILE **in, FILE **out);
void png_write_int(uint32_t value, FILE *stream, uint32_t *crc);
unsigned int png_fwrite(void *ptr, unsigned int size, FILE *stream, uint32_t *crc);

#endif
