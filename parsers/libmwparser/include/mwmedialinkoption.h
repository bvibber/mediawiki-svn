#ifndef MWMEDIALINKOPTION_H_
#define MWMEDIALINKOPTION_H_

#include <stdbool.h>

typedef enum {
    LOF_NONE, LOF_FRAME, LOF_FRAMELESS, LOF_THUMBNAIL
}
    MEDIALINKOPTION_FRAME;

typedef enum {
    LOHA_NONE, LOHA_LEFT, LOHA_RIGHT, LOHA_CENTER,
}
    MEDIALINKOPTION_HALIGN;

typedef enum {
    LOVA_NONE, LOVA_BASELINE, LOVA_SUB, LOVA_SUPER, LOVA_TOP, LOVA_TEXT_TOP, LOVA_MIDDLE, LOVA_BOTTOM, LOVA_TEXT_BOTTOM
}
    MEDIALINKOPTION_VALIGN;

typedef struct MEDIALINKOPTION_struct
{
    MEDIALINKOPTION_FRAME    frame;
    MEDIALINKOPTION_HALIGN   halign;
    MEDIALINKOPTION_VALIGN   valign;
    bool                     upright;
    bool                     border;
    pANTLR3_STRING           alt;
    pANTLR3_STRING           width;
    pANTLR3_STRING           height;
}
    MEDIALINKOPTION;

MEDIALINKOPTION *MWMediaLinkOptionNew(void);
void MWMediaLinkOptionFree(void *mediaLinkOption);

#endif
