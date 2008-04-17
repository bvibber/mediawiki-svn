
extern "C" {
#ifdef HAVE_CONFIG_H
#include "config.h"
#endif

#include "php.h"
#include "php_ini.h"
#include "ext/standard/info.h"
#include "php_transliterate.h"
}

#include <unicode/translit.h>

extern "C" {

/* True global resources - no need for thread safety here */
static int le_transliterate;

/* {{{ transliterate_functions[]
 */
zend_function_entry transliterate_functions[] = {
	PHP_FE(transliterate_with_id, NULL)
	/*PHP_FE(transliterate_with_rules, NULL)*/
	{NULL, NULL, NULL}
};
/* }}} */

/* {{{ transliterate_module_entry
 */
zend_module_entry transliterate_module_entry = {
#if ZEND_MODULE_API_NO >= 20010901
	STANDARD_MODULE_HEADER,
#endif
	"transliterate",
	transliterate_functions,
	PHP_MINIT(transliterate),
	PHP_MSHUTDOWN(transliterate),
	NULL, /* RINIT */
	NULL, /* RSHUTDOWN */
	PHP_MINFO(transliterate),
#if ZEND_MODULE_API_NO >= 20010901
	"0.1", /* Version */
#endif
	STANDARD_MODULE_PROPERTIES
};
/* }}} */

#ifdef COMPILE_DL_TRANSLITERATE
ZEND_GET_MODULE(transliterate)
#endif

/* }}} */

/* {{{ PHP_MINIT_FUNCTION
 */
PHP_MINIT_FUNCTION(transliterate)
{
	return SUCCESS;
}
/* }}} */

/* {{{ PHP_MSHUTDOWN_FUNCTION
 */
PHP_MSHUTDOWN_FUNCTION(transliterate)
{
	return SUCCESS;
}
/* }}} */

/* {{{ PHP_MINFO_FUNCTION
 */
PHP_MINFO_FUNCTION(transliterate)
{
	php_info_print_table_start();
	php_info_print_table_row(2, "ICU transliteration support", "enabled");
	php_info_print_table_end();
}
/* }}} */


/* {{{ proto string transliterate_with_id(string transID, string source)
   Transliterate with a given ICU transform ID */
PHP_FUNCTION(transliterate_with_id)
{
	char *transID = NULL, *source = NULL, *output;
	int transIDLength, sourceLength, tempLength, outputLength;

	if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "ss", 
		&transID, &transIDLength, &source, &sourceLength) == FAILURE) 
	{
		RETURN_FALSE;
	}
	
	try {
		/* Open the transliterator */
		UErrorCode error;
		UParseError parseError;
		UnicodeString uTransID(transID, transIDLength, "UTF-8");
		Transliterator * trans = Transliterator::createInstance(
				transID, UTRANS_FORWARD, parseError, error);
		if (U_FAILURE(error)) {
			if (error == U_INVALID_ID) {
				php_error(E_WARNING, "transliterate_with_id: Invalid transliterator ID");
			} else {
				php_error(E_WARNING, "transliterate_with_id: Transliterator::createInstance returned %s", 
					u_errorName(error));
			}
			delete trans;
			RETURN_FALSE;
		}
		
		/* Convert the string */
		UnicodeString buffer(source, sourceLength, "UTF-8");
		trans->transliterate(buffer);

		delete trans;
		
		/* Write it out to an emalloc'd buffer */
		tempLength = buffer.length() + 1;
		if (tempLength <= 0) {
			php_error(E_WARNING, "transliterate_with_id: output buffer too large (>2GB)");
			RETURN_FALSE;
		}
		output = (char*)emalloc(tempLength);
		outputLength = buffer.extract(0, buffer.length(), output, tempLength, "UTF-8");
		
		/* If the buffer wasn't big enough, expand it to the correct size and try again */
		if (outputLength > tempLength) {
			output = (char*)erealloc(output, outputLength + 1);
			buffer.extract(0, buffer.length(), output, outputLength + 1, "UTF-8");
		}

		RETURN_STRINGL(output, outputLength, 0);
	} catch (...) {
	}
	php_error(E_WARNING, "transliterate_with_id: unexpected C++ exception");
	RETURN_FALSE;
}
/* }}} */

} // end extern "C"

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 * vim600: noet sw=4 ts=4 fdm=marker
 * vim<600: noet sw=4 ts=4
 */
