/* $Id$ */
/*
 * Six degrees of Wikipedia: Server (request protocol support).
 * This source code is released into the public domain.
 */

#ifndef ENCODE_DECODE_H
#define ENCODE_DECODE_H

#include <string>
#include <map>
#include <vector>

struct request_encoder {
	request_encoder(void);

	void set_key(std::string const &, std::string const &);
	bool send_to(int);

private:
	std::map<std::string, std::string> keys;
};

struct request_decoder {
	request_decoder(void);

	void add_data(char const *data, std::size_t len);
	bool finished(void) const;
	bool error(void) const;
	bool has_key(std::string const &key) const;
	std::string get_key(std::string const &key) const;

private:
	bool done, err;
	void decode(void);
	std::vector<char> data;
	std::map<std::string, std::string> keys;
};


#endif	/* !ENCODE_DECODE_H */
