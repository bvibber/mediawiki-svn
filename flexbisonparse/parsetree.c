
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
#include <string.h>
#include <stdio.h>

Node newNode (NodeType newType)
{
    Node result = (Node) malloc (sizeof (struct NodeStruct));
    result->type = newType;
    result->firstChild = 0;
    result->nextSibling = 0;
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
Node newNodeE (NodeType newType, ExtensionData data)
{
    Node result = newNode (newType);
    result->data.ext = data;
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
    if (sibling)
    {
        while (node->nextSibling)
            node = node->nextSibling;
        node->nextSibling = sibling;
    }
    return node;
}

/* Return value is the first parameter */
Node nodePrependChild (Node node, Node child)
{
    child->nextSibling = node->firstChild;
    node->firstChild = child;
    return node;
}

ExtensionData newExtensionData (char *name, char *text)
{
    ExtensionData ed = (ExtensionData) malloc (sizeof (struct ExtensionDataStruct));
    ed->name = name;
    ed->text = (char *) malloc ((strlen (text)+1) * sizeof (char));
    strcpy (ed->text, text);
    return ed;
}

void removeAndFreeFirstChild (Node node)
{
    Node child = node->firstChild;
    if (!child) return;
    node->firstChild = child->nextSibling;
    free (child);
}

/* Parameter must be a ListLine node. Returns a List node. */
Node processListHelper (Node start)
{
    NodeType type, subtype;
    Node result, curChild, examine, item, previous, l;

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
        /* We know that examine->firstChild is ListBullet, ListNumbered, etc. Remove it */
        removeAndFreeFirstChild (examine);

        /* Empty list item? */
        if (!examine->firstChild)
            examine = examine->nextSibling;

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
        }
    }
    return result;
}

/* Parameter must be a ListBlock node. Returns a List node. */
Node processListBlock (Node block)
{
    if (!block) return 0;
    if (block->type != ListBlock) return 0;
    if (!block->firstChild) return 0;
    if (block->firstChild->type != ListLine) return 0;

    return processListHelper (block->firstChild);
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
            free (newlinesnode);
        }
        examine = examine->nextSibling;
    }
    return result;
}

Node processEndHeadingInText (int n)
{
    char* ret;

    if (n < 1) return 0;
    /* Performance optimisation */
    if (n == 1) return newNodeS (TextToken, "=");

    ret = (char*) malloc ((n+1) * sizeof (char));
    ret[n] = '\0';
    while (n) ret[--n] = '=';
    return newNodeS (TextToken, ret);
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
                    free (childSibling);
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
        free (b);
        return a;
    }
    else if (a->type == TextBlock)
        return nodeAddChild (a, b);
    else if (b->type == TextBlock)
        return nodePrependChild (b, a);
    else
        return nodeAddChild (nodeAddChild (newNode (TextBlock), a), b);
}

char* fb_buffer;
int   fb_buflen = 1024;    /* Start with 1 KB if user doesn't call fb_set_buffer_size() */
int   fb_bufcontentlen;

inline void fb_set_buffer_size (int size)
{
    fb_buflen = size;
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

inline void fb_write_to_buffer (const char* str)
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
                if (*s < ' ')
                {
                    sprintf (tmpstr, "&#%d;", *s);
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
        node->type == Heading       ? 0 /* outputXML already does this one; it may have attributes */ :
        node->type == List          ? 0 /* outputXML already does this one; it may have attributes */ :
        node->type == LinkEtc       ? 0 /* outputXML already does this one; it may have attributes */ :

        node->type == LinkTarget    ? "linktarget"  :
        node->type == LinkOption    ? "linkoption"  :
        node->type == Article       ? "article"     :
        node->type == Paragraph     ? "paragraph"   :
        node->type == PreBlock      ? "preblock"    :
        node->type == PreLine       ? "preline"     :
        node->type == ListItem      ? "listitem"    :
        node->type == Bold          ? "bold"        :
        node->type == Italics       ? "italics"     :
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
    ExtensionData ed;
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
            ed = node->data.ext;
            sprintf (tmpstr, "<extension name=\"%s\">", ed->name);
            fb_write_to_buffer (tmpstr);
            fb_write_to_buffer_escaped (ed->text);
            fb_write_to_buffer ("</extension>");
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

char* outputXML (Node node)
{
    fb_buffer = (char*) malloc (fb_buflen * sizeof (char));
    fb_buffer[0] = '\0';
    fb_bufcontentlen = 0;

    outputXMLHelper (node);
    return fb_buffer;
}
