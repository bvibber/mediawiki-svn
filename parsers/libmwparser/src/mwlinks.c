#include <antlr3.h>
#include <mwparsercontext.h>
#include <mwlinks.h>

static void beginInternalLink(MWPARSERCONTEXT *context, pANTLR3_STRING linkTitle);
static void endInternalLink(MWPARSERCONTEXT *context);
static void onInternalLink(MWPARSERCONTEXT *context, pANTLR3_STRING linkTitle);
static void beginExternalLink(MWPARSERCONTEXT *context, pANTLR3_STRING linkUrl);
static void endExternalLink(MWPARSERCONTEXT *context);
static void onExternalLink(MWPARSERCONTEXT *context, pANTLR3_STRING linkUrl);
static void beginMediaLink(MWPARSERCONTEXT *context, pANTLR3_VECTOR attr);
static void endMediaLink(MWPARSERCONTEXT *context);
static void onMediaLink(MWPARSERCONTEXT *context, pANTLR3_VECTOR attr);

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

static void
beginExternalLink(MWPARSERCONTEXT *context, pANTLR3_STRING linkUrl)
{
    MW_DELAYED_CALL(        context, beginExternalLink, endExternalLink, linkUrl, NULL);
    MW_BEGIN_ORDERED_FORMAT(context, beginExternalLink, endExternalLink, linkUrl, NULL, false);
    MWLISTENER *l = &context->listener;
    l->beginExternalLink(l, linkUrl);
}

static void
endExternalLink(MWPARSERCONTEXT *context)
{
    MW_SKIP_IF_EMPTY(     context, beginExternalLink, endExternalLink, NULL);
    MW_END_ORDERED_FORMAT(context, beginExternalLink, endExternalLink, NULL);
    MWLISTENER *l = &context->listener;
    l->endExternalLink(l);
}

static void
onExternalLink(MWPARSERCONTEXT *context, pANTLR3_STRING linkUrl)
{
    MW_TRIGGER_DELAYED_CALLS(context);
    MWLISTENER *l = &context->listener;
    l->onExternalLink(l, linkUrl);
}

static void
beginMediaLink(MWPARSERCONTEXT *context, pANTLR3_VECTOR attr)
{
    MW_DELAYED_CALL(        context, beginMediaLink, endMediaLink, attr, NULL);
    MW_BEGIN_ORDERED_FORMAT(context, beginMediaLink, endMediaLink, attr, NULL, false);

    pANTLR3_STRING linkUrl = attr->get(attr, attr->count - 1);
    attr->remove(attr, attr->count - 1);
    MWLISTENER *l = &context->listener;
    l->beginMediaLink(l, linkUrl, attr);
}

static void
endMediaLink(MWPARSERCONTEXT *context)
{
    MW_SKIP_IF_EMPTY(     context, beginMediaLink, endMediaLink, NULL);
    MW_END_ORDERED_FORMAT(context, beginMediaLink, endMediaLink, NULL);
    MWLISTENER *l = &context->listener;
    l->endMediaLink(l);
}

static void
onMediaLink(MWPARSERCONTEXT *context, pANTLR3_VECTOR attr)
{
    MW_TRIGGER_DELAYED_CALLS(context);
    pANTLR3_STRING linkUrl = attr->get(attr, attr->count - 1);
    attr->remove(attr, attr->count - 1);
    MWLISTENER *l = &context->listener;
    l->onMediaLink(l, linkUrl, attr);
}


void
mwLinksInit(MWPARSERCONTEXT *context)
{
    context->beginInternalLink        = beginInternalLink;
    context->endInternalLink          = endInternalLink;
    context->onInternalLink           = onInternalLink;
    context->beginExternalLink        = beginExternalLink;
    context->endExternalLink          = endExternalLink;
    context->onExternalLink           = onExternalLink;
    context->beginMediaLink           = beginMediaLink;
    context->endMediaLink             = endMediaLink;
    context->onMediaLink              = onMediaLink;
}
