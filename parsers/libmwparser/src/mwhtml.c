#include <antlr3defs.h>
#include <mwparsercontext.h>
#include <mwhtml.h>

static void beginHtmlB(MWPARSERCONTEXT *context, pANTLR3_VECTOR attr);
static void endHtmlB(MWPARSERCONTEXT *context);
static void beginHtmlI(MWPARSERCONTEXT *context, pANTLR3_VECTOR attr);
static void endHtmlI(MWPARSERCONTEXT *context);
static void beginHtmlStrong(MWPARSERCONTEXT *context, pANTLR3_VECTOR attr);
static void endHtmlStrong(MWPARSERCONTEXT *context);
static void beginHtmlSpan(MWPARSERCONTEXT *context, pANTLR3_VECTOR attr);
static void endHtmlSpan(MWPARSERCONTEXT *context);
static void beginHtmlU(MWPARSERCONTEXT *context, pANTLR3_VECTOR attr);
static void endHtmlU(MWPARSERCONTEXT *context);
static void beginHtmlDel(MWPARSERCONTEXT *context, pANTLR3_VECTOR attr);
static void endHtmlDel(MWPARSERCONTEXT *context);
static void beginHtmlIns(MWPARSERCONTEXT *context, pANTLR3_VECTOR attr);
static void endHtmlIns(MWPARSERCONTEXT *context);
static void beginHtmlFont(MWPARSERCONTEXT *context, pANTLR3_VECTOR attr);
static void endHtmlFont(MWPARSERCONTEXT *context);
static void beginHtmlBig(MWPARSERCONTEXT *context, pANTLR3_VECTOR attr);
static void endHtmlBig(MWPARSERCONTEXT *context);
static void beginHtmlSmall(MWPARSERCONTEXT *context, pANTLR3_VECTOR attr);
static void endHtmlSmall(MWPARSERCONTEXT *context);
static void beginHtmlSub(MWPARSERCONTEXT *context, pANTLR3_VECTOR attr);
static void endHtmlSub(MWPARSERCONTEXT *context);
static void beginHtmlSup(MWPARSERCONTEXT *context, pANTLR3_VECTOR attr);
static void endHtmlSup(MWPARSERCONTEXT *context);
static void beginHtmlCite(MWPARSERCONTEXT *context, pANTLR3_VECTOR attr);
static void endHtmlCite(MWPARSERCONTEXT *context);
static void beginHtmlCode(MWPARSERCONTEXT *context, pANTLR3_VECTOR attr);
static void endHtmlCode(MWPARSERCONTEXT *context);
static void beginHtmlStrike(MWPARSERCONTEXT *context, pANTLR3_VECTOR attr);
static void endHtmlStrike(MWPARSERCONTEXT *context);
static void beginHtmlTt(MWPARSERCONTEXT *context, pANTLR3_VECTOR attr);
static void endHtmlTt(MWPARSERCONTEXT *context);
static void beginHtmlVar(MWPARSERCONTEXT *context, pANTLR3_VECTOR attr);
static void endHtmlVar(MWPARSERCONTEXT *context);
static void beginHtmlAbbr(MWPARSERCONTEXT *context, pANTLR3_VECTOR attr);
static void endHtmlAbbr(MWPARSERCONTEXT *context);


static void
beginHtmlDiv(MWPARSERCONTEXT *context, pANTLR3_VECTOR attr)
{
    MWLISTENER *l = &context->listener;
    l->beginHtmlDiv(l, attr);
}

static void
endHtmlDiv(MWPARSERCONTEXT *context)
{
    MWLISTENER *l = &context->listener;
    l->endHtmlDiv(l);
}

static void
beginHtmlBlockquote(MWPARSERCONTEXT *context, pANTLR3_VECTOR attr)
{
    MWLISTENER *l = &context->listener;
    l->beginHtmlBlockquote(l, attr);
}

static void
endHtmlBlockquote(MWPARSERCONTEXT *context)
{
    MWLISTENER *l = &context->listener;
    l->endHtmlBlockquote(l);
}

static void
beginHtmlCenter(MWPARSERCONTEXT *context, pANTLR3_VECTOR attr)
{
    MWLISTENER *l = &context->listener;
    l->beginHtmlCenter(l, attr);
}

static void
endHtmlCenter(MWPARSERCONTEXT *context)
{
    MWLISTENER *l = &context->listener;
    l->endHtmlCenter(l);
}


