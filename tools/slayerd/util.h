/* Copyright (c) 2007-2009 River Tarnell <river@loreley.flyingparchment.org.uk>. */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */

/* $Id$ */

#ifndef UTIL_H
#define UTIL_H

#include	<sys/types.h>
#include	<string>
#include	<map>

std::string username(uid_t uid);
uid_t uid(std::string const &username);
void sendmail(std::string const &username, std::string const &message);
void log(std::string const &m);
std::string replace_file(std::string const &filename, std::map<std::string, std::string> const &vars);

#endif	/* !UTIL_H */
