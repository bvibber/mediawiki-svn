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
    MW_DELAYED_CALL(        context, beginHtmlB, endHtmlB, attr, false);
    MW_BEGIN_ORDERED_FORMAT(context, beginHtmlB, endHtmlB, attr, false);
    MWLISTENER *l = &context->listener;
    if (!context->inShortBold) {
        l->beginBold(l, attr);
    }
}

static void
endHtmlB(MWPARSERCONTEXT *context)
{
    MW_SKIP_IF_EMPTY(     context, beginHtmlB, endHtmlB);
    MW_END_ORDERED_FORMAT(context, beginHtmlB, endHtmlB);
    MWLISTENER *l = &context->listener;
    if (!context->inShortBold) {
        l->endBold(l);
    }
}

static void
beginHtmlI(MWPARSERCONTEXT *context, pANTLR3_VECTOR attr)
{
    MW_DELAYED_CALL(        context, beginHtmlI, endHtmlI, attr, false);
    MW_BEGIN_ORDERED_FORMAT(context, beginHtmlI, endHtmlI, attr, false);
    MWLISTENER *l = &context->listener;
    if (!context->inShortItalic) {
        l->beginItalic(l, attr);
    }
}

static void
endHtmlI(MWPARSERCONTEXT *context)
{
    MW_SKIP_IF_EMPTY(     context, beginHtmlI, endHtmlI);
    MW_END_ORDERED_FORMAT(context, beginHtmlI, endHtmlI);
    MWLISTENER *l = &context->listener;
    if (!context->inShortItalic) {
        l->endItalic(l);
    }
}

static void
beginHtmlDel(MWPARSERCONTEXT *context, pANTLR3_VECTOR attr)
{
    MW_DELAYED_CALL(        context, beginHtmlDel, endHtmlDel, attr, false);
    MW_BEGIN_ORDERED_FORMAT(context, beginHtmlDel, endHtmlDel, attr, false);
    MWLISTENER *l = &context->listener;
    l->beginHtmlDel(l, attr);
}

static void
endHtmlDel(MWPARSERCONTEXT *context)
{
    MW_SKIP_IF_EMPTY(     context, beginHtmlDel, endHtmlDel);
    MW_END_ORDERED_FORMAT(context, beginHtmlDel, endHtmlDel);
    MWLISTENER *l = &context->listener;
    l->endHtmlDel(l);
}

static void
beginHtmlStrong(MWPARSERCONTEXT *context, pANTLR3_VECTOR attr)
{
    MW_DELAYED_CALL(        context, beginHtmlStrong, endHtmlStrong, attr, false);
    MW_BEGIN_ORDERED_FORMAT(context, beginHtmlStrong, endHtmlStrong, attr, false);
    MWLISTENER *l = &context->listener;
    l->beginHtmlStrong(l, attr);
}

static void
endHtmlStrong(MWPARSERCONTEXT *context)
{
    MW_SKIP_IF_EMPTY(     context, beginHtmlStrong, endHtmlStrong);
    MW_END_ORDERED_FORMAT(context, beginHtmlStrong, endHtmlStrong);
    MWLISTENER *l = &context->listener;
    l->endHtmlStrong(l);
}

static void
beginHtmlSpan(MWPARSERCONTEXT *context, pANTLR3_VECTOR attr)
{
    MW_DELAYED_CALL(        context, beginHtmlSpan, endHtmlSpan, attr, false);
    MW_BEGIN_ORDERED_FORMAT(context, beginHtmlSpan, endHtmlSpan, attr, false);
    MWLISTENER *l = &context->listener;
    l->beginHtmlSpan(l, attr);
}

static void
endHtmlSpan(MWPARSERCONTEXT *context)
{
    MW_SKIP_IF_EMPTY(     context, beginHtmlSpan, endHtmlSpan);
    MW_END_ORDERED_FORMAT(context, beginHtmlSpan, endHtmlSpan);
    MWLISTENER *l = &context->listener;
    l->endHtmlSpan(l);
}

static void
beginHtmlU(MWPARSERCONTEXT *context, pANTLR3_VECTOR attr)
{
    MW_DELAYED_CALL(        context, beginHtmlU, endHtmlU, attr, false);
    MW_BEGIN_ORDERED_FORMAT(context, beginHtmlU, endHtmlU, attr, false);
    MWLISTENER *l = &context->listener;
    l->beginHtmlU(l, attr);
}

static void
endHtmlU(MWPARSERCONTEXT *context)
{
    MW_SKIP_IF_EMPTY(     context, beginHtmlU, endHtmlU);
    MW_END_ORDERED_FORMAT(context, beginHtmlU, endHtmlU);
    MWLISTENER *l = &context->listener;
    l->endHtmlU(l);
}

