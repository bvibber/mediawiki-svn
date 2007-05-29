/* Copyright (c) 2007 River Tarnell <river@attenuate.org>. */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */

/* $Id$ */

#ifndef TSUTILS_H
#define TSUTILS_H

char	*realloc_strcat(char *str, char const *add);
char	*realloc_strncat(char *str, char const *add, size_t len);
void	 strdup_free(char **s, char const *n);
int	 sendmail(char const *username, char const *message);
void	 logmsg(char const *msg, ...);
int	 daemon_detach(char const *progname);
int	 get_user_tty(char const *);
char	*file_to_string(char const *);

#endif	/* !TSUTILS_H */
