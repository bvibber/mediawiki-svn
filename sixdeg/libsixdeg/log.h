/* Six degrees of Wikipedia						*/
/* Copyright (c) 2007 River Tarnell <river@attenuate.org>.		*/
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */

/* $Id$ */

#ifndef LOG_H
#define LOG_H

#include <string>

namespace logger {

	void info(std::string const &);
	void warning(std::string const &);
	void error(std::string const &);

}

#endif
