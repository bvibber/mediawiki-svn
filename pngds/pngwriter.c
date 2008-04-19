#include <stdlib.h>
#include <stdio.h>
#include <string.h>

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

	pngwriter *winfo = calloc(sizeof(pngwriter), 1);
	winfo->zst.zalloc = Z_NULL;
	winfo->zst.zfree = Z_NULL;
	winfo->zst.opaque = Z_NULL;
	if (deflateInit(&winfo->zst, Z_DEFAULT_COMPRESSION) != Z_OK)
		png_die("zlib_init_error", NULL);
	winfo->in = malloc(header.width * info->bpp + 1);
	winfo->out = malloc(BUFFER_OUT_SIZE);
	winfo->zst.next_out = winfo->out;
	winfo->zst.avail_out = BUFFER_OUT_SIZE;
	
	info->extra2 = winfo;
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
	
	// Filter type
	winfo->in[0] = FILTER_NONE;
	memcpy(winfo->in + 1, scanline, length);
	
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
	
	png_resize(in, out, *((u_int32_t*)opts[PNGOPT_WIDTH]), 
		*((u_int32_t*)opts[PNGOPT_HEIGHT]), &callbacks);
	
	fclose(in); fclose(out);
	
	return 0;
}
#endif
	
