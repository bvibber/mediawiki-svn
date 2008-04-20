#include <stdlib.h>
#include <stdio.h>
#include <string.h>

#include "zlib.h"


#include "pngreader.h"
#include "pngutil.h"
#include "pngcmd.h"

#define BUFFER_IN_SIZE	32768
#define BUFFER_OUT_SIZE	131072

int png_read_chunk(pngreader *info);
void png_read_header(pngreader *info, uint32_t length);
void png_read_palette(pngreader *info, uint32_t length);
void png_read_data(pngreader *info, uint32_t length);
void png_defilter(pngreader *info, unsigned char *buffer, uint32_t size);
void png_read_ancillary(pngreader *info, uint32_t length);

void png_read(FILE* fin, FILE* fout, pngcallbacks* callbacks, void* extra1, void *extra2)
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
		callbacks->completed_scanline = &png_write_scanline_raw;
	info.callbacks = callbacks;
	
	info.extra1 = extra1;
	info.extra2 = extra2;
	
	char header[8];
	png_fread(header, 8, fin, NULL);
	if (strncmp(header, "\x89PNG\r\n\x1a\n", 8))
		png_die("header", header);
	
	while (png_read_chunk(&info));
		
	if (callbacks->done != NULL)
		(*callbacks->done)(&info);
	
	// Cleanup
	free(info.header);
	free(info.previous_scanline);
	free(info.current_scanline);
	// Hmmm need to find a way to free the palette
}

int png_read_chunk(pngreader *info)
{
	chunkheader c_head;
	png_read_int(&c_head.length, info->fin, NULL);
	
	info->crc = crc32(0, Z_NULL, 0);
	c_head.type = malloc(4);
	png_fread(c_head.type, 4, info->fin, &info->crc);
	
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
	
	uint32_t crc;
	png_read_int(&crc, info->fin, NULL);
#ifndef NO_CRC	
	if (crc != info->crc)
		png_die("crc_mismatch", &info->crc);
#endif
	
	// When I free this, I get a read error. Wtf?
	// free(c_head.type);
	
	return strncmp(c_head.type, "IEND", 4);
}

void png_read_header(pngreader *info, uint32_t length)
{
	if (length != 13)
		png_die("unexpected_header_length", &length);
	
	info->header = malloc(sizeof(pngheader));
	png_read_int(&info->header->width, info->fin, &info->crc);
	png_read_int(&info->header->height, info->fin, &info->crc);
	
	// Read the last 5 members
	png_fread(((uint32_t*)info->header) + 2, 5, info->fin, &info->crc);
	
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
	info->previous_scanline = calloc(info->header->width * info->bpp, 1);
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

void png_read_palette(pngreader *info, uint32_t length)
{
	if (length % 3)
		png_die("malformed_palette_length", &length);
	
	info->palette = malloc(sizeof(rgbcolor*) * length / 3);
	unsigned short i;
	for (i = 0; i < length; i += 3)
	{
		info->palette[i / 3] = malloc(sizeof(rgbcolor));
		png_fread(info->palette[i / 3], 3, info->fin, &info->crc);
	}
}

void png_read_data(pngreader *info, uint32_t length)
{
	unsigned char in[BUFFER_IN_SIZE], out[BUFFER_OUT_SIZE];
	int ret = Z_OK;
	
	// Loop until everything is read and the buffer is empty
	while (ret != Z_STREAM_END && length != 0)
	{		
		uint32_t size = 0;
		size = length > BUFFER_IN_SIZE ? BUFFER_IN_SIZE : length;
		info->zst.avail_in = png_fread(in, size, info->fin, &info->crc);
		info->zst.next_in = in;
		length -= size;
		
		do
		{
			info->zst.next_out = out;
			info->zst.avail_out = BUFFER_OUT_SIZE;
			ret = inflate(&info->zst, Z_SYNC_FLUSH);
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
					break;
				default:
					png_die("unknown_zlib_return", &ret);
			}
		} while (info->zst.avail_out == 0);
	}
	if (ret == Z_STREAM_END)
		inflateEnd(&info->zst);
	if (ret	 == Z_STREAM_END && length > 0)
		png_die("premature_stream_end", NULL);
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
		
		uint32_t x = info->scan_pos - info->bpp;
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
			char *tmp = info->previous_scanline;
			info->previous_scanline = info->current_scanline;
			info->current_scanline = tmp;
			info->expect_filter = 1;
		}
	}
}

void png_read_ancillary(pngreader *info, uint32_t length) 
{
	char buf;
	uint32_t i;
	for (i = 0; i < length; i++)
		png_fread(&buf, 1, info->fin, &info->crc);
}

void png_write_scanline_raw(unsigned char *scanline, unsigned char *previous_scanline, 
	uint32_t length, void *info_)
{
	pngreader *info = (pngreader*)info_;
	uint32_t i;
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
	void **opts = pngcmd_getopts(argc, argv);
	FILE *in, *out;
	png_open_streams(opts, &in, &out);
	
	png_read(in, out, NULL, NULL, NULL);
	
	fclose(in); fclose(out);
	
	return 0;
}

#endif
