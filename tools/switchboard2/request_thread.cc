/* Copyright (c) 2008 River Tarnell <river@wikimedia.org>. */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */
/* $Id$ */

#include	<iostream>
#include	<cerrno>

#include	<sys/types.h>
#include	<sys/socket.h>
#include	<sys/un.h>

#include	<fcntl.h>
#include	<pthread.h>

#include	<boost/format.hpp>

#include	"request_thread.h"
#include	"fcgi.h"
#include	"process_factory.h"
#include	"util.h"
#include	"config.h"

namespace {

extern "C" void*
do_start_thread(void *arg) {
	request_thread *req = static_cast<request_thread *>(arg);
	try {
		req->start_request();
	} catch (std::exception &e) {
		if (mainconf.log_request_errors) {
			log4cxx::LoggerPtr logger(log4cxx::Logger::getLogger("request_thread"));
			LOG4CXX_INFO(logger,
				boost::format("request failed: %s") % e.what());
		}
	}

	delete req;

	return NULL;
}

} // anonymous namespace

request_thread::request_thread(int fd)
	: fd_(fd)
	, rid_(-1)
	, cfd_(-1)
{
}

request_thread::~request_thread()
{
	if (cfd_ != -1)
		close(cfd_);

	if (fd_ != -1)
		close(fd_);
}

void
request_thread::start()
{
	pthread_attr_t attr;
	pthread_attr_init(&attr);
	pthread_attr_setdetachstate(&attr, PTHREAD_CREATE_DETACHED);
	pthread_create(&tid_, &attr, &do_start_thread, static_cast<void *>(this));
}

void
request_thread::start_request()
{
	fcgi::record rec;

	/*
	 * Start by parsing the request header.
	 */
	if (!fcgi::read_fcgi_record(fd_, &rec, mainconf.server_timeout))
		throw errno_exception("error reading initial record");

	if (rec.version() != 1)
		throw request_exception("unknown FCGI version");

	rid_ = rec.request_id();

	switch (rec.type()) {
	case fcgi::rectype::begin_request:
		try {
			handle_normal_request(rec);
		} catch (std::exception &e) {
			if (mainconf.log_request_errors) {
				log4cxx::LoggerPtr logger(log4cxx::Logger::getLogger("request_thread"));
				LOG4CXX_INFO(logger,
					boost::format("request failed: %s") % e.what());
			}

			fcgi::record r;

			r.version_ = 1;
			r.paddingLength_ = 0;
			r.reserved_ = 0;
			r.type_ = fcgi::rectype::stdout_;
			r.request_id(rid_);

			std::string err =
"Status: HTTP/1.1 500 Internal server error\r\n"
"Content-Type: text/html\r\n"
"\r\n"
"<html><head><title>switchboard error</title></head>\r\n"
"<body><p>hi,</p>\r\n"
"<p>i am the PHP switchboard, and i handle PHP requests (like yours) on this server.\r\n"
"i'm afraid i was unable to handle your request.  when i tried, the following\r\n"
"error occurred: <tt>%1%</tt>.</p>\r\n"
"<p>please try your request again in a few minutes.  if it still doesn't work,\r\n"
"you should contact the server administrator and inform him of the problem.</p>\r\n"
"<p>regards,<br> the PHP switchboard.</p>\r\n";
			err = str(boost::format(err) % e.what());

			std::copy(err.begin(), err.end(), std::back_inserter(r.contentData));
			r.content_length(r.contentData.size());
			if (!fcgi::write_fcgi_record(fd_, r, mainconf.server_timeout))
				return;

			r.contentData.clear();
			r.content_length(0);
			if (!fcgi::write_fcgi_record(fd_, r, mainconf.server_timeout))
				return;

			r.type_ = fcgi::rectype::end_request;
			r.contentData.resize(8);
			r.contentData[0] = 0;
			r.contentData[1] = 0;
			r.contentData[2] = 0;
			r.contentData[3] = 0;
			r.contentData[4] = 0;
			r.content_length(8);

			if (!fcgi::write_fcgi_record(fd_, r, mainconf.server_timeout))
				return;
		}

		return;


	case fcgi::rectype::get_values:
		handle_get_values(rec);
		return;

	default:
		throw request_exception("unexpected request type at this point");
	}
}

