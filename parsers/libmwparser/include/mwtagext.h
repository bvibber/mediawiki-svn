#ifndef MWTAGEXT_H_
#define MWTAGEXT_H_

#include <stdbool.h>

typedef struct MWPARSER_TAGEXT_struct {
    char * name;
    bool isBlock;
}
    MWPARSER_TAGEXT;

MWPARSER_TAGEXT * MWTagextCopy(const MWPARSER_TAGEXT *tagExt);
void MWTagextFree(void *tagExt);

#endif
