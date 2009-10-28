/* geoiplogtag
 * Tags log lines (received from stdin) containing IP addresses
 * with the associated country code, using the MaxMind db and library.
 *  
 * Copyright (C) 2009 Mark Bergsma <mark@wikimedia.org>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

#define _GNU_SOURCE
#include <stdio.h>
#include <stdlib.h>
#include <string.h>

#include <GeoIP.h>

/* Maximum length of an IPv4 address */
#define MAX_ADDR_LEN	15

int main(int argc, char *argv[]) {
	size_t linelen = 0;
	ssize_t readlen;
	char *line = NULL, *p, *q, addr[MAX_ADDR_LEN+1];
	int i, fieldidx = 0;
	GeoIP *gi;
	
	/* The (optional) 1st argument is the ip address field index */
	if (argc > 1 && sscanf(argv[1], "%ud", &fieldidx) > 0)
		fieldidx--;
	
	/* Open the GeoIP database as a mmap cached file */
	gi = GeoIP_new(GEOIP_MMAP_CACHE|GEOIP_CHECK_CACHE);

	while ((readlen = getline(&line, &linelen, stdin)) != -1) {
		/* Remove the newline at the end */
		line[readlen-1] = '\0';
		p = line;
		
		/* Find the requested field */
		for (i=0; i<fieldidx; i++) {
			if ((p = strchr(p, ' ')) != NULL)
				p++;
			else
				break;
		};
		
		/* Contain the field and copy to an aux var */
		if (p != NULL && (q = strchrnul(p, ' ')) != NULL && q-p <= MAX_ADDR_LEN) {
			strncpy(addr, p, q-p);
			addr[q-p] = '\0';
			
			/* Output line and add country code to the end */
			printf("%s %s\n", line, GeoIP_country_code_by_addr(gi, addr));
		}
		else {
			/* Output original line unmodified */
			printf("%s\n", line);
		}
	}
	
	free(line);	
	return 0;
}
