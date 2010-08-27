#include <antlr3defs.h>
#include <mwparsercontext.h>
#include <mwheadings.h>

static void beginHeading(MWPARSERCONTEXT *context, int level, pANTLR3_VECTOR attr);
static void endHeading(MWPARSERCONTEXT *context);
static void beginTableOfContents(MWPARSERCONTEXT *context);
static void endTableOfContents(MWPARSERCONTEXT *context);
static void beginTableOfContentsItem(MWPARSERCONTEXT *context, int level);
static void endTableOfContentsItem(MWPARSERCONTEXT *context);

static void
beginHeading(MWPARSERCONTEXT *context, int level, pANTLR3_VECTOR attr)
{
    MWLISTENER *l = &context->listener;
    l->beginHeading(l, level, attr);
}

static void
endHeading(MWPARSERCONTEXT *context)
{
    MWLISTENER *l = &context->listener;
    l->endHeading(l);
}

static void
beginTableOfContents(MWPARSERCONTEXT *context)
{
    MWLISTENER *l = &context->listener;
    l->beginTableOfContents(l);
}

static void
endTableOfContents(MWPARSERCONTEXT *context)
{
    MWLISTENER *l = &context->listener;
    l->endTableOfContents(l);
}

static void
beginTableOfContentsItem(MWPARSERCONTEXT *context, int level)
{
    MWLISTENER *l = &context->listener;
    l->beginTableOfContentsItem(l, level);
}

static void
endTableOfContentsItem(MWPARSERCONTEXT *context)
{
    MWLISTENER *l = &context->listener;
    l->endTableOfContentsItem(l);
}

void
mwHeadingsInit(MWPARSERCONTEXT *context)
{
    context->beginHeading             = beginHeading;
    context->endHeading               = endHeading;
    context->beginTableOfContents     = beginTableOfContents;
    context->endTableOfContents       = endTableOfContents;
    context->beginTableOfContentsItem = beginTableOfContentsItem;
    context->endTableOfContentsItem   = endTableOfContentsItem;
}
