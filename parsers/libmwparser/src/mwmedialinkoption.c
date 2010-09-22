#include <antlr3.h>
#include <mwmedialinkoption.h>

MEDIALINKOPTION * MWMediaLinkOptionNew(void)
{
    MEDIALINKOPTION *option = ANTLR3_MALLOC(sizeof(*option));
    if (option == NULL) {
        return NULL;
    }

    option->frame   = LOF_NONE;
    option->halign  = LOHA_NONE;
    option->valign  = LOVA_NONE;
    option->upright = false;
    option->border  = false;
    option->alt     = NULL;
    option->width   = NULL;
    option->height  = NULL;

    return option;
}

void MWMediaLinkOptionFree(void *mediaLinkOption)
{
    if (mediaLinkOption != NULL) {
        ANTLR3_FREE(mediaLinkOption);
    }
}
