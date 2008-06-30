
#ifdef HAVE_CONFIG_H
#include "config.h"
#endif

#include "php.h"
#include "php_ini.h"
#include "ext/standard/info.h"
#include "php_wmerrors.h"
#include "php_streams.h" /* for __php_stream_call_depth */
#include "SAPI.h" /* for sapi_module */
#include "ext/standard/php_smart_str.h" /* for smart_str */
#include "ext/standard/html.h" /* for php_escape_html_entities */

void wmerrors_cb(int type, const char *error_filename, const uint error_lineno, const char *format, va_list args);
static void wmerrors_show_message(int type, const char *error_filename, const uint error_lineno, const char *format, va_list args TSRMLS_DC);


ZEND_DECLARE_MODULE_GLOBALS(wmerrors)

static int le_wmerrors;

zend_function_entry wmerrors_functions[] = {
	{NULL, NULL, NULL}
};


zend_module_entry wmerrors_module_entry = {
#if ZEND_MODULE_API_NO >= 20010901
	STANDARD_MODULE_HEADER,
#endif
	"wmerrors",
	wmerrors_functions,
	PHP_MINIT(wmerrors),
	PHP_MSHUTDOWN(wmerrors),
	PHP_RINIT(wmerrors),	
	PHP_RSHUTDOWN(wmerrors),
	PHP_MINFO(wmerrors),
#if ZEND_MODULE_API_NO >= 20010901
	"0.1",
#endif
	STANDARD_MODULE_PROPERTIES
};


#ifdef COMPILE_DL_WMERRORS
ZEND_GET_MODULE(wmerrors)
#endif

PHP_INI_BEGIN()
	STD_PHP_INI_ENTRY("wmerrors.message_file", "", PHP_INI_ALL, OnUpdateString, message_file, zend_wmerrors_globals, wmerrors_globals)
PHP_INI_END()

static void php_wmerrors_init_globals(zend_wmerrors_globals *wmerrors_globals)
{
	wmerrors_globals->message_file = NULL;
}

PHP_MINIT_FUNCTION(wmerrors)
{
	REGISTER_INI_ENTRIES();	
	WMERRORS_G(old_error_cb) = zend_error_cb;
	WMERRORS_G(recursion_guard) = 0;
	zend_error_cb = wmerrors_cb;
	return SUCCESS;
}


PHP_MSHUTDOWN_FUNCTION(wmerrors)
{
	UNREGISTER_INI_ENTRIES();
	zend_error_cb = WMERRORS_G(old_error_cb);
	return SUCCESS;
}



PHP_RINIT_FUNCTION(wmerrors)
{
	return SUCCESS;
}



PHP_RSHUTDOWN_FUNCTION(wmerrors)
{
	return SUCCESS;
}


PHP_MINFO_FUNCTION(wmerrors)
{
	php_info_print_table_start();
	php_info_print_table_row(2, "Custom fatal error pages", "enabled");
	php_info_print_table_end();

}





void wmerrors_cb(int type, const char *error_filename, const uint error_lineno, const char *format, va_list args)
{
	TSRMLS_FETCH();
	
	if ((type != E_ERROR && type != E_CORE_ERROR && type != E_COMPILE_ERROR && type != E_USER_ERROR)
			|| strncmp(sapi_module.name, "apache", 6)
			|| WMERRORS_G(recursion_guard))
	{
		WMERRORS_G(old_error_cb)(type, error_filename, error_lineno, format, args);
		return;
	}
	WMERRORS_G(recursion_guard) = 1;
	/* No more OOM errors for now thanks */
	zend_set_memory_limit((size_t)-1);

	/* Show the message */
	wmerrors_show_message(type, error_filename, error_lineno, format, args TSRMLS_CC);

	/* TODO: improved logging */

	WMERRORS_G(recursion_guard) = 0;
	zend_set_memory_limit(PG(memory_limit));

	/* Pass through */
	WMERRORS_G(old_error_cb)(type, error_filename, error_lineno, format, args);
}

static void wmerrors_show_message(int type, const char *error_filename, const uint error_lineno, const char *format, va_list args TSRMLS_DC)
{
	php_stream *stream;
	char *message, *p;
	int message_len;
	long maxlen = PHP_STREAM_COPY_ALL;
	char * tmp1, *tmp2;
	int tmp1_len, tmp2_len;
	smart_str expanded = {0};

	/* Is there a sane message_file? */
	if (!WMERRORS_G(message_file) || *WMERRORS_G(message_file) == '\0') {
		return;
	}

	/* Open it */
	stream = php_stream_open_wrapper_ex(WMERRORS_G(message_file), "rb", 
			ENFORCE_SAFE_MODE | REPORT_ERRORS, NULL, NULL);
	if (!stream) {
		return;
	}
	
	/* Read the contents */
	message_len = php_stream_copy_to_mem(stream, &message, maxlen, 0);

	/* Replace some tokens */
	for (p = message; p < message + message_len; p++) { 
		if (*p == '$') {
			if (!strncmp(p, "$file", sizeof("$file")-1)) {
				tmp1 = php_escape_html_entities((unsigned char*)error_filename, 
						strlen(error_filename), &tmp1_len, 0, ENT_COMPAT, NULL TSRMLS_CC);
				smart_str_appendl(&expanded, tmp1, tmp1_len);
				efree(tmp1);
				p += sizeof("file") - 1;
			} else if (!strncmp(p, "$line", sizeof("$line")-1)) {
				tmp1_len = spprintf(&tmp1, 0, "%u", error_lineno);
				smart_str_appendl(&expanded, tmp1, tmp1_len);
				efree(tmp1);
				p += sizeof("line") - 1;
			} else if (!strncmp(p, "$message", sizeof("$message")-1)) {
				tmp1_len = vspprintf(&tmp1, 0, format, args);
				tmp2 = php_escape_html_entities((unsigned char*)tmp1, tmp1_len, &tmp2_len, 
						0, ENT_COMPAT, NULL TSRMLS_CC);
				smart_str_appendl(&expanded, tmp2, tmp2_len);
				efree(tmp1);
				efree(tmp2);
				p += sizeof("message") - 1;
			} else {
				smart_str_appendc(&expanded, '$');
			}
		} else {
			smart_str_appendc(&expanded, *p);
		}
	}

	/* Set headers */
	if (!SG(headers_sent)) {
		sapi_header_line ctr = {0};

		ctr.line = "HTTP/1.0 500 Internal Server Error";
		ctr.line_len = strlen(ctr.line);
		sapi_header_op(SAPI_HEADER_REPLACE, &ctr TSRMLS_CC);
	}

	/* Write the message out */
	if (expanded.c) {
		php_write(expanded.c, expanded.len TSRMLS_CC);
	}
	
	/* Clean up */
	smart_str_free(&expanded);
	efree(message);
}