static void
beginHtmlIns(MWPARSERCONTEXT *context, pANTLR3_VECTOR attr)
{
    MW_DELAYED_CALL(        context, beginHtmlIns, endHtmlIns, attr, false);
    MW_BEGIN_ORDERED_FORMAT(context, beginHtmlIns, endHtmlIns, attr, false);
    MWLISTENER *l = &context->listener;
    l->beginHtmlIns(l, attr);
}

static void
endHtmlIns(MWPARSERCONTEXT *context)
{
    MW_SKIP_IF_EMPTY(     context, beginHtmlIns, endHtmlIns);
    MW_END_ORDERED_FORMAT(context, beginHtmlIns, endHtmlIns);
    MWLISTENER *l = &context->listener;
    l->endHtmlIns(l);
}

static void
beginHtmlFont(MWPARSERCONTEXT *context, pANTLR3_VECTOR attr)
{
    MW_DELAYED_CALL(        context, beginHtmlFont, endHtmlFont, attr, false);
    MW_BEGIN_ORDERED_FORMAT(context, beginHtmlFont, endHtmlFont, attr, false);
    MWLISTENER *l = &context->listener;
    l->beginHtmlFont(l, attr);
}

static void
endHtmlFont(MWPARSERCONTEXT *context)
{
    MW_SKIP_IF_EMPTY(     context, beginHtmlFont, endHtmlFont);
    MW_END_ORDERED_FORMAT(context, beginHtmlFont, endHtmlFont);
    MWLISTENER *l = &context->listener;
    l->endHtmlFont(l);
}

static void
beginHtmlBig(MWPARSERCONTEXT *context, pANTLR3_VECTOR attr)
{
    MW_DELAYED_CALL(        context, beginHtmlBig, endHtmlBig, attr, false);
    MW_BEGIN_ORDERED_FORMAT(context, beginHtmlBig, endHtmlBig, attr, false);
    MWLISTENER *l = &context->listener;
    l->beginHtmlBig(l, attr);
}

static void
endHtmlBig(MWPARSERCONTEXT *context)
{
    MW_SKIP_IF_EMPTY(     context, beginHtmlBig, endHtmlBig);
    MW_END_ORDERED_FORMAT(context, beginHtmlBig, endHtmlBig);
    MWLISTENER *l = &context->listener;
    l->endHtmlBig(l);
}

static void
beginHtmlSmall(MWPARSERCONTEXT *context, pANTLR3_VECTOR attr)
{
    MW_DELAYED_CALL(        context, beginHtmlSmall, endHtmlSmall, attr, false);
    MW_BEGIN_ORDERED_FORMAT(context, beginHtmlSmall, endHtmlSmall, attr, false);
    MWLISTENER *l = &context->listener;
    l->beginHtmlSmall(l, attr);
}

static void
endHtmlSmall(MWPARSERCONTEXT *context)
{
    MW_SKIP_IF_EMPTY(     context, beginHtmlSmall, endHtmlSmall);
    MW_END_ORDERED_FORMAT(context, beginHtmlSmall, endHtmlSmall);
    MWLISTENER *l = &context->listener;
    l->endHtmlSmall(l);
}

static void
beginHtmlSub(MWPARSERCONTEXT *context, pANTLR3_VECTOR attr)
{
    MW_DELAYED_CALL(        context, beginHtmlSub, endHtmlSub, attr, false);
    MW_BEGIN_ORDERED_FORMAT(context, beginHtmlSub, endHtmlSub, attr, false);
    MWLISTENER *l = &context->listener;
    l->beginHtmlSub(l, attr);
}

static void
endHtmlSub(MWPARSERCONTEXT *context)
{
    MW_SKIP_IF_EMPTY(     context, beginHtmlSub, endHtmlSub);
    MW_END_ORDERED_FORMAT(context, beginHtmlSub, endHtmlSub);
    MWLISTENER *l = &context->listener;
    l->endHtmlSub(l);
}

static void
beginHtmlSup(MWPARSERCONTEXT *context, pANTLR3_VECTOR attr)
{
    MW_DELAYED_CALL(        context, beginHtmlSup, endHtmlSup, attr, false);
    MW_BEGIN_ORDERED_FORMAT(context, beginHtmlSup, endHtmlSup, attr, false);
    MWLISTENER *l = &context->listener;
    l->beginHtmlSup(l, attr);
}

static void
endHtmlSup(MWPARSERCONTEXT *context)
{
    MW_SKIP_IF_EMPTY(     context, beginHtmlSup, endHtmlSup);
    MW_END_ORDERED_FORMAT(context, beginHtmlSup, endHtmlSup);
    MWLISTENER *l = &context->listener;
    l->endHtmlSup(l);
}

