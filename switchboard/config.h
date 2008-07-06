/* Copyright (c) 2008 River Tarnell <river@wikimedia.org>. */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */
/* $Id$ */

#ifndef CONFIG_H
#define CONFIG_H

#include	<string>
#include	<vector>
#include	<stdexcept>
#include	<map>

#include	<boost/function.hpp>

#include	<log4cxx/logger.h>

struct config_error : std::runtime_error {
	config_error(char const *what) : std::runtime_error(what) {}
};

struct conf_listener {
	std::string host;
	std::string port;
};

struct config {
	std::vector<conf_listener>
			listeners;
	std::string logconf;
};

struct configuration_loader {
	configuration_loader();

	bool	load(std::string const &filename, config &newconf);

private:
	bool parse_config_line(std::string const &line, config &newconf);

	int lineno_;
	std::string file_;

	typedef boost::function<bool (configuration_loader *,
					std::vector<std::string> &fields,
					config &newconf)> confline_t;

	static std::map<std::string, confline_t> conflines;

	bool f_listen(std::vector<std::string> &fields, config &newconf);
	bool f_logconf(std::vector<std::string> &fields, config &newconf);

	log4cxx::LoggerPtr	logger;
};

extern config mainconf;

#endif
