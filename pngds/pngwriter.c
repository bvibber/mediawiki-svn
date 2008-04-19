#include <stdlib.h>
#include <stdio.h>
#include <string.h>

#include "zlib.h"

#include "pngreader.h"
#include "pngresize.h"
#include "pngutil.h"
#include "pngcmd.h"
#include "pngwriter.h"

#define BUFFER_OUT_SIZE	32768

void png_write_chunk(pngreader *info, char *type, void *ptr, u_int32_t size);

void png_write_header(void *_info)
{
	pngreader *info = (pngreader*)_info;
	pngheader header;
	
	memcpy(&header, info->header, 13);
	if (info->extra1 != NULL)
	{
		pngresize *rinfo = (pngresize*)info->extra1;
		header.width = rinfo->width;
		header.height = rinfo->height;
	}
	png_fwrite("\x89PNG\r\n\x1a\n", 8, info->fout, NULL);
	
	u_int32_t crc = crc32(0, Z_NULL, 0);
	png_write_int(13, info->fout, NULL);
	png_fwrite("IHDR", 4, info->fout, &crc);
	png_write_int(header.width, info->fout, &crc);
	png_write_int(header.height, info->fout, &crc);
	png_fwrite((char*)&header + 8, 5, info->fout, &crc);
	png_write_int(crc, info->fout, NULL);

	pngwriter *winfo = (pngwriter*)info->extra2;
	winfo->zst.zalloc = Z_NULL;
	winfo->zst.zfree = Z_NULL;
	winfo->zst.opaque = Z_NULL;
	if (deflateInit(&winfo->zst, winfo->deflate_level) != Z_OK)
		png_die("zlib_init_error", NULL);
	winfo->in = malloc(header.width * info->bpp + 1);
	winfo->out = malloc(BUFFER_OUT_SIZE);
	winfo->zst.next_out = winfo->out;
	winfo->zst.avail_out = BUFFER_OUT_SIZE;
}

void png_write_chunk(pngreader *info, char *type, void *ptr, u_int32_t size)
{
	u_int32_t crc = crc32(0, Z_NULL, 0);
	png_write_int(size, info->fout, NULL);
	png_fwrite(type, 4, info->fout, &crc);
	png_fwrite(ptr, size, info->fout, &crc);
	png_write_int(crc, info->fout, NULL);
}

void png_write_scanline(unsigned char *scanline, unsigned char *previous_scanline, 
	u_int32_t length, void *info_)
{
	pngreader *info = (pngreader*)info_;
	pngwriter *winfo = (pngwriter*)info->extra2;
	
	int i;
	unsigned char a, b, c;
	short p, pa, pb, pc;
	switch (winfo->filter_method)
	{
		case FILTER_NONE:
			// Filter type
			memcpy(winfo->in + 1, scanline, length);
			break;
		case FILTER_PAETH:
			for (i = 0; i < length; i++)
			{
				winfo->in[i + 1] = scanline[i];
				if (i >= info->bpp)
				{
					a = scanline[i - info->bpp];
					c = previous_scanline[i - info->bpp];
				}
				else
				{
					a = c = 0;
				}
				b = previous_scanline[i];
				
				p = a + b - c;
				pa = abs(p - a);
				pb = abs(p - b);
				pc = abs(p - c);
				
				if ((pa <= pb) && (pa <= pc)) winfo->in[i + 1] -= a;
				else if (pb <= pc) winfo->in[i + 1] -= b;
				else winfo->in[i + 1] -= c;
			}
			break;
		default:
			png_die("unsupported_filter", &winfo->filter_method);
	}
	winfo->in[0] = winfo->filter_method;
	
	int ret;
	winfo->zst.next_in = winfo->in;
	winfo->zst.avail_in = length + 1;
	
	while (winfo->zst.avail_in > 0)
	{
		ret = deflate(&winfo->zst, Z_NO_FLUSH);
		if (ret == Z_STREAM_ERROR)
			png_die("deflate_error", NULL);
		if (ret == Z_BUF_ERROR)
			png_die("deflate_buffer_error", NULL);
		
		if (winfo->zst.avail_out == 0)
		{
			// Flush to disk
			png_write_chunk(info, "IDAT", winfo->out, BUFFER_OUT_SIZE - winfo->zst.avail_out);
			winfo->zst.next_out = winfo->out;
			winfo->zst.avail_out = BUFFER_OUT_SIZE;
		}
	} 
	
}

void png_write_end(void *_info)
{
	pngreader *info = (pngreader*)_info;
	pngwriter *winfo = (pngwriter*)info->extra2;
	
	int ret;
	do
	{
		ret = deflate(&winfo->zst, Z_FINISH);
		if (ret == Z_STREAM_ERROR)
			png_die("deflate_finish_error", NULL);
		if (ret == Z_BUF_ERROR)
			png_die("deflate_finish_buffer_error", NULL);
		
		// Flush to disk
		png_write_chunk(info, "IDAT", winfo->out, BUFFER_OUT_SIZE - winfo->zst.avail_out);
		winfo->zst.next_out = winfo->out;
		winfo->zst.avail_out = BUFFER_OUT_SIZE;
	}
	while (ret != Z_STREAM_END);
	
	deflateEnd(&winfo->zst);
	
	png_write_chunk(info, "IEND", NULL, 0);
	
	// Cleanup
	free(winfo->in);
	free(winfo->out);
	free(winfo);
}

#ifdef PNGDS
int main(int argc, char **argv)
{
	void **opts = pngcmd_getopts(argc, argv);
	FILE *in, *out;
	png_open_streams(opts, &in, &out);
	
	pngcallbacks callbacks;
	callbacks.completed_scanline = &png_write_scanline;
	callbacks.read_header = &png_write_header;
	callbacks.done = &png_write_end;
	
	pngwriter *winfo = calloc(sizeof(pngwriter), 1);
	winfo->deflate_level = *(char *)opts[PNGOPT_DEFLATE_LEVEL];
	winfo->filter_method = *(char *)opts[PNGOPT_NO_FILTERING] ? FILTER_NONE : FILTER_PAETH;
	
	png_resize(in, out, *(u_int32_t *)opts[PNGOPT_WIDTH], 
		*(u_int32_t *)opts[PNGOPT_HEIGHT], &callbacks, winfo);
	
	fclose(in); fclose(out);
	
	return 0;
}
#endif
	
