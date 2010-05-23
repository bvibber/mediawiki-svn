#include "pngreader.h"

struct pngresize
{
	uint32_t width;
	uint32_t height;
	float fx;
	float fy;
	
	uint32_t line_count;
	uint32_t max_line_count;
	unsigned char **scanlines;
	
	uint32_t written_lines;
	unsigned char *last_line;
	
	pngcallbacks *callbacks;
};

void png_resize(FILE* fin, FILE* fout, uint32_t width, uint32_t height, pngcallbacks* callbacks, void *extra2);