void
request_thread::handle_normal_request(fcgi::record &initial)
{
	fcgi::record rec;
	fcgi::begin_request_payload *pl = reinterpret_cast<fcgi::begin_request_payload *>(&initial.contentData[0]);

	if (initial.content_length() != 8)
		throw request_exception("begin request had unexpected content length");

	switch (pl->role()) {
	case fcgi::role::responder:
		break;
	
	default:
		throw request_exception("begin_request had unexpected role");
	}

	std::size_t reqsize = 0;

	/*
	 * Now we should receive at least one params.
	 */
	for (;;) {
		if (!fcgi::read_fcgi_record(fd_, &rec, mainconf.server_timeout))
			throw errno_exception("error reading params from server");

		if (rec.type() != fcgi::rectype::params)
			throw request_exception("unexpected request type at this point");

		if (rec.content_length() == 0)
			break;

		if (reqsize + rec.content_length() > mainconf.max_request_size)
			throw request_exception("request too big");
		reqsize += rec.content_length();

		paramdata_.insert(paramdata_.end(), rec.contentData.begin(), rec.contentData.end());
	}

	/*
	 * And at least one stdin.
	 */
	for (;;) {
		if (!fcgi::read_fcgi_record(fd_, &rec, mainconf.server_timeout))
			throw errno_exception("error reading params from server");

		if (rec.type() != fcgi::rectype::stdin_)
			throw request_exception("unexpected request type at this point");

		if (rec.content_length() == 0)
			break;

		if (reqsize + rec.content_length() > mainconf.max_request_size)
			throw request_exception("request too big");
		reqsize += rec.content_length();

		stdin_.insert(stdin_.end(), rec.contentData.begin(), rec.contentData.end());
	}

	/*
	 * Handle the actual request.
	 */
#if 0
	for (std::map<std::string, std::string>::iterator
			it = params.begin(), end = params.end();
			it != end; ++it)
	{
		std::cout << "[" << it->first << "] = [" << it->second << "]\n";
	}
#endif

	int tries = 0;
	static int const max_tries = 10;

	do {
		try {
			if ((cfd_ = socket(AF_UNIX, SOCK_STREAM, 0)) == -1)
				throw errno_exception("unix socket creation failed");
			fcntl(cfd_, F_SETFD, FD_CLOEXEC);

			handle_normal_request_child();

			close(cfd_);
			cfd_ = -1;
			return;
		} catch (errno_exception &e) {
			close(cfd_);
			cfd_ = -1;
			if (tries++ >= max_tries || e.err() == ETIMEDOUT)
				throw e;
		} catch (std::exception &e) {
			close(cfd_);
			cfd_ = -1;
			if (tries++ >= max_tries)
				throw e;
		}
	} while (true);
}

