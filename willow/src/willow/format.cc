/* @(#) $Id$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * format: typesafe printf-style string formatter.
 */

#if defined __SUNPRO_CC || defined __DECC || defined __HP_cc
# pragma ident "@(#)$Id$"
#endif

#include "format.h"

format::format(string const &str)
	: _fmt(str)
	, _reserve(0)
{
	parse_format_string();
}

void
format::parse_format_string(void)
{
int	i, len = _fmt.length();
	_reserve += len;
	for (i = 0; i < len; ++i) {
		switch (_fmt[i]) {
		case '%':
			if (++i == len)
				throw invalid_format_string();
			switch (_fmt[i]) {
			case 's':
				_argtypes.push_back(at_string);
				break;
			case 'd':
				_argtypes.push_back(at_decint);
				break;
			case 'x':
				_argtypes.push_back(at_hexint);
				break;
			case 'o':
				_argtypes.push_back(at_octint);
				break;
			case 'e':
				_argtypes.push_back(at_errno);
				break;
			default:
				throw invalid_format_string();
			}
			break;
		default:
			break;
		}
	}
}

string
format::str(void) const
{
int	i, len = _fmt.length();
string	result;
int	argn = 0;
	result.reserve(_reserve);
	for (i = 0; i < len; ++i) {
		switch (_fmt[i]) {
		case '%':
			if (++i == len)
				throw invalid_format_string();
			if (_fmt[i] == 'e')
				result += strerror(errno);
			else
				result += _args[argn];
			argn++;
			break;
		default:
			result += _fmt[i];
			break;
		}
	}
	return result;
}

format::operator string (void) const
{
	return str();
}

#ifdef TEST
int
main(int argc, char *argv[])
{
char	**a = &argv[2];
	format o(argv[1]);
	while (*a)
		if (atoi(*a) != 0)
			o % atoi(*a++);
		else
			o % *a++;
	std::cout << o.str() << '\n';
}
#endif
