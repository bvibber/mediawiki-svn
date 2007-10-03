/* fast_counter - squid log aggregator
 * Copyright (C) 2007  Gregory Maxwell<gmaxwell@wikimedia.org>
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
 * Compilation:
 *      gcc -static -O3 -o fast_counter fast_counter.c -lJudy -lm
 *
 * Usage:
 *      ./fast_counter factor hourlythreshold dailythreshold weeklythreshold
 *      Log input on stdin expected as described at https://wikitech.leuksman.com/view/Squid_log_format
 *	Threshold sets the number of hits required for an object before it is output. I expect weekly to be set to
 *	1 while hourly may be left rather high to just capture the latest fads. :)
 *
 * Output:
 *	A set of files named cnt_num_timeending where num is the time interval stored in the file. 
 *
 * Todo:
 *	* Output to a database rather then a bunch of files.
 *	* Add a sleeptime to the output function to avoid pounding the database too hard all at once.
 *	* Make normalization code not suck. (I obviously don't know what I'm doing)
 *	* Catch sigchild in order to try freeing the old agg arrays at the moment their dump
 *	  children exit.
 *	  Right now they are freed right after forking which no doubt causes
 *	  excess copying and TLB thrashing, but I tried deferring the free 
 *	  until just before the next child started but found that it performed
 *	  worse probably due to cold cache effects. 
 */

#include <stdio.h>
#include <string.h>
#include <ctype.h>
#include <errno.h>
#include <math.h>
#include <unistd.h>
#include <sys/wait.h>

#include <Judy.h>

#define MAXINPUT 100000

#define GETFIELD 7
#define TIMEFIELD 2
#define URLFIELD 8

uint8_t inbuffer[MAXINPUT];
uint8_t buffer[MAXINPUT];

void
dumpAgg (Pvoid_t agg, int aggType, int thresh, int ftime, int ltime)
{

  PWord_t value;
  char fname[256];
  FILE *fp = (FILE *) NULL;
  long int totalh = 0;
  long int totalp = 0;

  snprintf (fname, 256, "cnt_%d_%d", aggType, ltime);

  buffer[0] = '\0';
  JSLF (value, agg, buffer);
  while (value != NULL)
    {
      if (*value >= thresh)
	{
	  totalp++;
	  totalh += *value;
	  if (fp == NULL)
	    {
	      fp = fopen (fname, "w");
	      fprintf (fp,
		       "#Starting: %d\n#Ending: %d\n#Threshold: %d\n#Interval type: %d\n",
		       ftime, ltime, thresh, aggType);
	    }
	  fprintf (fp, "%s\t%lu\n", buffer, *value);
	}
      JSLN (value, agg, buffer);
    }
  if (fp != NULL)
    {
      fprintf (fp, "#Total hits: %lu\n#Total pages: %lu\n", totalh, totalp);
      fclose (fp);
    }
  exit (0);
}


static int
hextoint (char *input)
{
  int Hi;
  int Lo;
  int output;

  Hi = input[0];
  if ('0' <= Hi && Hi <= '9')
    {
      Hi -= '0';
    }
  else if ('a' <= Hi && Hi <= 'f')
    {
      Hi -= ('a' - 10);
    }
  else if ('A' <= Hi && Hi <= 'F')
    {
      Hi -= ('A' - 10);
    }
  Lo = input[1];

  if ('0' <= Lo && Lo <= '9')
    {
      Lo -= '0';
    }
  else if ('a' <= Lo && Lo <= 'f')
    {
      Lo -= ('a' - 10);
    }
  else if ('A' <= Lo && Lo <= 'F')
    {
      Lo -= ('A' - 10);
    }
  output = Lo + (16 * Hi);
  return (output);
}


void
urlDecode (char *input)
{

  char *output;
  int i = 0;

  while (input[i])
    {
      if (input[i] == '+' || input[i] == '_')
	input[i] = ' ';
      i++;
    }

  output = input;
  while (*input)
    {

      if (*input == '%')
	{
	  input++;
	  if (isxdigit (input[0]) && isxdigit (input[1]))
	    {

	      *output++ = (char) hextoint (input);
	      input += 2;
	    }
	}
      else
	{
	  *output++ = *input++;
	}
    }
  *output = '\0';
}

