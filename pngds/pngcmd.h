#define PNGOPT_STDIN	0
#define PNGOPT_STDOUT	1

#define PNGOPT_COUNT	2

char** pngcmd_getopts(int argc, char **argv);
void pngcmd_die(char *msg);

