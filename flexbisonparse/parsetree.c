
/**
 **  This file is part of the flex/bison-based parser for MediaWiki.
 **           This source file implements all the functions
 **                       defined in parsetree.h.
 **
 ** This source file is licensed unter the GNU General Public License
 **                http://www.gnu.org/copyleft/gpl.html
 **                  Originally written 2004 by Timwi
 **/

#include "parsetree.h"
#include "fb_defines.h"
#include <string.h>
#include <stdio.h>

int debug_indent = 0;
/* - use this to find out if any nodes are allocated but never freed
#define debugMemLeak_alloc(x,y) printf("alloc: %u (%u)\n", x, y);
#define freeNode(node) \
    printf("free: %u (%u)\n", node, node->type); \
    free (node);
/*/
#define debugMemLeak_alloc(x,y)
#define freeNode(node) free(node)
/**/

Node newNode (NodeType newType)
{
    Node result = (Node) malloc (sizeof (struct NodeStruct));
    result->type = newType;
    result->firstChild = 0;
    result->nextSibling = 0;
    debugMemLeak_alloc (result, newType);
    return result;
}
Node newNodeI (NodeType newType, int data)
{
    Node result = newNode (newType);
    result->data.num = data;
    return result;
}
Node newNodeS (NodeType newType, char* data)
{
    Node result = newNode (newType);
    result->data.str = data;
    return result;
}
Node newNodeN (NodeType newType, char* name, char* value, int copyName, int copyValue)
{
    Node result = newNode (newType);
    result->data.nameval = (NameValue) malloc (sizeof (struct NameValueStruct));
    result->data.nameval->name  = copyName  ? strdup (name)  : name;
    result->data.nameval->value = copyValue ? strdup (value) : value;
    return result;
}
AttributeData newAttributeDataFromStr (char* str)
{
    AttributeData ret = (AttributeData) malloc (sizeof (struct AttributeDataStruct));
    int len = strlen (str);
    int i = len-1;

    while (str[i] == ' ') i--;
    i++;
    ret->name = (char*) malloc ((i+1) * sizeof (char));
    memcpy (ret->name, str, i * sizeof (char));
    ret->name[i] = '\0';
    ret->spacesAfterName = len-i;
    return ret;
}
Node newNodeA (int t, AttributeData ad, int sae, int sav)
{
    Node result = newNode (Attribute);
    result->data.attrdata = ad;
    result->data.attrdata->type = t;
    result->data.attrdata->spacesAfterEquals = sae;
    result->data.attrdata->spacesAfterValue = sav;
    return result;
}

/* Return value is the first parameter */
Node nodeAddChild (Node node, Node child)
{
    Node find;
    if (child)
    {
        if (node->firstChild)
        {
            find = node->firstChild;
            while (find->nextSibling)
                find = find->nextSibling;
            find->nextSibling = child;
        }
        else node->firstChild = child;
    }
    return node;
}

/* Return value is the first parameter */
Node nodeAddSibling (Node node, Node sibling)
{
    Node examine = node;
    if (sibling)
    {
        while (examine->nextSibling)
            examine = examine->nextSibling;
        examine->nextSibling = sibling;
    }
    return node;
}

/* Return value is the first parameter */
Node nodePrependChild (Node node, Node child)
{
    Node prevChild = node->firstChild;
    node->firstChild = child;
    return nodeAddChild (node, prevChild);
}

void freeRecursively (Node node)
{
    Node next, child = node->firstChild;

    debugpt2 ("freeRecursively (%u)\n", node);

    while (child)
    {
        next = child->nextSibling;
        freeRecursively (child);
        child = next;
    }

    freeNode (node);

    debugpt_end;
}

void freeRecursivelyWithSiblings (Node node)
{
    Node next, sibling = node;

    while (sibling)
    {
        next = sibling->nextSibling;
        freeRecursively (sibling);
        sibling = next;
    }
}

void removeAndFreeFirstChild (Node node)
{
    Node child = node->firstChild;
    if (!child) return;
    node->firstChild = child->nextSibling;
    freeRecursively (child);
}

