/*
 * Six degrees of Wikipedia: Client interface definitions.
 * This source code is released into the public domain.
 */

#ifndef LINKSC_H
#define LINKSC_H

#pragma ident "$URL: file:///home/river/s2s/linksd/linksc.h $ %E% %U%"

#define DOOR "/home/river/.linksnew.door"
#define CACHE "/home/river/.linksnew.cache"

#include <vector>
#include <string>

int linksc_findpath(std::vector<std::string>& res, std::string from, std::string to);

#endif
