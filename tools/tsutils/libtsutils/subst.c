/* Copyright (c) 2007 River Tarnell <river@attenuate.org>. */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */

/* $Id$ */

#include	<stdlib.h>
#include	<string.h>
#include	<stdio.h>
#include	"tsutils.h"
#include	"subst.h"

typedef struct subst_entry {
	struct subst_entry *ss_next;
	char *ss_var;
	char *ss_val;
} subst_entry;

typedef struct subst_state {
	subst_entry head;
} subst_state;

static char *find_value(subst_t, char const *, size_t);

subst_t
subst_new()
{
	return calloc(1, sizeof(subst_state));
}

void
subst_free(s)
	subst_t s;
{
subst_entry	*ent, *next;
	for (ent = s->head.ss_next, next = (ent ? ent->ss_next : NULL); ent;
	     ent = next, next = (ent ? ent->ss_next : NULL)) {
		free(ent->ss_var);
		free(ent->ss_val);
		free(ent);
	}
	free(s);
}

void
subst_add_var(s, var, val)
	subst_t s;
	char const *var, *val;
{
subst_entry	*ent;
	ent = calloc(1, sizeof(*ent));
	if (ent == NULL)
		return;

	ent->ss_val = strdup(val);
	if (ent->ss_val == NULL) {
		free(ent);
		return;
	}

	ent->ss_var = strdup(var);
	if (ent->ss_var == NULL) {
		free(ent->ss_val);
		free(ent);
		return;
	}

	ent->ss_next = s->head.ss_next;
	s->head.ss_next = ent;
	return;
}

char *
subst_run(s, src)
	subst_t s;
	char const *src;
{
char const	*p = src, *start, *end;
char		*result = strdup("");

	while ((start = strchr(p, '%')) != NULL) {
	char	*value;

		start++;
		if ((end = strchr(start, '%')) == NULL)
			break;

		/*LINTED ptrdiff_t overflow */
		result = realloc_strncat(result, p, start - p - 1);

		p = end + 1;
		
		/*LINTED ptrdiff_t overflow */
		value = find_value(s, start, end - start);
		if (value)
			result = realloc_strcat(result, value);
	}

	result = realloc_strcat(result, p);
	return result;
}

static char *
find_value(s, var, len)
	subst_t s;
	char const *var;
	size_t len;
{
subst_entry	*e;
	for (e = s->head.ss_next; e; e = e->ss_next)
		if (strlen(e->ss_var) == len && !memcmp(e->ss_var, var, len))
			return e->ss_val;
	return NULL;
}

#ifdef TEST
int
main()
{
	char *r;
	subst_t s = subst_new();
	if (s == NULL)
		return 1;

	subst_add_var(s, "one", "1");
	subst_add_var(s, "two", "2");
	subst_add_var(s, "three", "3");

	r = subst_run(s, "Testing %one%, %two% and %three...\n");
	printf("[%s]\n", r);
	subst_free(s);
	return 0;
}
#endif	/* TEST */