/* Parameter must be a ListLine node. Returns a List node. */
Node processListHelper (Node start)
{
    NodeType type, subtype;
    Node result, curChild, examine, item, previous, l, toBeFreed;

    if (!start) return 0;
    if (start->type != ListLine) return 0;
    if (!start->firstChild) return 0;

    type = start->firstChild->type;
    result = newNodeI (List,
        type == ListBullet      ?   1   :
        type == ListNumbered    ?   2   :   0);

    curChild = 0;
    examine = start;

    while (examine)
    {
        toBeFreed = examine;

        /* We know that examine->firstChild is ListBullet, ListNumbered, etc. Remove it */
        removeAndFreeFirstChild (examine);

        /* Empty list item? */
        if (!examine->firstChild)
        {
            examine = examine->nextSibling;
            freeNode (toBeFreed);
        }

        /* Does this item start a new list? */
        else if (examine->firstChild->type == ListBullet ||
                 examine->firstChild->type == ListNumbered)
        {
            /* If we are starting a new list, we want it to be inside the previous list item. */
            /* However, if that previous list item does not exist, we need to create a new one. */
            if (!curChild)
            {
                item = newNode (ListItem);
                result->firstChild = item;
                curChild = item;
            }

            subtype = examine->firstChild->type;

            /* progressively remove the firstChild for that sublist */
            previous = examine;
            l = examine->nextSibling;
            while (l && l->firstChild &&
                   l->firstChild->nextSibling &&
                   l->firstChild->nextSibling->type == subtype)
            {
                removeAndFreeFirstChild (l);
                previous = l;
                l = l->nextSibling;
            }

            /* trick recursive call into thinking list ends here */
            previous->nextSibling = 0;
            /* notice that the recursive call will take care of freeing the node */
            nodeAddChild (curChild, processListHelper (examine));
            previous->nextSibling = l;
            examine = l;
        }

        /* Otherwise it's a normal plain old list item */
        else
        {
            item = newNode (ListItem);
            if (curChild) curChild->nextSibling = item;
            else result->firstChild = item;
            curChild = item;
            nodeAddChild (curChild, examine->firstChild);
            examine = examine->nextSibling;
            freeNode (toBeFreed);
        }
    }
    return result;
}

/* Parameter must be a ListBlock node. Returns a List node. */
Node processListBlock (Node block)
{
    Node child;

    if (!block) return 0;
    if (block->type != ListBlock) return 0;
    if (!block->firstChild) return 0;
    if (block->firstChild->type != ListLine) return 0;

    child = block->firstChild;
    freeNode (block);
    return processListHelper (child);
}

/*  The PreBlock node returned by the grammar contains one or more PreLine
 *  nodes, each of which has one or two children. The first child is something
 *  representing text. The second, if present, is a Newlines node. We want to
 *  convert those Newlines nodes to empty PreLine nodes, except for the very
 *  last one in the block, which we want to discard.
 *
 *  It is harmless to call this twice on the same block (the second time won't
 *  make any more changes), but for efficiency, don't.
 */
Node processPreBlock (Node block)
{
    Node result, examine, newlinesnode, tmpnode;

    /******     EXAMPLE    ******
     *
     *      (*) node(type=PreBlock)
     *       |
     *       | firstChild
     *       |
     *       |A(type=PreLine)                      B(type=PreLine)
     *      (*)------------nextSibling------------(*)
     *       |                                     |
     *       | firstChild                          | firstChild
     *       |                                     |
     *       |W(type=TextToken)  X(type=Newlines)  |Y(type=TextToken)  Z(type=Newlines)
     *      (*)---nextSibling---(*)(num=2)        (*)---nextSibling---(*)(num=1)
     *
     *  We want to convert X to two PreLine nodes and get rid of Z.
     *  The following is the end-result:
     *
     *      (*) node(type=PreBlock)
     *       |
     *       | firstChild
     *       |
     *       |A(type=PreLine)    C(type=PreLine)     D(type=PreLine)     B(type=PreLine)
     *      (*)---nextSibling---(*)---nextSibling---(*)---nextSibling---(*)
     *       |                                                           |
     *       | firstChild                                                | firstChild
     *       |                                                           |
     *       |W(type=TextToken)                                          |Y(type=TextToken)
     *      (*)                                                         (*)
     *
     *  Notice how X has been removed and C and D created instead, while Z has been removed with
     *  no replacement. Notice also that W & Y might as well be TextBlocks, ExtensionTokens, etc.
     *
     */

    result = block;
    /* Iteratively examine all of the block's children (A and B) */
    examine = block->firstChild;
    while (examine)
    {
        /* If a Newlines node exists (i.e. X or Z)... */
        if (examine->firstChild->nextSibling)
        {
            newlinesnode = examine->firstChild->nextSibling;
            /* Detach the Newlines node from examine */
            examine->firstChild->nextSibling = 0;
            /* If this isn't the last PreLine in the block... */
            if (examine->nextSibling)
            {
                /* Remember the next sibling (B) */
                tmpnode = examine->nextSibling;
                /* Convert Newlines node to empty PreLine nodes */
                while (newlinesnode->data.num--)
                {
                    /* Insert an empty PreLine node (C and D) */
                    examine->nextSibling = newNode (PreLine);
                    /* Move on to the newly-created node */
                    examine = examine->nextSibling;
                }
                /* Re-attach the next sibling (B) */
                examine->nextSibling = tmpnode;
            }
            /* Newlines nodes don't have children, no need for freeRecursively */
            freeNode (newlinesnode);
        }
        examine = examine->nextSibling;
    }
    return result;
}

