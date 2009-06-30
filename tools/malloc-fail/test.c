#include <stdio.h>
#include <stdlib.h>

int main(int argc, char** argv)
{
	void *p;
	if (argc < 2 || !strcmp(argv[1], "malloc")) {
		puts("Testing malloc");
		malloc((size_t)-1);
	} else if (!strcmp(argv[1], "calloc")) {
		puts("Testing calloc");
		calloc((size_t)-1, 1);
	} else if (!strcmp(argv[1], "realloc")) {
		puts("Testing realloc");
		p = malloc(1);
		p = realloc(p, (size_t)-1);
	} else {
		printf("Usage: %s {malloc|realloc|calloc}\n", argv[0]);
		exit(1);
	}
	puts("Still here!");
}
