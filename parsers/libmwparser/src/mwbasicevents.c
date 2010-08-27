#include <antlr3defs.h>
#include <mwparsercontext.h>
#include <mwbasicevents.h>

static void
onWord(MWPARSERCONTEXT *context, pANTLR3_STRING word)
{
    MW_TRIGGER_DELAYED_CALLS(context);

    MWLISTENER *l = &context->listener;
    l->onWord(l, word);
}

static void
onSpecial(MWPARSERCONTEXT *context, pANTLR3_STRING special)
{
    MW_TRIGGER_DELAYED_CALLS(context);
    
    MWLISTENER *l = &context->listener;
    l->onSpecial(l, special);
}

static void
onHTMLEntity(MWPARSERCONTEXT *context, pANTLR3_STRING entity)
{
    MW_TRIGGER_DELAYED_CALLS(context);
    MWLISTENER *l = &context->listener;
    l->onHTMLEntity(l, entity);
}

static void
onSpace(MWPARSERCONTEXT *context, pANTLR3_STRING space)
{
    MW_TRIGGER_DELAYED_CALLS(context);

    MWLISTENER *l = &context->listener;
    l->onSpace(l, space);
}

static void
onNewline(MWPARSERCONTEXT *context)
{
    MWLISTENER *l = &context->listener;
    l->onNewline(l);
}

static void
onBr(MWPARSERCONTEXT *context, pANTLR3_VECTOR attr)
{
    MWLISTENER *l = &context->listener;
    l->onBr(l, attr);
}

static void
beginParagraph(MWPARSERCONTEXT *context, pANTLR3_VECTOR attr)
{
    MWLISTENER *l = &context->listener;
    l->beginParagraph(l, attr);
}

static void
endParagraph(MWPARSERCONTEXT *context)
{
    MWLISTENER *l = &context->listener;
    l->endParagraph(l);
}

static void
beginArticle(MWPARSERCONTEXT *context)
{
    MWLISTENER *l = &context->listener;
    l->beginArticle(l);
}

static void
endArticle(MWPARSERCONTEXT *context)
{
    MWLISTENER *l = &context->listener;
    l->endArticle(l);
}

void
mwBasicEventsInit(MWPARSERCONTEXT *context)
{
    context->onWord                   = onWord;
    context->onSpecial                = onSpecial;
    context->onSpace                  = onSpace;
    context->onNewline                = onNewline;
    context->onBr                     = onBr;
    context->beginParagraph           = beginParagraph;
    context->endParagraph             = endParagraph;
    context->beginArticle             = beginArticle;
    context->endArticle               = endArticle;
    context->onHTMLEntity             = onHTMLEntity;
}
