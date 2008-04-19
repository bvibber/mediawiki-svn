#ifndef	_PNGCMD_H
#define _PNGCMD_H	1

#define PNGOPT_STDIN	0
#define PNGOPT_STDOUT	1
#define PNGOPT_IN	2
#define PNGOPT_OUT	3
#define PNGOPT_WIDTH	4
#define PNGOPT_HEIGHT	5
#define PNGOPT_DEFLATE_LEVEL	6
#define PNGOPT_NO_FILTERING	7

#define PNGOPT_COUNT	8

void** pngcmd_getopts(int argc, char **argv);
void pngcmd_die(char *msg, char *extra);

#endif
