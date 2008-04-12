#include <stdlib.h>
#include <stdio.h>
#include <string.h>

#include "zlib.h"

#include "pngreader.h"
#include "pngcmd.h"

#define BUFFER_IN_SIZE	32768
#define BUFFER_OUT_SIZE	131072

int png_read_chunk(pngreader *info);
void png_read_header(pngreader *info, u_int32_t length);
void png_read_palette(pngreader *info, u_int32_t length);
void png_read_data(pngreader *info, u_int32_t length);
void png_defilter(pngreader *info, unsigned char *buffer, u_int32_t size);
void png_read_ancillary(pngreader *info, u_int32_t length);

void png_read(FILE* fin, FILE* fout, pngcallbacks* callbacks, void* extra1)
{
	pngreader info;
	
	info.fin = fin;
	info.fout = fout;
	if (callbacks == NULL)
	{
		callbacks = malloc(sizeof(pngcallbacks));
		callbacks->completed_scanline = NULL;
		callbacks->read_header = NULL;
		callbacks->done = NULL;
	}
	if (callbacks->completed_scanline == NULL)
		callbacks->completed_scanline = &png_write_scanline;
	info.callbacks = callbacks;
	
	info.extra1 = extra1;
	
	char header[8];
	fread(header, 1, 8, fin);
	if (strncmp(header, "\x89PNG\r\n\x1a\n", 8))
		png_die("header", header);
	
	while (png_read_chunk(&info));
		
	if (callbacks->done != NULL)
		(*callbacks->done)(&info);
}

void png_die(char *msg, void *data)
{
	if (strcmp(msg, "critical_chunk") == 0)
		fprintf(stderr, "%s: %.4s\n", msg, data);
	else if (strcmp(msg, "unknown_filter") == 0)
		fprintf(stderr, "%s: %i\n", msg, (int)(*((unsigned char*)data)));
	else
		fprintf(stderr, "%s\n", msg);
	exit(1);
}

void png_read_int(u_int32_t *ptr, FILE* stream)
{
	signed char i;
	*ptr = 0;
	for (i = 24; i >= 0; i -= 8)
	{
		unsigned char buf = 0;
		fread(&buf, 1, 1, stream);
		*ptr |= (((u_int32_t)buf) << i);
	}
}

int png_read_chunk(pngreader *info)
{
	chunkheader c_head;
	png_read_int(&c_head.length, info->fin);
	c_head.type = malloc(4);
	fread(c_head.type, 4, 1, info->fin);
	
	if (strncmp(c_head.type, "IHDR", 4) == 0)
	{
		png_read_header(info, c_head.length);
	}
	else if (strncmp(c_head.type, "PLTE", 4) == 0)
	{
		png_read_palette(info, c_head.length);
	}
	else if (strncmp(c_head.type, "IDAT", 4) == 0)
	{
		png_read_data(info, c_head.length);
	}
	else if (*c_head.type & 32)
	{
		png_read_ancillary(info, c_head.length);
	}
	else if (strncmp(c_head.type, "IEND", 4) != 0)
	{
		png_die("critical_chunk", c_head.type);
	}
	
	u_int32_t crc;
	png_read_int(&crc, info->fin);
	
	return strncmp(c_head.type, "IEND", 4);
}

void png_read_header(pngreader *info, u_int32_t length)
{
	info->header = malloc(sizeof(pngheader));
	png_read_int(&info->header->width, info->fin);
	png_read_int(&info->header->height, info->fin);
	
	// Read the last 5 members
	fread(((u_int32_t*)info->header) + 2, 5, 1, info->fin);
	
	if (info->header->compression != COMPRESS_DEFLATE)
		png_die("unknown_compression", &info->header->compression);
	if (info->header->filter_method != FILTER_METHOD_BASIC_ADAPTIVE)
		png_die("unknown_filter_method", &info->header->filter_method);
	if (info->header->interlace)
		png_die("interlace_unsupported", NULL);
	
	info->bytedepth = info->header->bitdepth / 8;
	if (info->header->bitdepth % 8) info->bytedepth++;
	
	// Bytes per pixel
	info->bpp = info->bytedepth;
	switch (info->header->colortype)
	{
		case COLOR_GRAY:
		case COLOR_PALETTE:
			info->bpp *= 1;
			break;
		case COLOR_GRAYA:
			info->bpp *= 2;
			break;
		case COLOR_RGB:
			info->bpp *= 3;
			break;
		case COLOR_RGBA:
			info->bpp *= 4;
			break;
		default:
			png_die("unknown_colortype", &info->header->colortype);
	}
	
	info->expect_filter = 1;
	info->previous_scanline = malloc(info->header->width * info->bpp);
	memset(info->previous_scanline, 0, info->header->width * info->bpp);
	info->current_scanline = malloc(info->header->width * info->bpp);
	info->line_count = 0;
	
	info->zst.zalloc = Z_NULL;
	info->zst.zfree = Z_NULL;
	info->zst.opaque = Z_NULL;
	info->zst.avail_in = 0;
	info->zst.next_in = Z_NULL;
	
	int ret = inflateInit(&info->zst);
	if (ret != Z_OK)
		png_die("zlib_init_error", &ret);
	
	if (info->callbacks->read_header != NULL)
		(*info->callbacks->read_header)(info);
}

void png_read_palette(pngreader *info, u_int32_t length)
{
	if (length % 3)
		png_die("malformed_palette_length", &length);
	
	info->palette = malloc(sizeof(rgbcolor*) * length / 3);
	unsigned short i;
	for (i = 0; i < length; i += 3)
	{
		info->palette[i / 3] = malloc(sizeof(rgbcolor));
		fread(info->palette[i / 3], 3, 1, info->fin);
	}
}

