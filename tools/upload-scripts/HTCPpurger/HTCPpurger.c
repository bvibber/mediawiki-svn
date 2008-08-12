#include <stdio.h>		/* printf(), snprintf() */
#include <stdlib.h>		/* strtol(), exit() */
#include <sys/types.h>
#include <sys/socket.h>		/* socket(), setsockopt(), bind(), recvfrom(), sendto() */
#include <errno.h>		/* perror() */
#include <netinet/in.h>		/* IPPROTO_IP, sockaddr_in, htons(), htonl() */
#include <arpa/inet.h>		/* inet_addr() */
#include <unistd.h>		/* fork(), sleep() */
#include <sys/utsname.h>	/* uname() */
#include <string.h>		/* memset() */
#include <ctype.h>

#define MAXLEN 1024
#define DELAY 2
#define TTL 1
#define MAX(x,y) ( (x) > (y) ? (x) : (y) )
#define MIN(x,y) ( (x) < (y) ? (x) : (y) )

char *opcodestring[] = { "NOP", "TST", "MON", "SET", "CLR" };

void urldecode(char * dest, const char * src, size_t num);

int main( int argc, char *argv[] ) {
	u_int yes = 1;		/* Used with SO_REUSEADDR.  In Linux both u_int */
	/* and u_char are valid. */
	int recv_s;		/* Sockets for sending and receiving. */
	char *prefix;
	int prefixlength = 0;

	struct sockaddr_in mcast_group;
	struct ip_mreq mreq;
	struct utsname name;
	if ( ( argc != 3 ) && ( argc != 5 ) ) {
		fprintf( stderr, "Usage: %s mcast_group port [prefix basedir]\n", argv[0] );
		exit( 1 );
	}
	memset( &mcast_group, 0, sizeof( mcast_group ) );
	mcast_group.sin_family = AF_INET;
	mcast_group.sin_port = htons( ( unsigned short int ) strtol( argv[2], NULL, 0 ) );
	mcast_group.sin_addr.s_addr = inet_addr( argv[1] );

	if ( argc == 5 ) {
		prefix = argv[3];
		prefixlength = strlen( prefix );
		printf( "Changing directory to %s\n", argv[4] );
		if ( chdir( argv[4] ) ) {
			perror( "Can't chdir()" );
			exit( 1 );
		}
	} else {
		prefix = NULL;
	}

	if ( ( recv_s = socket( AF_INET, SOCK_DGRAM, 0 ) ) < 0 ) {
		perror( "recv socket" );
		exit( 1 );
	}
	if ( setsockopt( recv_s, SOL_SOCKET, SO_REUSEADDR, &yes, sizeof( yes ) ) < 0 ) {
		perror( "reuseaddr setsockopt" );
		exit( 1 );
	}
	if ( bind( recv_s, ( struct sockaddr * ) &mcast_group, sizeof( mcast_group ) ) < 0 ) {
		perror( "bind" );
		exit( 1 );
	}
	/* Tell the kernel we want to join that multicast group. */
	mreq.imr_multiaddr = mcast_group.sin_addr;
	mreq.imr_interface.s_addr = htonl( INADDR_ANY );
	if ( setsockopt( recv_s, IPPROTO_IP, IP_ADD_MEMBERSHIP, &mreq, sizeof( mreq ) )
	     < 0 ) {
		perror( "add_membership setsockopt" );
		exit( 1 );
	}
	if ( uname( &name ) < 0 ) {
		perror( "uname" );
		exit( 1 );
	}
	int n;
	socklen_t len;
	struct sockaddr_in from;
	unsigned char message[MAXLEN + 1];
	for ( ;; ) {
		len = sizeof( from );
		if ( ( n = recvfrom( recv_s, message, MAXLEN, 0, ( struct sockaddr * ) &from, &len ) ) < 0 ) {
			perror( "recv" );
			exit( 1 );
		}
		message[n] = 0;	/* null-terminate string */
		if ( prefixlength == 0 ) {
			printf( "%s: Received message from %s.\n", name.nodename, inet_ntoa( from.sin_addr ) );
			int i;
			for ( i = 0; i < n; i++ ) {
				printf( " %2X", message[i] );
				if ( i % 8 == 7 ) {
					printf( "\n" );
				}
			}
			printf( "\n" );
			for ( i = 0; i < n; i++ ) {
				printf( "%c", isprint( message[i] ) ? message[i] : ' ' );
				if ( i % 80 == 79 ) {
					printf( "\n" );
				}
			}
			printf( "\n" );
		}

		uint16_t length = ( message[0] << 8 ) | message[1];
		unsigned char major = message[2];
		unsigned char minor = message[3];
		uint16_t dlength = ( message[4] << 8 ) | message[5];
		unsigned char response = message[6] >> 4;
		unsigned char opcode = message[6] & 0x0f;
		unsigned char f1 = ( message[7] & 0x40 ) >> 6;
		unsigned char rr = ( message[7] & 0x80 ) >> 7;

		if ( opcode == 4 /* CLR */  ) {
			unsigned char reason = ( message[13] & 0x70 ) >> 4;

			uint16_t methodlength = message[14] * 256 + message[15];
			char method[2000];
			strncpy( method, (char*)message + 16, MAX( methodlength, 1999 ) );
			method[MAX( methodlength, 1999 )] = '\0';

			int base = 16 + methodlength;
			uint16_t urilength = message[base] * 256 + message[base + 1];
			char uri[2000];

			/* Check for overlong URI */
			if ( urilength > 1999 ) {
				strncpy( uri, (char*)message + base + 2, 1999 );
				uri[1999] = '\0';
				printf( "URI too long: %s\n", uri );
				continue;
			}

			/* Decode the URI */
			urldecode( uri, (char*)message + base + 2, urilength );

			if ( prefix == NULL
			     || ( urilength >= prefixlength && strncmp( prefix, uri, prefixlength ) == 0 ) ) {
				errno = 0;
				char * path = uri + prefixlength;
				
				if ( strstr( path, "../" ) == path || strstr( path, "/../" ) ) {
					printf( "Error, path contains \"../\": " );
				} else if ( path[0] == '/' ) {
					printf( "Error, leading slash: " );
				} else if ( unlink( path ) ) {
					printf( "Failed on " );
					/*  exit(1); */
				} else {
					printf( "Deleted " );
				}
				printf( "%s\n", path );
			} else {
#ifdef DEBUG
				printf( "Length:      %d\n", length );
				printf( "Version:     %d.%d\n", major, minor );
				printf( "Data length: %d\n", dlength );
				printf( "Opcode:      %d (%s) \n", opcode, opcodestring[opcode] );
				printf( "Response:    %d\n", response );
				printf( "F1:          %d\n", f1 );
				printf( "RR:          %d\n", rr );
				printf( "Trans-ID:    %02x%02x%02x%02x\n", message[8],
					message[9], message[10], message[11] );
				printf( "Reason:      %d\n", reason );
				printf( "Method:      %s (%d)\n", method, methodlength );
				printf( "URI:         %s (%d)\n", uri, urilength );
				printf( "Prefix:      %s (%d)\n", prefix, prefixlength );
				printf( "\n" );
#endif
			}
		}
	}
}

/**
 * Decode a URL-encoded string
 * Adds a null byte to the end of the destination string
 * dest must have enough storage space for num+1 bytes (including null terminator)
 * src is a string of length num
 */
void urldecode(char * dest, const char * src, size_t num) {
	const char * end = src + num;
	char * dummy;
	unsigned char byte;
	char hexbuf[3];
	hexbuf[2] = '\0';
	
	while (src < end) {
		if (*src == '%') {
			if (src + 2 >= end) {
				/* Invalid "%" at end of string, pass through */
				*dest = *src;
			} else {
				hexbuf[0] = *(++src);
				hexbuf[1] = *(++src);
				byte = (unsigned char) strtoul(hexbuf, &dummy, 16);
				*(unsigned char*)dest = byte;
			}
		} else {
			*dest = *src;
		}
		++src;
		++dest;
	}
	*dest = '\0';
}


