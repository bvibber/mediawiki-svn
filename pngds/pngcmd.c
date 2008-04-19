#include <stdlib.h>
#include <stdio.h>
#include <string.h>

#include "pngcmd.h"

void pngcmd_help();

void** pngcmd_getopts(int argc, char **argv)
{
	int i;
	void **res;
	
	if (argc == 0) pngcmd_help();
	
	res = calloc(sizeof(void*), PNGOPT_COUNT);
	
	res[PNGOPT_STDIN] = calloc(sizeof(char), 1);
	res[PNGOPT_STDOUT] = calloc(sizeof(char), 1);
	
	res[PNGOPT_WIDTH] = calloc(sizeof(u_int32_t), 1);
	res[PNGOPT_HEIGHT] = calloc(sizeof(u_int32_t), 1);
	
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
			*((u_int32_t*)res[PNGOPT_HEIGHT]) = strtol(argv[i], NULL, 10);
		else if (strcmp(argv[i > 0 ? i - 1 : 0], "--width") == 0 && i != 0)
			*((u_int32_t*)res[PNGOPT_WIDTH]) = strtol(argv[i], NULL, 10);
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
#endif
#ifdef PNGRESIZE
		"pngresize [--from-stdin] [--to-stdout] [<source>] [<target>]\n"
		"	[--width <width>] [--height <height>]\n"
#endif
#ifdef PNGDS
		"pngds [--from-stdin] [--to-stdout] [<source>] [<target>]\n"
		"	[--width <width>] [--height <height>]\n"
#endif
		"\n");
	exit(0);
}