void png_read_data(pngreader *info, u_int32_t length)
{
	unsigned char buffer_overflow = 0;
	unsigned char in[BUFFER_IN_SIZE], out[BUFFER_OUT_SIZE];
	int ret = Z_OK;
	
	// Loop until everything is read and the buffer is empty
	while (ret != Z_STREAM_END && (length != 0 && !buffer_overflow))
	{		
		u_int32_t size = 0;
		if (!buffer_overflow)
		{
			size = length > BUFFER_IN_SIZE ? BUFFER_IN_SIZE : length;
			info->zst.avail_in = fread(in, 1, size, info->fin);
			info->zst.next_in = in;
			length -= size;
		} else if (!length)
			png_die("premature_stream_end", NULL);
		
		info->zst.next_out = out;
		info->zst.avail_out = BUFFER_OUT_SIZE;
		ret = inflate(&info->zst, Z_SYNC_FLUSH);
		//ret = inflate(&info->zst, Z_NO_FLUSH);
		switch (ret)
		{
			case (Z_BUF_ERROR):
				// Not enough buffer size, but still size left?
	    			if (info->zst.avail_out > 0) 
					png_die("input_error", NULL);
				// Fall through
			case (Z_STREAM_END):
				// Fall through
			case (Z_OK):
				png_defilter(info, out, BUFFER_OUT_SIZE - info->zst.avail_out);
			
				buffer_overflow = (ret == Z_BUF_ERROR);
			
				info->zst.avail_out = BUFFER_OUT_SIZE;
				break;
			default:
				png_die("unknown_zlib_return", &ret);
		}
	}
	if (ret == Z_STREAM_END)
		inflateEnd(&info->zst);
}

void png_defilter(pngreader *info, unsigned char *buffer, unsigned int size)
{
	unsigned int i;
	for (i = 0; i < size; i++)
	{
		unsigned char byte = buffer[i];
		
		if (info->expect_filter)
		{
			info->expect_filter = 0;
			info->filter = byte;
			info->scan_pos = 0;
			continue;
		}
		
		
		// Paeth variables
		unsigned char a, b, c;
		short p, pa, pb, pc;
		
		u_int32_t x = info->scan_pos - info->bpp;
		switch (info->filter)
		{
			case (FILTER_NONE):
				break;
			case (FILTER_SUB):
				if (info->scan_pos >= info->bpp)
					byte += info->current_scanline[x];
				break;
			case (FILTER_UP):
				byte += info->previous_scanline[info->scan_pos];
				break;
			case (FILTER_AVERAGE):
				if (info->scan_pos >= info->bpp)
					byte += (info->current_scanline[x] +
						info->previous_scanline[info->scan_pos]) / 2;
				else
					byte += info->previous_scanline[info->scan_pos] / 2;
				break;
			case (FILTER_PAETH):
				b = info->previous_scanline[info->scan_pos];
				if (info->scan_pos >= info->bpp) 
				{
					a = info->current_scanline[x];
					c = info->previous_scanline[x];
				}
				else 
				{
					a = c = 0;
				}
				
				p = a + b - c;
				pa = abs(p - a);
				pb = abs(p - b);
				pc = abs(p - c);
				
				if ((pa <= pb) && (pa <= pc)) byte += a;
				else if (pb <= pc) byte += b;
				else byte += c;
				break;
			default:
				png_die("unknown_filter", &info->filter);
		}
		info->current_scanline[info->scan_pos] = byte;
		info->scan_pos++;
		
		if ((info->scan_pos % info->bpp) == 0 &&
			(info->scan_pos / info->bpp) == info->header->width) 
		{
			info->line_count++;
			if (info->callbacks->completed_scanline != NULL)
				(*info->callbacks->completed_scanline)(info->current_scanline, 
				info->previous_scanline, info->scan_pos, info);
			memcpy(info->previous_scanline, info->current_scanline, info->scan_pos);
			info->expect_filter = 1;
		}
	}
}

void png_read_ancillary(pngreader *info, u_int32_t length) 
{
	char buf;
	u_int32_t i;
	for (i = 0; i < length; i++)
		fread(&buf, 1, 1, info->fin);
}

void png_write_scanline(unsigned char *scanline, unsigned char *previous_scanline, 
	u_int32_t length, void *info_)
{
	pngreader *info = (pngreader*)info_;
	u_int32_t i;
	for (i = 0; i < length; i += info->bpp)
	{
		rgbcolor color;
		switch (info->header->colortype)
		{
			case (COLOR_GRAY):
			case (COLOR_GRAYA):
				color.r = color.g = color.b = scanline[i];
				break;
			case (COLOR_RGB):
			case (COLOR_RGBA):
				color.r = scanline[i + 0];
				color.g = scanline[i + 1];
				color.b = scanline[i + 2];
				break;
			case (COLOR_PALETTE):
				color = *info->palette[scanline[i]];
				break;
			default:
				png_die("unknown_colortype", &info->header->colortype);
		}
		fwrite(&color, 3, 1, info->fout);
	}
}

#ifdef PNGREADER
int main(int argc, char **argv)
{
	char **opts = pngcmd_getopts(argc, argv);
	if (!*(opts[PNGOPT_STDIN]))
		pngcmd_die("input unspecified");
	if (!*(opts[PNGOPT_STDOUT]))
		pngcmd_die("output unspecified");
	
	png_read(stdin, stdout, NULL, NULL);
	
	fclose(stdout);
	
	return 0;
}

#endif
