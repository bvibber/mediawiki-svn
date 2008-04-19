#include "pngreader.h"

typedef struct
{
	u_int32_t width;
	u_int32_t height;
	float fx;
	float fy;
	
	u_int32_t line_count;
	unsigned char **scanlines;
	
	u_int32_t written_lines;
	unsigned char *last_line;
	
	pngcallbacks *callbacks;
} pngresize;
