
/**
 **  This file is part of the flex/bison-based parser for MediaWiki.
 **       This header file defines all types and functions used
 **                       throughout the parser.
 **
 ** This source file is licensed unter the GNU General Public License
 **                http://www.gnu.org/copyleft/gpl.html
 **                  Originally written 2004 by Timwi
 **/

typedef enum NodeType {
    Article, Paragraph, Heading, TextBlock, TextToken, ExtensionToken,
    Newlines, PreBlock, PreLine, Bold, Italics, LinkEtc, LinkTarget,
    LinkOption,

    /* After first parse */
    ListBlock, ListLine, ListBullet, ListNumbered,

    /* After processList */
    List, ListItem
} NodeType;

typedef struct ExtensionDataStruct
{
    char *  name;
    char *  text;
}
* ExtensionData;

typedef union DataType {
    char*           str;
    int             num;
    ExtensionData   ext;
} DataType;

typedef struct NodeStruct
{
    NodeType            type;
    DataType            data;
    struct NodeStruct*  nextSibling;
    struct NodeStruct*  firstChild;
}
* Node;

Node newNode (NodeType newType);
Node newNodeI (NodeType newType, int data);
Node newNodeS (NodeType newType, char* data);
Node newNodeE (NodeType newType, ExtensionData data);

/* Return value of all of these is the first parameter */
Node nodeAddChild (Node node, Node child);
#define nodeAddChild2(a,b,c) nodeAddChild (nodeAddChild (a, b), c)
Node nodePrependChild (Node node, Node child);
Node nodeAddSibling (Node node, Node sibling);

/* Parameter must be a ListBlock node. Returns a List node. */
Node processListBlock (Node block);

/* Parameter must be a PreBlock node. Returns same */
Node processPreBlock (Node block);

/* Returns a TextToken, or null if n < 1 */
Node processEndHeadingInText (int n);

/* If a is a TextBlock, adds b to it; if b is a TextBlock, prepends a;
 * if both are a TextBlock, adds b's children to a and frees b;
 * otherwise creates new TextBlock with a and b in it.
 * If any parameter is 0, returns the other. */
Node makeTextBlock (Node a, Node b);
#define makeTextBlock2(a,b,c) makeTextBlock (makeTextBlock (a, b), c)

/* Parameter must be a TextBlock. Turns something like
 * <italics>X<italics>Y</italics>Z</italics> into
 * <italics>X</italics>Y<italics>Z</italics>. Returns node. */
Node processNestedItalics (Node node);

ExtensionData newExtensionData (char* name, char* text);

void outputXML (Node node);
