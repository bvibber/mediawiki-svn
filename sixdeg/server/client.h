/* $Id$ */
/*
 * Six degrees of Wikipedia: Server.
 * This source code is released into the public domain.
 */

#ifndef CLIENT_H
#define CLIENT_H

#include <boost/noncopyable.hpp>

#include "encode_decode.h"

struct client : boost::noncopyable {
	client(int);
	~client();

	int fd;
	request_decoder decoder;
	request_encoder encoder;
};

#endif	/* !CLIENT_H */
