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
	char header[8];
	
	info.header = NULL;
	info.palette = NULL;
	info.previous_scanline = NULL;
	info.current_scanline = NULL;
	
	info.fin = fin;
	info.fout = fout;
	if (callbacks == NULL)
	{
		info.callbacks.completed_scanline = NULL;
		info.callbacks.read_header = NULL;
		info.callbacks.done = NULL;
	}
	else
		info.callbacks = *callbacks;
	if (info.callbacks.completed_scanline == NULL)
		info.callbacks.completed_scanline = &png_write_scanline_raw;
	
	info.extra1 = extra1;
	info.extra2 = extra2;
	
	png_fread(header, 8, fin, NULL);
	if (strncmp(header, "\x89PNG\r\n\x1a\n", 8))
		png_die("File is not a PNG", header);
	
	while (png_read_chunk(&info));
		
	if (info.callbacks.done != NULL)
		(*info.callbacks.done)(&info);
	
	/* Cleanup of fields allocated at png_read_header */
	free(info.header);
	free(info.previous_scanline);
	free(info.current_scanline);
	free(info.palette); /* Allocated at png_read_palette */
}

int png_read_chunk(pngreader *info)
{
	chunkheader c_head;
	uint32_t crc;

	c_head.length = png_read_int(info->fin, NULL);
	info->crc = crc32(0, Z_NULL, 0);
	png_fread(c_head.type, sizeof(c_head.type), info->fin, &info->crc);
	
	if (strncmp(c_head.type, "IHDR", 4) == 0)
	{
		png_read_header(info, c_head.length);
	}
	else if (info->header == NULL)
	{
		png_die_type("first_chunk_is_not_header", c_head.type);
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
		png_die_type("critical_chunk", c_head.type);
	}
	
	crc = png_read_int(info->fin, NULL);
#ifndef NO_CRC	
	if (crc != info->crc)
		png_die("crc_mismatch", &info->crc);
#endif
	
	return strncmp(c_head.type, "IEND", 4);
}

void png_read_header(pngreader *info, uint32_t length)
{
	int ret;

	if (length != 13)
		png_die("unexpected_header_length", &length);
	
	if (info->header) /* Not a valid PNG */
		png_die("file_has_several_headers", NULL);
	
	info->header = xmalloc(sizeof(pngheader));
	info->header->width = png_read_int(info->fin, &info->crc);
	info->header->height = png_read_int(info->fin, &info->crc);
	
	// Read the last 5 members
	png_fread(&info->header->properties, sizeof(info->header->properties), info->fin, &info->crc);
	
	if (info->header->properties.compression != COMPRESS_DEFLATE)
		png_die("unknown_properties.compression", &info->header->properties.compression);
	if (info->header->properties.filter_method != FILTER_METHOD_BASIC_ADAPTIVE)
		png_die("unknown_properties.filter_method", &info->header->properties.filter_method);
	if (info->header->properties.interlace)
		png_die("properties.interlace_unsupported", NULL);
	
	info->bytedepth = info->header->properties.bitdepth / 8;
	if (info->header->properties.bitdepth % 8) info->bytedepth++;
	
	// Bytes per pixel
	info->bpp = info->bytedepth;
	switch (info->header->properties.colortype)
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
			png_die("unknown_colortype", &info->header->properties.colortype);
	}
	
	info->expect_filter = 1;
	info->previous_scanline = calloc(info->header->width * info->bpp, 1);
	if (!info->previous_scanline) png_die("insufficient_memory", NULL);
	info->current_scanline = xmalloc(info->header->width * info->bpp);
	info->line_count = 0;
	
	info->zst.zalloc = Z_NULL;
	info->zst.zfree = Z_NULL;
	info->zst.opaque = Z_NULL;
	info->zst.avail_in = 0;
	info->zst.next_in = Z_NULL;
	
	ret = inflateInit(&info->zst);
	if (ret != Z_OK)
		png_die("zlib_init_error", &ret);
	
	if (info->callbacks.read_header != NULL)
		(*info->callbacks.read_header)(info);
}

void png_read_palette(pngreader *info, uint32_t length)
{
	if (length % 3)
		png_die("malformed_palette_length", &length);
	
	if (info->palette != NULL) /* Invalid PNG */
		png_die("file_has_several_palettes", NULL);
	
	info->palette = xmalloc(sizeof(rgbcolor)*256);
	png_fread(info->palette, length, info->fin, &info->crc);
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
		uint32_t x;

		// Paeth variables
		unsigned char a, b, c;
		short p, pa, pb, pc;
		
		if (info->expect_filter)
		{
			info->expect_filter = 0;
			info->filter = byte;
			info->scan_pos = 0;
			continue;
		}
		
		x = info->scan_pos - info->bpp;
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
			unsigned char *tmp;
				
			info->line_count++;
			if (info->callbacks.completed_scanline != NULL)
				(*info->callbacks.completed_scanline)(info->current_scanline, 
				info->previous_scanline, info->scan_pos, info);
			tmp = info->previous_scanline;
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
	uint32_t length, pngreader *info)
{
	uint32_t i;
	for (i = 0; i < length; i += info->bpp)
	{
		rgbcolor color;
		switch (info->header->properties.colortype)
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
				color = info->palette[scanline[i]];
				break;
			default:
				png_die("unknown_properties.colortype", &info->header->properties.colortype);
		}
		fwrite(&color, 3, 1, info->fout);
	}
}

#ifdef PNGREADER
int main(int argc, char **argv)
{
	FILE *in, *out;	
	struct pngopts opts;
	
	pngcmd_getopts(&opts, argc, argv);
	png_open_streams(&opts, &in, &out);
	
	png_read(in, out, NULL, NULL, NULL);
	
	fclose(in); fclose(out);
	return 0;
}

#endif
