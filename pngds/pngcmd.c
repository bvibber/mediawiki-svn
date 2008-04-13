#include <stdlib.h>
#include <stdio.h>
#include <string.h>

#include "pngcmd.h"

char** pngcmd_getopts(int argc, char **argv)
{
	int i;
	char **res;
	
	res = malloc(PNGOPT_COUNT * sizeof(char*));
	
	*(res + PNGOPT_STDIN) = malloc(sizeof(char));
	**(res + PNGOPT_STDIN) = 0;
	*(res + PNGOPT_STDOUT) = malloc(sizeof(char));
	**(res + PNGOPT_STDOUT) = 0;
	
	for (i = 0; i < argc; i++)
	{
		if (strcmp(*(argv + i), "--from-stdin") == 0)
			**(res + PNGOPT_STDIN) = 1;
		else if (strcmp(*(argv + i), "--to-stdout") == 0)
			**(res + PNGOPT_STDOUT) = 1;
		else if (strncmp(*(argv + i), "--", 2) == 0)
			pngcmd_die("unknown option
	}
	return res;
}

void pngcmd_die(char *msg, char *extra)
{
	char newline = '\n';
	fwrite(msg, strlen(msg), 1, stderr);
	fwrite(&newline, 1, 1, stderr);
	exit(255);
}
