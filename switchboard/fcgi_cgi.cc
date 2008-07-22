/* Copyright (c) 2008 River Tarnell <river@wikimedia.org>. */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */
/* $Id$ */

#include	<sys/socket.h>
#include	<sys/un.h>
#include	<netinet/in.h>
#include	<arpa/inet.h>

#include	<cstring>
using std::strerror;	/* for asio */

#include	<pwd.h>

#include	<asio.hpp>
#include	<boost/bind.hpp>
#include	<boost/format.hpp>

#include	"fcgi_cgi.h"
#include	"fcgi_application.h"
#include	"async_read_fcgi_record.h"
#include	"process.h"
#include	"process_factory.h"
#include	"config.h"

using asio::ip::tcp;
using boost::format;

fcgi_cgi::fcgi_cgi(
		int request_id,
		sbcontext &context,
		fcgi_applicationp app,
		fcgi::params &params)
	: context_(context)
	, child_socket_(new fcgi_socket<asio::local::stream_protocol::socket>(context_))
	, app_(app)
	, ref_(context)
	, request_id_(request_id)
	, alive_(true)
	, logger(log4cxx::Logger::getLogger("switchboard.fcgi_cgi"))
{
	LOG4CXX_DEBUG(logger, format("[req=%d] fcgi_cgi@%p is created") 
			% request_id_ % this);
	int i;

	assert(app_);

	std::map<std::string, std::string>::iterator it;

	/*
	 * Trying to find the script from path the env is a mess.  Under SJS 
	 * web server, we take the value from SCRIPT_NAME, and translate it 
	 * into a path on disk using the 'docroot' and 'userdir' configuration 
	 * options.  Under Apache, this doesn't work, because SCRIPT_NAME 
	 * contains garbage.  Instead we take PATH_TRANSLATED, which is the 
	 * on-disk path with the PATH_INFO appended, and remove path components 
	 * from it until we end up with a path which exists.
	 *
	 * Other web servers might require different handling; I haven't tested 
	 * any other than SJS and Apache.
	 */
	if ((it = params.find("PATH_TRANSLATED")) != params.end()) {
		std::string s = it->second;
		struct stat sb;
		while (stat(s.c_str(), &sb) == -1
			&& errno == ENOTDIR)
		{
			std::string::size_type n;
			if ((n = s.rfind('/')) == std::string::npos)
				break;

			s.erase(n);
		}

		if (lstat(s.c_str(), &sb) == 0) {
			script_path_ = s;

			/*
			 * Make sure the path is under the docroot or the 
			 * user's userdir.
			 */
			std::string x = mainconf.docroot + '/';
			if (s.substr(0, x.size()) != x) {
				struct passwd *pwd = getpwuid(sb.st_uid);
				if (pwd == NULL)
					throw creation_failure("script owner doesn't exist");
				x = std::string(pwd->pw_dir) + '/' + mainconf.userdir + '/';
				if (s.substr(0, x.size()) != x)
					throw creation_failure("script not under docroot or userdir");
			}
		}
	}

	if (script_path_.empty()) {
		if ((it = params.find("SCRIPT_NAME")) == params.end())
			throw creation_failure("neither SCRIPT_NAME nor PATH_TRANSLATED specified");

		std::string script_name = it->second;
		if (script_name.empty())
			throw creation_failure("SCRIPT_NAME is empty");

		if (script_name.size() >= 2 &&
		    script_name[0] == '/' && script_name[1] == '~') {
			/*
			 * The format is /~user/path/to/script.php
			 * We need to change it to 
			 * /home/user/public_html/path/to/script.php.
			 */
			std::string username;
			std::string script;
			script_name.erase(script_name.begin(), script_name.begin() + 2);
			if (script_name.empty())
				throw creation_failure("invalid SCRIPT_NAME");
			std::string::size_type n = script_name.find('/');
			username.assign(script_name.begin(), script_name.begin() + n);
			script.assign(script_name.begin() + n + 1, script_name.end());

			struct passwd *pwd;
			if ((pwd = getpwnam(username.c_str())) == NULL)
				throw creation_failure("user does not exist");
			script_path_ = std::string(pwd->pw_dir) + '/' + mainconf.userdir
				+ '/' + script;
		} else {
			/*
			 * Script is relative to docroot.
			 */
			script_path_ = mainconf.docroot + script_name;
		}
		params["SCRIPT_FILENAME"] = script_path_;
	}
}

void
fcgi_cgi::start(boost::function<void (void)> func)
{
	LOG4CXX_DEBUG(logger, "requesting process from factory");
	ref_.lock();
	context_.factory().create_from_filename(
		script_path_,
		boost::bind(&fcgi_cgi::process_ready, 
			shared_from_this(), func, _1));
}

void
fcgi_cgi::process_ready(
	boost::function<void (void)> func,
	processp proc)
{
	ref_.uid(proc->uid());
	ref_.gid(proc->gid());

	if (!alive_)
		return;

	LOG4CXX_DEBUG(logger, "process is ready, connecting");
	process_ = proc;
	process_->connect(child_socket_,
			boost::bind(&fcgi_cgi::connect_done,
				shared_from_this(),
				asio::placeholders::error,
				func));
}

