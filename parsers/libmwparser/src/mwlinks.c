#include <antlr3defs.h>
#include <mwparsercontext.h>
#include <mwlinks.h>

static void beginInternalLink(MWPARSERCONTEXT *context, pANTLR3_STRING linkTitle);
static void endInternalLink(MWPARSERCONTEXT *context);
static void onInternalLink(MWPARSERCONTEXT *context, pANTLR3_STRING linkTitle);

static void
beginInternalLink(MWPARSERCONTEXT *context, pANTLR3_STRING linkTitle)
{
    MW_DELAYED_CALL(        context, beginInternalLink, endInternalLink, linkTitle, NULL);
    MW_BEGIN_ORDERED_FORMAT(context, beginInternalLink, endInternalLink, linkTitle, NULL, false);
    MWLISTENER *l = &context->listener;
    l->beginInternalLink(l, linkTitle);
}

static void
endInternalLink(MWPARSERCONTEXT *context)
{
    MW_SKIP_IF_EMPTY(     context, beginInternalLink, endInternalLink, NULL);
    MW_END_ORDERED_FORMAT(context, beginInternalLink, endInternalLink, NULL);
    MWLISTENER *l = &context->listener;
    l->endInternalLink(l);
}

static void
onInternalLink(MWPARSERCONTEXT *context, pANTLR3_STRING linkTitle)
{
    MW_TRIGGER_DELAYED_CALLS(context);
    MWLISTENER *l = &context->listener;
    l->onInternalLink(l, linkTitle);
}


void
mwLinksInit(MWPARSERCONTEXT *context)
{
    context->beginInternalLink        = beginInternalLink;
    context->endInternalLink          = endInternalLink;
    context->onInternalLink           = onInternalLink;
}
