typedef struct
{
	unsigned char *in;
	unsigned char *out;
	z_stream zst;
	char deflate_level;
	char filter_method;
} pngwriter;

void png_write_header(void *_info);
void png_write_end(void *_info);
void png_write_scanline(unsigned char *scanline, unsigned char *previous_scanline, uint32_t length, void *info_);
