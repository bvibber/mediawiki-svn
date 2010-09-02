#include <mwtagext.h>
#include <stdlib.h>
#include <string.h>

MWPARSER_TAGEXT *
MWTagextCopy(const MWPARSER_TAGEXT *tagExt)
{
    MWPARSER_TAGEXT *copy = malloc(sizeof(*copy));
    *copy = *tagExt;
    copy->name = strdup(tagExt->name);
}

void
MWTagextFree(void *tagExt)
{
    MWPARSER_TAGEXT *t = tagExt;
    free(t->name);
    free(t);
}