static void
beginHtmlB(MWPARSERCONTEXT *context, pANTLR3_VECTOR attr)
{
    MW_DELAYED_CALL(        context, beginHtmlB, endHtmlB, attr, NULL);
    MW_BEGIN_ORDERED_FORMAT(context, beginHtmlB, endHtmlB, attr, NULL, false);
    MWLISTENER *l = &context->listener;
    if (!context->inShortBold) {
        l->beginBold(l, attr);
    }
}

static void
endHtmlB(MWPARSERCONTEXT *context)
{
    MW_SKIP_IF_EMPTY(     context, beginHtmlB, endHtmlB, NULL);
    MW_END_ORDERED_FORMAT(context, beginHtmlB, endHtmlB, NULL);
    MWLISTENER *l = &context->listener;
    if (!context->inShortBold) {
        l->endBold(l);
    }
}

static void
beginHtmlI(MWPARSERCONTEXT *context, pANTLR3_VECTOR attr)
{
    MW_DELAYED_CALL(        context, beginHtmlI, endHtmlI, attr, NULL);
    MW_BEGIN_ORDERED_FORMAT(context, beginHtmlI, endHtmlI, attr, NULL, false);
    MWLISTENER *l = &context->listener;
    if (!context->inShortItalic) {
        l->beginItalic(l, attr);
    }
}

static void
endHtmlI(MWPARSERCONTEXT *context)
{
    MW_SKIP_IF_EMPTY(     context, beginHtmlI, endHtmlI, NULL);
    MW_END_ORDERED_FORMAT(context, beginHtmlI, endHtmlI, NULL);
    MWLISTENER *l = &context->listener;
    if (!context->inShortItalic) {
        l->endItalic(l);
    }
}

static void
beginHtmlDel(MWPARSERCONTEXT *context, pANTLR3_VECTOR attr)
{
    MW_DELAYED_CALL(        context, beginHtmlDel, endHtmlDel, attr, NULL);
    MW_BEGIN_ORDERED_FORMAT(context, beginHtmlDel, endHtmlDel, attr, NULL, false);
    MWLISTENER *l = &context->listener;
    l->beginHtmlDel(l, attr);
}

static void
endHtmlDel(MWPARSERCONTEXT *context)
{
    MW_SKIP_IF_EMPTY(     context, beginHtmlDel, endHtmlDel, NULL);
    MW_END_ORDERED_FORMAT(context, beginHtmlDel, endHtmlDel, NULL);
    MWLISTENER *l = &context->listener;
    l->endHtmlDel(l);
}

static void
beginHtmlStrong(MWPARSERCONTEXT *context, pANTLR3_VECTOR attr)
{
    MW_DELAYED_CALL(        context, beginHtmlStrong, endHtmlStrong, attr, NULL);
    MW_BEGIN_ORDERED_FORMAT(context, beginHtmlStrong, endHtmlStrong, attr, NULL, false);
    MWLISTENER *l = &context->listener;
    l->beginHtmlStrong(l, attr);
}

static void
endHtmlStrong(MWPARSERCONTEXT *context)
{
    MW_SKIP_IF_EMPTY(     context, beginHtmlStrong, endHtmlStrong, NULL);
    MW_END_ORDERED_FORMAT(context, beginHtmlStrong, endHtmlStrong, NULL);
    MWLISTENER *l = &context->listener;
    l->endHtmlStrong(l);
}

static void
beginHtmlSpan(MWPARSERCONTEXT *context, pANTLR3_VECTOR attr)
{
    MW_DELAYED_CALL(        context, beginHtmlSpan, endHtmlSpan, attr, NULL);
    MW_BEGIN_ORDERED_FORMAT(context, beginHtmlSpan, endHtmlSpan, attr, NULL, false);
    MWLISTENER *l = &context->listener;
    l->beginHtmlSpan(l, attr);
}

static void
endHtmlSpan(MWPARSERCONTEXT *context)
{
    MW_SKIP_IF_EMPTY(     context, beginHtmlSpan, endHtmlSpan, NULL);
    MW_END_ORDERED_FORMAT(context, beginHtmlSpan, endHtmlSpan, NULL);
    MWLISTENER *l = &context->listener;
    l->endHtmlSpan(l);
}

