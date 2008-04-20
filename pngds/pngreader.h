#ifndef _PNGREADER_H
#define _PNGREADER_H	1

#include <stdio.h>
#include <stdint.h>

#include "zlib.h"

/*
 * Defines
 */

#define COLOR_GRAY	0
#define COLOR_RGB	2
#define COLOR_PALETTE	3
#define COLOR_GRAYA	4
#define COLOR_RGBA	6

#define COMPRESS_DEFLATE	0
#define COMPRESS_FLAG_DEFLATE	8

#define FILTER_METHOD_BASIC_ADAPTIVE	0

#define FILTER_NONE	0
#define FILTER_SUB	1
#define FILTER_UP	2
#define FILTER_AVERAGE	3
#define FILTER_PAETH	4


/*
 * Types
 */

typedef struct
{
	uint32_t width;
	uint32_t height;
	unsigned char bitdepth;
	unsigned char colortype;
	unsigned char compression;
	unsigned char filter_method;
	unsigned char interlace;
} pngheader;

typedef struct
{
	uint32_t length;
	char *type;
} chunkheader;

typedef struct
{
	unsigned char r;
	unsigned char g;
	unsigned char b;
} rgbcolor;

typedef struct
{
	// Both last parameters are really pointer to pngreader
	void (*completed_scanline)(unsigned char*, unsigned char*, uint32_t, void*);
	void (*read_header)(void*); 
	void (*done)(void*);
} pngcallbacks;


typedef struct
{
	pngheader *header;
	
	uint32_t crc;
	
	unsigned char bytedepth;
	unsigned char bpp;
	rgbcolor **palette;
	
	z_stream zst;
	
	unsigned char expect_filter;
	unsigned char filter;
	
	uint32_t scan_pos;
	uint32_t line_count;
	unsigned char *previous_scanline;
	unsigned char *current_scanline;
	
	FILE *fin;
	FILE *fout;
	
	pngcallbacks *callbacks;
	
	void *extra1;
	void *extra2;
} pngreader;

/* 
 * Functions
 */
void png_read(FILE* fin, FILE* fout, pngcallbacks* callbacks, void* extra1, void *extra2);
void png_write_scanline_raw(unsigned char *scanline, unsigned char *previous_scanline, uint32_t length, void *info_);

#endif
