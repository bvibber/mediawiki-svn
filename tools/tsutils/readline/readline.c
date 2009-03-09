/*
 * Read a line from stdin using readline for line editing.
 * $Id$
 */

#include	<stdio.h>
#include	<string.h>
#include	<errno.h>
#include	<readline/readline.h>

int
main(argc, argv)
	int argc
#ifdef __GNUC__
	__attribute__((unused))
#endif
	;
	char **argv;
{
char const	*prompt = argv[1];
char		*response;
FILE		*tty;

	if ((tty = fopen("/dev/tty", "w+")) == NULL) {
		(void) fprintf(stderr,
			"%s: cannot open /dev/tty: %s\n",
			argv[0], strerror(errno));
		return 1;
	}

	rl_instream = tty;
	rl_outstream = tty;

	if ((response = readline(prompt)) == NULL)
		return 1;

	(void) printf("%s\n", response);
	return 0;
}
