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
	unsigned int width;
	unsigned int height;
	unsigned char bitdepth;
	unsigned char colortype;
	unsigned char compression;
	unsigned char filter_method;
	unsigned char interlace;
} pngheader;

typedef struct
{
	unsigned int length;
	char[4] type;
} chunkheader;

typedef struct
{
	unsigned char r;
	unsigned char g;
	unsigned char b;
} rgbcolor;

typedef struct
{
	pngheader *header;
	
	unsigned char bytedepth;
	unsigned char bpp;
	rgbcolor **palette;
	
	z_stream zst;
	
	unsigned char expect_filter;
	unsigned char filter;
	int scan_pos;
	unsigned char *previous_scanline;
	unsigned char *current_scanline;
	
	FILE *fin;
	FILE *fout;
	
	void *extra1;
} pngreader;

typedef struct
{
	unsigned int width;
	unsigned int height;
	double fx;
	double fy;
} pngresize;