Node processEndHeadingInText (int n)
{
    char* ret;
    int equalses, spaces, i;

    equalses = n % 0x10000;
    spaces   = n >> 16;

    if (equalses + spaces < 1) return 0;

    ret = (char*) malloc ((equalses + spaces + 1) * sizeof (char));
    i = 0;
    while (equalses--) ret[i++] = '=';
    while (spaces--)   ret[i++] = ' ';
    ret[i] = '\0';
    return newNodeS (TextToken, ret);
}

Node processTableCellContents (Node node)
{
    Node ret;

    if (!node) return 0;
    if (node->type == Paragraph && !node->nextSibling)
    {
        ret = node->firstChild;
        freeNode (node);
        return ret;
    }
    return node;
}

Node processNestedItalics (Node node)
{
    Node examine, saveExamineSibling, childExamine, childSibling, saveChildSibling;

    if (!node) return 0;
    if (node->type != TextBlock) return node;

    /******     EXAMPLE    ******
     *
     *      (*) node(type=TextBlock)
     *       |
     *       | firstChild
     *       |
     *       |A(type=TextToken)  B(type=Italics)     C(type=TextToken)
     *      (*)---nextSibling---(*)---nextSibling---(*)
     *                           |
     *                           | firstChild
     *                           |
     *                          (*) T(type=TextBlock)
     *                           |
     *                           | firstChild
     *                           |
     *                           |W(type=TextToken)  X(type=Italics)     Y(type=TextToken)
     *                          (*)---nextSibling---(*)---nextSibling---(*)
     *                                               |
     *                                               | firstChild
     *                                               |
     *                                              (*) Z
     *
     *  Here we have two Italics nodes nested inside each other (X is inside B).
     *  The following is the end-result:
     *
     *      (*) node(type=TextBlock)
     *       |
     *       | firstChild
     *       |                 B                                  new
     *       |A             Italics              Z              Italics              C
     *      (*)--nextSibling--(*)--nextSibling--(*)--nextSibling--(*)--nextSibling--(*)
     *                         | firstChild                        | firstChild
     *                        (*) T                               (*) Y
     *                         | firstChild
     *                        (*) W
     *
     *  T is no longer really necessary, but specifically freeing it is pointless, so we keep it.
     *
     */

    for (examine = node->firstChild; examine; examine = examine->nextSibling)
    {
        /* In the above example, examine = B, but as new nodes are created and made siblings of B,
         * examine walks along them. Either way, it is the node just before C. */

        if (examine->type == Italics && examine->firstChild &&
            examine->firstChild /* i.e. T */->type == TextBlock)
        {
            /* Remember B's original sibling (C). We are going to insert a number of new
             * siblings after B, so at the end we want to re-attach C to the last one. */
            saveExamineSibling = examine->nextSibling;

            childExamine = examine->firstChild /* i.e. T */->firstChild /* i.e. W */;
            /* childSibling will be our "iterator" for iterating over the children of T */
            childSibling = childExamine->nextSibling;
            childExamine->nextSibling = 0;      /* Detach W's siblings */

            while (childSibling)
            {
                /* Remember the sibling we want to move on to later */
                saveChildSibling = childSibling->nextSibling;

                /* If we find a nested Italics (X in the example), move its child (Z) out
                 * and make it a sibling of B. */
                if (childSibling->type == Italics)
                {
                    examine->nextSibling = childSibling->firstChild;
                    /* Move examine on to the newly created sibling */
                    examine = examine->nextSibling;
                    /* Free the now-obsolete Italics node */
                    /* We have attached its children elsewhere, so don't use freeRecursively */
                    freeNode (childSibling);
                }
                /* Any node that is not an Italics node needs to become attached to one.
                 * (In the above example, this is only Y.) */
                else
                {
                    /* Detach the two */
                    childSibling->nextSibling = 0;

                    /* If examine already points to an Italics node, don't create a new one. */
                    /* Instead, combine its child and this one. (Doesn't occur in the example.) */
                    if (examine->type == Italics)
                        examine->firstChild = makeTextBlock (examine->firstChild, childSibling);
                    else
                    {
                        /* Create a new Italics node, attach the current node (Y) to it, and make
                         * it the next-distant sibling of B */
                        examine->nextSibling = nodeAddChild (newNode (Italics), childSibling);
                        examine = examine->nextSibling;
                    }
                }
                childSibling = saveChildSibling;
            }
            /* Now re-attach the previous sibling of B (i.e. C). */
            examine->nextSibling = saveExamineSibling;
        }
    }
    return node;
}

