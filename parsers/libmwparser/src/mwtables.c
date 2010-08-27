#include <antlr3defs.h>
#include <mwparsercontext.h>
#include <mwtables.h>

static void beginTable(MWPARSERCONTEXT *context, pANTLR3_VECTOR attributes);
static void endTable(MWPARSERCONTEXT *context);
static void beginTableRow(MWPARSERCONTEXT *context, pANTLR3_VECTOR attributes);
static void endTableRow(MWPARSERCONTEXT *context);
static void beginTableCell(MWPARSERCONTEXT *context, pANTLR3_VECTOR attributes);
static void endTableCell(MWPARSERCONTEXT *context);
static void beginTableHeading(MWPARSERCONTEXT *context, pANTLR3_VECTOR attributes);
static void endTableHeading(MWPARSERCONTEXT *context);
static void beginTableCaption(MWPARSERCONTEXT *context, pANTLR3_VECTOR attributes);
static void endTableCaption(MWPARSERCONTEXT *context);
static void beginTableBody(MWPARSERCONTEXT *context, pANTLR3_VECTOR attributes);
static void endTableBody(MWPARSERCONTEXT *context);

static void
beginTable(MWPARSERCONTEXT *context, pANTLR3_VECTOR attributes)
{
    MWLISTENER *l = &context->listener;
    l->beginTable(l, attributes);
}

static void
endTable(MWPARSERCONTEXT *context)
{
    MWLISTENER *l = &context->listener;
    l->endTable(l);
}

static void
beginTableRow(MWPARSERCONTEXT *context, pANTLR3_VECTOR attributes)
{
    MWLISTENER *l = &context->listener;
    l->beginTableRow(l, attributes);
}

static void
endTableRow(MWPARSERCONTEXT *context)
{
    MWLISTENER *l = &context->listener;
    l->endTableRow(l);
}

static void
beginTableCell(MWPARSERCONTEXT *context, pANTLR3_VECTOR attributes)
{
    MWLISTENER *l = &context->listener;
    l->beginTableCell(l, attributes);
}

static void
endTableCell(MWPARSERCONTEXT *context)
{
    MWLISTENER *l = &context->listener;
    l->endTableCell(l);
}

static void
beginTableHeading(MWPARSERCONTEXT *context, pANTLR3_VECTOR attributes)
{
    MWLISTENER *l = &context->listener;
    l->beginTableHeading(l, attributes);
}

static void
endTableHeading(MWPARSERCONTEXT *context)
{
    MWLISTENER *l = &context->listener;
    l->endTableHeading(l);
}

static void
beginTableCaption(MWPARSERCONTEXT *context, pANTLR3_VECTOR attributes)
{
    MWLISTENER *l = &context->listener;
    l->beginTableCaption(l, attributes);
}

static void
endTableCaption(MWPARSERCONTEXT *context)
{
    MWLISTENER *l = &context->listener;
    l->endTableCaption(l);
}


static void
beginTableBody(MWPARSERCONTEXT *context, pANTLR3_VECTOR attributes)
{
    MWLISTENER *l = &context->listener;
    l->beginTableBody(l, attributes);
}

static void
endTableBody(MWPARSERCONTEXT *context)
{
    MWLISTENER *l = &context->listener;
    l->endTableBody(l);
}


void mwTablesInit(MWPARSERCONTEXT *context)
{
    context->beginTable               = beginTable;
    context->endTable                 = endTable;
    context->beginTableRow            = beginTableRow;
    context->endTableRow              = endTableRow;
    context->beginTableCell           = beginTableCell;
    context->endTableCell             = endTableCell;
    context->beginTableHeading        = beginTableHeading;
    context->endTableHeading          = endTableHeading;
    context->beginTableCaption        = beginTableCaption;
    context->endTableCaption          = endTableCaption;
    context->beginTableBody           = beginTableBody;
    context->endTableBody             = endTableBody;
}
