struct pngwriter
{
	unsigned char *in;
	unsigned char *out;
	z_stream zst;
	char deflate_level;
	char filter_method;
};

void png_write_header(pngreader *info);
void png_write_end(pngreader *info);
void png_write_scanline(unsigned char *scanline, unsigned char *previous_scanline, uint32_t length, pngreader *info);