Node makeTextBlock (Node a, Node b)
{
    if (!a) return b;
    if (!b) return a;
    if (a->type == TextBlock && b->type == TextBlock)
    {
        nodeAddChild (a, b->firstChild);
        /* We have attached b's children elsewhere, so don't use freeRecursively */
        freeNode (b);
        return a;
    }
    else if (a->type == TextBlock)
        return nodeAddChild (a, b);
    else if (b->type == TextBlock)
        return nodePrependChild (b, a);
    else
        return nodeAddChild2 (newNode (TextBlock), a, b);
}

Node convertAttributesToText (Node node)
{
    char* str;
    int len, at, i;
    Node ret = 0, examine = node->firstChild, prevExamine;
    AttributeData ad;

    if (node->type != AttributeGroup) return 0;

    /* We've stored the first child in examine, so we can already free the parent */
    freeNode (node);

    while (examine) /* should be an Attribute node */
    {
        ad = examine->data.attrdata;
        /* first turn attribute name, equals sign (if any) and
         * opening apostrophe or quotes (if any) into one string */
        len = strlen (ad->name);
        at = len;
        len += ad->spacesAfterName;
        if (ad->type > 0)
        {
            len++; /* '=' */
            len += ad->spacesAfterEquals;
            if (ad->type > 1) len++;  /* ' or " */
        }
        len++; /* trailing '\0' */

        str = (char*) malloc (len * sizeof (char));
        memcpy (str, ad->name, at * sizeof (char));
        while (ad->spacesAfterName--) str[at++] = ' ';
        if (ad->type > 0)
        {
            str[at++] = '=';
            while (ad->spacesAfterEquals--) str[at++] = ' ';
            if (ad->type == 2) str[at++] = '\'';
            else if (ad->type == 3) str[at++] = '"';
        }
        str[at] = '\0';

        ret = makeTextBlock2 (ret, newNodeS (TextToken, str), examine->firstChild);

        if (ad->type > 1 || (ad->type == 1 && ad->spacesAfterValue > 0))
        {
            at = ad->type > 1 ? 1 : 0;
            len = at + ad->spacesAfterValue;
            str = (char*) malloc (len * sizeof (char));
            if (ad->type == 2) str[0] = '\'';
            else if (ad->type == 3) str[0] = '"';
            while (ad->spacesAfterValue--) str[at++] = ' ';
            str[at] = '\0';
            ret = makeTextBlock (ret, newNodeS (TextToken, str));
        }
        prevExamine = examine;
        examine = examine->nextSibling;
        freeNode (prevExamine);
    }

    return ret;
}

Node convertAttributeDataToText (AttributeData data)
{
    return makeTextBlock (newNodeS (TextToken, data->name),
                          newNodeS (TextToken, addSpaces ("", data->spacesAfterName)));
}

Node convertPipeSeriesToText (Node node)
{
    Node result = 0;
    Node nextNode, child;

    debugpt ("convertPipeSeriesToText()\n");

    while (node)
    {
        nextNode = node->nextSibling;
        child = node->firstChild;

        /* Performance optimisation: Instead of freeing 'node' and creating a new
         * TextToken node, we'll reuse this one! */
        node->type = TextToken;
        node->data.str = "|";
        node->nextSibling = 0;
        node->firstChild = 0;

        result = makeTextBlock2 (result, node, child);
        node = nextNode;
    }

    debugpt_end;

    return result;
}

