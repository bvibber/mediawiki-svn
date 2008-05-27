/* Copyright (c) 2008 River Tarnell <river@wikimedia.org>.                   */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */
#include	<pwd.h>
#include	<string.h>

#ifdef HAVE_CONFIG_H
#include "config.h"
#endif

#include	"php.h"
#include	"php_ini.h"
#include	"ext/standard/info.h"
 
#define SESSION_DIR	".php_sessions"

/*ARGSUSED*/
PHP_RINIT_FUNCTION(perusersessionsavepath)
{
struct passwd	*pwd = NULL;
struct stat	 sb;
uid_t		 uid;
char		*path = NULL;
	uid = getuid();
	if ((pwd = getpwuid(uid)) == NULL)
		goto end;
	if ((path = malloc(strlen(pwd->pw_dir) + sizeof(SESSION_DIR) + 1)) == NULL)
		goto end;
	sprintf(path, "%s/%s", pwd->pw_dir, SESSION_DIR);
	if (stat(path, &sb) == -1) {
		if (errno != ENOENT)
			goto end;

		if (mkdir(path, 0700) == -1)
			goto end;
	}

	zend_alter_ini_entry("session.save_path", sizeof("session.save_path"),
				path, strlen(path), PHP_INI_USER, PHP_INI_STAGE_RUNTIME);

end:
	if (pwd)
		endpwent();
	if (path)
		free(path);
	return SUCCESS;
}

/*ARGSUSED*/
PHP_MINIT_FUNCTION(perusersessionsavepath) {
	return SUCCESS;
}

/*ARGSUSED*/
PHP_MSHUTDOWN_FUNCTION(perusersessionsavepath) {
	return SUCCESS;
}

/*ARGSUSED*/
PHP_RSHUTDOWN_FUNCTION(perusersessionsavepath) {
	return SUCCESS;
}

/*ARGSUSED*/
PHP_MINFO_FUNCTION(perusersessionsavepath) {
	php_info_print_table_start();
	php_info_print_table_row(2, "Per-user session save path", "enabled");
	php_info_print_table_end();
}

static function_entry funcs[] = { NULL, NULL, NULL };

zend_module_entry perusersessionsavepath_module_entry = {
        STANDARD_MODULE_HEADER,
        "perusersessionsavepath",
        funcs,
        PHP_MINIT(perusersessionsavepath),
	PHP_MSHUTDOWN(perusersessionsavepath),
	PHP_RINIT(perusersessionsavepath),
	PHP_RSHUTDOWN(perusersessionsavepath),
	PHP_MINFO(perusersessionsavepath),
        "1.0",
        STANDARD_MODULE_PROPERTIES
};


#ifdef COMPILE_DL_PERUSERSESSIONSAVEPATH
ZEND_GET_MODULE(perusersessionsavepath)
#endif