void
fcgi_cgi::connect_done(
	asio::error_code error,
	boost::function<void (void)> func)
{
	if (!alive_)
		return;

	if (error) {
		LOG4CXX_DEBUG(logger,
			format("[req=%d] connection failed, %s; retrying")
			% request_id_ % error.message());
		process_.reset();
		child_socket_.reset(new fcgi_socket<asio::local::stream_protocol::socket>(context_));
		start(func);
		return;
	}

	tcp::socket::non_blocking_io cmd(true);
	child_socket_->socket_impl().io_control(cmd);

	if (fcntl(child_socket_->socket_impl().native(), F_SETFD, FD_CLOEXEC) == -1)
		LOG4CXX_WARN(logger, "connect_done: fcntl(FD_CLOEXEC) failed");

	LOG4CXX_DEBUG(logger, format("[req=%d] connected to child")
			% request_id_);
	child_socket_->async_read_record(
			boost::bind(&fcgi_cgi::handle_child_read, 
				shared_from_this(), _1, _2));

	context_.service().post(func);
}

fcgi_cgi::~fcgi_cgi()
{
	LOG4CXX_DEBUG(logger, format("[req=%d] fcgi_cgi@%p destructed") 
			% request_id_ % this);
	if (process_)
		context_.factory().release(process_);
}

void
fcgi_cgi::record(fcgi::recordp record)
{
	LOG4CXX_DEBUG(logger, 
		format("[req=%d] received a record vers=%d type=%d, fwding to child")
		% request_id_ % (int) record->version % (int) record->type);
	child_socket_->write_record(record,
		boost::bind(&fcgi_cgi::write_done, shared_from_this(), _1));
}

void
fcgi_cgi::record_noflush(fcgi::recordp record)
{
	LOG4CXX_DEBUG(logger,
		format("[req=%d] received a record vers=%d type=%d, fwding to child")
			% request_id_ % (int) record->version % (int) record->type);
	child_socket_->write_record_noflush(record);
}

void
fcgi_cgi::flush()
{
	child_socket_->flush(boost::bind(&fcgi_cgi::write_done, 
				shared_from_this(), _1));
}

void
fcgi_cgi::write_done(asio::error_code error)
{
	if (!alive_)
		return;

	if (!error)
		return;

	if (error == asio::error::operation_aborted) {
		LOG4CXX_DEBUG(logger, "fcgi_cgi::writer_error: operation aborted");
		return;
	}

	LOG4CXX_DEBUG(logger, format("[req=%d] write to child completed with error: %s")
			% request_id_ % error.message());

	process_.reset();

	if (app_)
		app_->destroy();
}

void
fcgi_cgi::handle_child_read(
		fcgi::recordp record,
		asio::error_code error)
{
	if (!alive_)
		return;

	if (error == asio::error::operation_aborted) {
		LOG4CXX_DEBUG(logger, "fcgi_cgi::handle_child_read: operation aborted");
		return;
	}

	assert(app_);

	if (child_socket_->socket_impl().native() == -1) {
		LOG4CXX_DEBUG(logger, "fcgi_cgi::handle_child_read: socket==-1!");
		return;
	}

	if (!record) {
		LOG4CXX_DEBUG(logger, format("child read error = %s") % error.message());
		process_.reset();
		if (error)
			LOG4CXX_DEBUG(logger, format("[req=%d] fcgi_cgi, error reading from child: %s")
					% request_id_ % error.message());
		if (app_)
			app_->destroy();
		return;
	}

	bool passup = false, destroy = false;

	switch (record->type) {
	case fcgi::rectype::abort_request:
		destroy = true;
	case fcgi::rectype::params:
	case fcgi::rectype::stdin_:
	case fcgi::rectype::stdout_:
	case fcgi::rectype::data:
		passup = true;
		break;

	case fcgi::rectype::end_request:
	case fcgi::rectype::begin_request:
	case fcgi::rectype::get_values:
	case fcgi::rectype::get_values_result:
	default:
		destroy = true;
		passup = false;
		break;
	}

	LOG4CXX_DEBUG(logger, format("[req=%d] received record from child, destroy=%d passup=%d")
			% request_id_ % destroy % passup);

	if (record->request_id() != request_id_) {
		LOG4CXX_DEBUG(logger, format("request id doesn't match!")
				% request_id_);
		passup = false;
		destroy = true;
	}

	if (passup)
		app_->record_from_child(record);

	if (destroy)
		this->destroy();
	else
		child_socket_->async_read_record(
			boost::bind(&fcgi_cgi::handle_child_read,
				shared_from_this(), _1, _2));
}

void
fcgi_cgi::destroy()
{
	LOG4CXX_DEBUG(logger, format("[req=%d] cgi@%p, destroy() called") 
			% request_id_ % this);
	alive_ = false;
	close();
	app_->destroy();
}

void
fcgi_cgi::close()
{
	LOG4CXX_DEBUG(logger, format("[req=%d] cgi@%p, close() called") 
			% request_id_ % this);
	alive_ = false;
	child_socket_->close();
}
