/**
 **             .-----------.       .---.       .---.
 **             |           |      /     '.   .'     \
 **             '---.   .---'     .        \ /        .
 **                 |   |         |         '         |
 **                 |   |         '                   '
 **                 |   |          \                 /
 **                 |   |           '.             .'
 **                 |   |             '.         .'
 **                 |   |               '.     .'
 **             .---'   '---.             \   /
 **             |           |              \ /
 **             '-----------'               '
 **             ___                   _____
 **             |  ) _   _  _   _       |  _  _  _  _
 **             |-´  _| |  (_  (_       | |  (_ (_ (_
 **             |   (_| |   _) (_       | |  (_ (_  _)
 **
 **
 ** This source file licensed unter the GNU General Public License
 **              http://www.gnu.org/copyleft/gpl.html
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
    ed->text = (char *) malloc (strlen (text) * sizeof (char));
    strcpy (ed->text, text);
    return ed;
}

DataType dataStr (char* arg) { DataType result; result.str = arg; return result; }
DataType dataNum (int arg)   { DataType result; result.num = arg; return result; }

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

void printEscaped (char* s)
{
    while (*s)
    {
        switch (*s)
        {
            case '&': printf ("&amp;");  break;
            case '<': printf ("&lt;");   break;
            case '>': printf ("&gt;");   break;
            case '"': printf ("&quot;"); break;
            default: putchar (*s);
        }
        s++;
    }
}

void outputNode (Node node)
{
    Node child;
    char *rname;
    char defaultname[255];
    if (!node) return;

    rname =
        node->type == TextBlock     ? 0 /* don't output tags for this, just the text */ :
        node->type == Heading       ? 0 /* outputXML already does this one */ :
        node->type == List          ? 0 /* outputXML already does this one */ :
        node->type == Article       ? "article" :
        node->type == Paragraph     ? "paragraph" :
        node->type == PreBlock      ? "preblock" :
        node->type == PreLine       ? "preline" :
        node->type == ListItem      ? "listitem" :
        node->type == Bold          ? "bold" :
        node->type == Italics       ? "italics" :
        /* Fallback value */
        (sprintf (defaultname, "type%dnode", node->type), defaultname);

    child = node->firstChild;
    if (rname) printf ("<%s>", rname);
    while (child)
    {
        outputXML (child);
        child = child->nextSibling;
    }
    if (rname) printf ("</%s>", rname);
}

/* This function is recursive */
void outputXML (Node node)
{
    Node child;
    ExtensionData ed;
    int i;

    switch (node->type)
    {
        case Heading:
            printf ("<heading level='%d'>", node->data.num);
            outputNode (node); /* will not output the tag */
            printf ("</heading>");
            break;

        case TextToken:
            printEscaped (node->data.str);
            break;

        case ExtensionToken:
            ed = node->data.ext;
            printf ("<extension name=\"%s\">", ed->name);
            printEscaped (ed->text);
            printf ("</extension>");
            break;

        case List:
            printf ("<list%s>",
                node->data.num == 1 ? " type='bullet'" :
                node->data.num == 2 ? " type='numbered'" : "");
            outputNode (node);
            printf ("</list>");
            break;

        default:
            outputNode (node);
            break;
    }
}