int
extractPage ()
{
  char sep[] = " ";
  char sep2[] = "/";
  char *save_ptr1;
  char *save_ptr2;

  char *hbuf;

  char timestamp[32];
  char host[64];
  char page[256];
  int i;

  char *result = NULL;

  result = strtok_r ((char *) buffer, sep, &save_ptr1);
  i = 0;
  while (result != NULL)
    {
      switch (i)
	{

	case GETFIELD:
	  if (strncmp (result, "GET", 3) != 0)
	    return -1;
	  break;

	case TIMEFIELD:
	  strncpy (timestamp, result, 32);
	  break;

	case URLFIELD:
	  if (strlen (result) <= 9 || strncmp ("http://", result, 7) != 0)
	    return -1;

	  hbuf = strtok_r (result + 7, sep2, &save_ptr2);
	  if (hbuf <= 0 || strncmp (hbuf, "upload.wikimedia.org", 20) == 0)
	    return -1;
	  strncpy (host, hbuf, 64);

	  hbuf = strtok_r (NULL, sep2, &save_ptr2);
	  if (hbuf <= 0 || strncmp (hbuf, "wiki", 20) != 0)
	    return -1;

	  hbuf = strtok_r (NULL, sep2, &save_ptr2);
	  if (hbuf <= 0 || strlen (hbuf) < 1)
	    return -1;
	  urlDecode (hbuf);
	  strncpy (page, hbuf, 256);
	  break;
	}
      if (i >= GETFIELD && i >= TIMEFIELD && i >= URLFIELD)
	break;
      result = strtok_r (NULL, sep, &save_ptr1);
      i++;
    }

  strncpy ((char *) buffer, host, 64);
  strncat ((char *) buffer, ":", 1);
  strncat ((char *) buffer, page, 256);

  return (int) rint (atof (timestamp));
}

int
main (int argc, char *argv[])
{
  Pvoid_t aggHour = (Pvoid_t) NULL;	// hour array
  Pvoid_t aggDay = (Pvoid_t) NULL;	// day array
  Pvoid_t aggWeek = (Pvoid_t) NULL;	// week array

  pid_t aggHourpid = -1;
  pid_t aggDaypid = -1;
  pid_t aggWeekpid = -1;

  int aggHourStart = 0;
  int aggDayStart = 0;
  int aggWeekStart = 0;

  int sampleRate;

  PWord_t value;		// item counter pointer
  Word_t Bytes;
  int linetime;

  if (argc != 5)
    {
      fprintf (stderr, "-EPEBCAK");
      exit (1);
    }

  sampleRate = atoi (argv[1]);

  signal (SIGCHLD, SIG_IGN);

  while (fgets ((char *) buffer, MAXINPUT, stdin) != (char *) NULL)
    {
      linetime = extractPage ();
      if (linetime > 0)
	{
	  if (aggHourStart == 0)
	    aggHourStart = linetime;
	  if (aggDayStart == 0)
	    aggDayStart = linetime;
	  if (aggWeekStart == 0)
	    aggWeekStart = linetime;

	  //Handle Hourly aggregate 
	  if (linetime >= aggHourStart + 3600)
	    {
	      waitpid (aggHourpid, NULL, 1);
	      aggHourpid = fork ();
	      if (aggHourpid == 0)
		{
		  dumpAgg (aggHour, 0, atoi (argv[2]), aggHourStart,
			   aggHourStart + 3600);
		}
	      JSLFA (Bytes, aggHour);	// free array
//        fprintf (stderr,"Freed %lu bytes of memory from hourly aggregate.\n", Bytes);

	      aggHour = NULL;
	      aggHourStart = aggHourStart + 3600;
	    }
	  JSLI (value, aggHour, buffer);
	  if (value == PJERR)
	    {
	      printf ("-EMALLOCBOOM\n");
	      exit (1);
	    }
	  (*value) += sampleRate;

	  //Handle daily aggregate      
	  if (linetime >= aggDayStart + 86400)
	    {
	      waitpid (aggDaypid, NULL, 1);
	      aggDaypid = fork ();
	      if (aggDaypid == 0)
		{
		  dumpAgg (aggDay, 1, atoi (argv[3]), aggDayStart,
			   aggDayStart + 86400);
		}
	      JSLFA (Bytes, aggDay);	// free array
//        fprintf (stderr,"Freed %lu bytes of memory from daily aggregate.\n", Bytes);

	      aggDay = NULL;
	      aggDayStart = aggDayStart + 86400;
	    }
	  JSLI (value, aggDay, buffer);
	  if (value == PJERR)
	    {
	      printf ("-EMALLOCBOOM\n");
	      exit (1);
	    }
	  (*value) += sampleRate;

	  //Handle weekly aggregate      
	  if (linetime >= aggWeekStart + (86400 * 7))
	    {
	      waitpid (aggWeekpid, NULL, 1);
	      aggWeekpid = fork ();
	      if (aggWeekpid == 0)
		{
		  dumpAgg (aggWeek, 2, atoi (argv[4]), aggWeekStart,
			   aggWeekStart + (86400 * 7));
		}
	      JSLFA (Bytes, aggWeek);	// free array
//        fprintf (stderr,"Freed %lu bytes of memory from weekly aggregate.\n", Bytes);

	      aggWeek = NULL;
	      aggWeekStart = aggWeekStart + (86400 * 7);
	    }
	  JSLI (value, aggWeek, buffer);
	  if (value == PJERR)
	    {
	      printf ("-EMALLOCBOOM\n");
	      exit (1);
	    }
	  (*value) += sampleRate;

	}
    }
  waitpid (aggWeekpid, NULL, 1);
  waitpid (aggDaypid, NULL, 1);
  waitpid (aggHourpid, NULL, 1);
  return (0);
}
