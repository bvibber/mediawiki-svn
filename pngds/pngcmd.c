#include <stdlib.h>
#include <stdio.h>
#include <string.h>

#ifdef _MSC_VER
typedef unsigned __int32  uint32_t;
#else
#include <stdint.h>
#endif

#include "zlib.h"
#include "pngcmd.h"

void pngcmd_help();

void** pngcmd_getopts(int argc, char **argv)
{
	int i;
	void **res;
	
	if (argc == 1) pngcmd_help();
	
	res = calloc(sizeof(void*), PNGOPT_COUNT);
	
	res[PNGOPT_STDIN] = calloc(sizeof(char), 1);
	res[PNGOPT_STDOUT] = calloc(sizeof(char), 1);
	
	res[PNGOPT_WIDTH] = calloc(sizeof(uint32_t), 1);
	res[PNGOPT_HEIGHT] = calloc(sizeof(uint32_t), 1);
	
	res[PNGOPT_DEFLATE_LEVEL] = malloc(sizeof(char));
	*(char *)res[PNGOPT_DEFLATE_LEVEL] = Z_DEFAULT_COMPRESSION;
	res[PNGOPT_NO_FILTERING] = calloc(sizeof(char), 1);
	
	
	for (i = 1; i < argc; i++)
	{
		if (strcmp(argv[i], "--help") == 0)
			pngcmd_help();
		else if (strcmp(argv[i], "--from-stdin") == 0)
			*(char *)res[PNGOPT_STDIN] = 1;
		else if (strcmp(argv[i], "--to-stdout") == 0)
			*(char *)res[PNGOPT_STDOUT] = 1;
#ifndef PNGREADER
		else if (strcmp(argv[i], "--height") == 0 || 
				strcmp(argv[i], "--width") == 0)
			; // Do nothing
		else if (strcmp(argv[i > 0 ? i - 1 : 0], "--height") == 0 && i != 0)
			*(uint32_t *)res[PNGOPT_HEIGHT] = strtol(argv[i], NULL, 10);
		else if (strcmp(argv[i > 0 ? i - 1 : 0], "--width") == 0 && i != 0)
			*(uint32_t *)res[PNGOPT_WIDTH] = strtol(argv[i], NULL, 10);
#endif
#ifdef PNGDS
		else if (argv[i][0] == '-' && 
				(argv[i][1] >= '0' && argv[i][1] <= '9'))
			*(char *)res[PNGOPT_DEFLATE_LEVEL] = argv[i][1] - 48;
		else if (strcmp(argv[i], "--no-filtering") == 0)
			*(char *)res[PNGOPT_NO_FILTERING] = 1;
#endif
		else if (strncmp(argv[i], "--", 2) == 0)
			pngcmd_die("unknown option",  argv[i]);
		else if (res[PNGOPT_IN] == NULL)
			res[PNGOPT_IN] = argv[i];
		else if (res[PNGOPT_OUT] == NULL)
			res[PNGOPT_OUT] = argv[i];
		else
			pngcmd_die("unknown option", argv[i]);
	}
	return res;
}

void pngcmd_die(char *msg, char *extra)
{
	if (extra == NULL)
		fprintf(stderr, "error: %s\n", msg);
	else
		fprintf(stderr, "error: %s (%s)\n", msg, extra);
	exit(255);
}

void pngcmd_help()
{
	fprintf(stderr,
#ifdef PNGREADER
		"pngreader [--from-stdin] [--to-stdout] [<source>] [<target>]\n"
		"\n"
#endif
#ifdef PNGRESIZE
		"pngresize [--from-stdin] [--to-stdout] [<source>] [<target>]\n"
		"	[--width <width>] [--height <height>]\n"
		"\n"
#endif
#ifdef PNGDS
		"pngds [--from-stdin] [--to-stdout] [<source>] [<target>]\n"
		"	[--width <width>] [--height <height>] [--no-filtering] [-n]\n"
		"\n"
#endif
		"	--from-stdin	Read data from stdin instead from <source>\n"
		"	--to-stdout	Output data to stdout instead to <target>\n"
#ifndef PNGREADER
		"\n"
		"	--width		Resize width\n"
		"	--height	Resize height\n"
		"			If only one of width or height is specified,\n"
		"			the image is resized keeping aspect ratio.\n"
#endif
#ifdef PNGDS
		"\n"
		"	--no-filtering	Disable Paeth filtering (faster)\n"
		"	-n		Compression level from 0-9 (-0 .. -9)\n"
#endif
		"\n");
	exit(0);
}
