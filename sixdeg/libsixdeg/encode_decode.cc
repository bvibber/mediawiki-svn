/*
 * Six degrees of Wikipedia: Server (request handler).
 * This source code is released into the public domain.
 */

#include <iostream>
#include <boost/format.hpp>

#include "encode_decode.h"

/*
 * Requests are encoded in the format:
 *
 * <size><name>=<value>[<size><name>=<value>...]\0\0\0\0
 *
 * <size> is the 32-bit unsigned size of the next name=value pair.  name and
 * value are ASCII strings.
 */

request_encoder::request_encoder(void)
{
}

void
request_encoder::set_key(std::string const &key, std::string const &value)
{
	keys[key] = value;
}

bool
request_encoder::send_to(int s)
{
	std::vector<char> buf;
	static std::string eod("\0\0\0\0", 4);

	for (std::map<std::string, std::string>::iterator it = keys.begin(),
	     end = keys.end(); it != end; ++it)
	{
		std::string kv = it->first + '=' + it->second;
		uint32_t len = kv.size();
		char lend[4];
		lend[0] = (len & 0xFF000000) >> 24;
		lend[1] = (len & 0x00FF0000) >> 16;
		lend[2] = (len & 0x0000FF00) >> 8;
		lend[3] = (len & 0x000000FF);
		std::cout << boost::format("send %d/%d/%d/%d\n") % (int)lend[0] % (int)lend[1] % (int)lend[2] % (int)lend[3];
		buf.insert(buf.end(), lend, lend + 4);
		buf.insert(buf.end(), kv.begin(), kv.end());
	}
	buf.insert(buf.end(), eod.begin(), eod.end());

	if (write(s, &buf[0], buf.size()) < buf.size())
		return false;
	return true;
}

request_decoder::request_decoder(void)
	: done(false)
	, err(false)
{
}

bool
request_decoder::has_key(std::string const &key) const
{
	return keys.find(key) != keys.end();
}

std::string
request_decoder::get_key(std::string const &key) const
{
	std::map<std::string, std::string>::const_iterator it;
	if ((it = keys.find(key)) == keys.end())
		return "";
	return it->second;
}


void
request_decoder::add_data(char const *newdata, std::size_t len)
{
	static std::string eod("\0\0\0\0", 4);
	data.insert(data.end(), newdata, newdata + len);
	if (std::search(data.begin(), data.end(), eod.begin(), eod.end()) != data.end()) {
		done = true;
		decode();
	}

	return;
}

bool
request_decoder::finished(void) const
{
	return done;
}

bool
request_decoder::error(void) const
{
	return err;
}

void
request_decoder::decode(void)
{
	for (;;) {
		if (data.size() < 4)
			return;

		uint32_t len;
		len =	  ((unsigned int)(unsigned char)data[0] << 24)
			| ((unsigned int)(unsigned char)data[1] << 16)
			| ((unsigned int)(unsigned char)data[2] << 8)
			| ((unsigned int)(unsigned char)data[3]);
		std::cout << boost::format("len = %d (%d/%d/%d/%s)\n") % len % (int)data[0] % (int)data[1] % (int)data[2] % (int)data[3];

		if (len == 0)
			return;

		if (len > (data.size() - 4)) {
			err = true;
			return;
		}

		std::string keyval(data.begin() + 4, data.begin() + 4 + len);
		data.erase(data.begin(), data.begin() + 4 + len);

		std::string key, value;
		std::string::size_type n = keyval.find('=');
		if (n == std::string::npos) {
			key = keyval;
			value = "1";
		} else {
			key = keyval.substr(0, n);
			value = keyval.substr(n + 1);
		}
		std::cout << boost::format("key = [%s], value = [%s]\n") % key % value;
		keys[key] = value;
	}
}


