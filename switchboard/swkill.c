/* Licensed to the Apache Software Foundation (ASF) under one or more
 * contributor license agreements.  See the NOTICE file distributed with
 * this work for additional information regarding copyright ownership.
 * The ASF licenses this file to You under the Apache License, Version 2.0
 * (the "License"); you may not use this file except in compliance with
 * the License.  You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/*
 * suexec.c -- "Wrapper" support program for suEXEC behaviour for Apache
 *
 ***********************************************************************
 *
 * NOTE! : DO NOT edit this code!!!  Unless you know what you are doing,
 *         editing this code might open up your system in unexpected
 *         ways to would-be crackers.  Every precaution has been taken
 *         to make this code as safe as possible; alter it at your own
 *         risk.
 *
 ***********************************************************************
 *
 *
 */

#define SB_SUEXEC_UMASK 022

#include <sys/param.h>
#include <sys/stat.h>
#include <sys/types.h>
#include <string.h>
#include <time.h>
#include <unistd.h>
#include <stdio.h>
#include <stdarg.h>
#include <stdlib.h>
#include <errno.h>

#include <pwd.h>
#include <grp.h>

#if defined(PATH_MAX)
#define SB_MAXPATH PATH_MAX
#elif defined(MAXPATHLEN)
#define SB_MAXPATH MAXPATHLEN
#else
#define SB_MAXPATH 8192
#endif

#define SB_ENVBUF 256

static FILE *log = NULL;

static void err_output(int is_error, const char *fmt, va_list ap)
{
#ifdef SB_LOG_EXEC
    time_t timevar;
    struct tm *lt;

    if (!log) {
        if ((log = fopen(SB_LOG_EXEC, "a")) == NULL) {
            fprintf(stderr, "suexec failure: could not open log file\n");
            perror("fopen");
            exit(1);
        }
    }

    if (is_error) {
        fprintf(stderr, "suexec policy violation: see suexec log for more "
                        "details\n");
    }

    time(&timevar);
    lt = localtime(&timevar);

    fprintf(log, "[%d-%.2d-%.2d %.2d:%.2d:%.2d]: swkill: ",
            lt->tm_year + 1900, lt->tm_mon + 1, lt->tm_mday,
            lt->tm_hour, lt->tm_min, lt->tm_sec);

    vfprintf(log, fmt, ap);

    fflush(log);
#endif /* SB_LOG_EXEC */
    return;
}

static void log_err(const char *fmt,...)
{
#ifdef SB_LOG_EXEC
    va_list ap;

    va_start(ap, fmt);
    err_output(1, fmt, ap); /* 1 == is_error */
    va_end(ap);
#endif /* SB_LOG_EXEC */
    return;
}

static void log_no_err(const char *fmt,...)
{
#ifdef SB_LOG_EXEC
    va_list ap;

    va_start(ap, fmt);
    err_output(0, fmt, ap); /* 0 == !is_error */
    va_end(ap);
#endif /* SB_LOG_EXEC */
    return;
}

int main(int argc, char *argv[])
{
    char *prog;
    char *target_uname, *target_gname;
    char *actual_uname, *actual_gname;
    uid_t uid;              /* user information          */
    gid_t gid;              /* target group placeholder  */
    pid_t target_pid;
    struct passwd *pw;      /* password entry holder     */
    struct group *gr;       /* group entry holder        */

    prog = argv[0];
    /*
     * Check existence/validity of the UID of the user
     * running this program.  Error out if invalid.
     */
    uid = getuid();
    if ((pw = getpwuid(uid)) == NULL) {
        log_err("crit: invalid uid: (%ld)\n", uid);
        exit(102);
    }

    /*
     * If there are a proper number of arguments, set
     * all of them to variables.  Otherwise, error out.
     */
    if (argc < 4) {
        log_err("too few arguments\n");
        exit(101);
    }
    target_uname = argv[1];
    target_gname = argv[2];
    target_pid = atoi(argv[3]);

    if (target_pid < 1) {
        log_err("invalid target pid (%s)\n", argv[3]);
        exit(102);
    }

    /*
     * Check to see if the user running this program
     * is the user allowed to do so as defined in
     * suexec.h.  If not the allowed user, error out.
     */
    if (strcmp(SB_USER, pw->pw_name)) {
        log_err("user mismatch (%s instead of %s)\n", pw->pw_name, SB_USER);
        exit(103);
    }

    /*
     * Error out if the target username is invalid.
     */
    if (strspn(target_uname, "1234567890") != strlen(target_uname)) {
        if ((pw = getpwnam(target_uname)) == NULL) {
            log_err("invalid target user name: (%s)\n", target_uname);
            exit(105);
        }
    }
    else {
        if ((pw = getpwuid(atoi(target_uname))) == NULL) {
            log_err("invalid target user id: (%s)\n", target_uname);
            exit(121);
        }
    }

    /*
     * Error out if the target group name is invalid.
     */
    if (strspn(target_gname, "1234567890") != strlen(target_gname)) {
        if ((gr = getgrnam(target_gname)) == NULL) {
            log_err("invalid target group name: (%s)\n", target_gname);
            exit(106);
        }
    }
    else {
        if ((gr = getgrgid(atoi(target_gname))) == NULL) {
            log_err("invalid target group id: (%s)\n", target_gname);
            exit(106);
        }
    }
    gid = gr->gr_gid;
    actual_gname = strdup(gr->gr_name);

    /*
     * Save these for later since initgroups will hose the struct
     */
    uid = pw->pw_uid;
    actual_uname = strdup(pw->pw_name);

    /*
     * Error out if attempt is made to execute as root or as
     * a UID less than SB_UID_MIN.  Tsk tsk.
     */
    if ((uid == 0) || (uid < SB_UID_MIN)) {
        log_err("cannot run as forbidden uid (%d)\n", uid);
        exit(107);
    }

    /*
     * Error out if attempt is made to execute as root group
     * or as a GID less than SB_GID_MIN.  Tsk tsk.
     */
    if ((gid == 0) || (gid < SB_GID_MIN)) {
        log_err("cannot run as forbidden gid (%d)\n", gid);
        exit(108);
    }

    /*
     * Change UID/GID here so that the following tests work over NFS.
     *
     * Initialize the group access list for the target user,
     * and setgid() to the target group. If unsuccessful, error out.
     */
    if (((setgid(gid)) != 0) || (initgroups(actual_uname, gid) != 0)) {
        log_err("failed to setgid (%ld)\n", gid);
        exit(109);
    }

    /*
     * setuid() to the target user.  Error out on fail.
     */
    if ((setuid(uid)) != 0) {
        log_err("failed to setuid (%ld)\n", uid);
        exit(110);
    }

    kill(target_pid, 9);
    exit(0);
}
