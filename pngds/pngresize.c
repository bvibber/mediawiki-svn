#include <stdlib.h>
#include <stdio.h>
#include <string.h>

#include "pngreader.h"
#include "pngutil.h"
#include "pngcmd.h"
#include "pngresize.h"

#define paranoia_ceil(d)	(uint32_t)d + ((uint32_t)d < d ? 1 : 0)
#define roundl(d)			((uint32_t)d + ((d - (uint32_t)d) >= 0.5 ? 1 : 0))

void png_resize_init(pngreader *info);
void png_resize_line(unsigned char *scanline, unsigned char *previous_scanline, uint32_t length, pngreader *info);
void png_resize_done(pngreader *info);

void png_resize(FILE* fin, FILE* fout, uint32_t width, uint32_t height, pngcallbacks* callbacks, void* extra2)
{
	pngcallbacks null_callbacks = { NULL, NULL, NULL };
	pngcallbacks reader_callbacks;	
	struct pngresize resize_info;
	resize_info.width = width;
	resize_info.height = height;
	resize_info.line_count = 0;
	
	if (width == 0 && height == 0)
		png_die("unspecified_dimensions", NULL);
	
	if (callbacks == NULL)
		resize_info.callbacks = &null_callbacks;
	else
		resize_info.callbacks = callbacks;

	reader_callbacks.completed_scanline = &png_resize_line;
	reader_callbacks.read_header = &png_resize_init;
	reader_callbacks.done = &png_resize_done;
	
	if (resize_info.callbacks->completed_scanline == NULL)
		resize_info.callbacks->completed_scanline = &png_write_scanline_raw;
	
	png_read(fin, fout, &reader_callbacks, &resize_info, extra2);
	
}

void png_resize_init(pngreader* info)
{
	struct pngresize *rinfo = info->extra1;
	unsigned int i;
	
	if (info->header->properties.colortype == COLOR_PALETTE)
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
	
	rinfo->max_line_count = (unsigned int)rinfo->fy;
	if (rinfo->fy > rinfo->max_line_count) rinfo->max_line_count++;
	
	rinfo->scanlines = xmalloc(rinfo->max_line_count * sizeof(char*));
	for (i = 0; i < rinfo->max_line_count; i++) 
		rinfo->scanlines[i] = xmalloc(rinfo->width * info->bpp * sizeof(char));
	rinfo->line_count = 0;
	
	rinfo->written_lines = 0;
	rinfo->last_line = calloc(rinfo->width * info->bpp, sizeof(char));
	if (!rinfo->last_line) png_die("insufficient_memory", NULL);
	
	if (rinfo->callbacks->read_header != NULL)
		(*rinfo->callbacks->read_header)(info);
}

void png_resize_line(unsigned char *scanline, unsigned char *previous_scanline, 
	uint32_t length, pngreader *info)
{
	struct pngresize *rinfo = info->extra1;
	
	uint32_t i, j, k, start, end;
	
	float divisor;
	
	if (rinfo->line_count >= rinfo->max_line_count)
		png_die("line_count_exceeds_max", NULL);
		
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
		if (!scanline) png_die("insufficient_memory", NULL);
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
	
void png_resize_done(pngreader* info)
{
	unsigned int i;
	struct pngresize* rinfo = info->extra1;
	
	while (rinfo->written_lines < rinfo->height)
	{
		(*rinfo->callbacks->completed_scanline)(
			rinfo->last_line, rinfo->last_line, 
			rinfo->width * info->bpp, info);
		rinfo->written_lines++;
	}
	if (rinfo->callbacks->done != NULL)
		(*rinfo->callbacks->done)(info);
		
	for (i=0; i < rinfo->max_line_count; i++)
		free(rinfo->scanlines[i]);

	free(rinfo->scanlines);
	free(rinfo->last_line);
}

#ifdef PNGRESIZE
int main(int argc, char **argv)
{
	FILE *in, *out;
	struct pngopts opts;
	
	pngcmd_getopts(&opts, argc, argv);
	png_open_streams(&opts, &in, &out);
	
	png_resize(in, out, opts.width, opts.height, NULL, NULL);
	
	fclose(in); fclose(out);
	return 0;
}

#endif
