#include <stdlib.h>
#include <stdio.h>
#include <string.h>

#include "pngreader.h"
#include "pngutil.h"
#include "pngcmd.h"
#include "pngresize.h"

#define paranoia_ceil(d)	(uint32_t)d + ((uint32_t)d < d ? 1 : 0)
#define roundl(d)			((uint32_t)d + ((d - (uint32_t)d) >= 0.5 ? 1 : 0))

void png_resize_init(void *info_);
void png_resize_line(unsigned char *scanline, unsigned char *previous_scanline, uint32_t length, void *info_);
void png_resize_done(void *info_);

void png_resize(FILE* fin, FILE* fout, uint32_t width, uint32_t height, pngcallbacks* callbacks, void* extra2)
{
	pngresize info;
	info.width = width;
	info.height = height;
	info.line_count = 0;
	
	if (width == 0 && height == 0)
		png_die("unspecified_dimensions", NULL);
	
	if (callbacks == NULL)
		info.callbacks = calloc(sizeof(pngcallbacks), 1);
	else
		info.callbacks = callbacks;
	callbacks = malloc(sizeof(pngcallbacks));
	callbacks->completed_scanline = &png_resize_line;
	callbacks->read_header = &png_resize_init;
	callbacks->done = &png_resize_done;
	
	if (info.callbacks->completed_scanline == NULL)
		info.callbacks->completed_scanline = &png_write_scanline_raw;
	
	png_read(fin, fout, callbacks, &info, extra2);
	free(callbacks);
	// Need to free info.scanlines, but don't know its length
	free(info.last_line);
}

void png_resize_init(void *info_)
{
	pngreader *info = (pngreader*)info_;
	pngresize *rinfo = (pngresize*)info->extra1;
	uint32_t max_line_count;
	unsigned int i;
	
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
		rinfo->height = roundl((float)(info->header->height / rinfo->fy));
	}
	else if (rinfo->height != 0)
	{
		rinfo->fx = rinfo->fy = (float)info->header->height / (float)rinfo->height;
		rinfo->width = roundl((float)(info->header->width / rinfo->fy));
	}
	
	if (rinfo->fx < 1.0 || rinfo->fy < 1.0)
		png_die("upscaling_unsupported", NULL);
	
	max_line_count = (unsigned int)rinfo->fy;
	if (rinfo->fy > max_line_count) max_line_count++;
	
	rinfo->scanlines = malloc(max_line_count * sizeof(char*));
	for (i = 0; i < max_line_count; i++) 
		rinfo->scanlines[i] = malloc(rinfo->width * info->bpp * sizeof(char));
	rinfo->line_count = 0;
	
	rinfo->written_lines = 0;
	rinfo->last_line = calloc(rinfo->width * info->bpp, sizeof(char));
	
	if (rinfo->callbacks->read_header != NULL)
		(*rinfo->callbacks->read_header)(info);
}

void png_resize_line(unsigned char *scanline, unsigned char *previous_scanline, 
	uint32_t length, void *info_)
{
	pngreader *info = (pngreader*)info_;
	pngresize *rinfo = (pngresize*)info->extra1;
	
	uint32_t i, j, k, start, end;
	
	float divisor;
	
	for (i = 0; i < rinfo->width; i++)
	{
		unsigned char pixel;

		// TODO: Check whether ceil() is suitable
		start = paranoia_ceil(rinfo->fx * i);
		end = paranoia_ceil(rinfo->fx * (i + 1));
		divisor = (float)(end - start);
		
		for (j = 0; j < info->bpp; j++)
		{
			pixel = 0;
			for (k = 0; k < end - start; k++)
				pixel += (unsigned char)(scanline[(start + k) * info->bpp + j] / divisor);
			rinfo->scanlines[rinfo->line_count][i * info->bpp + j] = pixel;
		}
	}
	rinfo->line_count++;
	
	if ((info->line_count / rinfo->fy) > (rinfo->written_lines + 1))
	{
		unsigned char *scanline = calloc(rinfo->width, info->bpp);
		for (i = 0; i < rinfo->width * info->bpp; i++)
		{
			for (j = 0; j < rinfo->line_count; j++)
				scanline[i] += rinfo->scanlines[j][i] / rinfo->line_count;
		}
		rinfo->line_count = 0;
		(*rinfo->callbacks->completed_scanline)(scanline, 
			rinfo->last_line, rinfo->width * info->bpp, info);

		free(rinfo->last_line);
		rinfo->last_line = scanline;
		rinfo->written_lines++;
	}
}
	
void png_resize_done(void *info_)
{
	pngreader *info = (pngreader*)info_;
	pngresize *rinfo = (pngresize*)info->extra1;
	
	while (rinfo->written_lines < rinfo->height)
	{
		(*rinfo->callbacks->completed_scanline)(
			rinfo->last_line, rinfo->last_line, 
			rinfo->width * info->bpp, info);
		rinfo->written_lines++;
	}
	if (rinfo->callbacks->done != NULL)
		(*rinfo->callbacks->done)(info);
}

#ifdef PNGRESIZE
int main(int argc, char **argv)
{
	void **opts = pngcmd_getopts(argc, argv);
	FILE *in, *out;
	png_open_streams(opts, &in, &out);
	
	png_resize(in, out, *((uint32_t*)opts[PNGOPT_WIDTH]), 
		*((uint32_t*)opts[PNGOPT_HEIGHT]), NULL, NULL);
	
	fclose(in); fclose(out);
	
	return 0;
}

#endif
