#include <stdlib.h>
#include <stdio.h>
#include <string.h>

#include "pngreader.h"
#include "pngutil.h"
#include "pngcmd.h"
#include "pngresize.h"

#define paranoia_ceil(d)	(u_int32_t)d + (u_int32_t)d < d ? 1: 0;

void png_resize(FILE* fin, FILE* fout, u_int32_t width, u_int32_t height, pngcallbacks* callbacks);
void png_resize_init(void *info_);
void png_resize_line(unsigned char *scanline, unsigned char *previous_scanline, u_int32_t length, void *info_);
void png_resize_done(void *info_);

void png_resize(FILE* fin, FILE* fout, u_int32_t width, u_int32_t height, pngcallbacks* callbacks)
{
	pngresize info;
	info.width = width;
	info.height = height;
	info.line_count = 0;
	
	if (width == 0 && height == 0)
		png_die("unspecified_dimensions", NULL);

	
	if (callbacks == NULL)
	{
		callbacks = malloc(sizeof(callbacks));
	}
	callbacks->completed_scanline = &png_resize_line;
	callbacks->read_header = &png_resize_init;
	callbacks->done = &png_resize_done;
	
	png_read(fin, fout, callbacks, &info);
}

void png_resize_init(void *info_)
{
	pngreader *info = (pngreader*)info_;
	pngresize *rinfo = (pngresize*)info->extra1;
	
	if (info->header->colortype == COLOR_PALETTE)
		png_die("palette_resizing_unsupported", NULL);
	
	if (rinfo->width != 0 && rinfo->height != 0)
	{
		rinfo->fx = (float)info->header->width / (float)rinfo->width;
		rinfo->fy = (float)info->header->height / (float)rinfo->height;
	}		
	else if (rinfo->width != 0)
	{
		rinfo->fx = rinfo->fy = (float)info->header->width / (float)rinfo->width;
		rinfo->height = (u_int32_t)(info->header->height / rinfo->fy);
	}
	else if (rinfo->height != 0)
	{
		rinfo->fx = rinfo->fy = (float)info->header->height / (float)rinfo->height;
		rinfo->width = (u_int32_t)(info->header->width / rinfo->fy);
	}
	
	if (rinfo->fx < 1.0 || rinfo->fy < 1.0)
		png_die("upscaling_unsupported", NULL);
	
	u_int32_t max_line_count = (unsigned int)rinfo->fy;
	if (rinfo->fy > max_line_count) max_line_count++;
		
	unsigned int i;
	
	rinfo->scanlines = malloc(max_line_count * sizeof(char*));
	for (i = 0; i < max_line_count; i++) 
		rinfo->scanlines[i] = malloc(rinfo->width * info->bpp * sizeof(char));
	rinfo->line_count = 0;
	
	rinfo->written_lines = 0;
	rinfo->last_line = calloc(rinfo->width * info->bpp * sizeof(char), 1);
	
}

void png_resize_line(unsigned char *scanline, unsigned char *previous_scanline, 
	u_int32_t length, void *info_)
{
	pngreader *info = (pngreader*)info_;
	pngresize *rinfo = (pngresize*)info->extra1;
	
	u_int32_t i, j, k, start, end;
	
	float divisor;
	unsigned char pixel[info->bpp];
	
	for (i = 0; i < rinfo->width; i++)
	{
		// TODO: Check whether ceil() is suitable
		start = (u_int32_t)(rinfo->fx * i);
		if ((rinfo->fx * i) > start) start++;
		end = (u_int32_t)(rinfo->fx * (i + 1));
		if ((rinfo->fx * (i + 1)) > end) end++;
		divisor = end - start;
		
		memset(pixel, 0, info->bpp);
		for (j = 0; j < info->bpp; j++)
		{
			for (k = 0; k < end - start; k++)
				pixel[j] += (unsigned char)(scanline[(start + k) * info->bpp + j] / divisor);
			rinfo->scanlines[rinfo->line_count][i * info->bpp + j] = pixel[j];
		}
	}
	rinfo->line_count++;
	
	if ((info->line_count / rinfo->fy) > (rinfo->written_lines + 1))
	{
		unsigned char scanline[rinfo->width * info->bpp];
		memset(scanline, 0, rinfo->width * info->bpp);
		for (i = 0; i < rinfo->width * info->bpp; i++)
		{
			for (j = 0; j < rinfo->line_count; j++)
				scanline[i] += rinfo->scanlines[j][i] / rinfo->line_count;
		}
		rinfo->line_count = 0;
		png_write_scanline(scanline, rinfo->last_line, rinfo->width * info->bpp, info);
		memcpy(rinfo->last_line, scanline, rinfo->width * info->bpp);
		rinfo->written_lines++;
	}
}
	
void png_resize_done(void *info_)
{
	pngreader *info = (pngreader*)info_;
	pngresize *rinfo = (pngresize*)info->extra1;
	
	while (rinfo->written_lines < rinfo->height)
	{
		png_write_scanline(rinfo->last_line, rinfo->last_line, rinfo->width * info->bpp, info);
		rinfo->written_lines++;
	}
}

#ifdef PNGRESIZE
int main(int argc, char **argv)
{
	char **opts = pngcmd_getopts(argc, argv);
	FILE *in, *out;
	png_open_streams(opts, &in, &out);
	
	png_resize(in, out, *((u_int32_t*)opts[PNGOPT_WIDTH]), 
		*((u_int32_t*)opts[PNGOPT_HEIGHT]), NULL);
	
	fclose(in); fclose(out);
	
	return 0;
}

#endif
