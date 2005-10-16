/*
 * Devtree: print Solaris device tree as CGI
 * Copyright 2005 Kate Turner.
 *
 * The contents of this file are subject to the terms of the
 * Common Development and Distribution License, Version 1.0 only
 * (the "License").  You may not use this file except in compliance
 * with the License.
 *
 * You can obtain a copy of the license at usr/src/OPENSOLARIS.LICENSE
 * or http://www.opensolaris.org/os/licensing.
 * See the License for the specific language governing permissions
 * and limitations under the License.
 *
 * When distributing Covered Code, include this CDDL HEADER in each
 * file and include the License file at usr/src/OPENSOLARIS.LICENSE.
 * If applicable, add the following below this CDDL HEADER, with the
 * fields enclosed by brackets "[]" replaced with your own identifying
 * information: Portions Copyright [yyyy] [name of copyright owner]
 *
 */

/*
 * Copyright 2005 Sun Microsystems, Inc.  All rights reserved.
 * Use is subject to license terms.
 */

/*      Copyright (c) 1984, 1986, 1987, 1988, 1989 AT&T */
/*        All Rights Reserved   */

#pragma ident "$Id$"

#include <sys/utsname.h>
#include <sys/systeminfo.h>

#include <stdio.h>
#include <stdlib.h>
#include <errno.h>
#include <string.h>
#include <ctype.h>
#include <libdevinfo.h>
#include <unistd.h>

void		output_start	(void);
void		output_end	(void);
void		safeprint	(const char *);
void		prtnode		(di_node_t, int);
di_node_t	dinode;

int
main(ac, av)
int	  ac;
char	**av;
{
char		*query;
char		 hw_provider[SYS_NMLN];
int		 i;
struct utsname	 uts;
long		 pagesize, npages;
	if ((query = getenv("QUERY_STRING")) == NULL) {
		output_start();
		printf("<p>No query string</p>");
		output_end();
		return 0;
	}
	if (!strlen(query))
		query = "/";
	output_start();
	i = sysinfo(SI_HW_PROVIDER, hw_provider, sizeof (hw_provider));
	/*
	 * If 0 bytes are returned (the system returns '1', for the \0),
	 * we're probably on x86, and there has been no si-hw-provider
	 * set in /etc/bootrc, so just default to Sun.
	 */
	if (i <= 1) {
		strncpy(hw_provider, "Sun Microsystems",
			sizeof (hw_provider));
	} else {
		/*
		* Provide backward compatibility by stripping out the _.
		*/
		if (strcmp(hw_provider, "Sun_Microsystems") == 0)
			hw_provider[3] = ' ';
	}
	uname(&uts);
	printf("<p>System configuration: ");
	safeprint(hw_provider);
	printf(" ");
	safeprint(uts.machine);
	printf("<br>Memory size: ");
	pagesize = sysconf(_SC_PAGESIZE);
	npages = sysconf(_SC_PHYS_PAGES);
	if (pagesize == -1 || npages == -1)
		printf("unable to determine\n");
	else {
		const int64_t kbyte = 1024;
		const int64_t mbyte = 1024 * 1024;
		int64_t ii = (int64_t)pagesize * npages;
		if (ii >= mbyte)
			printf("%ld Megabytes\n", (long)((ii+mbyte-1) / mbyte));
		else
			printf("%ld Kilobytes\n", (long)((ii+kbyte-1) / kbyte));
	}

	printf("</p>");

	if ((dinode = di_init(query, DINFOCPYALL)) == DI_NODE_NIL) {
		printf("<p>Error: device path <tt>");
		safeprint(query);
		printf("</tt> not found: %s\n", strerror(errno));
		output_end();
		return 0;
	}
	printf("<h2><tt>");
	safeprint(query);
	printf("</tt></h2>");
	printf("<ul>");
	prtnode(dinode, 1);
	printf("</ul>");
	output_end();
	di_fini(dinode);
}

void
output_start(void)
{
	printf("Content-Type: text/html\n\n");
	printf("<html>");
	printf("<body>");
}