static void
beginHtmlU(MWPARSERCONTEXT *context, pANTLR3_VECTOR attr)
{
    MW_DELAYED_CALL(        context, beginHtmlU, endHtmlU, attr, NULL);
    MW_BEGIN_ORDERED_FORMAT(context, beginHtmlU, endHtmlU, attr, NULL, false);
    MWLISTENER *l = &context->listener;
    l->beginHtmlU(l, attr);
}

static void
endHtmlU(MWPARSERCONTEXT *context)
{
    MW_SKIP_IF_EMPTY(     context, beginHtmlU, endHtmlU, NULL);
    MW_END_ORDERED_FORMAT(context, beginHtmlU, endHtmlU, NULL);
    MWLISTENER *l = &context->listener;
    l->endHtmlU(l);
}

static void
beginHtmlIns(MWPARSERCONTEXT *context, pANTLR3_VECTOR attr)
{
    MW_DELAYED_CALL(        context, beginHtmlIns, endHtmlIns, attr, NULL);
    MW_BEGIN_ORDERED_FORMAT(context, beginHtmlIns, endHtmlIns, attr, NULL, false);
    MWLISTENER *l = &context->listener;
    l->beginHtmlIns(l, attr);
}

static void
endHtmlIns(MWPARSERCONTEXT *context)
{
    MW_SKIP_IF_EMPTY(     context, beginHtmlIns, endHtmlIns, NULL);
    MW_END_ORDERED_FORMAT(context, beginHtmlIns, endHtmlIns, NULL);
    MWLISTENER *l = &context->listener;
    l->endHtmlIns(l);
}

static void
beginHtmlFont(MWPARSERCONTEXT *context, pANTLR3_VECTOR attr)
{
    MW_DELAYED_CALL(        context, beginHtmlFont, endHtmlFont, attr, NULL);
    MW_BEGIN_ORDERED_FORMAT(context, beginHtmlFont, endHtmlFont, attr, NULL, false);
    MWLISTENER *l = &context->listener;
    l->beginHtmlFont(l, attr);
}

static void
endHtmlFont(MWPARSERCONTEXT *context)
{
    MW_SKIP_IF_EMPTY(     context, beginHtmlFont, endHtmlFont, NULL);
    MW_END_ORDERED_FORMAT(context, beginHtmlFont, endHtmlFont, NULL);
    MWLISTENER *l = &context->listener;
    l->endHtmlFont(l);
}

static void
beginHtmlBig(MWPARSERCONTEXT *context, pANTLR3_VECTOR attr)
{
    MW_DELAYED_CALL(        context, beginHtmlBig, endHtmlBig, attr, NULL);
    MW_BEGIN_ORDERED_FORMAT(context, beginHtmlBig, endHtmlBig, attr, NULL, false);
    MWLISTENER *l = &context->listener;
    l->beginHtmlBig(l, attr);
}

static void
endHtmlBig(MWPARSERCONTEXT *context)
{
    MW_SKIP_IF_EMPTY(     context, beginHtmlBig, endHtmlBig, NULL);
    MW_END_ORDERED_FORMAT(context, beginHtmlBig, endHtmlBig, NULL);
    MWLISTENER *l = &context->listener;
    l->endHtmlBig(l);
}

static void
beginHtmlSmall(MWPARSERCONTEXT *context, pANTLR3_VECTOR attr)
{
    MW_DELAYED_CALL(        context, beginHtmlSmall, endHtmlSmall, attr, NULL);
    MW_BEGIN_ORDERED_FORMAT(context, beginHtmlSmall, endHtmlSmall, attr, NULL, false);
    MWLISTENER *l = &context->listener;
    l->beginHtmlSmall(l, attr);
}

static void
endHtmlSmall(MWPARSERCONTEXT *context)
{
    MW_SKIP_IF_EMPTY(     context, beginHtmlSmall, endHtmlSmall, NULL);
    MW_END_ORDERED_FORMAT(context, beginHtmlSmall, endHtmlSmall, NULL);
    MWLISTENER *l = &context->listener;
    l->endHtmlSmall(l);
}

static void
beginHtmlSub(MWPARSERCONTEXT *context, pANTLR3_VECTOR attr)
{
    MW_DELAYED_CALL(        context, beginHtmlSub, endHtmlSub, attr, NULL);
    MW_BEGIN_ORDERED_FORMAT(context, beginHtmlSub, endHtmlSub, attr, NULL, false);
    MWLISTENER *l = &context->listener;
    l->beginHtmlSub(l, attr);
}

