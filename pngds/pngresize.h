#include "pngreader.h"

typedef struct
{
	uint32_t width;
	uint32_t height;
	float fx;
	float fy;
	
	uint32_t line_count;
	unsigned char **scanlines;
	
	uint32_t written_lines;
	unsigned char *last_line;
	
	pngcallbacks *callbacks;
} pngresize;

void png_resize(FILE* fin, FILE* fout, uint32_t width, uint32_t height, pngcallbacks* callbacks, void *extra2);
