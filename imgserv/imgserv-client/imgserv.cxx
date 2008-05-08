#include	<iostream>
#include	<fstream>
#include	<cerrno>
#include	<cstring>
#include	<string>
#include	<ios>
#include	<sstream>
#include	<vector>

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
			server = atoi(optarg);
			break;
		default:
			usage();
			return 1;
		}
	}
	argc -= optind;
	argv += optind;

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

	if (argc < 2) {
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

	/*
	 * The reply is either "OK <size>\r\n<data>", or "ERROR <message>\r\n";
	 */
	int offs = 0;
	std::fill(v.begin(), v.end(), 0);
	for (;;) {
		ssize_t r;
		r = read(sock, &buf[0] + offs, buf.size() - offs - 1);
		if (r == -1) {
			std::cerr << "read error: " <<
				std::strerror(errno) << '\n';
			return 1;
		}

		if (r == 0) {
			std::cerr << "unexpected end of file\n";
			return 1;
		}

		offs += r;

		char *rn = std::strstr(&buf[0], "\r\n");
		if (rn != NULL) {
			if (std::memcmp(&buf[0], "ERROR ", 6) == 0) {
				std::cerr << "server error: "
					<< std::string(&buf[0] + 6, rn)
					<< '\n';
				return 1;
			} else if (std::memcmp(&buf[0], "OK ", 3) == 0) {
				std::cerr << "ok\n";
				outfile.write(rn + 2, 
					(&buf[0] + offs) - (rn + 2));
				break;
			}
		}

		if (offs > 256) {
			std::cerr << "too much garbage before reply.\n";
			return 1;
		}
	}

	ssize_t outsize = 0;
	for (;;) {
		ssize_t n;
		if ((n = read(sock, &buf[0], buf.size())) == -1) {
			std::cerr << "read error: " << 
				std::strerror(errno) << '\n';
			return 1;
		}

		if (n == 0)
			break;

		outsize += n;
		outfile.write(&buf[0], n);
	}

	std::cerr << "% Wrote " << outsize << " bytes to " << argv[1] << '\n';
}

namespace {

void 
usage()
{
	std::cerr << "usage: " << prognam << " -i <informat> -o <outformat> [-p port] [-s server] [-w width] [-h height] <infile> <outfile>\n";
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

}
