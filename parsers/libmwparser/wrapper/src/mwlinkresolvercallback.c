#include <antlr3.h>
#include <mwlinkresolvercallback.h>

#ifdef TARGET_LANGUAGE_PERL
#include <EXTERN.h>
#include <perl.h>
#include <XSUB.h>


void
MWLinkResolverCallback(MWLINKCOLLECTION *linkCollection, void *data)
{
    SV *lc = sv_newmortal();
    sv_setref_pv(lc, NULL, linkCollection);

    dSP;

    ENTER;
    SAVETMPS;

    PUSHMARK(SP);
    XPUSHs(lc);
    PUTBACK;

    call_pv("MWParserLinkResolverCallback", G_DISCARD);

    FREETMPS;
    LEAVE;    
}

#elif (defined TARGET_LANGUAGE_PHP)
#include <php.h>

void
MWLinkResolverCallback(MWLINKCOLLECTION *linkCollection, void *data)
{
    zval *name;
    zval *lc;
    zval *retval;
    MAKE_STD_ZVAL(name);
    MAKE_STD_ZVAL(retval);
    MAKE_STD_ZVAL(lc);
    ZVAL_RESOURCE(lc, (long) linkCollection);
    if (name == NULL) {
        zend_error(E_ERROR, "Function call failed");
    }
    ZVAL_STRING(name, "MWParserLinkResolverCallback", 1);
    if(call_user_function(CG(function_table), NULL, name, retval, 1, &lc) != SUCCESS) {
        zend_error(E_ERROR, "Function call failed");
    }
    Z_DELREF_P(name);
    Z_DELREF_P(lc);
    Z_DELREF_P(retval);
}
#endif
