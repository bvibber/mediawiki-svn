#ifndef MWLINKCOLLECTION_H_
#define MWLINKCOLLECTION_H_

#include <mwlinkresolution.h>

typedef struct MWLINKCOLLECTION_struct MWLINKCOLLECTION;

typedef void * MWLINKCOLLECTION_MARK;

typedef struct LCKEY_struct MWLCKEY;

typedef enum MWLINKTYPE { MWLT_INTERNAL, MWLT_EXTERNAL, MWLT_MEDIA, MWLT_LINKATTR } MWLINKTYPE;

MWLINKCOLLECTION *MWLinkCollectionNew(void);
void              MWLinkCollectionFree(void *linkCollection);
void              MWLinkCollectionAdd(MWLINKCOLLECTION *collection,
                                      MWLINKTYPE type,
                                      pANTLR3_STRING link,
                                      pANTLR3_COMMON_TOKEN token);
int               MWLinkCollectionNumLinks(MWLINKCOLLECTION *collection);

MWLINKCOLLECTION_MARK MWLinkCollectionMark(MWLINKCOLLECTION *collection);
void                  MWLinkCollectionRewind(MWLINKCOLLECTION *collection, MWLINKCOLLECTION_MARK mark);

void                  MWLinkCollectionTraverse(MWLINKCOLLECTION *linkCollection,
                                               int (*callback)(MWLCKEY *key, void *data),
                                               void *callbackData);

void                  MWLinkCollectionResolve(MWLINKCOLLECTION *linkCollection,
                                              MWLCKEY *key,
                                              MWLINKRESOLUTION *resolution);

const char *MWLCKeyGetLinkTitle(MWLCKEY *lckey);
MWLINKTYPE  MWLCKeyGetLinkType(MWLCKEY *lckey);




#endif
