#include <stdio.h>

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
	u_int32_t width;
	u_int32_t height;
	unsigned char bitdepth;
	unsigned char colortype;
	unsigned char compression;
	unsigned char filter_method;
	unsigned char interlace;
} __attribute__ ((packed)) pngheader;

typedef struct
{
	u_int32_t length;
	char *type;
} chunkheader;

typedef struct
{
	unsigned char r;
	unsigned char g;
	unsigned char b;
} __attribute__ ((packed)) rgbcolor;

typedef struct
{
	pngheader *header;
	
	unsigned char bytedepth;
	unsigned char bpp;
	rgbcolor **palette;
	
	z_stream zst;
	
	unsigned char expect_filter;
	unsigned char filter;
	u_int32_t scan_pos;
	unsigned char *previous_scanline;
	unsigned char *current_scanline;
	
	FILE *fin;
	FILE *fout;
	
	void *callbacks;
	
	void *extra1;
} pngreader;

typedef struct
{
	u_int32_t width;
	u_int32_t height;
	double fx;
	double fy;
} pngresize;

/* 
 * Functions
 */
void png_read(FILE* fin, FILE* fout, void* callbacks);
void png_die(char *msg, void *data);
void png_read_int(u_int32_t *ptr, FILE* stream);
void png_write_scanline(unsigned char *scanline, unsigned char *previous_scanline, u_int32_t length, pngreader *info);
