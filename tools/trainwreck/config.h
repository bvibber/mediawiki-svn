/* Copyright (c) 2007-2009 River Tarnell 
 * <river@loreley.flyingparchment.org.uk>.  */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */

/* $Id$ */
#ifndef TW_CONFIG_H
#define TW_CONFIG_H

#include	<regex.h>

int read_configuration(char const *);

int can_ignore_errno(unsigned);

extern int *ignorable_errno;
extern int nignorable;

extern int server_id;
extern char *master_host, *master_user, *master_pass;
extern int master_port;
extern char *slave_host, *slave_user, *slave_pass;
extern int slave_port;
extern int max_buffer;

extern char *ctldoor;
extern char *statedir;
extern int autostart;
extern int unsynced;

extern regex_t *db_regex;
extern regex_t *ignore_regex;

extern int binlog_v4;

#endif	/* !TW_CONFIG_H */