void
request_thread::handle_normal_request_child()
{
	std::map<std::string, std::string> params;
	fcgi::decode_params(paramdata_.begin(), paramdata_.end(), std::inserter(params, params.begin()));

	process_ = process_factory::instance().get_process(params);
	process_releaser p(process_);

	if (process_->connect(cfd_) == -1)
		throw errno_exception("can't create PHP");

	/*
	 * Write the request to the child.
	 */
	fcgi::record r_begin;
	fcgi::record r_params;
	fcgi::record r_stdin;

	r_begin.version_ = r_params.version_ = r_stdin.version_ = 1;
	r_begin.paddingLength_ = r_params.paddingLength_ = r_stdin.paddingLength_ = 0;
	r_begin.reserved_ = r_params.reserved_ = r_stdin.reserved_ = 0;

	r_begin.request_id(rid_);
	r_begin.type_ = fcgi::rectype::begin_request;
	r_begin.content_length(8);
	r_begin.contentData.resize(8);
	r_begin.contentData[0] = 0;
	r_begin.contentData[1] = 1;
	r_begin.contentData[2] = 0;
	if (!fcgi::write_fcgi_record(cfd_, r_begin, mainconf.php_timeout))
		throw errno_exception("error writing begin request to child");

	std::vector<unsigned char> newparams;
	std::vector<unsigned char>::iterator dit, dend;
	fcgi::encode_params(params.begin(), params.end(), std::back_inserter(newparams));
	r_params.request_id(rid_);
	r_params.type_ = fcgi::rectype::params;

	dit = newparams.begin();
	dend = newparams.end();

	while (dit != dend) {
		int now = std::min(65535, (int) std::distance(dit, dend));
		r_params.contentData.resize(now);
		std::copy(dit, dit + now, r_params.contentData.begin());
		r_params.content_length(now);
		if (!fcgi::write_fcgi_record(cfd_, r_params, mainconf.php_timeout))
			throw errno_exception("error writing params to child");

		dit += now;
	}

	r_params.contentData.clear();
	r_params.content_length(0);
	if (!fcgi::write_fcgi_record(cfd_, r_params, mainconf.php_timeout))
		throw errno_exception("error writing end of params to child");

	
	r_stdin.request_id(rid_);
	r_stdin.type_ = fcgi::rectype::stdin_;

	dit = stdin_.begin();
	dend = stdin_.end();

	/*
	 * Only 64K in allowed in one record.
	 */
	while (dit != dend) {
		int now = std::min(65535, (int) std::distance(dit, dend));
		r_stdin.contentData.resize(now);
		std::copy(dit, dit + now, r_stdin.contentData.begin());
		r_stdin.content_length(now);
		if (!fcgi::write_fcgi_record(cfd_, r_stdin, mainconf.php_timeout))
			throw errno_exception("error writing stdin to child");

		dit += now;
	}

	r_stdin.contentData.clear();
	r_stdin.content_length(0);
	if (!fcgi::write_fcgi_record(cfd_, r_stdin, mainconf.php_timeout))
		throw errno_exception("error writing stdin to child");

	if (!r_stdin.contentData.empty()) {
		r_stdin.contentData.clear();
		r_stdin.content_length(0);
		if (!fcgi::write_fcgi_record(cfd_, r_stdin, mainconf.php_timeout))
			throw errno_exception("error writing end of stdin to child");
	}

	/*
	 * Now read response requests and forward them to the server.
	 */
	fcgi::record resp;
	for (;;) {
		if (!fcgi::read_fcgi_record(cfd_, &resp, mainconf.php_timeout))
			throw errno_exception("couldn't read record from child");

		if (resp.request_id() != rid_)
			throw request_exception("child sent incorrect request id");

		if (!fcgi::write_fcgi_record(fd_, resp, mainconf.server_timeout))
			throw errno_exception("couldn't write record to server");

		if (resp.type() == fcgi::rectype::end_request)
			break;
	}

	//process_factory::instance().release_process(process_);
	p.release();
	process_.reset();
}

void
request_thread::handle_get_values(fcgi::record &initial)
{
	std::map<std::string, std::string> request;
	std::map<std::string, std::string> response;

	fcgi::decode_params(initial.contentData.begin(), initial.contentData.end(),
			std::inserter(request, request.begin()));

	for (std::map<std::string, std::string>::iterator
			it = request.begin(), end = request.end();
			it != end; ++it)
	{
		if (it->first == "FCGI_MAX_CONNS")
			response["FCGI_MAX_CONNS"] = "100";
		else if (it->first == "FCGI_MAX_REQS")
			response["FCGI_MAX_REQS"] = "100";
		else if (it->first == "FCGI_MPXS_CONNS")
			response["FCGI_MPXS_CONNS"] = "0";
	}

	fcgi::record rec;
	std::vector<unsigned char> responsedata;
	fcgi::encode_params(response.begin(), response.end(),
			std::back_inserter(rec.contentData));
	rec.version_ = 1;
	rec.type_ = fcgi::rectype::get_values_result;
	rec.requestId0_ = initial.requestId0_;
	rec.requestId1_ = initial.requestId1_;
	rec.reserved_ = 0;
	rec.content_length(rec.contentData.size());
	fcgi::write_fcgi_record(fd_, rec, mainconf.server_timeout);
}
