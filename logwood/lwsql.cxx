/*
 * Logwood: SQL driver.
 */

#include "lwsql.hxx"

namespace sql {

connection *
connection::create (string const& dbname, string const& dbhost,
		    string const& dbuser, string const& dbpass)
{
	return new connection(dbname, dbhost, dbuser, dbpass);
}


connection::connection (string const& dbname, string const& dbhost,
			string const& dbuser, string const& dbpass)
{
	mysql_init(&this->mconn);

	if (!mysql_real_connect(&this->mconn, dbhost.c_str(), dbuser.c_str(), 
				dbpass.c_str(), dbname.c_str(), 0, NULL, 0))
		throw connection_error(&this->mconn);
}

statement *
connection::prepare (string const& sql)
{
	return new statement(this, sql);
}

connection::~connection (void)
{
	mysql_close(&this->mconn);
}

statement::statement (connection *conn, string const &sql)
{
	this->mstmt = mysql_stmt_init(&conn->mconn);

	if (mysql_stmt_prepare(this->mstmt, sql.data(), sql.length())) {
		statement_error s(this->mstmt);
		mysql_stmt_close(this->mstmt);
		throw s;
	}

	this->mparams = mysql_stmt_param_count(this->mstmt);
	this->mbinddata.resize(this->mparams);
	this->mbinds.resize(this->mparams);

	if ((this->metadata = mysql_stmt_param_metadata(this->mstmt)) == NULL)
		return;
}

statement::~statement (void)
{
	mysql_stmt_close(this->mstmt);
}

template<> void
statement::bind<string> (int pos, string const &value)
{
bind_data	&bd = this->mbinddata[pos];
MYSQL_BIND	&b = this->mbinds[pos];

	std::memset(&b, 0, sizeof(b));
	bd.bd_string = value;
	bd.bd_len = value.length();
	b.buffer = const_cast<char *>(bd.bd_string.data());
	b.buffer_type = MYSQL_TYPE_STRING;
	b.length = &bd.bd_len;
	b.is_null = 0;
}

template<> void
statement::bind<number> (int pos, number const &value)
{
bind_data	&bd = this->mbinddata[pos];
MYSQL_BIND	&b = this->mbinds[pos];

	std::memset(&b, 0, sizeof(b));
	bd.bd_num = value;
	b.buffer = &bd.bd_num;
	b.buffer_type = MYSQL_TYPE_LONGLONG;
	b.length = 0;
	b.is_null = 0;
}

template<> void
statement::bind<unumber> (int pos, unumber const &value)
{
bind_data	&bd = this->mbinddata[pos];
MYSQL_BIND	&b = this->mbinds[pos];

	std::memset(&b, 0, sizeof(b));
	bd.bd_unum = value;
	b.buffer = &bd.bd_unum;
	b.buffer_type = MYSQL_TYPE_LONGLONG;
	b.is_unsigned = 1;
	b.length = 0;
	b.is_null = 0;
}

void
statement::execute (void)
{
	if (mysql_stmt_bind_param(this->mstmt, &this->mbinds[0]))
		throw statement_error(this->mstmt);
	if (mysql_stmt_execute(this->mstmt))
		throw statement_error(this->mstmt);
}

resultset *
statement::result (void)
{
	return new resultset(this->mstmt);
}

resultset::resultset (MYSQL_STMT *stmt_)
: mstmt(stmt_)
, metadata(mysql_stmt_result_metadata(mstmt))
{
	if (this->metadata) {
		this->nfields = mysql_num_fields(this->metadata);
		this->mbinds.resize(this->nfields);
		this->mdata.resize(this->nfields);
		this->mfields.resize(this->nfields);

		std::memset(&this->mbinds[0], 0, this->nfields * sizeof(MYSQL_BIND));
		std::memset(&this->mdata[0], 0, this->nfields * sizeof(struct bind_data));

		for (unsigned int i = 0; i < this->nfields; ++i) {
			this->mfields[i] = mysql_fetch_field(this->metadata);
			this->mbinds[i].length = &this->mdata[i].bd_length;
			this->mbinds[i].is_null = &this->mdata[i].bd_is_null;

			switch (this->mfields[i]->type) {
			case MYSQL_TYPE_VAR_STRING:
			case MYSQL_TYPE_STRING:
				this->mbinds[i].buffer_type = MYSQL_TYPE_STRING;
				this->mdata[i].bd_string = new char[this->mfields[i]->length + 1];
				this->mbinds[i].buffer = this->mdata[i].bd_string;
				this->mbinds[i].buffer_length = this->mfields[i]->length + 1;
				break;

			case MYSQL_TYPE_TINY:
			case MYSQL_TYPE_SHORT:
			case MYSQL_TYPE_LONG:
			case MYSQL_TYPE_INT24:
			case MYSQL_TYPE_LONGLONG:
				if (this->mfields[i]->flags & UNSIGNED_FLAG) {
					this->mbinds[i].buffer_type = MYSQL_TYPE_LONGLONG;
					this->mbinds[i].buffer = &this->mdata[i].bd_unum;
					this->mbinds[i].is_unsigned = 1;
				} else {
					this->mbinds[i].buffer_type = MYSQL_TYPE_LONGLONG;
					this->mbinds[i].buffer = &this->mdata[i].bd_num;
				}
				break;
			}
		}
	}
	mysql_stmt_bind_result(this->mstmt, &this->mbinds[0]);
}

bool
resultset::next (void)
{
	switch (mysql_stmt_fetch(this->mstmt)) {
	case MYSQL_NO_DATA:
		return false;
	case 1:
		throw statement_error(this->mstmt);
	default:
		return true;
	}
}

template<> string
resultset::get<string> (int pos)
{
	return this->mdata[pos].bd_string;
}

template<> number
resultset::get<number> (int pos)
{
	return this->mdata[pos].bd_num;
}

template<> unumber
resultset::get<unumber> (int pos)
{
	return this->mdata[pos].bd_unum;
}

resultset::~resultset (void)
{
	for (int i = 0; i < this->nfields; ++i)
		delete[] this->mdata[i].bd_string;
}

} // ns sql
