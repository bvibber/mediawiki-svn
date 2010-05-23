#ifndef	_PNGCMD_H
#define _PNGCMD_H	1

#ifndef __cplusplus
typedef enum { false, true } bool;
#endif

struct pngopts {
	bool stdin;
	bool stdout;
	
	char const* input_filename;
	char const* output_filename;
	
	uint32_t width;
	uint32_t height;
	
	int_least8_t deflate_level;
	bool no_filtering;
};

void pngcmd_getopts(struct pngopts* opts, int argc, char **argv);
void pngcmd_die(char *msg, char *extra);

#endif
