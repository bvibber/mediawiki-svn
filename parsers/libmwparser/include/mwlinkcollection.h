#ifndef MWLINKCOLLECTION_H_
#define MWLINKCOLLECTION_H_

typedef struct MWLINKCOLLECTION_struct MWLINKCOLLECTION;

typedef void * MWLINKCOLLECTION_MARK;

typedef enum MWLINKTYPE { MWLT_INTERNAL, MWLT_EXTERNAL, MWLT_MEDIA, MWLT_LINKATTR } MWLINKTYPE;

MWLINKCOLLECTION *MWLinkCollectionNew(void);
void              MWLinkCollectionFree(void *linkCollection);
void              MWLinkCollectionAdd(MWLINKCOLLECTION *collection,
                                      MWLINKTYPE type,
                                      pANTLR3_STRING link,
                                      pANTLR3_COMMON_TOKEN token);

MWLINKCOLLECTION_MARK MWLinkCollectionMark(MWLINKCOLLECTION *collection);
void                  MWLinkCollectionRewind(MWLINKCOLLECTION *collection, MWLINKCOLLECTION_MARK mark);

#endif
