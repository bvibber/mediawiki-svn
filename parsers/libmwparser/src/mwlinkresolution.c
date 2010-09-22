#include <stdlib.h>
#include <mwlinkresolution.h>

void MWLinkResolutionFree(void *linkResolution)
{
    MWLINKRESOLUTION *lr = linkResolution;
    lr->free(lr->freeData);
}
