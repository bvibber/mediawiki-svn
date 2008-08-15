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

#include	"request_thread.h"
#include	"fcgi.h"
#include	"process_factory.h"
#include	"util.h"

namespace {

/*
 * This is the number of idle threads, not total.
 */
#if 0
int nthreads;
pthread_mutex_t nthreads_lock = PTHREAD_MUTEX_INITIALIZER;

work_queue<request_thread *> work;
#endif

extern "C" void*
do_start_thread(void *arg) {
#if 0
	for (;;) {
		request_thread *req = work.wait();

		try {
			req->start_request();
		} catch (std::exception &e) {
			std::fprintf(stderr, "exception handling request: %s\n", e.what());
		}

		delete req;

#if 0
		pthread_mutex_lock(&nthreads_lock);
		nthreads++;
		pthread_mutex_unlock(&nthreads_lock);
#endif
		__sync_fetch_and_add(&nthreads, 1);
	}
#endif	
	request_thread *req = static_cast<request_thread *>(arg);
	try {
		req->start_request();
	} catch (std::exception &e) {
		std::fprintf(stderr, "exception handling request: %s\n", e.what());
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
#if 0
	pthread_mutex_lock(&nthreads_lock);
	if (nthreads == 0) {
#endif
		pthread_attr_t attr;
		pthread_attr_init(&attr);
		pthread_attr_setdetachstate(&attr, PTHREAD_CREATE_DETACHED);
		pthread_create(&tid_, &attr, &do_start_thread, static_cast<void *>(this));
#if 0
	} else
		nthreads--;
	pthread_mutex_unlock(&nthreads_lock);

	work.add_work(this);
#endif
}

void
request_thread::start_request()
{
	fcgi::record rec;

	/*
	 * Start by parsing the request header.
	 */
	fcgi::read_fcgi_record(fd_, &rec);

	if (rec.version() != 1)
		throw request_exception("unknown FCGI version");

	rid_ = rec.request_id();

	switch (rec.type()) {
	case fcgi::rectype::begin_request:
		handle_normal_request(rec);
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

	/*
	 * Now we should receive at least one params.
	 */
	std::vector<unsigned char> paramdata;

	for (;;) {
		fcgi::read_fcgi_record(fd_, &rec);

		if (rec.type() != fcgi::rectype::params)
			throw request_exception("unexpected request type at this point");

		if (rec.content_length() == 0)
			break;

		paramdata.insert(paramdata.end(), rec.contentData.begin(), rec.contentData.end());
	}

	/*
	 * And at least one stdin.
	 */
	std::vector<unsigned char> stdin_;

	for (;;) {
		fcgi::read_fcgi_record(fd_, &rec);

		if (rec.type() != fcgi::rectype::stdin_)
			throw request_exception("unexpected request type at this point");

		if (rec.content_length() == 0)
			break;

		stdin_.insert(stdin_.end(), rec.contentData.begin(), rec.contentData.end());
	}

	/*
	 * Handle the actual request.
	 */
	std::map<std::string, std::string> params;
	fcgi::decode_params(paramdata.begin(), paramdata.end(), std::inserter(params, params.begin()));

#if 0
	for (std::map<std::string, std::string>::iterator
			it = params.begin(), end = params.end();
			it != end; ++it)
	{
		std::cout << "[" << it->first << "] = [" << it->second << "]\n";
	}
#endif

	if ((cfd_ = socket(AF_UNIX, SOCK_STREAM, 0)) == -1)
		throw request_exception("unix socket creation failed");

	fcntl(cfd_, F_SETFD, FD_CLOEXEC);
	int r = 0, tries = 0;
	static int const max_tries = 10;

	do {
		process_ = process_factory::instance().get_process(params);
		if ((r = process_->connect(cfd_)) == -1) {
			if (errno != ECONNREFUSED)
				throw request_exception("can't create PHP");
		} else
			break;
	} while (++tries < max_tries);

	if (r == -1)
		throw request_exception("can't create PHP (too many tries)");

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
	fcgi::write_fcgi_record(cfd_, r_begin);

	std::vector<unsigned char> newparams;
	fcgi::encode_params(params.begin(), params.end(), std::back_inserter(newparams));
	r_params.request_id(rid_);
	r_params.type_ = fcgi::rectype::params;
	r_params.contentData.swap(newparams);
	//r_params.contentData.swap(paramdata);
	r_params.content_length(r_params.contentData.size());
	fcgi::write_fcgi_record(cfd_, r_params);

	if (!r_params.contentData.empty()) {
		r_params.contentData.clear();
		r_params.content_length(0);
		fcgi::write_fcgi_record(cfd_, r_params);
	}

	r_stdin.request_id(rid_);
	r_stdin.type_ = fcgi::rectype::stdin_;
	r_stdin.contentData.swap(stdin_);
	r_stdin.content_length(r_stdin.contentData.size());
	fcgi::write_fcgi_record(cfd_, r_stdin);

	if (!r_stdin.contentData.empty()) {
		r_stdin.contentData.clear();
		r_stdin.content_length(0);
		fcgi::write_fcgi_record(cfd_, r_stdin);
	}

	/*
	 * Now read response requests and forward them to the server.
	 */
	fcgi::record resp;
	for (;;) {
		if (!fcgi::read_fcgi_record(cfd_, &resp))
			throw request_exception("couldn't read record from child");

		if (resp.request_id() != rid_)
			throw request_exception("child sent incorrect request id");

		fcgi::write_fcgi_record(fd_, resp);
		if (resp.type() == fcgi::rectype::end_request)
			break;
	}

	close(cfd_);
	cfd_ = -1;
	process_factory::instance().release_process(process_);
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
	fcgi::write_fcgi_record(fd_, rec);
}