static void
endHtmlSub(MWPARSERCONTEXT *context)
{
    MW_SKIP_IF_EMPTY(     context, beginHtmlSub, endHtmlSub, NULL);
    MW_END_ORDERED_FORMAT(context, beginHtmlSub, endHtmlSub, NULL);
    MWLISTENER *l = &context->listener;
    l->endHtmlSub(l);
}

static void
beginHtmlSup(MWPARSERCONTEXT *context, pANTLR3_VECTOR attr)
{
    MW_DELAYED_CALL(        context, beginHtmlSup, endHtmlSup, attr, NULL);
    MW_BEGIN_ORDERED_FORMAT(context, beginHtmlSup, endHtmlSup, attr, NULL, false);
    MWLISTENER *l = &context->listener;
    l->beginHtmlSup(l, attr);
}

static void
endHtmlSup(MWPARSERCONTEXT *context)
{
    MW_SKIP_IF_EMPTY(     context, beginHtmlSup, endHtmlSup, NULL);
    MW_END_ORDERED_FORMAT(context, beginHtmlSup, endHtmlSup, NULL);
    MWLISTENER *l = &context->listener;
    l->endHtmlSup(l);
}

static void
beginHtmlCite(MWPARSERCONTEXT *context, pANTLR3_VECTOR attr)
{
    MW_DELAYED_CALL(        context, beginHtmlCite, endHtmlCite, attr, NULL);
    MW_BEGIN_ORDERED_FORMAT(context, beginHtmlCite, endHtmlCite, attr, NULL, false);
    MWLISTENER *l = &context->listener;
    l->beginHtmlCite(l, attr);
}

static void
endHtmlCite(MWPARSERCONTEXT *context)
{
    MW_SKIP_IF_EMPTY(     context, beginHtmlCite, endHtmlCite, NULL);
    MW_END_ORDERED_FORMAT(context, beginHtmlCite, endHtmlCite, NULL);
    MWLISTENER *l = &context->listener;
    l->endHtmlCite(l);
}

static void
beginHtmlCode(MWPARSERCONTEXT *context, pANTLR3_VECTOR attr)
{
    MW_DELAYED_CALL(        context, beginHtmlCode, endHtmlCode, attr, NULL);
    MW_BEGIN_ORDERED_FORMAT(context, beginHtmlCode, endHtmlCode, attr, NULL, false);
    MWLISTENER *l = &context->listener;
    l->beginHtmlCode(l, attr);
}

static void
endHtmlCode(MWPARSERCONTEXT *context)
{
    MW_SKIP_IF_EMPTY(     context, beginHtmlCode, endHtmlCode, NULL);
    MW_END_ORDERED_FORMAT(context, beginHtmlCode, endHtmlCode, NULL);
    MWLISTENER *l = &context->listener;
    l->endHtmlCode(l);
}

static void
beginHtmlStrike(MWPARSERCONTEXT *context, pANTLR3_VECTOR attr)
{
    MW_DELAYED_CALL(        context, beginHtmlStrike, endHtmlStrike, attr, NULL);
    MW_BEGIN_ORDERED_FORMAT(context, beginHtmlStrike, endHtmlStrike, attr, NULL, false);
    MWLISTENER *l = &context->listener;
    l->beginHtmlStrike(l, attr);
}

static void
endHtmlStrike(MWPARSERCONTEXT *context)
{
    MW_SKIP_IF_EMPTY(     context, beginHtmlStrike, endHtmlStrike, NULL);
    MW_END_ORDERED_FORMAT(context, beginHtmlStrike, endHtmlStrike, NULL);
    MWLISTENER *l = &context->listener;
    l->endHtmlStrike(l);
}

static void
beginHtmlTt(MWPARSERCONTEXT *context, pANTLR3_VECTOR attr)
{
    MW_DELAYED_CALL(        context, beginHtmlTt, endHtmlTt, attr, NULL);
    MW_BEGIN_ORDERED_FORMAT(context, beginHtmlTt, endHtmlTt, attr, NULL, false);
    MWLISTENER *l = &context->listener;
    l->beginHtmlTt(l, attr);
}

static void
endHtmlTt(MWPARSERCONTEXT *context)
{
    MW_SKIP_IF_EMPTY(     context, beginHtmlTt, endHtmlTt, NULL);
    MW_END_ORDERED_FORMAT(context, beginHtmlTt, endHtmlTt, NULL);
    MWLISTENER *l = &context->listener;
    l->endHtmlTt(l);
}

