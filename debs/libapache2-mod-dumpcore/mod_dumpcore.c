/* 
 * Author: Domas Mituzas
 * Released to public domain
 */

#include "httpd.h"
#include "http_config.h"
#include <sys/prctl.h>

static int dumpcore_handler(request_rec *r)
{
	prctl(PR_SET_DUMPABLE,1,0,0,0);
        return DECLINED;
}

static void dumpcore_register_hooks(apr_pool_t *p)
{
    ap_hook_handler(dumpcore_handler, NULL, NULL, APR_HOOK_MIDDLE);
}

module AP_MODULE_DECLARE_DATA dumpcore_module = {
    STANDARD20_MODULE_STUFF, 
    NULL,                  /* create per-dir    config structures */
    NULL,                  /* merge  per-dir    config structures */
    NULL,                  /* create per-server config structures */
    NULL,                  /* merge  per-server config structures */
    NULL,                  /* table of config file commands       */
    dumpcore_register_hooks  /* register hooks                      */
};