void
output_end(void)
{
	printf("</body></html>\n");
}

void
safeprint(s)
const char	*s;
{
	for(; *s; s++) {
		switch (*s) {
		case '>':
			printf("&gt;");
			break;
		case '<':
			printf("&lt;");
			break;
		case '&':
			printf("&amp;");
			break;
		case '"':
			printf("&dquot;");
			break;
		case '\'':
			printf("&quot;");
			break;
		default:
			fputc(*s, stdout);
			break;
		}
	}
}

void
prtnode(node, verb)
di_node_t	 node;
int		 verb;
{
di_node_t	cld;
char		*s, *t;
	printf("<li>");
	s = strdup(di_devfs_path(node));
	if (verb && strcmp(s, "/") && (t = strrchr(s, '/')) != NULL) {
		*t = '\0';
		printf("<a href=\"?");
		safeprint(s);
		printf("\">(up one level)</a><br>");
	}
	free(s);
	printf("<a href=\"?");
	safeprint(di_devfs_path(node));
	printf("\">");
	safeprint(di_node_name(node));
	printf("</a> (");
	if (di_driver_name(node) == NULL)
		printf("no driver attached");
	else {
		safeprint(di_driver_name(node));
	}
	if (di_instance(node) != -1)
		printf(", instance #%d", di_instance(node));
	printf(")");
	if (verb) {
	di_prop_t	 prop;
	int		*ival, n, i, ok;
	int64_t		*i64val;
	uchar_t		*cdata;
	char		*sval;
		printf("<table><tr><th>property</th><th>value</th></tr>");
		for (prop = di_prop_next(node, DI_PROP_NIL); prop;
		     prop = di_prop_next(node, prop)) {
			printf("<tr><td><tt>");
			safeprint(di_prop_name(prop));
			printf("</tt></td><td>");
			switch(di_prop_type(prop)) {
			case DI_PROP_TYPE_BOOLEAN:
				printf("TRUE");
				break;
			case DI_PROP_TYPE_INT:
				n = di_prop_ints(prop, &ival);
				for (i = 0; i < n; ++i) {
					if (i) printf(", ");
					printf("%d", ival[i]);
				}
				break;
			case DI_PROP_TYPE_INT64:
				n = di_prop_int64(prop, &i64val);
				for (i = 0; i < n; ++i) {
					if (i) printf(", ");
					printf("%lld", i64val[i]);
				}
				break;
			case DI_PROP_TYPE_STRING:
				n = di_prop_strings(prop, &sval);
				printf("<tt>");
				for (i = 0; i < n; ++i) {
					if (i) printf(", ");
					printf("\"");
					safeprint(sval);
					sval += strlen(sval) + 1;
					printf("\"");
				}
				printf("</tt>");
				break;
			case DI_PROP_TYPE_BYTE:
				n = di_prop_bytes(prop, &cdata);
				printf("<tt>");
				for (i = 0; i < n; ++i) {
					printf("%02x", cdata[i]);
				}
				printf("</tt>");
				break;	
			case DI_PROP_TYPE_UNDEF_IT:
				printf("(undefined)");
				break;
			case DI_PROP_TYPE_UNKNOWN:
				n = di_prop_bytes(prop, &cdata);
				for (i = ok = 1; i < n; ++i) {
					if (cdata[i] == '\0' && i == (n - 1))
						break;
					if (!isprint(cdata[i])) {
						ok = 0;
						break;
					}
				}
				printf("<tt>");
				if (ok) {
					safeprint(cdata);
				} else {
					for (i = 0; i < n; ++i)
						printf("%02x", cdata[i]);
				}
				printf("</tt>");
				break;
			default:
				printf("unk type");
				break;
			}
				
			printf("</td></tr>");
		}
		printf("</table>");
	}
	printf("<ul>");
	for (cld = di_child_node(node); cld; cld = di_sibling_node(cld)) {
		prtnode(cld, 0);
	}
	printf("</ul></li>");
}