static void
beginHtmlVar(MWPARSERCONTEXT *context, pANTLR3_VECTOR attr)
{
    MW_DELAYED_CALL(        context, beginHtmlVar, endHtmlVar, attr, NULL);
    MW_BEGIN_ORDERED_FORMAT(context, beginHtmlVar, endHtmlVar, attr, NULL, false);
    MWLISTENER *l = &context->listener;
    l->beginHtmlVar(l, attr);
}

static void
endHtmlVar(MWPARSERCONTEXT *context)
{
    MW_SKIP_IF_EMPTY(     context, beginHtmlVar, endHtmlVar, NULL);
    MW_END_ORDERED_FORMAT(context, beginHtmlVar, endHtmlVar, NULL);
    MWLISTENER *l = &context->listener;
    l->endHtmlVar(l);
}

static void
beginHtmlAbbr(MWPARSERCONTEXT *context, pANTLR3_VECTOR attr)
{
    MW_DELAYED_CALL(        context, beginHtmlAbbr, endHtmlAbbr, attr, NULL);
    MW_BEGIN_ORDERED_FORMAT(context, beginHtmlAbbr, endHtmlAbbr, attr, NULL, false);
    MWLISTENER *l = &context->listener;
    l->beginHtmlAbbr(l, attr);
}

static void
endHtmlAbbr(MWPARSERCONTEXT *context)
{
    MW_SKIP_IF_EMPTY(     context, beginHtmlAbbr, endHtmlAbbr, NULL);
    MW_END_ORDERED_FORMAT(context, beginHtmlAbbr, endHtmlAbbr, NULL);
    MWLISTENER *l = &context->listener;
    l->endHtmlAbbr(l);
}

void
mwHtmlInit(MWPARSERCONTEXT *context)
{
    context->inLongItalic            = false;
    context->inLongBold              = false;

    context->beginHtmlDiv   = beginHtmlDiv;
    context->endHtmlDiv     = endHtmlDiv;
    context->beginHtmlBlockquote   = beginHtmlBlockquote;
    context->endHtmlBlockquote     = endHtmlBlockquote;
    context->beginHtmlCenter= beginHtmlCenter;
    context->endHtmlCenter  = endHtmlCenter;
    context->beginHtmlB     = beginHtmlB;
    context->endHtmlB       = endHtmlB;
    context->beginHtmlI     = beginHtmlI;
    context->endHtmlI       = endHtmlI;
    context->beginHtmlU     = beginHtmlU;
    context->endHtmlU       = endHtmlU;
    context->beginHtmlDel   = beginHtmlDel;
    context->endHtmlDel     = endHtmlDel;
    context->beginHtmlIns   = beginHtmlIns;
    context->endHtmlIns     = endHtmlIns;
    context->beginHtmlFont  = beginHtmlFont;
    context->endHtmlFont    = endHtmlFont;
    context->beginHtmlBig   = beginHtmlBig;
    context->endHtmlBig     = endHtmlBig;
    context->beginHtmlSmall = beginHtmlSmall;
    context->endHtmlSmall   = endHtmlSmall;
    context->beginHtmlSub   = beginHtmlSub;
    context->endHtmlSub     = endHtmlSub;
    context->beginHtmlSup   = beginHtmlSup;
    context->endHtmlSup     = endHtmlSup;
    context->beginHtmlCite  = beginHtmlCite;
    context->endHtmlCite    = endHtmlCite;
    context->beginHtmlCode  = beginHtmlCode;
    context->endHtmlCode    = endHtmlCode;
    context->beginHtmlStrike= beginHtmlStrike;
    context->endHtmlStrike  = endHtmlStrike;
    context->beginHtmlStrong= beginHtmlStrong;
    context->endHtmlStrong  = endHtmlStrong;
    context->beginHtmlSpan  = beginHtmlSpan;
    context->endHtmlSpan    = endHtmlSpan;
    context->beginHtmlTt    = beginHtmlTt;
    context->endHtmlTt      = endHtmlTt;
    context->beginHtmlVar   = beginHtmlVar;
    context->endHtmlVar     = endHtmlVar;
    context->beginHtmlAbbr  = beginHtmlAbbr;
    context->endHtmlAbbr    = endHtmlAbbr;
}
