
#ifndef PHP_WMERRORS_H
#define PHP_WMERRORS_H

extern zend_module_entry wmerrors_module_entry;
#define phpext_wmerrors_ptr &wmerrors_module_entry

#ifdef PHP_WIN32
#define PHP_WMERRORS_API __declspec(dllexport)
#else
#define PHP_WMERRORS_API
#endif

#ifdef ZTS
#include "TSRM.h"
#endif

PHP_MINIT_FUNCTION(wmerrors);
PHP_MSHUTDOWN_FUNCTION(wmerrors);
PHP_RINIT_FUNCTION(wmerrors);
PHP_RSHUTDOWN_FUNCTION(wmerrors);
PHP_MINFO_FUNCTION(wmerrors);

ZEND_BEGIN_MODULE_GLOBALS(wmerrors)
	void (*old_error_cb)(int type, const char *error_filename, const uint error_lineno, const char *format, va_list args);
	char * message_file;
	int recursion_guard;
	int enabled;
ZEND_END_MODULE_GLOBALS(wmerrors)


#ifdef ZTS
#define WMERRORS_G(v) TSRMG(wmerrors_globals_id, zend_wmerrors_globals *, v)
#else
#define WMERRORS_G(v) (wmerrors_globals.v)
#endif

#endif

