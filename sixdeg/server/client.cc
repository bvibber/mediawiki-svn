/*
 * Six degrees of Wikipedia: Server.
 * This source code is released into the public domain.
 */

#include <unistd.h>

#include "client.h"

client::client(int s)
	: fd(s)
{
}

client::~client(void)
{
	close(fd);
}
