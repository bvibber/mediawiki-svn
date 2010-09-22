#include <antlr3.h>
#include <mwparsercontext.h>
#include <mwlinks.h>
#include <mwlinkresolution.h>

static void beginInternalLink(MWPARSERCONTEXT *context, pANTLR3_VECTOR attr);
static void endInternalLink(MWPARSERCONTEXT *context);
static void onInternalLink(MWPARSERCONTEXT *context, pANTLR3_VECTOR attr);
static void beginExternalLink(MWPARSERCONTEXT *context, pANTLR3_STRING linkUrl);
static void endExternalLink(MWPARSERCONTEXT *context);
static void onExternalLink(MWPARSERCONTEXT *context, pANTLR3_STRING linkUrl);
static void beginMediaLink(MWPARSERCONTEXT *context, pANTLR3_VECTOR attr);
static void endMediaLink(MWPARSERCONTEXT *context);
static void onMediaLink(MWPARSERCONTEXT *context, pANTLR3_VECTOR attr);

static void
beginInternalLink(MWPARSERCONTEXT *context, pANTLR3_VECTOR attr)
{
    MW_DELAYED_CALL(        context, beginInternalLink, endInternalLink, attr, false);
    MW_BEGIN_ORDERED_FORMAT(context, beginInternalLink, endInternalLink, attr, false);
    MWLISTENER *l = &context->listener;
    /*
     * Since this is a long term format, this call may be repeated
     * several times.  Since the client will want to unpack the
     * attribute vector, we'll make a copy of it.
     */
    pANTLR3_VECTOR v = context->vectorFactory->newVector(context->vectorFactory);
    int i;
    for (i = 0; i < attr->count ; i++) {
        v->add(v, attr->get(attr, i), NULL);
    }
    l->beginInternalLink(l, v);
}

static void
endInternalLink(MWPARSERCONTEXT *context)
{
    MW_SKIP_IF_EMPTY(     context, beginInternalLink, endInternalLink);
    MW_END_ORDERED_FORMAT(context, beginInternalLink, endInternalLink);
    MWLISTENER *l = &context->listener;
    l->endInternalLink(l);
}

static void
onInternalLink(MWPARSERCONTEXT *context, pANTLR3_VECTOR attr)
{
    MW_TRIGGER_DELAYED_CALLS(context);
    pANTLR3_VECTOR v = context->vectorFactory->newVector(context->vectorFactory);
    int i;
    for (i = 0; i < attr->count ; i++) {
        v->add(v, attr->get(attr, i), NULL);
    }
    MWLISTENER *l = &context->listener;
    l->onInternalLink(l, v);
}

static void
beginExternalLink(MWPARSERCONTEXT *context, pANTLR3_STRING linkUrl)
{
    MW_DELAYED_CALL(        context, beginExternalLink, endExternalLink, linkUrl, false);
    MW_BEGIN_ORDERED_FORMAT(context, beginExternalLink, endExternalLink, linkUrl, false);
    MWLISTENER *l = &context->listener;
    l->beginExternalLink(l, linkUrl);
}

static void
endExternalLink(MWPARSERCONTEXT *context)
{
    MW_SKIP_IF_EMPTY(     context, beginExternalLink, endExternalLink);
    MW_END_ORDERED_FORMAT(context, beginExternalLink, endExternalLink);
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
    MW_TRIGGER_DELAYED_CALLS(context);
    MWLISTENER *l = &context->listener;
    l->beginMediaLink(l, attr);
    context->tempReopenFormats(context);
}

static void
endMediaLink(MWPARSERCONTEXT *context)
{
    context->tempCloseFormats(context);
    MWLISTENER *l = &context->listener;
    l->endMediaLink(l);
    context->tempReopenFormats(context);
}

static void
onMediaLink(MWPARSERCONTEXT *context, pANTLR3_VECTOR attr)
{
    MW_TRIGGER_DELAYED_CALLS(context);
    MWLISTENER *l = &context->listener;
    l->onMediaLink(l, attr);
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