Node convertTableRowToText (int info)
{
    int minuses, spaces, i;
    char* text;

    minuses = info / 0x10000;
    spaces  = info % 0x10000;

    text = (char*) malloc ((minuses + spaces + 2) * sizeof (char));
    text[0] = '|';
    i = 1;
    while (minuses--) text[i++] = '-';
    while (spaces--)  text[i++] = ' ';
    text[i] = '\0';
    return newNodeS (TextToken, text);
}

Node convertTableCellToText (int info)
{
    return newNodeS (TextToken, addSpaces (info % 2 ? "|" : "||", info/2));
}

Node convertTableHeadToText (int info)
{
    return newNodeS (TextToken, addSpaces (info % 2 ? "!" : "!!", info/2));
}

Node convertTableCaptionToText (int info)
{
    return newNodeS (TextToken, addSpaces ("|+", info));
}

Node convertHeadingToText (int info)
{
    int i;
    char* text;

    text = (char*) malloc ((info + 1) * sizeof (char));
    i = 0;
    while (info--) text[i++] = '=';
    text[i] = '\0';
    return newNodeS (TextToken, text);
}

char* addSpaces (char* src, int spaces)
{
    char* ret;
    int len = strlen (src);

    ret = (char*) malloc ((len + spaces + 1) * sizeof (char));
    if (len > 0) memcpy (ret, src, len * sizeof (char));
    ret[len+spaces] = '\0';
    while (spaces--) ret[len+spaces] = ' ';
    return ret;
}

char* strtrim (char* src)
{
    int i = strlen (src);
    i--;
    while ((i > 0) && (src[i] == ' ')) i--;
    src[i+1] = '\0';
    return src;
}

int strtrimC (char* src)
{
    int i = strlen (src), j = i;
    i--;
    while ((i > 0) && (src[i] == ' ')) i--;
    src[i+1] = '\0';
    return j - i - 1;
}

Node strtrimN (Node src)
{
    if (src->type == TextToken)
        strtrim (src->data.str);
    return src;
}
int strtrimNC (Node src)
{
    if (src->type == TextToken)
        return strtrimC (src->data.str);
    return 0;
}

char* fb_buffer;
int   fb_buflen;
int   fb_bufcontentlen;

void fb_create_new_buffer (int size)
{
    fb_buffer = (char*) malloc (size * sizeof (char));
    fb_buffer[0] = '\0';
    fb_bufcontentlen = 0;
    fb_buflen = size;
}

char* fb_get_buffer()
{
    return fb_buffer;
}

void fb_write_to_buffer_len (const char* str, int len)
{
    char* newbuffer;

    while (fb_buflen - fb_bufcontentlen < len + 1 /* for the NUL */)
    {
        /* enlarge buffer */
        fb_buflen *= 2;
        newbuffer = (char*) malloc (fb_buflen * sizeof (char));
        /* we are not copying the trailing '\0' because we would be overwriting it anyway */
        memcpy (newbuffer, fb_buffer, fb_bufcontentlen);
        free (fb_buffer);
        fb_buffer = newbuffer;
    }
    memcpy (fb_buffer + fb_bufcontentlen, str, len);
    fb_bufcontentlen += len;
    fb_buffer[fb_bufcontentlen] = '\0';
}

void fb_write_to_buffer (const char* str)
{
    fb_write_to_buffer_len (str, strlen (str));
}

void fb_write_to_buffer_escaped (char* s)
{
    char* curstart;
    int curlen, curry;
    char tmpstr[32];

    curstart = s;
    curlen = 0;
    curry = 0;

    while (*s)
    {
        switch (*s)
        {
#           define FB_WRITE_CURRY(x) \
                if (curry) fb_write_to_buffer_len (curstart, curlen); \
                fb_write_to_buffer (x); \
                curry = 0; \
                break;

            case '&': FB_WRITE_CURRY ("&amp;");
            case '<': FB_WRITE_CURRY ("&lt;");
            case '>': FB_WRITE_CURRY ("&gt;");
            case '"': FB_WRITE_CURRY ("&quot;");
            default:
                if (*s < ' ' && *s != '\n')
                {
                    sprintf (tmpstr, "&#%u;", (unsigned char)*s);
                    FB_WRITE_CURRY (tmpstr);
                }
                else
                {
                    if (!curry)
                    {
                        curry = 1;
                        curstart = s;
                        curlen = 1;
                    }
                    else curlen++;
                }
#           undef FB_WRITE_CURRY
        }
        s++;
    }
    if (curry)
        fb_write_to_buffer_len (curstart, curlen);
}

