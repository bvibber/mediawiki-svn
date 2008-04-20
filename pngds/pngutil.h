#ifndef	_PNGUTIL_H
#define _PNGUTIL_H	1
#include <stdint.h>

void png_die(char *msg, void *data);
void png_read_int(uint32_t *ptr, FILE *stream, uint32_t *crc);
unsigned int png_fread(void *ptr, unsigned int size, FILE *stream, uint32_t *crc);
void png_open_streams(void **options, FILE **in, FILE **out);
void png_write_int(uint32_t value, FILE *stream, uint32_t *crc);
unsigned int png_fwrite(void *ptr, unsigned int size, FILE *stream, uint32_t *crc);

#endif
