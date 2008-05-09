#include	<iostream>
#include	<fstream>
#include	<cerrno>
#include	<cstring>
#include	<string>
#include	<ios>
#include	<sstream>
#include	<vector>
#include	<iterator>

#include	<sys/types.h>
#include	<sys/socket.h>
#include	<unistd.h>
#include	<netdb.h>

namespace {

	static const int BUFSIZE = 65535;

	char *prognam;
	std::string informat, outformat;
	int width, height;
	std::string server = "localhost";
	int port = 8765;

	void usage();
	int safe_write(int s, void *data, size_t n);
	std::string extract_extension(std::string const &);
	void dump_data(char *data, std::size_t n);

}

int
main(int argc, char **argv)
{
int	c;
	prognam = argv[0];
	while ((c = getopt(argc, argv, "i:o:s:p:w:h:")) != -1) {
		switch (c) {
		case 'i':
			informat = optarg;
			break;
		case 'o':
			outformat = optarg;
			break;
		case 's':
			server = optarg;
			break;
		case 'w':
			width = atoi(optarg);
			break;
		case 'h':
			height = atoi(optarg);
			break;
		case 'p':
			port = atoi(optarg);
			break;
		default:
			usage();
			return 1;
		}
	}
	argc -= optind;
	argv += optind;

	if (argc != 2) {
		usage();
		return 1;
	}

	if (informat.empty())
		informat = extract_extension(argv[0]);
	if (outformat.empty())
		outformat = extract_extension(argv[1]);

	if (informat.empty()) {
		std::cerr << "% No input format specified.\n";
		usage();
		return 1;
	}

	if (outformat.empty()) {
		std::cerr << "% No output format specified.\n";
		usage();
		return 1;
	}

	if (port == 0) {
		std::cerr << "% Invalid port specified.\n";
		usage();
		return 1;
	}

	size_t insize;

	std::ifstream infile(argv[0]);
	if (!infile) {
		std::cerr << "% Cannot open input file " << argv[1] << ": " 
			<< std::strerror(errno) << '\n';
		return 1;
	}

	infile.seekg(0, std::ios_base::end);
	insize = infile.tellg();
	infile.seekg(0);
	infile.clear();

	std::ofstream outfile(argv[1]);
	if (!outfile) {
		std::cerr << "% Cannot open output file " << argv[1] << ": " 
			<< std::strerror(errno) << '\n';
		return 1;
	}

	int sock, i;
	struct addrinfo hints, *res, *r;
	std::memset(&hints, 0, sizeof(hints));
	hints.ai_socktype = SOCK_STREAM;
	char ports[6];
	snprintf(ports, sizeof ports, "%d", port);

	if ((i = getaddrinfo(server.c_str(), ports, &hints, &res)) != 0) {
		std::cerr << "% Cannot resolve " << server << ':' <<
			port << ": " << gai_strerror(i) << '\n';
		return 1;
	}

	for (r = res; r; r = r->ai_next) {
		char hostname[NI_MAXHOST + 1], portname[NI_MAXSERV + 1];
		getnameinfo(r->ai_addr, r->ai_addrlen, 
				hostname, sizeof(hostname),
				portname, sizeof(portname),
				NI_NUMERICHOST);

		std::string canon;
		if (r->ai_canonname)
			canon = r->ai_canonname;
		else
			canon = hostname;

		std::cerr << "% Trying " << canon << " (" <<
			hostname << ") port " << portname << "... " << std::flush;
		if ((sock = socket(r->ai_family, r->ai_socktype, r->ai_protocol)) == -1) {
			std::cerr << "failed: " << std::strerror(errno) << '\n';
			continue;
		}

		if (connect(sock, r->ai_addr, r->ai_addrlen) == -1) {
			std::cerr << "failed: " << std::strerror(errno) << '\n';
			close(sock);
			continue;
		}

		std::cerr << "connected.\n";
		freeaddrinfo(res);
		break;
	}

	std::cerr << "% Writing input file to server... " << std::flush;

	std::ostringstream strm;
	strm 	<< "INFORMAT " << informat << "\r\n"
		<< "OUTFORMAT " << outformat << "\r\n";

	if (height)
		strm << "HEIGHT " << height << "\r\n";
	if (width)
		strm << "WIDTH " << width << "\r\n";

	strm << "DATA " << insize << "\r\n";
	std::string sd(strm.str());
	std::vector<char> v(sd.begin(), sd.end());
	if (safe_write(sock, &v[0], v.size()) == -1)
		return 1;

	std::vector<char> buf(BUFSIZE);
	std::size_t wr = 0;
	for (;;) {
		infile.read(&buf[0], BUFSIZE);
		if (infile.gcount()) 
			safe_write(sock, &buf[0], infile.gcount());
		wr += infile.gcount();
		insize -= infile.gcount();

		if (insize < 0) {
			std::cerr << "input file was too long!\n";
			return 1;
		}

		if (!infile)
			break;
	}

	if (insize) {
		std::cerr << "input file was too short!\n";
		return 1;
	}

	std::cerr << "done, " << wr << " bytes\n";
	std::cerr << "% Waiting for reply...";

	enum {
		READING_STATUS,
		READING_CHUNK_SIZE,
		READING_CHUNK
	} state = READING_STATUS;
	int chunksize;
	int bufpos = 0;
	int buflen = 0;
	ssize_t z;
	std::string s;
	std::vector<char>::iterator it, bufstart, bufend;
	static std::string const rn = "\r\n";
	std::size_t outsize = 0;

	for (;;) {
		if (buflen == 0) {
			z = read(sock, &buf[0], buf.size());
			if (z == -1) {
				std::cerr << "% Read error from server: "
					<< std::strerror(errno) << '\n';
				return 1;
			} else if (z == 0) {
				std::cerr << "% Unexpected EOF from server.\n";
				return 1;
			}

			//dump_data(&buf[0], z);
			buflen = z;
			bufpos = 0;
		}

		bufstart = buf.begin() + bufpos;
		bufend = buf.begin() + buflen - bufpos;

		switch (state) {
		case READING_STATUS:
			it = std::search(bufstart, bufend,
					rn.begin(), rn.end());
			if (it != bufend) {
				int len = std::distance(bufstart, it);
				s.insert(s.end(), bufstart, it);
				bufpos += len + 2;
				buflen -= len + 2;
				if (s == "OK") {
					std::cout << "ok.\n";
				} else {
					if (s.substr(0, 5) == "ERROR") {
						std::cout << "error: " << s.substr(6) << '\n';
					} else {
						std::cout << "error: unknown status\n";
					}
					return 1;
				}

				state = READING_CHUNK_SIZE;
				s = "";
				continue;
			} else {
				if (s.size() + buflen > 8192) {
					std::cout << "error: header too long\n";
					return 1;
				}

				s.insert(s.end(), bufstart, bufend);
				buflen = bufpos = 0;
			}
			break;

		case READING_CHUNK_SIZE:
			i = 4 - s.size();
			while (i && buflen) {
				s += buf[bufpos];
				bufpos++;
				buflen--;
				i--;
			}

			if (i == 0) {
				chunksize = std::strtol(s.c_str(), NULL, 16);
				if (chunksize == 0)
					goto done;
				state = READING_CHUNK;
				s = "";
			}
			continue;

		case READING_CHUNK:
			it = bufstart + std::min(chunksize, buflen);
			std::copy(bufstart, it, std::ostream_iterator<char>(outfile));
			outsize += std::distance(bufstart, it);

			if (buflen >= chunksize) {
				/* finished this chunk */
				buflen -= chunksize;
				bufpos += chunksize;
				state = READING_CHUNK_SIZE;
			} else {
				chunksize -= buflen;
				bufpos = buflen = 0;

				if (chunksize == 0) {
					state = READING_CHUNK_SIZE;
					continue;
				}
			}
			continue;

		default:
			abort();
		}
	}
done:;

	std::cerr << "% Wrote " << outsize << " bytes to " << argv[1] << '\n';
}

namespace {

void 
usage()
{
	std::cerr << "usage: " << prognam << " [-i <informat>] [-o <outformat>] [-p port] [-s server] [-w width] [-h height] <infile> <outfile>\n";
}

int
safe_write(int s, void *data, size_t n)
{
	ssize_t written;
	if ((written = write(s, data, n)) < n) {
		if (written == -1) 
			std::cerr << "% Write error to server: " << 
				std::strerror(errno) << '\n';
		else
			std::cerr << "% Short write to server.\n";
		return -1;
	}

	return 0;
}

std::string
extract_extension(std::string const &fname)
{
	std::string::size_type n;
	if ((n = fname.rfind('.')) == std::string::npos)
		return "";
	return fname.substr(n + 1);
}

void
dump_data(char *s, std::size_t n) 
{
	while (n--) {
		std::printf("%02x", (int)(unsigned char)*s);
		s++;
	}
}

}