/* forward declaration required because of mutually recursive functions */
void outputXMLHelper (Node node);

/* This function is mutually recursive with outputXMLHelper() */
void outputNode (Node node)
{
    Node child;
    char *rname;
    char defaultname[255];
    if (!node) return;

    rname =
        node->type == TextBlock     ? 0 /* don't output tags for this, just the text */ :

        /* For the following, the tag is already output by outputXMLHelper: */
        node->type == Heading       ? 0 :
        node->type == List          ? 0 :
        node->type == LinkEtc       ? 0 :
        node->type == Attribute     ? 0 :

        node->type == LinkTarget    ? "linktarget"  :
        node->type == LinkOption    ? "linkoption"  :
        node->type == Article       ? "article"     :
        node->type == Paragraph     ? "paragraph"   :
        node->type == PreBlock      ? "preblock"    :
        node->type == PreLine       ? "preline"     :
        node->type == ListItem      ? "listitem"    :
        node->type == Bold          ? "bold"        :
        node->type == Italics       ? "italics"     :
        node->type == Comment       ? "comment"     :
        node->type == Template      ? "template"    :
        node->type == TemplateVar   ? "templatevar" :

        node->type == Table         ? "table"       :
        node->type == TableRow      ? "tablerow"    :
        node->type == TableCell     ? "tablecell"   :
        node->type == TableHead     ? "tablehead"   :
        node->type == TableCaption  ? "caption"     :
        node->type == AttributeGroup? "attrs"       :

        /* Fallback value */
        (sprintf (defaultname, "type%dnode", node->type), defaultname);

    child = node->firstChild;
    if (rname)
    {
        fb_write_to_buffer ("<");
        fb_write_to_buffer (rname);
        fb_write_to_buffer (">");
    }
    while (child)
    {
        outputXMLHelper (child);
        child = child->nextSibling;
    }
    if (rname)
    {
        fb_write_to_buffer ("</");
        fb_write_to_buffer (rname);
        fb_write_to_buffer (">");
    }
}

/* This function is mutually recursive with outputNode() */
void outputXMLHelper (Node node)
{
    Node child;
    NameValue nv;
    int i;
    char tmpstr[255];

    switch (node->type)
    {
        case Heading:
            sprintf (tmpstr, "<heading level='%d'>", node->data.num);
            fb_write_to_buffer (tmpstr);
            outputNode (node); /* will not output the tag */
            fb_write_to_buffer ("</heading>");
            break;

        case TextToken:
            fb_write_to_buffer_escaped (node->data.str);
            break;

        case ExtensionToken:
            nv = node->data.nameval;
            sprintf (tmpstr, "<extension name='%s'>", nv->name);
            fb_write_to_buffer (tmpstr);
            fb_write_to_buffer_escaped (nv->value);
            fb_write_to_buffer ("</extension>");
            break;

        case Attribute:
            sprintf (tmpstr, "<attr name='%s'", node->data.attrdata->name);
            fb_write_to_buffer (tmpstr);
            if (node->data.attrdata->type == 0)
                fb_write_to_buffer (" isnull='yes'");
            fb_write_to_buffer (">");
            outputNode (node);
            fb_write_to_buffer ("</attr>");
            break;

        case List:
            fb_write_to_buffer (node->data.num == 1 ? "<list type='bullet'>"   :
                                node->data.num == 2 ? "<list type='numbered'>" :
                                                      "<list>");
            outputNode (node);
            fb_write_to_buffer ("</list>");
            break;

        case LinkEtc:
            fb_write_to_buffer ("<link");
            if (node->data.num & 1) fb_write_to_buffer (" emptypipeatend='yes'");
            if (node->data.num & 2) fb_write_to_buffer (" forcedlink='yes'");
            fb_write_to_buffer (">");
            outputNode (node);
            fb_write_to_buffer ("</link>");
            break;

        default:
            outputNode (node);
            break;
    }
}

char* outputXML (Node node, int initialBufferSize)
{
    fb_create_new_buffer (initialBufferSize);
    outputXMLHelper (node);
    return fb_get_buffer();
}
