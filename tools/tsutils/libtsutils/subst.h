/* Copyright (c) 2007 River Tarnell <river@attenuate.org>. */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */

/* $Id$ */

#ifndef SUBST_H
#define SUBST_H

struct subst_state;
typedef struct subst_state *subst_t;

subst_t subst_new(void);
void subst_free(subst_t);
void subst_add_var(subst_t, char const *var, char const *val);
char *subst_run(subst_t, char const *src);

#endif	/* !SUBST_H */
