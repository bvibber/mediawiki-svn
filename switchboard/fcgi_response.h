/* Copyright (c) 2008 River Tarnell <river@wikimedia.org>. */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */
/* $Id$ */

#ifndef FCGI_RESPONSE_H
#define FCGI_RESPONSE_H

#include	<vector>
#include	<string>

#include	"fcgi.h"

/*
 * Construct a FastCGI error response.
 */

struct fcgi_response {
	fcgi_response(int request_id);

	void	add_stdout(std::string const &text);
	void	add_stderr(std::string const &text);
	void	end(void);

	std::vector<fcgi::record> const &as_vector() const;

private:
	int request_id_;
	std::vector<fcgi::record> records_;
};

#endif	/* !FCGI_RESPONSE_H */
