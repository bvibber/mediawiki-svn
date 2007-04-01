/* $Id$ */
/*
 * Six degrees of Wikipedia: Client interface definitions.
 * This source code is released into the public domain.
 */

#ifndef LINKSC_H
#define LINKSC_H

#include <inttypes.h>

#include <vector>
#include <string>

#define LINKS_OKAY		4	/* Request succeeded */
#define LINKS_NO_CONNECT	3	/* Could not connect to links server or protocol error */
#define LINKS_NO_FROM		0	/* Source article does not exist */
#define LINKS_NO_TO		1	/* Target article does not exist */

typedef uint32_t page_id_t;
typedef uint32_t text_id_t;

int linksc_findpath(std::vector<std::string>& res, std::string const &from, std::string const &to, bool ignore_dates);

#endif