static void
beginHtmlCite(MWPARSERCONTEXT *context, pANTLR3_VECTOR attr)
{
    MW_DELAYED_CALL(        context, beginHtmlCite, endHtmlCite, attr, false);
    MW_BEGIN_ORDERED_FORMAT(context, beginHtmlCite, endHtmlCite, attr, false);
    MWLISTENER *l = &context->listener;
    l->beginHtmlCite(l, attr);
}

static void
endHtmlCite(MWPARSERCONTEXT *context)
{
    MW_SKIP_IF_EMPTY(     context, beginHtmlCite, endHtmlCite);
    MW_END_ORDERED_FORMAT(context, beginHtmlCite, endHtmlCite);
    MWLISTENER *l = &context->listener;
    l->endHtmlCite(l);
}

static void
beginHtmlCode(MWPARSERCONTEXT *context, pANTLR3_VECTOR attr)
{
    MW_DELAYED_CALL(        context, beginHtmlCode, endHtmlCode, attr, false);
    MW_BEGIN_ORDERED_FORMAT(context, beginHtmlCode, endHtmlCode, attr, false);
    MWLISTENER *l = &context->listener;
    l->beginHtmlCode(l, attr);
}

static void
endHtmlCode(MWPARSERCONTEXT *context)
{
    MW_SKIP_IF_EMPTY(     context, beginHtmlCode, endHtmlCode);
    MW_END_ORDERED_FORMAT(context, beginHtmlCode, endHtmlCode);
    MWLISTENER *l = &context->listener;
    l->endHtmlCode(l);
}

static void
beginHtmlStrike(MWPARSERCONTEXT *context, pANTLR3_VECTOR attr)
{
    MW_DELAYED_CALL(        context, beginHtmlStrike, endHtmlStrike, attr, false);
    MW_BEGIN_ORDERED_FORMAT(context, beginHtmlStrike, endHtmlStrike, attr, false);
    MWLISTENER *l = &context->listener;
    l->beginHtmlStrike(l, attr);
}

static void
endHtmlStrike(MWPARSERCONTEXT *context)
{
    MW_SKIP_IF_EMPTY(     context, beginHtmlStrike, endHtmlStrike);
    MW_END_ORDERED_FORMAT(context, beginHtmlStrike, endHtmlStrike);
    MWLISTENER *l = &context->listener;
    l->endHtmlStrike(l);
}

static void
beginHtmlTt(MWPARSERCONTEXT *context, pANTLR3_VECTOR attr)
{
    MW_DELAYED_CALL(        context, beginHtmlTt, endHtmlTt, attr, false);
    MW_BEGIN_ORDERED_FORMAT(context, beginHtmlTt, endHtmlTt, attr, false);
    MWLISTENER *l = &context->listener;
    l->beginHtmlTt(l, attr);
}

static void
endHtmlTt(MWPARSERCONTEXT *context)
{
    MW_SKIP_IF_EMPTY(     context, beginHtmlTt, endHtmlTt);
    MW_END_ORDERED_FORMAT(context, beginHtmlTt, endHtmlTt);
    MWLISTENER *l = &context->listener;
    l->endHtmlTt(l);
}

static void
beginHtmlVar(MWPARSERCONTEXT *context, pANTLR3_VECTOR attr)
{
    MW_DELAYED_CALL(        context, beginHtmlVar, endHtmlVar, attr, false);
    MW_BEGIN_ORDERED_FORMAT(context, beginHtmlVar, endHtmlVar, attr, false);
    MWLISTENER *l = &context->listener;
    l->beginHtmlVar(l, attr);
}

static void
endHtmlVar(MWPARSERCONTEXT *context)
{
    MW_SKIP_IF_EMPTY(     context, beginHtmlVar, endHtmlVar);
    MW_END_ORDERED_FORMAT(context, beginHtmlVar, endHtmlVar);
    MWLISTENER *l = &context->listener;
    l->endHtmlVar(l);
}

static void
beginHtmlAbbr(MWPARSERCONTEXT *context, pANTLR3_VECTOR attr)
{
    MW_DELAYED_CALL(        context, beginHtmlAbbr, endHtmlAbbr, attr, false);
    MW_BEGIN_ORDERED_FORMAT(context, beginHtmlAbbr, endHtmlAbbr, attr, false);
    MWLISTENER *l = &context->listener;
    l->beginHtmlAbbr(l, attr);
}

static void
endHtmlAbbr(MWPARSERCONTEXT *context)
{
    MW_SKIP_IF_EMPTY(     context, beginHtmlAbbr, endHtmlAbbr);
    MW_END_ORDERED_FORMAT(context, beginHtmlAbbr, endHtmlAbbr);
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
