#include "zlib.h"

#include "pngreader.h"

#define BUFFER_IN_SIZE	32768
#define BUFFER_OUT_SIZE	65536

void png_resize(FILE* fin, FILE* fout, unsigned int width, unsigned int height)
{
	pngreader info;
	
	info.width = width;
	info.height = height;
	info.fin = fin;
	info.fout = fout;
	
	char header[8];
	fread(header, 1, 8, fin);
	if (strcmp(header, "\x89PNG\r\n\x1a\n", 8))
		png_die("header", header, 8);
	
	while (png_read_chunk(&info));
}

int png_die(char *msg, void *data, int data_len)
{
	fwrite(msg, 1, strlen(msg), stderr);
	exit(1);
}

int png_read_chunk(pngresize *info)
{
	chunkheader c_head;
	fread(&c_head, 4, 2, info->fin);
	
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
	else if (c_head.type[0] & 32)
	{
		png_read_ancillary(info, c_head.length);
	}
	else if (strncmp(c_head.type, "IEND", 4) != 0)
	{
		png_die("critical_chunk", c_head.type, 4);
	}
	
	unsigned int crc;
	fread(&crc, 4, 1, info->fin);
	
	return strncmp(c_head.type, "IEND", 42);
}

void png_read_header(pngreader *info, int length)
{
	info->header = malloc(sizeof(pngheader));
	fread(info->header, sizeof(pngheader), 1, info->fin);
	
	if (info->header->compression != COMPRESS_DEFLATE)
		png_die("unknown_compression", info->header->compression, 1);
	if (info->header->filter_method != FILTER_METHOD_BASIC_ADAPTIVE)
		png_die("unknown_filter_method", info->header->filter_method, 1);
	if (info->header->interlace)
		png_die("interlace_unsupported", NULL, 0);
	
	info->bytedepth = info->header->bpp / 8;
	if (info->header->bitdepth % 8) info->bytedepth++;
	
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
			png_die("unknown_colortype", info->header->colortype, 1);
	}
	info->expect_filter = 0;
	info->previous_scanline = malloc(info->header->width * info->bpp);
	memset(info->previous_scanline, 0, info->header->width * info->bpp);
	info->current_scanline = malloc(info->header->width * info->bpp);
	
	info->zst.zalloc = (alloc_func)NULL;
	info->zst.zfree = (free_func)Z_NULL;
	
	inflateInit(&info->zst);
	
	info->zst.next_in = malloc(BUFFER_IN_SIZE);
	info->zst.next_out = malloc(BUFFER_OUT_SIZE);
}

void png_read_palette(pngreader *info, int length)
{
	if (length % 3)
		png_die("malformed_palette_length", length);
	
	info->palette = malloc(sizeof(*rgbcolor) * length / 3);
	for (int i = 0; i < length; i += 3)
	{
		*(info->palette + i / 3) = malloc(sizeof(color));
		fread(*(info->palette + i / 3), 3, 1, info->fin);
	}
}

void png_read_data(pngreader *info, int length)
{
	// Does not work, need to find out how the buffers work
	while (length)
	{
		int size = min(length, BUFFER_IN_SIZE);
		info->zst.next_in = malloc(size);
		info->zst.avail_in = size;
		fread(info->zst.next_in, 1, size, info->fin);
		length -= size;
		
		int err = inflate(info->zst, Z_SYNC_FLUSH);
		switch (err)
		{
			case (Z_STREAM_END):
				if (size)
					die_png("premature_input_end", NULL, 0);
				return;
			
			// Code from <http://svn.python.org/view/python/trunk/Modules/zlibmodule.c?rev=61874&view=auto>
			case (Z_BUF_ERROR):
				/*
				 * If there is at least 1 byte of room according to zst.avail_out
				 * and we get this error, assume that it means zlib cannot
				 * process the inflate call() due to an error in the data.
				 */
	    			if (info->zst.avail_out > 0) 
					die_debug("input_error", NULL, 0);
				// Fall through
			case (Z_OK):
				png_defilter(info, info->zst.next_out, 
					BUFFER_OUT_SIZE - info->zst.avail_out);
			default:
				// Hmm...
		}
	}
}

void png_defilter(pngreader *info, unsigned char *buffer, int size)
{
	for (int i = 0; i < size; i++)
	{
		int x;
		unsigned char byte = *(buffer + byte);
		
		if (info->expect_filter)
		{
			info->expect_filter = 0;
			info->filter = byte;
			continue;
		}
		
		switch (info->filter)
		{
			case (FILTER_NONE):
				break;
			case (FILTER_SUB):
				x = info->scan_pos - info->bpp;
				if (x >= 0) byte += *(info->current_scanline + x);
				break;
			case (FILTER_UP):
				byte += *(info->previous_scanline + info->scan_pos);
				break;
			case (FILTER_AVERAGE):
				x = info->scan_pos - info->bpp;
				if (x >= 0)
					byte += (*(info->current_scanline + x) +
						*(info->previous_scanline + info->scan_pos)) / 2;
				else
					byte += *(info->previous_scanline + info->scan_pos) / 2;
				break;
			case (FILTER_PAETH):
				unsigned char a, b, c;
				unsigned char pa, pb, pc;
				short p;
				
				x = info->scan_pos - info->bpp;
				b = *(info->previous_scanline + info->scan_pos);
				if (x >= 0) 
				{
					a = *(info->current_scanline + x);
					c = *(info->previous_scanline + x);
				}
				else 
				{
					a = c = 0;
				}
				
				p = a + b - c;
				pa = abs(p - a);
				pb = abs(p - b);
				pc = abs(p - c);
				
				if (pa <= pb && pa <= pc) byte += a;
				else if (pb <= pc) byte += b;
				else byte += c;
			default:
				png_die("unknown_filter", &info->filter, 1);
		}
		*(info->scanline +  i) = byte;
		
		if ((info->scan_pos % info->bpp) == 0 &&
			(info->scan_pos / info->bpp) == 0) 
		{
			info->previous_scanline = info->current_scanline;
			info->expect_filter = 1;
		}
	}
}