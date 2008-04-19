typedef struct
{
	char *in;
	char *out;
	z_stream zst;
	
} pngwriter;

void png_write_header(void *_info);
void png_write_end(void *_info);
void png_write_scanline(unsigned char *scanline, unsigned char *previous_scanline, u_int32_t length, void *info_);
