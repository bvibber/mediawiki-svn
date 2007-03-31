/*
 * Six degrees of Wikipedia: Client interface definitions.
 * This source code is released into the public domain.
 */

#ifndef LINKSC_H
#define LINKSC_H

// #pragma ident "$URL: file:///home/river/s2s/linksd/linksc.h $ %E% %U%"

#define DOOR "/home/river/.linksnew.door"
#define CACHE "/home/river/.linksnew.cache"

#include <inttypes.h>

#include <vector>
#include <string>

typedef uint32_t page_id_t;

int linksc_findpath(std::vector<std::string>& res, std::string const &from, std::string const &to, bool ignore_dates);

#endif
